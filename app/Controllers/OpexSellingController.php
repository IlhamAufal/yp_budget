<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class OpexSelling extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'title' => 'OPEX Selling - Entry Budget',
            'dept'  => $this->db->table('m_cost_center')->where('category', 'SELLING')->get()->getResultArray(),
        ];

        return view('opex_selling/entry_budget', $data);
    }

    public function entryBudgetDetail()
    {
        $header =$this->request->getPost('header');
        $idx    =$this->request->getPost('idx');
        $dept   =$this->request->getPost('dept');

        // Query data detail berdasarkan cost center & header
        $builder =$this->db->table('t_budget_opex_selling');
        $builder->where('cost_center_header',$header);
        if (!empty($dept)) {
            $builder->where('cost_center',$dept);
        }
        $query =$builder->get()->getResultArray();

        $data = [
            'filex'  => $query,
            'header' => $header,
            'idx'    => $idx,
            'dept'   => $dept
        ];

        return view('opex_selling/entry_budget_table', $data);
    }

    public function saveBudget()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid Request']);
        }

        $mainAccount =$this->request->getPost('main_account');
        $totiAa      =$this->request->getPost('toti_aa');
        $dept        =$this->request->getPost('dept');

        if (!empty($mainAccount)) {
            foreach ($mainAccount as$key => $account) {$total = $totiAa[$key] ?? 0;
                $this->db->table('t_budget_opex_selling')
                    ->where('main_account', $account)
                    ->where('cost_center', $dept)
                    ->update(['isi_tot' => $total, 'updated_at' => date('Y-m-d H:i:s')]);
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data Budget Selling berhasil disimpan!']);
    }

    public function reportDepartment()
    {
        $data = [
            'title' => 'OPEX Selling - Department Report',
            'dept'  => $this->db->table('m_cost_center')->where('category', 'SELLING')->get()->getResultArray(),
        ];

        return view('opex_selling/report_department', $data);
    }

    public function uploadActual()
    {
        $file =$this->request->getFile('file');
        $type =$this->request->getPost('upload_type'); // 'regular' or 'ap'

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Process Excel file logic
            return $this->response->setJSON(['status' => 'success', 'message' => 'Actual Budget (' . strtoupper($type) . ') berhasil diunggah!']);
        }

        return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Gagal mengunggah berkas.']);
    }
}