<?php
/* =====================================================================
 * partials/global_modal.php — GLOBAL MODAL COMPONENT (Alpine.js)
 *
 * Modal reusable satu-satunya yang dirender di layout utama.
 * Konten dimuat via AJAX dari controller yang mengembalikan partial view.
 *
 * ─────────────────────────────────────────────────────────────────────
 * API REFERENCE
 * ─────────────────────────────────────────────────────────────────────
 *
 * Modal.show(options)
 *   Membuka modal dan memuat konten via AJAX.
 *   options:
 *     url       (string)   — URL untuk fetch konten partial (required)
 *     title     (string)   — Judul modal (default: 'Detail')
 *     size      (string)   — 'sm' | 'md' | 'lg' | 'xl' | '2xl' | 'full'
 *                             (default: 'md')
 *     onLoaded  (function) — Callback setelah HTML berhasil di-inject
 *                             Gunakan untuk re-init plugin (select2, flatpickr, dll)
 *     onClose   (function) — Callback setelah modal tertutup
 *
 * Modal.close()
 *   Menutup modal. Dispatch event 'modal-closed'.
 *
 * Modal.setTitle(newTitle)
 *   Update judul modal secara reaktif.
 *
 * ─────────────────────────────────────────────────────────────────────
 * CONTOH PENGGUNAAN
 * ─────────────────────────────────────────────────────────────────────
 *
 *   // Basic — load form dari controller
 *   Modal.show({
 *       url: baseUrl + 'sys-admin/form',
 *       title: 'Tambah User Baru',
 *       size: 'lg'
 *   });
 *
 *   // Dengan callback (re-init plugin)
 *   Modal.show({
 *       url: baseUrl + 'master-data/product/form?id=5',
 *       title: 'Edit Produk',
 *       size: 'lg',
 *       onLoaded: () => {
 *           $('#selectCategory').select2();
 *           flatpickr('.datepicker', { dateFormat: 'Y-m-d' });
 *       }
 *   });
 *
 * ─────────────────────────────────────────────────────────────────────
 * PATTERN FORM SUBMISSION (di dalam partial yang dimuat)
 * ─────────────────────────────────────────────────────────────────────
 *
 *   // Di partial form (misal sys-admin/user-form.php):
 *   async function submitForm() {
 *       const res = await ypFetch('/sys-admin/api/user/save', formData);
 *       if (res.success) {
 *           Modal.close();
 *           showToast('success', res.message);
 *           window.dispatchEvent(new CustomEvent('modal-saved', {
 *               detail: { entity: 'user' }
 *           }));
 *       } else {
 *           showToast('error', res.message);
 *       }
 *   }
 *
 * ─────────────────────────────────────────────────────────────────────
 * PANDUAN MIGRASI (inline modal → global modal)
 * ─────────────────────────────────────────────────────────────────────
 *
 *   1. Pindahkan HTML form dari inline modal ke file partial baru
 *      (tanpa extend layout, tanpa wrapper modal — hanya isi body)
 *   2. Buat method controller yang return view partial
 *      (cek isAJAX(), return view tanpa layout)
 *   3. Tambahkan route GET untuk method tersebut
 *   4. Di halaman pemanggil, ganti @click="openCreateModal()"
 *      menjadi @click="Modal.show({url:'...', title:'...', size:'...'})"
 *   5. Tambahkan @modal-saved.window="refreshData()" di halaman pemanggil
 *   6. Hapus HTML inline modal lama (setelah verified working)
 *
 * ===================================================================== */
?>

<!-- ================================================================ -->
<!-- GLOBAL MODAL — Rendered once in layouts/main.php                 -->
<!-- ================================================================ -->
<div
  x-data="globalModal()"
  x-cloak
  @keydown.escape.window="handleEscape()"
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
    class="fixed inset-0 z-[9999999] flex items-center justify-center p-4 overflow-y-auto"
    style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
    @click.self="close()"
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
      class="relative w-full rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden my-8"
      :class="sizeClass"
      role="dialog"
      aria-modal="true"
      :aria-labelledby="'globalModalTitle'"
    >
      <!-- Header -->
      <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
        <h3
          id="globalModalTitle"
          class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-3"
        >
          <span x-text="title"></span>
        </h3>
        <button
          type="button"
          @click="close()"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 -m-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
          aria-label="Tutup"
        >
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <!-- Body -->
      <div class="relative">
        <!-- Loading State -->
        <div x-show="loading" class="flex items-center justify-center p-12">
          <div class="flex flex-col items-center gap-3">
            <svg class="animate-spin h-8 w-8 text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Memuat...</span>
          </div>
        </div>

        <!-- Error State -->
        <div x-show="error" x-cloak class="p-6">
          <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40 p-4 flex items-start gap-3">
            <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5 shrink-0"></i>
            <div>
              <p class="text-sm font-semibold text-red-700 dark:text-red-300">Gagal memuat konten</p>
              <p class="text-xs text-red-600 dark:text-red-400 mt-1" x-text="errorMessage"></p>
            </div>
          </div>
        </div>

        <!-- Content (injected via AJAX) -->
        <div
          x-show="!loading && !error"
          x-html="contentHtml"
          id="globalModalContent"
          class="global-modal-body"
        ></div>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================ -->
<!-- Global Modal Engine — globalModal() + window.Modal API           -->
<!-- ================================================================ -->
<script>
/**
 * globalModal() — Komponen Alpine.js untuk Global Modal.
 *
 * Mendaftarkan `window.Modal` sebagai public API yang dapat dipanggil
 * dari mana saja di aplikasi.
 *
 * @returns {Object} Alpine component data
 */
