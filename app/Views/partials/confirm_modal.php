<!-- ============================================================ -->
<!-- PARTIAL: Confirm Modal Reusable (Alpine)                    -->
<!-- Pemakaian: window.openConfirmDialog({ title, message, icon, -->
<!--   tone, confirmText, onConfirm })                            -->
<!-- ============================================================ -->
<div
  x-data="confirmBox()"
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
      class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden"
      role="dialog"
      aria-modal="true"
    >
      <!-- Header -->
      <div class="flex items-start justify-between gap-4 p-6 pb-0">
        <div class="flex items-center gap-4">
          <span
            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-lg"
            :class="{
              'bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400': tone === 'primary',
              'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400': tone === 'warning',
              'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400': tone === 'danger'
            }"
          >
            <i class="fa-solid" :class="icon"></i>
          </span>
          <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="title"></h3>
          </div>
        </div>
        <button
          type="button"
          @click="open = false"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 -m-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
          aria-label="Tutup"
        >
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <!-- Body -->
      <div class="p-6">
        <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-300" x-text="message"></p>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 flex items-center justify-end gap-3 bg-gray-50/60 dark:bg-gray-800/40 border-t border-gray-100 dark:border-gray-800">
        <button
          type="button"
          @click="open = false"
          class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
        >
          Batal
        </button>
        <button
          type="button"
          @click="confirm()"
          class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white shadow-md hover:brightness-110 focus:outline-none focus:ring-2 transition-all"
          :class="{
            'bg-brand-500 focus:ring-brand-500/40': tone === 'primary',
            'bg-amber-500 focus:ring-amber-500/40': tone === 'warning',
            'bg-red-600 focus:ring-red-500/40': tone === 'danger'
          }"
        >
          <span x-text="confirmText"></span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  /**
   * confirmBox() — komponen konfirmasi reusable.
   * Mendaftarkan window.openConfirmDialog(options):
   *   { title, message, icon (fa-class), tone ('primary'|'warning'|'danger'),
   *     confirmText, onConfirm (function) }
   */
  function confirmBox() {
    return {
      open: false,
      title: 'Konfirmasi',
      message: '',
      icon: 'fa-triangle-exclamation',
      tone: 'primary',
      confirmText: 'Ya, Lanjutkan',
      onConfirm: null,

      init() {
        window.openConfirmDialog = (opts = {}) => {
          this.title       = opts.title || 'Konfirmasi';
          this.message     = opts.message || '';
          this.icon        = opts.icon || 'fa-triangle-exclamation';
          this.tone        = opts.tone || 'primary';
          this.confirmText = opts.confirmText || 'Ya, Lanjutkan';
          this.onConfirm   = typeof opts.onConfirm === 'function' ? opts.onConfirm : null;
          this.open        = true;
        };
      },

      confirm() {
        if (typeof this.onConfirm === 'function') {
          this.onConfirm();
        }
        this.open = false;
      }
    };
  }
</script>
