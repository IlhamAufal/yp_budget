<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CapexModel;
use App\Libraries\AccessRestrict;
use App\Libraries\AuditLog;
use App\Libraries\ExcelExporter;
use CodeIgniter\API\ResponseTrait;

/**
 * CapexController — Entry, Summary, Report CAPEX.
 */
class CapexController extends BaseController
{
    use ResponseTrait;

    protected CapexModel $capexModel;
    protected $session;
    protected string $workingYear;

    public function __construct()
    {
        $this->capexModel  = new CapexModel();
        $this->session     = \Config\Services::session();
        $this->workingYear = $this->session->get('year_code') ?? $this->session->get('working_year') ?? date('Y');
    }

    /* ------------------------------------------------------------------
     * Entry Page
     * ------------------------------------------------------------------ */

    public function entry()
    {
        $year = $this->workingYear;
        $user = $this->session->get('user_name') ?? 'System';

        $uri     = $this->request->getUri();
        $segment = $uri->getSegment(1) . '/' . $uri->getSegment(2);
        $isValid = $this->capexModel->logAccess($user, $segment, $year);

        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 10;
        $total   = $this->capexModel->countCapexItems($year);
        $offset  = ($page - 1) * $perPage;

        $data = [
            'validasi'           => $isValid ? 'OK' : 'NOPE',
            'working_year'       => $year,
            'namax'              => $user,
            'categories'         => $this->getAssetCategories(),
            'capex_items'        => $this->capexModel->getCapexItems($year, $offset, $perPage),
            'deptx'              => $this->capexModel->getCostCenters($this->getUserDeptList()),
            'cost_center_options' => $this->getCostCenterOptions(),
            'page'               => $page,
            'perPage'            => $perPage,
            'total'              => $total,
        ];

        return view('capex/entry', $data);
    }

    /* ------------------------------------------------------------------
     * Summary Page
     * ------------------------------------------------------------------ */

    public function summary()
    {
        $year = $this->workingYear;
        $user = $this->session->get('user_name') ?? 'System';

        $uri     = $this->request->getUri();
        $segment = $uri->getSegment(1) . '/' . $uri->getSegment(2);
        $isValid = $this->capexModel->logAccess($user, $segment, $year);

        $costCenters = $this->capexModel->getAllCostCenters();

        $data = [
            'validasi'     => $isValid ? 'OK' : 'NOPE',
            'workingYear'  => $year,
            'title'        => 'Summary Capex',
            'costCenters'  => $costCenters,
        ];

        return view('capex/summary', $data);
    }

    /* ------------------------------------------------------------------
     * AJAX: Data Entry (tab_entry_capex) per Cost Center
     * ------------------------------------------------------------------ */

    public function getEntryData()
    {
        $year = $this->workingYear;
        $dept = $this->request->getGet('dept') ?? '';

        if (empty($dept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cost Center wajib dipilih.']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'rows'   => $this->capexModel->getEntryDataByCategory($year, $dept),
        ]);
    }

    /* ------------------------------------------------------------------
     * AJAX: Save Form CAPEX (modal form) — per kategori aset
     * ------------------------------------------------------------------ */

