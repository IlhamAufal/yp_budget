<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * RoleFilter — Validasi hak akses URL berdasarkan role user (PRD Phase 1.3).
 *
 * Alur:
 *   1. Bypass endpoint publik (login, set-year, api/active-years, logout).
 *   2. Admin (is_admin = Y) dapat mengakses seluruh halaman.
 *   3. Fail-open selama masa migrasi RBAC: user tanpa role belum dibatasi.
 *   4. User ber-role: segmen pertama URL harus termasuk modul yang diizinkan
 *      dari permission menu (gw_sm__rolemenu → gw_sm__menu.menu_link).
 *
 * Pendekatan berbasis segmen modul dipilih agar endpoint AJAX di dalam
 * modul yang diizinkan (mis. opex-ga/getEntryData) tetap berfungsi, sementara
 * modul yang tidak diizinkan diblokir secara konsisten.
 */
class RoleFilter implements FilterInterface
{
    /**
     * Segmen pertama yang dianggap modul bisnis terproteksi.
     * URL di luar daftar ini (endpoint umum) tidak diblokir.
     */
    private const BUSINESS_MODULES = [
        'dashboard',
        'monitoring',
        'pl',
        'foh',
        'opex-ga',
        'opexga',
        'opex_ga',
        'opex-selling',
        'opex_selling',
        'capex',
        'sales',
        'mpp',
        'master',
        'sys-admin',
        'new-head-account',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $uri     = trim((string) uri_string(), '/');

        // 1. Endpoint publik / non-aplikasi / belum login → biarkan filter auth menangani
        if ($uri === '' || ! $session->get('user_logged_in') || $this->isPublicPath($uri)) {
            return;
        }

        // 2. Admin melihat semua
        if ($session->get('is_admin')) {
            return;
        }

        // 3. Fail-open selama masa migrasi RBAC (user tanpa role)
        $roleIds = array_values(array_filter((array) ($session->get('role_ids') ?? [])));
        if (empty($roleIds)) {
            log_message('warning', 'RoleFilter: user tanpa role diizinkan sementara (fail-open) — URI: ' . $uri);
            return;
        }

        // 4. Kumpulkan segmen modul yang diizinkan dari permission menu
        $db              = \Config\Database::connect();
        $allowedSegments = [];
        foreach ($roleIds as $roleId) {
            $rows = $db->table('gw_sm__rolemenu rm')
                ->select('m.menu_link')
                ->join('gw_sm__menu m', 'm.menu_id = rm.rolemenu_menu_id', 'inner')
                ->where('rm.rolemenu_role_id', (int) $roleId)
                ->where('rm.rolemenu_active', 'Y')
                ->where('m.menu_active', 'Y')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $link = trim((string) ($row['menu_link'] ?? ''), '/');
                if ($link === '' || $link === '#') {
                    continue;
                }
                $allowedSegments[] = explode('/', $link)[0];
            }
        }
        $allowedSegments = array_unique($allowedSegments);

        // Diizinkan jika segmen pertama termasuk modul yang boleh diakses
        $firstSegment = explode('/', $uri)[0];
        if (in_array($firstSegment, $allowedSegments, true)) {
            return;
        }

        // Segmen pertama bukan modul bisnis → endpoint umum, izinkan
        if (! in_array($firstSegment, self::BUSINESS_MODULES, true)) {
            return;
        }

        // Modul bisnis yang tidak diizinkan → blokir
        log_message('warning', "RoleFilter: akses diblokir untuk URI '{$uri}' (modul '{$firstSegment}' tidak diizinkan).");

        if ($request->isAJAX()) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses ke modul ini.',
                ]);
        }

        $session->setFlashdata('error', 'Anda tidak memiliki hak akses ke halaman ini.');
        return redirect()->to('/dashboard');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada post-processing
    }

    /**
     * Path publik yang tidak perlu validasi role.
     */
    private function isPublicPath(string $uri): bool
    {
        if ($uri === 'login' || str_starts_with($uri, 'login/')) {
            return true;
        }
        if ($uri === 'logout') {
            return true;
        }
        if ($uri === 'set-year' || str_starts_with($uri, 'set-year/')) {
            return true;
        }
        if ($uri === 'api/active-years' || str_starts_with($uri, 'api/')) {
            return true;
        }

        return false;
    }
}
