<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
$labels = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];

$fmt = fn ($v) => number_format((float) ($v ?? 0), 0, ',', '.');

// Render tabel rekap revenue bulanan (dipakai tab Domestic/Export/Country/Region)
$renderRevTable = function (array $rows, bool $withTotal = true) use ($months, $labels, $fmt): void {
    $totals = [];
    foreach ($months as $m) { $totals[$m] = 0.0; }
    $grandTotal = 0.0;
    foreach ($rows as $row) {
        foreach ($months as $m) { $totals[$m] += (float) ($row['data'][$m] ?? 0); }
        $grandTotal += (float) ($row['data']['total'] ?? 0);
    }
    ?>
    <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
        <thead class="bg-gray-100/80 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 text-[11px] font-bold tracking-wider uppercase border-b border-gray-200 dark:border-gray-700">
            <tr>
                <th class="px-5 py-3.5 font-bold sticky left-0 z-10 bg-gray-100/90 dark:bg-gray-800 min-w-[210px] shadow-[2px_0_4px_-2px_rgba(0,0,0,0.06)] border-r border-gray-200/60 dark:border-gray-700/60">
                    <?= $withTotal ? 'Channel / Category' : 'Keterangan' ?>
                </th>
                <?php foreach ($labels as $label): ?>
                    <th class="px-3.5 py-3.5 text-right whitespace-nowrap min-w-[90px]"><?= $label ?></th>
                <?php endforeach; ?>
                <th class="px-5 py-3.5 text-right font-extrabold bg-gray-200/60 dark:bg-gray-800 text-gray-900 dark:text-white sticky right-0 z-10 shadow-[-2px_0_4px_-2px_rgba(0,0,0,0.06)] min-w-[120px]">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200/80 dark:divide-gray-800/80">
            <?php foreach ($rows as $row): ?>
            <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-800/50 transition-colors">
                <td class="px-5 py-3.5 font-semibold text-gray-900 dark:text-white sticky left-0 z-10 bg-white dark:bg-gray-900 border-r border-gray-200/40 dark:border-gray-800 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.04)]">
                    <?= esc($row['label']) ?>
                </td>
                <?php foreach ($months as $m): ?>
                <td class="px-3.5 py-3.5 text-right font-mono font-medium tabular-nums text-gray-700 dark:text-gray-300">
                    <?= $fmt($row['data'][$m] ?? 0) ?>
                </td>
                <?php endforeach; ?>
                <td class="px-5 py-3.5 text-right font-mono font-bold text-gray-900 dark:text-white bg-gray-50/80 dark:bg-gray-800/40 sticky right-0 z-10 shadow-[-2px_0_4px_-2px_rgba(0,0,0,0.04)]">
                    <?= $fmt($row['data']['total'] ?? 0) ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if ($withTotal): ?>
            <tr class="bg-primary/5 dark:bg-primary/10 font-bold text-gray-900 dark:text-white border-t-2 border-primary/20">
                <td class="px-5 py-4 sticky left-0 z-10 bg-gray-50/90 dark:bg-gray-900 font-extrabold text-gray-900 dark:text-white border-r border-gray-200/50 dark:border-gray-800 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.05)]">
                    TOTAL SALES
                </td>
                <?php foreach ($months as $m): ?>
                <td class="px-3.5 py-4 text-right font-mono text-primary font-bold tabular-nums">
                    <?= $fmt($totals[$m]) ?>
                </td>
                <?php endforeach; ?>
                <td class="px-5 py-4 text-right font-mono text-primary font-extrabold bg-primary/10 dark:bg-primary/20 sticky right-0 z-10 shadow-[-2px_0_4px_-2px_rgba(0,0,0,0.06)] text-sm">
                    <?= $fmt($grandTotal) ?>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
};
?>

