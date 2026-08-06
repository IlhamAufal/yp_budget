<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * FohModel — Factory Overhead (PRD Phase 2.1).
 *
 * Entry budget FOH disimpan ke yp_plan__trans_budget_entry_data dengan
 * source = 'FOH' (konsisten dengan CAPEX/MPP) sehingga mengalir otomatis
 * ke Monitoring & laporan P/L. Actual dibaca dari yp_plan__trans_budget_actual.
 * Breakdown sub-detail COA memakai yp_plan__trans_budget_entry_detail.
 */
class FohModel extends Model
{
    use \App\Models\Traits\BudgetBreakdownTrait;

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /* ------------------------------------------------------------------
     * Master
     * ------------------------------------------------------------------ */

    /**
     * Cost center tipe FOH (dropdown entry & filter).
     */
    public function getCostCenters(): array
    {
        return $this->db->table('gw_plan__master_cost_center')
            ->select('cost_center, cost_desc, cost_center_sap')
            ->where('status', 'A')
            ->where('type', 'FOH')
            ->orderBy('cost_center', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * COA yang relevan untuk FOH (category FOHEXP atau type FOH).
     */
    public function getCoas(): array
    {
        return $this->db->table('gw_plan__master_coa')
            ->select('main_account, cost_center_desc')
            ->groupStart()
                ->where('category', 'FOHEXP')
                ->orWhere('type', 'FOH')
            ->groupEnd()
            ->where('status', 'A')
            ->orderBy('main_account', 'ASC')
            ->get()
            ->getResultArray();
    }

    /* ------------------------------------------------------------------
     * Entry Budget
     * ------------------------------------------------------------------ */

    /**
     * Data budget FOH yang sudah tersimpan per tahun (+ opsional cost center).
     */
    public function getEntryData(string $year, ?string $dept = null): array
    {
        $builder = $this->db->table('yp_plan__trans_budget_entry_data t')
            ->select('t.id, t.id_coa, t.id_dept')
            ->select("COALESCE(c.cost_center_desc, '') AS coa_desc")
            ->select('IFNULL(t.total,0) AS total, COALESCE(t.submit_status, \'DRAFT\') AS submit_status')
            ->select("IFNULL(t.`1`,0) AS jan, IFNULL(t.`2`,0) AS feb, IFNULL(t.`3`,0) AS mar, IFNULL(t.`4`,0) AS apr, IFNULL(t.`5`,0) AS may, IFNULL(t.`6`,0) AS jun")
            ->select("IFNULL(t.`7`,0) AS jul, IFNULL(t.`8`,0) AS aug, IFNULL(t.`9`,0) AS sep, IFNULL(t.`10`,0) AS oct, IFNULL(t.`11`,0) AS nov, IFNULL(t.`12`,0) AS `dec`")
            ->join('gw_plan__master_coa c', 'c.main_account = t.id_coa', 'left')
            ->where('t.year_code', $year)
            ->where('t.source', 'FOH');

        if (! empty($dept)) {
            $builder->where('t.id_dept', $dept);
        }

        return $builder->orderBy('t.id_coa', 'ASC')->get()->getResultArray();
    }

    /**
     * Simpan / timpa seluruh baris budget FOH untuk sebuah cost center.
     *
     * @return array ['success' => bool, 'message' => string, 'count' => int]
     */
    public function saveBudget(string $year, ?string $dept, array $rows, int $userId): array
    {
        if (empty($dept)) {
            return ['success' => false, 'message' => 'Cost Center wajib dipilih.', 'count' => 0];
        }

        $this->db->transStart();

        // Hapus data FOH existing utk dept + tahun (mode replace, sekali simpan)
        $this->db->table('yp_plan__trans_budget_entry_data')
            ->where('year_code', $year)
            ->where('source', 'FOH')
            ->where('id_dept', $dept)
            ->delete();

        // Kolom bulan bernomor 1..12 harus di-escape backtick (numeric identifier)
        $sql = 'INSERT INTO yp_plan__trans_budget_entry_data
                (id_coa, id_dept, total, year_code, created_by, created_date, source, submit_status,
                 `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $saved = 0;
        foreach ($rows as $r) {
            $coa = (int) ($r['id_coa'] ?? 0);
            if ($coa <= 0) {
                continue;
            }

            $total = 0;
            $vals  = [];
            for ($m = 1; $m <= 12; $m++) {
                $val = (float) ($r['m' . $m] ?? 0);
                $vals[] = $val;
                $total += $val;
            }

            $this->db->query($sql, array_merge([
                $coa,
                (int) $dept,
                $total,
                $year,
                $userId,
                date('Y-m-d H:i:s'),
                'FOH',
                'DRAFT',
            ], $vals));
            $saved++;
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menyimpan data FOH.', 'count' => 0];
        }

        return ['success' => true, 'message' => "Data FOH berhasil disimpan ({$saved} baris).", 'count' => $saved];
    }

    /**
     * Workflow Submit — tandai seluruh budget FOH (dept & tahun) sebagai SUBMITTED.
     */
    public function submitBudget(string $year, ?string $dept, int $userId): array
    {
        $builder = $this->db->table('yp_plan__trans_budget_entry_data')
            ->where('year_code', $year)
            ->where('source', 'FOH');

        if (! empty($dept)) {
            $builder->where('id_dept', $dept);
        }

        $result = $builder->update([
            'submit_status' => 'SUBMITTED',
            'submit_by'     => $userId,
            'submit_date'   => date('Y-m-d H:i:s'),
        ]);

        if ($result === false) {
            return ['success' => false, 'message' => 'Gagal melakukan submit FOH.'];
        }

        return ['success' => true, 'message' => 'Budget FOH berhasil di-submit untuk persetujuan.'];
    }

    /* ------------------------------------------------------------------
     * Actual
     * ------------------------------------------------------------------ */

    /**
     * Data actual FOH per tahun (+ opsional cost center).
     */
    public function getActualData(string $year, ?string $dept = null): array
    {
        $builder = $this->db->table('yp_plan__trans_budget_actual a')
            ->select('a.id, a.id_coa, a.id_dept')
            ->select("COALESCE(c.cost_center_desc, '') AS coa_desc")
            ->select('IFNULL(a.total,0) AS total, a.assumption, a.notes')
            ->select("IFNULL(a.`1`,0) AS jan, IFNULL(a.`2`,0) AS feb, IFNULL(a.`3`,0) AS mar, IFNULL(a.`4`,0) AS apr, IFNULL(a.`5`,0) AS may, IFNULL(a.`6`,0) AS jun")
            ->select("IFNULL(a.`7`,0) AS jul, IFNULL(a.`8`,0) AS aug, IFNULL(a.`9`,0) AS sep, IFNULL(a.`10`,0) AS oct, IFNULL(a.`11`,0) AS nov, IFNULL(a.`12`,0) AS `dec`")
            ->join('gw_plan__master_coa c', 'c.main_account = a.id_coa', 'left')
            ->where('a.year_code', $year);

        if (! empty($dept)) {
            $builder->where('a.id_dept', $dept);
        }

        return $builder->orderBy('a.id_coa', 'ASC')->get()->getResultArray();
    }

    /* ------------------------------------------------------------------
     * Summary
     * ------------------------------------------------------------------ */

    /**
     * Ringkasan budget FOH per cost center.
     */
    public function getSummary(string $year): array
    {
        $sql = "SELECT t.id_dept,
                       COALESCE(cc.cost_desc, '') AS cost_desc,
                       IFNULL(SUM(t.`1`),0) AS jan, IFNULL(SUM(t.`2`),0) AS feb,
                       IFNULL(SUM(t.`3`),0) AS mar, IFNULL(SUM(t.`4`),0) AS apr,
                       IFNULL(SUM(t.`5`),0) AS may, IFNULL(SUM(t.`6`),0) AS jun,
                       IFNULL(SUM(t.`7`),0) AS jul, IFNULL(SUM(t.`8`),0) AS aug,
                       IFNULL(SUM(t.`9`),0) AS sep, IFNULL(SUM(t.`10`),0) AS oct,
                       IFNULL(SUM(t.`11`),0) AS nov, IFNULL(SUM(t.`12`),0) AS `dec`,
                       IFNULL(SUM(t.total),0) AS total,
                       SUM(CASE WHEN t.submit_status = 'SUBMITTED' THEN 1 ELSE 0 END) AS submitted_rows
                FROM yp_plan__trans_budget_entry_data t
                LEFT JOIN gw_plan__master_cost_center cc ON cc.cost_center = t.id_dept
                WHERE t.year_code = ? AND t.source = 'FOH'
                GROUP BY t.id_dept, cc.cost_desc
                ORDER BY t.id_dept";

        try {
            return $this->db->query($sql, [$year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'FohModel::getSummary: ' . $e->getMessage());
            return [];
        }
    }

    /* ------------------------------------------------------------------
     * Breakdown Sub-Detail COA (standar 1.5) — lihat BudgetBreakdownTrait
     * ------------------------------------------------------------------ */
}
