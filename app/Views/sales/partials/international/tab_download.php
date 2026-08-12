<?php
$staticTemplatePath = 'assets/docs/templates/sales/international/TEMPLATE_COGS_DLP_INTERNATIONAL.xlsx';
$staticTemplateExists = is_file(FCPATH . $staticTemplatePath);
?>
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-6">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-emerald-600"></i> Download Template Excel Sales International
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gunakan generator dinamis untuk template berdasarkan data tahun berjalan, atau unduh template COGS International yang diimpor.</p>
        </div>

        <div class="bg-slate-50 dark:bg-gray-800/60 p-5 rounded-xl border border-gray-200/80 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h4 class="text-xs font-bold text-gray-900 dark:text-white">Template Data Upload Budget (International)</h4>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Format dinamis untuk pengisian data detail transaksi international.</p>
            </div>
            <button type="button" @click="downloadExportTemplate()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-download"></i> Unduh Template Dinamis (.xlsx)
            </button>
        </div>

        <!-- <div class="flex flex-col gap-4 rounded-xl border border-emerald-200 bg-emerald-50/70 p-5 dark:border-emerald-900/50 dark:bg-emerald-950/20 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h4 class="text-xs font-bold text-gray-900 dark:text-white">Template COGS DLP International</h4>
                <p class="mt-1 text-[11px] text-gray-600 dark:text-gray-300">File Excel statis hasil impor untuk kebutuhan COGS DLP Sales International.</p>
            </div>
            <?php if ($staticTemplateExists): ?>
                <a href="<?= base_url($staticTemplatePath) ?>" download class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-semibold text-white shadow-xs transition hover:bg-emerald-700">
                    <i class="fa-solid fa-file-arrow-down"></i> Unduh File COGS (.xlsx)
                </a>
            <?php else: ?>
                <button type="button" disabled class="inline-flex shrink-0 cursor-not-allowed items-center gap-2 rounded-xl bg-gray-200 px-5 py-2.5 text-xs font-semibold text-gray-400 dark:bg-gray-800 dark:text-gray-600">
                    <i class="fa-solid fa-file-circle-xmark"></i> File Belum Tersedia
                </button>
            <?php endif; ?>
        </div> -->
    </div>
</div>
