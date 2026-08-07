<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Libraries\AccessRestrict;

class EnsureContextFilter implements FilterInterface
{
    /**
     * Halaman FORM ENTRY yang dikunci concurrent (PRD standar 1.7).
     *
     * Sejak 6 Agt 2026 (keputusan user): concurrent lock hanya berlaku untuk
     * halaman form entry — report, summary, dashboard, monitoring & P/L tetap
     * bisa dibuka semua user secara bersamaan. Proteksi saat simpan tetap
     * dilakukan di controller masing-masing via AccessRestrict::checkLock().
     *
     * Key = segmen pertama modul; value = segmen kedua yang merupakan form entry.
     */
    private const ENTRY_FORM_SECONDS = [
        'foh'          => ['entry'],
        'mpp'          => ['entry'],
        'capex'        => ['entry'],
        'opex-ga'      => ['entry', 'entry-budget'],
        'opexga'       => ['entry', 'entry-budget'],
        'opex_ga'      => ['entry', 'entry-budget'],
        'opex-selling' => ['entry', 'entry-budget'],
        'opex_selling' => ['entry', 'entry-budget'],
    ];

    /** Modul yang akar URL-nya (tanpa segmen kedua) memang form entry. */
    private const ROOT_IS_ENTRY = ['foh', 'mpp', 'opex-selling', 'opex_selling'];

    /**
     * Run before controller execution
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // 1. Bypass halaman publik / penentuan tahun
        $uriString = trim((string) uri_string(), '/');
        if ($uriString === '' || $uriString === 'login' || str_starts_with($uriString, 'login/')) {
            return;
        }
        if ($uriString === 'set-year' || str_starts_with($uriString, 'set-year/')) {
            return;
        }

        // 2. Concurrent lock hanya untuk halaman form entry (PRD 1.7)
        if (! $this->isEntryFormUri($uriString)) {
            return;
        }

        // 3. Cek & pasang lock kombinasi (user, url, year)
        $userId   = $session->get('user_id') ?? $session->get('username');
        $yearCode = $session->get('year_code');

        if ($userId && $yearCode) {
            $accessRestrict = new AccessRestrict();
            $check          = $accessRestrict->check((string) $userId, $uriString, (int) $yearCode);

            if (! $check['allowed']) {
                $accessedBy = $check['accessed_by'] ?? 'User lain';
                $session->setFlashdata('error', "Halaman ini sedang diakses oleh user '{$accessedBy}' untuk tahun anggaran {$yearCode}. Lock akan lepas otomatis dalam beberapa menit.");

                if ($request->isAJAX()) {
                    return service('response')
                        ->setStatusCode(409)
                        ->setJSON([
                            'success' => false,
                            'message' => "Halaman ini sedang diakses oleh user '{$accessedBy}'. Silakan coba lagi dalam beberapa menit.",
                        ]);
                }

                return redirect()->to('/dashboard');
            }
        }
    }

    /**
     * Apakah URI termasuk halaman form entry yang wajib concurrent lock?
     */
    private function isEntryFormUri(string $uri): bool
    {
        $parts  = explode('/', $uri);
        $module = $parts[0] ?? '';
        $second = $parts[1] ?? '';

        if (! isset(self::ENTRY_FORM_SECONDS[$module])) {
            return false;
        }

        // Akar modul (mis. 'foh', 'mpp') yang memang form entry
        if ($second === '') {
            return in_array($module, self::ROOT_IS_ENTRY, true);
        }

        return in_array($second, self::ENTRY_FORM_SECONDS[$module], true);
    }

    /**
     * Run after controller execution
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed
    }
}
