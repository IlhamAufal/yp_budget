<?php

namespace App\Controllers;

use App\Models\OpexSellingModel;
use App\Services\OpexReportService;
use App\Libraries\ExcelExporter;
use App\Libraries\ExcelImporter;
use App\Libraries\AccessRestrict;
use App\Libraries\AuditLog;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * OpexSellingController — OPEX Selling (PRD Phase 2.1 & 2.3).
 *
 * Entry budget, actual, report department, upload actual via Excel engine
 * terpusat, dan Service Export Excel.
 */
class OpexSellingController extends BaseController
{
    protected OpexSellingModel $opexModel;
    protected OpexReportService $reportService;
    protected $db;

    public function __construct()
    {
        $this->opexModel = new OpexSellingModel();
        $this->reportService = new OpexReportService();
        $this->db = \Config\Database::connect();
    }

    /* ------------------------------------------------------------------
     * Index & Entry Budget
     * ------------------------------------------------------------------ */

    public function index(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_selling/entry_budget', [
            'title'       => 'OPEX Selling - Entry Budget',
            'workingYear' => $workingYear,
            'costCenters' => $this->opexModel->getCostCentersForEntry(),
        ]);
    }

    /* ------------------------------------------------------------------
     * Department Report
     * ------------------------------------------------------------------ */

    public function reportDepartment(): string
    {
        $year = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $selected = trim((string) ($this->request->getGet('cost_center') ?? '0'));
        $isAdmin = (bool) session()->get('is_admin');
        $report = $this->buildDepartmentReport($year, $selected, $isAdmin);

        return view('reports/opex_department', [
            'title'        => 'OPEX Selling - Report Department',
            'module'       => 'OPEX SELLING',
            'workingYear'  => $year,
            'costCenters'  => $this->opexModel->getReportCostCenters($year, (array) session()->get('auth_obj'), $isAdmin),
            'selected'     => $selected,
            'report'       => $report,
            'dataUrl'      => base_url('opex-selling/report-data'),
        ]);
    }

    public function reportData(): ResponseInterface
    {
        $year = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $dept = trim((string) ($this->request->getVar('cost_center') ?? $this->request->getVar('dept') ?? '0'));
        $isAdmin = (bool) session()->get('is_admin');
        $resolved = $this->opexModel->resolveReportCostCenter($dept, $year, (array) session()->get('auth_obj'), $isAdmin);

        if ($resolved === null) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Cost Center tidak memiliki akses atau tidak aktif untuk OPEX Selling.',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'year'   => $year,
            'report' => $this->reportService->normalizeRows($this->opexModel->getReportRows($year, $dept, (array) session()->get('auth_obj'), $isAdmin)),
        ]);
    }

    private function buildDepartmentReport(string $year, string $dept, bool $isAdmin): array
    {
        if ($this->opexModel->resolveReportCostCenter($dept, $year, (array) session()->get('auth_obj'), $isAdmin) === null) {
            return $this->reportService->normalizeRows([]);
        }

        return $this->reportService->normalizeRows($this->opexModel->getReportRows($year, $dept, (array) session()->get('auth_obj'), $isAdmin));
    }

    public function actual(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_selling/actual_budget', [
            'title'       => 'OPEX Selling - Actual Data',
            'workingYear' => $workingYear,
        ]);
    }

    /**
     * Alias actual() — route opex_selling/actual_budget menunjuk ke sini,
     * dan nama ini diekspektasikan analyzer penyedia data view.
     */
    public function actualBudget(): string
    {
        return $this->actual();
    }

    /**
     * Halaman Detail Breakdown Entry Budget.
     * Membaca parameter query GET: header, dept, idx.
     */
    public function entryBudgetDetail(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $header = $this->request->getGet('header') ?? '';
        $dept   = $this->request->getGet('dept') ?? '';
        $idx    = $this->request->getGet('idx') ?? '1';

        return view('opex_selling/entry_budget_detail', [
            'title'         => 'Detail Budget OPEX Selling',
            'workingYear'   => $workingYear,
            'headerAccount' => $header,
            'dept'          => $dept,
            'idx'           => $idx,
            'costCenters'   => $this->opexModel->getCostCenters(),
        ]);
    }

    /* ------------------------------------------------------------------
     * AJAX Entry Data
     * ------------------------------------------------------------------ */

    /**
     * AJAX: header accounts OPEX Selling (Master COA tipe SELLING) beserta
     * actual per bulan (Jan-Agustus). Sumber: master COA + trans_budget_actual.
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
     * AJAX: data Entry Budget OPEX Selling yang sudah dikelompokkan per
     * cost_center_header, sama dengan struktur sistem lama.
     */
    public function getEntryDataGrouped(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->request->getGet('dept') ?? '';

        if (empty($dept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cost Center wajib dipilih.']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'rows'   => $this->opexModel->getEntryDataGrouped($year, $dept),
        ]);
    }

    /**
     * AJAX: data budget OPEX Selling tersimpan per cost center.
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
     * AJAX POST: flat actual + budget View Data OPEX Selling, kompatibel
     * dengan endpoint legacy cari_actual_table.
     */
    public function cariActualTable(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = trim((string) ($this->request->getPost('dept') ?? ''));

        if ($dept !== '' && $dept !== '0') {
            $dept = $this->opexModel->resolveCostCenter($dept);
        }

        return $this->response->setJSON($this->opexModel->getViewDataFlat($year, $dept));
    }

    /**
     * AJAX: data view dengan kategori grouping + actual vs budget per COA.
     *
     * Output: { status, groups: [{ header_name, items: [per-COA data] }], cc }
     * Digunakan oleh tab View Data di entry_tab_view.php.
     */
    public function getViewData(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->request->getGet('dept') ?? '';

        if (empty($dept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cost Center wajib dipilih.']);
        }

        $resolvedDept = $this->opexModel->resolveCostCenter((string) $dept);

        // Cari informasi cost center untuk label header.
        $cc = null;
        foreach ($this->opexModel->getCostCenters() as $c) {
            if ((string) ($c['cost_center'] ?? '') === $resolvedDept
                || (string) ($c['cost_center_sap'] ?? '') === (string) $dept) {
                $cc = [
                    'cc_code'   => $c['cost_center_sap'] ?? $c['cc_code'] ?? $c['cost_center'],
                    'cost_desc' => $c['cost_desc'] ?? '',
                ];
                break;
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'groups' => $this->opexModel->getViewDataGrouped($year, $resolvedDept),
            'cc'     => $cc,
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
        $idx    = $this->request->getGet('idx') ?? '';

        if (empty($dept) || empty($header)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter tidak lengkap.']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'matrix' => $this->opexModel->getDetailMatrix($year, $dept, $header, OpexSellingModel::SOURCE, $idx),
        ]);
    }

    /**
     * AJAX: seluruh data Actual Selling untuk satu pilihan Domestic/Export.
     *
     * Endpoint ini sengaja load-all. Search dan pagination dilakukan di browser
     * agar subtotal group dan grand total selalu memakai dataset terfilter penuh.
     */
    public function getActualData(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = trim((string) ($this->request->getPost('dept') ?? ''));

        if (! in_array($dept, ['600', '700'], true)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Cost Center harus bernilai 600 (Domestic) atau 700 (Export).']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'rows'   => $this->opexModel->getActualSellingRows((string) $year, $dept),
        ]);
    }

    /* ------------------------------------------------------------------
     * Save Budget & Detail
     * ------------------------------------------------------------------ */

    /**
     * AJAX: simpan budget OPEX Selling (dengan concurrent access lock).
     */
    public function saveBudget(): ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid Request']);
        }

        $mainAccount = $this->request->getPost('main_account');
        $totiAa      = $this->request->getPost('toti_aa');
        $dept        = trim((string) ($this->request->getPost('dept') ?? ''));
        $year        = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $totals = [];
        if ($dept === '' || ! is_array($mainAccount) || empty($mainAccount) || ! is_array($totiAa)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Department, akun, dan nilai budget wajib diisi dengan format yang valid.',
            ]);
        }

        foreach ($mainAccount as $key => $account) {
            if (! is_scalar($account)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status'  => 'error',
                    'message' => 'Akun dan nilai budget harus memiliki pasangan yang valid.',
                ]);
            }

            $account = trim((string) $account);
            $rawTotal = $totiAa[$key] ?? null;

            if ($account === '' || $rawTotal === null || is_array($rawTotal)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status'  => 'error',
                    'message' => 'Akun dan nilai budget harus memiliki pasangan yang valid.',
                ]);
            }

            $normalizedTotal = str_replace(',', '', trim((string) $rawTotal));
            if ($normalizedTotal === '' || ! is_numeric($normalizedTotal)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status'  => 'error',
                    'message' => 'Nilai budget harus berupa angka.',
                ]);
            }

            $totals[$key] = (float) $normalizedTotal;
            $mainAccount[$key] = $account;
        }

        $userId = (int) (session()->get('user_id') ?? 0);
        $lock   = (new AccessRestrict())->checkLock((string) $userId, 'opex-selling/entry', (int) $year);
        if (! $lock['allowed']) {
            return $this->response->setStatusCode(409)->setJSON(['status' => 'error', 'message' => $lock['message']]);
        }

        $hasSource = \App\Libraries\DbCompat::hasEntrySource();
        $table     = $this->db->table('yp_plan__trans_budget_entry_data');
        $saved     = false;

        try {
            $this->db->transBegin();

            foreach ($mainAccount as $key => $account) {
                $match = [
                    'id_coa'    => $account,
                    'id_dept'   => $dept,
                    'year_code' => $year,
                ];

                if ($hasSource) {
                    $existing = $table->where($match)
                        ->groupStart()
                            ->where('source', 'SELLING')
                            ->orWhere('source IS NULL')
                        ->groupEnd()
                        ->get()
                        ->getRowArray();

                    if ($existing) {
                        $updated = $table->where('id', $existing['id'])
                            ->update([
                                'total'  => $totals[$key],
                                'source' => 'SELLING',
                            ]);
                    } else {
                        $updated = $table->insert([
                            'id_coa'     => $account,
                            'id_dept'    => $dept,
                            'total'      => $totals[$key],
                            'year_code'  => $year,
                            'source'     => 'SELLING',
                            'created_by' => $userId,
                        ]);
                    }
                } else {
                    $existing = $table->where($match)->get()->getRowArray();

                    if ($existing) {
                        $updated = $table->where('id', $existing['id'])
                            ->update(['total' => $totals[$key]]);
                    } else {
                        $updated = $table->insert([
                            'id_coa'     => $account,
                            'id_dept'    => $dept,
                            'total'      => $totals[$key],
                            'year_code'  => $year,
                            'created_by' => $userId,
                        ]);
                    }
                }

                if ($updated === false) {
                    throw new \RuntimeException('Gagal menyimpan salah satu baris budget OPEX Selling.');
                }
            }

            if (! $this->db->transStatus()) {
                throw new \RuntimeException('Transaksi penyimpanan budget OPEX Selling gagal.');
            }

            $this->db->transCommit();
            $saved = true;
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'OpexSellingController::saveBudget failed: ' . $e->getMessage());
        }

        if (! $saved) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Data Budget Selling gagal disimpan.',
            ]);
        }

        AuditLog::saved('opex-selling/saveBudget', "Budget OPEX Selling {$year} CC {$dept} disimpan (" . count($mainAccount) . " baris)");

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data Budget Selling berhasil disimpan!']);
    }

    /**
     * POST: simpan detail breakdown dari modal entry detail.
     * Menerima payload array: desc[], jan[], feb[], ..., dec[].
     * Melakukan kalkulasi total otomatis di backend sebelum commit.
     */
    public function saveEntryDetail(): ResponseInterface
    {
        $header = $this->request->getPost('header') ?? '';
        $dept   = (string) ($this->request->getPost('dept') ?? '');
        $dept   = $this->opexModel->resolveCostCenter($dept);
        $idx    = (int) $this->request->getPost('idx');
        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId = (int) (session()->get('user_id') ?? 0);

        $descs = $this->request->getPost('desc') ?? [];
        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        if (empty($descs) || ! is_array($descs)) {
            return redirect()->back()->with('error', 'Tidak ada data detail untuk disimpan.');
        }

        $saved = 0;
        foreach ($descs as $i => $desc) {
            $desc = trim((string) $desc);
            if ($desc === '') {
                continue;
            }

            $total = 0;
            $values = [];
            foreach ($months as $m) {
                $val = (float) ($this->request->getPost($m)[$i] ?? 0);
                $values[$m] = $val;
                $total += $val;
            }

            // Simpan ke yp_plan__trans_budget_entry_detail
            $this->db->table('yp_plan__trans_budget_entry_detail')->insert([
                'entry_data_id' => $idx,
                'id_coa'        => 0,
                'id_dept'       => (int) $dept,
                'year_code'     => (int) $year,
                'nama_barang'   => $desc,
                'total'         => $total,
                'sort_order'    => $i + 1,
                'created_by'    => $userId,
                'jan'           => $values['jan'],
                'feb'           => $values['feb'],
                'mar'           => $values['mar'],
                'apr'           => $values['apr'],
                'may'           => $values['may'],
                'jun'           => $values['jun'],
                'jul'           => $values['jul'],
                'aug'           => $values['aug'],
                'sep'           => $values['sep'],
                'oct'           => $values['oct'],
                'nov'           => $values['nov'],
                'dec'           => $values['dec'],
            ]);
            $saved++;
        }

        AuditLog::saved('opex-selling/saveEntryDetail', "Detail breakdown OPEX Selling disimpan ({$saved} item)");

        return redirect()->back()->with('success', "Data detail berhasil disimpan ({$saved} baris).");
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
            'id_dept'   => (string) $this->request->getPost('dept'),
            'year_code' => (int) $year,
        ]);

        if ($result['success']) {
            AuditLog::saved('opex-selling/saveDetailItems', "Detail breakdown OPEX Selling entry_data_id={$entryDataId} disimpan ({$result['count']} item)");
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'count'   => $result['count'] ?? 0,
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
            AuditLog::saved('opex-selling/saveDetail', 'Breakdown item OPEX Selling ditambah/ubah');
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
            AuditLog::log('DELETE', 'opex-selling/deleteDetail', "Breakdown item OPEX Selling dihapus (id {$id})");
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }

    /* ------------------------------------------------------------------
     * Actual & Export
     * ------------------------------------------------------------------ */

    public function uploadActual(): ResponseInterface
    {
        $type = trim((string) ($this->request->getPost('upload_type') ?? ''));
        if (! in_array($type, ['satuan_juta', 'dalam_juta'], true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'upload_type harus satuan_juta atau dalam_juta.',
            ]);
        }

        $file = $this->request->getFile('excel_file');
        if (! $file) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'File Excel wajib dipilih.',
            ]);
        }

        $extension = strtolower((string) $file->getClientExtension());
        if (! in_array($extension, ['xls', 'xlsx'], true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'File harus berformat .xls atau .xlsx.',
            ]);
        }

        if (! $file->isValid() || $file->hasMoved()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'File Excel tidak valid atau gagal diunggah.',
            ]);
        }

        try {
            $rows = ExcelImporter::import($file, true);
            $requiredHeaders = ['main_account', 'description', 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'dept'];
            $availableHeaders = array_keys((array) ($rows[0] ?? []));
            foreach ($requiredHeaders as $header) {
                if (! in_array($header, $availableHeaders, true)) {
                    return $this->response->setStatusCode(400)->setJSON([
                        'status'  => 'error',
                        'message' => "Header wajib '{$header}' tidak ditemukan.",
                    ]);
                }
            }

            $factor = $type === 'dalam_juta' ? 0.000001 : 1.0;
            $normalizedRows = [];
            foreach ($rows as $row) {
                $mainAccount = (int) ExcelImporter::column($row, ['main_account'], 0);
                if ($mainAccount <= 0) {
                    continue;
                }

                $deptValue = ExcelImporter::toFloat(ExcelImporter::column($row, ['dept'], 0));
                if (! in_array((string) (int) $deptValue, ['600', '700'], true) || $deptValue !== (float) (int) $deptValue) {
                    return $this->response->setStatusCode(400)->setJSON([
                        'status'  => 'error',
                        'message' => "Nilai DEPT untuk MAIN ACCOUNT {$mainAccount} harus 600 atau 700.",
                    ]);
                }

                $normalized = [
                    'id_coa'  => $mainAccount,
                    'id_dept' => (int) $deptValue,
                    'sep'     => 0,
                    'oct'     => 0,
                    'nov'     => 0,
                    'dec'     => 0,
                ];
                foreach (['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug'] as $month) {
                    $normalized[$month] = ExcelImporter::toFloat($row[$month] ?? 0) * $factor;
                }
                $normalizedRows[] = $normalized;
            }

            if (empty($normalizedRows)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Tidak ada baris valid dengan MAIN ACCOUNT pada file Excel.',
                ]);
            }

            $year        = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
            $userId      = (int) (session()->get('user_id') ?? 0);
            $departments = array_values(array_unique(array_map(static function (array $row): string {
                return (string) $row['id_dept'];
            }, $normalizedRows)));
            $result = $this->opexModel->saveActualFromImport((string) $year, $normalizedRows, $userId);

            if ($result['success']) {
                AuditLog::log('UPLOAD', 'opex-selling/uploadActual', 'Upload Excel actual selling (' . $type . '): ' . $result['message']);
            }

            return $this->response->setJSON([
                'status'  => $result['success'] ? 'success' : 'error',
                'message' => $result['message'],
                'count'   => $result['count'] ?? 0,
                'departments' => $result['success'] ? $departments : [],
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'OPEX Selling upload: ' . $e->getMessage());

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal membaca Excel atau menyimpan transaction: ' . $e->getMessage(),
            ]);
        }
    }

    public function exportExcel(): ResponseInterface
    {
        $year      = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept      = $this->request->getGet('cost_center') ?? $this->request->getGet('dept') ?? $this->request->getGet('cc') ?? '';
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

        return ExcelExporter::export($headers, $data, 'OPEX_Selling_Service_Export_' . $year, 'OPEX Selling');
    }

    /**
     * Export Actual Selling dengan kontrak yang sama seperti tabel Actual:
     * MAIN ACCOUNT, DESCRIPTION, JAN-AUG, AVG, TOTAL.
     */
    public function exportActual(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = trim((string) ($this->request->getGet('cost_center') ?? $this->request->getGet('dept') ?? ''));

        if (! in_array($dept, ['600', '700'], true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Cost Center harus bernilai 600 (Domestic) atau 700 (Export).',
            ]);
        }

        $rows = $this->opexModel->getActualSellingRows((string) $year, $dept);
        $data = array_map(static function (array $row): array {
            return [
                $row['main_account'] ?? '',
                $row['cost_center_desc'] ?? '',
                (float) ($row['jan'] ?? 0),
                (float) ($row['feb'] ?? 0),
                (float) ($row['mar'] ?? 0),
                (float) ($row['apr'] ?? 0),
                (float) ($row['may'] ?? 0),
                (float) ($row['jun'] ?? 0),
                (float) ($row['jul'] ?? 0),
                (float) ($row['aug'] ?? 0),
                (float) ($row['avg'] ?? 0),
                (float) ($row['total'] ?? 0),
            ];
        }, $rows);

        return ExcelExporter::export(
            ['MAIN ACCOUNT', 'DESCRIPTION', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'AVG', 'TOTAL'],
            $data,
            'OPEX_SELLING_ACTUAL_' . $dept . '_' . date('Ymd_His'),
            'Actual OPEX Selling'
        );
    }

    /**
     * Download template Actual Selling: seluruh COA type SELLING dalam 11 kolom.
     */
    public function downloadTemplate(): ResponseInterface
    {
        $dept = trim((string) ($this->request->getGet('cost_center') ?? $this->request->getGet('dept') ?? ''));
        if (! in_array($dept, ['600', '700'], true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Cost Center harus bernilai 600 (Domestic) atau 700 (Export).',
            ]);
        }

        $rows = $this->opexModel->getActualSellingTemplateRows($dept);
        $data = array_map(static function (array $row) use ($dept): array {
            return [
                $row['main_account'] ?? '',
                $row['cost_center_desc'] ?? '',
                0, 0, 0, 0, 0, 0, 0, 0,
                $dept,
            ];
        }, $rows);

        return ExcelExporter::export(
            ['MAIN ACCOUNT', 'DESCRIPTION', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'DEPT'],
            $data,
            'TEMPLATE_OPEX_SELLING_' . $dept . '_' . date('Ymd_His'),
            'Template Actual OPEX Selling'
        );
    }
}
