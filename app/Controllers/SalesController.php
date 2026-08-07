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
     * Helper privat untuk mengambil tahun anggaran aktif secara konsisten.
     */
    private function getWorkingYear(): string
    {
        return (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
    }

    /**
     * 1. Sales Summary & Discount Allocation Page
     *
     * Menghadirkan kembali halaman "Sales Summary" sistem lama (dengan tab
     * Summary / Domestic / International / Delivery & Claim / Reclass)
     * dalam tata letak Tailwind yang konsisten dengan aplikasi baru.
     */
    public function index(): string
    {
        $workingYear = $this->getWorkingYear();

        // Resolusi kurs aktif untuk tampilan nominal IDR.
        $kurs = [];
        try {
            $kursRow = $this->db->table('yp_plan__assump_rate')
                ->where('year_code', (int) $workingYear)
                ->get()->getRow();
            $kurs = is_object($kursRow) && $kursRow->usd ? ['usd' => (float) $kursRow->usd] : [];
        } catch (\Throwable $e) {
            // abaikan bila tabel kurs belum ada
        }

        return view('sales/index', [
            'title'       => 'Sales Summary & Discount Reclass',
            'workingYear' => $workingYear,
            'summary'     => $this->getSalesSummary($workingYear),
            'country'     => $this->getCountrySummary($workingYear),
            'region'      => $this->getRegionSummary($workingYear),
            'discount'    => $this->getDiscountReclass($workingYear),
            'domestic'    => $this->getChannelSummary($workingYear, 'yp_plan__trans_sales_domestic'),
            'export'      => $this->getChannelSummary($workingYear, 'yp_plan__trans_sales_export'),
            'delivery'    => $this->getDeliveryAnnual($workingYear),
            'kurs'        => $kurs,
        ]);
    }

    /**
     * 2. Entry Sales Domestic Page
     */
    public function entryDomestic(): string
    {
        $workingYear = $this->getWorkingYear();

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
        $workingYear = $this->getWorkingYear();

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
        $workingYear = $this->getWorkingYear();
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
        $workingYear = $this->getWorkingYear();

        return view('sales/setup_target', [
            'title'        => 'Target & Showcase Setup',
            'workingYear'  => $workingYear,
            'targetData'   => [],
            'showcaseData' => [],
        ]);
    }

    /**
     * AJAX Actions: Simpan alokasi diskon reclass.
     * Mendukung data bertipe form-urlencoded maupun JSON payload.
     */
    public function saveDiscountReclass(): ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Invalid request method']);
        }

        $year   = $this->getWorkingYear();
        $userId = (int) (session()->get('user_id') ?? 0);

        // Ambil payload baik dari Form Post maupun JSON body
        $post = $this->request->getPost();
        if (empty($post)) {
            $post = $this->request->getJSON(true) ?? [];
        }

        // Bulan 1..12
        $months     = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $insertData = [
            'id_coa'       => null,
            'id_dept'      => null,
            'notes'        => 'DISCOUNT RECLASS',
            'year_code'    => (int) $year,
            'created_by'   => $userId,
            'created_date' => date('Y-m-d H:i:s'),
        ];

        $total = 0;
        foreach ($months as $i => $mk) {
            $val = (float) ($post[$mk] ?? 0);
            $insertData[(string) ($i + 1)] = $val;
            $total += $val;
        }
        $insertData['total'] = $total;

        try {
            $this->db->transStart();

            // Replace data existing untuk tahun berjalan
            $this->db->table('yp_plan__master_reclass_monthly')
                ->where('year_code', $year)
                ->delete();

            $this->db->table('yp_plan__master_reclass_monthly')->insert($insertData);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan alokasi diskon ke database.']);
            }

            AuditLog::saved('sales/saveDiscountReclass', "Discount reclass tahun {$year} disimpan (total {$total})");

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Alokasi diskon berhasil disimpan.',
                'total'   => $total,
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'SalesController::saveDiscountReclass: ' . $e->getMessage());

            return $this->response->setJSON([
                'status'  => 'error', 
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Upload Sales (.xlsx / .xls) via ExcelImporter.
     */
    public function processUpload()
    {
        // Validasi file unggahan standar CI4
        $rules = [
            'upload_type' => 'required|in_list[domestic,export]',
            'excel_file'  => [
                'rules'  => 'uploaded[excel_file]|mime_in[excel_file,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv]|max_size[excel_file,10240]',
                'errors' => [
                    'uploaded' => 'Harap pilih berkas Excel terlebih dahulu.',
                    'mime_in'  => 'Format berkas harus berupa Excel (.xls / .xlsx).',
                    'max_size' => 'Ukuran berkas maksimal adalah 10MB.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $type      = $this->request->getPost('upload_type');
        $file      = $this->request->getFile('excel_file');
        $typeSales = strtolower((string) $type) === 'export' ? 'International' : 'Domestic';
        $year      = $this->getWorkingYear();
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
                    foreach ($row as $cell) {
                        if (is_numeric($cell)) {
                            $value = (float) $cell;
                            break;
                        }
                    }
                }

                $this->db->table('yp_plan__assump_sales')->insert([
                    'type_sales'   => $typeSales,
                    'value_text'   => $value,
                    'year_code'    => $year,
                    'created_by'   => $userId,
                    'created_date' => date('Y-m-d H:i:s'),
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
            $this->db->transRollback();
            log_message('error', 'Sales upload: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export data assumption sales ke .xlsx.
     */
    public function exportExcel(): ResponseInterface
    {
        $year = $this->getWorkingYear();

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
     * Ringkasan sales nyata (IDR) per bulan.
     */
    private function getSalesSummary(string $year): array
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        $build = function (string $table) use ($year, $months): array {
            $selects = [];
            foreach ($months as $mk) {
                $alias     = $mk === 'dec' ? '`dec`' : $mk;
                $selects[] = "IFNULL(SUM({$mk}_rev),0) AS {$alias}";
            }
            $totalExpr = implode(' + ', array_map(fn ($mk) => "IFNULL(SUM({$mk}_rev),0)", $months));

            try {
                return $this->db->query(
                    "SELECT " . implode(', ', $selects) . ", ({$totalExpr}) AS total FROM {$table} WHERE year_code = ?",
                    [$year]
                )->getRowArray() ?? [];
            } catch (\Throwable $e) {
                log_message('error', "SalesController::getSalesSummary({$table}): " . $e->getMessage());
                return [];
            }
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
     * Agregasi QTY & REVENUE per channel (baris produk) untuk tabel
     * "Domestic" / "International" sistem lama. Sumber: trans_sales_*.
     * Return: rows [id_channel, {m}_qty, {m}_rev, total_qty, total_rev].
     */
    private function getChannelSummary(string $year, string $table): array
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        $segments  = [];
        $revExpr   = [];
        $qtyExpr   = [];
        foreach ($months as $m) {
            $segments[] = "IFNULL(SUM({$m}_qty),0) AS {$m}_qty";
            $segments[] = "IFNULL(SUM({$m}_rev),0) AS {$m}_rev";
            $revExpr[]  = "SUM({$m}_rev)";
            $qtyExpr[]  = "SUM({$m}_qty)";
        }

        $sql = "SELECT id_channel,
                       " . implode(', ', $segments) . ",
                       (" . implode(' + ', $revExpr) . ") AS total_rev,
                       (" . implode(' + ', $qtyExpr) . ") AS total_qty
                FROM {$table}
                WHERE year_code = ?
                GROUP BY id_channel
                ORDER BY total_rev DESC";

        try {
            return $this->db->query($sql, [$year])->getResultArray() ?? [];
        } catch (\Throwable $e) {
            log_message('error', "SalesController::getChannelSummary({$table}): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Data "Delivery Exp & Customer Claim" — per tipe & kategori.
     * Sumber: yp_plan__trans_delivery_customer (kolom tahun_1..3 adalah
     * ringkasan tahun anggaran sebelumnya / berjalan).
     */
    private function getDeliveryAnnual(string $year): array
    {
        $cols = ['tahun_1', 'tahun_2', 'tahun_3', 'tahun_4'];

        $selects = array_map(fn ($c) => "IFNULL(SUM({$c}),0) AS {$c}", $cols);

        $sql = "SELECT tipe, desc_value, " . implode(', ', $selects) . "
                FROM yp_plan__trans_delivery_customer
                WHERE year_code = ?
                GROUP BY tipe, desc_value
                ORDER BY tipe, desc_value";

        try {
            return $this->db->query($sql, [$year])->getResultArray() ?? [];
        } catch (\Throwable $e) {
            log_message('error', 'SalesController::getDeliveryAnnual: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Agregasi revenue per country.
     */
    private function getCountrySummary(string $year): array
    {
        return $this->getGroupedSummary('country', $year);
    }

    /**
     * Agregasi revenue per region.
     */
    private function getRegionSummary(string $year): array
    {
        return $this->getGroupedSummary('region', $year);
    }

    /**
     * Query revenue per bulan dikelompokkan kolom tertentu dengan Whitelist Validation.
     */
    private function getGroupedSummary(string $groupCol, string $year): array
    {
        // Validasi whitelist kolom pengelompokan untuk keamanan
        $allowedCols = ['country', 'region'];
        if (! in_array($groupCol, $allowedCols, true)) {
            log_message('error', "SalesController::getGroupedSummary invalid column: {$groupCol}");
            return [];
        }

        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $tables = ['yp_plan__trans_sales_domestic_region', 'yp_plan__trans_sales_export_country'];

        $selects = [];
        foreach ($months as $mk) {
            $alias     = $mk === 'dec' ? '`dec`' : $mk;
            $selects[] = "IFNULL(SUM(t.{$mk}_rev),0) AS {$alias}";
        }
        $totalExpr = implode(' + ', array_map(fn ($mk) => "IFNULL(SUM(t.{$mk}_rev),0)", $months));

        $union = [];
        foreach ($tables as $t) {
            $cols    = implode(', ', array_map(fn ($mk) => "{$mk}_rev", $months));
            $union[] = "SELECT {$groupCol}, {$cols} FROM {$t} WHERE year_code = ? AND {$groupCol} IS NOT NULL AND {$groupCol} <> ''";
        }

        try {
            return $this->db->query(
                "SELECT t.{$groupCol} AS label, " . implode(', ', $selects) . ", ({$totalExpr}) AS total
                 FROM (" . implode(' UNION ALL ', $union) . ") t
                 GROUP BY t.{$groupCol}
                 ORDER BY total DESC",
                [$year, $year]
            )->getResultArray() ?? [];
        } catch (\Throwable $e) {
            log_message('error', "SalesController::getGroupedSummary({$groupCol}): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Alokasi discount reclass tahun berjalan.
     */
    private function getDiscountReclass(string $year): array
    {
        return $this->loadDiscountReclass($year);
    }

    /**
     * AJAX: Get data alokasi discount reclass.
     */
    public function getDiscountReclassAjax(): ResponseInterface
    {
        $year = $this->getWorkingYear();

        return $this->response->setJSON([
            'status'   => 'success',
            'discount' => $this->loadDiscountReclass($year),
        ]);
    }

    /**
     * Pengambilan data discount reclass dari database.
     */
    private function loadDiscountReclass(string $year): array
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $out    = array_fill_keys($months, 0.0);
        $out['total'] = 0.0;

        try {
            $rows = $this->db->table('yp_plan__master_reclass_monthly')
                ->where('year_code', $year)
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'SalesController::loadDiscountReclass: ' . $e->getMessage());
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