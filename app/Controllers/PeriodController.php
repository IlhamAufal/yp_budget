<?php

namespace App\Controllers;

use App\Models\PeriodModel;
use CodeIgniter\HTTP\ResponseInterface;

class PeriodController extends BaseController
{
    /**
     * Endpoint untuk menyimpan/mengubah Tahun Anggaran (Working Year) ke Session
     */
    public function setYear(): ResponseInterface
    {
        $yearCode = $this->request->getPost('year_code');

        if (! $yearCode || ! is_numeric($yearCode)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Tahun anggaran wajib dipilih!',
                ])->setStatusCode(400);
            }

            return redirect()->back()->with('error', 'Tahun anggaran wajib dipilih!');
        }

        $yearCode = (int) $yearCode;
        session()->set('year_code', $yearCode);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'    => 'success',
                'message'   => "Tahun Anggaran {$yearCode} berhasil diaktifkan.",
                'year_code' => $yearCode,
            ]);
        }

        return redirect()->to(previous_url() ?? '/')->with('success', "Tahun Anggaran {$yearCode} aktif.");
    }

    /**
     * Get list of active years (AJAX endpoint if needed)
     */
    public function getActiveYears(): ResponseInterface
    {
        $periodModel = new PeriodModel();
        $years = $periodModel->getActiveYears();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $years,
        ]);
    }
}