    public function saveFormCapex()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Invalid method']);
        }

        $year   = $this->workingYear;
        $userId = (string) ($this->session->get('user_id') ?? 0);

        $mainAccount = (string) $this->request->getPost('main_account');
        $dept        = (string) $this->request->getPost('dept');
        $rowsRaw     = $this->request->getPost('rows');
        $rows        = is_string($rowsRaw) ? (array) json_decode($rowsRaw, true) : (array) ($rowsRaw ?? []);

        if (empty($mainAccount) || empty($dept) || empty($rows)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter tidak lengkap.']);
        }

        $lock = (new AccessRestrict())->checkLock((string) $userId, 'capex/entry', (int) $year);
        if (! $lock['allowed']) {
            return $this->response->setJSON(['status' => 'error', 'message' => $lock['message']]);
        }

        $saved = $this->capexModel->saveCapexFormData($rows, $mainAccount, $dept, $year, $userId);

        if ($saved) {
            AuditLog::saved('capex/saveFormCapex', "Proposal CAPEX {$year} kategori {$mainAccount} CC {$dept} disimpan");
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data CAPEX berhasil disimpan.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data CAPEX.']);
    }

    /* ------------------------------------------------------------------
     * AJAX: Cost Centers Dropdown
     * ------------------------------------------------------------------ */

    public function getCostCenters()
    {
        $options = array_merge([['id' => 'ALL', 'name' => 'ALL COST CENTER']], $this->getCostCenterOptions());

        return $this->response->setJSON(['status' => 'success', 'options' => $options]);
    }

    /* ------------------------------------------------------------------
     * AJAX: Summary View All
     * ------------------------------------------------------------------ */

    public function getSummaryViewAll()
    {
        $filterCc = $this->request->getGet('filter_cc') ?? 'ALL';
        $year     = $this->workingYear;

        $dbData = $this->capexModel->getSummaryViewAll($year, $filterCc === 'ALL' ? null : $filterCc);

        return $this->response->setJSON([
            'status' => 'success',
            'source' => 'database',
            'data'   => $dbData,
        ]);
    }

    /* ------------------------------------------------------------------
     * AJAX: Summary Acquisition per Kategori Aset
     * ------------------------------------------------------------------ */

    public function getSummaryAcquisition()
    {
        $filterCc = $this->request->getGet('filter_cc') ?? 'ALL';
        $year     = $this->workingYear;

        $dbData = $this->capexModel->getSummaryAcquisition($year, $filterCc === 'ALL' ? null : $filterCc);

        return $this->response->setJSON([
            'status' => 'success',
            'source' => 'database',
            'data'   => $dbData,
        ]);
    }

    /* ------------------------------------------------------------------
     * AJAX: Summary Depreciation per Kategori Aset
     * ------------------------------------------------------------------ */

    public function getSummaryDepreciation()
    {
        $filterCc = $this->request->getGet('filter_cc') ?? 'ALL';
        $year     = $this->workingYear;

        $dbData = $this->capexModel->getSummaryDepreciation($year, $filterCc === 'ALL' ? null : $filterCc);

        return $this->response->setJSON([
            'status' => 'success',
            'source' => 'database',
            'data'   => $dbData,
        ]);
    }

    /* ------------------------------------------------------------------
     * Export Summary Excel
     * ------------------------------------------------------------------ */

    public function exportSummaryExcel()
    {
        $tab     = $this->request->getGet('tab') ?? 'view_all';
        $filterCc = $this->request->getGet('filter_cc') ?? 'ALL';
        $year     = $this->workingYear;

        $monthKeys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        switch ($tab) {
            case 'acquisition':
                $dbData = $this->capexModel->getSummaryAcquisition($year, $filterCc === 'ALL' ? null : $filterCc);
                $headers = array_merge(['JENIS ASSET (ACQUISITION)'], ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'], ['TOTAL']);
                $data = array_map(function ($r) use ($monthKeys) {
                    $line = [$r['category_code'] . ' - ' . $r['category_name']];
                    foreach ($monthKeys as $m) { $line[] = (float) ($r[$m] ?? 0); }
                    $line[] = (float) ($r['total'] ?? 0);
                    return $line;
                }, $dbData);
                $filename = 'CAPEX_Summary_Acquisition_' . $year;
                $sheetTitle = 'Summary Acquisition';
                break;

            case 'depreciation':
                $dbData = $this->capexModel->getSummaryDepreciation($year, $filterCc === 'ALL' ? null : $filterCc);
                $headers = array_merge(['JENIS ASSET (DEPRECIATION)'], ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'], ['TOTAL']);
                $data = array_map(function ($r) use ($monthKeys) {
                    $line = [$r['category_code'] . ' - ' . $r['category_name']];
                    foreach ($monthKeys as $m) { $line[] = (float) ($r[$m] ?? 0); }
                    $line[] = (float) ($r['total'] ?? 0);
                    return $line;
                }, $dbData);
                $filename = 'CAPEX_Summary_Depreciation_' . $year;
                $sheetTitle = 'Summary Depreciation';
                break;

            default: // view_all
                $dbData = $this->capexModel->getSummaryViewAll($year, $filterCc === 'ALL' ? null : $filterCc);
                $headers = array_merge(['DESCRIPTION','ACCOUNT','COST CENTER','QTY','UNIT PRICE','REMARKS'], ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'], ['TOTAL']);
                $data = array_map(function ($r) use ($monthKeys) {
                    $line = [$r['description'] ?? '', $r['account'] ?? '', $r['cost_center'] ?? '', (int) ($r['qty'] ?? 0), (float) ($r['unit_price'] ?? 0), $r['remarks'] ?? ''];
                    foreach ($monthKeys as $m) { $line[] = (float) ($r[$m] ?? 0); }
                    $line[] = (float) ($r['total'] ?? 0);
                    return $line;
                }, $dbData);
                $filename = 'CAPEX_View_All_' . $year;
                $sheetTitle = 'View Capex All';
                break;
        }

        return ExcelExporter::export($headers, $data ?? [], $filename, $sheetTitle);
    }

    /* ------------------------------------------------------------------
     * Entry Budget Table & Save (Legacy)
     * ------------------------------------------------------------------ */

    public function entryBudgetTable()
    {
        $post    = $this->request->getPost();
        $year    = $this->workingYear;

        $dept     = $post['dep'] ?? '';
        $mainAcct = $post['main'] ?? '';
        $idx      = $post['idx'] ?? '';

        $data = [
            'dept'    => $dept,
            'main'    => $mainAcct,
            'idx'     => $idx,
            'header'  => str_replace(' ', '_', $post['header'] ?? ''),
            'headers' => str_replace('Depreciation - ', '', $post['header'] ?? ''),
            'amount'  => $this->capexModel->getDepreciationAmount($mainAcct),
            'deptx'   => $this->capexModel->getCostCenters($this->getUserDeptList()),
            'datax'   => $this->capexModel->getEntryTableData($mainAcct, $year, $idx, $dept),
        ];

        return view('capex/entry_table_capex', $data);
    }

    public function saveCapex()
    {
        $year = $this->workingYear;
        $user = $this->session->get('user_id') ?? 0;

        $lock = (new AccessRestrict())->checkLock((string) $user, 'capex/entry', (int) $year);
        if (! $lock['allowed']) {
            return $this->respond(['status' => 'error', 'message' => $lock['message']]);
        }

        $post = $this->request->getPost();

        if (isset($post['desc']) && is_array($post['desc'])) {
            $success = $this->capexModel->saveCapexTransaction($post, $year, $user);
        } else {
            $success = $this->capexModel->saveAssetCapex($post, $year, $user);
        }

        if ($success) {
            AuditLog::saved('capex/saveCapex', "Proposal CAPEX {$year} disimpan");
            return $this->respond(['status' => 'success', 'message' => 'Data Capex Berhasil Disimpan']);
        }

        return $this->failServerError('Gagal menyimpan data CAPEX.');
    }

    public function syncToOpex()
    {
        $year = $this->workingYear;
        $success = $this->capexModel->syncToOpex($year);

        if ($success) {
            AuditLog::log('SYNC', 'capex/syncToOpex', "Depresiasi CAPEX {$year} disinkronkan ke OPEX Engine");
            return $this->respond(['status' => 'success', 'message' => "Depresiasi CAPEX Tahun {$year} berhasil disinkronkan ke OPEX Engine."]);
        }

        return $this->failServerError('Gagal sinkronisasi depresiasi CAPEX ke OPEX.');
    }

    /* ------------------------------------------------------------------
     * Private Helpers
     * ------------------------------------------------------------------ */

    /**
     * Semua cost center aktif (master) dalam format {id, name} untuk dropdown.
     * Nama memakai cost_center_sap bila tersedia (fallback ke cost_center).
     */
    private function getCostCenterOptions(): array
    {
        $options = [];
        foreach ($this->capexModel->getAllCostCenters() as $cc) {
            $options[] = [
                'id'   => $cc['cost_center'],
                'name' => '[' . ($cc['cc_code'] ?? $cc['cost_center']) . '] ' . $cc['cost_desc'],
            ];
        }
        return $options;
    }

    private function getAssetCategories(): array
    {
        $cats = [];
        foreach ($this->capexModel->getCapexCategories() as $r) {
            $cats[] = [
                'code' => (string) $r['main_account'],
                'name' => $r['category_name'] ?? ('Asset ' . $r['main_account']),
            ];
        }
        return $cats;
    }

    private function getUserDeptList(): array
    {
        $deptAuth = $this->session->get('auth_obj')[0]['role_object_value'] ?? "''";
        return array_map('trim', explode(',', str_replace("'", '', $deptAuth)));
    }
}
