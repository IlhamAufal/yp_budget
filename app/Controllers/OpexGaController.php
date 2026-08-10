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
 * Entry budget, actual, summary, dan breakdown sub-detail COA.
 * Upload/export Excel melalui Excel engine terpusat.
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

    /* ------------------------------------------------------------------
     * Index & Entry Budget
     * ------------------------------------------------------------------ */

    public function index(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_ga/index', [
            'title'       => 'OPEX GA Summary & Budget Table',
            'workingYear' => $workingYear,
            'costCenters' => $this->opexModel->getCostCenters(),
        ]);
    }

    /**
     * Halaman Entry Budget OPEX GA (Container Utama).
     */
    public function entryBudget(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_ga/entry_budget', [
            'title'       => 'Entry Budget OPEX GA',
            'workingYear' => $workingYear,
            'costCenters' => $this->opexModel->getCostCenters(),
            'coas'        => $this->opexModel->getCoas(),
        ]);
    }

    /**
     * Halaman Detail Breakdown Item Budget.
     */
    public function entryBudgetDetail(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $header = $this->request->getGet('header') ?? '';
        $dept   = $this->request->getGet('dept') ?? '';
        $idx    = $this->request->getGet('idx') ?? '1';

        return view('opex_ga/entry_budget_detail', [
            'title'         => 'Detail Budget OPEX GA',
            'workingYear'   => $workingYear,
            'headerAccount' => $header,
            'dept'          => $dept,
            'idx'           => $idx,
            'costCenters'   => $this->opexModel->getCostCenters(),
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
     * AJAX: header accounts OPEX GA beserta total budget.
     */
    public function getHeaderAccounts(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->request->getGet('dept') ?? '';

        if (empty($dept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cost Center wajib dipilih.']);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'headers' => $this->opexModel->getHeaderAccounts($year, $dept),
        ]);
    }

    /**
     * AJAX: matrix budget + actual per sub-account untuk sebuah header.
     */
    public function getDetailMatrix(): ResponseInterface
    {
        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept   = $this->request->getGet('dept') ?? '';
        $header = $this->request->getGet('header') ?? '';

        if (empty($dept) || empty($header)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter tidak lengkap.']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'matrix' => $this->opexModel->getDetailMatrix($year, $dept, $header),
        ]);
    }

    /**
     * AJAX: simpan budget OPEX GA (dengan concurrent access lock).
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

        $lock = (new AccessRestrict())->checkLock((string) $userId, 'opex-ga/entry', (int) $year);
        if (! $lock['allowed']) {
            return $this->response->setJSON(['status' => 'error', 'message' => $lock['message']]);
        }

        $result = $this->opexModel->saveBudget($year, $dept, $rows, $userId);

        if ($result['success']) {
            AuditLog::saved('opex-ga/saveBudget', "Budget OPEX GA {$year} CC {$dept} disimpan ({$result['count']} baris)");
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'count'   => $result['count'] ?? 0,
        ]);
    }

    /**
     * AJAX: simpan detail breakdown items (batch dari modal detail).
     */
    public function saveDetailItems(): ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Invalid method']);
        }

        $year        = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId      = (int) (session()->get('user_id') ?? 0);
        $entryDataId = (int) $this->request->getPost('entry_data_id');
        $itemsRaw    = $this->request->getPost('items');
        $items       = is_string($itemsRaw) ? (array) json_decode($itemsRaw, true) : (array) ($itemsRaw ?? []);

        // id_coa/dept sebagai hint bila parent entry budget belum ada
        // (model akan auto-create sebelum menyimpan detail).
        $result = $this->opexModel->saveDetailItemsBatch($entryDataId, $items, $userId, [
            'id_coa'    => (int) $this->request->getPost('id_coa'),
            'id_dept'   => (int) $this->request->getPost('dept'),
            'year_code' => (int) $year,
        ]);

        if ($result['success']) {
            AuditLog::saved('opex-ga/saveDetailItems', "Detail breakdown OPEX GA entry_data_id={$entryDataId} disimpan ({$result['count']} item)");
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

    /* ------------------------------------------------------------------
     * Actual Data
     * ------------------------------------------------------------------ */

    /**
     * Halaman Actual Data (legacy).
     */
    public function actual(): string
    {
        return $this->actualBudget();
    }

    /**
     * Halaman Actual Budget Manager (3 sub-tabs).
     */
    public function actualBudget(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_ga/actual_budget', [
            'title'       => 'OPEX GA Actual Data Manager',
            'workingYear' => $workingYear,
            'costCenters' => $this->opexModel->getCostCenters(),
        ]);
    }

    /**
     * AJAX: data actual OPEX GA per cost center.
     */
    public function getActualData(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->request->getPost('dept') ?? '';
        $page = max(1, (int) ($this->request->getPost('page') ?? 1));

        $perPage = 10;
        $all     = $this->opexModel->getActualData($year, $dept);

        return $this->response->setJSON([
            'status' => 'success',
            'rows'   => array_slice($all, ($page - 1) * $perPage, $perPage),
            'total'  => count($all),
        ]);
    }

    /**
     * Upload actual OPEX GA dari Excel.
     *
     * View actual_budget.php memanggil via fetch + FormData dan mengharapkan
     * response JSON {status, message} saat request AJAX.
     */
    public function uploadActual()
    {
        $isAjax = $this->request->isAJAX();

        $file = $this->request->getFile('excel_file');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $isAjax
                ? $this->response->setJSON(['status' => 'error', 'message' => 'File tidak valid atau gagal diunggah.'])
                : redirect()->back()->with('error', 'File tidak valid atau gagal diunggah.');
        }

        $ext = strtolower($file->getExtension());
        if (! in_array($ext, ['xlsx', 'xls'], true)) {
            return $isAjax
                ? $this->response->setJSON(['status' => 'error', 'message' => 'Format file tidak didukung. Gunakan .xlsx atau .xls.'])
                : redirect()->back()->with('error', 'Format file tidak didukung. Gunakan .xlsx atau .xls.');
        }

        try {
            $rows   = ExcelImporter::import($file, true);
            $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
            $userId = (int) (session()->get('user_id') ?? 0);

            $result = $this->opexModel->saveActualFromImport($year, $rows, $userId);

            if (! $result['success']) {
                return $isAjax
                    ? $this->response->setJSON(['status' => 'error', 'message' => $result['message']])
                    : redirect()->back()->with('error', $result['message']);
            }

            AuditLog::log('UPLOAD', 'opex-ga/uploadActual', 'Upload Excel actual OPEX GA: ' . $result['message']);

            if ($isAjax) {
                return $this->response->setJSON(['status' => 'success', 'message' => $result['message']]);
            }

            return redirect()->back()->with('success', $result['message']);
        } catch (\Throwable $e) {
            log_message('error', 'OPEX GA upload actual: ' . $e->getMessage());

            return $isAjax
                ? $this->response->setJSON(['status' => 'error', 'message' => 'Gagal membaca file Excel: ' . $e->getMessage()])
                : redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk upload actual OPEX GA.
     */
    public function downloadTemplate(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->request->getGet('cost_center') ?? $this->request->getGet('cc') ?? '';

        $monthKeys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        $headers = array_merge(
            ['ID COA', 'COST CENTER', 'DESKRIPSI'],
            ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
            ['TOTAL', 'NOTES']
        );

        // Ambil data actual existing sebagai dasar template
        $existing = $this->opexModel->getActualData($year, $dept);
        $data = array_map(function ($r) use ($monthKeys) {
            $line = [$r['id_coa'] ?? '', $r['id_dept'] ?? '', $r['coa_desc'] ?? ''];
            foreach ($monthKeys as $m) {
                $line[] = (float) ($r[$m] ?? 0);
            }
            $line[] = (float) ($r['total'] ?? 0);
            $line[] = $r['notes'] ?? '';

            return $line;
        }, $existing);

        // Jika tidak ada data, buat baris kosong dari master COA
        if (empty($data)) {
            $coas = $this->opexModel->getCoas();
            $data = array_map(function ($c) use ($dept) {
                return [$c['main_account'], $dept, $c['cost_center_desc'], 0,0,0,0,0,0,0,0,0,0,0,0,0, ''];
            }, $coas);
        }

        $timestamp = date('Ymd_His');
        $filename  = $dept
            ? "TEMPLATE_OPEX_GA_COSTCENTER_{$dept}_{$timestamp}"
            : "TEMPLATE_OPEX_GA_ALL_{$timestamp}";

        return ExcelExporter::export($headers, $data, $filename, 'Template Actual OPEX GA');
    }

    /* ------------------------------------------------------------------
     * Breakdown Sub-Detail COA (standar 1.5)
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

    public function processUpload()
    {
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

    public function exportExcel(): ResponseInterface
    {
        $year      = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept      = $this->request->getGet('cost_center') ?? $this->request->getGet('dept') ?? '';
        $monthKeys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        $rows = $this->opexModel->getEntryData($year, $dept);
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

        $filename = 'OPEX_GA_' . $year . ($dept ? "_{$dept}" : '');

        return ExcelExporter::export($headers, $data, $filename, 'OPEX GA');
    }

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
