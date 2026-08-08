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
            'validasi'     => $isValid ? 'OK' : 'NOPE',
            'working_year' => $year,
            'namax'        => $user,
            'categories'   => $this->getAssetCategories(),
            'capex_items'  => $this->capexModel->getCapexItems($year, $offset, $perPage),
            'deptx'        => $this->capexModel->getCostCenters($this->getUserDeptList()),
            'page'         => $page,
            'perPage'      => $perPage,
            'total'        => $total,
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
     * AJAX: Cost Centers Dropdown
     * ------------------------------------------------------------------ */

    public function getCostCenters()
    {
        $costCenters = $this->capexModel->getAllCostCenters();

        $options = [['id' => 'ALL', 'name' => 'ALL COST CENTER']];
        foreach ($costCenters as $cc) {
            $options[] = [
                'id'   => $cc['cost_center'],
                'name' => '[' . ($cc['cc_code'] ?? $cc['cost_center']) . '] ' . $cc['cost_desc'],
            ];
        }

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

        if (empty($dbData)) {
            return $this->response->setJSON([
                'status' => 'success',
                'source' => 'mock_static',
                'data'   => [
                    [
                        'id'          => 1,
                        'description' => 'Pembangunan Gudang Baru',
                        'account'     => '7710202',
                        'cost_center' => '1000GP1100',
                        'qty'         => 1,
                        'unit_price'  => 5000.00,
                        'remarks'     => '5 tahun',
                        'jan' => 0.00, 'feb' => 0.00, 'mar' => 2500.00, 'apr' => 0.00,
                        'may' => 0.00, 'jun' => 0.00, 'jul' => 0.00, 'aug' => 0.00,
                        'sep' => 0.00, 'oct' => 0.00, 'nov' => 0.00, 'dec' => 0.00,
                        'total' => 2500.00,
                    ],
                    [
                        'id'          => 2,
                        'description' => 'Mesin Produksi Line 9',
                        'account'     => '7710203',
                        'cost_center' => '1000KA1004',
                        'qty'         => 2,
                        'unit_price'  => 3200.00,
                        'remarks'     => '10 tahun',
                        'jan' => 100.00, 'feb' => 100.00, 'mar' => 100.00, 'apr' => 100.00,
                        'may' => 100.00, 'jun' => 100.00, 'jul' => 100.00, 'aug' => 100.00,
                        'sep' => 100.00, 'oct' => 100.00, 'nov' => 100.00, 'dec' => 100.00,
                        'total' => 1200.00,
                    ],
                    [
                        'id'          => 3,
                        'description' => 'Komputer & Printer Kantor',
                        'account'     => '7710204',
                        'cost_center' => '1000GPF004',
                        'qty'         => 10,
                        'unit_price'  => 150.00,
                        'remarks'     => '4 tahun',
                        'jan' => 50.00, 'feb' => 50.00, 'mar' => 50.00, 'apr' => 50.00,
                        'may' => 0.00, 'jun' => 0.00, 'jul' => 0.00, 'aug' => 0.00,
                        'sep' => 0.00, 'oct' => 0.00, 'nov' => 0.00, 'dec' => 0.00,
                        'total' => 200.00,
                    ],
                ],
            ]);
        }

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

        if (empty($dbData)) {
            return $this->response->setJSON([
                'status' => 'success',
                'source' => 'mock_static',
                'data'   => [
                    [
                        'category_code' => '7710000',
                        'category_name' => 'Land',
                        'jan' => 0.00, 'feb' => 0.00, 'mar' => 1500.00, 'apr' => 0.00,
                        'may' => 0.00, 'jun' => 0.00, 'jul' => 0.00, 'aug' => 0.00,
                        'sep' => 0.00, 'oct' => 0.00, 'nov' => 0.00, 'dec' => 0.00,
                        'total' => 1500.00,
                    ],
                    [
                        'category_code' => '7710202',
                        'category_name' => 'Building And Facility',
                        'jan' => 250.00, 'feb' => 0.00, 'mar' => 0.00, 'apr' => 500.00,
                        'may' => 0.00, 'jun' => 0.00, 'jul' => 0.00, 'aug' => 0.00,
                        'sep' => 0.00, 'oct' => 0.00, 'nov' => 0.00, 'dec' => 0.00,
                        'total' => 750.00,
                    ],
                    [
                        'category_code' => '7710203',
                        'category_name' => 'Machinery Equipment',
                        'jan' => 100.00, 'feb' => 200.00, 'mar' => 0.00, 'apr' => 0.00,
                        'may' => 300.00, 'jun' => 0.00, 'jul' => 0.00, 'aug' => 0.00,
                        'sep' => 0.00, 'oct' => 0.00, 'nov' => 0.00, 'dec' => 0.00,
                        'total' => 600.00,
                    ],
                    [
                        'category_code' => '7710204',
                        'category_name' => 'Office Equipment',
                        'jan' => 50.00, 'feb' => 50.00, 'mar' => 50.00, 'apr' => 50.00,
                        'may' => 0.00, 'jun' => 0.00, 'jul' => 0.00, 'aug' => 0.00,
                        'sep' => 0.00, 'oct' => 0.00, 'nov' => 0.00, 'dec' => 0.00,
                        'total' => 200.00,
                    ],
                ],
            ]);
        }

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

        if (empty($dbData)) {
            return $this->response->setJSON([
                'status' => 'success',
                'source' => 'mock_static',
                'data'   => [
                    [
                        'category_code' => '7710000',
                        'category_name' => 'Land',
                        'jan' => 0.00, 'feb' => 0.00, 'mar' => 25.00, 'apr' => 0.00,
                        'may' => 0.00, 'jun' => 0.00, 'jul' => 0.00, 'aug' => 0.00,
                        'sep' => 0.00, 'oct' => 0.00, 'nov' => 0.00, 'dec' => 0.00,
                        'total' => 25.00,
                    ],
                    [
                        'category_code' => '7710202',
                        'category_name' => 'Building And Facility',
                        'jan' => 4.17, 'feb' => 4.17, 'mar' => 4.17, 'apr' => 4.17,
                        'may' => 4.17, 'jun' => 4.17, 'jul' => 4.17, 'aug' => 4.17,
                        'sep' => 4.17, 'oct' => 4.17, 'nov' => 4.17, 'dec' => 4.17,
                        'total' => 50.00,
                    ],
                    [
                        'category_code' => '7710203',
                        'category_name' => 'Machinery Equipment',
                        'jan' => 5.00, 'feb' => 5.00, 'mar' => 5.00, 'apr' => 5.00,
                        'may' => 5.00, 'jun' => 5.00, 'jul' => 5.00, 'aug' => 5.00,
                        'sep' => 5.00, 'oct' => 5.00, 'nov' => 5.00, 'dec' => 5.00,
                        'total' => 60.00,
                    ],
                    [
                        'category_code' => '7710204',
                        'category_name' => 'Office Equipment',
                        'jan' => 2.08, 'feb' => 2.08, 'mar' => 2.08, 'apr' => 2.08,
                        'may' => 2.08, 'jun' => 2.08, 'jul' => 2.08, 'aug' => 2.08,
                        'sep' => 2.08, 'oct' => 2.08, 'nov' => 2.08, 'dec' => 2.08,
                        'total' => 25.00,
                    ],
                ],
            ]);
        }

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

    private function getAssetCategories(): array
    {
        $rows = $this->capexModel->getDepreciationMasters();
        $cats = [];
        foreach ($rows as $r) {
            $cats[] = [
                'id'            => (int) $r['id'],
                'main_account'  => (int) $r['main_account'],
                'category_name' => ($r['acct_code'] ?? $r['main_account']) . ' (' . $r['amount'] . ' th)',
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
