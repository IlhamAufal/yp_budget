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
            ->select('t.id, t.id_coa, COALESCE(NULLIF(c.id_acct_ext,\'\'), c.main_account, 0) AS main_account, COALESCE(c.cost_center_desc, \'\') AS cost_center_desc, t.id_dept AS cost_center_header')
            ->select("t.`1` AS isi_1, t.`2` AS isi_2, t.`3` AS isi_3, t.`4` AS isi_4, t.`5` AS isi_5, t.`6` AS isi_6")
            ->select("t.`7` AS isi_7, t.`8` AS isi_8, t.`9` AS isi_9, t.`10` AS isi_10, t.`11` AS isi_11, t.`12` AS isi_12, t.total AS isi_tot")
            ->join('gw_plan__master_coa c', 'c.main_account = t.id_coa', 'left')
            ->where('t.year_code', $year);

        if (\App\Libraries\DbCompat::hasEntrySource()) {
            $builder->groupStart()
                ->where('t.source', 'SELLING')
                ->orWhere('t.source IS NULL')
            ->groupEnd();
        }

        if (! empty($dept)) {
            $builder->where('t.id_dept', $dept);
        }

        // Server-side pagination (specific: page via POST)
        $page    = max(1, (int) ($this->request->getPost('page') ?? 1));
        $perPage = 10;
        $total   = (clone $builder)->countAllResults();
        $offset  = ($page - 1) * $perPage;

        $query = $builder->orderBy('t.id_coa', 'ASC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('opex_selling/entry_budget_table', [
            'filex'   => $query,
            'header'  => $header,
            'idx'     => $idx,
            'dept'    => $dept,
            'page'    => $page,
            'perPage' => $perPage,
            'total'   => $total,
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

        // Kolom source tidak ada di skema legacy → simpan di-nonaktifkan sementara
        // (keputusan user 6 Agt 2026: jangan ubah struktur DB).
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

        AuditLog::saved('opex-selling/saveBudget', "Budget OPEX Selling {$year} CC {$dept} disimpan (" . count((array) $mainAccount) . " baris)");

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data Budget Selling berhasil disimpan!']);
    }

    /* ------------------------------------------------------------------
     * Breakdown Sub-Detail COA (standar 1.5) — AJAX partial update
     * ------------------------------------------------------------------ */

    /**
     * AJAX: daftar item breakdown sebuah entry budget selling.
     */
    public function getDetailItems(): ResponseInterface
    {
        $entryDataId = (int) $this->request->getVar('entry_data_id');

        return $this->response->setJSON([
            'status' => 'success',
            'items'  => $this->opexModel->getDetailItems($entryDataId),
        ]);
    }

    /**
     * AJAX: simpan item breakdown (modal Alpine, tanpa refresh).
     */
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

    /**
     * AJAX: hapus item breakdown.
     */
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

    /**
     * Service Export — Export budget OPEX Selling ke .xlsx via ExcelExporter.
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

        return ExcelExporter::export($headers, $data, 'OPEX_Selling_Service_Export_' . $year, 'OPEX Selling');
    }
}
