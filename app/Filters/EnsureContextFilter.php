<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Libraries\AccessRestrict;

class EnsureContextFilter implements FilterInterface
{
    /**
     * Run before controller execution
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // 1. Cek Autentikasi Login (Jika belum login, bypass jika di Halaman Login)
        $uriString = uri_string();
        if ($uriString === 'login' || str_starts_with($uriString, 'login/')) {
            return;
        }

        // 2. Cek apakah Working Year (year_code) belum diset di Session
        // Jika endpoint-nya adalah 'set-year', izinkan proses penentuan tahun
        if ($uriString === 'set-year' || str_starts_with($uriString, 'set-year/')) {
            return;
        }

        // 3. Cek Concurrent Access Restriction jika user sudah login dan year_code sudah ada
        $userId   = $session->get('user_id') ?? $session->get('username');
        $yearCode = $session->get('year_code');

        if ($userId && $yearCode && ! empty($uriString) && $uriString !== '/') {
            $accessRestrict = new AccessRestrict();
            $check = $accessRestrict->check((string) $userId, $uriString, (int) $yearCode);

            if (! $check['allowed']) {
                $session->setFlashdata('error', "Halaman ini sedang diakses oleh user '{$check['accessed_by']}' untuk tahun anggaran {$yearCode}.");
                return redirect()->to('/dashboard');
            }
        }
    }

    /**
     * Run after controller execution
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed
    }
}
