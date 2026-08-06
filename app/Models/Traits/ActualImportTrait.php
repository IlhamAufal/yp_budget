<?php

namespace App\Models\Traits;

/**
 * ActualImportTrait — Import data actual dari Excel (PRD Phase 2.3).
 *
 * Menyimpan hasil import Excel ke yp_plan__trans_budget_actual dengan
 * format bulanan (kolom 1..12). Dipakai bersama oleh OPEX GA dan
 * OPEX Selling agar konsisten.
 */
trait ActualImportTrait
{
    /**
     * Simpan actual hasil import Excel (mode replace per coa+dept+tahun).
     *
     * @param string      $year   Tahun anggaran
     * @param array       $rows   Baris hasil ExcelImporter (assoc)
     * @param int         $userId ID user
     * @param string|null $source Tidak digunakan (tabel actual tidak punya kolom source)
     *
     * @return array ['success' => bool, 'message' => string, 'count' => int]
     */
    public function saveActualFromImport(string $year, array $rows, int $userId, ?string $source = null): array
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $saved  = 0;

        $this->db->transStart();

        foreach ($rows as $row) {
            $coa = (int) \App\Libraries\ExcelImporter::column($row, ['id_coa', 'coa', 'main_account', 'account', 'kode_akun', 'kode'], 0);
            if ($coa <= 0) {
                continue;
            }

            $dept = (int) \App\Libraries\ExcelImporter::column($row, ['id_dept', 'dept', 'cost_center', 'cc'], 0);

            $values = [];
            foreach ($months as $i => $mk) {
                $values[(string) ($i + 1)] = \App\Libraries\ExcelImporter::toFloat($row[$mk] ?? 0);
            }
            $total = array_sum($values);

            // Hapus baris actual existing utk (coa, dept, year) — mode replace
            $this->db->table('yp_plan__trans_budget_actual')
                ->where('id_coa', $coa)
                ->where('id_dept', $dept)
                ->where('year_code', $year)
                ->delete();

            $sql = 'INSERT INTO yp_plan__trans_budget_actual
                    (id_coa, id_dept, year_code, created_by, notes,
                     `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

            $notes = (string) \App\Libraries\ExcelImporter::column($row, ['notes', 'keterangan', 'remark'], '');

            $this->db->query($sql, array_merge([
                $coa, $dept, $year, $userId, $notes,
            ], array_values($values)));
            $saved++;
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menyimpan data actual.', 'count' => 0];
        }

        return ['success' => true, 'message' => "Data actual berhasil diimport ({$saved} baris).", 'count' => $saved];
    }
}
