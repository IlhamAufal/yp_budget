<?php

namespace App\Filters;

use App\Libraries\AuthorizationService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * Enforces the legacy module gate and exact gw_sm__menu.menu_link lookup.
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('user_logged_in') || $this->isPublic($request)) {
            return;
        }

        try {
            // Re-read authorization for the decision instead of trusting the
            // session snapshot. AuthorizationContextFilter already performs a
            // refresh for the request lifecycle; this guarantees the guard is
            // still DB-backed when invoked independently in tests or routes.
            $service = new AuthorizationService();
            $context = $service->refresh();
            $uri = ltrim((string) uri_string(), '/');
            if ($service->authorizeRequest($request->getMethod(), $uri, $context)) {
                return;
            }
        } catch (\App\Exceptions\AuthorizationException $e) {
            return $this->deny($request, 'Sesi authorization tidak valid.', 401);
        } catch (\Throwable $e) {
            log_message('error', 'RoleFilter authorization failed: ' . $e->getMessage());
            return $this->deny($request, 'Authorization tidak tersedia. Akses ditolak.', 503);
        }

        return $this->deny($request, 'Anda tidak memiliki hak akses ke halaman ini.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function isPublic(RequestInterface $request): bool
    {
        $key = strtoupper($request->getMethod()) . ' ' . trim((string) uri_string(), '/');
        return in_array($key, [
            'GET login',
            'POST login/process',
            'GET logout',
            'GET api/active-years',
        ], true);
    }

    private function deny(RequestInterface $request, string $message, int $status = 403)
    {
        if ($request->isAJAX() || str_starts_with(trim((string) uri_string(), '/'), 'api/')) {
            return service('response')->setStatusCode($status)->setJSON([
                'success' => false,
                'status'  => 'error',
                'message' => $message,
            ]);
        }

        return service('response')->setStatusCode($status)->setBody(view('errors/html/error_403', [
            'title'   => 'Akses Ditolak',
            'message' => $message,
        ]));
    }
}
