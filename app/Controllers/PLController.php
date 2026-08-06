<?php

namespace App\Controllers;

use App\Models\ModelPl;

class Pl extends BaseController
{
    protected $modelPl;

    public function __construct()
    {
        $this->modelPl = new ModelPl();
    }

    /**
     * Halaman Utama Monitoring P&L Summary
     */
    public function index()
    {
        // Mengambil working_year & user session dari CI4 Session Service
        $workingYear = session()->get('working_year') ?? date('Y');
        $idDept      = session()->get('id_dept');
        $userLevel   = session()->get('user_level');

        // Mengambil data dari ModelPl
        $data = [
            'title'        => 'Summary Profit & Loss Monitoring',
            'working_year' => $workingYear,
            'curr'         => $this->modelPl->get_curr_summary($workingYear, $idDept, $userLevel, 'OPEX'),
            'curr2'        => $this->modelPl->get_curr_summary($workingYear, $idDept, $userLevel, 'FOH'),
            'mpp'          => $this->modelPl->get_mpp_summary($workingYear, $idDept, $userLevel, 'OPEX'),
            'mpp_foh'      => $this->modelPl->get_mpp_summary($workingYear, $idDept, $userLevel, 'FOH'),
            'capex'        => $this->modelPl->get_capex_summary($workingYear, $idDept, $userLevel),
        ];

        return view('monitoring/pl_summary', $data);
    }

    /**
     * Endpoint AJAX untuk mengambil rincian detail modal per Cost Center
     */
    public function cari_view_data()
    {
        // CI4 Request Handling
        $idDept = $this->request->getGet('id_dept');
        $type   = $this->request->getGet('type');
        $workingYear = session()->get('working_year') ?? date('Y');

        if (empty($idDept)) {
            return $this->response->setStatusCode(400)->setBody('ID Department tidak boleh kosong.');
        }

        $detailData = $this->modelPl->get_detail_by_dept($workingYear, $idDept, $type);

        // Menampilkan partial view untuk dimasukkan ke x-html Modal Alpine.js
        return view('monitoring/modal_detail_content', [
            'details' => $detailData,
            'id_dept' => $idDept,
            'type'    => $type
        ]);
    }
}