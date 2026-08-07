<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model Departemen — tabel `gw_plan__master_department`.
 */
class DepartmentModel extends Model
{
    protected $table            = 'gw_plan__master_department';
    protected $primaryKey       = 'id_dept';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'dept_code',
        'dept_desc',
        'status',
    ];

    /**
     * Daftar departemen dengan filter (search / status).
     */
    public function getAll(array $filters = []): array
    {
        $builder = $this->builder();

        if (! empty($filters['search'])) {
            $like = trim($filters['search']);
            $builder->groupStart()
                ->like('dept_code', $like)
                ->orLike('dept_desc', $like)
                ->groupEnd();
        }
        if (! empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        return $builder->orderBy('dept_code', 'ASC')
            ->orderBy('id_dept', 'ASC')
            ->limit(500)
            ->get()
            ->getResultArray();
    }

    /**
     * Simpan departemen baru / update.
     */
    public function saveDepartment(array $data, ?int $id = null): array
    {
        $payload = [
            'dept_code' => trim($data['dept_code'] ?? ''),
            'dept_desc' => trim($data['dept_desc'] ?? ''),
            'status'    => ($data['status'] ?? 'A') === 'D' ? 'D' : 'A',
        ];

        if ($payload['dept_desc'] === '') {
            return ['success' => false, 'message' => 'Nama departemen wajib diisi.'];
        }

        try {
            if ($id) {
                $this->update($id, $payload);
            } else {
                $this->insert($payload);
            }
        } catch (\Throwable $e) {
            log_message('error', 'DepartmentModel::saveDepartment: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menyimpan departemen.'];
        }

        return ['success' => true, 'message' => 'Departemen berhasil disimpan.'];
    }

    public function toggleStatus(int $id): array
    {
        $row = $this->find($id);
        if (! $row) {
            return ['success' => false, 'message' => 'Departemen tidak ditemukan.'];
        }

        $newStatus = ($row['status'] === 'A') ? 'D' : 'A';
        try {
            $this->update($id, ['status' => $newStatus]);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Gagal mengubah status departemen.'];
        }

        return [
            'success' => true,
            'message' => $newStatus === 'A' ? 'Departemen diaktifkan.' : 'Departemen dinonaktifkan.',
        ];
    }
}
