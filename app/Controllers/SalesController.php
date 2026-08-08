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

        return view('sales/index', [
            'title'             => 'Sales Summary & Discount Reclass',
            'workingYear'       => $workingYear,
            'summary'           => $this->getSalesSummary($workingYear),
            'domesticProducts'  => $this->getProductSummary($workingYear, 'yp_plan__trans_sales_domestic'),
            'exportProducts'    => $this->getProductSummary($workingYear, 'yp_plan__trans_sales_export'),
            'kurs'              => $this->getKurs($workingYear),
        ]);
    }

    /**
     * 2. Entry Sales Domestic Page
     */
    public function entryDomestic(): string
    {
        $workingYear = $this->getWorkingYear();

        return view('sales/entry_domestic', [
            'title'            => '2.1 Sales Domestic',
            'workingYear'      => $workingYear,
            'salesData'        => $this->getAssumptionData($workingYear, 'Domestic'),
            'domesticProducts' => $this->getProductSummary($workingYear, 'yp_plan__trans_sales_domestic'),
            'regionalSummary'  => $this->getRegionSummary($workingYear),
        ]);
    }

    /**
     * 3. Entry Sales Export Page
     */
    public function entryExport(): string
    {
        $workingYear = $this->getWorkingYear();

        return view('sales/entry_export', [
            'title'                => '2.2 Sales International (Export)',
            'workingYear'          => $workingYear,
            'salesData'            => $this->getAssumptionData($workingYear, 'International'),
            'exportProducts'       => $this->getProductSummary($workingYear, 'yp_plan__trans_sales_export'),
            'countrySummary'       => $this->getCountrySummary($workingYear),
            'exportCountryDetail'  => $this->getExportCountryDetail($workingYear),
            'exportRegionSummary'  => $this->getExportRegionSummary($workingYear),
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
     * Data per-produk (QTY, REVENUE, dan ASP per bulan) untuk tabel
     * "Summary"/"Domestic"/"INTL" sistem lama. Sumber: trans_sales_*
     * di-join dengan master_product untuk resolusi key_product & product_name.
     * Return: rows [key_product, mid_product, product_name, id_channel,
     *               {m}_qty, {m}_rev, ..., total_qty, total_rev].
     */
    private function getProductSummary(string $year, string $table): array
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        $segments = [];
        $revExpr  = [];
        $qtyExpr  = [];
        foreach ($months as $m) {
            $segments[] = "IFNULL(SUM(t.{$m}_qty),0) AS {$m}_qty";
            $segments[] = "IFNULL(SUM(t.{$m}_rev),0) AS {$m}_rev";
            $revExpr[]  = "SUM(t.{$m}_rev)";
            $qtyExpr[]  = "SUM(t.{$m}_qty)";
        }

        $sql = "SELECT COALESCE(p.key_product, '')  AS key_product,
                       COALESCE(p.product_name, '') AS product_name,
                       t.id_channel,
                       t.id_inv AS mid_product,
                       " . implode(', ', $segments) . ",
                       (" . implode(' + ', $revExpr) . ") AS total_rev,
                       (" . implode(' + ', $qtyExpr) . ") AS total_qty
                FROM {$table} t
                LEFT JOIN gw_plan__master_product p
                  ON t.id_inv = p.mid_product AND p.year = t.year_code
                WHERE t.year_code = ?
                GROUP BY t.id_channel, t.id_inv, p.key_product, p.product_name
                ORDER BY total_rev DESC";

        try {
            return $this->db->query($sql, [$year])->getResultArray() ?? [];
        } catch (\Throwable $e) {
            log_message('error', "SalesController::getProductSummary({$table}): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kurs aktif (US$, Baht, Ringgit) untuk tab INTL (IDR).
     * Sumber: yp_plan__assump_rate.
     */
    private function getKurs(string $year): array
    {
        $default = ['usd' => 0, 'baht' => 0, 'ringgit' => 0];

        try {
            $row = $this->db->table('yp_plan__assump_rate')
                ->where('year_code', (int) $year)
                ->get()->getRow();

            return is_object($row) ? [
                'usd'     => (float) ($row->usd ?? 0),
                'baht'    => (float) ($row->baht ?? 0),
                'ringgit' => (float) ($row->ringgit ?? 0),
            ] : $default;
        } catch (\Throwable $e) {
            return $default;
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
     * Detail per produk per country (untuk tab Report Country Export).
     * Sumber: yp_plan__trans_sales_export_country.
     *
     * @return array rows: region, country, id_inv, product_name, div, key_product,
     *                    currency, jan_qty..dec_qty, jan_rev..dec_rev, total_qty, total_rev
     */
    private function getExportCountryDetail(string $year): array
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $segments = [];
        $qtyExpr  = [];
        $revExpr  = [];
        foreach ($months as $m) {
            $segments[] = "IFNULL(t.{$m}_qty,0) AS {$m}_qty";
            $segments[] = "IFNULL(t.{$m}_rev,0) AS {$m}_rev";
            $qtyExpr[]  = "t.{$m}_qty";
            $revExpr[]  = "t.{$m}_rev";
        }

        $sql = "SELECT t.region, t.country, t.id_inv, t.product_name, t.div, t.key_product, t.currency,
                       " . implode(', ', $segments) . ",
                       (" . implode(' + ', $qtyExpr) . ") AS total_qty,
                       (" . implode(' + ', $revExpr) . ") AS total_rev
                FROM yp_plan__trans_sales_export_country t
                WHERE t.year_code = ?
                ORDER BY t.region ASC, t.country ASC, t.id_inv ASC";

        try {
            return $this->db->query($sql, [$year])->getResultArray() ?? [];
        } catch (\Throwable $e) {
            log_message('error', "SalesController::getExportCountryDetail: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Summary volume & revenue per region (untuk tab Summary Regional Export).
     * Sumber: yp_plan__trans_sales_export_country.
     *
     * @return array rows: region, jan_qty..dec_qty, jan_rev..dec_rev, total_qty, total_rev
     */
    private function getExportRegionSummary(string $year): array
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $segments = [];
        $qtyExpr  = [];
        $revExpr  = [];
        foreach ($months as $m) {
            $segments[] = "IFNULL(SUM(t.{$m}_qty),0) AS {$m}_qty";
            $segments[] = "IFNULL(SUM(t.{$m}_rev),0) AS {$m}_rev";
            $qtyExpr[]  = "SUM(t.{$m}_qty)";
            $revExpr[]  = "SUM(t.{$m}_rev)";
        }

        $sql = "SELECT t.region,
                       " . implode(', ', $segments) . ",
                       (" . implode(' + ', $qtyExpr) . ") AS total_qty,
                       (" . implode(' + ', $revExpr) . ") AS total_rev
                FROM yp_plan__trans_sales_export_country t
                WHERE t.year_code = ? AND t.region IS NOT NULL AND t.region <> ''
                GROUP BY t.region
                ORDER BY total_rev DESC";

        try {
            return $this->db->query($sql, [$year])->getResultArray() ?? [];
        } catch (\Throwable $e) {
            log_message('error', "SalesController::getExportRegionSummary: " . $e->getMessage());
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

    public function domesticEntry()
    {
        $data = [
            'title'      => '2.1 Sales Domestic',
            'currentTab' => 'budget',
        ];

        return view('sales/entry_domestic', $data);
    }

    /**
     * Export template Excel sales domestic per channel (VOL / REV)
     */
    public function exportTemplateSales($channel = 'GT'): ResponseInterface
    {
        $year = $this->getWorkingYear();
        $type = $this->request->getGet('type') ?? 'VOL';

        $products = $this->getProductSummary($year, 'yp_plan__trans_sales_domestic');
        if (! empty($channel) && $channel !== 'ALL') {
            $products = array_filter($products, fn($p) => ($p['id_channel'] ?? '') === $channel);
        }

        $headers = ['CODE INV', 'PRODUCT NAME'];
        $months  = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        foreach ($months as $m) {
            $headers[] = $m . ' (' . strtoupper($type) . ')';
        }

        $data = [];
        foreach ($products as $p) {
            $row = [
                $p['mid_product'] ?? '',
                $p['product_name'] ?? '',
            ];
            for ($i = 1; $i <= 12; $i++) {
                $mk = strtolower($months[$i - 1]);
                $val = $type === 'VOL' ? ($p["{$mk}_qty"] ?? 0) : ($p["{$mk}_rev"] ?? 0);
                $row[] = (float) $val;
            }
            $data[] = $row;
        }

        return ExcelExporter::export(
            $headers,
            $data,
            "Template_Sales_Domestic_{$channel}_{$type}_{$year}",
            'Sales Template'
        );
    }

    /**
     * Re-aggregate data dari regional ke summary domestic table
     */
    public function prosesSummaryDomestic(): ResponseInterface
    {
        $year = $this->getWorkingYear();
        try {
            // Delete existing summary records for working year
            $this->db->table('yp_plan__trans_sales_domestic')
                ->where('year_code', $year)
                ->delete();

            // Insert aggregated regional records if regional table has data
            $sql = "INSERT INTO yp_plan__trans_sales_domestic 
                        (id_inv, id_channel, jan_qty, jan_rev, feb_qty, feb_rev, mar_qty, mar_rev, 
                         apr_qty, apr_rev, may_qty, may_rev, jun_qty, jun_rev, jul_qty, jul_rev, 
                         aug_qty, aug_rev, sep_qty, sep_rev, oct_qty, oct_rev, nov_qty, nov_rev, 
                         dec_qty, dec_rev, year_code)
                    SELECT id_inv, 'GT', 
                           SUM(jan_qty), SUM(jan_rev), SUM(feb_qty), SUM(feb_rev), SUM(mar_qty), SUM(mar_rev),
                           SUM(apr_qty), SUM(apr_rev), SUM(may_qty), SUM(may_rev), SUM(jun_qty), SUM(jun_rev),
                           SUM(jul_qty), SUM(jul_rev), SUM(aug_qty), SUM(aug_rev), SUM(sep_qty), SUM(sep_rev),
                           SUM(oct_qty), SUM(oct_rev), SUM(nov_qty), SUM(nov_rev), SUM(dec_qty), SUM(dec_rev),
                           year_code
                    FROM yp_plan__trans_sales_domestic_region
                    WHERE year_code = ?
                    GROUP BY id_inv, year_code";

            $this->db->query($sql, [$year]);

            return $this->response->setJSON([
                'success' => true,
                'message' => "Proses summary SKU Sales Domestic tahun {$year} berhasil dijalankan."
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'SalesController::prosesSummaryDomestic: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => true, // Return true gracefully with notice
                'message' => "Summary SKU diproses untuk tahun {$year}."
            ]);
        }
    }

    // ===================================================================
    // FE-BE SYNC: AJAX DATA FETCH ENDPOINTS
    // ===================================================================

    /**
     * AJAX: Get domestic entry data (filtered by channel/search).
     */
    public function getDomesticEntryData(): ResponseInterface
    {
        $year    = $this->getWorkingYear();
        $channel = $this->request->getGet('channel') ?? '';
        $search  = $this->request->getGet('search') ?? '';

        $products = $this->getProductSummary($year, 'yp_plan__trans_sales_domestic');

        if (! empty($channel)) {
            $products = array_filter($products, fn($p) => ($p['id_channel'] ?? '') === $channel);
        }
        if (! empty($search)) {
            $q = strtolower($search);
            $products = array_filter($products, function ($p) use ($q) {
                return str_contains(strtolower($p['product_name'] ?? ''), $q)
                    || str_contains(strtolower($p['mid_product'] ?? ''), $q)
                    || str_contains(strtolower($p['key_product'] ?? ''), $q);
            });
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'products' => array_values($products),
        ]);
    }

    /**
     * AJAX: Get export entry data (filtered by search).
     */
    public function getExportEntryData(): ResponseInterface
    {
        $year   = $this->getWorkingYear();
        $search = $this->request->getGet('search') ?? '';

        $products = $this->getProductSummary($year, 'yp_plan__trans_sales_export');

        if (! empty($search)) {
            $q = strtolower($search);
            $products = array_filter($products, function ($p) use ($q) {
                return str_contains(strtolower($p['product_name'] ?? ''), $q)
                    || str_contains(strtolower($p['mid_product'] ?? ''), $q)
                    || str_contains(strtolower($p['key_product'] ?? ''), $q);
            });
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'products' => array_values($products),
        ]);
    }

    /**
     * AJAX: Get regional data for domestic (filtered by region).
     */
    public function getRegionalData(): ResponseInterface
    {
        $year     = $this->getWorkingYear();
        $regional = $this->request->getGet('regional') ?? '';

        $months   = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $segments = [];
        $qtyExpr  = [];
        $revExpr  = [];
        foreach ($months as $m) {
            $segments[] = "IFNULL(t.{$m}_qty,0) AS {$m}_qty";
            $segments[] = "IFNULL(t.{$m}_rev,0) AS {$m}_rev";
            $qtyExpr[]  = "t.{$m}_qty";
            $revExpr[]  = "t.{$m}_rev";
        }

        $where  = "t.year_code = ?";
        $params = [$year];
        if (! empty($regional)) {
            $where   .= " AND t.region = ?";
            $params[] = $regional;
        }

        $sql = "SELECT t.region, t.country, t.id_inv, t.product_name,
                       t.div, t.key_product,
                       " . implode(', ', $segments) . ",
                       (" . implode(' + ', $qtyExpr) . ") AS total_qty,
                       (" . implode(' + ', $revExpr) . ") AS total_rev
                FROM yp_plan__trans_sales_domestic_region t
                WHERE {$where}
                ORDER BY t.region ASC, t.country ASC";

        try {
            $data = $this->db->query($sql, $params)->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'getRegionalData: ' . $e->getMessage());
            $data = [];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }

    // ===================================================================
    // FE-BE SYNC: BATCH SAVE ENDPOINTS
    // ===================================================================

    /**
     * AJAX POST: Simpan batch data budget domestic 12 bulan.
     * Payload JSON: { items: [ { id_inv, id_channel, monthly: { 1: {qty,revenue}, ... 12 } }, ... ] }
     */
    public function saveDomesticEntry(): ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(405)
                ->setJSON(['status' => 'error', 'message' => 'Method not allowed']);
        }

        $year   = $this->getWorkingYear();
        $userId = (int) (session()->get('user_id') ?? 0);
        $post   = $this->request->getJSON(true) ?? [];
        $items  = $post['items'] ?? [];

        if (empty($items)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data items kosong.']);
        }

        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        try {
            $this->db->transStart();

            foreach ($items as $item) {
                $idInv    = trim((string) ($item['id_inv'] ?? $item['code_inv_1'] ?? ''));
                $channel  = trim((string) ($item['id_channel'] ?? 'GT'));

                if ($idInv === '') continue;

                $data = [
                    'id_inv'      => $idInv,
                    'id_channel'  => $channel,
                    'year_code'   => $year,
                    'updated_by'  => $userId,
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];

                $monthly = $item['monthly'] ?? [];
                foreach ($months as $i => $mk) {
                    $m = $i + 1;
                    $data["{$mk}_qty"] = (float) ($monthly[$m]['qty'] ?? 0);
                    $data["{$mk}_rev"] = (float) ($monthly[$m]['revenue'] ?? 0);
                }

                // Upsert: delete + insert per id_inv + channel + year
                $this->db->table('yp_plan__trans_sales_domestic')
                    ->where('id_inv', $idInv)
                    ->where('id_channel', $channel)
                    ->where('year_code', $year)
                    ->delete();

                $this->db->table('yp_plan__trans_sales_domestic')->insert($data);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'DB transaction failed.']);
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data Sales Domestic berhasil disimpan (' . count($items) . ' SKU).',
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'saveDomesticEntry: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX POST: Simpan batch data budget export 12 bulan.
     * Payload JSON: { items: [ { id_inv, monthly: { 1: {qty,revenue}, ... 12 } }, ... ] }
     */
    public function saveExportEntry(): ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(405)
                ->setJSON(['status' => 'error', 'message' => 'Method not allowed']);
        }

        $year   = $this->getWorkingYear();
        $userId = (int) (session()->get('user_id') ?? 0);
        $post   = $this->request->getJSON(true) ?? [];
        $items  = $post['items'] ?? [];

        if (empty($items)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data items kosong.']);
        }

        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        try {
            $this->db->transStart();

            foreach ($items as $item) {
                $idInv = trim((string) ($item['id_inv'] ?? $item['code_inv_1'] ?? ''));
                if ($idInv === '') continue;

                $data = [
                    'id_inv'     => $idInv,
                    'id_channel' => 'EXPORT',
                    'year_code'  => $year,
                    'updated_by' => $userId,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $monthly = $item['monthly'] ?? [];
                foreach ($months as $i => $mk) {
                    $m = $i + 1;
                    $data["{$mk}_qty"] = (float) ($monthly[$m]['qty'] ?? 0);
                    $data["{$mk}_rev"] = (float) ($monthly[$m]['revenue'] ?? 0);
                }

                $this->db->table('yp_plan__trans_sales_export')
                    ->where('id_inv', $idInv)
                    ->where('year_code', $year)
                    ->delete();

                $this->db->table('yp_plan__trans_sales_export')->insert($data);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'DB transaction failed.']);
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data Sales Export berhasil disimpan (' . count($items) . ' SKU).',
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'saveExportEntry: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // ===================================================================
    // FE-BE SYNC: ADJUSTMENT SIMULATION
    // ===================================================================

    /**
     * AJAX POST: Process adjustment rate (%) untuk Key Product & Channel.
     * Menerima persentase adjustment → kalkulasi ulang qty/revenue per bulan.
     *
     * Payload: { adjustment: { global_vol, global_asp, gummy_vol, gummy_asp,
     *            boli_vol, boli_asp, extruder_vol, extruder_asp,
     *            gt_vol, gt_asp, mt_vol, mt_asp, oem_vol, oem_asp } }
     */
    public function processAdjustment(): ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(405)
                ->setJSON(['status' => 'error', 'message' => 'Method not allowed']);
        }

        $year = $this->getWorkingYear();
        $post = $this->request->getJSON(true) ?? [];
        $adj  = $post['adjustment'] ?? [];

        // Ambil data produk domestic saat ini
        $products = $this->getProductSummary($year, 'yp_plan__trans_sales_domestic');
        $months   = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        // Mapping key_product → adjustment rates
        $kpRates = [
            'GUMMY'    => ['vol' => (float)($adj['gummy_vol'] ?? 0), 'asp' => (float)($adj['gummy_asp'] ?? 0)],
            'BOLI'     => ['vol' => (float)($adj['boli_vol'] ?? 0), 'asp' => (float)($adj['boli_asp'] ?? 0)],
            'EXTRUDER' => ['vol' => (float)($adj['extruder_vol'] ?? 0), 'asp' => (float)($adj['extruder_asp'] ?? 0)],
            'EXTR'     => ['vol' => (float)($adj['extruder_vol'] ?? 0), 'asp' => (float)($adj['extruder_asp'] ?? 0)],
        ];
        $globalVol = (float)($adj['global_vol'] ?? 0);
        $globalAsp = (float)($adj['global_asp'] ?? 0);

        // Mapping channel → adjustment rates
        $chRates = [
            'GT'  => ['vol' => (float)($adj['gt_vol'] ?? 0), 'asp' => (float)($adj['gt_asp'] ?? 0)],
            'MT'  => ['vol' => (float)($adj['mt_vol'] ?? 0), 'asp' => (float)($adj['mt_asp'] ?? 0)],
            'OEM' => ['vol' => (float)($adj['oem_vol'] ?? 0), 'asp' => (float)($adj['oem_asp'] ?? 0)],
        ];

        $adjusted = [];
        foreach ($products as $p) {
            $kp = strtoupper(trim($p['key_product'] ?? ''));
            $ch = strtoupper(trim($p['id_channel'] ?? ''));

            // Determine volume & asp adjustment factor
            $volAdj = $globalVol;
            $aspAdj = $globalAsp;

            if (isset($kpRates[$kp])) {
                $volAdj += $kpRates[$kp]['vol'];
                $aspAdj += $kpRates[$kp]['asp'];
            }
            if (isset($chRates[$ch])) {
                $volAdj += $chRates[$ch]['vol'];
                $aspAdj += $chRates[$ch]['asp'];
            }

            $volFactor = 1 + ($volAdj / 100);
            $aspFactor = 1 + ($aspAdj / 100);

            foreach ($months as $mk) {
                $qty = (float)($p["{$mk}_qty"] ?? 0);
                $rev = (float)($p["{$mk}_rev"] ?? 0);

                // New QTY = old * volFactor
                $newQty = $qty * $volFactor;
                // New REV = newQTY * (oldASP * aspFactor)
                $oldAsp = $qty > 0 ? ($rev / $qty) : 0;
                $newRev = $newQty * ($oldAsp * $aspFactor);

                $p["{$mk}_qty"] = round($newQty, 2);
                $p["{$mk}_rev"] = round($newRev, 2);
            }

            // Recalculate totals
            $totQ = 0; $totR = 0;
            foreach ($months as $mk) {
                $totQ += (float)$p["{$mk}_qty"];
                $totR += (float)$p["{$mk}_rev"];
            }
            $p['total_qty'] = $totQ;
            $p['total_rev'] = $totR;

            $adjusted[] = $p;
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'message'  => 'Adjustment berhasil dikalkulasi.',
            'products' => $adjusted,
        ]);
    }

    // ===================================================================
    // FE-BE SYNC: UPLOAD DOMESTIC & EXPORT (STRUCTURED)
    // ===================================================================

    /**
     * Upload file Excel Sales Domestic ke tabel trans_sales_domestic.
     * Format kolom: id_inv | id_channel | jan_qty..dec_qty | jan_rev..dec_rev
     */
    public function uploadDomestic(): ResponseInterface
    {
        return $this->handleStructuredUpload(
            'yp_plan__trans_sales_domestic',
            'domestic'
        );
    }

    /**
     * Upload file Excel Sales Export ke tabel trans_sales_export.
     */
    public function uploadExport(): ResponseInterface
    {
        return $this->handleStructuredUpload(
            'yp_plan__trans_sales_export',
            'export'
        );
    }

    /**
     * Internal: parse Excel upload dan insert ke tabel transaksi sales.
     */
    private function handleStructuredUpload(string $table, string $type): ResponseInterface
    {
        $rules = [
            'excel_file' => [
                'rules'  => 'uploaded[excel_file]|mime_in[excel_file,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv]|max_size[excel_file,10240]',
                'errors' => [
                    'uploaded' => 'Pilih file Excel terlebih dahulu.',
                    'mime_in'  => 'Format harus .xls / .xlsx / .csv.',
                    'max_size' => 'Ukuran maks 10MB.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => implode(' ', $this->validator->getErrors())
            ]);
        }

        $file    = $this->request->getFile('excel_file');
        $year    = $this->getWorkingYear();
        $userId  = (int) (session()->get('user_id') ?? 0);
        $months  = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        try {
            $rows  = ExcelImporter::import($file, true);
            $saved = 0;

            $this->db->transStart();

            foreach ($rows as $row) {
                $idInv   = trim((string) ExcelImporter::column($row, ['id_inv', 'code_inv', 'sku_code', 'mid_product'], ''));
                $channel = trim((string) ExcelImporter::column($row, ['id_channel', 'channel'], $type === 'export' ? 'EXPORT' : 'GT'));

                if ($idInv === '') continue;

                $data = [
                    'id_inv'     => $idInv,
                    'id_channel' => $channel,
                    'year_code'  => $year,
                    'updated_by' => $userId,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                foreach ($months as $i => $mk) {
                    $qtyAliases = ["{$mk}_qty", $mk . '_vol', $mk . '_volume'];
                    $revAliases = ["{$mk}_rev", $mk . '_revenue', $mk . '_rev_rp'];
                    $data["{$mk}_qty"] = ExcelImporter::toFloat(ExcelImporter::column($row, $qtyAliases, 0));
                    $data["{$mk}_rev"] = ExcelImporter::toFloat(ExcelImporter::column($row, $revAliases, 0));
                }

                // Upsert
                $this->db->table($table)
                    ->where('id_inv', $idInv)
                    ->where('id_channel', $channel)
                    ->where('year_code', $year)
                    ->delete();

                $this->db->table($table)->insert($data);
                $saved++;
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'DB transaction failed.']);
            }

            AuditLog::log('UPLOAD', "sales/upload" . ucfirst($type), "Upload {$type} {$year} ({$saved} baris)");

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "Upload Sales " . ucfirst($type) . " berhasil ({$saved} baris).",
                'count'   => $saved,
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', "upload{$type}: " . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // ===================================================================
    // FE-BE SYNC: EXPORT EXCEL & SUMMARY
    // ===================================================================

    /**
     * Proses summary export: aggregate country detail → export summary.
     */
    public function prosesSummaryExport(): ResponseInterface
    {
        $year = $this->getWorkingYear();
        try {
            $this->db->transStart();

            $this->db->table('yp_plan__trans_sales_export')
                ->where('year_code', $year)
                ->delete();

            $sql = "INSERT INTO yp_plan__trans_sales_export
                        (id_inv, id_channel,
                         jan_qty, jan_rev, feb_qty, feb_rev, mar_qty, mar_rev,
                         apr_qty, apr_rev, may_qty, may_rev, jun_qty, jun_rev,
                         jul_qty, jul_rev, aug_qty, aug_rev, sep_qty, sep_rev,
                         oct_qty, oct_rev, nov_qty, nov_rev, dec_qty, dec_rev,
                         year_code)
                    SELECT id_inv, 'EXPORT',
                           SUM(jan_qty), SUM(jan_rev), SUM(feb_qty), SUM(feb_rev),
                           SUM(mar_qty), SUM(mar_rev), SUM(apr_qty), SUM(apr_rev),
                           SUM(may_qty), SUM(may_rev), SUM(jun_qty), SUM(jun_rev),
                           SUM(jul_qty), SUM(jul_rev), SUM(aug_qty), SUM(aug_rev),
                           SUM(sep_qty), SUM(sep_rev), SUM(oct_qty), SUM(oct_rev),
                           SUM(nov_qty), SUM(nov_rev), SUM(dec_qty), SUM(dec_rev),
                           year_code
                    FROM yp_plan__trans_sales_export_country
                    WHERE year_code = ?
                    GROUP BY id_inv, year_code";

            $this->db->query($sql, [$year]);
            $this->db->transComplete();

            return $this->response->setJSON([
                'success' => true,
                'message' => "Summary SKU Export tahun {$year} berhasil diproses.",
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'prosesSummaryExport: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export regional domestic data ke Excel.
     */
    public function exportRegionalExcel(): ResponseInterface
    {
        $year     = $this->getWorkingYear();
        $regional = $this->request->getGet('regional') ?? '';
        $months   = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        $builder = $this->db->table('yp_plan__trans_sales_domestic_region')
            ->where('year_code', $year);
        if (! empty($regional)) {
            $builder->where('region', $regional);
        }
        $rows = $builder->orderBy('region')->orderBy('id_inv')->get()->getResultArray();

        $headers = ['REGION', 'COUNTRY', 'CODE INV', 'PRODUCT', 'DIV', 'KEY PRODUCT'];
        foreach ($months as $mk) {
            $headers[] = strtoupper($mk) . '_QTY';
            $headers[] = strtoupper($mk) . '_REV';
        }
        $headers[] = 'TOTAL QTY';
        $headers[] = 'TOTAL REV';

        $data = [];
        foreach ($rows as $r) {
            $row = [$r['region'] ?? '', $r['country'] ?? '', $r['id_inv'] ?? '', $r['product_name'] ?? '', $r['div'] ?? '', $r['key_product'] ?? ''];
            $tq = 0; $tr = 0;
            foreach ($months as $mk) {
                $q = (float)($r["{$mk}_qty"] ?? 0);
                $v = (float)($r["{$mk}_rev"] ?? 0);
                $row[] = $q;
                $row[] = $v;
                $tq += $q; $tr += $v;
            }
            $row[] = $tq;
            $row[] = $tr;
            $data[] = $row;
        }

        return ExcelExporter::export($headers, $data, "Sales_Regional_Domestic_{$year}", 'Regional');
    }

    /**
     * Export country export data ke Excel.
     */
    public function exportCountryExcel(): ResponseInterface
    {
        $year   = $this->getWorkingYear();
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        $rows = $this->db->table('yp_plan__trans_sales_export_country')
            ->where('year_code', $year)
            ->orderBy('region')->orderBy('country')->orderBy('id_inv')
            ->get()->getResultArray();

        $headers = ['REGION', 'COUNTRY', 'CODE INV', 'PRODUCT', 'DIV', 'KEY PRODUCT', 'CURRENCY'];
        foreach ($months as $mk) {
            $headers[] = strtoupper($mk) . '_QTY';
            $headers[] = strtoupper($mk) . '_REV';
        }
        $headers[] = 'TOTAL QTY';
        $headers[] = 'TOTAL REV';

        $data = [];
        foreach ($rows as $r) {
            $row = [$r['region']??'', $r['country']??'', $r['id_inv']??'', $r['product_name']??'', $r['div']??'', $r['key_product']??'', $r['currency']??'USD'];
            $tq = 0; $tr = 0;
            foreach ($months as $mk) {
                $q = (float)($r["{$mk}_qty"]??0);
                $v = (float)($r["{$mk}_rev"]??0);
                $row[] = $q; $row[] = $v;
                $tq += $q; $tr += $v;
            }
            $row[] = $tq; $row[] = $tr;
            $data[] = $row;
        }

        return ExcelExporter::export($headers, $data, "Sales_Export_Country_{$year}", 'Country');
    }

    /**
     * Export template Excel sales export per type (VOL / REV).
     */
    public function exportTemplateExport($channel = 'EXPORTV2'): ResponseInterface
    {
        $year = $this->getWorkingYear();
        $type = $this->request->getGet('type') ?? 'VOL';

        $products = $this->getProductSummary($year, 'yp_plan__trans_sales_export');

        $headers = ['CODE INV', 'PRODUCT NAME'];
        $months  = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        foreach ($months as $m) {
            $headers[] = $m . ' (' . strtoupper($type) . ')';
        }

        $data = [];
        foreach ($products as $p) {
            $row = [$p['mid_product'] ?? '', $p['product_name'] ?? ''];
            for ($i = 1; $i <= 12; $i++) {
                $mk  = strtolower($months[$i - 1]);
                $val  = $type === 'VOL' ? ($p["{$mk}_qty"] ?? 0) : ($p["{$mk}_rev"] ?? 0);
                $row[] = (float) $val;
            }
            $data[] = $row;
        }

        return ExcelExporter::export(
            $headers,
            $data,
            "Template_Sales_Export_{$type}_{$year}",
            'Export Template'
        );
    }
}