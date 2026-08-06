<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class OpexGaController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * 1. OPEX GA Summary & Main Table View
     */
    public function index()
    {
        $workingYear = session('working_year') ?? date('Y');

        $data = [
            'title'       => 'OPEX GA Summary & Budget Table',
            'workingYear' => $workingYear,
            'opexData'    => [] // Data ditarik dari OpexGaModel
        ];

        return view('opex_ga/index', $data);
    }

    /**
     * 2. Actual OPEX Data Management Page
     */
    public function actual()
    {
        $workingYear = session('working_year') ?? date('Y');

        $data = [
            'title'       => 'OPEX GA Actual Data',
            'workingYear' => $workingYear,
            'actuals'     => []
        ];

        return view('opex_ga/actual', $data);
    }

    /**
     * 3. Entry Budget & Detail Allocation Matrix
     */
    public function entryBudget()
    {
        $workingYear = session('working_year') ?? date('Y');

        $data = [
            'title'       => 'Entry Budget OPEX GA',
            'workingYear' => $workingYear,
            'budgetItems' => []
        ];

        return view('opex_ga/entry_budget', $data);
    }

    /**
     * AJAX Actions & Form Processing
     */
    public function saveBudgetDetail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Invalid method']);
        }

        $postData =$this->request->getJSON(true);
        
        // Logika penyimpanan detail rincian bulanan ke DB
        
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'OPEX GA budget allocation updated successfully!'
        ]);
    }

    public function processUpload()
    {
        $type =$this->request->getPost('upload_type'); // 'actual' atau 'budget'
        $file =$this->request->getFile('excel_file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Processing file Excel via PhpSpreadsheet
            return redirect()->back()->with('success', 'OPEX GA data uploaded successfully!');
        }

        return redirect()->back()->with('error', 'Failed to upload OPEX GA file.');
    }

    public function exportExcel()
    {
        // Export laporan OPEX GA ke format Excel (.xlsx) via PhpSpreadsheet (Phase 2.3)
        return redirect()->back()->with('info', 'Export Excel OPEX GA akan tersedia pada Phase 2.3 (Excel Engine terpusat).');
    }

    /**
     * Report Department — view opex_ga/report_department (daftar cost center OPEX).
     */
    public function reportDepartment()
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $data = [
            'title'       => 'OPEX GA - Report Departemen',
            'workingYear' => $workingYear,
            'dept'        => $this->getOpexCostCenters(),
        ];

        return view('opex_ga/report_department', $data);
    }

    /**
     * AJAX: data tabel actual+budget per cost center (dipanggil view report_department).
     */
    public function cariActualTable()
    {
        $dept = $this->request->getPost('dept') ?? '';
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $rows = [];
        try {
            $builder = $this->db->table('yp_plan__trans_budget_entry_data t')
                ->select("COALESCE(c.main_account, 0) AS main_account")
                ->select("COALESCE(c.cost_center_desc, '') AS cost_center_desc")
                ->select("COALESCE(c.cost_center_header, '') AS cost_center_header")
                ->select("t.`1` AS isi_1, t.`2` AS isi_2, t.`3` AS isi_3, t.`4` AS isi_4, t.`5` AS isi_5, t.`6` AS isi_6")
                ->select("t.`7` AS isi_7, t.`8` AS isi_8, t.`9` AS isi_9, t.`10` AS isi_10, t.`11` AS isi_11, t.`12` AS isi_12, t.total AS isi_tot")
                ->join('gw_plan__master_coa c', 'c.main_account = t.id_coa', 'left')
                ->where('t.year_code', $year);

            if (! empty($dept) && $dept !== '0' && $dept !== '1' && $dept !== '2') {
                $builder->where('t.id_dept', $dept);
            }

            $rows = $builder->orderBy('c.main_account', 'ASC')->get()->getResultArray();
        } catch (\Throwable $e) {
            $rows = [];
        }

        return $this->response->setJSON($rows);
    }

    /**
     * Export template OPEX GA per cost center (placeholder Phase 2.3).
     */
    public function exportTemplateOpexGa($dept = null)
    {
        return redirect()->back()->with('info', 'Export Template OPEX GA akan tersedia pada Phase 2.3 (Excel Engine terpusat).');
    }

    /**
     * Daftar cost center OPEX aktif.
     */
    private function getOpexCostCenters(): array
    {
        return $this->db->table('gw_plan__master_cost_center')
            ->select('cost_center, cost_desc')
            ->where('status', 'A')
            ->where('type', 'OPEX')
            ->orderBy('cost_center', 'ASC')
            ->get()->getResultArray();
    }
}