<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Sales extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * 1. Sales Summary & Discount Allocation Page
     */
    public function index()
    {
        $workingYear = session('working_year') ?? date('Y');

        $data = [
            'title'       => 'Sales Summary & Discount Reclass',
            'workingYear' => $workingYear,
            // Data summary diambil dari model / query database
            'summary'     => [] 
        ];

        return view('sales/summary', $data);
    }

    /**
     * 2. Entry Sales Domestic Page
     */
    public function entryDomestic()
    {
        $workingYear = session('working_year') ?? date('Y');

        $data = [
            'title'       => 'Entry Sales Domestic',
            'workingYear' => $workingYear,
            'salesData'   => []
        ];

        return view('sales/entry_domestic', $data);
    }

    /**
     * 3. Entry Sales Export Page
     */
    public function entryExport()
    {
        $workingYear = session('working_year') ?? date('Y');

        $data = [
            'title'       => 'Entry Sales Export',
            'workingYear' => $workingYear,
            'salesData'   => []
        ];

        return view('sales/entry_export', $data);
    }

    /**
     * 4. Sales Simulation Page (Revenue & Volume)
     */
    public function simulation()
    {
        $workingYear = session('working_year') ?? date('Y');

        $data = [
            'title'       => 'Sales Simulation',
            'workingYear' => $workingYear,
            'revData'     => [],
            'volData'     => []
        ];

        return view('sales/simulation', $data);
    }

    /**
     * 5. Setup Target & Showcase Page
     */
    public function setupTarget()
    {
        $workingYear = session('working_year') ?? date('Y');

        $data = [
            'title'        => 'Target & Showcase Setup',
            'workingYear'  => $workingYear,
            'targetData'   => [],
            'showcaseData' => []
        ];

        return view('sales/setup_target', $data);
    }

    /**
     * AJAX Actions & Form Processing
     */
    public function saveDiscountReclass()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Invalid method']);
        }

        $postData = $this->request->getJSON(true);
        // Logika simpan alokasi/reclass diskon ke database
        
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Discount reclassification saved successfully!'
        ]);
    }

    public function processUpload()
    {
        $type = $this->request->getPost('upload_type'); // 'domestic' or 'export'
        $file = $this->request->getFile('excel_file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Logika parsing Excel via PhpSpreadsheet
            return redirect()->back()->with('success', 'Data uploaded successfully!');
        }

        return redirect()->back()->with('error', 'Failed to upload file.');
    }

    public function exportExcel()
    {
        // Engine export Excel berbasis PhpSpreadsheet (menggantikan PHPExcel legacy)
        // Menghasilkan output stream langsung ke browser tanpa view terpisah
    }
}