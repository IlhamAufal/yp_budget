<?php
/* =====================================================================
 * partials/global_modal.php — GLOBAL MODAL COMPONENT (Vanilla JS)
 *
 * Modal reusable satu-satunya yang dirender di layout utama.
 * Konten dimuat via AJAX dari controller yang mengembalikan partial view.
 * Dibuat dengan Pure Vanilla JS (tanpa ketergantungan Alpine.js).
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
 *     size      (string)   — 'auto' (default) | 'sm' | 'md' | 'lg' | 'xl' | '2xl' | 'full'
 *                             'auto' : lebar mengikuti konten (clamp 20rem–72rem, ≤92vw),
 *                                      tinggi mengikuti konten dengan scroll internal.
 *     onLoaded  (function) — Callback setelah HTML berhasil di-inject
 *     onClose   (function) — Callback setelah modal tertutup
 *
 * Modal.close()
 *   Menutup modal. Dispatch event 'modal-closed'.
 *
 * Modal.setTitle(newTitle)
 *   Update judul modal.
 *
 * Modal.refit()
 *   Hitung ulang lebar saat size 'auto' (panggil jika konten berubah
 *   ukuran secara asinkron setelah onLoaded).
 * ===================================================================== */
?>

<!-- ================================================================ -->
<!-- GLOBAL MODAL — Rendered once in layouts/main.php                 -->
<!-- ================================================================ -->
<div
  id="globalModalOverlay"
  class="fixed inset-0 z-[9999999] flex items-center justify-center p-4 overflow-y-auto hidden opacity-0 transition-opacity duration-200"
  style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
>
  <!-- Dialog -->
  <div
    id="globalModalDialog"
    class="relative flex flex-col w-full rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden my-8 transform scale-95 transition-all duration-200 max-w-md"
    style="max-height: calc(100vh - 2rem);"
    role="dialog"
    aria-modal="true"
    aria-labelledby="globalModalTitle"
  >
    <!-- Header -->
    <div class="shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
      <h3
        id="globalModalTitle"
        class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-3"
      >
        Detail
      </h3>
      <button
        type="button"
        id="globalModalCloseBtn"
        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 -m-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        aria-label="Tutup"
      >
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>

    <!-- Body -->
    <div class="relative flex-1 overflow-y-auto" style="min-height: 0;">
      <!-- Loading State -->
      <div id="globalModalLoading" class="flex items-center justify-center p-12 hidden">
        <div class="flex flex-col items-center gap-3">
          <svg class="animate-spin h-8 w-8 text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Memuat...</span>
        </div>
      </div>

      <!-- Error State -->
      <div id="globalModalError" class="p-6 hidden">
        <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40 p-4 flex items-start gap-3">
          <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5 shrink-0"></i>
          <div>
            <p class="text-sm font-semibold text-red-700 dark:text-red-300">Gagal memuat konten</p>
            <p id="globalModalErrorMessage" class="text-xs text-red-600 dark:text-red-400 mt-1"></p>
          </div>
        </div>
      </div>

      <!-- Content (injected via AJAX) -->
      <div
        id="globalModalContent"
        class="global-modal-body"
      ></div>
    </div>
  </div>
</div>

