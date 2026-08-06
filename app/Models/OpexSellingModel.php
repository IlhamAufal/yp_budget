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

    /**
     * Daftar cost center (mengikuti perilaku legacy: semua aktif).
     */
    public function getCostCenters(): array
    {
        return $this->db->table('gw_plan__master_cost_center')
            ->select('cost_center, cost_desc, cost_center_sap')
            ->where('status', 'A')
            ->orderBy('cost_center', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * COA relevan untuk selling (type SELLING / kategori SELLEXP).
     */
    public function getCoas(): array
    {
        return $this->db->table('gw_plan__master_coa')
            ->select('main_account, cost_center_desc')
            ->groupStart()
                ->where('type', 'SELLING')
                ->orWhere('category', 'SELLEXP')
            ->groupEnd()
            ->where('status', 'A')
            ->orderBy('main_account', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Data budget selling tersimpan per tahun (+ opsional cost center).
     */
    public function getEntryData(string $year, ?string $dept = null, string $source = self::SOURCE): array
    {
        $builder = $this->db->table('yp_plan__trans_budget_entry_data t')
            ->select('t.id, t.id_coa, t.id_dept')
            ->select("COALESCE(c.cost_center_desc, '') AS coa_desc")
            ->select('IFNULL(t.total,0) AS total, COALESCE(t.submit_status, \'DRAFT\') AS submit_status')
            ->select("IFNULL(t.`1`,0) AS jan, IFNULL(t.`2`,0) AS feb, IFNULL(t.`3`,0) AS mar, IFNULL(t.`4`,0) AS apr, IFNULL(t.`5`,0) AS may, IFNULL(t.`6`,0) AS jun")
            ->select("IFNULL(t.`7`,0) AS jul, IFNULL(t.`8`,0) AS aug, IFNULL(t.`9`,0) AS sep, IFNULL(t.`10`,0) AS oct, IFNULL(t.`11`,0) AS nov, IFNULL(t.`12`,0) AS `dec`")
            ->join('gw_plan__master_coa c', 'c.main_account = t.id_coa', 'left')
            ->where('t.year_code', $year)
            ->groupStart()
                ->where('t.source', $source)
                ->orWhere('t.source IS NULL')
            ->groupEnd();

        if (! empty($dept)) {
            $builder->where('t.id_dept', $dept);
        }

        return $builder->orderBy('t.id_coa', 'ASC')->get()->getResultArray();
    }

    /**
     * Data actual selling per tahun (+ opsional cost center).
     */
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
