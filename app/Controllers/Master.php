<?php

namespace App\Controllers;

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

    public function __construct()
    {
        $this->coa        = new CoaModel();
        $this->costCenter = new CostCenterModel();
        $this->department = new DepartmentModel();
        $this->period     = new PeriodModel();
        $this->product    = new ProductModel();
        $this->salaryMpp  = new SalaryMppModel();
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

        return $this->jsonResult($result);
    }

    public function coaToggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->coa->toggleStatus($id);

        return $this->jsonResult($result);
    }

    public function coaCopyYear(): ResponseInterface
    {
        $from   = (int) $this->request->getPost('from_year');
        $to     = (int) $this->request->getPost('to_year');
        $result = $this->coa->copyYear($from, $to);

        return $this->jsonResult($result);
    }

    /* ------------------------------------------------------------------
     * Cost Center AJAX
     * ------------------------------------------------------------------ */

    public function costCenterSave(): ResponseInterface
    {
        $data = $this->request->getPost();
        $id   = ! empty($data['id']) ? (int) $data['id'] : null;
        $result = $this->costCenter->saveCostCenter($data, $id);

        return $this->jsonResult($result);
    }

    public function costCenterToggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->costCenter->toggleStatus($id);

        return $this->jsonResult($result);
    }

    /* ------------------------------------------------------------------
     * Departemen AJAX
     * ------------------------------------------------------------------ */

    public function departmentSave(): ResponseInterface
    {
        $data = $this->request->getPost();
        $id   = ! empty($data['id']) ? (int) $data['id'] : null;
        $result = $this->department->saveDepartment($data, $id);

        return $this->jsonResult($result);
    }

    public function departmentToggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->department->toggleStatus($id);

        return $this->jsonResult($result);
    }

    /* ------------------------------------------------------------------
     * Product AJAX
     * ------------------------------------------------------------------ */

    public function productSave(): ResponseInterface
    {
        $data = $this->request->getPost();
        $id   = ! empty($data['id']) ? (int) $data['id'] : null;
        $result = $this->product->saveProduct($data, $id);

        return $this->jsonResult($result);
    }

    public function productToggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->product->toggleStatus($id);

        return $this->jsonResult($result);
    }

    /* ------------------------------------------------------------------
     * Salary MPP AJAX
     * ------------------------------------------------------------------ */

    public function salaryMppSave(): ResponseInterface
    {
        $data = $this->request->getPost();
        $id   = ! empty($data['id']) ? (int) $data['id'] : null;
        $result = $this->salaryMpp->saveSalaryMpp($data, $id);

        return $this->jsonResult($result);
    }

    public function salaryMppToggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->salaryMpp->toggleStatus($id);

        return $this->jsonResult($result);
    }

    /* ------------------------------------------------------------------
     * Periode AJAX
     * ------------------------------------------------------------------ */

    public function periodSave(): ResponseInterface
    {
        $data = $this->request->getPost();
        $result = $this->period->saveYear($data);

        return $this->jsonResult($result);
    }

    public function periodSetActive(): ResponseInterface
    {
        $year   = (int) $this->request->getPost('year_code');
        $result = $this->period->setActive($year);

        return $this->jsonResult($result);
    }

    public function periodSetLocked(): ResponseInterface
    {
        $year   = (int) $this->request->getPost('year_code');
        $locked = (bool) $this->request->getPost('locked');
        $result = $this->period->setLocked($year, $locked);

        return $this->jsonResult($result);
    }

    public function periodDelete(): ResponseInterface
    {
        $year   = (int) $this->request->getPost('year_code');
        $result = $this->period->deleteYear($year);

        return $this->jsonResult($result);
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

    private function jsonResult(array $result): ResponseInterface
    {
        if ($result['success']) {
            session()->setFlashdata('master_msg', $result['message']);
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
