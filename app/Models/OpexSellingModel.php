<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * OpexSellingModel — OPEX Selling (PRD Phase 2.1 & 2.3).
 *
 * Entry budget disimpan ke yp_plan__trans_budget_entry_data dengan
 * source = 'SELLING'. Breakdown sub-detail & import actual memakai
 * trait bersama (BudgetBreakdownTrait / ActualImportTrait).
 */
class OpexSellingModel extends Model
{
    use \App\Models\Traits\BudgetBreakdownTrait;
    use \App\Models\Traits\ActualImportTrait;

    /** Sumber data di yp_plan__trans_budget_entry_data. */
    public const SOURCE = 'SELLING';

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /* ------------------------------------------------------------------
     * Master
     * ------------------------------------------------------------------ */

    public function getCostCenters(): array
    {
        return $this->db->table('gw_plan__master_cost_center')
            ->select("cost_center, COALESCE(NULLIF(cost_center_sap,''), CAST(cost_center AS CHAR)) AS cc_code, cost_desc, cost_center_sap")
            ->where('status', 'A')
            ->orderBy('cost_center', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getCoas(): array
    {
        return $this->db->table('gw_plan__master_coa')
            ->select("main_account, COALESCE(NULLIF(id_acct_ext,''), CAST(main_account AS CHAR)) AS acct_code, cost_center_desc")
            ->groupStart()
                ->where('type', 'SELLING')
                ->orWhere('category', 'SELLEXP')
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
     * Header accounts OPEX Selling dengan total budget.
     */
    public function getHeaderAccounts(string $year, ?string $dept = null, string $source = self::SOURCE): array
    {
        $hasSource = \App\Libraries\DbCompat::hasEntrySource();
        $sourceCond = $hasSource ? "AND (t.source = ? OR t.source IS NULL)" : '';

        $sql = "SELECT t.id, t.id_coa,
                       COALESCE(NULLIF(c.id_acct_ext,''), c.main_account, t.id_coa) AS acct_code,
                       COALESCE(c.cost_center_desc, '') AS coa_desc,
                       t.id_dept,
                       IFNULL(SUM(t.total),0) AS total_budget
                FROM yp_plan__trans_budget_entry_data t
                LEFT JOIN gw_plan__master_coa c ON c.main_account = t.id_coa
                WHERE t.year_code = ?
                  {$sourceCond}";

        $params = [$year];
        if ($hasSource) {
            $params[] = $source;
        }

        if (! empty($dept)) {
            $sql .= ' AND t.id_dept = ?';
            $params[] = $dept;
        }

        $sql .= ' GROUP BY t.id_coa, t.id_dept
                  ORDER BY t.id_coa ASC';

        return $this->db->query($sql, $params)->getResultArray();
    }

    /**
     * Data budget selling tersimpan per tahun (+ opsional cost center).
     */
    public function getEntryData(string $year, ?string $dept = null, string $source = self::SOURCE): array
    {
        $builder = $this->db->table('yp_plan__trans_budget_entry_data t')
            ->select('t.id, t.id_coa, t.id_dept')
            ->select("COALESCE(NULLIF(c.id_acct_ext,''), c.main_account, 0) AS acct_code")
            ->select("COALESCE(c.cost_center_desc, '') AS coa_desc")
            ->select('IFNULL(t.total,0) AS total, ' . \App\Libraries\DbCompat::submitStatusExpr())
            ->select("IFNULL(t.`1`,0) AS jan, IFNULL(t.`2`,0) AS feb, IFNULL(t.`3`,0) AS mar, IFNULL(t.`4`,0) AS apr, IFNULL(t.`5`,0) AS may, IFNULL(t.`6`,0) AS jun")
            ->select("IFNULL(t.`7`,0) AS jul, IFNULL(t.`8`,0) AS aug, IFNULL(t.`9`,0) AS sep, IFNULL(t.`10`,0) AS oct, IFNULL(t.`11`,0) AS nov, IFNULL(t.`12`,0) AS `dec`")
            ->join('gw_plan__master_coa c', 'c.main_account = t.id_coa', 'left')
            ->where('t.year_code', $year);

        if (\App\Libraries\DbCompat::hasEntrySource()) {
            $builder->groupStart()
                ->where('t.source', $source)
                ->orWhere('t.source IS NULL')
            ->groupEnd();
        }

        if (! empty($dept)) {
            $builder->where('t.id_dept', $dept);
        }

        return $builder->orderBy('t.id_coa', 'ASC')->get()->getResultArray();
    }

    /**
     * Matrix budget + actual per sub-account untuk sebuah header account.
     */
    public function getDetailMatrix(string $year, ?string $dept, string $header, string $source = self::SOURCE): array
    {
        $hasSource = \App\Libraries\DbCompat::hasEntrySource();
        $sourceCond = $hasSource ? "AND (t.source = ? OR t.source IS NULL)" : '';

        $budgetSql = "SELECT t.id, t.id_coa, t.id_dept,
                             IFNULL(t.`1`,0) AS b_jan, IFNULL(t.`2`,0) AS b_feb, IFNULL(t.`3`,0) AS b_mar,
                             IFNULL(t.`4`,0) AS b_apr, IFNULL(t.`5`,0) AS b_may, IFNULL(t.`6`,0) AS b_jun,
                             IFNULL(t.`7`,0) AS b_jul, IFNULL(t.`8`,0) AS b_aug, IFNULL(t.`9`,0) AS b_sep,
                             IFNULL(t.`10`,0) AS b_oct, IFNULL(t.`11`,0) AS b_nov, IFNULL(t.`12`,0) AS b_dec,
                             t.total AS b_total
                      FROM yp_plan__trans_budget_entry_data t
                      WHERE t.year_code = ? AND t.id_dept = ?
                        {$sourceCond}";

        $params = [$year, $dept];
        if ($hasSource) {
            $params[] = $source;
        }

        $budgetRows = $this->db->query($budgetSql, $params)->getResultArray();

        $actualSql = "SELECT a.id_coa, a.id_dept,
                             IFNULL(a.`1`,0) AS a_jan, IFNULL(a.`2`,0) AS a_feb, IFNULL(a.`3`,0) AS a_mar,
                             IFNULL(a.`4`,0) AS a_apr, IFNULL(a.`5`,0) AS a_may, IFNULL(a.`6`,0) AS a_jun,
                             IFNULL(a.`7`,0) AS a_jul, IFNULL(a.`8`,0) AS a_aug, IFNULL(a.`9`,0) AS a_sep,
                             IFNULL(a.`10`,0) AS a_oct, IFNULL(a.`11`,0) AS a_nov, IFNULL(a.`12`,0) AS a_dec,
                             (IFNULL(a.`1`,0)+IFNULL(a.`2`,0)+IFNULL(a.`3`,0)+IFNULL(a.`4`,0)+IFNULL(a.`5`,0)+IFNULL(a.`6`,0)+IFNULL(a.`7`,0)+IFNULL(a.`8`,0)+IFNULL(a.`9`,0)+IFNULL(a.`10`,0)+IFNULL(a.`11`,0)+IFNULL(a.`12`,0)) AS a_total
                      FROM yp_plan__trans_budget_actual a
                      WHERE a.year_code = ? AND a.id_dept = ?";

        $actualRows = $this->db->query($actualSql, [$year, $dept])->getResultArray();

        $actualMap = [];
        foreach ($actualRows as $ar) {
            $actualMap[$ar['id_coa']] = $ar;
        }

        $matrix = [];
        foreach ($budgetRows as $br) {
            $subAcct = $br['id_coa'];
            $ar      = $actualMap[$subAcct] ?? [];

            $matrix[] = [
                'id'       => $br['id'],
                'id_coa'   => $subAcct,
                'id_dept'  => $dept,
                'b_jan' => $br['b_jan'], 'b_feb' => $br['b_feb'], 'b_mar' => $br['b_mar'],
                'b_apr' => $br['b_apr'], 'b_may' => $br['b_may'], 'b_jun' => $br['b_jun'],
                'b_jul' => $br['b_jul'], 'b_aug' => $br['b_aug'], 'b_sep' => $br['b_sep'],
                'b_oct' => $br['b_oct'], 'b_nov' => $br['b_nov'], 'b_dec' => $br['b_dec'],
                'b_total' => $br['b_total'],
                'a_jan' => $ar['a_jan'] ?? 0, 'a_feb' => $ar['a_feb'] ?? 0, 'a_mar' => $ar['a_mar'] ?? 0,
                'a_apr' => $ar['a_apr'] ?? 0, 'a_may' => $ar['a_may'] ?? 0, 'a_jun' => $ar['a_jun'] ?? 0,
                'a_jul' => $ar['a_jul'] ?? 0, 'a_aug' => $ar['a_aug'] ?? 0, 'a_sep' => $ar['a_sep'] ?? 0,
                'a_oct' => $ar['a_oct'] ?? 0, 'a_nov' => $ar['a_nov'] ?? 0, 'a_dec' => $ar['a_dec'] ?? 0,
                'a_total' => $ar['a_total'] ?? 0,
            ];
        }

        return $matrix;
    }

    /**
     * Batch simpan detail breakdown items (dari modal detail).
     */
    public function saveDetailItemsBatch(int $entryDataId, array $items, int $userId): array
    {
        if ($entryDataId <= 0) {
            return ['success' => false, 'message' => 'ID entry budget tidak valid.', 'count' => 0];
        }

        $parent = $this->db->table('yp_plan__trans_budget_entry_data')
            ->where('id', $entryDataId)
            ->get()->getRowArray();

        if (! $parent) {
            return ['success' => false, 'message' => 'Entry budget tidak ditemukan.', 'count' => 0];
        }

        $this->db->table('yp_plan__trans_budget_entry_detail')
            ->where('entry_data_id', $entryDataId)
            ->delete();

        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
        $saved  = 0;

        foreach ($items as $item) {
            $namaBarang = trim((string) ($item['nama_barang'] ?? ''));
            if ($namaBarang === '') {
                continue;
            }

            $total  = 0;
            $fields = [];
            foreach ($months as $mk) {
                $val       = (float) ($item[$mk] ?? 0);
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
                'sort_order'    => (int) ($item['sort_order'] ?? ($saved + 1)),
                'created_by'    => $userId,
            ], $fields));
            $saved++;
        }

        return ['success' => true, 'message' => "Detail breakdown berhasil disimpan ({$saved} item).", 'count' => $saved];
    }

    /* ------------------------------------------------------------------
     * Actual
     * ------------------------------------------------------------------ */

    public function getActualData(string $year, ?string $dept = null): array
    {
        $builder = $this->db->table('yp_plan__trans_budget_actual a')
            ->select('a.id, a.id_coa, a.id_dept')
            ->select("COALESCE(c.cost_center_desc, '') AS coa_desc")
            ->select('(IFNULL(a.`1`,0)+IFNULL(a.`2`,0)+IFNULL(a.`3`,0)+IFNULL(a.`4`,0)+IFNULL(a.`5`,0)+IFNULL(a.`6`,0)+IFNULL(a.`7`,0)+IFNULL(a.`8`,0)+IFNULL(a.`9`,0)+IFNULL(a.`10`,0)+IFNULL(a.`11`,0)+IFNULL(a.`12`,0)) AS total, a.assumption, a.notes')
            ->select("IFNULL(a.`1`,0) AS jan, IFNULL(a.`2`,0) AS feb, IFNULL(a.`3`,0) AS mar, IFNULL(a.`4`,0) AS apr, IFNULL(a.`5`,0) AS may, IFNULL(a.`6`,0) AS jun")
            ->select("IFNULL(a.`7`,0) AS jul, IFNULL(a.`8`,0) AS aug, IFNULL(a.`9`,0) AS sep, IFNULL(a.`10`,0) AS oct, IFNULL(a.`11`,0) AS nov, IFNULL(a.`12`,0) AS `dec`")
            ->join('gw_plan__master_coa c', 'c.main_account = a.id_coa', 'left')
            ->where('a.year_code', $year);

        if (! empty($dept)) {
            $builder->where('a.id_dept', $dept);
        }

        return $builder->orderBy('a.id_coa', 'ASC')->get()->getResultArray();
    }
}
