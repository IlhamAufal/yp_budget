<?php

namespace App\Controllers;

use App\Models\OpexSellingModel;
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
    protected $db;

    public function __construct()
    {
        $this->opexModel = new OpexSellingModel();
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
            'costCenters' => $this->opexModel->getCostCenters(),
        ]);
    }

    public function actual(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_selling/actual_budget', [
            'title'       => 'OPEX Selling - Actual Data',
            'workingYear' => $workingYear,
            'costCenters' => $this->opexModel->getCostCenters(),
        ]);
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
     * AJAX: header accounts OPEX Selling beserta total budget.
     * Mengembalikan data dari DB, atau mock data fallback jika kosong.
     */
    public function getHeaderAccounts(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->request->getGet('dept') ?? '';

        if (empty($dept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cost Center wajib dipilih.']);
        }

        $headers = $this->opexModel->getHeaderAccounts($year, $dept);

        // Mock data fallback jika DB kosong
        if (empty($headers)) {
            $headers = [
                [
                    'id'         => 1,
                    'id_coa'     => '5111000',
                    'acct_code'  => '5111000',
                    'coa_desc'   => 'Salaries & Wages',
                    'id_dept'    => $dept,
                    'total_budget' => 1440.00,
                ],
                [
                    'id'         => 2,
                    'id_coa'     => '5112000',
                    'acct_code'  => '5112000',
                    'coa_desc'   => 'Overtime & Allowance',
                    'id_dept'    => $dept,
                    'total_budget' => 720.00,
                ],
                [
                    'id'         => 3,
                    'id_coa'     => '5121000',
                    'acct_code'  => '5121000',
                    'coa_desc'   => 'Office Supplies',
                    'id_dept'    => $dept,
                    'total_budget' => 360.00,
                ],
            ];
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'headers' => $headers,
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
     * AJAX: data view dengan parent-child hierarchy untuk expandable row.
     */
    public function getViewData(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->request->getGet('dept') ?? '';

        if (empty($dept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cost Center wajib dipilih.']);
        }

        $headers = $this->opexModel->getHeaderAccounts($year, $dept);
        $viewData = [];

        foreach ($headers as $h) {
            $children = $this->opexModel->getDetailItems((int) ($h['id'] ?? 0));
            $viewData[] = [
                'id'          => $h['id'] ?? 0,
                'account'     => $h['acct_code'] ?? $h['id_coa'],
                'description' => $h['coa_desc'] ?? '',
                'total'       => (float) ($h['total_budget'] ?? 0),
                'children'    => array_map(function ($c) {
                    return [
                        'desc'  => $c['nama_barang'] ?? '',
                        'jan'   => (float) ($c['jan'] ?? $c['1'] ?? 0),
                        'feb'   => (float) ($c['feb'] ?? $c['2'] ?? 0),
                        'mar'   => (float) ($c['mar'] ?? $c['3'] ?? 0),
                        'apr'   => (float) ($c['apr'] ?? $c['4'] ?? 0),
                        'may'   => (float) ($c['may'] ?? $c['5'] ?? 0),
                        'jun'   => (float) ($c['jun'] ?? $c['6'] ?? 0),
                        'jul'   => (float) ($c['jul'] ?? $c['7'] ?? 0),
                        'aug'   => (float) ($c['aug'] ?? $c['8'] ?? 0),
                        'sep'   => (float) ($c['sep'] ?? $c['9'] ?? 0),
                        'oct'   => (float) ($c['oct'] ?? $c['10'] ?? 0),
                        'nov'   => (float) ($c['nov'] ?? $c['11'] ?? 0),
                        'dec'   => (float) ($c['dec'] ?? $c['12'] ?? 0),
                        'total' => (float) ($c['total'] ?? 0),
                    ];
                }, $children),
            ];
        }

        // Mock data fallback jika DB kosong
        if (empty($viewData)) {
            $viewData = [
                [
                    'id'          => 1,
                    'account'     => '5111000',
                    'description' => 'Salaries & Wages',
                    'total'       => 1440.00,
                    'children'    => [
                        ['desc' => 'Basic Salary Staff', 'jan' => 50, 'feb' => 50, 'mar' => 50, 'apr' => 50, 'may' => 50, 'jun' => 50, 'jul' => 50, 'aug' => 50, 'sep' => 50, 'oct' => 50, 'nov' => 50, 'dec' => 50, 'total' => 600],
                        ['desc' => 'Allowance Staff', 'jan' => 70, 'feb' => 70, 'mar' => 70, 'apr' => 70, 'may' => 70, 'jun' => 70, 'jul' => 70, 'aug' => 70, 'sep' => 70, 'oct' => 70, 'nov' => 70, 'dec' => 70, 'total' => 840],
                    ],
                ],
            ];
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'viewData' => $viewData,
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
        $dept        = $this->request->getPost('dept');
        $year        = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $userId = (int) (session()->get('user_id') ?? 0);
        $lock   = (new AccessRestrict())->checkLock((string) $userId, 'opex-selling/entry', (int) $year);
        if (! $lock['allowed']) {
            return $this->response->setJSON(['status' => 'error', 'message' => $lock['message']]);
        }

        if (! \App\Libraries\DbCompat::hasEntrySource()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Penyimpanan budget OPEX Selling dinonaktifkan sementara (kolom source belum tersedia di skema DB legacy).']);
        }

        if (! empty($mainAccount) && is_array($mainAccount)) {
            foreach ($mainAccount as $key => $account) {
                $total = (float) str_replace(',', '', $totiAa[$key] ?? 0);

                $match = [
                    'id_coa'    => $account,
                    'id_dept'   => $dept,
                    'year_code' => $year,
                ];

                $exists = $this->db->table('yp_plan__trans_budget_entry_data')
                    ->where($match)
                    ->groupStart()
                        ->where('source', 'SELLING')
                        ->orWhere('source IS NULL')
                    ->groupEnd()
                    ->countAllResults();

                if ($exists > 0) {
                    $this->db->table('yp_plan__trans_budget_entry_data')
                        ->where($match)
                        ->groupStart()
                            ->where('source', 'SELLING')
                            ->orWhere('source IS NULL')
                        ->groupEnd()
                        ->update([
                            'total'  => $total,
                            'source' => 'SELLING',
                        ]);
                } else {
                    $this->db->table('yp_plan__trans_budget_entry_data')->insert([
                        'id_coa'     => $account,
                        'id_dept'    => $dept,
                        'total'      => $total,
                        'year_code'  => $year,
                        'source'     => 'SELLING',
                        'created_by' => $userId,
                    ]);
                }
            }
        }

        AuditLog::saved('opex-selling/saveBudget', "Budget OPEX Selling {$year} CC {$dept} disimpan (" . count((array) $mainAccount) . " baris)");

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
        $dept   = $this->request->getPost('dept') ?? '';
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

        $userId      = (int) (session()->get('user_id') ?? 0);
        $entryDataId = (int) $this->request->getPost('entry_data_id');
        $itemsRaw    = $this->request->getPost('items');
        $items       = is_string($itemsRaw) ? (array) json_decode($itemsRaw, true) : (array) $itemsRaw;

        $result = $this->opexModel->saveDetailItemsBatch($entryDataId, $items, $userId);

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
        $file = $this->request->getFile('excel_file');
        $type = $this->request->getPost('upload_type');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'File tidak valid atau gagal diunggah.']);
        }

        try {
            $rows   = ExcelImporter::import($file, true);
            $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
            $userId = (int) (session()->get('user_id') ?? 0);

            $result = $this->opexModel->saveActualFromImport($year, $rows, $userId);

            if ($result['success']) {
                AuditLog::log('UPLOAD', 'opex-selling/uploadActual', 'Upload Excel actual selling (' . $type . '): ' . $result['message']);
            }

            return $this->response->setJSON([
                'status'  => $result['success'] ? 'success' : 'error',
                'message' => $result['success']
                    ? 'Actual Budget (' . strtoupper($type) . ') ' . $result['message']
                    : $result['message'],
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'OPEX Selling upload: ' . $e->getMessage());

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal membaca file Excel: ' . $e->getMessage(),
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
     * Download template Excel untuk upload actual OPEX Selling.
     * View actual_tab_download.php memanggil opex_selling/download_template.
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
            ? "TEMPLATE_OPEX_SELLING_{$dept}_{$timestamp}"
            : "TEMPLATE_OPEX_SELLING_ALL_{$timestamp}";

        return ExcelExporter::export($headers, $data, $filename, 'Template Actual OPEX Selling');
    }
}
