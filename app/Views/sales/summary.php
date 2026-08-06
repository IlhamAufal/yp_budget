<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
$labels = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
?>
<div x-data="salesSummaryPage()" class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Sales Summary & Discount Reclass</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Working Year: <span class="font-semibold text-primary"><?= esc($workingYear) ?></span></p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="$dispatch('open-upload-modal', { type: 'domestic' })"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-xs hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload Excel
            </button>
            <a href="<?= base_url('sales/exportExcel') ?>"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-800 px-4 py-2.5 text-sm font-medium text-white shadow-xs hover:bg-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    <div class="border-b border-gray-200 dark:border-gray-800">
        <nav class="-mb-px flex space-x-8">
            <button @click="activeTab = 'summary'"
                    :class="activeTab === 'summary' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Sales Summary Recap (IDR)
            </button>
            <button @click="activeTab = 'reclass'"
                    :class="activeTab === 'reclass' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Discount Reclassification
            </button>
        </nav>
    </div>

    <!-- ============================================================ -->
    <!-- TAB: SALES SUMMARY RECAP (data riil IDR)                     -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'summary'" class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <div>
                <h3 class="text-sm font-bold text-gray-800 dark:text-white">Rekap Revenue Sales (Rp)</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Agregasi dari trans_sales_domestic & trans_sales_export</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 px-3 py-1 text-xs font-bold">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Total Tahun: <span x-text="fmtNumber(summary.total.total)"></span>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th class="px-4 py-3 font-semibold sticky left-0 bg-gray-50 dark:bg-gray-800/60 min-w-[180px]">Channel / Category</th>
                        <?php foreach ($labels as $label): ?>
                            <th class="px-3 py-3 text-right"><?= $label ?></th>
                        <?php endforeach; ?>
                        <th class="px-4 py-3 text-right font-bold bg-gray-100 dark:bg-gray-800">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-900">Domestic Sales</td>
                        <?php foreach ($months as $m): ?>
                            <td class="px-3 py-3 text-right" x-text="fmtNumber(<?= (float) ($summary['domestic'][$m] ?? 0) ?>)"></td>
                        <?php endforeach; ?>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800/40" x-text="fmtNumber(<?= (float) ($summary['domestic']['total'] ?? 0) ?>)"></td>
                    </tr>
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-900">Export Sales</td>
                        <?php foreach ($months as $m): ?>
                            <td class="px-3 py-3 text-right" x-text="fmtNumber(<?= (float) ($summary['export'][$m] ?? 0) ?>)"></td>
                        <?php endforeach; ?>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800/40" x-text="fmtNumber(<?= (float) ($summary['export']['total'] ?? 0) ?>)"></td>
                    </tr>
                    <tr class="bg-gray-50/60 dark:bg-gray-800/30 font-bold text-gray-900 dark:text-white">
                        <td class="px-4 py-3 sticky left-0 bg-gray-50 dark:bg-gray-800/30">TOTAL SALES</td>
                        <?php foreach ($months as $m): ?>
                            <td class="px-3 py-3 text-right text-primary" x-text="fmtNumber(<?= (float) ($summary['total'][$m] ?? 0) ?>)"></td>
                        <?php endforeach; ?>
                        <td class="px-4 py-3 text-right text-primary bg-gray-100 dark:bg-gray-800" x-text="fmtNumber(<?= (float) ($summary['total']['total'] ?? 0) ?>)"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB: DISCOUNT RECLASSIFICATION (terhubung backend)           -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'reclass'" x-cloak class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-xs space-y-4">
        <div>
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Discount Allocation Input</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Simpan alokasi diskon bulanan ke yp_plan__master_reclass_monthly (mode replace per tahun)</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <template x-for="(m, i) in months" :key="m">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 uppercase mb-1" x-text="m.toUpperCase()"></label>
                    <input type="number" x-model.number="discount[m]"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-xs text-right text-gray-900 dark:text-white focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </template>
        </div>

        <div class="flex items-center justify-between border-t border-gray-100 dark:border-gray-800 pt-4">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Total Allocation: <span class="font-bold text-primary" x-text="fmtNumber(totalDiscount)"></span>
            </div>
            <button type="button" @click="saveDiscount()" :disabled="saving"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2 text-sm font-medium text-white shadow-xs hover:bg-primary-dark transition-colors disabled:opacity-50">
                <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span x-text="saving ? 'Menyimpan...' : 'Save Discount Allocation'"></span>
            </button>
        </div>
    </div>

</div>

<?= $this->include('sales/upload_modal') ?>

<script>
function salesSummaryPage() {
    const MONTHS = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

    return {
        activeTab: 'summary',
        months: MONTHS,
        saving: false,

        discount: {
            jan: <?= (float) ($discount['jan'] ?? 0) ?>, feb: <?= (float) ($discount['feb'] ?? 0) ?>,
            mar: <?= (float) ($discount['mar'] ?? 0) ?>, apr: <?= (float) ($discount['apr'] ?? 0) ?>,
            may: <?= (float) ($discount['may'] ?? 0) ?>, jun: <?= (float) ($discount['jun'] ?? 0) ?>,
            jul: <?= (float) ($discount['jul'] ?? 0) ?>, aug: <?= (float) ($discount['aug'] ?? 0) ?>,
            sep: <?= (float) ($discount['sep'] ?? 0) ?>, oct: <?= (float) ($discount['oct'] ?? 0) ?>,
            nov: <?= (float) ($discount['nov'] ?? 0) ?>, dec: <?= (float) ($discount['dec'] ?? 0) ?>,
        },

        summary: {
            domestic: {<?php foreach ($months as $m): ?><?= $m ?>: <?= (float) ($summary['domestic'][$m] ?? 0) ?>,<?php endforeach; ?> total: <?= (float) ($summary['domestic']['total'] ?? 0) ?>},
            export: {<?php foreach ($months as $m): ?><?= $m ?>: <?= (float) ($summary['export'][$m] ?? 0) ?>,<?php endforeach; ?> total: <?= (float) ($summary['export']['total'] ?? 0) ?>},
            total: {<?php foreach ($months as $m): ?><?= $m ?>: <?= (float) ($summary['total'][$m] ?? 0) ?>,<?php endforeach; ?> total: <?= (float) ($summary['total']['total'] ?? 0) ?>},
        },

        get totalDiscount() {
            return MONTHS.reduce((sum, m) => sum + Number(this.discount[m] || 0), 0);
        },

        fmtNumber(v) {
            return Number(v || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 });
        },

        async saveDiscount() {
            this.saving = true;
            const res = await window.ypFetch('<?= base_url('sales/saveDiscountReclass') ?>', { ...this.discount });
            this.saving = false;

            if (res.status === 'success') {
                window.showToast('success', res.message || 'Alokasi diskon berhasil disimpan.');
            } else {
                window.showToast('error', res.message || 'Gagal menyimpan alokasi diskon.');
            }
        },
    };
}
</script>

<?= $this->endSection() ?>
