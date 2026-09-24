<?php

namespace App\Controllers;

use App\Models\ModelPl;
use App\Libraries\ExcelExporter;
use App\Libraries\AuditLog;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * PlController — Halaman Monitoring Progress Entry (Phase 3.1)
 * & Laporan Profit & Loss (Phase 3.2).
 */
class PlController extends BaseController
{
    protected $modelPl;

    public function __construct()
    {
        $this->modelPl = new ModelPl();
    }

    /**
     * Halaman Monitoring Progress Entry (Summary P/L per Cost Center).
     */
    public function index()
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $idDept      = session()->get('id_dept');
        $userLevel   = session()->get('user_level');

        $data = [
            'title'        => 'Monitoring Progress Entry',
            'working_year' => $workingYear,
            'curr'         => $this->modelPl->get_curr_summary($workingYear, $idDept, $userLevel, 'OPEX'),
            'curr2'        => $this->modelPl->get_curr_summary($workingYear, $idDept, $userLevel, 'FOH'),
            'mpp'          => $this->modelPl->get_mpp_summary($workingYear, $idDept, $userLevel, 'OPEX'),
            'mpp_foh'      => $this->modelPl->get_mpp_summary($workingYear, $idDept, $userLevel, 'FOH'),
            'capex'        => $this->modelPl->get_capex_monitoring($workingYear),
        ];

