<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CapexModel;
use App\Models\CoaModel;
use App\Libraries\AccessRestrict;
use App\Libraries\AuditLog;
use CodeIgniter\API\ResponseTrait;

/**
 * CapexController — Entry, Summary, Report CAPEX (raw file dihubungkan ke view TailAdmin).
 */
class CapexController extends BaseController
{
    use ResponseTrait;

    protected CapexModel $capexModel;
    protected $session;

    public function __construct()
    {
        $this->capexModel = new CapexModel();
        $this->session    = \Config\Services::session();
    }

    /**
     * Entry Page (TailAdmin view: working_year, categories, capex_items)
     */
    public function entry()
    {
        $year = $this->session->get('year_code') ?? $this->session->get('working_year') ?? date('Y');
        $user = $this->session->get('user_name') ?? 'System';

        $uri     = $this->request->getUri();
        $segment = $uri->getSegment(1) . '/' . $uri->getSegment(2);
        $isValid = $this->capexModel->logAccess($user, $segment, $year);

        $data = [
            'validasi'     => $isValid ? 'OK' : 'NOPE',
            'working_year' => $year,
            'namax'        => $user,
            'categories'   => $this->getAssetCategories(),
            'capex_items'  => $this->capexModel->getCapexItems($year),
            'deptx'        => $this->capexModel->getCostCenters($this->getUserDeptList()),
        ];

        return view('capex/entry', $data);
    }

    /**
     * Report Page (TailAdmin view: working_year, dept_reports, total_opex_sync)
     */
    public function report()
    {
        $year = $this->session->get('year_code') ?? $this->session->get('working_year') ?? date('Y');
        $user = $this->session->get('user_name') ?? 'System';

        $uri     = $this->request->getUri();
        $segment = $uri->getSegment(1) . '/' . $uri->getSegment(2);
        $isValid = $this->capexModel->logAccess($user, $segment, $year);

        $data = [
            'validasi'      => $isValid ? 'OK' : 'NOPE',
            'working_year'  => $year,
            'namax'         => $user,
            'dept_reports'  => $this->capexModel->getDeptReports($year),
            'total_opex_sync' => $this->capexModel->getTotalOpexSync($year),
            'deptx'         => $this->capexModel->getDepartments($this->getUserDeptList()),
        ];

        return view('capex/report', $data);
    }

    /**
     * Summary Page (TailAdmin view: working_year, summary_data)
     */
    public function summary()
    {
        $year = $this->session->get('year_code') ?? $this->session->get('working_year') ?? date('Y');
        $user = $this->session->get('user_name') ?? 'System';

        $uri     = $this->request->getUri();
        $segment = $uri->getSegment(1) . '/' . $uri->getSegment(2);
        $isValid = $this->capexModel->logAccess($user, $segment, $year);

        $data = [
            'validasi'     => $isValid ? 'OK' : 'NOPE',
            'working_year' => $year,
            'namax'        => $user,
            'summary_data' => $this->capexModel->getSummaryData($year),
            'deptx'        => $this->capexModel->getDepartments($this->getUserDeptList()),
        ];

        return view('capex/summary', $data);
    }

    /**
     * Load Entry Budget Dynamic Form Table (partial)
     */
    public function entryBudgetTable()
    {
        $post    = $this->request->getPost();
        $year    = $this->session->get('year_code') ?? $this->session->get('working_year') ?? date('Y');

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

    /**
     * Save Capex Asset (AJAX) — format form TailAdmin:
     * asset_description, category_id, acquisition_month, acquisition_cost, useful_life_years, monthly_depreciation
     */
    public function saveCapex()
    {
        $year = $this->session->get('year_code') ?? $this->session->get('working_year') ?? date('Y');
        $user = $this->session->get('user_id') ?? 0;

        // PRD 1.7 — Concurrent Access Locking sebelum proses simpan
        $lock = (new AccessRestrict())->checkLock((string) $user, 'capex/entry', (int) $year);
        if (! $lock['allowed']) {
            return $this->respond(['status' => 'error', 'message' => $lock['message']]);
        }

        $post = $this->request->getPost();

        // Format lama (legacy array) tetap didukung
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

    /**
     * Process & Sync depresiasi CAPEX ke OPEX Engine (AJAX)
     */
    public function syncToOpex()
    {
        $year = $this->session->get('year_code') ?? $this->session->get('working_year') ?? date('Y');

        $success = $this->capexModel->syncToOpex($year);

        if ($success) {
            AuditLog::log('SYNC', 'capex/syncToOpex', "Depresiasi CAPEX {$year} disinkronkan ke OPEX Engine");

            return $this->respond(['status' => 'success', 'message' => "Depresiasi CAPEX Tahun {$year} berhasil disinkronkan ke OPEX Engine."]);
        }

        return $this->failServerError('Gagal sinkronisasi depresiasi CAPEX ke OPEX.');
    }

    public function manual_book()
    {
        return view('capex/manual_book', [
            'title' => 'CAPEX - Manual Book & Documentation',
        ]);
    }

    /**
     * PDF Manual Book Viewer
     */
    public function pdfReader()
    {
        return view('capex/manual_book');
    }

    /**
     * Kategori aset dari master depresiasi (main_account + tahun)
     */
    private function getAssetCategories(): array
    {
        $rows = $this->capexModel->getDepreciationMasters();
        $cats = [];
        foreach ($rows as $r) {
            $cats[] = [
                'id'            => (int) $r['id'],
                'main_account'  => (int) $r['main_account'],
                'category_name' => 'MA ' . $r['main_account'] . ' (' . $r['amount'] . ' th)',
            ];
        }
        return $cats;
    }

    /**
     * Daftar dept dari session auth (fallback kosong → semua dept via model)
     */
    private function getUserDeptList(): array
    {
        $deptAuth = $this->session->get('auth_obj')[0]['role_object_value'] ?? "''";
        return array_map('trim', explode(',', str_replace("'", '', $deptAuth)));
    }
}
