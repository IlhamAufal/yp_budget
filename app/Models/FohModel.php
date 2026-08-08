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
                ->select("IFNULL(t.`1`,0) AS b1, IFNULL(t.`2`,0) AS b2, IFNULL(t.`3`,0) AS b3,
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

            $result[] = array_merge($sub, [
                'budget' => $budgetRow ?: array_fill_keys(['b1','b2','b3','b4','b5','b6','b7','b8','b9','b10','b11','b12','btotal'], 0),
                'actual' => $actual ?: array_fill_keys(['a1','a2','a3','a4','a5','a6','a7','a8','a9','a10','a11','a12','atotal'], 0),
            ]);
        }

        return $result;
    }

    /**
     * Simpan detail breakdown items (batch) — dipanggil dari modal detail.
     *
     * @param array  $items  [{ name, monthly: {1:val,...,12:val}, sort_order }]
     * @param int    $entryDataId ID dari yp_plan__trans_budget_entry_data
     */
    public function saveDetailItemsBatch(int $entryDataId, array $items, int $userId): array
    {
        $parent = $this->db->table('yp_plan__trans_budget_entry_data')
            ->where('id', $entryDataId)
            ->get()->getRowArray();

        if (! $parent) {
            return ['success' => false, 'message' => 'Entry budget tidak ditemukan.', 'count' => 0];
        }

        $this->db->transStart();

        // Hapus item lama
        $this->db->table('yp_plan__trans_budget_entry_detail')
            ->where('entry_data_id', $entryDataId)
            ->delete();

        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
        $saved = 0;

        $sql = 'INSERT INTO yp_plan__trans_budget_entry_detail
                (entry_data_id, id_coa, id_dept, year_code, nama_barang, total, sort_order, created_by,
                 `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        foreach ($items as $idx => $item) {
            $nama = trim((string) ($item['name'] ?? ''));
            if ($nama === '') {
                continue;
            }

            $total = 0;
            $vals  = [];
            $monthly = $item['monthly'] ?? [];
            for ($m = 1; $m <= 12; $m++) {
                $val = (float) ($monthly[$m] ?? 0);
                $vals[] = $val;
                $total += $val;
            }

            $this->db->query($sql, array_merge([
                $entryDataId,
                (int) ($parent['id_coa'] ?? 0),
                (int) ($parent['id_dept'] ?? 0),
                (int) ($parent['year_code'] ?? 0),
                $nama,
                $total,
                (int) ($item['sort_order'] ?? ($idx + 1)),
                $userId,
            ], $vals));
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
