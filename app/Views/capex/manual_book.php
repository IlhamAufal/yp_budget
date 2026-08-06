<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="manualBookHandler()" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Panduan & Manual Book CAPEX</h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Petunjuk operasional pengajuan, perhitungan penyusutan, dan sinkronisasi ke OPEX/FOH</p>
        </div>
        <div class="flex items-center gap-2">
            <?php if (!empty($pdfExists)): ?>
            <a :href="pdfUrl" download class="px-3.5 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors flex items-center gap-2 border border-gray-200/60 dark:border-gray-700">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Unduh PDF
            </a>
            <?php endif; ?>
            <button @click="toggleFullscreen()" class="px-4.5 py-2.5 bg-primary text-white rounded-xl text-xs sm:text-sm font-semibold hover:bg-primary-dark active:scale-[0.98] transition-all shadow-xs flex items-center gap-2 cursor-pointer">
                <svg x-show="!isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
                <svg x-show="isFullscreen" class="w-4 h-4" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span x-text="isFullscreen ? 'Keluarkan Mode Layar Penuh' : 'Layar Penuh'"></span>
            </button>
        </div>
    </div>

    <div id="pdf-container" class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden flex flex-col min-h-[750px]" :class="{ 'fixed inset-0 z-[999999] rounded-none': isFullscreen }">
        
        <div class="p-4 bg-gray-50/80 dark:bg-gray-800/60 border-b border-gray-200/80 dark:border-gray-800 flex flex-wrap justify-between items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-bold border border-primary/20">Dokumen Resmi</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium hidden sm:inline">CAPEX_User_Manual_v2.pdf</span>
            </div>
            
            <div class="text-xs text-gray-400 dark:text-gray-500">
                Gunakan fitur zoom di dalam viewer untuk tampilan lebih jelas.
            </div>
        </div>

        <?php if (!empty($pdfExists)): ?>
        <div class="flex-1 w-full h-full bg-gray-100 dark:bg-gray-950 relative">
            <iframe :src="pdfUrl" class="w-full h-full min-h-[700px] border-0" title="PDF Manual Book CAPEX">
                <div class="p-8 text-center space-y-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Peramban Anda tidak mendukung tampilan PDF langsung.</p>
                    <a :href="pdfUrl" download class="inline-flex items-center gap-2 text-xs font-semibold text-primary hover:underline">
                        Klik di sini untuk mengunduh dokumen PDF secara langsung
                    </a>
                </div>
            </iframe>
        </div>
        <?php else: ?>
        <div class="flex-1 w-full h-full bg-gray-100/50 dark:bg-gray-950/40 relative flex items-center justify-center p-12">
            <div class="text-center max-w-md space-y-3">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Dokumen Manual Book Belum Tersedia</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        File PDF belum diupload. Silakan letakkan file
                        <code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-xs font-mono">manual_book_capex.pdf</code>
                        pada folder <code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-xs font-mono">public/assets/docs/</code> lalu muat ulang halaman ini.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

</div>

<script>
function manualBookHandler() {
    return {
        isFullscreen: false,
        pdfUrl: '<?= esc($pdfUrl) ?>',
        toggleFullscreen() {
            this.isFullscreen = !this.isFullscreen;
            if (this.isFullscreen) {
                document.body.classList.add('overflow-hidden');
            } else {
                document.body.classList.remove('overflow-hidden');
            }
        }
    }
}
</script>
<?= $this->endSection() ?>