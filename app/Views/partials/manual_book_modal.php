<?php
/* =====================================================================
 * partials/manual_book_modal.php — REUSABLE MODAL PDF MANUAL BOOK
 *
 * Modal Alpine.js untuk menampilkan PDF manual book di dalam halaman
 * (tanpa perlu halaman/route terpisah). Dipakai oleh Entry Budget
 * OPEX GA dan Entry CAPEX agar konsisten.
 *
 * Cara pakai di view pemanggil:
 *   1. Include partial ini di dalam SECTION 'modals' (WAJIB) agar dirender
 *      di luar wrapper layout (layouts/main.php) — sama seperti
 *      partials/modal_select_year.php. Kalau include di dalam section
 *      'content', overlay modal hanya menutupi area konten dan tertutup
 *      layer navbar/sidebar.
 *        <?= $this->section('modals') ?>
 *          <?= $this->include('partials/manual_book_modal', [
 *              'mbTitle'     => 'Manual Book - Input OPEX GA',
 *              'mbPdfUrl'    => base_url('assets/docs/manual_book/MANUAL BOOK - BUDGET SYSTEM - INPUT OPEX GA.pdf'),
 *              'mbPdfExists' => is_file(FCPATH . 'assets/docs/manual_book/MANUAL BOOK - BUDGET SYSTEM - INPUT OPEX GA.pdf'),
 *          ]) ?>
 *        <?= $this->endSection() ?>
 *   2. Trigger dari tombol mana pun di halaman:
 *        <button type="button" @click="openManualBook()">Manual Book</button>
 * ===================================================================== */
$mbTitle     = $mbTitle ?? 'Manual Book';
$mbPdfUrl    = $mbPdfUrl ?? '';
$mbPdfExists = $mbPdfExists ?? false;
?>

<!-- ================================================================ -->
<!-- MODAL: PDF MANUAL BOOK                                           -->
<!-- Diletakkan di bagian bawah DOM dengan z-index tinggi & backdrop  -->
<!-- blur (standar 1.2). Dibuka via window.openManualBook().          -->
<!-- ================================================================ -->
<div
  x-data="manualBookModal('<?= esc($mbPdfUrl, 'js') ?>', <?= $mbPdfExists ? 'true' : 'false' ?>, '<?= esc($mbTitle, 'js') ?>')"
  x-cloak
  @keydown.escape.window="open = false"
>
  <!-- Overlay -->
  <div
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[9999999] flex items-center justify-center p-4"
    style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
    @click.self="open = false"
  >
    <!-- Dialog -->
    <div
      x-show="open"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0 scale-95 translate-y-3"
      x-transition:enter-end="opacity-100 scale-100 translate-y-0"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100 scale-100 translate-y-0"
      x-transition:leave-end="opacity-0 scale-95 translate-y-3"
      class="relative w-full max-w-5xl rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden flex flex-col"
      style="height: 88vh;"
      role="dialog"
      aria-modal="true"
      :class="{ 'fixed inset-0 z-[99999999] rounded-none h-full': isFullscreen }"
    >
      <!-- Header -->
      <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-800/40">
        <div class="flex items-center gap-3 min-w-0">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
            <i class="fa-solid fa-book-open"></i>
          </span>
          <div class="min-w-0">
            <h3 class="text-base font-bold text-gray-900 dark:text-white truncate" x-text="title"></h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Panduan standar penggunaan dan pengisian alokasi anggaran</p>
          </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
          <template x-if="pdfExists">
            <a :href="pdfUrl" target="_blank" download class="inline-flex items-center gap-2 rounded-lg bg-primary px-3.5 py-2 text-xs font-bold text-white hover:bg-opacity-90 transition-colors">
              <i class="fa-solid fa-download"></i>
              <span>Unduh PDF</span>
            </a>
          </template>
          <button
            type="button"
            @click="toggleFullscreen()"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            x-show="pdfExists"
          >
            <i class="fa-solid" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i>
            <span x-text="isFullscreen ? 'Keluar Layar Penuh' : 'Layar Penuh'"></span>
          </button>
          <button
            type="button"
            @click="open = false"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
            aria-label="Tutup"
          >
            <i class="fa-solid fa-xmark text-lg"></i>
          </button>
        </div>
      </div>

      <!-- Body -->
      <div class="flex-1 w-full bg-gray-100 dark:bg-gray-950 overflow-auto">
        <template x-if="pdfExists">
          <iframe :src="pdfUrl" class="w-full h-full min-h-[500px] border-0" title="PDF Manual Book Viewer"></iframe>
        </template>

        <template x-if="!pdfExists">
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
        </template>
      </div>
    </div>
  </div>
</div>

<script>
  /**
   * manualBookModal(pdfUrl, pdfExists, title) — komponen modal PDF manual book.
   * Mendaftarkan window.openManualBook() untuk membuka modal dari tombol
   * mana pun di halaman.
   */
  function manualBookModal(pdfUrl = '', pdfExists = false, title = 'Manual Book') {
    return {
      open: false,
      isFullscreen: false,
      pdfUrl: pdfUrl,
      pdfExists: pdfExists,
      title: title,

      init() {
        window.openManualBook = () => {
          this.open = true;
        };
      },

      toggleFullscreen() {
        this.isFullscreen = !this.isFullscreen;
        if (this.isFullscreen) {
          document.body.classList.add('overflow-hidden');
        } else {
          document.body.classList.remove('overflow-hidden');
        }
      }
    };
  }
</script>
