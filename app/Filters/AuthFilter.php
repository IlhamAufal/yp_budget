<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session()->get('user_logged_in')) {
            return;
        }

        if ($request->isAJAX() || str_starts_with(trim((string) uri_string(), '/'), 'api/')) {
            return service('response')->setStatusCode(401)->setJSON([
                'success' => false,
                'status'  => 'error',
                'message' => 'Sesi login tidak tersedia. Silakan login kembali.',
            ]);
        }

        return redirect()->to('/login')->with('error', 'Sesi login tidak tersedia. Silakan login kembali.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
