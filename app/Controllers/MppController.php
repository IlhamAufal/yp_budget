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
            'costCenterList'  => $this->getCostCenterSapList(),
            'periodInfo'      => $periodInfo,
        ];

        return view('mpp/entry', $data);
    }

    /**
     * Halaman Summary Headcount
     */
    public function summary(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $data = [
            'title'          => 'Man Power Planning - Summary Headcount',
            'workingYear'    => $workingYear,
            'costCenterList' => $this->getCostCenterSapList(),
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
     * AJAX: Detail breakdown per posisi untuk sebuah kategori (tipe_mpp)
     */
    public function getMppBreakdown(): ResponseInterface
    {
        $yearCode = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $idDept   = $this->request->getGet('id_dept');
        $tipeId   = (int) $this->request->getGet('tipe_id');

        if (empty($idDept) || $tipeId <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter tidak lengkap.']);
        }

        $data = $this->mppModel->getPositionsByTipe($yearCode, (string) $idDept, $tipeId);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    /**
     * AJAX: Simpan data MPP multi-posisi per kategori
     */
    public function saveMppBreakdown(): ResponseInterface
    {
        $yearCode = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $json     = $this->request->getJSON(true);

        $idDept = $json['id_dept'] ?? null;
        $tipeId = (int) ($json['tipe_id'] ?? 0);
        $rows   = $json['rows'] ?? [];
        $note   = $json['note'] ?? '';

        if (empty($idDept) || $tipeId <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter tidak lengkap.']);
        }

        // Concurrent Access Locking
        $userId = (int) (session()->get('user_id') ?? 0);
        $lock   = (new AccessRestrict())->checkLock((string) $userId, 'mpp/entry', (int) $yearCode);
        if (! $lock['allowed']) {
            return $this->response->setJSON(['status' => 'error', 'message' => $lock['message']]);
        }

        $success = $this->mppModel->saveMppPositions($yearCode, (string) $idDept, $tipeId, $rows, $note);

        if ($success) {
            AuditLog::saved('mpp/saveMppBreakdown', "MPP {$yearCode} CC {$idDept} Tipe {$tipeId} disimpan (" . count($rows) . " posisi)");

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data Man Power Planning berhasil disimpan!',
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data ke database.']);
    }

    /**
     * AJAX: Hapus transaksi satu posisi MPP tanpa menghapus master posisi.
     */
    public function deleteMppPosition(): ResponseInterface
    {
        $json = $this->request->getJSON(true);
        if (! is_array($json)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'success' => false,
                'message' => 'Payload JSON tidak valid.',
            ]);
        }

        $idDept    = trim((string) ($json['id_dept'] ?? ''));
        $tipeId    = filter_var($json['tipe_id'] ?? null, FILTER_VALIDATE_INT);
        $positionId = filter_var($json['position_id'] ?? null, FILTER_VALIDATE_INT);

        if ($idDept === '' || ! preg_match('/^\d+$/', $idDept) || $tipeId === false || $tipeId <= 0 || $positionId === false || $positionId <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'success' => false,
                'message' => 'Department, tipe, dan posisi wajib berupa identifier yang valid.',
            ]);
        }

        $yearCode = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $userId   = (int) (session()->get('user_id') ?? 0);
        $lock     = (new AccessRestrict())->checkLock((string) $userId, 'mpp/entry', (int) $yearCode);
        if (! $lock['allowed']) {
            return $this->response->setStatusCode(409)->setJSON([
                'status'  => 'error',
                'success' => false,
                'message' => $lock['message'],
            ]);
        }

        $result = $this->mppModel->deleteMppPosition($yearCode, $idDept, (int) $tipeId, (int) $positionId);
        if (! $result['valid']) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'success' => false,
                'message' => $result['message'],
            ]);
        }

        if (! $result['success']) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'success' => false,
                'message' => $result['message'] ?? 'Penghapusan entry MPP gagal.',
            ]);
        }

        AuditLog::log(
            'DELETE',
            'mpp/deleteMppPosition',
            "Entry posisi MPP {$yearCode} CC {$idDept} tipe {$tipeId} posisi {$positionId} dihapus (" . $result['deleted'] . ' header)'
        );

        return $this->response->setJSON([
            'status'  => 'success',
            'success' => true,
            'message' => 'Entry posisi MPP berhasil dihapus.',
            'deleted' => $result['deleted'],
        ]);
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

    /**
     * Helper: Cost Center list dari gw_plan__master_cost_center dengan cost_center_sap
     */
    private function getCostCenterSapList(): array
    {
        $db = \Config\Database::connect();
        return $db->table('gw_plan__master_cost_center')
            ->select("cost_center, COALESCE(NULLIF(cost_center_sap,''), CAST(cost_center AS CHAR)) AS cost_center_sap, cost_desc")
            ->where('status', 'A')
            ->orderBy('cost_center', 'ASC')
            ->get()
            ->getResultArray();
    }
}