function globalModal() {
  return {
    // ─── Reactive State ───────────────────────────────────────────
    open: false,
    loading: false,
    error: false,
    errorMessage: '',
    title: '',
    size: 'md',
    contentHtml: '',

    // ─── Internal (non-reactive) ──────────────────────────────────
    _onLoaded: null,
    _onClose: null,
    _lastUrl: '',
    _abortController: null,

    // ─── Size Mapping ─────────────────────────────────────────────
    /** @type {Object.<string, string>} Tailwind max-width class per size key */
    _sizeMap: {
      sm:   'max-w-sm',
      md:   'max-w-md',
      lg:   'max-w-lg',
      xl:   'max-w-xl',
      '2xl': 'max-w-2xl',
      full: 'max-w-full'
    },

    /**
     * Computed: returns Tailwind class based on current size.
     * @returns {string}
     */
    get sizeClass() {
      return this._sizeMap[this.size] || this._sizeMap['md'];
    },

    // ─── Lifecycle ────────────────────────────────────────────────
    /**
     * Alpine init hook — register window.Modal public API.
     */
    init() {
      const self = this;

      window.Modal = {
        /**
         * Membuka modal dan memuat konten via AJAX.
         *
         * @param {Object} options
         * @param {string} options.url       - URL endpoint yang mengembalikan HTML partial
         * @param {string} [options.title]   - Judul modal (default: 'Detail')
         * @param {string} [options.size]    - Ukuran: sm|md|lg|xl|2xl|full (default: 'md')
         * @param {Function} [options.onLoaded] - Callback setelah content loaded
         * @param {Function} [options.onClose]  - Callback setelah modal ditutup
         */
        show(options = {}) {
          self._show(options);
        },

        /**
         * Menutup modal.
         */
        close() {
          self.close();
        },

        /**
         * Update judul modal secara reaktif.
         * @param {string} newTitle
         */
        setTitle(newTitle) {
          self.title = newTitle;
        }
      };
    },

    // ─── Core Methods ─────────────────────────────────────────────

    /**
     * Internal: Proses pembukaan modal dan fetch konten.
     * @param {Object} options - Lihat Modal.show() untuk detail
     */
    async _show(options) {
      const { url, title = 'Detail', size = 'md', onLoaded = null, onClose = null } = options;

      if (!url) {
        console.warn('[Modal] url is required in Modal.show()');
        return;
      }

      // Abort previous request if still pending
      if (this._abortController) {
        this._abortController.abort();
      }

      // Reset state
      this.title = title;
      this.size = size;
      this.contentHtml = '';
      this.error = false;
      this.errorMessage = '';
      this.loading = true;
      this._onLoaded = typeof onLoaded === 'function' ? onLoaded : null;
      this._onClose = typeof onClose === 'function' ? onClose : null;
      this._lastUrl = url;

      // Open modal (show overlay + spinner)
      this.open = true;
      document.body.classList.add('overflow-hidden');

      // Fetch content
      this._abortController = new AbortController();
      try {
        const response = await fetch(url, {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
          },
          signal: this._abortController.signal
        });

        if (!response.ok) {
          throw new Error(`HTTP ${response.status} — ${response.statusText || 'Request failed'}`);
        }

        const html = await response.text();
        this.contentHtml = html;
        this.loading = false;

        // Wait for DOM update, then call onLoaded
        this.$nextTick(() => {
          // Re-init Alpine components inside injected HTML
          this._initInjectedContent();
          if (this._onLoaded) {
            this._onLoaded();
          }
        });
      } catch (err) {
        if (err.name === 'AbortError') return; // Intentional abort, ignore
        this.loading = false;
        this.error = true;
        this.errorMessage = err.message || 'Terjadi kesalahan saat memuat konten.';
        console.error('[Modal] Fetch error:', err);
      } finally {
        this._abortController = null;
      }
    },

    /**
     * Menutup modal dan cleanup state.
     */
    close() {
      this.open = false;
      document.body.classList.remove('overflow-hidden');

      // Call onClose callback
      if (this._onClose) {
        this._onClose();
      }

      // Dispatch event
      window.dispatchEvent(new CustomEvent('modal-closed'));

      // Reset content after transition completes (300ms)
      setTimeout(() => {
        this.contentHtml = '';
        this.title = '';
        this.error = false;
        this.errorMessage = '';
        this.loading = false;
        this._onLoaded = null;
        this._onClose = null;
        this._lastUrl = '';
      }, 300);
    },

    /**
     * Handle Escape key — tutup modal jika sedang terbuka.
     */
    handleEscape() {
      if (this.open) {
        this.close();
      }
    },

    /**
     * Re-initialize Alpine.js pada konten yang baru di-inject.
     * Alpine secara otomatis mendeteksi elemen baru yang ditambahkan ke DOM
     * via MutationObserver, sehingga x-data di dalam partial akan otomatis
     * terinisialisasi. Method ini disediakan untuk kebutuhan khusus.
     * @private
     */
    _initInjectedContent() {
      // Alpine's MutationObserver handles new x-data elements automatically.
      // This method exists as a hook for edge cases or future plugin inits.

      // Execute inline <script> tags inside injected content
      const container = document.getElementById('globalModalContent');
      if (!container) return;

      const scripts = container.querySelectorAll('script');
      scripts.forEach(oldScript => {
        const newScript = document.createElement('script');
        // Copy attributes
        Array.from(oldScript.attributes).forEach(attr => {
          newScript.setAttribute(attr.name, attr.value);
        });
        // Copy content
        newScript.textContent = oldScript.textContent;
        oldScript.parentNode.replaceChild(newScript, oldScript);
      });
    }
  };
}
</script>
