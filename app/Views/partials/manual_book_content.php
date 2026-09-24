<?php
/* =====================================================================
 * partials/manual_book_content.php — KONTEN viewer PDF Manual Book
 * Dimuat ke dalam global modal via endpoint PartialController::manualBook.
 * Bukan modal lengkap — hanya isi body (info dokumen + iframe / empty state).
 * ===================================================================== */
$mbTitle     = $mbTitle ?? 'Manual Book';
$mbPdfUrl    = $mbPdfUrl ?? '';
$mbPdfExists = $mbPdfExists ?? false;
?>

<?php if ($mbPdfExists): ?>
<div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-800/40">
    <p class="text-xs text-gray-500 dark:text-gray-400">
        Dokumen: <span class="font-semibold text-gray-700 dark:text-gray-200"><?= esc($mbTitle) ?></span>
    </p>
    <a href="<?= esc($mbPdfUrl) ?>" target="_blank" download
       class="inline-flex items-center gap-2 rounded-lg bg-[#2F3185] px-3.5 py-2 text-xs font-bold text-white hover:bg-[#25276d] transition-colors">
        <i class="fa-solid fa-download"></i>
        <span>Unduh PDF</span>
    </a>
</div>
<iframe src="<?= esc($mbPdfUrl) ?>" class="w-full border-0 bg-gray-100 dark:bg-gray-950" style="height: 72vh;" title="PDF Manual Book Viewer"></iframe>
<?php else: ?>
<div class="w-full flex items-center justify-center p-12" style="min-height: 320px;">
    <div class="text-center max-w-md space-y-3">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
            <i class="fa-solid fa-file-pdf text-2xl"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Dokumen Manual Book Belum Tersedia</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                File PDF belum diupload. Silakan letakkan file PDF pada folder
                <code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-xs font-mono">public/assets/docs/manual_book/</code>
                lalu buka kembali modal ini.
            </p>
        </div>
    </div>
</div>
<?php endif; ?>
