<?php

namespace App\Controllers;

use App\Libraries\ExcelExporter;
use App\Libraries\ExcelImporter;
use App\Libraries\AuditLog;
use App\Models\AssumptionModel;
use App\Models\CoaModel;
use App\Models\CostCenterModel;
use App\Models\DepartmentModel;
use App\Models\PeriodModel;
use App\Models\ProductModel;
use App\Models\SalaryMppModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Master Data — COA, Cost Center, Departemen, Product, Salary MPP, dan Configure Period.
 *
 * Halaman di-render server-side dengan filter via GET.
 * Mutasi (save / toggle / copy / lock) via POST AJAX → JSON.
 */
class Master extends BaseController
{
    protected $coa;
    protected $costCenter;
    protected $department;
    protected $period;
    protected $product;
    protected $salaryMpp;
    protected $assumption;

    public function __construct()
    {
        $this->coa        = new CoaModel();
        $this->costCenter = new CostCenterModel();
        $this->department = new DepartmentModel();
        $this->period     = new PeriodModel();
        $this->product    = new ProductModel();
        $this->salaryMpp  = new SalaryMppModel();
        $this->assumption = new AssumptionModel();
    }

    public function index(): RedirectResponse
    {
        return redirect()->to('/master/coa');
    }

    /* ------------------------------------------------------------------
     * Halaman
     * ------------------------------------------------------------------ */

    public function coa()
    {
        $years = $this->coa->getYears();
        $filters = [
            'search' => $this->request->getGet('search') ?? '',
            'type'   => $this->request->getGet('type') ?? '',
            'year'   => $this->resolveYearFilter($years),
            'status' => $this->request->getGet('status') ?? 'A',
        ];

        return view('master-data/chart-of-account', [
            'title'   => 'Master Data - Chart of Account (COA)',
            'rows'    => $this->coa->getAll($filters),
            'years'   => $years,
            'types'   => $this->coa->getTypes(),
            'filters' => $filters,
            'flash'   => $this->consumeFlash(),
        ]);
    }

    public function costCenter()
    {
        $filters = [
            'search' => $this->request->getGet('search') ?? '',
            'type'   => $this->request->getGet('type') ?? '',
            'year'   => $this->request->getGet('year') ?: session()->get('year_code'),
            'status' => $this->request->getGet('status') ?? 'A',
        ];

        return view('master-data/cost-center', [
            'title'       => 'Master Data - Cost Center',
            'costCenters' => $this->costCenter->getAll($filters),
            'departments' => $this->department->getAll(['status' => 'A']),
            'years'       => $this->costCenter->getYears(),
            'types'       => $this->costCenter->getTypes(),
            'filters'     => $filters,
            'flash'       => $this->consumeFlash(),
        ]);
    }

    public function department()
    {
        $filters = [
            'search' => $this->request->getGet('search') ?? '',
            'status' => $this->request->getGet('status') ?? 'A',
        ];

        return view('master-data/departemen', [
            'title'   => 'Master Data - Departemen',
            'rows'    => $this->department->getAll($filters),
            'filters' => $filters,
            'flash'   => $this->consumeFlash(),
        ]);
    }

    public function product()
    {
        $filters = [
            'search'  => $this->request->getGet('search') ?? '',
            'channel' => $this->request->getGet('channel') ?? '',
            'year'    => $this->request->getGet('year') ?: session()->get('year_code'),
            'status'  => $this->request->getGet('status') ?? 'A',
        ];

        return view('master-data/product', [
            'title'    => 'Master Data - Produk',
            'rows'     => $this->product->getAll($filters),
            'channels' => $this->product->getChannels(),
            'years'    => $this->product->getYears(),
            'filters'  => $filters,
            'flash'    => $this->consumeFlash(),
        ]);
    }

    public function salaryMpp()
    {
        $filters = [
            'search'  => $this->request->getGet('search') ?? '',
            'dept_id' => $this->request->getGet('dept_id') ?? '',
            'type'    => $this->request->getGet('type') ?? '',
            'year'    => $this->request->getGet('year') ?: session()->get('year_code'),
            'status'  => $this->request->getGet('status') ?? 'A',
        ];

        return view('master-data/salary-mpp', [
            'title'       => 'Master Data - Salary & MPP',
            'rows'        => $this->salaryMpp->getAll($filters),
            'departments' => $this->department->getAll(['status' => 'A']),
            'mppTypes'    => $this->salaryMpp->getMppTypes(),
            'years'       => $this->salaryMpp->getYears(),
            'filters'     => $filters,
            'flash'       => $this->consumeFlash(),
        ]);
    }

