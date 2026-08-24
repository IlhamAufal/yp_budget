<?php
/*
 * partials/toast.php
 *
 * 1) #toast-container — Vanilla JS stack component, SELALU dirender.
 *    Menangkap event 'show-toast' dari window dan menampilkan toast.
 *
 * 2) window.showToast(type, message) — API global. Semua views memanggil
 *    ini secara langsung setelah operasi AJAX berhasil / gagal.
 *
 * 3) PHP flashdata — trigger showToast() langsung via inline <script>
 *    agar redirect-based feedback juga masuk ke stack.
 */
?>

<!-- ============================================================ -->
<!-- Toast Stack Container (JS-driven, selalu ada di DOM)         -->
<!-- ============================================================ -->
<div id="toast-container"
     class="fixed bottom-5 right-5 z-[999999] flex flex-col-reverse gap-3 items-end pointer-events-none"
     style="max-width: 24rem;"></div>

<!-- ============================================================ -->
<!-- Toast Engine — window.showToast() (Vanilla JS)               -->
<!-- ============================================================ -->
<script>
(function () {
    'use strict';

    var container = document.getElementById('toast-container');
    var counter = 0;

    var ICONS = {
        success: '<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        error:   '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        info:    '<svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        warning: '<svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    };

    var BORDERS = {
        success: 'border-emerald-500/30',
        error:   'border-red-500/30',
        info:    'border-brand-500/30',
        warning: 'border-brand-500/30'
    };

    function buildNode(type, message) {
        var el = document.createElement('div');
        el.className = 'w-full pointer-events-auto bg-white dark:bg-gray-800 rounded-xl shadow-xl border p-4 flex items-start gap-3 transition-all duration-300 opacity-0 translate-y-2';
        if (BORDERS[type]) el.classList.add(BORDERS[type]);

        var iconWrap = document.createElement('div');
        iconWrap.className = 'shrink-0 mt-0.5';
        iconWrap.innerHTML = ICONS[type] || ICONS.info;

        var msg = document.createElement('div');
        msg.className = 'flex-1 text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200';
        msg.textContent = message;

        var btn = document.createElement('button');
        btn.className = 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors';
        btn.setAttribute('aria-label', 'Tutup');
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
        btn.addEventListener('click', function () { removeToast(el); });

        el.appendChild(iconWrap);
        el.appendChild(msg);
        el.appendChild(btn);
        return el;
    }

    function removeToast(el) {
        if (!el || !el.parentNode) return;
        el.classList.add('opacity-0', 'translate-y-2');
        setTimeout(function () {
            if (el.parentNode) el.parentNode.removeChild(el);
        }, 250);
    }

    /**
     * window.showToast(type, message)
     * API global untuk memunculkan toast dari mana saja.
     * type: 'success' | 'error' | 'info' | 'warning'
     */
    window.showToast = function (type, message) {
        if (!container) return;

        var el = buildNode(type, message);
        container.appendChild(el);

        // Masuk: fade + slide-up
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                el.classList.remove('opacity-0', 'translate-y-2');
            });
        });

        // Auto-dismiss setelah 5 detik
        setTimeout(function () { removeToast(el); }, 5000);
    };

    // Alias untuk kompatibilitas
    window.ypToast = window.showToast;

    // Kompatibilitas: view lama yang masih dispatch CustomEvent('show-toast')
    window.addEventListener('show-toast', function (e) {
        var d = e.detail || {};
        window.showToast(d.type || 'info', d.message || '');
    });
})();
</script>

<!-- ============================================================ -->
<!-- PHP Flashdata → showToast (setelah redirect)                 -->
<!-- ============================================================ -->
<?php if (session()->getFlashdata('success')): ?>
<script>window.showToast('success', '<?= esc(session()->getFlashdata('success'), 'js') ?>');</script>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<script>window.showToast('error', '<?= esc(session()->getFlashdata('error'), 'js') ?>');</script>
<?php endif; ?>
<?php if (session()->getFlashdata('info')): ?>
<script>window.showToast('info', '<?= esc(session()->getFlashdata('info'), 'js') ?>');</script>
<?php endif; ?>
