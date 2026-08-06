<?php

namespace App\Controllers;

use App\Libraries\ExcelExporter;
use App\Libraries\ExcelImporter;
use App\Libraries\AuditLog;
use App\Models\AssumptionModel;
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
            'summary'     => $this->getSalesSummary($workingYear),
            'discount'    => $this->getDiscountReclass($workingYear),
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
     *
     * Basis data berasal dari Master Assumption (Phase 3):
     *   - KURS (USD/EUR) dari yp_plan__master_assumption
     *   - Volume & ASP dari yp_plan__master_assumption_sales_domestic / _export
     * Simulasi (what-if) dihitung client-side via Alpine.js.
     */
    public function simulation(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $assumption  = new AssumptionModel();
        $yearInt     = (int) $workingYear;

        return view('sales/simulation', [
            'title'          => 'Sales Simulation',
            'workingYear'    => $workingYear,
            'kurs'           => $assumption->getKurs($yearInt),
            'domesticAssump' => $assumption->getSalesDomestic($yearInt),
            'exportAssump'   => $assumption->getSalesExport($yearInt),
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

        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId = (int) (session()->get('user_id') ?? 0);

        // View mengirim via ypFetch (application/x-www-form-urlencoded) — pakai getPost,
        // bukan getJSON (yang mem-parsing php://input sebagai JSON dan akan selalu kosong).
        $post = $this->request->getPost() ?? [];

        // Bulan 1..12 (default 0)
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $values = [];
        $total  = 0;
        foreach ($months as $i => $mk) {
            $val = (float) ($post[$mk] ?? 0);
            $values[(string) ($i + 1)] = $val;
            $total += $val;
        }

        try {
            $this->db->transStart();

            // Hapus baris reclass existing utk tahun ini (mode replace)
            $this->db->table('yp_plan__master_reclass_monthly')
                ->where('year_code', $year)
                ->delete();

            $sql = 'INSERT INTO yp_plan__master_reclass_monthly
                    (id_coa, id_dept, total, notes, year_code, created_by, created_date,
                     `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

            $this->db->query($sql, array_merge([
                null,                     // id_coa — reclass global (bukan per akun)
                null,                     // id_dept
                $total,
                'DISCOUNT RECLASS',
                (int) $year,
                $userId,
                date('Y-m-d H:i:s'),
            ], array_values($values)));

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan alokasi diskon.']);
            }

            AuditLog::saved('sales/saveDiscountReclass', "Discount reclass tahun {$year} disimpan (total {$total})");

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Alokasi diskon berhasil disimpan.',
                'total'   => $total,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'SalesController::saveDiscountReclass: ' . $e->getMessage());

            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan alokasi diskon: ' . $e->getMessage()]);
        }
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

            AuditLog::log('UPLOAD', 'sales/processUpload', "Upload sales {$typeSales} tahun {$year} ({$saved} baris)");

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

        AuditLog::log('EXPORT', 'sales/exportExcel', "Export data sales assumption {$year} ke Excel");

        return ExcelExporter::export(
            ['TYPE SALES', 'VALUE', 'YEAR'],
            $data,
            'Sales_Assumption_' . $year,
            'Sales'
        );
    }

    /**
     * Ringkasan sales nyata (IDR) per bulan dari tabel transaksi sales.
     *
     * Return: ['domestic' => [jan..dec,total], 'export' => [...], 'total' => [...]]
     * Kolom revenue mengikuti format legacy `{bulan}_rev` di
     * yp_plan__trans_sales_domestic & yp_plan__trans_sales_export.
     */
    private function getSalesSummary(string $year): array
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        $build = function (string $table) use ($year, $months): array {
            $selects = [];
            foreach ($months as $mk) {
                // Alias `dec` adalah reserved word — wajib escape backtick
                $alias = $mk === 'dec' ? '`dec`' : $mk;
                $selects[] = "IFNULL(SUM({$mk}_rev),0) AS {$alias}";
            }
            $totalExpr = implode(' + ', array_map(fn ($mk) => "IFNULL(SUM({$mk}_rev),0)", $months));

            try {
                $row = $this->db->query(
                    "SELECT " . implode(', ', $selects) . ", ({$totalExpr}) AS total FROM {$table} WHERE year_code = ?",
                    [$year]
                )->getRowArray() ?? [];
            } catch (\Throwable $e) {
                log_message('error', "SalesController::getSalesSummary({$table}): " . $e->getMessage());
                $row = [];
            }

            return $row;
        };

        $domestic = $build('yp_plan__trans_sales_domestic');
        $export   = $build('yp_plan__trans_sales_export');

        $summary = [
            'domestic' => [],
            'export'   => [],
            'total'    => [],
        ];

        foreach ($months as $mk) {
            $d = (float) ($domestic[$mk] ?? 0);
            $e = (float) ($export[$mk] ?? 0);
            $summary['domestic'][$mk] = $d;
            $summary['export'][$mk]   = $e;
            $summary['total'][$mk]    = $d + $e;
        }

        $summary['domestic']['total'] = (float) ($domestic['total'] ?? 0);
        $summary['export']['total']   = (float) ($export['total'] ?? 0);
        $summary['total']['total']    = $summary['domestic']['total'] + $summary['export']['total'];

        return $summary;
    }

    /**
     * Alokasi discount reclass tahun berjalan dari yp_plan__master_reclass_monthly.
     *
     * Return: ['jan'..'dec' => float, 'total' => float]
     */
    private function getDiscountReclass(string $year): array
    {
        return $this->loadDiscountReclass($year);
    }

    /**
     * AJAX: data alokasi discount reclass untuk tahun berjalan (dipakai view summary).
     */
    public function getDiscountReclassAjax(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return $this->response->setJSON([
            'status'   => 'success',
            'discount' => $this->loadDiscountReclass($year),
        ]);
    }

    /**
     * Implementasi pengambilan alokasi discount reclass (dipakai index & AJAX).
     *
     * Return: ['jan'..'dec' => float, 'total' => float]
     */
    private function loadDiscountReclass(string $year): array
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $out    = [];
        foreach ($months as $mk) {
            $out[$mk] = 0.0;
        }
        $out['total'] = 0.0;

        try {
            $rows = $this->db->table('yp_plan__master_reclass_monthly')
                ->where('year_code', $year)
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'SalesController::getDiscountReclass: ' . $e->getMessage());

            return $out;
        }

        foreach ($rows as $r) {
            foreach ($months as $i => $mk) {
                $out[$mk] += (float) ($r[(string) ($i + 1)] ?? 0);
            }
            $out['total'] += (float) ($r['total'] ?? 0);
        }

        return $out;
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
