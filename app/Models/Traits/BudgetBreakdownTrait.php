<?php

namespace App\Models\Traits;

/**
 * BudgetBreakdownTrait — Breakdown Sub-Detail COA (PRD standar 1.5).
 *
 * CRUD item detail (yp_plan__trans_budget_entry_detail) yang dipakai
 * bersama oleh seluruh modul entry budget (FOH, OPEX GA, OPEX Selling)
 * agar struktur modal & endpoint konsisten.
 *
 * NOTE: Model yang memakai trait ini WAJIB memiliki properti $db
 * (CodeIgniter database instance).
 */
trait BudgetBreakdownTrait
{
    /**
     * Daftar item breakdown sebuah entry budget.
     */
    public function getDetailItems(int $entryDataId): array
    {
        return $this->db->table('yp_plan__trans_budget_entry_detail')
            ->where('entry_data_id', $entryDataId)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Simpan item breakdown (AJAX partial update, tanpa refresh).
     *
     * @return array ['success' => bool, 'message' => string, 'id' => ?int]
     */
    public function saveDetailItem(array $data, int $userId): array
    {
        $entryDataId = (int) ($data['entry_data_id'] ?? 0);
        if ($entryDataId <= 0) {
            return ['success' => false, 'message' => 'ID entry budget tidak valid.', 'id' => null];
        }

        $parent = $this->db->table('yp_plan__trans_budget_entry_data')
            ->where('id', $entryDataId)
            ->get()->getRowArray();

        if (! $parent) {
            return ['success' => false, 'message' => 'Entry budget tidak ditemukan.', 'id' => null];
        }

        $namaBarang = trim((string) ($data['nama_barang'] ?? ''));
        if ($namaBarang === '') {
            return ['success' => false, 'message' => 'Nama barang / item wajib diisi.', 'id' => null];
        }

        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $total  = 0;
        $fields = [];
        foreach ($months as $i => $mk) {
            $val = (float) ($data[$mk] ?? 0);
            $fields[$mk] = $val;
            $total += $val;
        }

        $this->db->table('yp_plan__trans_budget_entry_detail')->insert(array_merge([
            'entry_data_id' => $entryDataId,
            'id_coa'        => (int) ($parent['id_coa'] ?? 0),
            'id_dept'       => (int) ($parent['id_dept'] ?? 0),
            'year_code'     => (int) ($parent['year_code'] ?? 0),
            'nama_barang'   => $namaBarang,
            'total'         => $total,
            'sort_order'    => (int) ($data['sort_order'] ?? 0),
            'created_by'    => $userId,
        ], $fields));

        return [
            'success' => true,
            'message' => 'Detail item berhasil disimpan.',
            'id'      => (int) $this->db->insertID(),
        ];
    }

    /**
     * Hapus item breakdown.
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteDetailItem(int $id): array
    {
        $exists = $this->db->table('yp_plan__trans_budget_entry_detail')
            ->where('id', $id)
            ->get()->getRowArray();

        if (! $exists) {
            return ['success' => false, 'message' => 'Detail item tidak ditemukan.'];
        }

        $this->db->table('yp_plan__trans_budget_entry_detail')->where('id', $id)->delete();

        return ['success' => true, 'message' => 'Detail item berhasil dihapus.'];
    }
}