<!-- ================================================================ -->
<!-- Global Modal Engine (Vanilla JS)                                 -->
<!-- ================================================================ -->
<script>
(function() {
  const sizeMap = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
    full: 'max-w-full'
  };
  const sizeClasses = Object.values(sizeMap);

  let onLoadedCallback = null;
  let onCloseCallback = null;
  let abortController = null;
  let isOpen = false;

  const overlay = document.getElementById('globalModalOverlay');
  const dialog = document.getElementById('globalModalDialog');
  const titleEl = document.getElementById('globalModalTitle');
  const closeBtn = document.getElementById('globalModalCloseBtn');
  const loadingEl = document.getElementById('globalModalLoading');
  const errorEl = document.getElementById('globalModalError');
  const errorMsgEl = document.getElementById('globalModalErrorMessage');
  const contentEl = document.getElementById('globalModalContent');

  function setModalSize(sizeKey) {
    if (!dialog) return;
    dialog.classList.remove(...sizeClasses);

    if (sizeKey === 'auto') {
      // Lebar diatur via inline style setelah konten termuat (fitAutoWidth).
      // Sementara menunggu konten, pakai lebar provisional agar spinner rapi.
      dialog.style.width = 'min(24rem, 92vw)';
      return;
    }

    dialog.style.width = '';
    const targetClass = sizeMap[sizeKey] || sizeMap['md'];
    dialog.classList.add(targetClass);
  }

  /**
   * Ukur lebar natural konten (mode 'auto') lalu terapkan pada dialog,
   * dibatasi: minimal 20rem, maksimal 72rem dan tidak lebih dari 92vw.
   */
  function fitAutoWidth() {
    if (!dialog || !contentEl) return;

    const prevWidth = dialog.style.width;
    const prevMaxWidth = dialog.style.maxWidth;

    dialog.style.width = 'max-content';
    dialog.style.maxWidth = 'none';
    const natural = Math.ceil(dialog.getBoundingClientRect().width);

    dialog.style.width = prevWidth;
    dialog.style.maxWidth = prevMaxWidth;

    const maxVw = Math.round(window.innerWidth * 0.92);
    const target = Math.max(320, Math.min(natural + 2, maxVw, 1152));
    dialog.style.width = 'min(' + target + 'px, 92vw)';
  }

  function executeInjectedScripts(container) {
    if (!container) return;
    const scripts = container.querySelectorAll('script');
    scripts.forEach(oldScript => {
      const newScript = document.createElement('script');
      Array.from(oldScript.attributes).forEach(attr => {
        newScript.setAttribute(attr.name, attr.value);
      });
      newScript.textContent = oldScript.textContent;
      oldScript.parentNode.replaceChild(newScript, oldScript);
    });
  }

  window.Modal = {
    /**
     * Membuka modal dan memuat konten via AJAX.
     * @param {Object} options
     */
    async show(options = {}) {
      const { url, title = 'Detail', size = 'auto', onLoaded = null, onClose = null } = options;

      if (!url) {
        console.warn('[Modal] url is required in Modal.show()');
        return;
      }

      if (abortController) {
        abortController.abort();
      }

      onLoadedCallback = typeof onLoaded === 'function' ? onLoaded : null;
      onCloseCallback = typeof onClose === 'function' ? onClose : null;

      if (titleEl) titleEl.textContent = title;
      setModalSize(size);

      if (contentEl) contentEl.innerHTML = '';
      if (errorEl) errorEl.classList.add('hidden');
      if (loadingEl) loadingEl.classList.remove('hidden');

      // Buka overlay
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
      document.body.classList.add('overflow-hidden');

      // Fetch konten
      abortController = new AbortController();
      try {
        const response = await fetch(url, {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
          },
          signal: abortController.signal
        });

        if (!response.ok) {
          throw new Error(`HTTP ${response.status} — ${response.statusText || 'Request failed'}`);
        }

        const html = await response.text();
        if (loadingEl) loadingEl.classList.add('hidden');
        if (contentEl) {
          contentEl.innerHTML = html;
          executeInjectedScripts(contentEl);
        }

        if (size === 'auto') {
          fitAutoWidth();
        }

        if (onLoadedCallback) {
          onLoadedCallback();
        }
      } catch (err) {
        if (err.name === 'AbortError') return;
        if (loadingEl) loadingEl.classList.add('hidden');
        if (errorEl) {
          errorEl.classList.remove('hidden');
          if (errorMsgEl) errorMsgEl.textContent = err.message || 'Terjadi kesalahan saat memuat konten.';
        }
        console.error('[Modal] Fetch error:', err);
      } finally {
        abortController = null;
      }
    },

    /**
     * Menutup modal.
     */
    close() {
      if (!isOpen) return;
      isOpen = false;

      if (overlay) {
        overlay.classList.add('opacity-0');
        if (dialog) {
          dialog.classList.remove('scale-100');
          dialog.classList.add('scale-95');
        }
        setTimeout(() => {
          overlay.classList.add('hidden');
          if (contentEl) contentEl.innerHTML = '';
          if (loadingEl) loadingEl.classList.add('hidden');
          if (errorEl) errorEl.classList.add('hidden');
        }, 200);
      }

      document.body.classList.remove('overflow-hidden');

      if (onCloseCallback) {
        onCloseCallback();
        onCloseCallback = null;
      }

      window.dispatchEvent(new CustomEvent('modal-closed'));
    },

    /**
     * Update judul modal secara dinamis.
     * @param {string} newTitle
     */
    setTitle(newTitle) {
      if (titleEl) {
        titleEl.textContent = newTitle;
      }
    },

    /**
     * Hitung ulang lebar modal pada mode 'auto'
     * (panggil bila konten berubah ukuran secara asinkron).
     */
    refit() {
      fitAutoWidth();
    }
  };

  // Event Listeners
  if (closeBtn) {
    closeBtn.addEventListener('click', () => window.Modal.close());
  }

  if (overlay) {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        window.Modal.close();
      }
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isOpen) {
      window.Modal.close();
    }
  });
})();
</script>
