<?php

namespace App\Controllers;

use App\Models\FohModel;
use App\Libraries\AccessRestrict;
use App\Libraries\AuditLog;
use App\Libraries\ExcelImporter;
use App\Libraries\ExcelExporter;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Foh — Modul Factory Overhead (PRD Phase 2.1).
 *
 * Entry budget FOH, actual, summary, dan breakdown sub-detail COA.
 * Setiap proses simpan memanggil AccessRestrict::checkLock() (standar 1.7)
 * untuk mencegah dua user mengubah cost center yang sama bersamaan.
 */
class FohController extends BaseController
{
    protected FohModel $fohModel;

    public function __construct()
    {
        $this->fohModel = new FohModel();
    }

    /* ------------------------------------------------------------------
     * Entry Budget
     * ------------------------------------------------------------------ */

    /**
     * Halaman Entry Budget FOH.
     */
    public function entry(): string
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('foh/entry_budget', [
            'title'       => 'FOH - Entry Budget',
            'workingYear' => $year,
            'costCenters' => $this->fohModel->getCostCenters(),
            'coas'        => $this->fohModel->getCoas(),
        ]);
    }

    /**
     * Halaman Detail Entry Budget FOH.
     */
    public function entryBudgetDetail(): string
    {
        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $header = $this->request->getGet('header') ?? '';
        $dept   = $this->request->getGet('dept') ?? '';
        $idx    = $this->request->getGet('idx') ?? '1';

        return view('foh/entry_budget_detail', [
            'title'         => 'FOH - Entry Budget Detail',
            'workingYear'   => $year,
            'headerAccount' => $header,
            'dept'          => $dept,
            'idx'           => $idx,
            'costCenters'   => $this->fohModel->getCostCenters(),
        ]);
    }

    /**
     * AJAX: data budget FOH yang sudah tersimpan per cost center.
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
            'rows'   => $this->fohModel->getEntryData($year, $dept),
        ]);
    }

    /**
     * AJAX: konfigurasi periode submit FOH.
     */
    public function getConfigPeriod(): ResponseInterface
    {
        $period = $this->fohModel->getConfigPeriod('FOH');

        return $this->response->setJSON([
            'status' => 'success',
            'period' => $period,
        ]);
    }

    /**
     * AJAX: header accounts FOH beserta actual per bulan.
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
            'headers' => $this->fohModel->getHeaderAccounts($year, $dept),
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
            'matrix' => $this->fohModel->getDetailMatrix($year, $dept, $header),
        ]);
    }

    /**
     * AJAX: simpan budget FOH (dengan concurrent access lock).
     */
    public function saveBudget(): ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Invalid method']);
        }

        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId = (int) (session()->get('user_id') ?? 0);
        $dept   = $this->request->getPost('dept') ?? '';
        $rows   = (array) ($this->request->getPost('rows') ?? []);

        if (empty($dept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cost Center wajib dipilih.']);
        }

        // PRD 1.7 — Concurrent Access Locking sebelum proses simpan
        $lock = (new AccessRestrict())->checkLock((string) $userId, 'foh/entry', (int) $year);
        if (! $lock['allowed']) {
            return $this->response->setJSON(['status' => 'error', 'message' => $lock['message']]);
        }

        $result = $this->fohModel->saveBudget($year, $dept, $rows, $userId);

        if ($result['success']) {
            $savedCount = $result['count'] ?? 0;
            AuditLog::saved('foh/saveBudget', "Budget FOH {$year} CC {$dept} disimpan ({$savedCount} baris)");
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

        // Front-end mengirim items sebagai JSON string (JSON.stringify) —
        // decode agar menjadi array asli (konsisten dengan OPEX GA/Selling).
        $itemsRaw = $this->request->getPost('items');
        $items    = is_string($itemsRaw) ? (array) json_decode($itemsRaw, true) : (array) ($itemsRaw ?? []);

        // id_coa/dept sebagai hint bila parent entry budget belum ada
        // (model akan auto-create sebelum menyimpan detail).
        $result = $this->fohModel->saveDetailItemsBatch($entryDataId, $items, $userId, [
            'id_coa'    => (int) $this->request->getPost('id_coa'),
            'id_dept'   => (int) $this->request->getPost('dept'),
            'year_code' => (int) $year,
        ]);

        if ($result['success']) {
            AuditLog::saved('foh/saveDetailItems', "Detail breakdown FOH entry_data_id={$entryDataId} disimpan ({$result['count']} item)");
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'count'   => $result['count'] ?? 0,
        ]);
    }

    /**
     * AJAX: workflow submit budget FOH.
     */
    public function submit(): ResponseInterface
    {
        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId = (int) (session()->get('user_id') ?? 0);
        $dept   = $this->request->getPost('dept') ?? '';

        if (empty($dept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cost Center wajib dipilih.']);
        }

        $result = $this->fohModel->submitBudget($year, $dept, $userId);

        if ($result['success']) {
            AuditLog::submitted('foh/submit', "Budget FOH {$year} CC {$dept} disubmit");
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }

    /**
     * AJAX: workflow approve budget FOH.
     */
    public function approve(): ResponseInterface
    {
        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId = (int) (session()->get('user_id') ?? 0);
        $dept   = $this->request->getPost('dept') ?? '';

        if (empty($dept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cost Center wajib dipilih.']);
        }

        $result = $this->fohModel->approveBudget($year, $dept, $userId);

        if ($result['success']) {
            AuditLog::log('APPROVE', 'foh/approve', "Budget FOH {$year} CC {$dept} disetujui");
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }

    /**
     * AJAX: workflow reject budget FOH.
     */
    public function reject(): ResponseInterface
    {
        $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $userId = (int) (session()->get('user_id') ?? 0);
        $dept   = $this->request->getPost('dept') ?? '';
        $note   = $this->request->getPost('note') ?? '';

        if (empty($dept)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cost Center wajib dipilih.']);
        }

        $result = $this->fohModel->rejectBudget($year, $dept, $userId, (string) $note);

        if ($result['success']) {
            AuditLog::log('REJECT', 'foh/reject', "Budget FOH {$year} CC {$dept} ditolak");
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }

    /**
     * AJAX: status workflow budget FOH per dept.
     */
    public function getStatus(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->request->getGet('dept') ?? '';

        return $this->response->setJSON([
            'status'  => 'success',
            'workflow' => $this->fohModel->getBudgetStatus($year, $dept),
        ]);
    }

    /* ------------------------------------------------------------------
     * Actual
     * ------------------------------------------------------------------ */

    /**
     * Halaman Actual FOH.
     */
    public function actual(): string
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        return view('foh/actual', [
            'title'       => 'FOH - Actual (Realisasi)',
            'workingYear' => $year,
            'costCenters' => $this->fohModel->getCostCenters(),
        ]);
    }

    /**
     * AJAX: data actual FOH per cost center.
     */
    public function cariActualTable(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->request->getPost('dept') ?? '';
        $page = max(1, (int) ($this->request->getPost('page') ?? 1));

        $perPage = 10;
        $all     = $this->fohModel->getActualData($year, $dept);

        // View tab_actual_data mengharapkan key main_account/description/grand_total
        // (SAP display), sementara model mengembalikan id_coa/coa_desc/total.
        $mapped = array_map(static function (array $r): array {
            return array_merge($r, [
                'main_account' => $r['id_coa'] ?? '',
                'description'  => $r['coa_desc'] ?? '',
                'grand_total'  => $r['total'] ?? 0,
            ]);
        }, $all);

        return $this->response->setJSON([
            'status' => 'success',
            'rows'   => array_slice($mapped, ($page - 1) * $perPage, $perPage),
            'total'  => count($all),
        ]);
    }

    /**
     * Upload actual FOH dari Excel (.xlsx/.xls).
     */
    public function uploadActual()
    {
        $file   = $this->request->getFile('excel_file');
        $source = $this->request->getPost('upload_source') ?? 'TEMPLATE';

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'File tidak valid atau gagal diunggah.');
        }

        // Validasi ekstensi
        $ext = strtolower($file->getExtension());
        if (! in_array($ext, ['xlsx', 'xls'], true)) {
            return redirect()->back()->with('error', 'Format file tidak didukung. Gunakan .xlsx atau .xls.');
        }

        try {
            $rows   = ExcelImporter::import($file, true);
            $year   = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
            $userId = (int) (session()->get('user_id') ?? 0);

            $result = $this->fohModel->saveActualFromImport($year, $rows, $userId);

            if (! $result['success']) {
                return redirect()->back()->with('error', $result['message']);
            }

            AuditLog::log('UPLOAD', 'foh/uploadActual', "Upload Excel actual FOH ({$source}): " . $result['message']);

            return redirect()->back()->with('success', $result['message']);
        } catch (\Throwable $e) {
            log_message('error', 'FOH upload actual: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk upload actual FOH.
     */
    public function downloadTemplate(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept = $this->request->getGet('cc') ?? '';

        $headers = array_merge(
            ['ID COA', 'COST CENTER', 'DESKRIPSI'],
            ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
            ['TOTAL', 'NOTES']
        );

        $monthKeys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        // Ambil data actual existing sebagai dasar template
        $existing = $this->fohModel->getActualData($year, $dept);
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
            $coas = $this->fohModel->getCoas();
            $data = array_map(function ($c) use ($dept) {
                return [$c['main_account'], $dept, $c['cost_center_desc'], 0,0,0,0,0,0,0,0,0,0,0,0,0, ''];
            }, $coas);
        }

        $filename = 'FOH_Actual_Template_' . $year . ($dept ? "_{$dept}" : '');
        $title    = 'Template Actual FOH';

        return ExcelExporter::export($headers, $data, $filename, $title);
    }

    /**
     * Export data FOH ke Excel.
     */
    public function exportExcel(): ResponseInterface
    {
        $year      = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $dept      = $this->request->getGet('dept') ?? '';
        $monthKeys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        $rows = $this->fohModel->getActualData($year, $dept);
        $data = array_map(function ($r) use ($monthKeys) {
            $line = [$r['id_coa'] ?? '', $r['coa_desc'] ?? '', $r['id_dept'] ?? ''];
            foreach ($monthKeys as $m) {
                $line[] = (float) ($r[$m] ?? 0);
            }
            $line[] = (float) ($r['total'] ?? 0);
            $line[] = $r['notes'] ?? '';

            return $line;
        }, $rows);

        $headers = array_merge(
            ['MAIN ACCOUNT', 'DESKRIPSI', 'COST CENTER'],
            ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
            ['TOTAL', 'NOTES']
        );

        $filename = 'FOH_Actual_' . $year . ($dept ? "_{$dept}" : '');

        return ExcelExporter::export($headers, $data, $filename, 'FOH Actual');
    }

    /* ------------------------------------------------------------------
     * Summary
     * ------------------------------------------------------------------ */

    /**
     * Halaman Summary FOH.
     */
    public function summary(): string
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $all     = $this->fohModel->getSummary($year);
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 10;
        $total   = count($all);

        return view('foh/summary', [
            'title'       => 'FOH - Summary',
            'workingYear' => $year,
            'summary'     => array_slice($all, ($page - 1) * $perPage, $perPage),
            'page'        => $page,
            'perPage'     => $perPage,
            'total'       => $total,
        ]);
    }

    /**
     * Export Summary FOH ke Excel (view foh/summary memanggil foh/summary/export).
     */
    public function summaryExport(): ResponseInterface
    {
        $year = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');
        $cc   = $this->request->getGet('cc') ?? 'ALL';

        $monthKeys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        $rows = $this->fohModel->getSummary($year);
        if (! in_array($cc, ['', 'ALL'], true)) {
            $rows = array_values(array_filter($rows, static fn ($r) => (string) ($r['id_dept'] ?? '') === (string) $cc));
        }

        $headers = array_merge(
            ['COST CENTER', 'DESCRIPTION'],
            ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
            ['TOTAL']
        );
        $data = array_map(static function (array $r) use ($monthKeys): array {
            $line = [$r['id_dept'] ?? '', $r['cost_desc'] ?? ''];
            foreach ($monthKeys as $m) {
                $line[] = (float) ($r[$m] ?? 0);
            }
            $line[] = (float) ($r['total'] ?? 0);

            return $line;
        }, $rows);

        $filename = 'FOH_Summary_' . $year . ($cc !== '' && $cc !== 'ALL' ? "_{$cc}" : '');

        return ExcelExporter::export($headers, $data, $filename, 'FOH Summary');
    }

    /* ------------------------------------------------------------------
     * Breakdown Sub-Detail COA (standar 1.5) — AJAX partial update
     * ------------------------------------------------------------------ */

    /**
     * AJAX: daftar item breakdown sebuah entry budget.
     */
    public function getDetailItems(): ResponseInterface
    {
        $entryDataId = (int) $this->request->getGet('entry_data_id');

        return $this->response->setJSON([
            'status' => 'success',
            'items'  => $this->fohModel->getDetailItems($entryDataId),
        ]);
    }

    /**
     * AJAX: simpan item breakdown (modal Alpine, tanpa refresh).
     */
    public function saveDetail(): ResponseInterface
    {
        $userId = (int) (session()->get('user_id') ?? 0);
        $data   = $this->request->getPost();

        $result = $this->fohModel->saveDetailItem($data, $userId);

        if ($result['success']) {
            AuditLog::saved('foh/saveDetail', "Breakdown item FOH ditambah/ubah (entry_data_id " . ($data['entry_data_id'] ?? '-') . ")");
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
        $id = (int) $this->request->getPost('id');

        $result = $this->fohModel->deleteDetailItem($id);

        if ($result['success']) {
            AuditLog::log('DELETE', 'foh/deleteDetail', "Breakdown item FOH dihapus (id {$id})");
        }

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }
}
