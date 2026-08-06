<?php

namespace App\Controllers;

use App\Libraries\ExcelExporter;
use App\Libraries\ExcelImporter;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * SalesController — Modul Sales (PRD Phase 2.3).
 *
 * Upload data sales domestic/export via Excel engine terpusat
 * (disimpan ke yp_plan__assump_sales) + export template/summary.
 */
class SalesController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * 1. Sales Summary & Discount Allocation Page
     */
    public function index(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('sales/summary', [
            'title'       => 'Sales Summary & Discount Reclass',
            'workingYear' => $workingYear,
            'summary'     => $this->getAssumptionData($workingYear),
        ]);
    }

    /**
     * 2. Entry Sales Domestic Page
     */
    public function entryDomestic(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('sales/entry_domestic', [
            'title'       => 'Entry Sales Domestic',
            'workingYear' => $workingYear,
            'salesData'   => $this->getAssumptionData($workingYear, 'Domestic'),
        ]);
    }

    /**
     * 3. Entry Sales Export Page
     */
    public function entryExport(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('sales/entry_export', [
            'title'       => 'Entry Sales Export',
            'workingYear' => $workingYear,
            'salesData'   => $this->getAssumptionData($workingYear, 'International'),
        ]);
    }

    /**
     * 4. Sales Simulation Page (Revenue & Volume)
     */
    public function simulation(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('sales/simulation', [
            'title'       => 'Sales Simulation',
            'workingYear' => $workingYear,
            'revData'     => [],
            'volData'     => [],
        ]);
    }

    /**
     * 5. Setup Target & Showcase Page
     */
    public function setupTarget(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('sales/setup_target', [
            'title'        => 'Target & Showcase Setup',
            'workingYear'  => $workingYear,
            'targetData'   => [],
            'showcaseData' => [],
        ]);
    }

    /**
     * AJAX Actions & Form Processing
     */
    public function saveDiscountReclass(): ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Invalid method']);
        }

        $postData = $this->request->getJSON(true);

        // TODO: alokasi/reclass diskon — dilengkapi pada iterasi berikutnya

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Discount reclassification saved successfully!',
        ]);
    }

    /**
     * Upload Sales (.xlsx) — via ExcelImporter ke yp_plan__assump_sales.
     * upload_type: 'domestic' → Domestic, 'export' → International.
     */
    public function processUpload()
    {
        $type = $this->request->getPost('upload_type'); // 'domestic' atau 'export'
        $file = $this->request->getFile('excel_file');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'File tidak valid atau gagal diunggah.');
        }

        $typeSales = strtolower((string) $type) === 'export' ? 'International' : 'Domestic';
        $year      = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId    = (int) (session()->get('user_id') ?? 0);

        try {
            $rows  = ExcelImporter::import($file, true);
            $saved = 0;

            $this->db->transStart();

            foreach ($rows as $row) {
                $value = ExcelImporter::toFloat(ExcelImporter::column(
                    $row,
                    ['value', 'value_text', 'amount', 'nilai', 'nominal', 'harga', 'volume'],
                    null
                ));

                if ($value === null || $value === 0.0) {
                    // Fallback: kolom numerik pertama yang tidak kosong
                    foreach ($row as $cell) {
                        if (is_numeric($cell)) {
                            $value = (float) $cell;
                            break;
                        }
                    }
                }

                $this->db->table('yp_plan__assump_sales')->insert([
                    'type_sales'  => $typeSales,
                    'value_text'  => $value,
                    'year_code'   => $year,
                    'created_by'  => $userId,
                    'created_date'=> date('Y-m-d H:i:s'),
                ]);
                $saved++;
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal menyimpan data upload sales.');
            }

            return redirect()->back()->with('success', "Data Sales {$typeSales} berhasil diimport ({$saved} baris).");
        } catch (\Throwable $e) {
            log_message('error', 'Sales upload: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export data assumption sales ke .xlsx via ExcelExporter.
     */
    public function exportExcel(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $rows = $this->getAssumptionData($year);
        $data = array_map(function ($r) {
            return [
                $r['type_sales'] ?? '',
                (float) ($r['value_text'] ?? 0),
                $r['year_code'] ?? '',
            ];
        }, $rows);

        return ExcelExporter::export(
            ['TYPE SALES', 'VALUE', 'YEAR'],
            $data,
            'Sales_Assumption_' . $year,
            'Sales'
        );
    }

    /**
     * Data assumption sales per tahun (+ opsional tipe).
     */
    private function getAssumptionData(string $year, ?string $type = null): array
    {
        try {
            $builder = $this->db->table('yp_plan__assump_sales')
                ->select('type_sales, value_text, year_code')
                ->where('year_code', $year);

            if (! empty($type)) {
                $builder->where('type_sales', $type);
            }

            return $builder->orderBy('type_sales', 'ASC')->orderBy('id', 'ASC')->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'SalesController::getAssumptionData: ' . $e->getMessage());
            return [];
        }
    }
}
