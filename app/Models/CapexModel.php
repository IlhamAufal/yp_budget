<?php

namespace App\Models;

use CodeIgniter\Model;

class CapexModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Clear & Insert Access Log
     */
    public function logAccess(string $userId, string $link, string$year): bool
    {
        $this->db->transStart();
        
        // Clear existing restrict log for user
        $this->db->table('gw_sm__access_restrict')
                 ->where('user_id', $userId)
                 ->delete();

        // Check if access is restricted
        $builder = $this->db->table('gw_sm__access_restrict');$check = $builder->where('menu_url',$link)
                         ->where('year', $year)
                         ->countAllResults();

        if ($check === 0) {$builder->insert([
                'user_id'  => $userId,
                'menu_url' => $link,
                'status'   => 'A',
                'year'     => $year
            ]);
            $this->db->transComplete();
            return true;
        }

        $this->db->transComplete();
        return false;
    }

    /**
     * Fetch Master Department Options for Login User
     */
    public function getDepartments(array $deptList): array
    {
        if (empty($deptList)) return [];

        return $this->db->table('gw_plan__master_department')
                        ->whereIn('dept_code', $deptList)
                        ->orderBy('dept_code', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    /**
     * Fetch Cost Centers filtered by Period and Capex Authorization
     */
    public function getCostCenters(array $deptList): array
    {
        if (empty($deptList)) return [];

        $today = date('Y-m-d');
        
        $subQuery =$this->db->table('gw_plan__master_cost_center a')
            ->select("CASE 
                        WHEN b.id_cost_center = '*' THEN a.cost_center
                        WHEN c.id_cost_center <> '' THEN a.cost_center
                     END AS logic", false)
            ->join('yp_plan__master_period b', "'{$today}' BETWEEN b.begda AND b.endda AND b.id_cost_center = '*' AND b.tipe = 'CAPEX'", 'left')
            ->join('yp_plan__master_period c', "'{$today}' BETWEEN c.begda AND c.endda AND (c.id_cost_center = a.cost_center OR c.id_cost_center = a.cost_center_sap) AND c.tipe = 'CAPEX'", 'left')
            ->groupBy('a.cost_center');

        return $this->db->table('gw_plan__master_cost_center')
                        ->whereIn('cost_center', $deptList)
                        ->whereIn('cost_center', $subQuery)
                        ->orderBy('cost_center', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    /**
     * Entry Table Capex Detail Data
     */
    public function getEntryTableData(string $mainAcct, string$year, string $idx, string$dept): array
    {
        return $this->db->table('yp_plan__trans_capex_entry_header a')
                        ->join('yp_plan__trans_capex_entry_detail b', 'a.id = b.id_header')
                        ->where([
                            'a.main_account' => $mainAcct,
                            'a.year_code'    => $year,
                            'a.id_coa'       => $idx,
                            'a.dept_id'      => $dept
                        ])
                        ->get()
                        ->getResultArray();
    }

    /**
     * Get Amount Depreciation Master
     */
    public function getDepreciationAmount(string $mainAccount): float
    {
        $row =$this->db->table('yp_plan__master_amount_depreciation')
                        ->select('amount')
                        ->where('main_account', $mainAccount)
                        ->get()
                        ->getRowArray();

        return $row ? (float) $row['amount'] : 0.0;
    }

    /**
     * Save Capex Entries with Multi-table Transaction
     */
    public function saveCapexTransaction(array $postData, string $year, string$user): bool
    {
        $this->db->transStart();

        $main   =$postData['main_account'];
        $dept   =$postData['deptx'];
        $idCoa  =$postData['id_coa'];
        $amtDep =$postData['amt_depre'];

        // 1. Clean existing headers for this user selection
        $this->db->table('yp_plan__trans_capex_entry_header')
                 ->where(['year_code' => $year, 'main_account' =>$main, 'dept_id' => $dept, 'id_coa' =>$idCoa])
                 ->delete();

        // 2. Clean Orphan Details/Totals/Depreciations
        $this->db->query("DELETE d FROM yp_plan__trans_capex_entry_detail d
                          LEFT JOIN yp_plan__trans_capex_entry_header h ON h.id = d.id_header
                          WHERE h.id IS NULL");
        $this->db->query("DELETE dep FROM yp_plan__trans_capex_entry_depreciation dep
                          LEFT JOIN yp_plan__trans_capex_entry_header h ON h.id = dep.id_header
                          WHERE h.id IS NULL");
        $this->db->query("DELETE t FROM yp_plan__trans_capex_entry_total t
                          LEFT JOIN yp_plan__trans_capex_entry_header h ON h.id = t.id_header
                          WHERE h.id IS NULL");

        // 3. Batch Insert Items & Details
        $lastInsertedHeaderId = null;

        if (isset($postData['desc']) && is_array($postData['desc'])) {
            foreach ($postData['desc'] as $row =>$desc) {
                if (empty($desc)) continue;

                $headerData = [
                    'main_account' => $main,
                    'id_coa'       => $idCoa,
                    'dept_id'      => $dept,
                    'newlines'     => $postData['newlines'][$row] ?? 0,                     'item_desc'    =>$desc,
                    'cost_center'  => $postData['cc'][$row] ?? '',
                    'unit'         => $postData['amount'][$row] ?? 0,
                    'unit_price'   => $postData['nominal'][$row] ?? 0,
                    'remarks'      => $postData['remarks'][$row] ?? '',
                    'year_code'    => $year
                ];

                $this->db->table('yp_plan__trans_capex_entry_header')->insert($headerData);
                $lastInsertedHeaderId =$this->db->insertID();

                // Clean formatting numbers
                $cleanVal = static fn($val) => (float) preg_replace('/[^\d.]/', '', str_replace(',', '',$val ?? '0'));

                $detailData = [
                    'id_header' => $lastInsertedHeaderId,
                    '1'  => $cleanVal($postData['isi_1'][$row]),                     '2'  =>$cleanVal($postData['isi_2'][$row]),
                    '3'  => $cleanVal($postData['isi_3'][$row]),                     '4'  =>$cleanVal($postData['isi_4'][$row]),
                    '5'  => $cleanVal($postData['isi_5'][$row]),                     '6'  =>$cleanVal($postData['isi_6'][$row]),
                    '7'  => $cleanVal($postData['isi_7'][$row]),                     '8'  =>$cleanVal($postData['isi_8'][$row]),
                    '9'  => $cleanVal($postData['isi_9'][$row]),                     '10' =>$cleanVal($postData['isi_10'][$row]),
                    '11' => $cleanVal($postData['isi_11'][$row]),                     '12' =>$cleanVal($postData['isi_12'][$row]),
                    'total' => $cleanVal($postData['isi_tot'][$row]),
                ];

                $this->db->table('yp_plan__trans_capex_entry_detail')->insert($detailData);
            }
        }

        // 4. Save Depreciation & Summary Total if header exists
        if ($lastInsertedHeaderId) {$cleanVal = static fn($val) => (float) preg_replace('/[^\d.]/', '', str_replace(',', '',$val ?? '0'));

            // Depreciation Record
            $this->db->table('yp_plan__trans_capex_entry_depreciation')->insert([
                'id_header'     => $lastInsertedHeaderId,
                'id_coa'        => $idCoa,
                'main_account'  => $main,
                'dept_id'       => $dept,
                'year_code'     => $year,
                'master_amount' => $amtDep,
                '1'  => $cleanVal($postData['depre_1'] ?? 0),
                '2'  => $cleanVal($postData['depre_2'] ?? 0),
                '3'  => $cleanVal($postData['depre_3'] ?? 0),
                '4'  => $cleanVal($postData['depre_4'] ?? 0),
                '5'  => $cleanVal($postData['depre_5'] ?? 0),
                '6'  => $cleanVal($postData['depre_6'] ?? 0),
                '7'  => $cleanVal($postData['depre_7'] ?? 0),
                '8'  => $cleanVal($postData['depre_8'] ?? 0),
                '9'  => $cleanVal($postData['depre_9'] ?? 0),
                '10' => $cleanVal($postData['depre_10'] ?? 0),
                '11' => $cleanVal($postData['depre_11'] ?? 0),
                '12' => $cleanVal($postData['depre_12'] ?? 0),
                'total' => $cleanVal($postData['depre_13'] ?? 0)
            ]);

            // Entry Total Record
            $this->db->table('yp_plan__trans_capex_entry_total')->insert([
                'id_header'    => $lastInsertedHeaderId,
                'id_coa'       => $idCoa,
                'main_account' => $main,
                'dept_id'      => $dept,
                'year_code'    => $year,
                '1'  => $cleanVal($postData['total_1'] ?? 0),
                '2'  => $cleanVal($postData['total_2'] ?? 0),
                '3'  => $cleanVal($postData['total_3'] ?? 0),
                '4'  => $cleanVal($postData['total_4'] ?? 0),
                '5'  => $cleanVal($postData['total_5'] ?? 0),
                '6'  => $cleanVal($postData['total_6'] ?? 0),
                '7'  => $cleanVal($postData['total_7'] ?? 0),
                '8'  => $cleanVal($postData['total_8'] ?? 0),
                '9'  => $cleanVal($postData['total_9'] ?? 0),
                '10' => $cleanVal($postData['total_10'] ?? 0),
                '11' => $cleanVal($postData['total_11'] ?? 0),
                '12' => $cleanVal($postData['total_12'] ?? 0),
                'total' => $cleanVal($postData['total_13'] ?? 0)
            ]);
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /* ============================================================
     * Method tambahan untuk view TailAdmin (Phase wiring raw files)
     * ============================================================ */

    /**
     * Semua cost center aktif (untuk dropdown filter summary).
     */
    public function getAllCostCenters(): array
    {
        return $this->db->table('gw_plan__master_cost_center')
            ->select("cost_center, COALESCE(NULLIF(cost_center_sap,''), CAST(cost_center AS CHAR)) AS cc_code, cost_desc, cost_center_sap")
            ->where('status', 'A')
            ->orderBy('cost_center', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Summary View All — seluruh rincian transaksi CAPEX detail.
     */
    public function getSummaryViewAll(string $year, ?string $dept = null): array
    {
        $sql = "SELECT h.id,
                       h.item_desc AS description,
                       COALESCE(NULLIF(c.id_acct_ext,''), h.main_account, 0) AS account,
                       h.dept_id AS cost_center,
                       h.unit AS qty,
                       h.unit_price,
                       COALESCE(h.remarks, '') AS remarks,
                       IFNULL(d.`1`,0) AS jan, IFNULL(d.`2`,0) AS feb, IFNULL(d.`3`,0) AS mar,
                       IFNULL(d.`4`,0) AS apr, IFNULL(d.`5`,0) AS may, IFNULL(d.`6`,0) AS jun,
                       IFNULL(d.`7`,0) AS jul, IFNULL(d.`8`,0) AS aug, IFNULL(d.`9`,0) AS sep,
                       IFNULL(d.`10`,0) AS oct, IFNULL(d.`11`,0) AS nov, IFNULL(d.`12`,0) AS `dec`,
                       IFNULL(d.total, 0) AS total
                FROM yp_plan__trans_capex_entry_header h
                LEFT JOIN yp_plan__trans_capex_entry_detail d ON d.id_header = h.id
                LEFT JOIN gw_plan__master_coa c ON c.main_account = h.main_account
                WHERE h.year_code = ?";

        $params = [$year];

        if (! empty($dept)) {
            $sql .= ' AND h.dept_id = ?';
            $params[] = $dept;
        }

        $sql .= ' ORDER BY h.id DESC';

        try {
            return $this->db->query($sql, $params)->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Summary Acquisition — GROUP BY kategori aset (main_account).
     */
    public function getSummaryAcquisition(string $year, ?string $dept = null): array
    {
        $sql = "SELECT COALESCE(NULLIF(c.id_acct_ext,''), h.main_account, 0) AS category_code,
                       COALESCE(c.cost_center_desc, CONCAT('Asset ', h.main_account)) AS category_name,
                       IFNULL(SUM(d.`1`),0) AS jan, IFNULL(SUM(d.`2`),0) AS feb, IFNULL(SUM(d.`3`),0) AS mar,
                       IFNULL(SUM(d.`4`),0) AS apr, IFNULL(SUM(d.`5`),0) AS may, IFNULL(SUM(d.`6`),0) AS jun,
                       IFNULL(SUM(d.`7`),0) AS jul, IFNULL(SUM(d.`8`),0) AS aug, IFNULL(SUM(d.`9`),0) AS sep,
                       IFNULL(SUM(d.`10`),0) AS oct, IFNULL(SUM(d.`11`),0) AS nov, IFNULL(SUM(d.`12`),0) AS `dec`,
                       IFNULL(SUM(d.total), 0) AS total
                FROM yp_plan__trans_capex_entry_header h
                LEFT JOIN yp_plan__trans_capex_entry_detail d ON d.id_header = h.id
                LEFT JOIN gw_plan__master_coa c ON c.main_account = h.main_account
                WHERE h.year_code = ?";

        $params = [$year];

        if (! empty($dept)) {
            $sql .= ' AND h.dept_id = ?';
            $params[] = $dept;
        }

        $sql .= ' GROUP BY h.main_account, c.id_acct_ext, c.cost_center_desc
                  ORDER BY h.main_account ASC';

        try {
            return $this->db->query($sql, $params)->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Summary Depreciation — GROUP BY kategori aset, kalkulasi depresiasi.
     */
    public function getSummaryDepreciation(string $year, ?string $dept = null): array
    {
        $sql = "SELECT COALESCE(NULLIF(c.id_acct_ext,''), h.main_account, 0) AS category_code,
                       COALESCE(c.cost_center_desc, CONCAT('Asset ', h.main_account)) AS category_name,
                       IFNULL(SUM(dep.`1`),0) AS jan, IFNULL(SUM(dep.`2`),0) AS feb, IFNULL(SUM(dep.`3`),0) AS mar,
                       IFNULL(SUM(dep.`4`),0) AS apr, IFNULL(SUM(dep.`5`),0) AS may, IFNULL(SUM(dep.`6`),0) AS jun,
                       IFNULL(SUM(dep.`7`),0) AS jul, IFNULL(SUM(dep.`8`),0) AS aug, IFNULL(SUM(dep.`9`),0) AS sep,
                       IFNULL(SUM(dep.`10`),0) AS oct, IFNULL(SUM(dep.`11`),0) AS nov, IFNULL(SUM(dep.`12`),0) AS `dec`,
                       IFNULL(SUM(dep.total), 0) AS total
                FROM yp_plan__trans_capex_entry_header h
                LEFT JOIN yp_plan__trans_capex_entry_depreciation dep ON dep.id_header = h.id
                LEFT JOIN gw_plan__master_coa c ON c.main_account = h.main_account
                WHERE h.year_code = ?";

        $params = [$year];

        if (! empty($dept)) {
            $sql .= ' AND h.dept_id = ?';
            $params[] = $dept;
        }

        $sql .= ' GROUP BY h.main_account, c.id_acct_ext, c.cost_center_desc
                  ORDER BY h.main_account ASC';

        try {
            return $this->db->query($sql, $params)->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Master depresiasi (kategori aset) untuk dropdown Entry CAPEX.
     */
    public function getDepreciationMasters(): array
    {
        try {
            return $this->db->table('yp_plan__master_amount_depreciation a')
                ->select("a.id, a.main_account, COALESCE(NULLIF(c.id_acct_ext,''), c.main_account, 0) AS acct_code, a.amount")
                ->join('gw_plan__master_coa c', 'c.main_account = a.main_account', 'left')
                ->orderBy('a.main_account', 'ASC')
                ->get()->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Kategori aset CAPEX (master depresiasi + nama friendly dari master_coa).
     * Dipakai untuk baris tabel tab_entry_capex & title modal form.
     */
    public function getCapexCategories(): array
    {
        $sql = "SELECT a.main_account,
                       COALESCE(NULLIF(REPLACE(MIN(c.cost_center_desc), 'Depreciation - ', ''), ''),
                                CONCAT('Asset ', a.main_account)) AS category_name
                FROM yp_plan__master_amount_depreciation a
                LEFT JOIN gw_plan__master_coa c
                       ON c.main_account = a.main_account AND c.status = 'A'
                GROUP BY a.main_account
                ORDER BY a.main_account ASC";
        try {
            return $this->db->query($sql)->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Simpan form CAPEX (modal) — header + detail + depresiasi + total per baris.
     * Data lama untuk (tahun, main_account, dept) dihapus lalu di-insert ulang
     * agar re-save kategori yang sama tidak menumpuk.
     */
    public function saveCapexFormData(array $rows, string $mainAccount, string $dept, string $year, $user): bool
    {
        $this->db->transStart();

        // Useful life (tahun) dari master depresiasi untuk kalkulasi depresiasi
        $master = $this->db->table('yp_plan__master_amount_depreciation')
            ->where('main_account', $mainAccount)->get()->getRowArray();
        $lifeYears = $master ? (int) $master['amount'] : 0;

        // id_coa = id_cost_center dari master_coa tipe CAPEX
        $coa = $this->db->table('gw_plan__master_coa')
            ->where('main_account', $mainAccount)
            ->where('type', 'CAPEX')
            ->where('status', 'A')
            ->get()->getRowArray();
        $idCoa = $coa ? (int) $coa['id_cost_center'] : 0;

        // 1. Hapus entry lama untuk kombinasi ini
        $this->db->table('yp_plan__trans_capex_entry_header')
            ->where(['year_code' => $year, 'main_account' => $mainAccount, 'dept_id' => $dept])
            ->delete();

        // Bersihkan orphan detail/depresiasi/total (header sudah tidak ada)
        $this->db->query("DELETE d FROM yp_plan__trans_capex_entry_detail d
                          LEFT JOIN yp_plan__trans_capex_entry_header h ON h.id = d.id_header
                          WHERE h.id IS NULL");
        $this->db->query("DELETE dep FROM yp_plan__trans_capex_entry_depreciation dep
                          LEFT JOIN yp_plan__trans_capex_entry_header h ON h.id = dep.id_header
                          WHERE h.id IS NULL");
        $this->db->query("DELETE t FROM yp_plan__trans_capex_entry_total t
                          LEFT JOIN yp_plan__trans_capex_entry_header h ON h.id = t.id_header
                          WHERE h.id IS NULL");

        $monthNames = [1 => 'jan', 2 => 'feb', 3 => 'mar', 4 => 'apr', 5 => 'may', 6 => 'jun',
                       7 => 'jul', 8 => 'aug', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dec'];
        $any = false;

        foreach ($rows as $r) {
            $desc = trim((string) ($r['description'] ?? ''));
            if ($desc === '') continue;

            $headerData = [
                'main_account' => (int) $mainAccount,
                'id_coa'       => $idCoa,
                'dept_id'      => (int) $dept,
                'newlines'     => (($r['newLines'] ?? 'Tidak') === 'Ya') ? 1 : 0,
                'item_desc'    => $desc,
                'cost_center'  => (int) ($r['costCenter'] ?? 0),
                'unit'         => (int) ($r['qty'] ?? 0),
                'unit_price'   => (float) ($r['unitPrice'] ?? 0),
                'remarks'      => (string) ($r['remarks'] ?? ''),
                'year_code'    => (int) $year,
            ];
            $this->db->table('yp_plan__trans_capex_entry_header')->insert($headerData);
            $headerId = $this->db->insertID();

            // Nilai per bulan (acquisition period)
            $vals = [];
            $total = 0.0;
            foreach ($monthNames as $m) {
                $v = (float) ($r[$m] ?? 0);
                $vals[] = $v;
                $total += $v;
            }

            // 2. Detail (acquisition per bulan)
            $this->db->query(
                "INSERT INTO yp_plan__trans_capex_entry_detail
                 (id_header, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, total)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                array_merge([$headerId], $vals, [round($total, 4)])
            );

            // 3. Depresiasi straight-line: aset bulan k didepresiasi mulai bulan k
            $depVals = [];
            $depTotal = 0.0;
            if ($lifeYears > 0) {
                for ($m = 1; $m <= 12; $m++) {
                    $dep = 0.0;
                    for ($k = 1; $k <= $m; $k++) {
                        if (($vals[$k - 1] ?? 0) > 0) {
                            $dep += $vals[$k - 1] / ($lifeYears * 12);
                        }
                    }
                    $depVals[] = round($dep, 4);
                    $depTotal += $dep;
                }
            } else {
                $depVals = array_fill(0, 12, 0.0);
            }
            $this->db->query(
                "INSERT INTO yp_plan__trans_capex_entry_depreciation
                 (id_header, id_coa, main_account, dept_id, year_code, master_amount,
                  `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, total)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                array_merge([$headerId, $idCoa, (int) $mainAccount, (int) $dept, (int) $year, $lifeYears],
                    $depVals, [round($depTotal, 4)])
            );

            // 4. Total (mirror detail acquisition)
            $this->db->query(
                "INSERT INTO yp_plan__trans_capex_entry_total
                 (id_header, id_coa, main_account, dept_id, year_code,
                  `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, total)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                array_merge([$headerId, $idCoa, (int) $mainAccount, (int) $dept, (int) $year],
                    $vals, [round($total, 4)])
            );

            $any = true;
        }

        $this->db->transComplete();
        return $any && $this->db->transStatus();
    }

    /**
     * Total per kategori aset (main_account) untuk tab_entry_capex,
     * difilter cost center (dept_id) + tahun.
     */
    public function getEntryDataByCategory(string $year, string $dept): array
    {
        $sql = "SELECT h.main_account AS category_code,
                       IFNULL(SUM(d.`1`),0) AS jan, IFNULL(SUM(d.`2`),0) AS feb,
                       IFNULL(SUM(d.`3`),0) AS mar, IFNULL(SUM(d.`4`),0) AS apr,
                       IFNULL(SUM(d.`5`),0) AS may, IFNULL(SUM(d.`6`),0) AS jun,
                       IFNULL(SUM(d.`7`),0) AS jul, IFNULL(SUM(d.`8`),0) AS aug,
                       IFNULL(SUM(d.`9`),0) AS sep, IFNULL(SUM(d.`10`),0) AS oct,
                       IFNULL(SUM(d.`11`),0) AS nov, IFNULL(SUM(d.`12`),0) AS `dec`,
                       IFNULL(SUM(d.total),0) AS total
                FROM yp_plan__trans_capex_entry_header h
                LEFT JOIN yp_plan__trans_capex_entry_detail d ON d.id_header = h.id
                WHERE h.year_code = ? AND h.dept_id = ?
                GROUP BY h.main_account
                ORDER BY h.main_account ASC";
        try {
            return $this->db->query($sql, [$year, $dept])->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Daftar item CAPEX yang sudah di-entry pada tahun tertentu.
     * Mapping ke kontrak view TailAdmin (asset_description, category_name, dll).
     */
    public function getCapexItems(string $year, ?int $offset = null, ?int $perPage = null): array
    {
        $sql = "SELECT h.id,
                       h.item_desc AS asset_description,
                       COALESCE(c.cost_center_desc, CONCAT('MA ', h.main_account)) AS category_name,
                       COALESCE(NULLIF(c.id_acct_ext,''), h.main_account, 0) AS main_account,
                       h.unit AS acquisition_month,
                       COALESCE(CAST(h.remarks AS UNSIGNED), 0) AS useful_life_years,
                       h.unit_price AS acquisition_cost,
                       IFNULL(d.total, 0) AS monthly_depreciation,
                       h.dept_id, h.cost_center
                FROM yp_plan__trans_capex_entry_header h
                LEFT JOIN yp_plan__trans_capex_entry_depreciation d ON d.id_header = h.id
                LEFT JOIN gw_plan__master_coa c ON c.main_account = h.main_account
                WHERE h.year_code = ?
                ORDER BY h.id DESC";

        if ($perPage !== null) {
            $sql .= ' LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset;
        }

        try {
            return $this->db->query($sql, [$year])->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function countCapexItems(string $year): int
    {
        $sql = "SELECT COUNT(*) AS total FROM yp_plan__trans_capex_entry_header h WHERE h.year_code = ?";
        try {
            $row = $this->db->query($sql, [$year])->getRowArray();
            return (int) ($row['total'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Laporan per Departemen (Total Item, Nilai Akuisisi, Depresiasi/Tahun).
     */
    public function getDeptReports(string $year): array
    {
        $sql = "SELECT COALESCE(dp.dept_desc, '') AS department_name,
                       COUNT(h.id) AS total_items,
                       IFNULL(SUM(h.unit_price),0) AS total_acquisition,
                       IFNULL(SUM(d.total),0) AS annual_depreciation
                FROM yp_plan__trans_capex_entry_header h
                LEFT JOIN gw_plan__master_department dp ON dp.dept_code = h.dept_id
                LEFT JOIN yp_plan__trans_capex_entry_depreciation d ON d.id_header = h.id
                WHERE h.year_code = ?
                GROUP BY dp.dept_desc
                ORDER BY dp.dept_desc";
        try {
            return $this->db->query($sql, [$year])->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Konsolidasi alokasi depresiasi ke akun OPEX/FOH (Jan-Jun & Jul-Des).
     */
    public function getTotalOpexSync(string $year): array
    {
        $sql = "SELECT COALESCE(c.cost_center_desc, d.id_coa) AS account_name,
                       IFNULL(SUM(d.`1`)+SUM(d.`2`)+SUM(d.`3`)+SUM(d.`4`)+SUM(d.`5`)+SUM(d.`6`),0) AS h1_amount,
                       IFNULL(SUM(d.`7`)+SUM(d.`8`)+SUM(d.`9`)+SUM(d.`10`)+SUM(d.`11`)+SUM(d.`12`),0) AS h2_amount,
                       IFNULL(SUM(d.total),0) AS total_amount
                FROM yp_plan__trans_capex_entry_depreciation d
                LEFT JOIN gw_plan__master_coa c ON c.main_account = d.id_coa
                WHERE d.year_code = ?
                GROUP BY c.cost_center_desc, d.id_coa
                ORDER BY d.id_coa";
        try {
            return $this->db->query($sql, [$year])->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Summary Eksekutif CAPEX (Total Anggaran, Unit Aset, Beban Depresiasi).
     */
    public function getSummaryData(string $year): array
    {
        try {
            $totalCapex = $this->db->table('yp_plan__trans_capex_entry_header')
                ->selectSum('unit_price', 'total')
                ->where('year_code', $year)
                ->get()->getRowArray();
            $totalUnits = $this->db->table('yp_plan__trans_capex_entry_header')
                ->selectSum('unit', 'total')
                ->where('year_code', $year)
                ->get()->getRowArray();
            $totalDep = $this->db->table('yp_plan__trans_capex_entry_depreciation')
                ->selectSum('total', 'total')
                ->where('year_code', $year)
                ->get()->getRowArray();

            return [
                'total_capex'       => (float) ($totalCapex['total'] ?? 0),
                'total_units'       => (float) ($totalUnits['total'] ?? 0),
                'total_depreciation'=> (float) ($totalDep['total'] ?? 0),
            ];
        } catch (\Throwable $e) {
            return ['total_capex' => 0, 'total_units' => 0, 'total_depreciation' => 0];
        }
    }

    /**
     * Simpan 1 aset CAPEX dari form TailAdmin (asset_description, category_id,
     * acquisition_month, acquisition_cost, useful_life_years, monthly_depreciation).
     */
    public function saveAssetCapex(array $post, string $year, $user): bool
    {
        $this->db->transStart();

        $categoryId  = (int) ($post['category_id'] ?? 0);
        $desc        = trim((string) ($post['asset_description'] ?? ''));
        $acqMonth    = max(1, min(12, (int) ($post['acquisition_month'] ?? 1)));
        $cost        = (float) ($post['acquisition_cost'] ?? 0);
        $lifeYears   = max(1, (int) ($post['useful_life_years'] ?? 4));
        $monthlyDep  = (float) ($post['monthly_depreciation'] ?? 0);
        $deptId      = trim((string) ($post['dept_id'] ?? ''));

        $mainAccount = (int) ($post['main_account'] ?? $categoryId);

        $this->db->table('yp_plan__trans_capex_entry_header')->insert([
            'main_account' => $mainAccount,
            'id_coa'       => $categoryId,
            'dept_id'      => $deptId,
            'newlines'     => 0,
            'item_desc'    => $desc,
            'cost_center'  => (int) ($post['cost_center'] ?? 0),
            'unit'         => 1,
            'unit_price'   => $cost,
            'remarks'      => $lifeYears . ' tahun',
            'year_code'    => $year,
        ]);
        $headerId = $this->db->insertID();

        if (! $monthlyDep && $cost > 0 && $lifeYears > 0) {
            $monthlyDep = round($cost / ($lifeYears * 12), 2);
        }

        $total = 0;
        $vals  = [];
        for ($m = 1; $m <= 12; $m++) {
            $val = ($m >= $acqMonth) ? $monthlyDep : 0;
            $vals[] = $val;
            $total += $val;
        }

        // Kolom bulan bernomor 1..12 di-escape backtick (numeric identifier)
        $sqlDet = 'INSERT INTO yp_plan__trans_capex_entry_detail
                   (id_header, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, total)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $this->db->query($sqlDet, array_merge([$headerId], $vals, [$total]));

        $depVals = [];
        for ($m = 1; $m <= 12; $m++) {
            $depVals[] = ($m >= $acqMonth) ? $monthlyDep : 0;
        }
        $sqlDep = 'INSERT INTO yp_plan__trans_capex_entry_depreciation
                   (id_header, id_coa, main_account, dept_id, year_code, master_amount, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, total)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $this->db->query($sqlDep, array_merge([$headerId, $categoryId, $mainAccount, $deptId, $year, $lifeYears], $depVals, [$total]));

        $sqlTot = 'INSERT INTO yp_plan__trans_capex_entry_total
                   (id_header, id_coa, main_account, dept_id, year_code, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, total)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $this->db->query($sqlTot, array_merge([$headerId, $categoryId, $mainAccount, $deptId, $year], $depVals, [$total]));

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Sync depresiasi CAPEX ke OPEX Engine (yp_plan__trans_budget_entry_data).
     */
    public function syncToOpex(string $year): bool
    {
        // Kolom source tidak ada di skema legacy → sinkronisasi di-nonaktifkan
        // sementara (keputusan user 6 Agt 2026: jangan ubah struktur DB).
        if (! \App\Libraries\DbCompat::hasEntrySource()) {
            log_message('warning', 'CapexModel::syncToOpex dinonaktifkan — kolom source belum tersedia di skema DB legacy.');

            return false;
        }

        $this->db->transStart();

        $rows = $this->db->table('yp_plan__trans_capex_entry_depreciation')
            ->where('year_code', $year)
            ->get()->getResultArray();

        if (empty($rows)) {
            $this->db->transComplete();
            return $this->db->transStatus();
        }

        $userId = (int) (session()->get('user_id') ?? 0);
        $batch  = [];
        $coas   = [];
        foreach ($rows as $r) {
            $coas[] = (int) $r['id_coa'];
        }
        $coas = array_values(array_unique($coas));

        $this->db->table('yp_plan__trans_budget_entry_data')
            ->where('year_code', $year)
            ->where('source', 'CAPEX')
            ->whereIn('id_coa', $coas)
            ->delete();

        $sql = 'INSERT INTO yp_plan__trans_budget_entry_data
                (id_coa, id_dept, total, year_code, created_by, created_date, source, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        foreach ($rows as $r) {
            $vals = [];
            for ($m = 1; $m <= 12; $m++) {
                $vals[] = (float) $r[(string) $m];
            }
            $this->db->query($sql, array_merge([
                (int) $r['id_coa'],
                (int) ($r['dept_id'] ?: 0),
                (float) $r['total'],
                $year,
                $userId,
                date('Y-m-d H:i:s'),
                'CAPEX',
            ], $vals));
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }
}