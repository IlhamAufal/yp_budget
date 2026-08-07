<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Jalankan sebelum request
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika user belum login, redirect ke login
        if (! session()->get('user_logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Jalankan setelah request
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu handling khusus
    }
}
