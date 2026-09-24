<?php

namespace App\Controllers;

/**
 * PartialController — Endpoint kecil yang me-return partial view (bukan full page)
 * untuk dimuat ke dalam global modal via AJAX.
 */
class PartialController extends BaseController
{
    /**
     * GET manual-book/view
     * Konten viewer PDF Manual Book untuk global modal.
     *
     * Params:
     *   file  (string) — nama file PDF (basename) di public/assets/docs/manual_book/
     *   title (string) — judul dokumen untuk ditampilkan (opsional)
     */
    public function manualBook(): string
    {
        $file  = basename((string) ($this->request->getGet('file') ?? ''));
        $title = (string) ($this->request->getGet('title') ?? 'Manual Book');

        $dir   = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'manual_book' . DIRECTORY_SEPARATOR;
        $path  = $dir . $file;
        $valid = $file !== '' && is_file($path);

        return view('partials/manual_book_content', [
            'mbTitle'     => $title,
            'mbPdfUrl'    => $valid ? base_url('assets/docs/manual_book/' . $file) : '',
            'mbPdfExists' => $valid,
        ]);
    }
}
