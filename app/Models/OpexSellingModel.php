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

    /**
     * Cost center OPEX Selling yang valid pada periode aktif.
     *
     * Nilai yang dikirim ke client adalah cost_center_sap agar sama dengan
     * identifier yang dipakai sistem lama. Bila tidak ada periode aktif,
     * gunakan seluruh cost center aktif bertipe OPEX sebagai fallback.
     */
    public function getCostCentersForEntry(): array
    {
        try {
            $today = date('Y-m-d');
            $hasActivePeriod = $this->db->table('yp_plan__master_period')
                ->where('tipe', 'OPEX_SELLING')
                ->where('status', 'A')
                ->where('begda <=', $today)
                ->where('endda >=', $today)
                ->countAllResults() > 0;

            if ($hasActivePeriod) {
                return $this->db->query(
                    "SELECT DISTINCT cc.cost_center, cc.cost_center_sap, cc.cost_desc
                     FROM gw_plan__master_cost_center cc
                     INNER JOIN yp_plan__master_period p
                       ON p.tipe = 'OPEX_SELLING'
                      AND p.status = 'A'
                      AND ? BETWEEN p.begda AND p.endda
                      AND (p.id_cost_center = '*'
                           OR p.id_cost_center = cc.cost_center
                           OR p.id_cost_center = cc.cost_center_sap)
                     WHERE cc.status = 'A'
                       AND cc.type = 'OPEX'
                     ORDER BY cc.cost_center ASC",
                    [$today]
                )->getResultArray();
            }

            return $this->db->table('gw_plan__master_cost_center')
                ->select('cost_center, cost_center_sap, cost_desc')
                ->where('status', 'A')
                ->where('type', 'OPEX')
                ->orderBy('cost_center', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'OpexSellingModel::getCostCentersForEntry: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Resolve cost_center_sap atau cost_center internal ke ID cost center DB.
     * Dropdown Entry memakai kode SAP, sedangkan tabel transaksi menyimpan
     * cost_center internal.
     */
    /**
     * Cost centers visible to the report, constrained by the user's object
     * scope. Admin callers pass $isAdmin=true and bypass the object list.
     */
    public function getReportCostCenters(?string $year = null, ?array $authObj = null, bool $isAdmin = false): array
    {
        $allowed = $isAdmin ? ['*'] : $this->authorizedReportValues((array) ($authObj ?? session()->get('auth_obj')));
        if ($allowed === []) {
            return [];
        }

        return array_values(array_filter($this->getCostCentersForEntry(), function (array $row) use ($allowed): bool {
            if (in_array('*', $allowed, true) || in_array('ALL', array_map('strtoupper', $allowed), true)) {
                return true;
            }
            $internal = trim((string) ($row['cost_center'] ?? ''));
            $sap = trim((string) ($row['cost_center_sap'] ?? ''));
            return in_array($internal, $allowed, true) || ($sap !== '' && in_array($sap, $allowed, true));
        }));
    }

    /** Resolve an internal/SAP report filter only when it is in scope. */
    public function resolveReportCostCenter(?string $dept, ?string $year = null, ?array $authObj = null, bool $isAdmin = false): ?string
    {
        $dept = trim((string) ($dept ?? ''));
        if ($dept === '' || $dept === '0') {
            return '0';
        }

        $resolved = $this->resolveCostCenter($dept);
        foreach ($this->getReportCostCenters($year, $authObj, $isAdmin) as $row) {
            if ((string) ($row['cost_center'] ?? '') === $resolved
                || (string) ($row['cost_center_sap'] ?? '') === $dept) {
                return (string) ($row['cost_center'] ?? $resolved);
            }
        }

        return null;
    }

    /** Return only report rows within the caller's cost-center scope. */
    public function getReportRows(string $year, ?string $dept = null, ?array $authObj = null, bool $isAdmin = false): array
    {
        $resolved = $this->resolveReportCostCenter($dept, $year, $authObj, $isAdmin);
        if ($resolved === null) {
            return [];
        }

        if ($resolved !== '0') {
            return $this->getViewDataFlat($year, $resolved);
        }

        $rows = [];
        foreach ($this->getReportCostCenters($year, $authObj, $isAdmin) as $row) {
            $internal = trim((string) ($row['cost_center'] ?? ''));
            if ($internal !== '') {
                $rows = array_merge($rows, $this->getViewDataFlat($year, $internal));
            }
        }

        return $rows;
    }

    /** @return string[] */
    private function authorizedReportValues(array $authObj): array
    {
        $values = [];
        foreach ($authObj as $object) {
            $raw = is_array($object) ? ($object['role_object_value'] ?? '') : $object;
            if (! is_scalar($raw)) {
                continue;
            }
            $raw = trim((string) $raw);
            if ($raw === '') {
                continue;
            }
            if (preg_match_all("/'([^']+)'/", $raw, $matches)) {
                $parts = $matches[1];
            } else {
                $parts = preg_split('/\\s*,\\s*/', trim($raw, "'\\\" ")) ?: [];
            }
            foreach ($parts as $part) {
                $part = trim((string) $part, "'\\\" ");
                if ($part !== '') {
                    $values[] = $part;
                }
            }
        }

        return array_values(array_unique($values));
    }

    public function resolveCostCenter(string $dept): string
    {
        $dept = trim($dept);
        if ($dept === '') {
            return '';
        }

        try {
            $row = $this->db->table('gw_plan__master_cost_center')
                ->select('cost_center')
                ->where('status', 'A')
                ->where('cost_center', $dept)
                ->get()
                ->getRowArray();

            if (! empty($row['cost_center'])) {
                return (string) $row['cost_center'];
            }

            $row = $this->db->table('gw_plan__master_cost_center')
                ->select('cost_center')
                ->where('status', 'A')
                ->where('cost_center_sap', $dept)
                ->get()
                ->getRowArray();

            return ! empty($row['cost_center']) ? (string) $row['cost_center'] : $dept;
        } catch (\Throwable $e) {
            log_message('error', 'OpexSellingModel::resolveCostCenter: ' . $e->getMessage());

            return $dept;
        }
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
     * Daftar header account OPEX Selling (Master COA tipe SELLING) beserta total
     * actual per bulan untuk 8 bulan pertama (Jan-Agustus).
     *
     * Sumber kolom bulan adalah yp_plan__trans_budget_actual (realisasi),
     * bukan budget entry — sesuai standar tampilan Entry Budget (kolom ACTUAL)
     * yang sama dengan modul GA/FOH.
     */
    public function getHeaderAccounts(string $year, ?string $dept = null, string $source = self::SOURCE): array
    {
        if (empty($dept)) {
            return [];
        }

        $sql = "SELECT c.main_account,
                       COALESCE(NULLIF(c.id_acct_ext,''), CAST(c.main_account AS CHAR)) AS acct_code,
                       c.cost_center_desc AS coa_name,
                       IFNULL(a.`1`,0) AS jan, IFNULL(a.`2`,0) AS feb,
                       IFNULL(a.`3`,0) AS mar, IFNULL(a.`4`,0) AS apr,
                       IFNULL(a.`5`,0) AS may, IFNULL(a.`6`,0) AS jun,
                       IFNULL(a.`7`,0) AS jul, IFNULL(a.`8`,0) AS aug,
                       (IFNULL(a.`1`,0)+IFNULL(a.`2`,0)+IFNULL(a.`3`,0)+IFNULL(a.`4`,0)+
                        IFNULL(a.`5`,0)+IFNULL(a.`6`,0)+IFNULL(a.`7`,0)+IFNULL(a.`8`,0)) AS total_actual
                FROM gw_plan__master_coa c
                LEFT JOIN yp_plan__trans_budget_actual a
                  ON a.id_coa = c.main_account AND a.id_dept = ? AND a.year_code = ?
                WHERE c.status = 'A'
                  AND (c.type = 'SELLING' OR c.category = 'SELLEXP')
                GROUP BY c.main_account, c.id_acct_ext, c.cost_center_desc
                ORDER BY c.main_account ASC";

        try {
            return $this->db->query($sql, [(int) $dept, $year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'OpexSellingModel::getHeaderAccounts: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Data actual OPEX Selling dikelompokkan per cost center header.
     *
     * Dept input adalah cost_center_sap. Query juga menerima cost_center
     * internal melalui subquery resolusi agar hasil identik dengan sistem lama.
     */
    public function getEntryDataGrouped(string $year, string $deptInput): array
    {
        if ($deptInput === '') {
            return [];
        }

        $sql = "SELECT
                    cost_center_header,
                    id_cost_header,
                    SUM(jan) AS jan,
                    SUM(feb) AS feb,
                    SUM(mar) AS mar,
                    SUM(apr) AS apr,
                    SUM(may) AS may,
                    SUM(jun) AS jun,
                    SUM(jul) AS jul,
                    SUM(aug) AS aug,
                    SUM(jan + feb + mar + apr + may + jun + jul + aug) AS total,
                    MAX(indicator) AS indicator
                FROM (
                    SELECT
                        a.id_acct_ext,
                        a.id_cost_header,
                        a.cost_center_header,
                        a.cost_center_desc,
                        IFNULL(bb.`1`, 0) AS jan,
                        IFNULL(bb.`2`, 0) AS feb,
                        IFNULL(bb.`3`, 0) AS mar,
                        IFNULL(bb.`4`, 0) AS apr,
                        IFNULL(bb.`5`, 0) AS may,
                        IFNULL(bb.`6`, 0) AS jun,
                        IFNULL(bb.`7`, 0) AS jul,
                        IFNULL(bb.`8`, 0) AS aug,
                        CASE
                            WHEN c.id_coa IS NULL THEN 'belum'
                            ELSE 'sudah'
                        END AS indicator
                    FROM gw_plan__master_coa a
                    LEFT JOIN yp_plan__trans_budget_actual b
                        ON a.main_account = b.id_coa
                    LEFT JOIN yp_plan__trans_budget_actual bb
                        ON a.main_account = bb.id_coa
                        AND bb.year_code = ?
                        AND (bb.id_dept = ? OR bb.id_dept IN (
                            SELECT cost_center
                            FROM gw_plan__master_cost_center
                            WHERE cost_center_sap = ?
                        ))
                    LEFT JOIN yp_plan__trans_budget_entry_data c
                        ON b.id_coa = c.id_coa
                        AND (c.id_dept = ? OR c.id_dept IN (
                            SELECT cost_center
                            FROM gw_plan__master_cost_center
                            WHERE cost_center_sap = ?
                        ))
                        AND c.year_code = ?
                    WHERE a.type = 'SELLING'
                    GROUP BY a.id_acct_ext
                ) aa
                GROUP BY cost_center_header
                ORDER BY id_acct_ext ASC";

        try {
            return $this->db->query($sql, [
                $year,
                $deptInput,
                $deptInput,
                $deptInput,
                $deptInput,
                $year,
            ])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'OpexSellingModel::getEntryDataGrouped: ' . $e->getMessage());

            return [];
        }
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

    /* ------------------------------------------------------------------
     * Actual
     * ------------------------------------------------------------------ */

    /**
     * Actual Selling untuk tab Actual Data.
     *
     * Method ini sengaja terpisah dari query actual lama agar Entry Budget dan
     * View Data tidak berubah. Endpoint Actual Selling membutuhkan seluruh
     * baris COA SELLING, delapan bulan pertama, serta agregat AVG/TOTAL.
     */
    public function getActualSellingRows(string $year, string $dept): array
    {
        $sql = "SELECT
                    b.id,
                    a.main_account,
                    COALESCE(a.cost_center_header, '') AS cost_center_header,
                    COALESCE(a.cost_center_desc, '') AS cost_center_desc,
                    IFNULL(b.`1`, 0) AS jan,
                    IFNULL(b.`2`, 0) AS feb,
                    IFNULL(b.`3`, 0) AS mar,
                    IFNULL(b.`4`, 0) AS apr,
                    IFNULL(b.`5`, 0) AS may,
                    IFNULL(b.`6`, 0) AS jun,
                    IFNULL(b.`7`, 0) AS jul,
                    IFNULL(b.`8`, 0) AS aug,
                    (
                        IFNULL(b.`1`, 0) + IFNULL(b.`2`, 0) +
                        IFNULL(b.`3`, 0) + IFNULL(b.`4`, 0) +
                        IFNULL(b.`5`, 0) + IFNULL(b.`6`, 0) +
                        IFNULL(b.`7`, 0) + IFNULL(b.`8`, 0)
                    ) / 8 AS avg,
                    (
                        IFNULL(b.`1`, 0) + IFNULL(b.`2`, 0) +
                        IFNULL(b.`3`, 0) + IFNULL(b.`4`, 0) +
                        IFNULL(b.`5`, 0) + IFNULL(b.`6`, 0) +
                        IFNULL(b.`7`, 0) + IFNULL(b.`8`, 0)
                    ) AS total
                FROM gw_plan__master_coa a
                INNER JOIN yp_plan__trans_budget_actual b
                    ON b.id_coa = a.main_account
                   AND b.year_code = ?
                   AND b.id_dept = ?
                WHERE a.type = 'SELLING'
                ORDER BY a.main_account ASC";

        try {
            $rows = $this->db->query($sql, [$year, $dept])->getResultArray();

            return array_map(static function (array $row): array {
                foreach (['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'avg', 'total'] as $key) {
                    $row[$key] = (float) ($row[$key] ?? 0);
                }
                $row['id'] = (int) ($row['id'] ?? 0);
                $row['main_account'] = (string) ($row['main_account'] ?? '');
                $row['cost_center_header'] = (string) ($row['cost_center_header'] ?? '');
                $row['cost_center_desc'] = (string) ($row['cost_center_desc'] ?? '');

                return $row;
            }, $rows);
        } catch (\Throwable $e) {
            log_message('error', 'OpexSellingModel::getActualSellingRows: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Master COA Selling untuk template Actual. Data actual tidak menjadi
     * syarat keberadaan baris template.
     */
    public function getActualSellingTemplateRows(string $dept): array
    {
        try {
            return $this->db->table('gw_plan__master_coa')
                ->select('main_account, cost_center_desc')
                ->where('type', 'SELLING')
                ->orderBy('main_account', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'OpexSellingModel::getActualSellingTemplateRows: ' . $e->getMessage());

            return [];
        }
    }

    public function getActualDataPaginated(string $year, ?string $dept = null, int $offset = 0, int $perPage = 25, string $search = ''): array
    {
        $builder = $this->db->table('yp_plan__trans_budget_actual a')
            ->select('a.id, a.id_coa, a.id_dept')
            ->select("COALESCE(NULLIF(c.id_acct_ext,''), CAST(a.id_coa AS CHAR)) AS acct_code")
            ->select("COALESCE(c.cost_center_desc, '') AS description")
            ->select('(IFNULL(a.`1`,0)+IFNULL(a.`2`,0)+IFNULL(a.`3`,0)+IFNULL(a.`4`,0)+IFNULL(a.`5`,0)+IFNULL(a.`6`,0)+IFNULL(a.`7`,0)+IFNULL(a.`8`,0)+IFNULL(a.`9`,0)+IFNULL(a.`10`,0)+IFNULL(a.`11`,0)+IFNULL(a.`12`,0)) AS total')
            ->select("IFNULL(a.`1`,0) AS jan, IFNULL(a.`2`,0) AS feb, IFNULL(a.`3`,0) AS mar, IFNULL(a.`4`,0) AS apr, IFNULL(a.`5`,0) AS may, IFNULL(a.`6`,0) AS jun")
            ->select("IFNULL(a.`7`,0) AS jul, IFNULL(a.`8`,0) AS aug, IFNULL(a.`9`,0) AS sep, IFNULL(a.`10`,0) AS oct, IFNULL(a.`11`,0) AS nov, IFNULL(a.`12`,0) AS `dec`")
            ->join('gw_plan__master_coa c', 'c.main_account = a.id_coa', 'left')
            ->where('a.year_code', $year);

        if ($dept !== '') {
            $builder->where('a.id_dept', $dept);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('a.id_coa', $search)
                ->orLike('c.id_acct_ext', $search)
                ->orLike('c.cost_center_desc', $search)
            ->groupEnd();
        }

        return $builder->orderBy('a.id_coa', 'ASC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Jumlah total data actual sesuai filter (untuk server-side pagination).
     */
    public function countActualData(string $year, ?string $dept = null, string $search = ''): int
    {
        $builder = $this->db->table('yp_plan__trans_budget_actual a')
            ->join('gw_plan__master_coa c', 'c.main_account = a.id_coa', 'left')
            ->where('a.year_code', $year);

        if ($dept !== '') {
            $builder->where('a.id_dept', $dept);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('a.id_coa', $search)
                ->orLike('c.id_acct_ext', $search)
                ->orLike('c.cost_center_desc', $search)
            ->groupEnd();
        }

        return (int) $builder->countAllResults();
    }

    /**
     * Matrix data per Sub-Account untuk sebuah Header Account OPEX Selling:
     * budget (12 bulan) + actual (realisasi) + simulated (breakdown detail).
     *
     * Header dikirim sebagai main_account (kode COA) — konsisten dengan modul GA/FOH.
     */
    public function getDetailMatrix(string $year, ?string $dept, string $header, string $source = self::SOURCE, ?string $headerId = null): array
    {
        $dept       = $this->resolveCostCenter((string) $dept);
        $headerAcct = (int) $header;

        // Grouped Entry mengirim cost_center_header + id_cost_header,
        // bukan main_account numerik seperti endpoint legacy.
        $subAccounts = [];
        if (! is_numeric($header)) {
            $groupQuery = $this->db->table('gw_plan__master_coa c')
                ->select("c.main_account,
                          COALESCE(NULLIF(c.id_acct_ext,''), CAST(c.main_account AS CHAR)) AS acct_code,
                          c.cost_center_desc AS coa_name")
                ->where('c.status', 'A')
                ->where('c.type', 'SELLING')
                ->where('c.cost_center_header', $header);

            if ($headerId !== null && $headerId !== '') {
                $groupQuery->where('c.id_cost_header', (int) $headerId);
            }

            $subAccounts = $groupQuery
                ->orderBy('c.main_account', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Legacy: sub-accounts di bawah main_account numerik.
        if (empty($subAccounts) && \App\Libraries\DbCompat::hasColumn('gw_plan__master_coa', 'main_category')) {
            $subAccounts = $this->db->table('gw_plan__master_coa c')
                ->select("c.main_account,
                          COALESCE(NULLIF(c.id_acct_ext,''), CAST(c.main_account AS CHAR)) AS acct_code,
                          c.cost_center_desc AS coa_name")
                ->where('c.status', 'A')
                ->where('c.main_category', $headerAcct)
                ->orderBy('c.main_account', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Jika tidak ada sub-account, coba treat header sebagai leaf
        if (empty($subAccounts)) {
            $subAccounts = [$this->db->table('gw_plan__master_coa')
                ->select("main_account,
                          COALESCE(NULLIF(id_acct_ext,''), CAST(main_account AS CHAR)) AS acct_code,
                          cost_center_desc AS coa_name")
                ->where('main_account', $headerAcct)
                ->get()
                ->getRowArray()];
            $subAccounts = array_filter($subAccounts);
        }

        $result = [];
        foreach ($subAccounts as $sub) {
            $coaId = (int) $sub['main_account'];

            // Budget data
            $budget = $this->db->table('yp_plan__trans_budget_entry_data t')
                ->select("t.id AS entry_data_id,
                         IFNULL(t.`1`,0) AS b1, IFNULL(t.`2`,0) AS b2, IFNULL(t.`3`,0) AS b3,
                         IFNULL(t.`4`,0) AS b4, IFNULL(t.`5`,0) AS b5, IFNULL(t.`6`,0) AS b6,
                         IFNULL(t.`7`,0) AS b7, IFNULL(t.`8`,0) AS b8, IFNULL(t.`9`,0) AS b9,
                         IFNULL(t.`10`,0) AS b10, IFNULL(t.`11`,0) AS b11, IFNULL(t.`12`,0) AS b12,
                         IFNULL(t.total,0) AS btotal")
                ->where('t.id_coa', $coaId)
                ->where('t.id_dept', $dept)
                ->where('t.year_code', $year);

            if (\App\Libraries\DbCompat::hasEntrySource()) {
                $budget->groupStart()
                    ->where('t.source', $source)
                    ->orWhere('t.source IS NULL')
                ->groupEnd();
            }
            $budgetRow = $budget->get()->getRowArray();

            // Actual data
            $actual = $this->db->table('yp_plan__trans_budget_actual a')
                ->select("IFNULL(a.`1`,0) AS a1, IFNULL(a.`2`,0) AS a2, IFNULL(a.`3`,0) AS a3,
                         IFNULL(a.`4`,0) AS a4, IFNULL(a.`5`,0) AS a5, IFNULL(a.`6`,0) AS a6,
                         IFNULL(a.`7`,0) AS a7, IFNULL(a.`8`,0) AS a8, IFNULL(a.`9`,0) AS a9,
                         IFNULL(a.`10`,0) AS a10, IFNULL(a.`11`,0) AS a11, IFNULL(a.`12`,0) AS a12,
                         (IFNULL(a.`1`,0)+IFNULL(a.`2`,0)+IFNULL(a.`3`,0)+IFNULL(a.`4`,0)+
                          IFNULL(a.`5`,0)+IFNULL(a.`6`,0)+IFNULL(a.`7`,0)+IFNULL(a.`8`,0)+
                          IFNULL(a.`9`,0)+IFNULL(a.`10`,0)+IFNULL(a.`11`,0)+IFNULL(a.`12`,0)) AS atotal,
                         a.assumption, a.notes")
                ->where('a.id_coa', $coaId)
                ->where('a.id_dept', $dept)
                ->where('a.year_code', $year)
                ->get()
                ->getRowArray();

            // Simulated actual dari breakdown entry detail
            $simulated   = null;
            $entryDataId = (int) ($budgetRow['entry_data_id'] ?? 0);
            if ($entryDataId > 0) {
                $simulated = $this->db->table('yp_plan__trans_budget_entry_detail')
                    ->select("IFNULL(SUM(jan),0) AS a1, IFNULL(SUM(feb),0) AS a2, IFNULL(SUM(mar),0) AS a3,
                             IFNULL(SUM(apr),0) AS a4, IFNULL(SUM(may),0) AS a5, IFNULL(SUM(jun),0) AS a6,
                             IFNULL(SUM(jul),0) AS a7, IFNULL(SUM(aug),0) AS a8, IFNULL(SUM(sep),0) AS a9,
                             IFNULL(SUM(oct),0) AS a10, IFNULL(SUM(nov),0) AS a11, IFNULL(SUM(`dec`),0) AS a12,
                             (IFNULL(SUM(jan),0)+IFNULL(SUM(feb),0)+IFNULL(SUM(mar),0)+IFNULL(SUM(apr),0)+
                              IFNULL(SUM(may),0)+IFNULL(SUM(jun),0)+IFNULL(SUM(jul),0)+IFNULL(SUM(aug),0)+
                              IFNULL(SUM(sep),0)+IFNULL(SUM(oct),0)+IFNULL(SUM(nov),0)+IFNULL(SUM(`dec`),0)) AS atotal")
                    ->where('entry_data_id', $entryDataId)
                    ->get()
                    ->getRowArray();
            }

            $result[] = array_merge($sub, [
                // ID entry budget (parent) — dipakai modal detail untuk simpan breakdown.
                'entry_data_id' => $entryDataId,
                'budget'        => $budgetRow ?: array_fill_keys(['b1','b2','b3','b4','b5','b6','b7','b8','b9','b10','b11','b12','btotal'], 0),
                'actual'        => $actual ?: array_fill_keys(['a1','a2','a3','a4','a5','a6','a7','a8','a9','a10','a11','a12','atotal'], 0),
                'simulated'     => $simulated ?: array_fill_keys(['a1','a2','a3','a4','a5','a6','a7','a8','a9','a10','a11','a12','atotal'], 0),
            ]);
        }

        return $result;
    }

    /**
     * Flat View Data OPEX Selling yang kompatibel dengan endpoint legacy
     * cari_actual_table.
     *
     * Tidak memakai filter source pada budget entry. Mode dept 0/empty
     * mengagregasi semua cost center berdasarkan main_account.
     */
    public function getViewDataFlat(string $year, string $dept): array
    {
        $isAll = $dept === '' || $dept === '0';

        $actualSelect = "
            IFNULL(b.`1`, 0) AS JAN,
            IFNULL(b.`2`, 0) AS FEB,
            IFNULL(b.`3`, 0) AS MAR,
            IFNULL(b.`4`, 0) AS APR,
            IFNULL(b.`5`, 0) AS MAY,
            IFNULL(b.`6`, 0) AS JUN,
            IFNULL(b.`7`, 0) AS JUL,
            IFNULL(b.`8`, 0) AS AUG,
            IFNULL((IFNULL(b.`1`,0) + IFNULL(b.`2`,0) + IFNULL(b.`3`,0) + IFNULL(b.`4`,0) +
                    IFNULL(b.`5`,0) + IFNULL(b.`6`,0) + IFNULL(b.`7`,0) + IFNULL(b.`8`,0)) / 8, 0) AS AVG,
            IFNULL((IFNULL(b.`1`,0) + IFNULL(b.`2`,0) + IFNULL(b.`3`,0) + IFNULL(b.`4`,0) +
                    IFNULL(b.`5`,0) + IFNULL(b.`6`,0) + IFNULL(b.`7`,0) + IFNULL(b.`8`,0)), 0) AS TOT";

        $budgetSelect = "
            IFNULL(c.`1`, 0) AS isi_1,
            IFNULL(c.`2`, 0) AS isi_2,
            IFNULL(c.`3`, 0) AS isi_3,
            IFNULL(c.`4`, 0) AS isi_4,
            IFNULL(c.`5`, 0) AS isi_5,
            IFNULL(c.`6`, 0) AS isi_6,
            IFNULL(c.`7`, 0) AS isi_7,
            IFNULL(c.`8`, 0) AS isi_8,
            IFNULL(c.`9`, 0) AS isi_9,
            IFNULL(c.`10`, 0) AS isi_10,
            IFNULL(c.`11`, 0) AS isi_11,
            IFNULL(c.`12`, 0) AS isi_12,
            IFNULL(c.total, 0) AS isi_tot";

        $assumptionSelect = "
            CASE
                WHEN a.main_account = '7700011' THEN CONCAT(ass_b.amount, '%')
                WHEN a.main_account = '7700024' THEN '1x Gaji'
                WHEN a.main_account = '7700012' THEN ass_c.amount
                WHEN a.main_account = '7700014' THEN CONCAT(ass_d.amount, '%')
                WHEN a.main_account = '7700022' THEN CONCAT(ass_e.amount, '%')
                WHEN a.main_account = '7700026' THEN CONCAT(ass_f.amount, '%')
                ELSE IFNULL(CONCAT(ass.value, '%'), 0)
            END AS assumption";

        if ($isAll) {
            $sql = "SELECT
                        MAX(notes_value) AS notes_value,
                        MAX(fx_notes) AS fx_notes,
                        MAX(main_account) AS main_account,
                        MAX(cost_center_header) AS cost_center_header,
                        MAX(cost_center_desc) AS cost_center_desc,
                        IFNULL(SUM(JAN), 0) AS JAN,
                        IFNULL(SUM(FEB), 0) AS FEB,
                        IFNULL(SUM(MAR), 0) AS MAR,
                        IFNULL(SUM(APR), 0) AS APR,
                        IFNULL(SUM(MAY), 0) AS MAY,
                        IFNULL(SUM(JUN), 0) AS JUN,
                        IFNULL(SUM(JUL), 0) AS JUL,
                        IFNULL(SUM(AUG), 0) AS AUG,
                        IFNULL(SUM(AVG), 0) AS AVG,
                        IFNULL(SUM(TOT), 0) AS TOT,
                        MAX(assumption) AS assumption,
                        MAX(notes) AS notes,
                        IFNULL(SUM(isi_1), 0) AS isi_1,
                        IFNULL(SUM(isi_2), 0) AS isi_2,
                        IFNULL(SUM(isi_3), 0) AS isi_3,
                        IFNULL(SUM(isi_4), 0) AS isi_4,
                        IFNULL(SUM(isi_5), 0) AS isi_5,
                        IFNULL(SUM(isi_6), 0) AS isi_6,
                        IFNULL(SUM(isi_7), 0) AS isi_7,
                        IFNULL(SUM(isi_8), 0) AS isi_8,
                        IFNULL(SUM(isi_9), 0) AS isi_9,
                        IFNULL(SUM(isi_10), 0) AS isi_10,
                        IFNULL(SUM(isi_11), 0) AS isi_11,
                        IFNULL(SUM(isi_12), 0) AS isi_12,
                        IFNULL(SUM(isi_tot), 0) AS isi_tot
                    FROM (
                        SELECT
                            c.notes_value,
                            c.fx_notes,
                            a.main_account,
                            a.cost_center_header,
                            a.cost_center_desc,
                            {$actualSelect},
                            {$assumptionSelect},
                            IFNULL(b.notes, '') AS notes,
                            {$budgetSelect}
                        FROM gw_plan__master_coa a
                        LEFT JOIN yp_plan__trans_budget_actual b
                            ON a.main_account = b.id_coa
                           AND b.year_code = ?
                        LEFT JOIN yp_plan__trans_budget_entry_data c
                            ON a.main_account = c.id_coa
                           AND c.year_code = ?
                           AND b.id_dept = c.id_dept
                        LEFT JOIN yp_plan__master_assumption ass
                            ON ass.type_id = 3
                           AND ass.year = ?
                           AND a.cost_center_header NOT IN ('Salaries', 'Employee Fringe Benefit', 'Depreciation Amortization')
                        LEFT JOIN yp_plan__trans_opex ass_b
                            ON ass_b.type_opex = 1 AND ass_b.year_code = ?
                        LEFT JOIN yp_plan__trans_opex ass_c
                            ON ass_c.type_opex = 2 AND ass_c.year_code = ?
                        LEFT JOIN yp_plan__trans_opex ass_d
                            ON ass_d.type_opex = 5 AND ass_d.year_code = ?
                        LEFT JOIN yp_plan__trans_opex ass_e
                            ON ass_e.type_opex = 3 AND ass_e.year_code = ?
                        LEFT JOIN yp_plan__trans_opex ass_f
                            ON ass_f.type_opex = 6 AND ass_f.year_code = ?
                        WHERE a.type = 'SELLING'
                        GROUP BY a.main_account, b.id_dept, c.id_dept
                    ) aa
                    GROUP BY aa.main_account
                    ORDER BY aa.main_account ASC";

            $bindings = [$year, $year, $year, $year, $year, $year, $year, $year];
        } else {
            $sql = "SELECT
                        c.notes_value,
                        c.fx_notes,
                        a.main_account,
                        a.cost_center_header,
                        a.cost_center_desc,
                        {$actualSelect},
                        {$assumptionSelect},
                        IFNULL(b.notes, '') AS notes,
                        {$budgetSelect}
                    FROM gw_plan__master_coa a
                    LEFT JOIN yp_plan__trans_budget_actual b
                        ON a.main_account = b.id_coa
                       AND b.year_code = ?
                       AND b.id_dept = ?
                    LEFT JOIN yp_plan__trans_budget_entry_data c
                        ON a.main_account = c.id_coa
                       AND c.year_code = ?
                       AND c.id_dept = ?
                    LEFT JOIN yp_plan__master_assumption ass
                        ON ass.type_id = 3
                       AND ass.year = ?
                       AND a.cost_center_header NOT IN ('Salaries', 'Employee Fringe Benefit', 'Depreciation Amortization')
                    LEFT JOIN yp_plan__trans_opex ass_b
                        ON ass_b.type_opex = 1 AND ass_b.year_code = ?
                    LEFT JOIN yp_plan__trans_opex ass_c
                        ON ass_c.type_opex = 2 AND ass_c.year_code = ?
                    LEFT JOIN yp_plan__trans_opex ass_d
                        ON ass_d.type_opex = 5 AND ass_d.year_code = ?
                    LEFT JOIN yp_plan__trans_opex ass_e
                        ON ass_e.type_opex = 3 AND ass_e.year_code = ?
                    LEFT JOIN yp_plan__trans_opex ass_f
                        ON ass_f.type_opex = 6 AND ass_f.year_code = ?
                    WHERE a.type = 'SELLING'
                    GROUP BY a.main_account
                    ORDER BY a.main_account ASC";

            $bindings = [$year, $dept, $year, $dept, $year, $year, $year, $year, $year, $year];
        }

        try {
            return $this->db->query($sql, $bindings)->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'OpexSellingModel::getViewDataFlat: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Data View Data OPEX Selling: COA SELLING dikelompokkan per kategori
     * (cost_center_header), lengkap dengan actual (realisasi 12 bulan) dan
     * budget entry (12 bulan) per item, plus breakdown children per entry.
     *
     * Struktur output meniru shape yang dipakai modul FOH di tab View Data:
     *   [{ header_name, items: [{ acct_code, coa_name, entry_data_id,
     *       actual: {a1..a12, atotal}, budget: {b1..b12, btotal}, children[] }] }]
     *
     * @return array
     */
    public function getViewDataGrouped(string $year, ?string $dept): array
    {
        if (empty($dept)) {
            return [];
        }

        $coas = $this->db->table('gw_plan__master_coa')
            ->select('main_account, cost_center_desc, cost_center_header')
            ->groupStart()
                ->where('type', 'SELLING')
                ->orWhere('category', 'SELLEXP')
            ->groupEnd()
            ->where('status', 'A')
            ->orderBy('cost_center_header', 'ASC')
            ->orderBy('main_account', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($coas)) {
            return [];
        }

        $coaIds = array_map(static fn ($c) => (int) $c['main_account'], $coas);

        // Actual (realisasi) per COA — satu query untuk semua akun
        $actualByCoa = [];
        foreach ($this->db->table('yp_plan__trans_budget_actual')
            ->select('id_coa, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`')
            ->where('id_dept', $dept)
            ->where('year_code', $year)
            ->whereIn('id_coa', $coaIds)
            ->get()
            ->getResultArray() as $a) {
            $actualByCoa[(int) $a['id_coa']] = $a;
        }

        // Budget entry per COA — satu query untuk semua akun
        $budgetBuilder = $this->db->table('yp_plan__trans_budget_entry_data t')
            ->select('t.id, t.id_coa, t.total, t.`1`, t.`2`, t.`3`, t.`4`, t.`5`, t.`6`, t.`7`, t.`8`, t.`9`, t.`10`, t.`11`, t.`12`')
            ->where('t.id_dept', $dept)
            ->where('t.year_code', $year)
            ->whereIn('t.id_coa', $coaIds);

        if (\App\Libraries\DbCompat::hasEntrySource()) {
            $budgetBuilder->groupStart()
                ->where('t.source', self::SOURCE)
                ->orWhere('t.source IS NULL')
            ->groupEnd();
        }

        $budgetByCoa = [];
        foreach ($budgetBuilder->get()->getResultArray() as $b) {
            $budgetByCoa[(int) $b['id_coa']] = $b;
        }

        // Breakdown children per entry_data_id — satu query untuk semua entry
        $entryIds = array_values(array_filter(array_map(
            static fn ($b) => (int) ($b['id'] ?? 0),
            $budgetByCoa
        )));
        $childrenByEntry = [];
        if (! empty($entryIds)) {
            foreach ($this->db->table('yp_plan__trans_budget_entry_detail')
                ->whereIn('entry_data_id', $entryIds)
                ->orderBy('sort_order', 'ASC')
                ->get()
                ->getResultArray() as $c) {
                $childrenByEntry[(int) $c['entry_data_id']][] = $c;
            }
        }

        $monthKeys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];

        $groups = [];
        foreach ($coas as $coa) {
            $coaId = (int) $coa['main_account'];
            $cat   = trim((string) ($coa['cost_center_header'] ?? ''));
            $cat   = $cat !== '' ? $cat : 'Lainnya';

            $act      = $actualByCoa[$coaId] ?? [];
            $bud      = $budgetByCoa[$coaId] ?? [];
            $entryId  = (int) ($bud['id'] ?? 0);

            $actual = [];
            $atotal = 0.0;
            foreach ($monthKeys as $mk) {
                $v = (float) ($act[$mk] ?? 0);
                $actual['a' . (int) $mk] = $v;
                $atotal += $v;
            }
            $actual['atotal'] = $atotal;

            $budget = [];
            $btotal = 0.0;
            foreach ($monthKeys as $mk) {
                $v = (float) ($bud[$mk] ?? 0);
                $budget['b' . (int) $mk] = $v;
                $btotal += $v;
            }
            $budget['btotal'] = $btotal;

            $children = [];
            foreach ($childrenByEntry[$entryId] ?? [] as $c) {
                $children[] = [
                    'desc'  => (string) ($c['nama_barang'] ?? ''),
                    'jan'   => (float) ($c['jan'] ?? 0),
                    'feb'   => (float) ($c['feb'] ?? 0),
                    'mar'   => (float) ($c['mar'] ?? 0),
                    'apr'   => (float) ($c['apr'] ?? 0),
                    'may'   => (float) ($c['may'] ?? 0),
                    'jun'   => (float) ($c['jun'] ?? 0),
                    'jul'   => (float) ($c['jul'] ?? 0),
                    'aug'   => (float) ($c['aug'] ?? 0),
                    'sep'   => (float) ($c['sep'] ?? 0),
                    'oct'   => (float) ($c['oct'] ?? 0),
                    'nov'   => (float) ($c['nov'] ?? 0),
                    'dec'   => (float) ($c['dec'] ?? 0),
                    'total' => (float) ($c['total'] ?? 0),
                ];
            }

            $groups[$cat]['items'][] = [
                'acct_code'     => (string) $coaId,
                'coa_name'      => (string) ($coa['cost_center_desc'] ?? ''),
                'entry_data_id' => $entryId,
                'actual'        => $actual,
                'budget'        => $budget,
                'children'      => $children,
            ];
        }

        $result = [];
        foreach ($groups as $cat => $g) {
            $result[] = ['header_name' => $cat, 'items' => $g['items']];
        }

        return $result;
    }

    /**
     * Batch simpan detail breakdown items (dari modal detail).
     *
     * Bila parent entry budget belum ada (entry_data_id = 0 atau tidak
     * ditemukan), parent dibuat otomatis dari $parentHint
     * (id_coa / id_dept / year_code) sehingga modal detail tetap bisa
     * dipakai walau budget sub-account belum pernah disimpan.
     *
     * @param array  $items      [{ nama_barang, jan..dec, sort_order }]
     * @param int    $entryDataId ID dari yp_plan__trans_budget_entry_data (0 bila belum ada)
     * @param array  $parentHint [id_coa, id_dept, year_code] untuk auto-create parent
     */
    public function saveDetailItemsBatch(int $entryDataId, array $items, int $userId, array $parentHint = []): array
    {
        // Validasi kelengkapan hint lebih awal (sebelum transaksi) untuk
        // kasus entry_data_id = 0, agar tidak membuka transaksi kosong.
        $resolvedDept = (int) $this->resolveCostCenter((string) ($parentHint['id_dept'] ?? ''));

        if ($entryDataId <= 0) {
            $idCoa  = (int) ($parentHint['id_coa'] ?? 0);
            $idDept = $resolvedDept;
            $year   = (int) ($parentHint['year_code'] ?? 0);
            if ($idCoa <= 0 || $idDept <= 0 || $year <= 0) {
                return ['success' => false, 'message' => 'Entry budget tidak ditemukan dan data parent tidak lengkap.', 'count' => 0];
            }
        }

        $this->db->transStart();

        $parent = ($entryDataId > 0)
            ? $this->db->table('yp_plan__trans_budget_entry_data')->where('id', $entryDataId)->get()->getRowArray()
            : null;

        if (! $parent) {
            // Auto-create parent dari hint agar detail bisa disimpan tanpa
            // harus mengisi budget terlebih dahulu.
            $idCoa  = (int) ($parentHint['id_coa'] ?? 0);
            $idDept = $resolvedDept;
            $year   = (int) ($parentHint['year_code'] ?? 0);

            if ($idCoa <= 0 || $idDept <= 0 || $year <= 0) {
                $this->db->transComplete();

                return ['success' => false, 'message' => 'Entry budget tidak ditemukan dan data parent tidak lengkap.', 'count' => 0];
            }

            // Cegah duplikat bila parent ternyata sudah ada — kunci baris
            // (SELECT ... FOR UPDATE) agar aman dari race antar user.
            $existing = $this->db->query(
                'SELECT * FROM yp_plan__trans_budget_entry_data
                 WHERE id_coa = ? AND id_dept = ? AND year_code = ? LIMIT 1 FOR UPDATE',
                [$idCoa, $idDept, $year]
            )->getRowArray();

            if ($existing) {
                $parent      = $existing;
                $entryDataId = (int) $existing['id'];
            } else {
                $insertData = [
                    'id_coa'       => $idCoa,
                    'id_dept'      => $idDept,
                    'year_code'    => $year,
                    'total'        => 0,
                    'created_by'   => $userId,
                    'created_date' => date('Y-m-d H:i:s'),
                ];

                if (\App\Libraries\DbCompat::hasEntrySource()) {
                    $insertData['source']        = self::SOURCE;
                    $insertData['submit_status'] = 'DRAFT';
                }

                $this->db->table('yp_plan__trans_budget_entry_data')->insert($insertData);
                $entryDataId = (int) $this->db->insertID();
                $parent      = array_merge(['id' => $entryDataId], $insertData);
            }
        }

        // Hapus item lama (mode replace)
        $this->db->table('yp_plan__trans_budget_entry_detail')
            ->where('entry_data_id', $entryDataId)
            ->delete();

        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
        $saved  = 0;

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $namaBarang = trim((string) ($item['nama_barang'] ?? ''));
            if ($namaBarang === '') {
                continue;
            }

            $total  = 0;
            $fields = [];
            foreach ($months as $mk) {
                $val         = (float) ($item[$mk] ?? 0);
                $fields[$mk] = $val;
                $total      += $val;
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

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menyimpan detail item.', 'count' => 0];
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