<div x-data="salesSummaryPage()" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">

    <!-- Page Header & Action Controls -->
    <div class="flex flex-col gap-5 mb-5 sm:flex-row sm:items-center sm:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="space-y-1.5">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Sales Summary & Discount Reclass</h2>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Ringkasan revenue penjualan domestic, export, breakdown wilayah, serta alokasi reclass diskon bulanan.</p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <button @click="$dispatch('open-upload-modal', { type: 'domestic' })"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4.5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-xs hover:bg-emerald-700 active:scale-[0.98] transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload Excel
            </button>
            <a href="<?= base_url('sales/exportExcel') ?>"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 dark:bg-gray-800 px-4.5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-xs hover:bg-gray-800 dark:hover:bg-gray-700 active:scale-[0.98] transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Segmented Navigation Tabs -->
    <div class="bg-gray-100/80 dark:bg-gray-800/60 p-1.5 rounded-2xl border border-gray-200/80 dark:border-gray-700/60 shadow-xs">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar">
            <template x-for="tab in tabs" :key="tab.key">
                <button @click="activeTab = tab.key"
                        :class="activeTab === tab.key 
                            ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm font-bold border border-gray-200/80 dark:border-gray-700' 
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-gray-800/50 font-medium border border-transparent'"
                        class="px-5 py-2.5 rounded-xl text-xs sm:text-sm transition-all duration-150 whitespace-nowrap flex items-center gap-2 cursor-pointer">
                    <span x-text="tab.label"></span>
                </button>
            </template>
        </nav>
    </div>

    <!-- ============================================================ -->
    <!-- TAB: DOMESTIC SUMMARY                                       -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'domestic'" class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden transition-all">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 gap-3 bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Rekap Revenue Domestic (Rp)
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Agregasi transaksi penjualan domestik dari <code class="font-mono text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-[11px]">yp_plan__trans_sales_domestic</code></p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 px-4 py-1.5 text-xs font-bold shrink-0 self-start sm:self-auto shadow-2xs">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Total Tahun: <span class="font-mono font-extrabold"><?= $fmt($summary['domestic']['total'] ?? 0) ?></span>
            </span>
        </div>
        <div class="overflow-x-auto">
            <?php $renderRevTable([['label' => 'Domestic Sales', 'data' => $summary['domestic']]]); ?>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB: EXPORT SUMMARY                                         -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'export'" x-cloak class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden transition-all">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 gap-3 bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Rekap Revenue Export (Rp)
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Agregasi transaksi penjualan ekspor dari <code class="font-mono text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-[11px]">yp_plan__trans_sales_export</code></p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 px-4 py-1.5 text-xs font-bold shrink-0 self-start sm:self-auto shadow-2xs">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Total Tahun: <span class="font-mono font-extrabold"><?= $fmt($summary['export']['total'] ?? 0) ?></span>
            </span>
        </div>
        <div class="overflow-x-auto">
            <?php $renderRevTable([['label' => 'Export Sales', 'data' => $summary['export']]]); ?>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB: COUNTRY SUMMARY (domestic + export breakdown)          -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'country'" x-cloak class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden transition-all">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 gap-3 bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Rekap Revenue per Country (Rp)
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Breakdown agregasi wilayah & negara dari data penjualan domestic dan export</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 px-4 py-1.5 text-xs font-bold shrink-0 self-start sm:self-auto shadow-2xs">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Total Tahun: <span class="font-mono font-extrabold"><?= $fmt(array_sum(array_column($country, 'total'))) ?></span>
            </span>
        </div>
        <div class="overflow-x-auto">
            <?php if (empty($country)): ?>
                <div class="px-6 py-20 text-center text-gray-400 dark:text-gray-500 space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800/60 flex items-center justify-center mx-auto text-gray-400 dark:text-gray-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 002.5-2.5V11a2 2 0 012-2h1.405M14 3.935A10.003 10.003 0 0121 12c0 5.523-4.477 10-10 10S1 17.523 1 12C1 7.28 4.28 3.328 8.7 2.45"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Belum ada data summary per country</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Silakan upload atau tambahkan transaksi sales per negara.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php $renderRevTable(array_map(fn ($r) => ['label' => ($r['label'] ?? '-'), 'data' => $r], $country)); ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB: REGION SUMMARY (domestic + export breakdown)           -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'region'" x-cloak class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden transition-all">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 gap-3 bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Rekap Revenue per Region (Rp)
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Breakdown agregasi wilayah per region dari data penjualan domestic dan export</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 px-4 py-1.5 text-xs font-bold shrink-0 self-start sm:self-auto shadow-2xs">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Total Tahun: <span class="font-mono font-extrabold"><?= $fmt(array_sum(array_column($region, 'total'))) ?></span>
            </span>
        </div>
        <div class="overflow-x-auto">
            <?php if (empty($region)): ?>
                <div class="px-6 py-20 text-center text-gray-400 dark:text-gray-500 space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800/60 flex items-center justify-center mx-auto text-gray-400 dark:text-gray-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 002.5-2.5V11a2 2 0 012-2h1.405M14 3.935A10.003 10.003 0 0121 12c0 5.523-4.477 10-10 10S1 17.523 1 12C1 7.28 4.28 3.328 8.7 2.45"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Belum ada data summary per region</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Silakan upload atau tambahkan transaksi sales per region.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php $renderRevTable(array_map(fn ($r) => ['label' => ($r['label'] ?? '-'), 'data' => $r], $region)); ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB: DISCOUNT RECLASSIFICATION (terhubung backend)           -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'reclass'" x-cloak class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 sm:p-8 shadow-xs space-y-6 transition-all">
        <div class="border-b border-gray-200/80 dark:border-gray-800 pb-5">
            <h3 class="text-base font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Discount Allocation Input
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Simpan alokasi diskon bulanan ke <code class="font-mono text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-[11px]">yp_plan__master_reclass_monthly</code> (mode replace per tahun).</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-5">
            <template x-for="(m, i) in months" :key="m">
                <div class="bg-gray-50/60 dark:bg-gray-800/40 p-3 rounded-xl border border-gray-200/60 dark:border-gray-800 space-y-1.5 focus-within:border-primary/50 focus-within:bg-white dark:focus-within:bg-gray-800 transition-all">
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider" x-text="m.toUpperCase()"></label>
                    <input type="number" x-model.number="discount[m]"
                           class="w-full rounded-lg border border-gray-300/80 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-xs font-mono font-semibold text-right text-gray-900 dark:text-white shadow-2xs focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                           placeholder="0">
                </div>
            </template>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-t border-gray-200/80 dark:border-gray-800 pt-6 mt-8 gap-4">
            <div class="inline-flex items-center gap-3 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                <span>Total Allocation:</span>
                <span class="font-extrabold font-mono text-primary text-base" x-text="fmtNumber(totalDiscount)"></span>
            </div>
            <button type="button" @click="saveDiscount()" :disabled="saving"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-xs hover:bg-primary-dark active:scale-[0.98] transition-all disabled:opacity-50 cursor-pointer">
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
        tabs: [
            { key: 'domestic', label: 'Domestic' },
            { key: 'export',   label: 'Export' },
            { key: 'country',  label: 'Country' },
            { key: 'region',   label: 'Region' },
            { key: 'reclass',  label: 'Discount Reclass' },
        ],
        activeTab: 'domestic',
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

