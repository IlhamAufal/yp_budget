<?php
/* =====================================================================
 * partials/manual_book_modal.php — REUSABLE MODAL PDF MANUAL BOOK (Vanilla JS)
 *
 * Modal Vanilla JS untuk menampilkan PDF manual book di dalam halaman
 * (tanpa perlu halaman/route terpisah). Dipakai oleh Entry Budget
 * OPEX GA dan Entry CAPEX agar konsisten.
 *
 * Cara pakai di view pemanggil:
 *   1. Include partial ini:
 *        <?= $this->include('partials/manual_book_modal', [
 *            'mbTitle'     => 'Manual Book - Input OPEX GA',
 *            'mbPdfUrl'    => base_url('assets/docs/manual_book/MANUAL BOOK - BUDGET SYSTEM - INPUT OPEX GA.pdf'),
 *            'mbPdfExists' => is_file(FCPATH . 'assets/docs/manual_book/MANUAL BOOK - BUDGET SYSTEM - INPUT OPEX GA.pdf'),
 *        ]) ?>
 *   2. Trigger dari tombol mana pun di halaman:
 *        <button type="button" onclick="window.openManualBook && window.openManualBook()">Manual Book</button>
 * ===================================================================== */
$mbTitle     = $mbTitle ?? 'Manual Book';
$mbPdfUrl    = $mbPdfUrl ?? '';
$mbPdfExists = $mbPdfExists ?? false;
?>

<!-- ================================================================ -->
<!-- MODAL: PDF MANUAL BOOK                                           -->
<!-- Diletakkan di bagian bawah DOM dengan z-index tinggi & backdrop  -->
<!-- blur. Dibuka via window.openManualBook().                        -->
<!-- ================================================================ -->
<div
  id="manualBookModalOverlay"
  class="fixed inset-0 z-[9999999] flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-200"
  style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
>
  <!-- Dialog -->
  <div
    id="manualBookModalDialog"
    class="relative w-full max-w-5xl rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden flex flex-col transform scale-95 transition-all duration-200"
    style="height: 88vh;"
    role="dialog"
    aria-modal="true"
  >
    <!-- Header -->
    <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-800/40 shrink-0">
      <div class="flex items-center gap-3 min-w-0">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
          <i class="fa-solid fa-book-open"></i>
        </span>
        <div class="min-w-0">
          <h3 class="text-base font-bold text-gray-900 dark:text-white truncate"><?= esc($mbTitle) ?></h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">Panduan standar penggunaan dan pengisian alokasi anggaran</p>
        </div>
      </div>

      <div class="flex items-center gap-2 shrink-0">
        <?php if ($mbPdfExists): ?>
          <a href="<?= esc($mbPdfUrl) ?>" target="_blank" download class="inline-flex items-center gap-2 rounded-lg bg-primary px-3.5 py-2 text-xs font-bold text-white hover:bg-opacity-90 transition-colors">
            <i class="fa-solid fa-download"></i>
            <span>Unduh PDF</span>
          </a>
          <button
            type="button"
            id="manualBookFullscreenBtn"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          >
            <i id="manualBookFullscreenIcon" class="fa-solid fa-expand"></i>
            <span id="manualBookFullscreenText">Layar Penuh</span>
          </button>
        <?php endif; ?>
        <button
          type="button"
          id="manualBookCloseBtn"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
          aria-label="Tutup"
        >
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>
    </div>

    <!-- Body -->
    <div class="flex-1 w-full bg-gray-100 dark:bg-gray-950 overflow-auto">
      <?php if ($mbPdfExists): ?>
        <iframe src="<?= esc($mbPdfUrl) ?>" class="w-full h-full min-h-[500px] border-0" title="PDF Manual Book Viewer"></iframe>
      <?php else: ?>
        <div class="w-full h-full flex items-center justify-center p-12">
          <div class="text-center max-w-md space-y-3">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
              <i class="fa-solid fa-file-pdf text-2xl"></i>
            </div>
            <div>
              <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Dokumen Manual Book Belum Tersedia</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                File PDF belum diupload. Silakan letakkan file PDF pada folder
                <code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-xs font-mono">public/assets/docs/manual_book/</code>
                lalu muat ulang halaman ini.
              </p>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
(function() {
  const overlay = document.getElementById('manualBookModalOverlay');
  const dialog = document.getElementById('manualBookModalDialog');
  const closeBtn = document.getElementById('manualBookCloseBtn');
  const fullscreenBtn = document.getElementById('manualBookFullscreenBtn');
  const fullscreenIcon = document.getElementById('manualBookFullscreenIcon');
  const fullscreenText = document.getElementById('manualBookFullscreenText');

  let isOpen = false;
  let isFullscreen = false;

  function closeManualBook() {
    if (!isOpen) return;
    isOpen = false;
    if (isFullscreen) toggleFullscreen();
    if (overlay) {
      overlay.classList.add('opacity-0');
      if (dialog) {
        dialog.classList.remove('scale-100');
        dialog.classList.add('scale-95');
      }
      setTimeout(() => {
        overlay.classList.add('hidden');
      }, 200);
    }
  }

  function toggleFullscreen() {
    if (!dialog) return;
    isFullscreen = !isFullscreen;
    if (isFullscreen) {
      dialog.classList.add('fixed', 'inset-0', 'z-[99999999]', 'rounded-none', 'h-full', 'max-w-none');
      dialog.classList.remove('relative', 'max-w-5xl', 'rounded-2xl');
      dialog.style.height = '100vh';
      if (fullscreenIcon) {
        fullscreenIcon.classList.remove('fa-expand');
        fullscreenIcon.classList.add('fa-compress');
      }
      if (fullscreenText) fullscreenText.textContent = 'Keluar Layar Penuh';
      document.body.classList.add('overflow-hidden');
    } else {
      dialog.classList.remove('fixed', 'inset-0', 'z-[99999999]', 'rounded-none', 'h-full', 'max-w-none');
      dialog.classList.add('relative', 'max-w-5xl', 'rounded-2xl');
      dialog.style.height = '88vh';
      if (fullscreenIcon) {
        fullscreenIcon.classList.remove('fa-compress');
        fullscreenIcon.classList.add('fa-expand');
      }
      if (fullscreenText) fullscreenText.textContent = 'Layar Penuh';
      document.body.classList.remove('overflow-hidden');
    }
  }

  window.openManualBook = function() {
    isOpen = true;
    if (overlay) {
      overlay.classList.remove('hidden');
      requestAnimationFrame(() => {
        overlay.classList.remove('opacity-0');
        if (dialog) {
          dialog.classList.remove('scale-95');
          dialog.classList.add('scale-100');
        }
      });
    }
  };

  if (closeBtn) closeBtn.addEventListener('click', closeManualBook);
  if (fullscreenBtn) fullscreenBtn.addEventListener('click', toggleFullscreen);

  if (overlay) {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeManualBook();
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isOpen) {
      closeManualBook();
    }
  });
})();
</script>
