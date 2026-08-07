<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model Cost Center — tabel `gw_plan__master_cost_center`
 * (tabel yang benar-benar terisi di database legacy, bukan gw_plan__master_cc).
 */
class CostCenterModel extends Model
{
    protected $table            = 'gw_plan__master_cost_center';
    protected $primaryKey       = 'id_cost_center';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'cost_center',
        'cost_center_sap',
        'location',
        'year',
        'cost_desc',
        'type',
        'status',
    ];

    /**
     * Daftar cost center dengan filter (search / type / year / status).
     */
    public function getAll(array $filters = []): array
    {
        $builder = $this->builder();

        if (! empty($filters['search'])) {
            $like = trim($filters['search']);
            $builder->groupStart()
                ->like('cost_center', $like)
                ->orLike('cost_center_sap', $like)
                ->orLike('cost_desc', $like)
                ->orLike('location', $like)
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

        return $builder->orderBy('cost_center', 'ASC')
            ->orderBy('id_cost_center', 'ASC')
            ->limit(500)
            ->get()
            ->getResultArray();
    }

    public function getYears(): array
    {
        try {
            $rows = $this->builder()->select('year')->distinct()->orderBy('year', 'DESC')->get()->getResultArray();
            return array_map('intval', array_filter(array_column($rows, 'year')));
        } catch (\Throwable $e) {
            return [];
        }
    }

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
     * Simpan cost center baru / update.
     */
    public function saveCostCenter(array $data, ?int $id = null): array
    {
        $payload = [
            'cost_center'    => ! empty($data['cost_center']) ? (int) $data['cost_center'] : null,
            'cost_center_sap' => ! empty($data['cost_center_sap']) ? $data['cost_center_sap'] : null,
            'location'       => ! empty($data['location']) ? $data['location'] : null,
            'year'           => (int) ($data['year'] ?? date('Y')),
            'cost_desc'      => trim($data['cost_desc'] ?? ''),
            'type'           => trim($data['type'] ?? 'OPEX'),
            'status'         => ($data['status'] ?? 'A') === 'D' ? 'D' : 'A',
        ];

        if ($payload['cost_desc'] === '') {
            return ['success' => false, 'message' => 'Deskripsi cost center wajib diisi.'];
        }

        try {
            if ($id) {
                $payload['updated_date'] = date('Y-m-d H:i:s');
                $this->update($id, $payload);
            } else {
                $this->insert($payload);
            }
        } catch (\Throwable $e) {
            log_message('error', 'CostCenterModel::saveCostCenter: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menyimpan cost center.'];
        }

        return ['success' => true, 'message' => 'Cost center berhasil disimpan.'];
    }

    public function toggleStatus(int $id): array
    {
        $row = $this->find($id);
        if (! $row) {
            return ['success' => false, 'message' => 'Cost center tidak ditemukan.'];
        }

        $newStatus = ($row['status'] === 'A') ? 'D' : 'A';
        try {
            $this->update($id, [
                'status'       => $newStatus,
                'updated_date' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Gagal mengubah status cost center.'];
        }

        return [
            'success' => true,
            'message' => $newStatus === 'A' ? 'Cost center diaktifkan.' : 'Cost center dinonaktifkan.',
        ];
    }
}
