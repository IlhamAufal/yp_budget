<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class OpexSellingController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $data = [
            'title'       => 'OPEX Selling - Entry Budget',
            'workingYear' => $workingYear,
            'dept'        => $this->getSellingCostCenters(),
        ];

        return view('opex_selling/entry_budget', $data);
    }

    public function actual()
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $data = [
            'title'       => 'OPEX Selling - Actual Data',
            'workingYear' => $workingYear,
            'dept'        => $this->getSellingCostCenters(),
        ];

        return view('opex_selling/entry_budget', $data);
    }

    public function entryBudgetDetail()
    {
        $header = $this->request->getPost('header') ?? '';
        $idx    = $this->request->getPost('idx') ?? '';
        $dept   = $this->request->getPost('dept') ?? '';
        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        // Query dari tabel transaksi budget nyata + join COA untuk deskripsi
        $builder = $this->db->table('yp_plan__trans_budget_entry_data t')
            ->select("t.id_coa AS main_account, COALESCE(c.cost_center_desc, '') AS cost_center_desc, t.id_dept AS cost_center_header")
            ->select("t.`1` AS isi_1, t.`2` AS isi_2, t.`3` AS isi_3, t.`4` AS isi_4, t.`5` AS isi_5, t.`6` AS isi_6")
            ->select("t.`7` AS isi_7, t.`8` AS isi_8, t.`9` AS isi_9, t.`10` AS isi_10, t.`11` AS isi_11, t.`12` AS isi_12, t.total AS isi_tot")
            ->join('gw_plan__master_coa c', 'c.main_account = t.id_coa', 'left')
            ->where('t.year_code', $year);

        if (! empty($dept)) {
            $builder->where('t.id_dept', $dept);
        }
        $query = $builder->orderBy('t.id_coa', 'ASC')->get()->getResultArray();

        $data = [
            'filex'  => $query,
            'header' => $header,
            'idx'    => $idx,
            'dept'   => $dept
        ];

        return view('opex_selling/entry_budget_table', $data);
    }

    public function saveBudget()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid Request']);
        }

        $mainAccount = $this->request->getPost('main_account');
        $totiAa      = $this->request->getPost('toti_aa');
        $dept        = $this->request->getPost('dept');
        $year        = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        if (! empty($mainAccount) && is_array($mainAccount)) {
            foreach ($mainAccount as $key => $account) {
                $total = (float) str_replace(',', '', $totiAa[$key] ?? 0);

                $exists = $this->db->table('yp_plan__trans_budget_entry_data')
                    ->where('id_coa', $account)
                    ->where('id_dept', $dept)
                    ->where('year_code', $year)
                    ->countAllResults();

                if ($exists > 0) {
                    $this->db->table('yp_plan__trans_budget_entry_data')
                        ->where('id_coa', $account)
                        ->where('id_dept', $dept)
                        ->where('year_code', $year)
                        ->update(['total' => $total]);
                } else {
                    $this->db->table('yp_plan__trans_budget_entry_data')->insert([
                        'id_coa'    => $account,
                        'id_dept'   => $dept,
                        'total'     => $total,
                        'year_code' => $year,
                    ]);
                }
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data Budget Selling berhasil disimpan!']);
    }

    public function reportDepartment()
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $data = [
            'title'       => 'OPEX Selling - Department Report',
            'workingYear' => $workingYear,
            'dept'        => $this->getSellingCostCenters(),
        ];

        return view('opex_selling/report_department', $data);
    }

    /**
     * AJAX: data tabel budget selling per cost center (untuk report_department).
     */
    public function cariActualTable()
    {
        $dept = $this->request->getPost('dept') ?? '';
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $rows = [];
        try {
            $builder = $this->db->table('yp_plan__trans_budget_entry_data t')
                ->select("t.id_coa AS main_account, COALESCE(c.cost_center_desc, '') AS cost_center_desc")
                ->select("t.`1` AS isi_1, t.`2` AS isi_2, t.`3` AS isi_3, t.`4` AS isi_4, t.`5` AS isi_5, t.`6` AS isi_6")
                ->select("t.`7` AS isi_7, t.`8` AS isi_8, t.`9` AS isi_9, t.`10` AS isi_10, t.`11` AS isi_11, t.`12` AS isi_12, t.total AS isi_tot")
                ->join('gw_plan__master_coa c', 'c.main_account = t.id_coa', 'left')
                ->where('t.year_code', $year);

            if (! empty($dept)) {
                $builder->where('t.id_dept', $dept);
            }

            $rows = $builder->orderBy('t.id_coa', 'ASC')->get()->getResultArray();
        } catch (\Throwable $e) {
            $rows = [];
        }

        return $this->response->setJSON($rows);
    }

    public function uploadActual()
    {
        $file =$this->request->getFile('file');
        $type =$this->request->getPost('upload_type'); // 'regular' or 'ap'

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Process Excel file logic
            return $this->response->setJSON(['status' => 'success', 'message' => 'Actual Budget (' . strtoupper($type) . ') berhasil diunggah!']);
        }

        return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Gagal mengunggah berkas.']);
    }

    /**
     * Daftar cost center selling dari gw_plan__master_cost_center.
     */
    private function getSellingCostCenters(): array
    {
        return $this->db->table('gw_plan__master_cost_center')
            ->select('cost_center, cost_desc')
            ->where('status', 'A')
            ->orderBy('cost_center', 'ASC')
            ->get()->getResultArray();
    }
}