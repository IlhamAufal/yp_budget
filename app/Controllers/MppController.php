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
        
        $data = [
            'title'       => 'Man Power Planning - Form Entry',
            'workingYear' => $workingYear,
            'departments' => $this->getDepartmentList()
        ];

        return view('mpp/entry', $data);
    }

    /**
     * Dynamic AJAX Handler: Load Partial View Form Table
     */
    public function getEntryTable(): ResponseInterface
    {
        $yearCode   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $idDept     = $this->request->getGet('id_dept');
        $isNewlines = filter_var($this->request->getGet('is_newlines'), FILTER_VALIDATE_BOOLEAN);

        if (empty($idDept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Department ID Wajib dipilih!']);
        }

        $entryData = $this->mppModel->getEntryData($yearCode, $idDept, $isNewlines);

        $html = view('mpp/entry_table_mpp', [
            'entryData'  => $entryData,
            'isNewlines' => $isNewlines
        ]);

        return $this->response->setJSON(['status' => 'success', 'html' => $html]);
    }

    /**
     * Dynamic AJAX Handler: Save Budget Data
     */
    public function saveBudget(): ResponseInterface
    {
        $yearCode   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $idDept     = $this->request->getPost('id_dept');
        $isNewlines = filter_var($this->request->getPost('is_newlines'), FILTER_VALIDATE_BOOLEAN);
        $details    = $this->request->getPost('details') ?? [];

        if (empty($idDept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan, Cost Center / Department kosong.']);
        }

        // PRD 1.7 — Concurrent Access Locking sebelum proses simpan
        $userId = (int) (session()->get('user_id') ?? 0);
        $lock   = (new AccessRestrict())->checkLock((string) $userId, 'mpp/entry', (int) $yearCode);
        if (! $lock['allowed']) {
            return $this->response->setJSON(['status' => 'error', 'message' => $lock['message']]);
        }

        $success = $this->mppModel->saveMppBudget($yearCode, $idDept, $details, $isNewlines);

        if ($success) {
            AuditLog::saved('mpp/saveBudget', "MPP {$yearCode} CC {$idDept} disimpan");

            return $this->response->setJSON(['status' => 'success', 'message' => 'Data Man Power Planning berhasil disimpan!']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data ke database.']);
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