    public function configurePeriod()
    {
        return view('master-data/configure-period', [
            'title' => 'Master Data - Configure Period (Tahun Anggaran)',
            'rows'  => $this->period->getAllYears(),
            'cc'    => $this->costCenter->getAll(['status' => 'A']),
            'flash' => $this->consumeFlash(),
        ]);
    }

    public function period()
    {
        return $this->configurePeriod();
    }

    public function assumption()
    {
        $year = (int) ($this->request->getGet('year') ?: session()->get('year_code') ?: date('Y'));

        return view('master-data/assumption', [
            'title'    => 'Master Data - Assumption',
            'year'     => $year,
            'years'    => $this->assumption->getYears(),
            'types'    => $this->assumption->getTypes(),
            'economic' => $this->assumption->getEconomic($year),
            'domestic' => $this->assumption->getSalesDomestic($year),
            'export'   => $this->assumption->getSalesExport($year),
            'other'    => $this->assumption->getOther($year),
            'flash'    => $this->consumeFlash(),
        ]);
    }

    /* ------------------------------------------------------------------
     * Export CSV Support
     * ------------------------------------------------------------------ */

    public function coaExport()
    {
        $filters = [
            'search' => $this->request->getGet('search') ?? '',
            'type'   => $this->request->getGet('type') ?? '',
            'year'   => $this->resolveYearFilter($this->coa->getYears()),
            'status' => $this->request->getGet('status') ?? '',
        ];

        $rows = $this->coa->getAll($filters);
        $filename = 'COA_Export_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Main Account', 'SAP Code', 'Header', 'Sub Account', 'Description', 'Year', 'Type', 'Category', 'Status']);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id_cost_center'] ?? '',
                $r['main_account'] ?? '',
                $r['id_acct_ext'] ?? '',
                $r['cost_center_header'] ?? '',
                $r['cost_center_sub'] ?? '',
                $r['cost_center_desc'] ?? '',
                $r['year'] ?? '',
                $r['type'] ?? '',
                $r['category'] ?? '',
                ($r['status'] === 'A') ? 'Active' : 'Inactive',
            ]);
        }
        fclose($out);
        exit;
    }

    public function costCenterExport()
    {
        $filters = [
            'search' => $this->request->getGet('search') ?? '',
            'type'   => $this->request->getGet('type') ?? '',
            'year'   => $this->request->getGet('year') ?: session()->get('year_code'),
            'status' => $this->request->getGet('status') ?? '',
        ];

        $rows = $this->costCenter->getAll($filters);
        $filename = 'CostCenter_Export_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Cost Center Code', 'SAP Code', 'Description', 'Location', 'Year', 'Type', 'Status']);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id_cost_center'] ?? '',
                $r['cost_center'] ?? '',
                $r['cost_center_sap'] ?? '',
                $r['cost_desc'] ?? '',
                $r['location'] ?? '',
                $r['year'] ?? '',
                $r['type'] ?? '',
                ($r['status'] === 'A') ? 'Active' : 'Inactive',
            ]);
        }
        fclose($out);
        exit;
    }

    public function departmentExport()
    {
        $filters = [
            'search' => $this->request->getGet('search') ?? '',
            'status' => $this->request->getGet('status') ?? '',
        ];

        $rows = $this->department->getAll($filters);
        $filename = 'Department_Export_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Department Code', 'Department Name', 'Status']);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id_dept'] ?? '',
                $r['dept_code'] ?? '',
                $r['dept_desc'] ?? '',
                ($r['status'] === 'A') ? 'Active' : 'Inactive',
            ]);
        }
        fclose($out);
        exit;
    }

    public function productExport()
    {
        $filters = [
            'search'  => $this->request->getGet('search') ?? '',
            'channel' => $this->request->getGet('channel') ?? '',
            'year'    => $this->request->getGet('year') ?: session()->get('year_code'),
            'status'  => $this->request->getGet('status') ?? '',
        ];

        $rows = $this->product->getAll($filters);
        $filename = 'Product_Export_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Product Name', 'Channel', 'Key Product', 'MID Product', 'Default Box (DB)', 'Pcs / Box', 'Gramasi (Gr)', 'Year', 'Status']);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id_product'] ?? '',
                $r['product_name'] ?? '',
                $r['id_channel'] ?? '',
                $r['key_product'] ?? '',
                $r['mid_product'] ?? '',
                $r['db'] ?? '',
                $r['pcs'] ?? '',
                $r['gr'] ?? '',
                $r['year'] ?? '',
                ($r['status'] === 'A') ? 'Active' : 'Inactive',
            ]);
        }
        fclose($out);
        exit;
    }

    public function salaryMppExport()
    {
        $filters = [
            'search'  => $this->request->getGet('search') ?? '',
            'dept_id' => $this->request->getGet('dept_id') ?? '',
            'type'    => $this->request->getGet('type') ?? '',
            'year'    => $this->request->getGet('year') ?: session()->get('year_code'),
            'status'  => $this->request->getGet('status') ?? '',
        ];

        $rows = $this->salaryMpp->getAll($filters);
        $filename = 'SalaryMPP_Export_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Position Description', 'Department', 'Type', 'Salary Rate (Rp)', 'Year', 'Status']);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'] ?? '',
                $r['desc'] ?? '',
                $r['dept_desc'] ?? ($r['dept_code'] ?? ''),
                $r['type_name'] ?? $r['type'],
                $r['salary'] ?? 0,
                $r['year_code'] ?? '',
                ($r['status'] === 'A') ? 'Active' : 'Inactive',
            ]);
        }
        fclose($out);
        exit;
    }

    /* ------------------------------------------------------------------
     * COA AJAX
     * ------------------------------------------------------------------ */

    public function coaSave(): ResponseInterface
    {
        $data = $this->request->getPost();
        $id   = ! empty($data['id']) ? (int) $data['id'] : null;
        $result = $this->coa->saveAccount($data, $id);

        return $this->jsonResult($result, 'master/coa/save');
    }

    public function coaToggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->coa->toggleStatus($id);

        return $this->jsonResult($result, 'master/coa/toggle');
    }

    public function coaCopyYear(): ResponseInterface
    {
        $from   = (int) $this->request->getPost('from_year');
        $to     = (int) $this->request->getPost('to_year');
        $result = $this->coa->copyYear($from, $to);

        return $this->jsonResult($result, 'master/coa/copyYear');
    }

    /* ------------------------------------------------------------------
     * Cost Center AJAX
     * ------------------------------------------------------------------ */

    public function costCenterSave(): ResponseInterface
    {
        $data = $this->request->getPost();
        $id   = ! empty($data['id']) ? (int) $data['id'] : null;
        $result = $this->costCenter->saveCostCenter($data, $id);

        return $this->jsonResult($result, 'master/cost-center/save');
    }

    public function costCenterToggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->costCenter->toggleStatus($id);

        return $this->jsonResult($result, 'master/cost-center/toggle');
    }

    /* ------------------------------------------------------------------
     * Departemen AJAX
     * ------------------------------------------------------------------ */

    public function departmentSave(): ResponseInterface
    {
        $data = $this->request->getPost();
        $id   = ! empty($data['id']) ? (int) $data['id'] : null;
        $result = $this->department->saveDepartment($data, $id);

        return $this->jsonResult($result, 'master/department/save');
    }

    public function departmentToggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->department->toggleStatus($id);

        return $this->jsonResult($result, 'master/department/toggle');
    }

    /* ------------------------------------------------------------------
     * Product AJAX
     * ------------------------------------------------------------------ */

    public function productSave(): ResponseInterface
    {
        $data = $this->request->getPost();
        $id   = ! empty($data['id']) ? (int) $data['id'] : null;
        $result = $this->product->saveProduct($data, $id);

        return $this->jsonResult($result, 'master/product/save');
    }

    public function productToggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->product->toggleStatus($id);

        return $this->jsonResult($result, 'master/product/toggle');
    }

    /* ------------------------------------------------------------------
     * Salary MPP AJAX
     * ------------------------------------------------------------------ */

    public function salaryMppSave(): ResponseInterface
    {
        $data = $this->request->getPost();
        $id   = ! empty($data['id']) ? (int) $data['id'] : null;
        $result = $this->salaryMpp->saveSalaryMpp($data, $id);

        return $this->jsonResult($result, 'master/salary-mpp/save');
    }

    public function salaryMppToggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->salaryMpp->toggleStatus($id);

        return $this->jsonResult($result, 'master/salary-mpp/toggle');
    }

    /* ------------------------------------------------------------------
     * Assumption AJAX / Upload / Export
     * ------------------------------------------------------------------ */

    public function assumptionSave(): ResponseInterface
    {
        $category = (string) $this->request->getPost('category');
        $year     = (int) ($this->request->getPost('year') ?: session()->get('year_code') ?: date('Y'));
        $userId   = (int) (session()->get('user_id') ?? 0);

        // Guard: payload baris wajib JSON array yang valid. Payload rusak JANGAN
        // diteruskan — save bersifat replace-per-year (bisa menghapus data lama).
        $rows = json_decode((string) $this->request->getPost('rows'), true);
        if (! is_array($rows)) {
            return $this->jsonResult(['success' => false, 'message' => 'Format data baris tidak valid. Perubahan tidak disimpan.']);
        }

        $result = match ($category) {
            'ekonomi'  => ['success' => $this->assumption->saveEconomic($year, $rows, $userId), 'message' => "Asumsi ekonomi tahun {$year} berhasil disimpan."],
            'domestic' => ['success' => $this->assumption->saveSalesDomestic($year, $rows, $userId), 'message' => "Asumsi sales domestic tahun {$year} berhasil disimpan."],
            'export'   => ['success' => $this->assumption->saveSalesExport($year, $rows, $userId), 'message' => "Asumsi sales export tahun {$year} berhasil disimpan."],
            'other'    => ['success' => $this->assumption->saveOther($year, $rows, $userId), 'message' => "Asumsi lain (FOH) tahun {$year} berhasil disimpan."],
            default    => ['success' => false, 'message' => 'Kategori asumsi tidak dikenal.'],
        };

        return $this->jsonResult($result, 'master/assumption/save');
    }

    public function assumptionUpload()
    {
        $category = (string) $this->request->getPost('category');
        $year     = (int) ($this->request->getPost('year') ?: session()->get('year_code') ?: date('Y'));
        $file     = $this->request->getFile('excel_file');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            session()->setFlashdata('master_err', 'File tidak valid atau gagal diunggah.');

            return redirect()->back();
        }

        $userId = (int) (session()->get('user_id') ?? 0);

        try {
            $rows  = ExcelImporter::import($file, true);
            $saved = 0;

            if (empty($rows)) {
                session()->setFlashdata('master_err', 'File Excel kosong atau header tidak dikenali.');

                return redirect()->back();
            }

            switch ($category) {
                case 'ekonomi':
                    $clean = [];
                    foreach ($rows as $row) {
                        $typeId = $this->assumption->resolveTypeId(ExcelImporter::column($row, ['type', 'type_id', 'tipe']));
                        $desc   = trim((string) ExcelImporter::column($row, ['description', 'desc', 'deskripsi'], ''));
                        if ($typeId === null || $desc === '') {
                            continue;
                        }
                        $clean[] = [
                            'desc'    => $desc,
                            'type_id' => $typeId,
                            'value'   => ExcelImporter::toFloat(ExcelImporter::column($row, ['value', 'nilai'], 0)),
                        ];
                        $saved++;
                    }
                    $ok = $this->assumption->saveEconomic($year, $clean, $userId);
                    break;

                case 'domestic':
                case 'export':
                    $clean = [];
                    foreach ($rows as $row) {
                        $clean[] = [
                            'key_channel'     => trim((string) ExcelImporter::column($row, ['channel', 'key_channel'], '')),
                            'key_description' => trim((string) ExcelImporter::column($row, ['description', 'key_description'], '')),
                            'key_indicator'   => trim((string) ExcelImporter::column($row, ['indicator', 'key_indicator'], '')),
                            'key_value'       => ExcelImporter::toFloat(ExcelImporter::column($row, ['value', 'key_value'], 0)),
                        ];
                        $saved++;
                    }
                    $ok = $category === 'domestic'
                        ? $this->assumption->saveSalesDomestic($year, $clean, $userId)
                        : $this->assumption->saveSalesExport($year, $clean, $userId);
                    break;

                case 'other':
                    $clean = [];
                    foreach ($rows as $row) {
                        $clean[] = [
                            'id_assp'       => trim((string) ExcelImporter::column($row, ['id_assp', 'kode'], '')),
                            'tipe_group'    => trim((string) ExcelImporter::column($row, ['tipe_group', 'group', 'grouping'], '')),
                            'variable_text' => trim((string) ExcelImporter::column($row, ['variable', 'variable_text', 'description'], '')),
                            'value_text'    => ExcelImporter::toFloat(ExcelImporter::column($row, ['value', 'value_text'], 0)),
                        ];
                        $saved++;
                    }
                    $ok = $this->assumption->saveOther($year, $clean, $userId);
                    break;

                default:
                    session()->setFlashdata('master_err', 'Kategori asumsi tidak dikenal.');

                    return redirect()->back();
            }

            if (! $ok) {
                session()->setFlashdata('master_err', 'Gagal menyimpan data upload asumsi.');

                return redirect()->back();
            }

            session()->setFlashdata('master_msg', "Data asumsi {$category} tahun {$year} berhasil diimport ({$saved} baris).");
            AuditLog::log('UPLOAD', 'master/assumption/upload', "Upload asumsi {$category} tahun {$year} ({$saved} baris)");

            return redirect()->back();
        } catch (\Throwable $e) {
            log_message('error', 'Assumption upload: ' . $e->getMessage());

            session()->setFlashdata('master_err', 'Gagal membaca file Excel: ' . $e->getMessage());

            return redirect()->back();
        }
    }

    public function assumptionExport($category): ResponseInterface
    {
        $year  = (int) ($this->request->getGet('year') ?: session()->get('year_code') ?: date('Y'));
        $rows  = [];
        $headers = ['KATEGORI', 'DESKRIPSI', 'NILAI'];

        switch ($category) {
            case 'ekonomi':
                $headers = ['TYPE', 'DESCRIPTION', 'VALUE'];
                foreach ($this->assumption->getEconomic($year) as $r) {
                    $rows[] = [$r['type_desc'] ?? '', $r['desc'] ?? '', (float) ($r['value'] ?? 0)];
                }
                break;

            case 'domestic':
            case 'export':
                $headers = ['CHANNEL', 'DESCRIPTION', 'INDICATOR', 'VALUE'];
                $data = $category === 'domestic'
                    ? $this->assumption->getSalesDomestic($year)
                    : $this->assumption->getSalesExport($year);
                foreach ($data as $r) {
                    $rows[] = [$r['key_channel'] ?? '', $r['key_description'] ?? '', $r['key_indicator'] ?? '', (float) ($r['key_value'] ?? 0)];
                }
                break;

            case 'other':
                $headers = ['ID_ASSP', 'TIPE_GROUP', 'VARIABLE', 'VALUE'];
                foreach ($this->assumption->getOther($year) as $r) {
                    $rows[] = [$r['id_assp'] ?? '', $r['tipe_group'] ?? '', $r['variable_text'] ?? '', (float) ($r['value_text'] ?? 0)];
                }
                break;

            default:
                return $this->response->setStatusCode(404);
        }

        AuditLog::log('EXPORT', 'master/assumption/export', "Export asumsi {$category} tahun {$year} ke Excel");

        return ExcelExporter::export($headers, $rows, 'Assumption_' . $category . '_' . $year, 'Assumption');
    }

    /* ------------------------------------------------------------------
     * Periode AJAX
     * ------------------------------------------------------------------ */

    public function periodSave(): ResponseInterface
    {
        $data = $this->request->getPost();
        $result = $this->period->saveYear($data);

        return $this->jsonResult($result, 'master/period/save');
    }

    public function periodSetActive(): ResponseInterface
    {
        $year   = (int) $this->request->getPost('year_code');
        $result = $this->period->setActive($year);

        return $this->jsonResult($result, 'master/period/setActive');
    }

    public function periodSetLocked(): ResponseInterface
    {
        $year   = (int) $this->request->getPost('year_code');
        $locked = (bool) $this->request->getPost('locked');
        $result = $this->period->setLocked($year, $locked);

        return $this->jsonResult($result, 'master/period/setLocked');
    }

    public function periodDelete(): ResponseInterface
    {
        $year   = (int) $this->request->getPost('year_code');
        $result = $this->period->deleteYear($year);

        return $this->jsonResult($result, 'master/period/delete');
    }

    /* ------------------------------------------------------------------
     * Helper
     * ------------------------------------------------------------------ */

    /**
     * Resolusi tahun filter:
     * - Parameter GET ?year= diprioritaskan ('' artinya Semua Tahun).
     * - Tanpa param, pakai Working Year di session, TAPI hanya jika tahun itu
     *   benar-benar punya data (ada di daftar $years). Jika tidak, jatuh ke
     *   tahun terbaru yang punya data supaya tabel tidak tampil kosong.
     */
    private function resolveYearFilter(array $years): ?int
    {
        $yearParam = $this->request->getGet('year');
        if ($yearParam === null) {
            $sessionYear = session()->get('year_code');
            return ($sessionYear && in_array((int) $sessionYear, $years, true))
                ? (int) $sessionYear
                : ($years[0] ?? null);
        }

        return $yearParam === '' ? null : (int) $yearParam;
    }

    private function jsonResult(array $result, string $action = 'master/save'): ResponseInterface
    {
        if ($result['success']) {
            session()->setFlashdata('master_msg', $result['message']);
            AuditLog::saved($action, $result['message']);
        } else {
            session()->setFlashdata('master_err', $result['message']);
        }

        return $this->response->setJSON($result);
    }

    private function consumeFlash(): array
    {
        return [
            'success' => session()->getFlashdata('master_msg'),
            'error'   => session()->getFlashdata('master_err'),
        ];
    }
}
