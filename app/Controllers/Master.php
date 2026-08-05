<?php

namespace App\Controllers;

use App\Models\CoaModel;
use App\Models\CostCenterModel;
use App\Models\DepartmentModel;
use App\Models\PeriodModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Master Data — COA, Cost Center, Departemen, dan Periode (Tahun Anggaran).
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

    public function __construct()
    {
        $this->coa        = new CoaModel();
        $this->costCenter = new CostCenterModel();
        $this->department = new DepartmentModel();
        $this->period     = new PeriodModel();
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
        $filters = [
            'search' => $this->request->getGet('search') ?? '',
            'type'   => $this->request->getGet('type') ?? '',
            'year'   => $this->request->getGet('year') ?: session()->get('year_code'),
            'status' => $this->request->getGet('status') ?? 'A',
        ];

        return view('master/coa', [
            'title'   => 'Master Data - Chart of Account (COA)',
            'rows'    => $this->coa->getAll($filters),
            'years'   => $this->coa->getYears(),
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

        return view('master/cost_center', [
            'title'   => 'Master Data - Cost Center',
            'rows'    => $this->costCenter->getAll($filters),
            'years'   => $this->costCenter->getYears(),
            'types'   => $this->costCenter->getTypes(),
            'filters' => $filters,
            'flash'   => $this->consumeFlash(),
        ]);
    }

    public function department()
    {
        $filters = [
            'search' => $this->request->getGet('search') ?? '',
            'status' => $this->request->getGet('status') ?? 'A',
        ];

        return view('master/department', [
            'title'   => 'Master Data - Departemen',
            'rows'    => $this->department->getAll($filters),
            'filters' => $filters,
            'flash'   => $this->consumeFlash(),
        ]);
    }

    public function period()
    {
        return view('master/period', [
            'title' => 'Master Data - Periode (Tahun Anggaran)',
            'rows'  => $this->period->getAllYears(),
            'flash' => $this->consumeFlash(),
        ]);
    }

    /* ------------------------------------------------------------------
     * COA
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
     * Cost Center
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
     * Departemen
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
     * Periode
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
     * Bungkus hasil menjadi JSON. Simpan pesan ke flashdata agar tampil
     * setelah halaman di-reload oleh JS.
     */
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
