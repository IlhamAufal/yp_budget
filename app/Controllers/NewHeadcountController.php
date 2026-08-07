<?php
namespace App\Controllers;

use App\Models\MppDetailModel;
use App\Models\NewHeadAccountModel;
// Gunakan master model lainnya (CostCenterModel, DepartmentModel)

class NewHeadcountController extends BaseController
{
    protected $mppDetailModel;

    public function __construct()
    {
        $this->mppDetailModel = new MppDetailModel();
    }

    // --- Halaman 7.1 Entry MPP ---
    public function entry()
    {
        // Ambil data Cost Center berdasarkan hak akses user
        $data = [
            'title'       => 'Man Power Planning - Entry',
            'costCenters' => [] // Lempar data master Cost Center ke view
        ];
        return view('mpp/entry', $data);
    }

    // --- Halaman Summary Headcount ---
    public function summary()
    {
        // Ambil data Department
        $data = [
            'title'       => 'Summary Headcount',
            'departments' => [] // Lempar data master Department ke view
        ];
        return view('mpp/summary', $data);
    }
    
    // Logika simpan data via AJAX dari Alpine.js
    public function saveEntry()
    {
        // Tangkap JSON / POST data, lakukan validasi, lalu simpan/update ke yp_plan__trans_mpp_detail 
    }
}