<?php

namespace App\Controllers;

use App\Models\OpexGaModel;
use App\Services\OpexReportService;
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
    protected OpexReportService $reportService;
    protected $db;

    public function __construct()
    {
        $this->opexModel = new OpexGaModel();
        $this->reportService = new OpexReportService();
        $this->db = \Config\Database::connect();
    }

    /**
     * Baca + resolve param dept (SAP code dari dropdown) ke cost_center internal.
     */
    private function resolveDeptParam(?string $dept): string
    {
        return (string) ($this->opexModel->resolveDept($dept) ?? '');
    }

    private function eligibleDeptParam(?string $dept, string $year): ?string
    {
        return $this->opexModel->resolveEligibleDept($dept, $year, (array) session()->get('auth_obj'));
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
            'costCenters' => $this->opexModel->getCostCenters($workingYear, (array) session()->get('auth_obj')),
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
            'costCenters' => $this->opexModel->getCostCenters($workingYear, (array) session()->get('auth_obj')),
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
            'costCenters'   => $this->opexModel->getCostCenters($workingYear, (array) session()->get('auth_obj')),
        ]);
    }

    /**
     * AJAX: data budget OPEX GA tersimpan per cost center.
     */
    public function getEntryData(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->eligibleDeptParam($this->request->getGet('dept'), (string) $year);

        if ($dept === null) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Cost Center tidak memiliki akses atau periode OPEX GA tidak aktif.']);
        }

        $viewData = $this->opexModel->getEntryData((string) $year, $dept);

        return $this->response->setJSON(array_merge([
            'status' => 'success',
        ], $viewData));
    }

    /**
     * AJAX: header accounts OPEX GA beserta total budget.
     */
    public function getHeaderAccounts(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->eligibleDeptParam($this->request->getGet('dept'), (string) $year);

        if ($dept === null) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Cost Center tidak memiliki akses atau periode OPEX GA tidak aktif.']);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'headers' => $this->opexModel->getHeaderAccounts((string) $year, $dept),
        ]);
    }

    /**
     * AJAX: matrix budget + actual per sub-account untuk sebuah header.
     */
    public function getDetailMatrix(): ResponseInterface
    {
        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept   = $this->eligibleDeptParam($this->request->getGet('dept'), (string) $year);
        $header = trim((string) ($this->request->getGet('header') ?? ''));
        $idx    = trim((string) ($this->request->getGet('idx') ?? ''));

        if ($dept === null || $header === '' || $idx === '') {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Parameter dept, header, dan idx wajib valid.']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'header' => ['cost_center_header' => $header, 'id_cost_header' => $idx],
            'matrix' => $this->opexModel->getDetailMatrix((string) $year, $dept, $header, OpexGaModel::SOURCE, $idx),
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
        $dept   = $this->eligibleDeptParam($this->request->getPost('dept'), (string) $year);
        $header = trim((string) ($this->request->getPost('header') ?? ''));
        $idx    = trim((string) ($this->request->getPost('idx') ?? $this->request->getPost('id_cost_header') ?? ''));

        $rowsRaw = $this->request->getPost('rows');
        $rows = is_string($rowsRaw) ? json_decode($rowsRaw, true) : $rowsRaw;
        $rows = is_array($rows) ? $rows : [];

        if ($dept === null) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Cost Center tidak memiliki akses atau periode OPEX GA tidak aktif.']);
        }
        if ($header === '' || $idx === '') {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Header dan idx wajib dikirim.']);
        }

        $lock = (new AccessRestrict())->checkLock((string) $userId, 'opex-ga/entry', (int) $year);
        if (! $lock['allowed']) {
            return $this->response->setJSON(['status' => 'error', 'message' => $lock['message']]);
        }

        $result = $this->opexModel->saveBudget((string) $year, $dept, $header, $idx, $rows, $userId);

        if ($result['success']) {
            AuditLog::saved('opex-ga/saveBudget', "Budget OPEX GA {$year} CC {$dept} header {$idx} disimpan ({$result['count']} baris)");
        }

        return $this->response->setJSON(array_merge([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'count' => $result['count'] ?? 0,
        ], $result));
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
        $items       = is_string($itemsRaw) ? json_decode($itemsRaw, true) : $itemsRaw;
        $items       = is_array($items) ? $items : [];
        $deptInput   = trim((string) $this->request->getPost('dept'));
        $dept        = $this->eligibleDeptParam($deptInput, (string) $year);
        $header      = trim((string) $this->request->getPost('header'));
        $headerId    = trim((string) ($this->request->getPost('idx') ?? $this->request->getPost('id_cost_header') ?? ''));

        if ($dept === null || $header === '' || $headerId === '') {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Cost Center, header, dan idx tidak eligible atau tidak lengkap.']);
        }

        $result = $this->opexModel->saveDetailItemsBatch($entryDataId, $items, $userId, [
            'id_coa'         => (int) $this->request->getPost('id_coa'),
            'id_dept'        => (int) $dept,
            'year_code'      => (int) $year,
            'header'         => $header,
            'id_cost_header' => $headerId,
        ]);

        if ($result['success']) {
            AuditLog::saved('opex-ga/saveDetailItems', "Detail breakdown OPEX GA entry_data_id={$entryDataId} header={$headerId} disimpan ({$result['count']} item)");
        }

        return $this->response->setJSON(array_merge([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'count' => $result['count'] ?? 0,
        ], $result));
    }

    /**
     * AJAX: workflow submit budget OPEX GA.
     */
    public function submitBudget(): ResponseInterface
    {
        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId = (int) (session()->get('user_id') ?? 0);
        $dept   = $this->eligibleDeptParam($this->request->getPost('dept'), (string) $year);
        if ($dept === null) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Cost Center tidak eligible.']);
        }

        $result = $this->opexModel->submitBudget((string) $year, $dept, $userId);

        if ($result['success']) {
            AuditLog::submitted('opex-ga/submitBudget', "Budget OPEX GA {$year} CC {$dept} disubmit");
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }

    /* ------------------------------------------------------------------
     * Department Report
     * ------------------------------------------------------------------ */

    public function reportDepartment(): string
    {
        $year = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $selected = trim((string) ($this->request->getGet('cost_center') ?? ''));
        $report = $selected !== '' ? $this->buildDepartmentReport($year, $selected) : $this->reportService->normalizeRows([]);

        return view('reports/opex_department', [
            'title'        => 'OPEX GA - Report Department',
            'module'       => 'OPEX GA',
            'workingYear'  => $year,
            'costCenters'  => $this->opexModel->getCostCenters($year, (array) session()->get('auth_obj')),
            'selected'     => $selected,
            'report'       => $report,
            'dataUrl'      => base_url('opex-ga/report-data'),
        ]);
    }

    public function reportData(): ResponseInterface
    {
        $year = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $dept = trim((string) ($this->request->getVar('cost_center') ?? $this->request->getVar('dept') ?? ''));

        if ($dept === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Cost Center wajib dipilih.',
            ]);
        }

        $resolved = $this->opexModel->resolveEligibleDept($dept, $year, (array) session()->get('auth_obj'));
        if ($resolved === null) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Cost Center tidak memiliki akses atau periode OPEX GA tidak aktif.',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'year'   => $year,
            'report' => $this->reportService->normalizeRows($this->opexModel->getEntryData($year, $resolved)['rows'] ?? []),
        ]);
    }

    private function buildDepartmentReport(string $year, string $dept): array
    {
        $resolved = $this->opexModel->resolveEligibleDept($dept, $year, (array) session()->get('auth_obj'));
        if ($resolved === null) {
            return $this->reportService->normalizeRows([]);
        }

        return $this->reportService->normalizeRows($this->opexModel->getEntryData($year, $resolved)['rows'] ?? []);
    }

    /* ------------------------------------------------------------------
     * Actual Data
     * ------------------------------------------------------------------ */
    public function actualBudget(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_ga/actual_budget', [
            'title'       => 'OPEX GA Actual Data Manager',
            'workingYear' => $workingYear,
            'costCenters' => $this->opexModel->getCostCenters($workingYear, (array) session()->get('auth_obj')),
        ]);
    }

    /**
     * AJAX: data actual OPEX GA per cost center (server-side pagination).
     *
     * POST: dept, page, perPage, search.
     * Query memakai LIMIT/OFFSET di Model — payload per halaman saja.
     */
    public function getActualData(): ResponseInterface
    {
        $year    = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept    = $this->resolveDeptParam(trim((string) ($this->request->getPost('dept') ?? '')));
        $page    = max(1, (int) ($this->request->getPost('page') ?? 1));
        $perPage = max(5, min(100, (int) ($this->request->getPost('perPage') ?? 25)));
        $search  = trim((string) ($this->request->getPost('search') ?? ''));

        if ($dept === '') {
            return $this->response->setJSON(['status' => 'success', 'rows' => [], 'total' => 0]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'rows'   => $this->opexModel->getActualDataPaginated($year, $dept, ($page - 1) * $perPage, $perPage, $search),
            'total'  => $this->opexModel->countActualData($year, $dept, $search),
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
        $parent = $this->db->table('yp_plan__trans_budget_entry_data')->where('id', $entryDataId)->get()->getRowArray();
        $year = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        if (! $parent || $this->eligibleDeptParam((string) ($parent['id_dept'] ?? ''), $year) === null) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Entry detail tidak dapat diakses.']);
        }

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
     * GET opexga/upload-modal — konten form upload Excel untuk global modal.
     * Params: type (whitelist, default 'opex_summary').
     */
    public function uploadModalForm(): string
    {
        $type = (string) ($this->request->getGet('type') ?? 'opex_summary');
        if (! in_array($type, ['opex_summary'], true)) {
            $type = 'opex_summary';
        }

        return view('opex_ga/upload_modal_content', ['uploadType' => $type]);
    }

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
        $dept      = $this->eligibleDeptParam($this->request->getGet('cost_center') ?? $this->request->getGet('dept') ?? '', (string) $year);
        $monthKeys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
        if ($dept === null) {
            return $this->response->setStatusCode(403)->setBody('Cost Center tidak eligible.');
        }

        $viewData = $this->opexModel->getEntryData((string) $year, $dept);
        $rows = $viewData['rows'] ?? [];
        $data = array_map(function (array $r) use ($monthKeys) {
            $line = [$r['account'] ?? $r['id_coa'], $r['description'] ?? '', $r['cost_center_header'] ?? ''];
            foreach ($monthKeys as $m) {
                $line[] = (float) ($r['budget_' . $m] ?? 0);
            }
            $line[] = (float) ($r['budget_total'] ?? 0);

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

        $viewData = $this->opexModel->getEntryData((string) $year, $dept);
        $rows = $viewData['rows'] ?? [];
        $data = array_map(function (array $r) use ($monthKeys) {
            $line = [$r['id_coa'], $r['id_dept'] ?? $r['cost_center_header'], $r['description'] ?? ''];
            foreach ($monthKeys as $m) {
                $line[] = (float) ($r['budget_' . $m] ?? 0);
            }
            $line[] = (float) ($r['budget_total'] ?? 0);
            $line[] = '';

            return $line;
        }, $rows);

        return ExcelExporter::export($headers, $data, 'TEMPLATE_OPEX_GA_' . $dept . '_' . $year, 'Template');
    }
}
