<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model Product — tabel `gw_plan__master_product`.
 */
class ProductModel extends Model
{
    protected $table            = 'gw_plan__master_product';
    protected $primaryKey       = 'id_product';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'id_channel',
        'key_product',
        'mid_product',
        'product_name',
        'db',
        'pcs',
        'gr',
        'year',
        'submit_by',
        'submit_date',
        'status',
    ];

    /**
     * Daftar product dengan filter (search, channel, year, status).
     */
    public function getAll(array $filters = []): array
    {
        $builder = $this->builder();

        if (! empty($filters['search'])) {
            $like = trim($filters['search']);
            $builder->groupStart()
                ->like('product_name', $like)
                ->orLike('mid_product', $like)
                ->orLike('key_product', $like)
                ->groupEnd();
        }

        if (! empty($filters['channel'])) {
            $builder->where('id_channel', $filters['channel']);
        }

        if (! empty($filters['year'])) {
            $builder->where('year', (int) $filters['year']);
        }

        if (! empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        if (! empty($filters['limit'])) {
            $builder->limit((int) $filters['limit'], (int) ($filters['offset'] ?? 0));
        } else {
            $builder->limit(1000);
        }

        return $builder->orderBy('product_name', 'ASC')
            ->orderBy('id_product', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function countAll(array $filters = []): int
    {
        $builder = $this->builder();

        if (! empty($filters['search'])) {
            $like = trim($filters['search']);
            $builder->groupStart()
                ->like('product_name', $like)
                ->orLike('mid_product', $like)
                ->orLike('key_product', $like)
                ->groupEnd();
        }
        if (! empty($filters['channel'])) {
            $builder->where('id_channel', $filters['channel']);
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
     * Ambil daftar channel unik yang tersedia dari database.
     */
    public function getChannels(): array
    {
        $rows = db_connect()->table('gw_plan__master_channel')
            ->select('channel_code')
            ->orderBy('id_channel', 'ASC')
            ->get()
            ->getResultArray();

        return array_column($rows, 'channel_code');
    }

    /**
     * Ambil daftar tahun produk.
     */
    public function getYears(): array
    {
        $res = $this->builder()
            ->select('DISTINCT(year) as year', false)
            ->where('year IS NOT NULL')
            ->orderBy('year', 'DESC')
            ->get()
            ->getResultArray();

        $years = array_values(array_filter(array_column($res, 'year')));
        if (empty($years)) {
            $years = [(int) date('Y')];
        }

        return $years;
    }

    /**
     * Simpan / perbarui data product.
     */
    public function saveProduct(array $data, ?int $id = null): array
    {
        $payload = [
            'id_channel'   => trim($data['id_channel'] ?? 'GT'),
            'key_product'  => trim($data['key_product'] ?? ''),
            'mid_product'  => trim($data['mid_product'] ?? ''),
            'product_name' => trim($data['product_name'] ?? ''),
            'db'           => (float) ($data['db'] ?? 0),
            'pcs'          => (float) ($data['pcs'] ?? 0),
            'gr'           => (float) ($data['gr'] ?? 0),
            'year'         => (int) ($data['year'] ?? date('Y')),
            'status'       => ($data['status'] ?? 'A') === 'D' ? 'D' : 'A',
            'submit_by'    => session()->get('user_id') ?? 'SYSTEM',
        ];

        if ($payload['product_name'] === '') {
            return ['success' => false, 'message' => 'Nama produk wajib diisi.'];
        }

        try {
            if ($id) {
                $this->update($id, $payload);
            } else {
                $this->insert($payload);
            }
        } catch (\Throwable $e) {
            log_message('error', 'ProductModel::saveProduct: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menyimpan produk: ' . $e->getMessage()];
        }

        return ['success' => true, 'message' => 'Data produk berhasil disimpan.'];
    }

    /**
     * Toggle status aktif / non-aktif produk.
     */
    public function toggleStatus(int $id): array
    {
        $row = $this->find($id);
        if (! $row) {
            return ['success' => false, 'message' => 'Data produk tidak ditemukan.'];
        }

        $newStatus = ($row['status'] === 'A') ? 'D' : 'A';
        try {
            $this->update($id, ['status' => $newStatus]);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Gagal mengubah status produk.'];
        }

        return [
            'success' => true,
            'message' => $newStatus === 'A' ? 'Produk berhasil diaktifkan.' : 'Produk dinonaktifkan.',
        ];
    }
}
