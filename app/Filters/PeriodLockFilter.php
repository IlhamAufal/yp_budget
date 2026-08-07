<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * PeriodLockFilter — Pengunci form otomatis di luar periode aktif (PRD Phase 1.3).
 *
 * Membaca tabel yp_plan__master_period (tipe = OPEX_GA / OPEX_SELLING / FOH /
 * MPP / CAPEX). Jika tidak ada periode aktif yang mencakup tanggal hari ini,
 * form entry modul tersebut diblokir.
 *
 * Aturan:
 *   - Hanya modul transaksi yang terdaftar (self::MODULE_TYPES) yang dicek.
 *   - Halaman report / summary tetap bisa diakses (hanya form yang dikunci).
 *   - Jika modul sama sekali belum dikonfigurasi periode (0 baris) → fail-open
 *     (belum ada kebijakan, izinkan).
 *   - AJAX request yang terblokir menerima 423 + JSON.
 */
class PeriodLockFilter implements FilterInterface
{
    private const MODULE_TYPES = [
        'opex-ga'      => 'OPEX_GA',
        'opexga'       => 'OPEX_GA',
        'opex_ga'      => 'OPEX_GA',
        'opex-selling' => 'OPEX_SELLING',
        'opex_selling' => 'OPEX_SELLING',
        'foh'          => 'FOH',
        'capex'        => 'CAPEX',
        'mpp'          => 'MPP',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $uri     = trim((string) uri_string(), '/');

        if ($uri === '' || ! $session->get('user_logged_in')) {
            return;
        }

        $firstSegment = explode('/', $uri)[0];
        if (! isset(self::MODULE_TYPES[$firstSegment])) {
            return; // bukan modul transaksi periode
        }

        // Halaman laporan / summary tidak dikunci
        if (preg_match('#/(report|summary)(/|$)#', $uri)) {
            return;
        }

        $tipe = self::MODULE_TYPES[$firstSegment];
        $db   = \Config\Database::connect();
        $now  = date('Y-m-d H:i:s');

        // Modul belum dikonfigurasi periode sama sekali → fail-open
        $configured = $db->table('yp_plan__master_period')
            ->where('tipe', $tipe)
            ->where('status', 'A')
            ->countAllResults();

        if ($configured === 0) {
            return;
        }

        // Ada periode aktif yang mencakup hari ini?
        $active = $db->table('yp_plan__master_period')
            ->where('tipe', $tipe)
            ->where('status', 'A')
            ->groupStart()
            ->where('begda <=', $now)
            ->where('endda >=', $now)
            ->groupEnd()
            ->countAllResults();

        if ($active > 0) {
            return; // periode sedang berjalan → izinkan
        }

        // Di luar periode aktif → kunci form
        log_message('warning', "PeriodLockFilter: blokir URI '{$uri}' — tidak ada periode aktif {$tipe} pada {$now}.");

        if ($request->isAJAX()) {
            return service('response')
                ->setStatusCode(423)
                ->setJSON([
                    'success' => false,
                    'message' => "Periode entry {$tipe} sedang ditutup. Silakan hubungi Administrator.",
                ]);
        }

        $session->setFlashdata('error', "Periode entry {$tipe} sedang ditutup (di luar periode aktif). Form tidak dapat diakses saat ini.");
        return redirect()->to('/dashboard');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada post-processing
    }
}
