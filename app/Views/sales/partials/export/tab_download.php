<div class="space-y-6">
    <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-6">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-emerald-600"></i> Download Template Excel Sales International (Export)
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unduh berkas template resmi untuk pengisian masal data target Sales Export.</p>
        </div>
        <div class="bg-slate-50 dark:bg-gray-800/60 p-5 rounded-xl border border-gray-200/80 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h4 class="text-xs font-bold text-gray-900 dark:text-white">Template Data Upload Budget (EXPORTV2)</h4>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Format V2 untuk pengisian data detail transaksi ekspor.</p>
            </div>
            <button type="button" @click="downloadExportTemplate()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-download"></i> Unduh Template (.xlsx)
            </button>
        </div>
    </div>
</div>
