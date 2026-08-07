<?php
/*
 * partials/toast.php
 *
 * 1) #toast-container — Alpine.js stack component, SELALU dirender.
 *    Menangkap event 'show-toast' dari window dan menampilkan toast.
 *
 * 2) window.showToast(type, message) — API global. Semua views memanggil
 *    ini secara langsung setelah operasi AJAX berhasil / gagal.
 *
 * 3) PHP flashdata — setelah <script> selesai, trigger showToast() via
 *    inline <script> agar redirect-based feedback juga masuk ke stack.
 */
?>

<!-- ============================================================ -->
<!-- Toast Stack Container (JS-driven, selalu ada di DOM)         -->
<!-- ============================================================ -->
<div id="toast-container"
     x-data="toastManager()"
     x-on:show-toast.window="add($event.detail)"
     class="fixed bottom-5 right-5 z-[999999] flex flex-col-reverse gap-3 items-end pointer-events-none"
     style="max-width: 24rem;">

    <template x-for="(toast, i) in toasts" :key="toast.id">
        <div class="w-full pointer-events-auto bg-white dark:bg-gray-800 rounded-xl shadow-xl border p-4 flex items-start gap-3"
             :class="{
                'border-emerald-500/30': toast.type === 'success',
                'border-red-500/30':     toast.type === 'error',
                'border-brand-500/30':   toast.type === 'info' || toast.type === 'warning'
             }"
             x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             x-cloak>

            <div class="shrink-0 mt-0.5">
                <template x-if="toast.type === 'success'">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="toast.type === 'info' || toast.type === 'warning'">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
            </div>

            <div class="flex-1 text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200" x-text="toast.message"></div>

            <button @click="remove(i)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>

<!-- ============================================================ -->
<!-- Toast Engine — toastManager() + window.showToast()           -->
<!-- ============================================================ -->
<script>
    function toastManager() {
        return {
            toasts: [],
            _counter: 0,

            add(detail) {
                const id = ++this._counter;
                const toast = {
                    id,
                    type:    detail.type    || 'info',
                    message: detail.message || '',
                    visible: true,
                };
                this.toasts.push(toast);
                // Auto-dismiss setelah 5 detik
                setTimeout(() => {
                    const idx = this.toasts.findIndex(t => t.id === id);
                    if (idx !== -1) this.remove(idx);
                }, 5000);
            },

            remove(index) {
                if (index < 0 || index >= this.toasts.length) return;
                this.toasts[index].visible = false;
                // Bersihkan dari array setelah animasi leave (250ms)
                setTimeout(() => { this.toasts.splice(index, 1); }, 250);
            }
        };
    }

    /**
     * window.showToast(type, message)
     * API global untuk memunculkan toast dari mana saja.
     * type: 'success' | 'error' | 'info' | 'warning'
     */
    window.showToast = function (type, message) {
        window.dispatchEvent(new CustomEvent('show-toast', {
            detail: { type: type, message: message }
        }));
    };

    // Alias untuk kompatibilitas
    window.ypToast = window.showToast;
</script>

<!-- ============================================================ -->
<!-- PHP Flashdata → showToast (setelah redirect)                 -->
<!-- ============================================================ -->
<?php if (session()->getFlashdata('success')): ?>
<script>document.addEventListener('alpine:init', () => window.showToast('success', '<?= esc(session()->getFlashdata('success'), 'js') ?>'));</script>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<script>document.addEventListener('alpine:init', () => window.showToast('error', '<?= esc(session()->getFlashdata('error'), 'js') ?>'));</script>
<?php endif; ?>
<?php if (session()->getFlashdata('info')): ?>
<script>document.addEventListener('alpine:init', () => window.showToast('info', '<?= esc(session()->getFlashdata('info'), 'js') ?>'));</script>
<?php endif; ?>