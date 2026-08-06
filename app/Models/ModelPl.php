<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ModelPl — Ringkasan Monitoring (OPEX/FOH/MPP/CAPEX) & Laporan P/L.
 * Query disesuaikan dengan skema legacy nyata (kolom bulan bernomor 1..12).
 */
class ModelPl extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Ringkasan Budget per Cost Center untuk tab OPEX GA / FOH.
     * Return: id_dept, cost_desc, tags, JAN..DEC, TOT
     */
    public function get_curr_summary(string $year, ?string $idDept = null, ?string $userLevel = null, string $type = 'OPEX'): array
    {
        $sql = "SELECT t.id_dept,
                       COALESCE(cc.cost_desc, '') AS cost_desc,
                       COALESCE(MAX(t.created_by), '') AS tags,
                       IFNULL(SUM(t.`1`),0) AS JAN, IFNULL(SUM(t.`2`),0) AS FEB,
                       IFNULL(SUM(t.`3`),0) AS MAR, IFNULL(SUM(t.`4`),0) AS APR,
                       IFNULL(SUM(t.`5`),0) AS MAY, IFNULL(SUM(t.`6`),0) AS JUN,
                       IFNULL(SUM(t.`7`),0) AS JUL, IFNULL(SUM(t.`8`),0) AS AUG,
                       IFNULL(SUM(t.`9`),0) AS SEP, IFNULL(SUM(t.`10`),0) AS OCT,
                       IFNULL(SUM(t.`11`),0) AS NOV, IFNULL(SUM(t.`12`),0) AS `DEC`,
                       IFNULL(SUM(t.total),0) AS TOT
                FROM yp_plan__trans_budget_entry_data t
                LEFT JOIN gw_plan__master_cost_center cc ON cc.cost_center = t.id_dept
                WHERE t.year_code = ? AND cc.type = ?
                GROUP BY t.id_dept, cc.cost_desc
                ORDER BY t.id_dept";

        $params = [$year, $type];

        try {
            return $this->db->query($sql, $params)->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_curr_summary: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ringkasan MPP (New Headcount) per Cost Center.
     */
    public function get_mpp_summary(string $year, ?string $idDept = null, ?string $userLevel = null, string $type = 'OPEX'): array
    {
        $sql = "SELECT h.id_dept AS cost_center,
                       COALESCE(cc.cost_desc, '') AS cost_desc,
                       COALESCE(tm.desc_mpp, '') AS desc_mpp,
                       COALESCE(h.staff_name, '') AS staff_name,
                       IFNULL(SUM(d.`1`),0) AS JAN, IFNULL(SUM(d.`2`),0) AS FEB,
                       IFNULL(SUM(d.`3`),0) AS MAR, IFNULL(SUM(d.`4`),0) AS APR,
                       IFNULL(SUM(d.`5`),0) AS MAY, IFNULL(SUM(d.`6`),0) AS JUN,
                       IFNULL(SUM(d.`7`),0) AS JUL, IFNULL(SUM(d.`8`),0) AS AUG,
                       IFNULL(SUM(d.`9`),0) AS SEP, IFNULL(SUM(d.`10`),0) AS OCT,
                       IFNULL(SUM(d.`11`),0) AS NOV, IFNULL(SUM(d.`12`),0) AS `DEC`,
                       IFNULL(SUM(d.grand_total),0) AS TOT,
                       '' AS notes,
                       COALESCE(s.salary, 0) AS salary,
                       IFNULL(SUM(d.`1`),0) * COALESCE(s.salary,0) AS JAN_AMT,
                       IFNULL(SUM(d.`2`),0) * COALESCE(s.salary,0) AS FEB_AMT,
                       IFNULL(SUM(d.`3`),0) * COALESCE(s.salary,0) AS MAR_AMT,
                       IFNULL(SUM(d.`4`),0) * COALESCE(s.salary,0) AS APR_AMT,
                       IFNULL(SUM(d.`5`),0) * COALESCE(s.salary,0) AS MAY_AMT,
                       IFNULL(SUM(d.`6`),0) * COALESCE(s.salary,0) AS JUN_AMT,
                       IFNULL(SUM(d.`7`),0) * COALESCE(s.salary,0) AS JUL_AMT,
                       IFNULL(SUM(d.`8`),0) * COALESCE(s.salary,0) AS AUG_AMT,
                       IFNULL(SUM(d.`9`),0) * COALESCE(s.salary,0) AS SEP_AMT,
                       IFNULL(SUM(d.`10`),0) * COALESCE(s.salary,0) AS OCT_AMT,
                       IFNULL(SUM(d.`11`),0) * COALESCE(s.salary,0) AS NOV_AMT,
                       IFNULL(SUM(d.`12`),0) * COALESCE(s.salary,0) AS `DEC_AMT`,
                       IFNULL(SUM(d.grand_total),0) * COALESCE(s.salary,0) AS TOT_AMT
                FROM yp_plan__trans_mpp_header h
                LEFT JOIN gw_plan__master_cost_center cc ON cc.cost_center = h.id_dept
                LEFT JOIN yp_plan__master_tipe_mpp tm ON tm.id_mpp = h.id_tipe
                LEFT JOIN yp_plan__trans_mpp_detail d ON d.id_header = h.id
                LEFT JOIN yp_plan__master_mpp_salary s
                       ON s.dept_id = h.id_dept AND s.type = h.id_tipe AND s.year_code = h.year_code AND s.status = 'A'
                WHERE h.year_code = ? AND cc.type = ?
                GROUP BY h.id_dept, cc.cost_desc, tm.desc_mpp, h.staff_name, s.salary
                ORDER BY h.id_dept";

        try {
            return $this->db->query($sql, [$year, $type])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_mpp_summary: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ringkasan Depresiasi CAPEX per Cost Center (OPEX & FOH).
     */
    public function get_capex_summary(string $year, ?string $idDept = null, ?string $userLevel = null): array
    {
        $sql = "SELECT c.dept_id AS id_dept,
                       COALESCE(cc.cost_desc, '') AS cost_desc,
                       IFNULL(SUM(c.`1`),0) AS JAN, IFNULL(SUM(c.`2`),0) AS FEB,
                       IFNULL(SUM(c.`3`),0) AS MAR, IFNULL(SUM(c.`4`),0) AS APR,
                       IFNULL(SUM(c.`5`),0) AS MAY, IFNULL(SUM(c.`6`),0) AS JUN,
                       IFNULL(SUM(c.`7`),0) AS JUL, IFNULL(SUM(c.`8`),0) AS AUG,
                       IFNULL(SUM(c.`9`),0) AS SEP, IFNULL(SUM(c.`10`),0) AS OCT,
                       IFNULL(SUM(c.`11`),0) AS NOV, IFNULL(SUM(c.`12`),0) AS `DEC`,
                       IFNULL(SUM(c.total),0) AS TOT
                FROM yp_plan__trans_capex_entry_depreciation c
                LEFT JOIN gw_plan__master_cost_center cc ON cc.cost_center = c.dept_id
                WHERE c.year_code = ?
                GROUP BY c.dept_id, cc.cost_desc
                ORDER BY c.dept_id";

        try {
            return $this->db->query($sql, [$year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_capex_summary: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Detail transaksi per Cost Center untuk modal monitoring.
     */
    public function get_detail_by_dept(string $year, string $idDept, string $type): array
    {
        $sql = "SELECT t.id_coa, t.id_dept, COALESCE(c.cost_center_desc, '') AS cost_center_desc, t.total,
                       IFNULL(t.`1`,0) AS m1, IFNULL(t.`2`,0) AS m2, IFNULL(t.`3`,0) AS m3,
                       IFNULL(t.`4`,0) AS m4, IFNULL(t.`5`,0) AS m5, IFNULL(t.`6`,0) AS m6,
                       IFNULL(t.`7`,0) AS m7, IFNULL(t.`8`,0) AS m8, IFNULL(t.`9`,0) AS m9,
                       IFNULL(t.`10`,0) AS m10, IFNULL(t.`11`,0) AS m11, IFNULL(t.`12`,0) AS m12
                FROM yp_plan__trans_budget_entry_data t
                LEFT JOIN gw_plan__master_coa c ON c.main_account = t.id_coa
                WHERE t.year_code = ? AND t.id_dept = ?";

        try {
            return $this->db->query($sql, [$year, $idDept])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_detail_by_dept: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Summary P/L: per akun COA, bulanan + total.
     * Return: account_desc, jan..dec, total, is_header
     */
    public function get_pl_summary(string $year): array
    {
        $sql = "SELECT COALESCE(c.cost_center_desc, '') AS account_desc,
                       COALESCE(c.main_account, 0) AS account_code,
                       IFNULL(SUM(t.`1`),0) AS jan, IFNULL(SUM(t.`2`),0) AS feb,
                       IFNULL(SUM(t.`3`),0) AS mar, IFNULL(SUM(t.`4`),0) AS apr,
                       IFNULL(SUM(t.`5`),0) AS may, IFNULL(SUM(t.`6`),0) AS jun,
                       IFNULL(SUM(t.`7`),0) AS jul, IFNULL(SUM(t.`8`),0) AS aug,
                       IFNULL(SUM(t.`9`),0) AS sep, IFNULL(SUM(t.`10`),0) AS oct,
                       IFNULL(SUM(t.`11`),0) AS nov, IFNULL(SUM(t.`12`),0) AS `dec`,
                       IFNULL(SUM(t.total),0) AS total, 0 AS is_header
                FROM yp_plan__trans_budget_entry_data t
                LEFT JOIN gw_plan__master_coa c ON c.main_account = t.id_coa
                WHERE t.year_code = ?
                GROUP BY c.cost_center_desc, c.main_account
                ORDER BY c.main_account";

        try {
            return $this->db->query($sql, [$year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_pl_summary: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Daftar akun P/L dengan total tahunan.
     * Return: account_code, account_desc, type, annual_total
     */
    public function get_pl_details(string $year): array
    {
        $sql = "SELECT c.main_account AS account_code, COALESCE(c.cost_center_desc, '') AS account_desc,
                       COALESCE(c.type, 'Expense') AS type,
                       IFNULL(SUM(t.total),0) AS annual_total
                FROM gw_plan__master_coa c
                LEFT JOIN yp_plan__trans_budget_entry_data t
                       ON t.id_coa = c.main_account AND t.year_code = ?
                WHERE c.status = 'A'
                GROUP BY c.main_account, c.cost_center_desc, c.type
                ORDER BY c.main_account";

        try {
            return $this->db->query($sql, [$year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_pl_details: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Detail transaksi per akun untuk modal P/L.
     * Return: cost_center, remarks, amount
     */
    public function get_detail_account(string $account, string $year): array
    {
        $sql = "SELECT t.id_dept AS cost_center, COALESCE(c.cost_center_desc, '') AS remarks,
                       IFNULL(t.total,0) AS amount
                FROM yp_plan__trans_budget_entry_data t
                LEFT JOIN gw_plan__master_coa c ON c.main_account = t.id_coa
                WHERE t.id_coa = ? AND t.year_code = ?";

        try {
            return $this->db->query($sql, [$account, $year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_detail_account: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Daftar Cost Center aktif untuk dropdown filter P/L.
     */
    public function get_departments(): array
    {
        try {
            return $this->db->table('gw_plan__master_cost_center')
                ->select('id_cost_center AS id_dept, cost_center, cost_desc')
                ->where('status', 'A')
                ->orderBy('cost_center', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
