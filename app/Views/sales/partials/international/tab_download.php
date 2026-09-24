<?php
$staticTemplatePath = 'assets/docs/templates/sales/international/TEMPLATE_COGS_DLP_INTERNATIONAL.xlsx';
$staticTemplateExists = is_file(FCPATH . $staticTemplatePath);
?>
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-6">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Download Template Excel Sales International</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unduh template dinamis pengisian data target volume dan revenue Sales International (USD $).</p>
        </div>

        <div class="bg-gray-50/50 dark:bg-gray-800/40 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Template Data Upload Budget (International)</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Format dinamis untuk pengisian data detail transaksi international.</p>
            </div>
            <button type="button" @click="downloadExportTemplate()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98] shrink-0">
                <i class="fa-solid fa-download"></i>
                <span>Download Template (.xlsx)</span>
            </button>
        </div>
    </div>
</div>
