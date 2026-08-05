<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk Tahun Anggaran (Working Year).
 *
 * Menggunakan tabel baru `yp_plan__master_year` (dibuat via migration
 * CreateMasterYearTable) — bukan `yp_plan__master_period` legacy yang
 * berisi periode entry per-modul.
 */
class PeriodModel extends Model
{
    protected $table            = 'yp_plan__master_year';
    protected $primaryKey       = 'year_code';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'year_code',
        'period_start',
        'period_end',
        'status',
        'locked',
    ];

    /**
     * Daftar tahun anggaran aktif (untuk modal Working Year & api/active-years).
     * Mengembalikan array integer, diurutkan dari tahun terbaru.
     */
    public function getActiveYears(): array
    {
        try {
            $results = $this->where('status', 'A')
                ->orderBy('year_code', 'DESC')
                ->findAll();

            if (! empty($results)) {
                return array_map('intval', array_column($results, 'year_code'));
            }
        } catch (\Throwable $e) {
            log_message('error', 'PeriodModel::getActiveYears: ' . $e->getMessage());
        }

        // Fallback jika tabel belum dibuat / kosong
        $currentYear = (int) date('Y');
        return [
            $currentYear + 1,
            $currentYear,
            $currentYear - 1,
        ];
    }

    /**
     * Semua tahun anggaran (halaman Master > Periode), terbaru dulu.
     */
    public function getAllYears(): array
    {
        try {
            return $this->orderBy('year_code', 'DESC')->findAll();
        } catch (\Throwable $e) {
            log_message('error', 'PeriodModel::getAllYears: ' . $e->getMessage());
            return [];
        }
    }

    public function findYear(int $yearCode): ?array
    {
        return $this->find($yearCode);
    }

    /**
     * Simpan tahun baru atau update periode yang sudah ada.
     */
    public function saveYear(array $data): array
    {
        $yearCode = (int) ($data['year_code'] ?? 0);
        if ($yearCode < 2000 || $yearCode > 2100) {
            return ['success' => false, 'message' => 'Tahun anggaran tidak valid (2000 - 2100).'];
        }

        $existing = $this->findYear($yearCode);
        $payload = [
            'year_code'    => $yearCode,
            'period_start' => ! empty($data['period_start']) ? $data['period_start'] : null,
            'period_end'   => ! empty($data['period_end']) ? $data['period_end'] : null,
            'status'       => ($data['status'] ?? 'A') === 'D' ? 'D' : 'A',
            'locked'       => ! empty($data['locked']) ? 1 : 0,
            'updated_date' => date('Y-m-d H:i:s'),
        ];

        try {
            if ($existing) {
                $this->update($yearCode, $payload);
            } else {
                $payload['created_date'] = date('Y-m-d H:i:s');
                $this->insert($payload);
            }
        } catch (\Throwable $e) {
            log_message('error', 'PeriodModel::saveYear: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menyimpan tahun anggaran.'];
        }

        return ['success' => true, 'message' => "Tahun anggaran {$yearCode} berhasil disimpan."];
    }

    /**
     * Aktifkan satu tahun sebagai Working Year; tahun lain menjadi non-aktif.
     */
    public function setActive(int $yearCode): array
    {
        try {
            $this->db->transBegin();
            $this->where('year_code !=', $yearCode)->set('status', 'D')->update();
            $this->update($yearCode, ['status' => 'A', 'updated_date' => date('Y-m-d H:i:s')]);
            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'PeriodModel::setActive: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal mengaktifkan tahun anggaran.'];
        }

        return ['success' => true, 'message' => "Tahun anggaran {$yearCode} aktif sebagai Working Year."];
    }

    /**
     * Kunci / buka tahun anggaran (locked = 1 artinya entry data ditutup).
     */
    public function setLocked(int $yearCode, bool $locked): array
    {
        try {
            $this->update($yearCode, [
                'locked'       => $locked ? 1 : 0,
                'updated_date' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'PeriodModel::setLocked: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal mengubah status lock.'];
        }

        return [
            'success' => true,
            'message' => $locked
                ? "Tahun anggaran {$yearCode} dikunci (tidak bisa entry data)."
                : "Tahun anggaran {$yearCode} dibuka kembali.",
        ];
    }

    public function deleteYear(int $yearCode): array
    {
        try {
            $this->delete($yearCode);
        } catch (\Throwable $e) {
            log_message('error', 'PeriodModel::deleteYear: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menghapus tahun anggaran.'];
        }

        return ['success' => true, 'message' => "Tahun anggaran {$yearCode} berhasil dihapus."];
    }
}
