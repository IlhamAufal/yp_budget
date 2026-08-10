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
     * Matrix data per Sub-Account untuk sebuah Header Account OPEX Selling:
     * budget (12 bulan) + actual (realisasi) + simulated (breakdown detail).
     *
     * Header dikirim sebagai main_account (kode COA) — konsisten dengan modul GA/FOH.
     */
    public function getDetailMatrix(string $year, ?string $dept, string $header, string $source = self::SOURCE): array
    {
        $headerAcct = (int) $header;

        // Sub-accounts di bawah header ini (Master COA tipe SELLING).
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
