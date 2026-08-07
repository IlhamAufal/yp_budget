<?php

namespace App\Controllers;

use App\Models\OpexGaModel;
use App\Libraries\AccessRestrict;
use App\Libraries\AuditLog;
use App\Libraries\ExcelExporter;
use App\Libraries\ExcelImporter;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * OpexGaController — OPEX GA (PRD Phase 2.1 & 2.3).
 *
 * Entry budget nyata + workflow submit + breakdown sub-detail COA (AJAX),
 * serta upload/export Excel melalui Excel engine terpusat.
 */
class OpexGaController extends BaseController
{
    protected OpexGaModel $opexModel;
    protected $db;

    public function __construct()
    {
        $this->opexModel = new OpexGaModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * 1. OPEX GA Summary & Main Table View
     */
    public function index(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_ga/index', [
            'title'       => 'OPEX GA Summary & Budget Table',
            'workingYear' => $workingYear,
            'opexData'    => $this->opexModel->getEntryData($workingYear),
        ]);
    }

    /**
     * 2. Actual OPEX Data Management Page
     */
    public function actual(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_ga/actual', [
            'title'       => 'OPEX GA Actual Data',
            'workingYear' => $workingYear,
            'costCenters' => $this->opexModel->getCostCenters(),
            'actuals'     => $this->opexModel->getActualData($workingYear),
        ]);
    }

    /**
     * 3. Entry Budget & Detail Allocation Matrix
     */
    public function entryBudget(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_ga/entry_budget', [
            'title'       => 'Entry Budget OPEX GA',
            'workingYear' => $workingYear,
            'costCenters' => $this->opexModel->getCostCenters(),
            'coas'        => $this->opexModel->getCoas(),
            'budgetItems' => $this->opexModel->getEntryData($workingYear),
        ]);
    }

    /**
     * AJAX: data budget OPEX GA tersimpan per cost center.
     */
    public function getEntryData(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->request->getGet('dept') ?? '';

        if (empty($dept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cost Center wajib dipilih.']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'rows'   => $this->opexModel->getEntryData($year, $dept),
        ]);
    }

    /**
     * AJAX: simpan budget OPEX GA (dengan concurrent access lock standar 1.7).
     */
    public function saveBudget(): ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Invalid method']);
        }

        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId = (int) (session()->get('user_id') ?? 0);
        $dept   = $this->request->getPost('dept') ?? '';

        $rowsRaw = $this->request->getPost('rows');
        $rows    = is_string($rowsRaw)
            ? (array) json_decode($rowsRaw, true)
            : (array) ($rowsRaw ?? []);

        if (empty($dept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cost Center wajib dipilih.']);
        }

        // PRD 1.7 — Concurrent Access Locking sebelum proses simpan
        $lock = (new AccessRestrict())->checkLock((string) $userId, 'opex-ga/entry', (int) $year);
        if (! $lock['allowed']) {
            return $this->response->setJSON(['status' => 'error', 'message' => $lock['message']]);
        }

        $result = $this->opexModel->saveBudget($year, $dept, $rows, $userId);

        if ($result['success']) {
            $savedCount = $result['count'] ?? 0;
            AuditLog::saved('opex-ga/saveBudget', "Budget OPEX GA {$year} CC {$dept} disimpan ({$savedCount} baris)");
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'count'   => $result['count'] ?? 0,
        ]);
    }

    /**
     * AJAX: workflow submit budget OPEX GA.
     */
    public function submitBudget(): ResponseInterface
    {
        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId = (int) (session()->get('user_id') ?? 0);
        $dept   = $this->request->getPost('dept') ?? '';

        $result = $this->opexModel->submitBudget($year, $dept, $userId);

        if ($result['success']) {
            AuditLog::submitted('opex-ga/submitBudget', "Budget OPEX GA {$year} CC {$dept} disubmit");
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }

    /**
     * AJAX: data actual OPEX GA per cost center.
     */
    public function getActualData(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->request->getPost('dept') ?? '';

        return $this->response->setJSON([
            'status' => 'success',
            'rows'   => $this->opexModel->getActualData($year, $dept),
        ]);
    }

    /* ------------------------------------------------------------------
     * Breakdown Sub-Detail COA (standar 1.5) — AJAX partial update
     * ------------------------------------------------------------------ */

    public function getDetailItems(): ResponseInterface
    {
        $entryDataId = (int) $this->request->getVar('entry_data_id');

        return $this->response->setJSON([
            'status' => 'success',
            'items'  => $this->opexModel->getDetailItems($entryDataId),
        ]);
    }

    public function saveDetail(): ResponseInterface
    {
        $userId = (int) (session()->get('user_id') ?? 0);
        $result = $this->opexModel->saveDetailItem((array) $this->request->getPost(), $userId);

        if ($result['success']) {
            AuditLog::saved('opex-ga/saveDetail', 'Breakdown item OPEX GA ditambah/ubah');
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'id'      => $result['id'] ?? null,
        ]);
    }

    public function deleteDetail(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->opexModel->deleteDetailItem($id);

        if ($result['success']) {
            AuditLog::log('DELETE', 'opex-ga/deleteDetail', "Breakdown item OPEX GA dihapus (id {$id})");
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }

    /* ------------------------------------------------------------------
     * Excel (Phase 2.3 — Excel engine terpusat)
     * ------------------------------------------------------------------ */

    /**
     * Upload actual OPEX GA (.xlsx) → import ke yp_plan__trans_budget_actual.
     */
    public function processUpload()
    {
        $type = $this->request->getPost('upload_type'); // 'actual' atau 'budget'
        $file = $this->request->getFile('excel_file');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'File tidak valid atau gagal diunggah.');
        }

        try {
            $rows   = ExcelImporter::import($file, true);
            $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
            $userId = (int) (session()->get('user_id') ?? 0);

            $result = $this->opexModel->saveActualFromImport($year, $rows, $userId);

            if (! $result['success']) {
                return redirect()->back()->with('error', $result['message']);
            }

            AuditLog::log('UPLOAD', 'opex-ga/processUpload', 'Upload Excel actual OPEX GA: ' . $result['message']);

            return redirect()->back()->with('success', $result['message']);
        } catch (\Throwable $e) {
            log_message('error', 'OPEX GA upload: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export laporan OPEX GA ke .xlsx via ExcelExporter.
     */
    public function exportExcel(): ResponseInterface
    {
        $year      = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $monthKeys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        $rows = $this->opexModel->getEntryData($year);
        $data = array_map(function ($r) use ($monthKeys) {
            $line = [$r['acct_code'] ?? $r['id_coa'], $r['coa_desc'] ?? '', $r['id_dept']];
            foreach ($monthKeys as $m) {
                $line[] = (float) ($r[$m] ?? 0);
            }
            $line[] = (float) $r['total'];

            return $line;
        }, $rows);

        $headers = array_merge(
            ['MAIN ACCOUNT', 'DESKRIPSI', 'COST CENTER'],
            ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
            ['TOTAL']
        );

        return ExcelExporter::export($headers, $data, 'OPEX_GA_' . $year, 'OPEX GA');
    }

    /**
     * Export template upload actual OPEX GA per cost center.
     */
    public function exportTemplateOpexGa($dept = null): ResponseInterface
    {
        $year      = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $monthKeys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        $headers = array_merge(
            ['ID COA', 'COST CENTER', 'DESKRIPSI'],
            ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
            ['TOTAL', 'NOTES']
        );

        $rows = $this->opexModel->getEntryData($year, $dept);
        $data = array_map(function ($r) use ($monthKeys) {
            $line = [$r['id_coa'], $r['id_dept'], $r['coa_desc'] ?? ''];
            foreach ($monthKeys as $m) {
                $line[] = (float) ($r[$m] ?? 0);
            }
            $line[] = (float) $r['total'];
            $line[] = '';

            return $line;
        }, $rows);

        return ExcelExporter::export($headers, $data, 'TEMPLATE_OPEX_GA_' . $dept . '_' . $year, 'Template');
    }
}
