<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * OpexGaModel — OPEX GA (PRD Phase 2.1).
 *
 * Entry budget OPEX GA disimpan ke yp_plan__trans_budget_entry_data dengan
 * source = 'OPEX' sehingga mengalir ke Monitoring & P/L. Menyediakan save
 * budget nyata, workflow submit, actual, dan breakdown sub-detail COA
 * (BudgetBreakdownTrait).
 */
class OpexGaModel extends Model
{
    use \App\Models\Traits\BudgetBreakdownTrait;
    use \App\Models\Traits\ActualImportTrait;

    /** Sumber data di yp_plan__trans_budget_entry_data. */
    public const SOURCE = 'OPEX';

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
     * Cost center tipe OPEX (dropdown entry & filter).
     */
    public function getCostCenters(): array
    {
        return $this->db->table('gw_plan__master_cost_center')
            ->select("cost_center, COALESCE(NULLIF(cost_center_sap,''), CAST(cost_center AS CHAR)) AS cc_code, cost_desc, cost_center_sap")
            ->where('status', 'A')
            ->where('type', 'OPEX')
            ->orderBy('cost_center', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * COA relevan untuk OPEX GA (type GA / kategori admin & gaji).
     */
    public function getCoas(): array
    {
        return $this->db->table('gw_plan__master_coa')
            ->select("main_account, COALESCE(NULLIF(id_acct_ext,''), CAST(main_account AS CHAR)) AS acct_code, cost_center_desc")
            ->groupStart()
                ->where('type', 'GA')
                ->orWhereIn('category', ['ADMINEXP', 'SALARYEXP', 'OTHERS'])
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
     * Data budget OPEX GA tersimpan per tahun (+ opsional cost center).
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

        // Tampilkan baris modern (source sesuai) + baris legacy (source NULL) —
        // filter ini hanya bila kolom source tersedia di skema.
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
     * Simpan / timpa seluruh baris budget OPEX GA untuk sebuah cost center.
     *
     * @return array ['success' => bool, 'message' => string, 'count' => int]
     */
    public function saveBudget(string $year, ?string $dept, array $rows, int $userId, string $source = self::SOURCE): array
    {
        if (empty($dept)) {
            return ['success' => false, 'message' => 'Cost Center wajib dipilih.', 'count' => 0];
        }

        // Kolom source tidak ada di skema legacy → entry di-nonaktifkan sementara
        // (keputusan user 6 Agt 2026: jangan ubah struktur DB).
        if (! \App\Libraries\DbCompat::hasEntrySource()) {
            return ['success' => false, 'message' => 'Penyimpanan entry OPEX GA dinonaktifkan sementara (kolom source belum tersedia di skema DB legacy).', 'count' => 0];
        }

        $this->db->transStart();

        $delete = $this->db->table('yp_plan__trans_budget_entry_data')
            ->where('year_code', $year)
            ->where('id_dept', $dept)
            ->groupStart()
                ->where('source', $source)
                ->orWhere('source IS NULL')
            ->groupEnd();
        $delete->delete();

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
                $source,
                'DRAFT',
            ], $vals));
            $saved++;
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menyimpan data OPEX GA.', 'count' => 0];
        }

        return ['success' => true, 'message' => "Data OPEX GA berhasil disimpan ({$saved} baris).", 'count' => $saved];
    }

    /**
     * Workflow Submit — tandai budget OPEX GA (dept & tahun) sebagai SUBMITTED.
     */
    public function submitBudget(string $year, ?string $dept, int $userId, string $source = self::SOURCE): array
    {
        if (! \App\Libraries\DbCompat::hasEntrySubmitStatus()) {
            return ['success' => false, 'message' => 'Workflow submit OPEX GA dinonaktifkan sementara (kolom submit_status belum tersedia di skema DB legacy).'];
        }

        $builder = $this->db->table('yp_plan__trans_budget_entry_data')
            ->where('year_code', $year)
            ->groupStart()
                ->where('source', $source)
                ->orWhere('source IS NULL')
            ->groupEnd();

        if (! empty($dept)) {
            $builder->where('id_dept', $dept);
        }

        $result = $builder->update([
            'submit_status' => 'SUBMITTED',
            'submit_by'     => $userId,
            'submit_date'   => date('Y-m-d H:i:s'),
        ]);

        if ($result === false) {
            return ['success' => false, 'message' => 'Gagal melakukan submit OPEX GA.'];
        }

        return ['success' => true, 'message' => 'Budget OPEX GA berhasil di-submit untuk persetujuan.'];
    }

    /* ------------------------------------------------------------------
     * Actual
     * ------------------------------------------------------------------ */

    /**
     * Data actual OPEX GA per tahun (+ opsional cost center).
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
     * Header Accounts (Entry Budget Detail)
     * ------------------------------------------------------------------ */

    /**
     * Daftar header account OPEX GA (Master COA tipe GA) beserta total actual
     * per bulan untuk 8 bulan pertama (Jan-Agustus).
     *
     * Sumber kolom bulan adalah yp_plan__trans_budget_actual (realisasi),
     * bukan budget entry — sesuai standar tampilan Entry Budget (kolom ACTUAL).
     */
    public function getHeaderAccounts(string $year, ?string $dept = null, string $source = self::SOURCE): array
    {
        if (empty($dept)) {
            return [];
        }

        $sql = "SELECT c.main_account,
                       COALESCE(NULLIF(c.id_acct_ext,''), CAST(c.main_account AS CHAR)) AS acct_code,
                       c.cost_center_desc AS coa_desc,
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
                  AND (c.type = 'GA' OR c.category IN ('ADMINEXP','SALARYEXP','OTHERS'))
                GROUP BY c.main_account, c.id_acct_ext, c.cost_center_desc
                ORDER BY c.main_account ASC";

        try {
            return $this->db->query($sql, [(int) $dept, $year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'OpexGaModel::getHeaderAccounts: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Matrix data per Sub-Account untuk sebuah Header Account OPEX GA:
     * budget (12 bulan) + actual (realisasi) + simulated (breakdown detail).
     *
     * Header dikirim sebagai main_account (kode COA) — sama seperti modul FOH.
     */
    public function getDetailMatrix(string $year, ?string $dept, string $header, string $source = self::SOURCE): array
    {
        $headerAcct = (int) $header;

        // Sub-accounts di bawah header ini (Master COA tipe GA).
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
            $simulated  = null;
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
     * Batch simpan detail breakdown items (dari modal detail).
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

    /**
     * Import actual dari Excel — lihat ActualImportTrait (Phase 2.3).
     */
}
