<?php

namespace App\Controllers;

use App\Models\OpexSellingModel;
use App\Libraries\ExcelExporter;
use App\Libraries\ExcelImporter;
use App\Libraries\AccessRestrict;
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

    public function index(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_selling/entry_budget', [
            'title'       => 'OPEX Selling - Entry Budget',
            'workingYear' => $workingYear,
            'dept'        => $this->opexModel->getCostCenters(),
        ]);
    }

    public function actual(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_selling/entry_budget', [
            'title'       => 'OPEX Selling - Actual Data',
            'workingYear' => $workingYear,
            'dept'        => $this->opexModel->getCostCenters(),
        ]);
    }

    public function entryBudgetDetail(): string
    {
        $header = $this->request->getPost('header') ?? '';
        $idx    = $this->request->getPost('idx') ?? '';
        $dept   = $this->request->getPost('dept') ?? '';
        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $builder = $this->db->table('yp_plan__trans_budget_entry_data t')
            ->select("t.id_coa AS main_account, COALESCE(c.cost_center_desc, '') AS cost_center_desc, t.id_dept AS cost_center_header")
            ->select("t.`1` AS isi_1, t.`2` AS isi_2, t.`3` AS isi_3, t.`4` AS isi_4, t.`5` AS isi_5, t.`6` AS isi_6")
            ->select("t.`7` AS isi_7, t.`8` AS isi_8, t.`9` AS isi_9, t.`10` AS isi_10, t.`11` AS isi_11, t.`12` AS isi_12, t.total AS isi_tot")
            ->join('gw_plan__master_coa c', 'c.main_account = t.id_coa', 'left')
            ->where('t.year_code', $year)
            ->groupStart()
                ->where('t.source', 'SELLING')
                ->orWhere('t.source IS NULL')
            ->groupEnd();

        if (! empty($dept)) {
            $builder->where('t.id_dept', $dept);
        }

        $query = $builder->orderBy('t.id_coa', 'ASC')->get()->getResultArray();

        return view('opex_selling/entry_budget_table', [
            'filex'  => $query,
            'header' => $header,
            'idx'    => $idx,
            'dept'   => $dept,
        ]);
    }

    public function saveBudget(): ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid Request']);
        }

        $mainAccount = $this->request->getPost('main_account');
        $totiAa      = $this->request->getPost('toti_aa');
        $dept        = $this->request->getPost('dept');
        $year        = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        // PRD 1.7 — Concurrent Access Locking sebelum proses simpan
        $userId = (int) (session()->get('user_id') ?? 0);
        $lock   = (new AccessRestrict())->checkLock((string) $userId, 'opex-selling/entry', (int) $year);
        if (! $lock['allowed']) {
            return $this->response->setJSON(['status' => 'error', 'message' => $lock['message']]);
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
                    // Update total SAJA — nilai bulanan (1..12) tetap dipertahankan
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
                        'created_by' => (int) (session()->get('user_id') ?? 0),
                    ]);
                }
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data Budget Selling berhasil disimpan!']);
    }

    public function reportDepartment(): string
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('opex_selling/report_department', [
            'title'       => 'OPEX Selling - Department Report',
            'workingYear' => $workingYear,
            'dept'        => $this->opexModel->getCostCenters(),
        ]);
    }

    /**
     * AJAX: data tabel budget selling per cost center (untuk report_department).
     */
    public function cariActualTable(): ResponseInterface
    {
        $dept = $this->request->getPost('dept') ?? '';
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $rows = [];
        try {
            $builder = $this->db->table('yp_plan__trans_budget_entry_data t')
                ->select("t.id_coa AS main_account, COALESCE(c.cost_center_desc, '') AS cost_center_desc")
                ->select("t.`1` AS isi_1, t.`2` AS isi_2, t.`3` AS isi_3, t.`4` AS isi_4, t.`5` AS isi_5, t.`6` AS isi_6")
                ->select("t.`7` AS isi_7, t.`8` AS isi_8, t.`9` AS isi_9, t.`10` AS isi_10, t.`11` AS isi_11, t.`12` AS isi_12, t.total AS isi_tot")
                ->join('gw_plan__master_coa c', 'c.main_account = t.id_coa', 'left')
                ->where('t.year_code', $year)
                ->groupStart()
                    ->where('t.source', 'SELLING')
                    ->orWhere('t.source IS NULL')
                ->groupEnd();

            if (! empty($dept)) {
                $builder->where('t.id_dept', $dept);
            }

            $rows = $builder->orderBy('t.id_coa', 'ASC')->get()->getResultArray();
        } catch (\Throwable $e) {
            $rows = [];
        }

        return $this->response->setJSON($rows);
    }

    /**
     * Upload Actual Selling (.xlsx) — via ExcelImporter ke yp_plan__trans_budget_actual.
     */
    public function uploadActual(): ResponseInterface
    {
        $file = $this->request->getFile('file');
        $type = $this->request->getPost('upload_type'); // 'regular' atau 'ap'

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'File tidak valid atau gagal diunggah.']);
        }

        try {
            $rows   = ExcelImporter::import($file, true);
            $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
            $userId = (int) (session()->get('user_id') ?? 0);

            $result = $this->opexModel->saveActualFromImport($year, $rows, $userId);

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

    /**
     * Service Export — Export budget OPEX Selling ke .xlsx via ExcelExporter.
     */
    public function exportExcel(): ResponseInterface
    {
        $year      = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $monthKeys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        $rows = $this->opexModel->getEntryData($year);
        $data = array_map(function ($r) use ($monthKeys) {
            $line = [$r['id_coa'], $r['coa_desc'] ?? '', $r['id_dept']];
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
}
