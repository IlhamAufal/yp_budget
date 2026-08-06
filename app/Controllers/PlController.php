<?php

namespace App\Controllers;

use App\Models\ModelPl;

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
            'capex'        => $this->modelPl->get_capex_summary($workingYear, $idDept, $userLevel),
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
     * Halaman Laporan P/L Summary & Breakdown per Akun.
     */
    public function summary()
    {
        $workingYear = session()->get('year_code') ?? session()->get('working_year') ?? date('Y');

        $data = [
            'title'       => 'Profit & Loss (P&L) Report',
            'workingYear' => $workingYear,
            'pl_summary'  => $this->modelPl->get_pl_summary($workingYear),
            'pl_details'  => $this->modelPl->get_pl_details($workingYear),
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
     * Export P/L ke Excel (placeholder — engine ExcelImporter/Exporter Phase 2.3).
     */
    public function exportExcel()
    {
        return redirect()->back()->with('info', 'Export Excel P/L akan tersedia pada Phase 2.3 (Excel Engine terpusat).');
    }
}
