<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="w-full space-y-5" x-data="manualBookHandler()">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-default">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Manual Book - Input OPEX GA</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Panduan standar penggunaan dan pengisian alokasi anggaran OPEX GA</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a :href="pdfUrl" download="MANUAL BOOK - BUDGET SYSTEM - INPUT OPEX GA.pdf" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-xs font-medium hover:bg-opacity-90 transition-colors">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                    <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/>
                </svg>
                <span>Unduh Manual Book</span>
            </a>
        </div>
    </div>

    <div class="w-full bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-default overflow-hidden min-h-[750px]">
        <iframe :src="pdfUrl" class="w-full h-[750px] border-0 rounded-2xl" title="Manual Book PDF Viewer"></iframe>
    </div>
</div>

<script>
function manualBookHandler() {
    return {
        pdfUrl: '<?= base_url('assets/manual_book/MANUAL BOOK - BUDGET SYSTEM - INPUT OPEX GA.pdf'); ?>'
    }
}
</script>
<?= $this->endSection() ?>