        return view('monitoring/monitoring', $data);
    }

    /**
     * Endpoint AJAX: rincian detail per Cost Center untuk modal monitoring.
     */
    public function cariViewData()
    {
        $idDept      = $this->request->getGet('id_dept');
        $type        = $this->request->getGet('type') ?? 'OPEX';
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        if (empty($idDept)) {
            return $this->response->setStatusCode(400)->setBody('ID Department tidak boleh kosong.');
        }

        $detailData = $this->modelPl->get_detail_by_dept($workingYear, $idDept, $type);

        return view('monitoring/modal_detail_content', [
            'details' => $detailData,
            'id_dept' => $idDept,
            'type'    => $type,
        ]);
    }

    /**
     * Halaman Laporan P/L Summary & Breakdown per Akun + per Bagian.
     */
    public function summary()
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $plSections = $this->modelPl->get_pl_sections($workingYear);
        $plAdjs     = $this->modelPl->get_pl_adjs($workingYear);

        // State Alpine untuk kolom Total / Adjustment per bagian
        $rowState = [];
        foreach ($plSections as $sec) {
            $rowState[$sec['code']] = [
                'total' => (float) $sec['total'],
                'adj'   => (float) ($plAdjs[$sec['code']] ?? 0),
            ];
        }

        $data = [
            'title'       => 'Profit & Loss (P&L) Report',
            'workingYear' => $workingYear,
            'pl_summary'  => $this->modelPl->get_pl_summary($workingYear),
            'pl_details'  => $this->modelPl->get_pl_details($workingYear),
            'pl_sections' => $plSections,
            'pl_notes'    => $this->modelPl->get_pl_notes($workingYear),
            'pl_adjs'     => $plAdjs,
            'rowState'    => $rowState,
            'departments' => $this->modelPl->get_departments(),
        ];

        return view('pl/pl_summary', $data);
    }

    /**
     * Endpoint AJAX: detail transaksi per akun untuk modal P/L.
     */
    public function getDetailAccount()
    {
        $account = $this->request->getGet('account');
        $year    = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        if (empty($account)) {
            return $this->response->setStatusCode(400)->setJSON([]);
        }

        return $this->response->setJSON($this->modelPl->get_detail_account($account, $year));
    }

    /**
     * Endpoint AJAX: detail baris per akun untuk satu bagian P/L (modal).
     */
    public function getSectionDetail()
    {
        $section = strtoupper((string) ($this->request->getGet('section') ?? ''));
        $year    = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $label = ModelPl::PL_SECTIONS[$section]['label'] ?? $section;

        return $this->response->setJSON([
            'success' => true,
            'label'   => $label,
            'items'   => $this->modelPl->get_pl_section_detail($year, $section),
        ]);
    }

    /**
     * Endpoint AJAX: simpan catatan per bagian P/L.
     */
    public function saveNotes(): ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid Request']);
        }

        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId = (int) (session()->get('user_id') ?? 0);
        $code   = (string) ($this->request->getPost('code') ?? '');
        $notes  = (string) ($this->request->getPost('notes') ?? '');

        $ok = $this->modelPl->save_pl_note($year, $code, $notes, $userId);

        if ($ok) {
            AuditLog::log('NOTES', 'pl/saveNotes', "Catatan P/L {$year} bagian {$code} disimpan");
        }

        return $this->response->setJSON([
            'status'  => $ok ? 'success' : 'error',
            'message' => $ok ? 'Catatan berhasil disimpan.' : 'Gagal menyimpan catatan.',
        ]);
    }

    /**
     * Endpoint AJAX: simpan adjustment manual per bagian P/L.
     */
    public function saveAdjs(): ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid Request']);
        }

        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId = (int) (session()->get('user_id') ?? 0);
        $code   = (string) ($this->request->getPost('code') ?? '');
        $value  = (float) str_replace(',', '', (string) ($this->request->getPost('value') ?? 0));

        $ok = $this->modelPl->save_pl_adjs($year, $code, $value, $userId);

        if ($ok) {
            AuditLog::log('ADJUSTMENT', 'pl/saveAdjs', "Adjustment manual P/L {$year} bagian {$code} = {$value}");
        }

        return $this->response->setJSON([
            'status'  => $ok ? 'success' : 'error',
            'message' => $ok ? 'Adjustment berhasil disimpan.' : 'Gagal menyimpan adjustment.',
        ]);
    }

    /**
     * Export P/L (format konsolidasi per bagian) ke .xlsx via ExcelExporter.
     */
    public function exportExcel(): ResponseInterface
    {
        $year      = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $sections  = $this->modelPl->get_pl_sections($year);
        $adjs      = $this->modelPl->get_pl_adjs($year);

        $months   = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $headers  = array_merge(['SEKSI P/L', 'KATEGORI'], $months, ['TOTAL', 'ADJUSTMENT', 'TOTAL ADJUSTED']);

        $rows = [];
        foreach ($sections as $sec) {
            $line = [$sec['code'], $sec['label']];
            foreach (range(1, 12) as $m) {
                $line[] = (float) $sec['m' . $m];
            }
            $adj  = (float) ($adjs[$sec['code']] ?? 0);
            $line[] = (float) $sec['total'];
            $line[] = $adj;
            $line[] = (float) $sec['total'] + $adj;

            $rows[] = $line;
        }

        AuditLog::log('EXPORT', 'pl/exportExcel', "Export P/L report {$year} ke Excel");

        return ExcelExporter::export($headers, $rows, 'PL_Report_' . $year, 'P&L Report');
    }

    /**
     * Export tabel Monitoring Progress Entry per sub-tab ke .xlsx.
     *
     * $type: opex_ga | foh | mpp_opex | mpp_foh | capex
     * Data identik dengan yang dirender di masing-masing tabel tab.
     */
    public function exportMonitoring(string $type = 'opex_ga'): ResponseInterface
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $idDept      = session()->get('id_dept');
        $userLevel   = session()->get('user_level');

        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $type   = strtolower($type);

        switch ($type) {
            case 'opex_ga':
            case 'foh':
                $budgetType = $type === 'foh' ? 'FOH' : 'OPEX';
                $data       = $this->modelPl->get_curr_summary($workingYear, $idDept, $userLevel, $budgetType);

                $headers = array_merge(['Cost Center', 'Entry By'], $months, ['Total']);
                $rows    = [];
                foreach ($data as $r) {
                    $line = [($r['cc_sap'] ?? $r['id_dept'] ?? '') . ' - ' . ($r['cost_desc'] ?? '')];
                    $line[] = $r['tags'] ?? '-';
                    foreach (range(1, 12) as $m) {
                        $line[] = (float) ($r[strtoupper(date('M', mktime(0, 0, 0, $m, 1)))] ?? 0);
                    }
                    $line[] = (float) ($r['TOT'] ?? 0);
                    $rows[] = $line;
                }

                $label      = $type === 'foh' ? 'Budget_FOH' : 'Budget_OPEX_GA';
                $sheetTitle = $type === 'foh' ? 'Budget FOH' : 'Budget OPEX GA';
                break;

            case 'mpp_opex':
            case 'mpp_foh':
                $mppType = $type === 'mpp_foh' ? 'FOH' : 'OPEX';
                $data    = $this->modelPl->get_mpp_summary($workingYear, $idDept, $userLevel, $mppType);

                $headers = array_merge(
                    ['Cost Center', 'Tipe', 'Jabatan'],
                    array_map(fn($m) => 'HC ' . $m, $months),
                    ['HC Total', 'Notes', 'Salary'],
                    array_map(fn($m) => 'Amount ' . $m, $months),
                    ['Amount Total']
                );
                $rows = [];
                foreach ($data as $r) {
                    $line = [($r['cc_sap'] ?? $r['cost_center'] ?? '') . ' - ' . ($r['cost_desc'] ?? '')];
                    $line[] = $r['desc_mpp'] ?? '-';
                    $line[] = $r['staff_name'] ?? '-';
                    foreach (range(1, 12) as $m) {
                        $line[] = (float) ($r[strtoupper(date('M', mktime(0, 0, 0, $m, 1)))] ?? 0);
                    }
                    $line[] = (float) ($r['TOT'] ?? 0);
                    $line[] = $r['notes'] ?? '-';
                    $line[] = is_numeric($r['salary'] ?? null) ? (float) $r['salary'] : ($r['salary'] ?? '-');
                    foreach (range(1, 12) as $m) {
                        $line[] = (float) ($r[strtoupper(date('M', mktime(0, 0, 0, $m, 1))) . '_AMT'] ?? 0);
                    }
                    $line[] = (float) ($r['TOT_AMT'] ?? 0);
                    $rows[] = $line;
                }

                $label      = $type === 'mpp_foh' ? 'Budget_MPP_FOH' : 'Budget_MPP_OPEX';
                $sheetTitle = $type === 'mpp_foh' ? 'Budget MPP FOH' : 'Budget MPP OPEX';
                break;

            case 'capex':
                $data = $this->modelPl->get_capex_monitoring($workingYear);

                $headers = array_merge(
                    ['Cost Center', 'Item Description', 'Account', 'CC Code', 'Unit', 'Unit Price', 'Remarks'],
                    $months,
                    ['Total']
                );
                $rows = [];
                foreach ($data as $r) {
                    $line = [
                        $r['cost_center_desc'] ?? '',
                        $r['item_desc'] ?? '',
                        $r['main_account'] ?? '',
                        $r['cost_center'] ?? '',
                        (float) ($r['unit'] ?? 0),
                        (float) ($r['unit_price'] ?? 0),
                        $r['remarks'] ?? '-',
                    ];
                    foreach (range(1, 12) as $m) {
                        $line[] = (float) ($r[strtoupper(date('M', mktime(0, 0, 0, $m, 1)))] ?? 0);
                    }
                    $line[] = (float) ($r['total'] ?? 0);
                    $rows[] = $line;
                }

                $label      = 'Budget_CAPEX';
                $sheetTitle = 'Budget CAPEX';
                break;

            default:
                return $this->response->setStatusCode(404)->setBody('Tipe export tidak dikenal.');
        }

        AuditLog::log('EXPORT', 'monitoring/export/' . $type, "Export monitoring {$label} {$workingYear} ke Excel");

        return ExcelExporter::export($headers, $rows, 'Monitoring_' . $label . '_' . $workingYear, $sheetTitle);
    }
}
