<?php

namespace App\Controllers;

use App\Models\FohModel;
use App\Libraries\AccessRestrict;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Foh — Modul Factory Overhead (PRD Phase 2.1).
 *
 * Entry budget FOH, actual, summary, dan breakdown sub-detail COA.
 * Setiap proses simpan memanggil AccessRestrict::checkLock() (standar 1.7)
 * untuk mencegah dua user mengubah cost center yang sama bersamaan.
 */
class Foh extends BaseController
{
    protected FohModel $fohModel;

    public function __construct()
    {
        $this->fohModel = new FohModel();
    }

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

        $result = $this->fohModel->submitBudget($year, $dept, $userId);

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
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

        return $this->response->setJSON([
            'status' => 'success',
            'rows'   => $this->fohModel->getActualData($year, $dept),
        ]);
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

        return view('foh/summary', [
            'title'       => 'FOH - Summary',
            'workingYear' => $year,
            'summary'     => $this->fohModel->getSummary($year),
        ]);
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

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }
}
