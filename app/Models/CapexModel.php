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
        $validIdsSubquery =$this->db->table('yp_plan__trans_capex_entry_header')->select('id');
        
        $this->db->table('yp_plan__trans_capex_entry_detail')->whereNotIn('id_header',$validIdsSubquery)->delete();
        $this->db->table('yp_plan__trans_capex_entry_depreciation')->whereNotIn('id_header',$validIdsSubquery)->delete();
        $this->db->table('yp_plan__trans_capex_entry_total')->whereNotIn('id_header',$validIdsSubquery)->delete();

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
}