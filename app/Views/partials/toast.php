<div
  x-data="{
    toasts: [],
    addToast(type, message) {
      const id = Date.now();
      this.toasts.push({ id, type, message });
      setTimeout(() => { this.removeToast(id); }, 4500);
    },
    removeToast(id) {
      this.toasts = this.toasts.filter(t => t.id !== id);
    }
  }"
  x-init="
    /* Event Listener untuk JS / Custom Events */
    window.addEventListener('show-toast', e => {
      if (e.detail && e.detail.message) {
        addToast(e.detail.type || 'info', e.detail.message);
      }
    });

    /* Global Window Helper */
    window.showToast = (type, message) => {
      window.dispatchEvent(new CustomEvent('show-toast', { detail: { type, message } }));
    };
    window.ypToast = (message, type = 'success') => {
      window.dispatchEvent(new CustomEvent('show-toast', { detail: { type, message } }));
    };

    /* Flashdata CI4 Auto Trigger */
    <?php if (session()->getFlashdata('success')): ?>
      addToast('success', '<?= esc(session()->getFlashdata('success'), 'js') ?>');
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      addToast('error', '<?= esc(session()->getFlashdata('error'), 'js') ?>');
    <?php endif; ?>
    <?php if (session()->getFlashdata('info')): ?>
      addToast('info', '<?= esc(session()->getFlashdata('info'), 'js') ?>');
    <?php endif; ?>
  "
  class="fixed top-5 right-5 z-[999999] flex flex-col gap-3 max-w-sm w-full pointer-events-none"
>
  <template x-for="toast in toasts" :key="toast.id">
    <div
      x-show="true"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 translate-y-2 scale-95"
      x-transition:enter-end="opacity-100 translate-y-0 scale-100"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100 translate-y-0 scale-100"
      x-transition:leave-end="opacity-0 translate-y-2 scale-95"
      class="pointer-events-auto flex items-center justify-between gap-3 p-4 rounded-xl shadow-xl border text-sm font-medium transition-all"
      :class="{
        'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-900/80 dark:text-emerald-200 dark:border-emerald-800': toast.type === 'success',
        'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-900/80 dark:text-rose-200 dark:border-rose-800': toast.type === 'error',
        'bg-sky-50 text-sky-800 border-sky-200 dark:bg-sky-900/80 dark:text-sky-200 dark:border-sky-800': toast.type === 'info' || toast.type === 'warning'
      }"
    >
      <div class="flex items-center gap-3">
        <template x-if="toast.type === 'success'">
          <i class="fa-solid fa-circle-check text-lg text-emerald-500"></i>
        </template>
        <template x-if="toast.type === 'error'">
          <i class="fa-solid fa-circle-exclamation text-lg text-rose-500"></i>
        </template>
        <template x-if="toast.type === 'info' || toast.type === 'warning'">
          <i class="fa-solid fa-circle-info text-lg text-sky-500"></i>
        </template>

        <span x-text="toast.message"></span>
      </div>

      <button
        @click="removeToast(toast.id)"
        class="opacity-70 hover:opacity-100 p-1 rounded-lg transition shrink-0"
      >
        <i class="fa-solid fa-xmark text-base"></i>
      </button>
    </div>
  </template>
</div>