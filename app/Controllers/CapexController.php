<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CapexModel;
use CodeIgniter\API\ResponseTrait;

class Capex extends BaseController
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
     * Entry Page
     */
    public function entry()
    {
        $year     = $this->session->get('year_code') ?? date('Y');
        $user     = $this->session->get('user_name') ?? 'System';
        $deptAuth = $this->session->get('auth_obj')[0]['role_object_value'] ?? "''";
        $deptArr  = array_map('trim', explode(',', str_replace("'", "", $deptAuth)));

        $uri      = $this->request->getUri();
        $segment  = $uri->getSegment(1) . '/' . $uri->getSegment(2);

        $isValid  = $this->capexModel->logAccess($user, $segment, $year);

        $data = [
            'validasi' => $isValid ? 'OK' : 'NOPE',
            'namax'    => $user,
            'dept'     => $deptAuth,
            'deptx'    => $this->capexModel->getCostCenters($deptArr),
        ];

        return view('capex/entry', $data);
    }

    /**
     * Report Page
     */
    public function report()
    {
        $year     = $this->session->get('year_code') ?? date('Y');
        $user     = $this->session->get('user_name') ?? 'System';
        $deptAuth = $this->session->get('auth_obj')[0]['role_object_value'] ?? "''";
        $deptArr  = array_map('trim', explode(',', str_replace("'", "", $deptAuth)));

        $uri      = $this->request->getUri();
        $segment  = $uri->getSegment(1) . '/' . $uri->getSegment(2);

        $isValid  = $this->capexModel->logAccess($user, $segment, $year);

        $data = [
            'validasi' => $isValid ? 'OK' : 'NOPE',
            'namax'    => $user,
            'dept'     => $deptAuth,
            'deptx'    => $this->capexModel->getDepartments($deptArr)
        ];

        return view('capex/report', $data);
    }

    /**
     * Summary Page
     */
    public function summary()
    {
        $year     = $this->session->get('year_code') ?? date('Y');
        $user     = $this->session->get('user_name') ?? 'System';
        $deptAuth = $this->session->get('auth_obj')[0]['role_object_value'] ?? "''";
        $deptArr  = array_map('trim', explode(',', str_replace("'", "", $deptAuth)));

        $uri      = $this->request->getUri();
        $segment  = $uri->getSegment(1) . '/' . $uri->getSegment(2);

        $isValid  = $this->capexModel->logAccess($user, $segment, $year);

        $data = [
            'validasi' => $isValid ? 'OK' : 'NOPE',
            'namax'    => $user,
            'dept'     => $deptAuth,
            'years'    => $year,
            'deptx'    => $this->capexModel->getDepartments($deptArr)
        ];

        return view('capex/summary', $data);
    }

    /**
     * Load Entry Budget Dynamic Form Table
     */
    public function entryBudgetTable()
    {
        $post    = $this->request->getPost();
        $year    = $this->session->get('year_code') ?? date('Y');
        $depts   = $this->session->get('auth_obj')[0]['role_object_value'] ?? "''";
        $deptArr = array_map('trim', explode(',', str_replace("'", "", $depts)));

        $dept     = $post['dep'] ?? '';
        $mainAcct = $post['main'] ?? '';
        $idx      = $post['idx'] ?? '';

        $data = [
            'dept'    => $dept,
            'main'    => $mainAcct,
            'idx'     => $idx,
            'header'  => str_replace(" ", "_", $post['header'] ?? ''),
            'headers' => str_replace("Depreciation - ", "", $post['header'] ?? ''),
            'amount'  => $this->capexModel->getDepreciationAmount($mainAcct),
            'deptx'   => $this->capexModel->getCostCenters($deptArr),
            'datax'   => $this->capexModel->getEntryTableData($mainAcct, $year, $idx, $dept)
        ];

        return view('capex/entry_table_capex', $data);
    }

    /**
     * Save Capex Form AJAX Endpoint
     */
    public function saveCapex()
    {
        $year = $this->session->get('year_code') ?? date('Y');
        $user = $this->session->get('user_username') ?? 'System';

        $success = $this->capexModel->saveCapexTransaction($this->request->getPost(), $year, $user);

        if ($success) {
            return $this->respond(['status' => 'success', 'message' => 'Data Capex Berhasil Disimpan']);
        }

        return $this->failServerError('Gagal menyimpan data CAPEX.');
    }

    public function manual_book()
    {
        return view('capex/manual_book', [
            'title' => 'CAPEX - Manual Book & Documentation'
        ]);
    }

    /**
     * PDF Manual Book Viewer
     */
    public function pdfReader()
    {
        return view('capex/manual_book');
    }
}