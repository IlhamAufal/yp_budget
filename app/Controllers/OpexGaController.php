<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class OpexGa extends BaseController
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
        // Export laporan OPEX GA ke format Excel (.xlsx) via PhpSpreadsheet
    }
}