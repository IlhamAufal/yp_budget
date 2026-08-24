<!-- ============================================================ -->
<!-- PARTIAL: Confirm Modal Reusable (Vanilla JS)                 -->
<!-- Pemakaian: window.openConfirmDialog({ title, message, icon, -->
<!--   tone, confirmText, onConfirm })                            -->
<!-- ============================================================ -->
<div
  id="confirmModalOverlay"
  class="fixed inset-0 z-[9999999] flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-200"
  style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
>
  <!-- Dialog -->
  <div
    id="confirmModalDialog"
    class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden transform scale-95 transition-all duration-200"
    role="dialog"
    aria-modal="true"
  >
    <!-- Header -->
    <div class="flex items-start justify-between gap-4 p-6 pb-0">
      <div class="flex items-center gap-4">
        <span
          id="confirmModalIconWrapper"
          class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-lg bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400"
        >
          <i id="confirmModalIcon" class="fa-solid fa-triangle-exclamation"></i>
        </span>
        <div>
          <h3 id="confirmModalTitle" class="text-lg font-bold text-gray-900 dark:text-white">Konfirmasi</h3>
        </div>
      </div>
      <button
        type="button"
        id="confirmModalCloseBtn"
        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 -m-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        aria-label="Tutup"
      >
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>

    <!-- Body -->
    <div class="p-6">
      <p id="confirmModalMessage" class="text-sm leading-relaxed text-gray-600 dark:text-gray-300"></p>
    </div>

    <!-- Footer -->
    <div class="px-6 py-4 flex items-center justify-end gap-3 bg-gray-50/60 dark:bg-gray-800/40 border-t border-gray-100 dark:border-gray-800">
      <button
        type="button"
        id="confirmModalCancelBtn"
        class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
      >
        Batal
      </button>
      <button
        type="button"
        id="confirmModalConfirmBtn"
        class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white shadow-md hover:brightness-110 focus:outline-none focus:ring-2 transition-all bg-brand-500 focus:ring-brand-500/40"
      >
        <span id="confirmModalConfirmText">Ya, Lanjutkan</span>
      </button>
    </div>
  </div>
</div>

<script>
(function() {
  const overlay = document.getElementById('confirmModalOverlay');
  const dialog = document.getElementById('confirmModalDialog');
  const titleEl = document.getElementById('confirmModalTitle');
  const msgEl = document.getElementById('confirmModalMessage');
  const iconWrapper = document.getElementById('confirmModalIconWrapper');
  const iconEl = document.getElementById('confirmModalIcon');
  const confirmBtn = document.getElementById('confirmModalConfirmBtn');
  const confirmTextEl = document.getElementById('confirmModalConfirmText');
  const cancelBtn = document.getElementById('confirmModalCancelBtn');
  const closeBtn = document.getElementById('confirmModalCloseBtn');

  let currentOnConfirm = null;
  let isOpen = false;

  const toneStyles = {
    primary: {
      iconWrapper: ['bg-brand-50', 'text-brand-500', 'dark:bg-brand-500/10', 'dark:text-brand-400'],
      btn: ['bg-brand-500', 'focus:ring-brand-500/40']
    },
    warning: {
      iconWrapper: ['bg-amber-50', 'text-amber-600', 'dark:bg-amber-500/10', 'dark:text-amber-400'],
      btn: ['bg-amber-500', 'focus:ring-amber-500/40']
    },
    danger: {
      iconWrapper: ['bg-red-50', 'text-red-600', 'dark:bg-red-500/10', 'dark:text-red-400'],
      btn: ['bg-red-600', 'focus:ring-red-500/40']
    }
  };

  const allIconWrapperClasses = [
    'bg-brand-50', 'text-brand-500', 'dark:bg-brand-500/10', 'dark:text-brand-400',
    'bg-amber-50', 'text-amber-600', 'dark:bg-amber-500/10', 'dark:text-amber-400',
    'bg-red-50', 'text-red-600', 'dark:bg-red-500/10', 'dark:text-red-400'
  ];

  const allBtnClasses = [
    'bg-brand-500', 'focus:ring-brand-500/40',
    'bg-amber-500', 'focus:ring-amber-500/40',
    'bg-red-600', 'focus:ring-red-500/40'
  ];

  function closeConfirm() {
    if (!isOpen) return;
    isOpen = false;
    currentOnConfirm = null;
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

  window.openConfirmDialog = function(opts = {}) {
    const title = opts.title || 'Konfirmasi';
    const message = opts.message || '';
    const icon = opts.icon || 'fa-triangle-exclamation';
    const tone = opts.tone || 'primary';
    const confirmText = opts.confirmText || 'Ya, Lanjutkan';
    currentOnConfirm = typeof opts.onConfirm === 'function' ? opts.onConfirm : null;

    if (titleEl) titleEl.textContent = title;
    if (msgEl) msgEl.textContent = message;
    if (confirmTextEl) confirmTextEl.textContent = confirmText;

    if (iconEl) {
      iconEl.className = 'fa-solid ' + icon;
    }

    const currentTone = toneStyles[tone] || toneStyles.primary;
    if (iconWrapper) {
      iconWrapper.classList.remove(...allIconWrapperClasses);
      iconWrapper.classList.add(...currentTone.iconWrapper);
    }

    if (confirmBtn) {
      confirmBtn.classList.remove(...allBtnClasses);
      confirmBtn.classList.add(...currentTone.btn);
    }

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

  if (confirmBtn) {
    confirmBtn.addEventListener('click', () => {
      const cb = currentOnConfirm;
      closeConfirm();
      if (typeof cb === 'function') {
        cb();
      }
    });
  }

  if (cancelBtn) cancelBtn.addEventListener('click', closeConfirm);
  if (closeBtn) closeBtn.addEventListener('click', closeConfirm);

  if (overlay) {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeConfirm();
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isOpen) {
      closeConfirm();
    }
  });
})();
</script>
