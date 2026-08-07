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
                       COALESCE(NULLIF(cc.cost_center_sap,''), CAST(cc.cost_center AS CHAR)) AS cc_sap,
                       COALESCE(MAX(t.created_by), '') AS tags,
                       IFNULL(SUM(t.`1`),0) AS JAN, IFNULL(SUM(t.`2`),0) AS FEB,
                       IFNULL(SUM(t.`3`),0) AS MAR, IFNULL(SUM(t.`4`),0) AS APR,
                       IFNULL(SUM(t.`5`),0) AS MAY, IFNULL(SUM(t.`6`),0) AS JUN,
                       IFNULL(SUM(t.`7`),0) AS JUL, IFNULL(SUM(t.`8`),0) AS AUG,
                       IFNULL(SUM(t.`9`),0) AS SEP, IFNULL(SUM(t.`10`),0) AS OCT,
                       IFNULL(SUM(t.`11`),0) AS NOV, IFNULL(SUM(t.`12`),0) AS `DEC`,
                       (IFNULL(SUM(t.`1`),0)+IFNULL(SUM(t.`2`),0)+IFNULL(SUM(t.`3`),0)+IFNULL(SUM(t.`4`),0)
                       +IFNULL(SUM(t.`5`),0)+IFNULL(SUM(t.`6`),0)+IFNULL(SUM(t.`7`),0)+IFNULL(SUM(t.`8`),0)
                       +IFNULL(SUM(t.`9`),0)+IFNULL(SUM(t.`10`),0)+IFNULL(SUM(t.`11`),0)+IFNULL(SUM(t.`12`),0)) AS TOT
                FROM yp_plan__trans_budget_entry_data t
                LEFT JOIN gw_plan__master_cost_center cc ON cc.cost_center = t.id_dept
                WHERE t.year_code = ? AND cc.type = ?
                GROUP BY t.id_dept, cc.cost_desc, cc.cost_center_sap
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
                       COALESCE(NULLIF(cc.cost_center_sap,''), CAST(h.id_dept AS CHAR)) AS cc_sap,
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
                GROUP BY h.id_dept, cc.cost_desc, cc.cost_center_sap, tm.desc_mpp, h.staff_name, s.salary
                ORDER BY h.id_dept";

        try {
            return $this->db->query($sql, [$year, $type])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_mpp_summary: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * @deprecated Gunakan get_capex_monitoring() untuk tab CAPEX di monitoring.
     * Ringkasan depresiasi CAPEX per Cost Center (OPEX & FOH).
     */
    public function get_capex_summary(string $year, ?string $idDept = null, ?string $userLevel = null): array
    {
        $sql = "SELECT c.dept_id AS id_dept,
                       COALESCE(cc.cost_desc, '') AS cost_desc,
                       COALESCE(cc.cost_center_sap, c.dept_id, '') AS main_account,
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
                GROUP BY c.dept_id, cc.cost_desc, cc.cost_center_sap
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
        $sql = "SELECT t.id_coa, t.id_dept,
                       COALESCE(NULLIF(c.id_acct_ext,''), c.main_account, 0) AS acct_code,
                       COALESCE(c.cost_center_desc, '') AS cost_center_desc,
                       (IFNULL(t.`1`,0)+IFNULL(t.`2`,0)+IFNULL(t.`3`,0)+IFNULL(t.`4`,0)
                       +IFNULL(t.`5`,0)+IFNULL(t.`6`,0)+IFNULL(t.`7`,0)+IFNULL(t.`8`,0)
                       +IFNULL(t.`9`,0)+IFNULL(t.`10`,0)+IFNULL(t.`11`,0)+IFNULL(t.`12`,0)) AS total,
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
                       COALESCE(NULLIF(c.id_acct_ext,''), c.main_account, 0) AS account_code,
                       IFNULL(SUM(t.`1`),0) AS jan, IFNULL(SUM(t.`2`),0) AS feb,
                       IFNULL(SUM(t.`3`),0) AS mar, IFNULL(SUM(t.`4`),0) AS apr,
                       IFNULL(SUM(t.`5`),0) AS may, IFNULL(SUM(t.`6`),0) AS jun,
                       IFNULL(SUM(t.`7`),0) AS jul, IFNULL(SUM(t.`8`),0) AS aug,
                       IFNULL(SUM(t.`9`),0) AS sep, IFNULL(SUM(t.`10`),0) AS oct,
                       IFNULL(SUM(t.`11`),0) AS nov, IFNULL(SUM(t.`12`),0) AS `dec`,
                       (IFNULL(SUM(t.`1`),0)+IFNULL(SUM(t.`2`),0)+IFNULL(SUM(t.`3`),0)+IFNULL(SUM(t.`4`),0)
                       +IFNULL(SUM(t.`5`),0)+IFNULL(SUM(t.`6`),0)+IFNULL(SUM(t.`7`),0)+IFNULL(SUM(t.`8`),0)
                       +IFNULL(SUM(t.`9`),0)+IFNULL(SUM(t.`10`),0)+IFNULL(SUM(t.`11`),0)+IFNULL(SUM(t.`12`),0)) AS total,
                       0 AS is_header
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
        $sql = "SELECT COALESCE(NULLIF(c.id_acct_ext,''), c.main_account, 0) AS account_code,
                       c.main_account AS id_coa,
                       COALESCE(c.cost_center_desc, '') AS account_desc,
                       COALESCE(c.type, 'Expense') AS type,
                       (IFNULL(SUM(t.`1`),0)+IFNULL(SUM(t.`2`),0)+IFNULL(SUM(t.`3`),0)+IFNULL(SUM(t.`4`),0)
                       +IFNULL(SUM(t.`5`),0)+IFNULL(SUM(t.`6`),0)+IFNULL(SUM(t.`7`),0)+IFNULL(SUM(t.`8`),0)
                       +IFNULL(SUM(t.`9`),0)+IFNULL(SUM(t.`10`),0)+IFNULL(SUM(t.`11`),0)+IFNULL(SUM(t.`12`),0)) AS annual_total
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
        $sql = "SELECT COALESCE(NULLIF(cc.cost_center_sap,''), CAST(t.id_dept AS CHAR)) AS cost_center,
                       COALESCE(c.cost_center_desc, '') AS remarks,
                       (IFNULL(t.`1`,0)+IFNULL(t.`2`,0)+IFNULL(t.`3`,0)+IFNULL(t.`4`,0)
                       +IFNULL(t.`5`,0)+IFNULL(t.`6`,0)+IFNULL(t.`7`,0)+IFNULL(t.`8`,0)
                       +IFNULL(t.`9`,0)+IFNULL(t.`10`,0)+IFNULL(t.`11`,0)+IFNULL(t.`12`,0)) AS amount
                FROM yp_plan__trans_budget_entry_data t
                LEFT JOIN gw_plan__master_coa c ON c.main_account = t.id_coa
                LEFT JOIN gw_plan__master_cost_center cc ON cc.cost_center = t.id_dept
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
                ->select("id_cost_center AS id_dept, cost_center, COALESCE(NULLIF(cost_center_sap,''), CAST(cost_center AS CHAR)) AS cc_sap, cost_desc")
                ->where('status', 'A')
                ->orderBy('cost_center', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /* ------------------------------------------------------------------
     * Phase 3.2 — P/L Report per Bagian (Sections) & Notes/Adjustment
     * ------------------------------------------------------------------ */

    /**
     * Definisi bagian P/L (section) dan sumber datanya.
     * type null = section khusus (SALES dari tabel transaksi sales).
     */
    public const PL_SECTIONS = [
        'SALES'        => ['label' => 'Sales Revenue',               'type' => null],
        'COGS'         => ['label' => 'COGS (Direct Labor)',         'type' => 'DL'],
        'SELLING'      => ['label' => 'Selling Expense',             'type' => 'SELLING'],
        'GENERAL'      => ['label' => 'General & Admin',             'type' => 'GA'],
        'FOH'          => ['label' => 'Factory Overhead (FOH)',      'type' => 'FOH'],
        'DEPRECIATION' => ['label' => 'Depreciation (CAPEX)',        'type' => 'CAPEX'],
        'OTHER'        => ['label' => 'Other Expense / Income',      'type' => 'OE'],
    ];

    /** @var string[] Bulan bernomor 1..12 → nama kolom `{nama}_rev` di tabel sales. */
    private const MONTH_NAMES = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

    /**
     * Map tipe COA (gw_plan__master_coa.type) ke kode section P/L.
     */
    private function typeToSection(string $type): string
    {
        $map = [
            'DL'      => 'COGS',
            'SELLING' => 'SELLING',
            'GA'      => 'GENERAL',
            'FOH'     => 'FOH',
            'CAPEX'   => 'DEPRECIATION',
            'OE'      => 'OTHER',
        ];

        return $map[$type] ?? 'OTHER';
    }

    /**
     * Ringkasan P/L per bagian: bulanan + total untuk seluruh section.
     * Return: [ ['code','label','m1'..'m12','total'], ... ] urut sesuai definisi.
     */
    public function get_pl_sections(string $year): array
    {
        $sections = [];
        foreach (self::PL_SECTIONS as $code => $def) {
            $row = ['code' => $code, 'label' => $def['label']];
            foreach (range(1, 12) as $m) {
                $row['m' . $m] = 0.0;
            }
            $row['total'] = 0.0;
            $sections[$code] = $row;
        }

        // 1) Bagian berbasis budget (COGS/SELLING/GENERAL/FOH/DEPRECIATION/OTHER)
        $monthSelect = [];
        foreach (range(1, 12) as $m) {
            $monthSelect[] = "IFNULL(SUM(t.`{$m}`),0) AS m{$m}";
        }
        $monthSelectSql = implode(', ', $monthSelect);

        $sql = "SELECT COALESCE(c.type, 'OE') AS sec, {$monthSelectSql},
                       (IFNULL(SUM(t.`1`),0)+IFNULL(SUM(t.`2`),0)+IFNULL(SUM(t.`3`),0)+IFNULL(SUM(t.`4`),0)
                       +IFNULL(SUM(t.`5`),0)+IFNULL(SUM(t.`6`),0)+IFNULL(SUM(t.`7`),0)+IFNULL(SUM(t.`8`),0)
                       +IFNULL(SUM(t.`9`),0)+IFNULL(SUM(t.`10`),0)+IFNULL(SUM(t.`11`),0)+IFNULL(SUM(t.`12`),0)) AS total
                FROM yp_plan__trans_budget_entry_data t
                LEFT JOIN gw_plan__master_coa c ON c.main_account = t.id_coa
                WHERE t.year_code = ?
                GROUP BY c.type";

        try {
            $rows = $this->db->query($sql, [$year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_pl_sections(budget): ' . $e->getMessage());
            $rows = [];
        }

        foreach ($rows as $row) {
            $code = $this->typeToSection((string) $row['sec']);
            if (! isset($sections[$code])) {
                continue;
            }
            foreach (range(1, 12) as $m) {
                $sections[$code]['m' . $m] = (float) ($row['m' . $m] ?? 0);
            }
            $sections[$code]['total'] = (float) ($row['total'] ?? 0);
        }

        // 2) Bagian SALES dari transaksi sales domestic + export
        $salesRow = $this->getSalesSectionRow($year);
        if ($salesRow !== null) {
            $sections['SALES'] = array_merge($sections['SALES'], $salesRow);
        }

        return array_values($sections);
    }

    /**
     * Total revenue bulanan dari trans_sales_domestic + trans_sales_export.
     */
    private function getSalesSectionRow(string $year): ?array
    {
        $selects = [];
        foreach (self::MONTH_NAMES as $i => $name) {
            $selects[] = "IFNULL(SUM({$name}_rev),0) AS m" . ($i + 1);
        }
        $selectSql = implode(', ', $selects);
        $totalExpr = implode(' + ', array_map(fn ($name) => "IFNULL(SUM({$name}_rev),0)", self::MONTH_NAMES));

        $sql = "SELECT {$selectSql}, ({$totalExpr}) AS total FROM yp_plan__trans_sales_domestic WHERE year_code = ?";
        $sqlE = "SELECT {$selectSql}, ({$totalExpr}) AS total FROM yp_plan__trans_sales_export WHERE year_code = ?";

        try {
            $dom = $this->db->query($sql, [$year])->getRowArray() ?? [];
            $exp = $this->db->query($sqlE, [$year])->getRowArray() ?? [];
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::getSalesSectionRow: ' . $e->getMessage());

            return null;
        }

        $row = ['total' => 0.0];
        foreach (range(1, 12) as $m) {
            $row['m' . $m] = (float) ($dom['m' . $m] ?? 0) + (float) ($exp['m' . $m] ?? 0);
            $row['total'] += $row['m' . $m];
        }

        return $row;
    }

    /**
     * Detail baris per akun untuk satu bagian P/L (untuk modal AJAX).
     * Return: account, account_desc, cost_center, m1..m12, total.
     */
    public function get_pl_section_detail(string $year, string $section): array
    {
        $code = strtoupper($section);
        if (! isset(self::PL_SECTIONS[$code])) {
            return [];
        }

        if ($code === 'SALES') {
            return $this->getSalesSectionDetail($year);
        }

        $type = self::PL_SECTIONS[$code]['type'];
        $monthSelect = [];
        foreach (range(1, 12) as $m) {
            $monthSelect[] = "IFNULL(t.`{$m}`,0) AS m{$m}";
        }
        $monthSelectSql = implode(', ', $monthSelect);

        $sql = "SELECT COALESCE(NULLIF(c.id_acct_ext,''), t.id_coa, 0) AS account,
                       COALESCE(c.cost_center_desc, '') AS account_desc,
                       COALESCE(NULLIF(cc.cost_center_sap,''), CAST(t.id_dept AS CHAR)) AS cost_center,
                       {$monthSelectSql},
                       (IFNULL(t.`1`,0)+IFNULL(t.`2`,0)+IFNULL(t.`3`,0)+IFNULL(t.`4`,0)
                       +IFNULL(t.`5`,0)+IFNULL(t.`6`,0)+IFNULL(t.`7`,0)+IFNULL(t.`8`,0)
                       +IFNULL(t.`9`,0)+IFNULL(t.`10`,0)+IFNULL(t.`11`,0)+IFNULL(t.`12`,0)) AS total
                FROM yp_plan__trans_budget_entry_data t
                LEFT JOIN gw_plan__master_coa c ON c.main_account = t.id_coa
                LEFT JOIN gw_plan__master_cost_center cc ON cc.cost_center = t.id_dept
                WHERE t.year_code = ? AND c.type = ?
                ORDER BY t.id_coa, t.id_dept";

        try {
            return $this->db->query($sql, [$year, $type])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_pl_section_detail: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Detail SALES: agregasi per channel (domestic) + export.
     */
    private function getSalesSectionDetail(string $year): array
    {
        $selects = [];
        foreach (self::MONTH_NAMES as $i => $name) {
            $selects[] = "IFNULL(SUM({$name}_rev),0) AS m" . ($i + 1);
        }
        $selectSql = implode(', ', $selects);
        $totalExpr = implode(' + ', array_map(fn ($name) => "IFNULL(SUM({$name}_rev),0)", self::MONTH_NAMES));

        $sqlD = "SELECT id_channel AS account, 'Domestic' AS origin, {$selectSql}, ({$totalExpr}) AS total
                 FROM yp_plan__trans_sales_domestic WHERE year_code = ? GROUP BY id_channel ORDER BY id_channel";
        $sqlE = "SELECT id_channel AS account, 'Export' AS origin, {$selectSql}, ({$totalExpr}) AS total
                 FROM yp_plan__trans_sales_export WHERE year_code = ? GROUP BY id_channel ORDER BY id_channel";

        try {
            $dom = $this->db->query($sqlD, [$year])->getResultArray();
            $exp = $this->db->query($sqlE, [$year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::getSalesSectionDetail: ' . $e->getMessage());

            return [];
        }

        $items = [];
        foreach (array_merge($dom, $exp) as $row) {
            $items[] = [
                'account'      => $row['account'] ?? '',
                'account_desc' => $row['origin'] ?? '',
                'cost_center'  => $row['account'] ?? '',
                'm1'           => (float) ($row['m1'] ?? 0),
                'm2'           => (float) ($row['m2'] ?? 0),
                'm3'           => (float) ($row['m3'] ?? 0),
                'm4'           => (float) ($row['m4'] ?? 0),
                'm5'           => (float) ($row['m5'] ?? 0),
                'm6'           => (float) ($row['m6'] ?? 0),
                'm7'           => (float) ($row['m7'] ?? 0),
                'm8'           => (float) ($row['m8'] ?? 0),
                'm9'           => (float) ($row['m9'] ?? 0),
                'm10'          => (float) ($row['m10'] ?? 0),
                'm11'          => (float) ($row['m11'] ?? 0),
                'm12'          => (float) ($row['m12'] ?? 0),
                'total'        => (float) ($row['total'] ?? 0),
            ];
        }

        return $items;
    }

    /**
     * Rincian item CAPEX (header + detail bulanan + mapping SAP) untuk tab monitoring.
     * Return: cost_center_desc, item_desc, main_account(SAP), cost_center(SAP CC),
     *         unit, unit_price, remarks, JAN..DEC, total.
     */
    public function get_capex_monitoring(string $year): array
    {
        $monthSelect = [];
        foreach (range(1, 12) as $m) {
            $name = strtoupper(date('M', mktime(0, 0, 0, $m, 1)));
            $monthSelect[] = "IFNULL(d.`{$m}`,0) AS `{$name}`";
        }
        $monthSelectSql = implode(', ', $monthSelect);

        $sql = "SELECT COALESCE(cc.cost_desc, '') AS cost_center_desc,
                       h.item_desc,
                       COALESCE(NULLIF(c.id_acct_ext,''), h.main_account, 0) AS main_account,
                       COALESCE(NULLIF(cc.cost_center_sap,''), CAST(h.dept_id AS CHAR)) AS cost_center,
                       h.unit, h.unit_price, h.remarks,
                       {$monthSelectSql},
                       IFNULL(d.total,0) AS total
                FROM yp_plan__trans_capex_entry_header h
                LEFT JOIN yp_plan__trans_capex_entry_detail d ON d.id_header = h.id
                LEFT JOIN gw_plan__master_coa c ON c.main_account = h.main_account
                LEFT JOIN gw_plan__master_cost_center cc ON cc.cost_center = h.dept_id
                WHERE h.year_code = ?
                ORDER BY h.id DESC";

        try {
            return $this->db->query($sql, [$year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_capex_monitoring: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Peta catatan per section: [CODE => notes].
     */
    public function get_pl_notes(string $year): array
    {
        try {
            $rows = $this->db->table('yp_plan__trans_notes_pl')
                ->where('year_code', (int) $year)
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_pl_notes: ' . $e->getMessage());

            return [];
        }

        $map = [];
        foreach ($rows as $r) {
            $map[strtoupper((string) ($r['code_pl'] ?? ''))] = (string) ($r['notes'] ?? '');
        }

        return $map;
    }

    /**
     * Simpan / update catatan section P/L (yp_plan__trans_notes_pl).
     */
    public function save_pl_note(string $year, string $code, string $notes, int $userId): bool
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return false;
        }

        try {
            $exists = $this->db->table('yp_plan__trans_notes_pl')
                ->where('code_pl', $code)
                ->where('year_code', (int) $year)
                ->countAllResults();

            $data = [
                'notes'        => $notes,
                'created_by'   => $userId,
                'created_date' => date('Y-m-d H:i:s'),
            ];

            if ($exists > 0) {
                return $this->db->table('yp_plan__trans_notes_pl')
                    ->where('code_pl', $code)
                    ->where('year_code', (int) $year)
                    ->update($data);
            }

            $data['code_pl']   = $code;
            $data['year_code'] = (int) $year;

            return $this->db->table('yp_plan__trans_notes_pl')->insert($data);
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::save_pl_note: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Peta adjustment manual per section: [CODE => nilai].
     */
    public function get_pl_adjs(string $year): array
    {
        try {
            $rows = $this->db->table('yp_plan__trans_opex_adjs')
                ->where('year_code', (int) $year)
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::get_pl_adjs: ' . $e->getMessage());

            return [];
        }

        $map = [];
        foreach ($rows as $r) {
            $map[strtoupper((string) ($r['code_adjs'] ?? ''))] = (float) ($r['values_adjs'] ?? 0);
        }

        return $map;
    }

    /**
     * Simpan / update adjustment manual section P/L (yp_plan__trans_opex_adjs).
     */
    public function save_pl_adjs(string $year, string $code, float $value, int $userId): bool
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return false;
        }

        try {
            $exists = $this->db->table('yp_plan__trans_opex_adjs')
                ->where('code_adjs', $code)
                ->where('year_code', (int) $year)
                ->countAllResults();

            $data = [
                'values_adjs'  => $value,
                'created_by'   => $userId,
                'created_date' => date('Y-m-d H:i:s'),
            ];

            if ($exists > 0) {
                return $this->db->table('yp_plan__trans_opex_adjs')
                    ->where('code_adjs', $code)
                    ->where('year_code', (int) $year)
                    ->update($data);
            }

            $data['code_adjs']  = $code;
            $data['year_code']  = (int) $year;

            return $this->db->table('yp_plan__trans_opex_adjs')->insert($data);
        } catch (\Throwable $e) {
            log_message('error', 'ModelPl::save_pl_adjs: ' . $e->getMessage());

            return false;
        }
    }
}
