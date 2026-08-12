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
    use \App\Models\Traits\ActualImportTrait;

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
            ->select("cost_center, COALESCE(NULLIF(cost_center_sap,''), CAST(cost_center AS CHAR)) AS cc_code, cost_desc, cost_center_sap")
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
            ->select("main_account, COALESCE(NULLIF(id_acct_ext,''), CAST(main_account AS CHAR)) AS acct_code, cost_center_desc")
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
            ->select("COALESCE(NULLIF(c.id_acct_ext,''), c.main_account, 0) AS acct_code")
            ->select("COALESCE(c.cost_center_desc, '') AS coa_desc")
            ->select('IFNULL(t.total,0) AS total, ' . \App\Libraries\DbCompat::submitStatusExpr())
            ->select("IFNULL(t.`1`,0) AS jan, IFNULL(t.`2`,0) AS feb, IFNULL(t.`3`,0) AS mar, IFNULL(t.`4`,0) AS apr, IFNULL(t.`5`,0) AS may, IFNULL(t.`6`,0) AS jun")
            ->select("IFNULL(t.`7`,0) AS jul, IFNULL(t.`8`,0) AS aug, IFNULL(t.`9`,0) AS sep, IFNULL(t.`10`,0) AS oct, IFNULL(t.`11`,0) AS nov, IFNULL(t.`12`,0) AS `dec`")
            ->join('gw_plan__master_coa c', 'c.main_account = t.id_coa', 'left')
            ->where('t.year_code', $year);

        // Kolom source tidak selalu ada di skema legacy — filter hanya bila tersedia.
        if (\App\Libraries\DbCompat::hasEntrySource()) {
            $builder->where('t.source', 'FOH');
        }

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

        // Kolom source tidak ada di skema legacy → entry di-nonaktifkan sementara
        // (keputusan user 6 Agt 2026: jangan ubah struktur DB).
        if (! \App\Libraries\DbCompat::hasEntrySource()) {
            return ['success' => false, 'message' => 'Penyimpanan entry FOH dinonaktifkan sementara (kolom source belum tersedia di skema DB legacy).', 'count' => 0];
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
     * Workflow Submit — tandai budget FOH (dept & tahun) sebagai WAITING (W)
     * di tabel legacy yp_plan__trans_status_budget (W/A/R).
     * Bila kolom submit_status tersedia di skema, set juga SUBMITTED.
     */
    public function submitBudget(string $year, ?string $dept, int $userId): array
    {
        return $this->setBudgetStatus($year, $dept, $userId, 'W', 'Budget FOH disubmit untuk persetujuan.');
    }

    /**
     * Workflow Approve — tandai budget FOH sebagai APPROVED (A).
     */
    public function approveBudget(string $year, ?string $dept, int $userId): array
    {
        return $this->setBudgetStatus($year, $dept, $userId, 'A', 'Budget FOH disetujui.');
    }

    /**
     * Workflow Reject — tandai budget FOH sebagai REJECTED (R) dengan catatan.
     */
    public function rejectBudget(string $year, ?string $dept, int $userId, string $note = ''): array
    {
        return $this->setBudgetStatus($year, $dept, $userId, 'R', $note !== '' ? $note : 'Budget FOH ditolak.');
    }

    /**
     * Upsert status budget FOH di yp_plan__trans_status_budget (legacy W/A/R).
     */
    private function setBudgetStatus(string $year, ?string $dept, int $userId, string $status, string $note): array
    {
        $this->db->transStart();

        $existing = $this->db->table('yp_plan__trans_status_budget')
            ->where('type_budget', 'FOH')
            ->where('year_code', $year)
            ->where('cost_center', $dept)
            ->get()->getRowArray();

        $data = [
            'status'       => $status,
            'notes'        => $note,
            'created_by'   => $userId,
            'created_date' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $this->db->table('yp_plan__trans_status_budget')
                ->where('id_trans', $existing['id_trans'])
                ->update($data);
        } else {
            $this->db->table('yp_plan__trans_status_budget')->insert(array_merge([
                'type_budget' => 'FOH',
                'cost_center' => $dept,
                'year_code'   => $year,
            ], $data));
        }

        // Bila skema punya submit_status (modern), sinkronkan juga.
        if (\App\Libraries\DbCompat::hasEntrySubmitStatus()) {
            $builder = $this->db->table('yp_plan__trans_budget_entry_data')
                ->where('year_code', $year)
                ->where('source', 'FOH');
            if (! empty($dept)) {
                $builder->where('id_dept', $dept);
            }
            $builder->update([
                'submit_status' => $status === 'A' ? 'APPROVED' : ($status === 'R' ? 'REJECTED' : 'SUBMITTED'),
                'submit_by'     => $userId,
                'submit_date'   => date('Y-m-d H:i:s'),
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal memperbarui status budget FOH.'];
        }

        return ['success' => true, 'message' => $note];
    }

    /**
     * Baca status workflow budget FOH per dept & tahun.
     * Return: null bila belum ada, atau array [status, notes, created_date].
     */
    public function getBudgetStatus(string $year, ?string $dept): ?array
    {
        if (empty($dept)) {
            return null;
        }

        try {
            return $this->db->table('yp_plan__trans_status_budget')
                ->where('type_budget', 'FOH')
                ->where('year_code', $year)
                ->where('cost_center', $dept)
                ->get()->getRowArray();
        } catch (\Throwable $e) {
            return null;
        }
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

    /* ------------------------------------------------------------------
     * Summary
     * ------------------------------------------------------------------ */

    /**
     * Ringkasan budget FOH per cost center.
     */
    public function getSummary(string $year): array
    {
        $hasSource   = \App\Libraries\DbCompat::hasEntrySource();
        $hasSubmit   = \App\Libraries\DbCompat::hasEntrySubmitStatus();
        $sourceCond  = $hasSource ? "AND t.source = 'FOH'" : '';
        $submitExpr  = $hasSubmit
            ? "SUM(CASE WHEN t.submit_status = 'SUBMITTED' THEN 1 ELSE 0 END)"
            : '0';

        $sql = "SELECT t.id_dept,
                       COALESCE(cc.cost_desc, '') AS cost_desc,
                       IFNULL(SUM(t.`1`),0) AS jan, IFNULL(SUM(t.`2`),0) AS feb,
                       IFNULL(SUM(t.`3`),0) AS mar, IFNULL(SUM(t.`4`),0) AS apr,
                       IFNULL(SUM(t.`5`),0) AS may, IFNULL(SUM(t.`6`),0) AS jun,
                       IFNULL(SUM(t.`7`),0) AS jul, IFNULL(SUM(t.`8`),0) AS aug,
                       IFNULL(SUM(t.`9`),0) AS sep, IFNULL(SUM(t.`10`),0) AS oct,
                       IFNULL(SUM(t.`11`),0) AS nov, IFNULL(SUM(t.`12`),0) AS `dec`,
                       IFNULL(SUM(t.total),0) AS total,
                       {$submitExpr} AS submitted_rows
                FROM yp_plan__trans_budget_entry_data t
                LEFT JOIN gw_plan__master_cost_center cc ON cc.cost_center = t.id_dept
                WHERE t.year_code = ? {$sourceCond}
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
     * Summary View Data, Summary by Cost Center & Summary by Account
     * (PRD FOH Summary — replika sistem lama yp_budget/foh)
     * ------------------------------------------------------------------ */

    /**
     * Dropdown cost center yang memiliki data budget (replika query lama summary()).
     */
    public function getCostCentersWithData(): array
    {
        $sql = "SELECT a.cost_center, a.cost_desc, a.cost_center_sap
                FROM gw_plan__master_cost_center a
                INNER JOIN yp_plan__trans_budget_entry_data b ON a.cost_center = b.id_dept
                WHERE (a.cost_center BETWEEN 810 AND 950 OR a.cost_center IN ('8860','630','636'))
                GROUP BY a.cost_center, a.cost_desc, a.cost_center_sap
                ORDER BY a.cost_center ASC";

        try {
            return $this->db->query($sql)->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'FohModel::getCostCentersWithData: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Dropdown cost center "New Lines" (replika query lama summary()).
     */
    public function getCostCentersNewlines(string $year): array
    {
        $sql = "SELECT cost_center, cost_desc, cost_center_sap
                FROM gw_plan__master_cost_center
                WHERE cost_center IN (
                    SELECT LEFT(id_dept, 3)
                    FROM yp_plan__trans_budget_entry_data_newlines
                    WHERE id_coa LIKE '66%' AND year_code = ?
                    GROUP BY id_dept
                )
                ORDER BY cost_center ASC";

        try {
            return $this->db->query($sql, [(int) $year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'FohModel::getCostCentersNewlines: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Tab View Data — replika exact Foh::cari_actual_table() di sistem lama.
     *
     * Branching mengikuti logika lama berdasarkan nilai $deptInput:
     *  - dept SAP (> 5 digit) dengan data di _newlines   → query newlines
     *  - dept normal (3 digit, mis. 810/910)             → query single dept
     *  - '1'  → [All] Only New Lines
     *  - '3'  → [Eng] All Cost Center Engineering
     *  - '2'  → [All] Exclude New Lines
     *  - '0'  → [All] Include New Lines
     */
    public function getViewData(string $year, string $deptInput): array
    {
        $yearInt = (int) $year;

        // Prologue replica cari_actual_table: resolve dept alias (cost_center vs cost_center_sap)
        $deptMeta = $this->resolveCostCenterIdentifiers($deptInput);
        $deptx    = $deptInput;
        $lengths  = strlen($deptx);

        if ($lengths > 5) {
            $cnt = 0;
            try {
                $row = $this->db->table('yp_plan__trans_budget_entry_data_newlines')
                    ->selectCount('id', 'cnt')
                    ->where('id_dept', $deptInput)
                    ->get()->getRow();
                $cnt = (int) ($row->cnt ?? 0);
            } catch (\Throwable $e) {
                $cnt = 0;
            }

            if ($cnt <= 0 && ($deptMeta['cost_center'] !== $deptInput || $deptMeta['cost_center_sap'] !== $deptInput)) {
                $deptx   = (string) $deptMeta['cost_center'];
                $lengths = strlen($deptx);
            }
        } elseif ($deptMeta['cost_center'] !== $deptInput || $deptMeta['cost_center_sap'] !== $deptInput) {
            $deptx   = (string) $deptMeta['cost_center'];
            $lengths = strlen($deptx);
        }

        try {
            if ($lengths > 5) {
                $sql = $this->buildViewDataSap($yearInt, $this->db->escape($deptx));
            } elseif ($deptx !== '0' && $deptx !== '1' && $deptx !== '2' && $deptx !== '3') {
                $sql = $this->buildViewDataSingle($yearInt, $this->db->escape($deptx));
            } elseif ($deptx === '1') {
                $sql = $this->buildViewDataOnlyNewlines($yearInt);
            } elseif ($deptx === '3') {
                $sql = $this->buildViewDataEngineering($yearInt);
            } elseif ($deptx === '2') {
                $sql = $this->buildViewDataExcludeNewlines($yearInt);
            } else {
                $sql = $this->buildViewDataIncludeNewlines($yearInt);
            }

            return $this->db->query($sql)->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'FohModel::getViewData: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Tab Summary by Cost Center — replika Foh::summary_costcenter().
     */
    public function getSummaryCostCenter(string $year): array
    {
        $y      = (int) $year;
        $groups = $this->summaryGroupSpec();

        $members = [];
        foreach ($groups as $g) {
            [$code, $jenis, $ids] = $g;
            foreach (['yp_plan__trans_budget_entry_data', 'yp_plan__trans_budget_entry_data_newlines'] as $tbl) {
                $members[] = "SELECT
                    '{$code}' AS groups,
                    a.main_account,
                    a.`cost_center_desc`,
                    cost_center AS tipe,
                    id_cost_header,
                    cost_desc,
                    '{$jenis}' jenis,
                    c.total,
                    (b.`1`+b.`2`+b.`3`+b.`4`+b.`5`+b.`6`+b.`7`+b.`8`) total_actual
                FROM gw_plan__master_coa a
                LEFT JOIN {$tbl} c ON a.`main_account` = c.`id_coa` AND a.type IN ('FOH') AND c.`year_code` = {$y}
                LEFT JOIN yp_plan__trans_budget_actual b ON c.`id_coa` = b.`id_coa` AND c.year_code = b.`year_code` AND c.`id_dept` = b.`id_dept`
                LEFT JOIN gw_plan__master_cost_center xx ON c.`id_dept` = xx.cost_center
                WHERE c.`year_code` = {$y}
                GROUP BY a.`main_account`, cost_center
                HAVING id_cost_header IN ({$ids})";
            }
        }

        $sql = "SELECT jenis, `groups`, tipe, main_account, cost_center_desc, cost_desc,
                       SUM(total_actual) total_actual, SUM(total) total_budget
                FROM (" . implode(' UNION ALL ', $members) . ") aa
                GROUP BY tipe, main_account, `groups`
                HAVING total_budget > 0
                ORDER BY `groups`, tipe, main_account DESC";

        try {
            return $this->db->query($sql)->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'FohModel::getSummaryCostCenter: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Tab Summary by Account — replika Foh::summary_account().
     */
    public function getSummaryAccount(string $year): array
    {
        $y      = (int) $year;
        $groups = $this->summaryGroupSpec();

        $members = [];
        foreach ($groups as $g) {
            [$code, $jenis, $ids] = $g;
            foreach (['yp_plan__trans_budget_entry_data', 'yp_plan__trans_budget_entry_data_newlines'] as $tbl) {
                $members[] = "SELECT
                    '{$code}' AS `groups`,
                    '{$jenis}' AS jenis,
                    main_account AS tipe,
                    id_cost_header,
                    cost_center_desc cost_center_desc,
                    c.total
                FROM gw_plan__master_coa a
                LEFT JOIN {$tbl} c ON a.`main_account` = c.`id_coa` AND c.year_code = {$y} AND a.type IN ('FOH')
                HAVING id_cost_header in ({$ids})";
            }
        }

        $sql = "SELECT `groups`, jenis, tipe, cost_center_desc, sum(total) total
                FROM (" . implode(' UNION ALL ', $members) . ") aa
                GROUP BY cost_center_desc, `groups`
                HAVING total > 0
                ORDER BY `groups`, total DESC";

        try {
            return $this->db->query($sql)->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'FohModel::getSummaryAccount: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Spesifikasi grouping category (kode, jenis, daftar id_cost_header).
     */
    private function summaryGroupSpec(): array
    {
        return [
            ['AA1', 'Personnel Exp', '1, 2'],
            ['BB1', 'Maintenance', '11'],
            ['CC1', 'GA Expense', '3, 4, 5, 6, 7, 9, 12, 13, 14, 15, 16, 17, 18, 19, 21, 22, 23, 24, 25, 26'],
            ['DD1', 'Energy', '8'],
            ['EE1', 'Depreciation & Amort', '20'],
            ['FF1', 'Consumble', '10'],
        ];
    }

    /**
     * Resolve cost center dari input (replika _resolve_cost_center_identifiers).
     */
    private function resolveCostCenterIdentifiers(string $deptInput): array
    {
        $deptInput = trim($deptInput);

        $row = null;
        try {
            $row = $this->db->table('gw_plan__master_cost_center')
                ->where('cost_center', $deptInput)
                ->orWhere('cost_center_sap', $deptInput)
                ->limit(1)
                ->get()
                ->getRowArray();
        } catch (\Throwable $e) {
            $row = null;
        }

        $deptNum = (! empty($row['cost_center'])) ? (string) $row['cost_center'] : $deptInput;
        $deptSap = (! empty($row['cost_center_sap'])) ? (string) $row['cost_center_sap'] : $deptInput;

        return [
            'input'           => $deptInput,
            'cost_center'     => $deptNum,
            'cost_center_sap' => $deptSap,
            'cost_desc'       => (! empty($row['cost_desc'])) ? (string) $row['cost_desc'] : $deptInput,
            'list_sql'        => $this->buildSqlInList([$deptInput, $deptNum, $deptSap]),
        ];
    }

    private function buildSqlInList(array $values): string
    {
        $clean = [];
        foreach ($values as $value) {
            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }
            $clean[$value] = "'" . addslashes($value) . "'";
        }

        return empty($clean) ? "''" : implode(',', array_values($clean));
    }

    /**
     * Fragment CASE assumption — style 'new' (newlines & union) vs 'std' (single dept).
     */
    private function fohAssumptionFragment(string $style): string
    {
        if ($style === 'new') {
            return "CASE
                WHEN main_account = '6605011' THEN CONCAT(ass_b.`amount`, '%')
                WHEN main_account = '6605013' THEN CONCAT(ass_b.`amount`, '%')
                WHEN main_account = '6605014' THEN CONCAT(ass_d.`amount`, '%')
                WHEN main_account = '6605023' THEN CONCAT(ass_b.`amount`, '%')
                WHEN main_account = '6605027' THEN CONCAT(ass_b.`amount`, '%')
                WHEN main_account = '6605028' THEN CONCAT(ass_b.`amount`, '%')
                WHEN main_account = '6605024' THEN '1x Gaji'
                WHEN main_account = '6605012' THEN ass_c.`amount`
                WHEN main_account = '6605022' THEN CONCAT(ass_e.`amount`, '%')
                WHEN main_account = '6605026' THEN CONCAT(ass_f.`amount`, '%')
                ELSE IFNULL(CONCAT(ass.`value`, '%'), 0)
            END AS assumption";
        }

        return "CASE
            WHEN main_account = '6605011' then CONCAT(ass_b.`amount`, '%')
            WHEN main_account = '6605024' then '1x Gaji'
            WHEN main_account = '6605012' THEN ass_c.`amount`
            WHEN main_account = '6605014' THEN CONCAT(ass_d.`amount`, '%')
            WHEN main_account = '6605022' THEN CONCAT(ass_e.`amount`, '%')
            WHEN main_account = '6605026' THEN CONCAT(ass_f.`amount`, '%')
            ELSE IFNULL(CONCAT(ass.`value`, '%'), 0)
        END AS assumption";
    }

    /**
     * Fragment LEFT JOIN assumption + trans_foh (dipakai seluruh branch view data).
     */
    private function fohAssumptionJoins(int $year): string
    {
        return "LEFT JOIN yp_plan__master_assumption ass
                ON ass.type_id = '3' AND ass.year = {$year}
                AND cost_center_header NOT IN ('Salaries','Employee Fringe Benefit','Depreciation Amortization')
                LEFT JOIN yp_plan__trans_foh ass_b ON ass_b.type_foh = '1' AND ass_b.year_code = {$year}
                LEFT JOIN yp_plan__trans_foh ass_c ON ass_c.type_foh = '2' AND ass_c.year_code = {$year}
                LEFT JOIN yp_plan__trans_foh ass_d ON ass_d.type_foh = '5' AND ass_d.year_code = {$year}
                LEFT JOIN yp_plan__trans_foh ass_e ON ass_e.type_foh = '3' AND ass_e.year_code = {$year}
                LEFT JOIN yp_plan__trans_foh ass_f ON ass_f.type_foh = '6' AND ass_f.year_code = {$year}";
    }

    /**
     * Kolom isi_1..isi_12 + isi_tot dari tabel budget (SUM vs plain, replika lama).
     */
    private function fohViewIsi(bool $summed): string
    {
        $months = ['1','2','3','4','5','6','7','8','9','10','11','12'];
        $parts  = [];
        foreach ($months as $m) {
            $parts[] = ($summed ? "sum(IFNULL(c.`{$m}`, 0))" : "IFNULL(c.`{$m}`,0)") . " AS 'isi_{$m}'";
        }
        $parts[] = ($summed ? "sum(IFNULL(c.`total`, 0))" : "IFNULL(c.`total`,0)") . " AS 'isi_tot'";

        return implode(', ', $parts);
    }

    /**
     * Outer SELECT untuk branch union (MAX/SUM per main_account).
     */
    private function fohViewOuterSelect(): string
    {
        return "MAX(main_account) main_account, MAX(cost_center_header) cost_center_header, MAX(cost_center_desc) cost_center_desc,
            SUM(JAN) 'JAN', SUM(FEB) 'FEB', SUM(MAR) 'MAR', SUM(APR) 'APR', SUM(MAY) 'MAY', SUM(JUN) 'JUN', SUM(JUL) 'JUL', SUM(AUG) 'AUG',
            SUM(AVG) 'AVG', SUM(TOT) 'TOT', MAX(assumption) assumption,
            SUM(isi_1) 'isi_1', SUM(isi_2) 'isi_2', SUM(isi_3) 'isi_3', SUM(isi_4) 'isi_4',
            SUM(isi_5) 'isi_5', SUM(isi_6) 'isi_6', SUM(isi_7) 'isi_7', SUM(isi_8) 'isi_8',
            SUM(isi_9) 'isi_9', SUM(isi_10) 'isi_10', SUM(isi_11) 'isi_11', SUM(isi_12) 'isi_12',
            SUM(isi_tot) 'isi_tot'";
    }

    /**
     * Branch A: dept SAP (6 digit) dengan data di _newlines.
     */
    private function buildViewDataSap(int $year, string $deptEscaped): string
    {
        $ass  = $this->fohAssumptionFragment('new');
        $join = $this->fohAssumptionJoins($year);
        $isi  = $this->fohViewIsi(true);

        return "SELECT
            a.main_account, a.cost_center_header, a.cost_center_desc,
            0 AS 'JAN', 0 AS 'FEB', 0 AS 'MAR', 0 AS 'APR', 0 AS 'MAY', 0 AS 'JUN', 0 AS 'JUL', 0 AS 'AUG',
            0 AS AVG, 0 AS TOT,
            {$ass},
            '' notes,
            {$isi}
        FROM gw_plan__master_coa a
        LEFT JOIN yp_plan__trans_budget_entry_data_newlines c
            ON a.`main_account` = c.`id_coa` AND c.`year_code` = {$year} AND a.type = 'FOH'
        {$join}
        WHERE c.id_dept = {$deptEscaped} AND main_account LIKE '6605%'
        GROUP BY a.main_account
        ORDER BY a.main_account ASC";
    }

    /**
     * Branch B: dept normal (3 digit) — query single dept.
     */
    private function buildViewDataSingle(int $year, string $deptEscaped): string
    {
        $ass  = $this->fohAssumptionFragment('std');
        $join = $this->fohAssumptionJoins($year);

        return "SELECT
            a.main_account, a.cost_center_header, a.cost_center_desc,
            b.`1` AS 'JAN', b.`2` AS 'FEB', b.`3` AS 'MAR', b.`4` AS 'APR',
            b.`5` AS 'MAY', b.`6` AS 'JUN', b.`7` AS 'JUL', b.`8` AS 'AUG',
            (b.`1`+b.`2`+b.`3`+b.`4`+b.`5`+b.`6`+b.`7`+b.`8`)/8 as AVG,
            (b.`1`+b.`2`+b.`3`+b.`4`+b.`5`+b.`6`+b.`7`+b.`8`) as TOT,
            {$ass},
            '' notes,
            IFNULL(c.`1`,0) AS 'isi_1', IFNULL(c.`2`,0) AS 'isi_2', IFNULL(c.`3`,0) AS 'isi_3', IFNULL(c.`4`,0) AS 'isi_4',
            IFNULL(c.`5`,0) AS 'isi_5', IFNULL(c.`6`,0) AS 'isi_6', IFNULL(c.`7`,0) AS 'isi_7', IFNULL(c.`8`,0) AS 'isi_8',
            IFNULL(c.`9`,0) AS 'isi_9', IFNULL(c.`10`,0) AS 'isi_10', IFNULL(c.`11`,0) AS 'isi_11', IFNULL(c.`12`,0) AS 'isi_12',
            IFNULL(c.`total`,0) AS 'isi_tot'
        FROM gw_plan__master_coa a
        LEFT JOIN yp_plan__trans_budget_actual b
            ON a.`main_account` = b.`id_coa` AND b.`year_code` = {$year} AND b.id_dept = {$deptEscaped}
        LEFT JOIN yp_plan__trans_budget_entry_data c
            ON a.`main_account` = c.`id_coa` AND c.`year_code` = {$year} AND b.id_dept = c.id_dept
        {$join}
        WHERE a.type = 'FOH'
        ORDER BY a.main_account ASC";
    }

    /**
     * Branch C: dept = '1' — [All] Only New Lines.
     */
    private function buildViewDataOnlyNewlines(int $year): string
    {
        $ass  = $this->fohAssumptionFragment('new');
        $join = $this->fohAssumptionJoins($year);
        $isi  = $this->fohViewIsi(true);

        $entryMember = "SELECT a.main_account, a.cost_center_header, a.cost_center_desc,
            0 'JAN', 0 'FEB', 0 'MAR', 0 'APR', 0 'MAY', 0 'JUN', 0 'JUL', 0 'AUG', 0 AVG, 0 TOT,
            {$ass}, '' notes, {$isi}
        FROM gw_plan__master_coa a
        LEFT JOIN yp_plan__trans_budget_entry_data c
            ON a.`main_account` = c.`id_coa` AND c.`year_code` = {$year} AND a.type = 'FOH'
        {$join}
        WHERE main_account LIKE '6605%'
        GROUP BY a.main_account, c.id_dept";

        $newlinesMember = str_replace(
            'yp_plan__trans_budget_entry_data c',
            'yp_plan__trans_budget_entry_data_newlines c',
            $entryMember
        );

        return "SELECT {$this->fohViewOuterSelect()}
                FROM ( {$entryMember} UNION ALL {$newlinesMember} ) aa
                GROUP BY aa.main_account";
    }

    /**
     * Branch D: dept = '0' — [All] Include New Lines.
     */
    private function buildViewDataIncludeNewlines(int $year): string
    {
        $assStd    = $this->fohAssumptionFragment('std');
        $assNew    = $this->fohAssumptionFragment('new');
        $join      = $this->fohAssumptionJoins($year);
        $isiPlain  = $this->fohViewIsi(false);
        $isiSum    = $this->fohViewIsi(true);

        $entryMember = "SELECT a.main_account, a.cost_center_header, a.cost_center_desc,
            0 'JAN', 0 'FEB', 0 'MAR', 0 'APR', 0 'MAY', 0 'JUN', 0 'JUL', 0 'AUG', 0 AVG, 0 TOT,
            {$assStd}, '' notes, {$isiPlain}
        FROM gw_plan__master_coa a
        LEFT JOIN yp_plan__trans_budget_entry_data c
            ON a.`main_account` = c.`id_coa` AND c.`year_code` = {$year}
        {$join}
        WHERE a.type = 'FOH'";

        $actualMember = "SELECT a.main_account, a.cost_center_header, a.cost_center_desc,
            FORMAT(b.`1`, 2) 'JAN', FORMAT(b.`2`, 2) 'FEB', FORMAT(b.`3`, 2) 'MAR', FORMAT(b.`4`, 2) 'APR',
            FORMAT(b.`5`, 2) 'MAY', FORMAT(b.`6`, 2) 'JUN', FORMAT(b.`7`, 2) 'JUL', FORMAT(b.`8`, 2) 'AUG',
            (b.`1`+b.`2`+b.`3`+b.`4`+b.`5`+b.`6`+b.`7`+b.`8`)/8 AVG,
            (b.`1`+b.`2`+b.`3`+b.`4`+b.`5`+b.`6`+b.`7`+b.`8`) TOT,
            '' assumption, '' notes,
            '0' 'isi_1', '0' 'isi_2', '0' 'isi_3', '0' 'isi_4', '0' 'isi_5', '0' 'isi_6', '0' 'isi_7', '0' 'isi_8',
            '0' 'isi_9', '0' 'isi_10', '0' 'isi_11', '0' 'isi_12', '0' 'isi_tot'
        FROM gw_plan__master_coa a
        LEFT JOIN yp_plan__trans_budget_actual b
            ON a.`main_account` = b.`id_coa` AND b.`year_code` = {$year}
        WHERE a.type = 'FOH'";

        $newlinesMember = "SELECT a.main_account, a.cost_center_header, a.cost_center_desc,
            0 'JAN', 0 'FEB', 0 'MAR', 0 'APR', 0 'MAY', 0 'JUN', 0 'JUL', 0 'AUG', 0 AVG, 0 TOT,
            {$assNew}, '' notes, {$isiSum}
        FROM gw_plan__master_coa a
        LEFT JOIN yp_plan__trans_budget_entry_data_newlines c
            ON a.`main_account` = c.`id_coa` AND c.`year_code` = {$year} AND a.type = 'FOH'
        {$join}
        WHERE main_account LIKE '6605%'
        GROUP BY a.main_account";

        return "SELECT {$this->fohViewOuterSelect()}
                FROM ( {$entryMember} UNION ALL {$actualMember} UNION ALL {$newlinesMember} ) aa
                GROUP BY aa.main_account
                ORDER BY aa.main_account ASC";
    }

    /**
     * Branch E: dept = '2' — [All] Exclude New Lines.
     */
    private function buildViewDataExcludeNewlines(int $year): string
    {
        $ass  = $this->fohAssumptionFragment('std');
        $join = $this->fohAssumptionJoins($year);
        $isi  = $this->fohViewIsi(false);

        $inner = "SELECT a.main_account, a.cost_center_header, a.cost_center_desc,
            FORMAT(b.`1`, 2) 'JAN', FORMAT(b.`2`, 2) 'FEB', FORMAT(b.`3`, 2) 'MAR', FORMAT(b.`4`, 2) 'APR',
            FORMAT(b.`5`, 2) 'MAY', FORMAT(b.`6`, 2) 'JUN', FORMAT(b.`7`, 2) 'JUL', FORMAT(b.`8`, 2) 'AUG',
            (b.`1`+b.`2`+b.`3`+b.`4`+b.`5`+b.`6`+b.`7`+b.`8`)/8 AVG,
            (b.`1`+b.`2`+b.`3`+b.`4`+b.`5`+b.`6`+b.`7`+b.`8`) TOT,
            {$ass}, '' notes, {$isi}
        FROM gw_plan__master_coa a
        LEFT JOIN yp_plan__trans_budget_actual b
            ON a.`main_account` = b.`id_coa` AND b.`year_code` = {$year}
        LEFT JOIN yp_plan__trans_budget_entry_data c
            ON a.`main_account` = c.`id_coa` AND c.`year_code` = {$year} AND b.id_dept = c.id_dept
        {$join}
        WHERE a.type = 'FOH'";

        return "SELECT {$this->fohViewOuterSelect()}
                FROM ( {$inner} ) aa
                GROUP BY aa.main_account
                ORDER BY aa.main_account ASC";
    }

    /**
     * Branch F: dept = '3' — [Eng] All Cost Center Engineering.
     */
    private function buildViewDataEngineering(int $year): string
    {
        $eng    = "'910','916','920','926','930','936','940','946','950'";
        $engSap = "'910000','916000','920000','926000','930000','936000','940000','946000','950000'";

        $assStd    = $this->fohAssumptionFragment('std');
        $assNew    = $this->fohAssumptionFragment('new');
        $join      = $this->fohAssumptionJoins($year);
        $isiPlain  = $this->fohViewIsi(false);
        $isiSum    = $this->fohViewIsi(true);

        $entryMember = "SELECT a.main_account, a.cost_center_header, a.cost_center_desc,
            0 'JAN', 0 'FEB', 0 'MAR', 0 'APR', 0 'MAY', 0 'JUN', 0 'JUL', 0 'AUG', 0 AVG, 0 TOT,
            {$assStd}, '' notes, {$isiPlain}
        FROM gw_plan__master_coa a
        LEFT JOIN yp_plan__trans_budget_entry_data c
            ON a.`main_account` = c.`id_coa` AND c.`year_code` = {$year} AND c.id_dept IN ({$eng})
        {$join}
        WHERE a.type = 'FOH'";

        $actualMember = "SELECT a.main_account, a.cost_center_header, a.cost_center_desc,
            FORMAT(b.`1`, 2) 'JAN', FORMAT(b.`2`, 2) 'FEB', FORMAT(b.`3`, 2) 'MAR', FORMAT(b.`4`, 2) 'APR',
            FORMAT(b.`5`, 2) 'MAY', FORMAT(b.`6`, 2) 'JUN', FORMAT(b.`7`, 2) 'JUL', FORMAT(b.`8`, 2) 'AUG',
            (b.`1`+b.`2`+b.`3`+b.`4`+b.`5`+b.`6`+b.`7`+b.`8`)/8 AVG,
            (b.`1`+b.`2`+b.`3`+b.`4`+b.`5`+b.`6`+b.`7`+b.`8`) TOT,
            '' assumption, '' notes,
            '0' 'isi_1', '0' 'isi_2', '0' 'isi_3', '0' 'isi_4', '0' 'isi_5', '0' 'isi_6', '0' 'isi_7', '0' 'isi_8',
            '0' 'isi_9', '0' 'isi_10', '0' 'isi_11', '0' 'isi_12', '0' 'isi_tot'
        FROM gw_plan__master_coa a
        LEFT JOIN yp_plan__trans_budget_actual b
            ON a.`main_account` = b.`id_coa` AND b.`year_code` = {$year} AND b.id_dept IN ({$eng})
        WHERE a.type = 'FOH'";

        $newlinesMember = "SELECT a.main_account, a.cost_center_header, a.cost_center_desc,
            0 'JAN', 0 'FEB', 0 'MAR', 0 'APR', 0 'MAY', 0 'JUN', 0 'JUL', 0 'AUG', 0 AVG, 0 TOT,
            {$assNew}, '' notes, {$isiSum}
        FROM gw_plan__master_coa a
        LEFT JOIN yp_plan__trans_budget_entry_data_newlines c
            ON a.`main_account` = c.`id_coa` AND c.`year_code` = {$year} AND a.type = 'FOH' AND c.id_dept IN ({$engSap})
        {$join}
        WHERE main_account LIKE '6605%'
        GROUP BY a.main_account";

        return "SELECT {$this->fohViewOuterSelect()}
                FROM ( {$entryMember} UNION ALL {$actualMember} UNION ALL {$newlinesMember} ) aa
                GROUP BY aa.main_account
                ORDER BY aa.main_account ASC";
    }

    /* ------------------------------------------------------------------
     * Breakdown Sub-Detail COA (standar 1.5) — lihat BudgetBreakdownTrait
     * ------------------------------------------------------------------ */

    /* ------------------------------------------------------------------
     * Entry Budget Detail — Matrix & Config Period
     * ------------------------------------------------------------------ */

    /**
     * Konfigurasi periode submit FOH dari yp_plan__master_period (tipe FOH, status A).
     * View entry_budget mengharapkan key start_date/end_date.
     */
    public function getConfigPeriod(string $module = 'FOH'): ?array
    {
        try {
            $row = $this->db->table('yp_plan__master_period')
                ->where('tipe', $module)
                ->where('status', 'A')
                ->orderBy('begda', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            if (! $row) {
                return null;
            }

            return [
                'start_date' => $row['begda'] ?? null,
                'end_date'   => $row['endda'] ?? null,
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Header Account FOH (Main Account / category FOHEXP) beserta
     * total actual per bulan untuk 8 bulan pertama (Jan-Agust).
     */
    public function getHeaderAccounts(string $year, string $dept): array
    {
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
                  AND (c.category = 'FOHEXP' OR c.type = 'FOH')
                GROUP BY c.main_account, c.id_acct_ext, c.cost_center_desc
                ORDER BY c.main_account ASC";

        try {
            return $this->db->query($sql, [$dept, $year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'FohModel::getHeaderAccounts: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Matrix data: budget (12 bulan) + actual (5 bulan pertama) per Sub-Account
     * untuk sebuah Header Account tertentu.
     */
    public function getDetailMatrix(string $year, string $dept, string $headerAccount): array
    {
        $headerAcct = (int) $headerAccount;

        // Sub-accounts di bawah header ini (category FOHEXP/type FOH).
        // Kolom main_category hanya ada di skema baru — di skema legacy
        // langkah ini dilewati dan langsung jatuh ke leaf fallback di bawah.
        $subAccounts = [];
        if (\App\Libraries\DbCompat::hasColumn('gw_plan__master_coa', 'main_category')) {
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
                $budget->where('t.source', 'FOH');
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

            // Simulated actual from entry detail breakdown
            $simulated = null;
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
                'entry_data_id' => (int) ($budgetRow['entry_data_id'] ?? 0),
                'budget' => $budgetRow ?: array_fill_keys(['b1','b2','b3','b4','b5','b6','b7','b8','b9','b10','b11','b12','btotal'], 0),
                'actual' => $actual ?: array_fill_keys(['a1','a2','a3','a4','a5','a6','a7','a8','a9','a10','a11','a12','atotal'], 0),
                'simulated' => $simulated ?: array_fill_keys(['a1','a2','a3','a4','a5','a6','a7','a8','a9','a10','a11','a12','atotal'], 0),
            ]);
        }

        return $result;
    }

    /**
     * Simpan detail breakdown items (batch) — dipanggil dari modal detail.
     *
     * Kolom bulan di yp_plan__trans_budget_entry_detail bernama jan..dec
     * (bukan numerik 1..12), konsisten dengan BudgetBreakdownTrait.
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
        if ($entryDataId <= 0) {
            $idCoa  = (int) ($parentHint['id_coa'] ?? 0);
            $idDept = (int) ($parentHint['id_dept'] ?? 0);
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
            $idDept = (int) ($parentHint['id_dept'] ?? 0);
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
                    $insertData['source']        = 'FOH';
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
        $saved = 0;

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $nama = trim((string) ($item['nama_barang'] ?? ''));
            if ($nama === '') {
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
                'nama_barang'   => $nama,
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

        return ['success' => true, 'message' => "Detail item berhasil disimpan ({$saved} baris).", 'count' => $saved];
    }

    /**
     * Data FOH untuk tab View Data (Actual vs Budget per cost center).
     */
    public function getFohDataForView(string $year, string $dept): array
    {
        // Ambil semua header accounts
        $headers = $this->getHeaderAccounts($year, $dept);

        $result = [];
        foreach ($headers as $h) {
            $headerId = (int) $h['main_account'];
            $matrix = $this->getDetailMatrix($year, $dept, (string) $headerId);

            $budgetTotal = 0;
            $actualTotal = 0;
            $budgetMonthly = array_fill(1, 12, 0);
            $actualMonthly = array_fill(1, 12, 0);

            foreach ($matrix as $row) {
                for ($m = 1; $m <= 12; $m++) {
                    $budgetMonthly[$m] += (float) ($row['budget']['b' . $m] ?? 0);
                    $actualMonthly[$m] += (float) ($row['actual']['a' . $m] ?? 0);
                }
                $budgetTotal += (float) ($row['budget']['btotal'] ?? 0);
                $actualTotal += (float) ($row['actual']['atotal'] ?? 0);
            }

            $result[] = [
                'header_name' => $h['coa_name'] ?: $h['acct_code'],
                'acct_code'   => $h['acct_code'],
                'budget'      => $budgetMonthly,
                'budget_total'=> $budgetTotal,
                'actual'      => $actualMonthly,
                'actual_total'=> $actualTotal,
                'items'       => $matrix,
            ];
        }

        return $result;
    }
}
