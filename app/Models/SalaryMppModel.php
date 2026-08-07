<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model Salary MPP — tabel `yp_plan__master_mpp_salary`.
 */
class SalaryMppModel extends Model
{
    protected $table            = 'yp_plan__master_mpp_salary';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'desc',
        'salary',
        'dept_id',
        'type',
        'year_code',
        'status',
    ];

    /**
     * Daftar MPP Salary dengan filter dan join ke Departemen & Tipe MPP.
     */
    public function getAll(array $filters = []): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table . ' s')
            ->select('s.*, d.dept_code, d.dept_desc, t.desc_mpp as type_name')
            ->join('gw_plan__master_department d', 'd.id_dept = s.dept_id', 'left')
            ->join('yp_plan__master_tipe_mpp t', 't.id_mpp = s.type', 'left');

        if (! empty($filters['search'])) {
            $like = trim($filters['search']);
            $builder->groupStart()
                ->like('s.desc', $like)
                ->orLike('d.dept_desc', $like)
                ->orLike('d.dept_code', $like)
                ->groupEnd();
        }

        if (! empty($filters['dept_id'])) {
            $builder->where('s.dept_id', (int) $filters['dept_id']);
        }

        if (! empty($filters['type'])) {
            $builder->where('s.type', (int) $filters['type']);
        }

        if (! empty($filters['year'])) {
            $builder->where('s.year_code', (int) $filters['year']);
        }

        if (! empty($filters['status'])) {
            $builder->where('s.status', $filters['status']);
        }

        return $builder->orderBy('s.year_code', 'DESC')
            ->orderBy('d.dept_code', 'ASC')
            ->orderBy('s.desc', 'ASC')
            ->limit(1000)
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil daftar tipe MPP dari tabel `yp_plan__master_tipe_mpp`.
     */
    public function getMppTypes(): array
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('yp_plan__master_tipe_mpp')) {
            return $db->table('yp_plan__master_tipe_mpp')
                ->orderBy('id_mpp', 'ASC')
                ->get()
                ->getResultArray();
        }

        return [
            ['id_mpp' => 1, 'desc_mpp' => 'Direct Labor'],
            ['id_mpp' => 2, 'desc_mpp' => 'Indirect Labor'],
            ['id_mpp' => 3, 'desc_mpp' => 'Staff / Monthly'],
        ];
    }

    /**
     * Ambil daftar tahun unik pada MPP Salary.
     */
    public function getYears(): array
    {
        $res = $this->builder()
            ->select('DISTINCT(year_code) as year', false)
            ->where('year_code IS NOT NULL')
            ->orderBy('year_code', 'DESC')
            ->get()
            ->getResultArray();

        $years = array_values(array_filter(array_column($res, 'year')));
        if (empty($years)) {
            $years = [(int) date('Y')];
        }

        return $years;
    }

    /**
     * Simpan / perbarui data master Salary MPP.
     */
    public function saveSalaryMpp(array $data, ?int $id = null): array
    {
        $payload = [
            'desc'      => trim($data['desc'] ?? ''),
            'salary'    => (float) ($data['salary'] ?? 0),
            'dept_id'   => ! empty($data['dept_id']) ? (int) $data['dept_id'] : null,
            'type'      => ! empty($data['type']) ? (int) $data['type'] : 1,
            'year_code' => (int) ($data['year_code'] ?? date('Y')),
            'status'    => ($data['status'] ?? 'A') === 'D' ? 'D' : 'A',
        ];

        if ($payload['desc'] === '') {
            return ['success' => false, 'message' => 'Deskripsi / Posisi jabatan wajib diisi.'];
        }

        try {
            if ($id) {
                $this->update($id, $payload);
            } else {
                $this->insert($payload);
            }
        } catch (\Throwable $e) {
            log_message('error', 'SalaryMppModel::saveSalaryMpp: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menyimpan Salary MPP: ' . $e->getMessage()];
        }

        return ['success' => true, 'message' => 'Master Salary MPP berhasil disimpan.'];
    }

    /**
     * Toggle status aktif / non-aktif.
     */
    public function toggleStatus(int $id): array
    {
        $row = $this->find($id);
        if (! $row) {
            return ['success' => false, 'message' => 'Data Salary MPP tidak ditemukan.'];
        }

        $newStatus = ($row['status'] === 'A') ? 'D' : 'A';
        try {
            $this->update($id, ['status' => $newStatus]);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Gagal mengubah status Salary MPP.'];
        }

        return [
            'success' => true,
            'message' => $newStatus === 'A' ? 'Salary MPP diaktifkan.' : 'Salary MPP dinonaktifkan.',
        ];
    }
}
