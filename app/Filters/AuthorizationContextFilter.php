<?php

namespace App\Filters;

use App\Exceptions\AuthorizationException;
use App\Libraries\AuthorizationService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Refreshes DB authorization state before RoleFilter evaluates the request. */
class AuthorizationContextFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('user_logged_in') || $this->isPublic($request)) {
            return;
        }

        try {
            (new AuthorizationService())->refresh();
        } catch (AuthorizationException $e) {
            return $this->reject($request, 401, 'Sesi tidak valid. Silakan login kembali.');
        } catch (\Throwable $e) {
            log_message('error', 'Authorization context refresh failed: ' . $e->getMessage());
            return $this->reject($request, 503, 'Authorization tidak tersedia. Akses ditolak.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function isPublic(RequestInterface $request): bool
    {
        $uri = trim((string) uri_string(), '/');
        return in_array($request->getMethod() . ' ' . $uri, [
            'GET login', 'POST login/process', 'GET logout', 'GET api/active-years',
        ], true);
    }

    private function reject(RequestInterface $request, int $status, string $message)
    {
        session()->destroy();
        if ($request->isAJAX() || str_starts_with(trim((string) uri_string(), '/'), 'api/')) {
            return service('response')->setStatusCode($status)->setJSON([
                'success' => false,
                'status'  => 'error',
                'message' => $message,
            ]);
        }
        return redirect()->to('/login')->with('error', $message);
    }
}
