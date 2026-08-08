<?php

namespace App\Controllers;

use App\Models\MppModel;
use App\Libraries\AccessRestrict;
use App\Libraries\AuditLog;
use CodeIgniter\HTTP\ResponseInterface;

class MppController extends BaseController
{
    protected MppModel $mppModel;

    public function __construct()
    {
        $this->mppModel = new MppModel();
    }

    /**
     * Halaman Utama Entry MPP Form
     */
    public function index(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $submitPeriod = $this->mppModel->getSubmitPeriod((string) $workingYear);
        $periodInfo   = '';
        if ($submitPeriod && ! empty($submitPeriod['begda']) && ! empty($submitPeriod['endda'])) {
            $begda = date('d M Y', strtotime($submitPeriod['begda']));
            $endda = date('d M Y', strtotime($submitPeriod['endda']));
            $periodInfo = "Periode submit data MPP dimulai pada {$begda} s/d {$endda}";
        }

        $data = [
            'title'           => 'Man Power Planning - Form Entry',
            'workingYear'     => $workingYear,
            'departments'     => $this->getDepartmentList(),
            'costCenterList'  => $this->mppModel->getCostCentersActive(),
            'periodInfo'      => $periodInfo,
        ];

        return view('mpp/entry', $data);
    }

    /**
     * Halaman Summary Headcount & Kalkulasi OPEX
     */
    public function summary(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 10;
        $total   = $this->mppModel->countSummaryWithSalary($workingYear);
        $offset  = ($page - 1) * $perPage;

        $data = [
            'title'       => 'Summary Headcount & Salary Integration',
            'workingYear' => $workingYear,
            'departments' => $this->getDepartmentList(),
            'summary'     => $this->mppModel->getSummaryWithSalary($workingYear, null, $offset, $perPage),
            'page'        => $page,
            'perPage'     => $perPage,
            'total'       => $total,
        ];

        return view('mpp/summary', $data);
    }

    /**
     * AJAX Process Sync to OPEX Engine
     */
    public function syncToOpex(): ResponseInterface
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $success = $this->mppModel->syncToOpex($workingYear);

        if ($success) {
            AuditLog::log('SYNC', 'mpp/syncToOpex', "Alokasi gaji MPP {$workingYear} ke OPEX Engine berhasil");

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "Proses alokasi Gaji ke OPEX Engine Tahun {$workingYear} berhasil dilakukan!"
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Terjadi kesalahan saat memproses data ke OPEX.'
        ]);
    }

    // -------------------------------------------------------------------
    // AJAX Endpoints untuk Integrasi Frontend-Backend
    // -------------------------------------------------------------------

    /**
     * AJAX: Daftar Cost Center / Department aktif
     */
    public function getCostCenters(): ResponseInterface
    {
        $costCenters = $this->mppModel->getCostCentersActive();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $costCenters,
        ]);
    }

    /**
     * AJAX: Matriks headcount per kategori (tipe_mpp) untuk Cost Center tertentu
     */
    public function getMppMatrix(): ResponseInterface
    {
        $yearCode = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $idDept   = $this->request->getGet('id_dept');

        if (empty($idDept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Department ID wajib dipilih.']);
        }

        $matrix = $this->mppModel->getMppMatrix($yearCode, (string) $idDept);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $matrix,
        ]);
    }

    /**
     * AJAX: Detail breakdown 12 bulan per kategori (tipe_mpp)
     */
    public function getMppBreakdown(): ResponseInterface
    {
        $yearCode = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $idDept   = $this->request->getGet('id_dept');
        $tipeId   = (int) $this->request->getGet('tipe_id');

        if (empty($idDept) || $tipeId <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter tidak lengkap.']);
        }

        $detail = $this->mppModel->getMppCategoryDetail($yearCode, (string) $idDept, $tipeId);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $detail,
        ]);
    }

    /**
     * AJAX: Simpan data MPP per kategori dengan transaksi
     */
    public function saveMppBreakdown(): ResponseInterface
    {
        $yearCode = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $json     = $this->request->getJSON(true);

        $idDept = $json['id_dept'] ?? null;
        $tipeId = (int) ($json['tipe_id'] ?? 0);
        $months = $json['months'] ?? [];
        $note   = $json['note'] ?? '';

        if (empty($idDept) || $tipeId <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter tidak lengkap.']);
        }

        if (count($months) !== 12) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data bulan harus 12 kolom.']);
        }

        // Concurrent Access Locking
        $userId = (int) (session()->get('user_id') ?? 0);
        $lock   = (new AccessRestrict())->checkLock((string) $userId, 'mpp/entry', (int) $yearCode);
        if (! $lock['allowed']) {
            return $this->response->setJSON(['status' => 'error', 'message' => $lock['message']]);
        }

        $success = $this->mppModel->saveMppCategory($yearCode, (string) $idDept, $tipeId, $months, $note);

        if ($success) {
            AuditLog::saved('mpp/saveMppBreakdown', "MPP {$yearCode} CC {$idDept} Tipe {$tipeId} disimpan");

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data Man Power Planning berhasil disimpan!',
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data ke database.']);
    }

    /**
     * AJAX: Data ringkasan untuk tab View MPP Data
     */
    public function getViewData(): ResponseInterface
    {
        $yearCode = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $idDept   = $this->request->getGet('id_dept');

        if (empty($idDept)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'data'    => [],
                'totals'  => array_fill(0, 13, 0),
            ]);
        }

        $data = $this->mppModel->getViewSummary($yearCode, (string) $idDept);

        // Hitung grand totals per bulan (indeks 0..11 = Jan..Dec, 12 = TOTAL)
        $totals = array_fill(0, 13, 0);
        foreach ($data as $row) {
            for ($m = 1; $m <= 12; $m++) {
                $totals[$m - 1] += (float) ($row['m' . $m] ?? 0);
            }
            $totals[12] += (float) ($row['grand_total'] ?? 0);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data,
            'totals' => $totals,
        ]);
    }

    // -------------------------------------------------------------------

    /**
     * Helper Private untuk Master Department
     */
    private function getDepartmentList(): array
    {
        $db = \Config\Database::connect();
        return $db->table('gw_plan__master_department')
            ->select('id_dept, dept_code AS department_code, dept_desc AS department_name')
            ->where('status', 'A')
            ->orderBy('dept_code', 'ASC')
            ->get()->getResultArray();
    }
}