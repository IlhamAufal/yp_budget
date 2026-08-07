<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model Chart of Account (COA) — tabel `gw_plan__master_coa`.
 */
class CoaModel extends Model
{
    protected $table            = 'gw_plan__master_coa';
    protected $primaryKey       = 'id_cost_center';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'main_account',
        'id_cost_header',
        'id_acct_ext',
        'cost_center_header',
        'cost_center_sub',
        'cost_center_desc',
        'year',
        'main_acc_type',
        'type',
        'category',
        'status',
    ];

    /**
     * Daftar COA dengan filter (search / type / year / status).
     */
    public function getAll(array $filters = []): array
    {
        $builder = $this->builder();

        if (! empty($filters['search'])) {
            $like = trim($filters['search']);
            $builder->groupStart()
                ->like('main_account', $like)
                ->orLike('id_acct_ext', $like)
                ->orLike('cost_center_header', $like)
                ->orLike('cost_center_sub', $like)
                ->orLike('cost_center_desc', $like)
                ->groupEnd();
        }
        if (! empty($filters['type'])) {
            $builder->where('type', $filters['type']);
        }
        if (! empty($filters['year'])) {
            $builder->where('year', (int) $filters['year']);
        }
        if (! empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        // Server-side pagination
        if (! empty($filters['limit'])) {
            $builder->limit((int) $filters['limit'], (int) ($filters['offset'] ?? 0));
        } else {
            $builder->limit(500);
        }

        return $builder->orderBy('main_account', 'ASC')
            ->orderBy('id_cost_center', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function countAll(array $filters = []): int
    {
        $builder = $this->builder();

        if (! empty($filters['search'])) {
            $like = trim($filters['search']);
            $builder->groupStart()
                ->like('main_account', $like)
                ->orLike('id_acct_ext', $like)
                ->orLike('cost_center_header', $like)
                ->orLike('cost_center_sub', $like)
                ->orLike('cost_center_desc', $like)
                ->groupEnd();
        }
        if (! empty($filters['type'])) {
            $builder->where('type', $filters['type']);
        }
        if (! empty($filters['year'])) {
            $builder->where('year', (int) $filters['year']);
        }
        if (! empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        return (int) $builder->countAllResults();
    }

    /**
     * Daftar tahun yang punya data COA (untuk dropdown filter).
     */
    public function getYears(): array
    {
        try {
            $rows = $this->builder()->select('year')->distinct()->orderBy('year', 'DESC')->get()->getResultArray();
            // array_values: buang tahun falsy (0/null) sekaligus rapikan ulang
            // index agar tahun terbaru selalu aman diakses via $years[0].
            return array_values(array_map('intval', array_filter(array_column($rows, 'year'))));
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Daftar tipe akun (FOH / GA / SELLING / CAPEX / dll) yang terpakai.
     */
    public function getTypes(): array
    {
        try {
            $rows = $this->builder()->select('type')->distinct()->orderBy('type', 'ASC')->get()->getResultArray();
            return array_values(array_filter(array_column($rows, 'type')));
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Simpan akun baru / update akun yang sudah ada.
     */
    public function saveAccount(array $data, ?int $id = null): array
    {
        $payload = [
            'main_account'       => (int) ($data['main_account'] ?? 0),
            'id_cost_header'     => ! empty($data['id_cost_header']) ? (int) $data['id_cost_header'] : null,
            'id_acct_ext'        => ! empty($data['id_acct_ext']) ? $data['id_acct_ext'] : null,
            'cost_center_header' => trim($data['cost_center_header'] ?? ''),
            'cost_center_sub'    => trim($data['cost_center_sub'] ?? ''),
            'cost_center_desc'   => trim($data['cost_center_desc'] ?? ''),
            'year'               => (int) ($data['year'] ?? date('Y')),
            'main_acc_type'      => trim($data['main_acc_type'] ?? ''),
            'type'               => trim($data['type'] ?? ''),
            'category'           => trim($data['category'] ?? ''),
            'status'             => ($data['status'] ?? 'A') === 'D' ? 'D' : 'A',
        ];

        if ($payload['main_account'] <= 0) {
            return ['success' => false, 'message' => 'Nomor akun utama wajib diisi.'];
        }

        try {
            if ($id) {
                $payload['updated_date'] = date('Y-m-d H:i:s');
                $this->update($id, $payload);
            } else {
                $this->insert($payload);
            }
        } catch (\Throwable $e) {
            log_message('error', 'CoaModel::saveAccount: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menyimpan akun COA.'];
        }

        return ['success' => true, 'message' => 'Akun COA berhasil disimpan.'];
    }

    /**
     * Aktifkan / nonaktifkan akun (soft delete via status).
     */
    public function toggleStatus(int $id): array
    {
        $row = $this->find($id);
        if (! $row) {
            return ['success' => false, 'message' => 'Akun tidak ditemukan.'];
        }

        $newStatus = ($row['status'] === 'A') ? 'D' : 'A';
        try {
            $this->update($id, [
                'status'       => $newStatus,
                'updated_date' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Gagal mengubah status akun.'];
        }

        return [
            'success' => true,
            'message' => $newStatus === 'A' ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.',
        ];
    }

    /**
     * Copy seluruh akun aktif dari satu tahun ke tahun lain.
     * Mengembalikan jumlah akun yang disalin.
     */
    public function copyYear(int $fromYear, int $toYear): array
    {
        if ($fromYear === $toYear) {
            return ['success' => false, 'message' => 'Tahun asal dan tujuan harus berbeda.'];
        }

        $source = $this->builder()
            ->where('year', $fromYear)
            ->where('status', 'A')
            ->get()
            ->getResultArray();

        if (empty($source)) {
            return ['success' => false, 'message' => "Tidak ada akun aktif di tahun {$fromYear}."];
        }

        $existing = $this->builder()
            ->select('main_account')
            ->where('year', $toYear)
            ->get()
            ->getResultArray();
        $existingAccounts = array_map('intval', array_column($existing, 'main_account'));

        $batch = [];
        foreach ($source as $row) {
            if (in_array((int) $row['main_account'], $existingAccounts, true)) {
                continue; // sudah ada di tahun tujuan, jangan duplikat
            }
            $batch[] = [
                'main_account'       => (int) $row['main_account'],
                'id_cost_header'     => $row['id_cost_header'],
                'id_acct_ext'        => $row['id_acct_ext'],
                'cost_center_header' => $row['cost_center_header'],
                'cost_center_sub'    => $row['cost_center_sub'],
                'cost_center_desc'   => $row['cost_center_desc'],
                'year'               => $toYear,
                'main_acc_type'      => $row['main_acc_type'],
                'type'               => $row['type'],
                'category'           => $row['category'],
                'status'             => 'A',
            ];
        }

        if (empty($batch)) {
            return ['success' => false, 'message' => "Semua akun tahun {$fromYear} sudah ada di tahun {$toYear}."];
        }

        try {
            $this->builder()->insertBatch($batch);
        } catch (\Throwable $e) {
            log_message('error', 'CoaModel::copyYear: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menyalin akun COA.'];
        }

        return [
            'success' => true,
            'message' => "Berhasil menyalin " . count($batch) . " akun dari {$fromYear} ke {$toYear}.",
        ];
    }
}
