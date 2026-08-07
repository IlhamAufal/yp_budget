<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
$labels = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];

$fmt  = fn ($v) => number_format((float) ($v ?? 0), 0, ',', '.');
$fmt2 = fn ($v) => number_format((float) ($v ?? 0), 2, ',', '.');
$asp  = fn ($rev, $qty) => ((float) $qty) > 0 ? (float) $rev / (float) $qty : 0;

// ------------------------------------------------------------------
// Tabel rekap revenue bulanan (domestic / export / country / region)
// ------------------------------------------------------------------
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

// ------------------------------------------------------------------
// Tabel per channel: QTY / REVENUE / ASP per bulan + total
// ------------------------------------------------------------------
$renderChannelTable = function (array $rows) use ($months, $labels, $fmt, $fmt2, $asp): void {
    if (empty($rows)) {
        ?>
        <div class="px-6 py-20 text-center text-gray-400 dark:text-gray-500 space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800/60 flex items-center justify-center mx-auto text-gray-400 dark:text-gray-500">
                <i class="fa-solid fa-box-open text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Belum ada data transaksi</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Silakan unggah data sales terlebih dahulu.</p>
            </div>
        </div>
        <?php
        return;
    }

    $totQty = array_fill_keys($months, 0.0);
    $totRev = array_fill_keys($months, 0.0);
    $totQtyAll = $totRevAll = 0.0;
    foreach ($rows as $row) {
        foreach ($months as $m) {
            $totQty[$m] += (float) ($row["{$m}_qty"] ?? 0);
            $totRev[$m] += (float) ($row["{$m}_rev"] ?? 0);
        }
        $totQtyAll += (float) ($row['total_qty'] ?? 0);
        $totRevAll += (float) ($row['total_rev'] ?? 0);
    }
    ?>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse min-w-[1400px]">
            <thead class="bg-gray-100/80 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 text-[10px] font-bold uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th rowspan="2" class="px-4 py-3 sticky left-0 z-10 bg-gray-100 dark:bg-gray-800 min-w-[130px]">Channel</th>
                    <?php foreach ($labels as $i => $lb): ?>
                        <th colspan="3" class="px-2 py-3 text-center whitespace-nowrap <?= $i % 2 === 0 ? 'bg-sky-50/70 dark:bg-sky-900/20' : 'bg-blue-100/70 dark:bg-blue-900/20' ?>"><?= $lb ?></th>
                    <?php endforeach; ?>
                    <th colspan="3" class="px-2 py-3 text-center whitespace-nowrap bg-gray-200/70 dark:bg-gray-800">Total</th>
                </tr>
                <tr>
                    <?php for ($i = 0; $i < 13; $i++): ?>
                        <th class="px-2 py-2 text-right whitespace-nowrap <?= $i % 2 === 0 ? 'bg-sky-50/70 dark:bg-sky-900/20' : 'bg-blue-100/70 dark:bg-blue-900/20' ?>">Qty</th>
                        <th class="px-2 py-2 text-right whitespace-nowrap <?= $i % 2 === 0 ? 'bg-sky-50/70 dark:bg-sky-900/20' : 'bg-blue-100/70 dark:bg-blue-900/20' ?>">Rev</th>
                        <th class="px-2 py-2 text-right whitespace-nowrap <?= $i % 2 === 0 ? 'bg-sky-50/70 dark:bg-sky-900/20' : 'bg-blue-100/70 dark:bg-blue-900/20' ?>">ASP/kg</th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200/80 dark:divide-gray-800/80">
                <?php foreach ($rows as $row): ?>
                <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-4 py-3 font-bold text-gray-900 dark:text-white sticky left-0 z-10 bg-white dark:bg-gray-900 border-r border-gray-200/40 dark:border-gray-800">
                        <?= esc($row['id_channel'] ?? '-') ?>
                    </td>
                    <?php foreach ($months as $i => $m): ?>
                        <td class="px-2 py-3 text-right font-mono tabular-nums <?= $i % 2 === 0 ? 'bg-sky-50/30 dark:bg-sky-900/10' : '' ?>"><?= $fmt2($row["{$m}_qty"] ?? 0) ?></td>
                        <td class="px-2 py-3 text-right font-mono tabular-nums <?= $i % 2 === 0 ? 'bg-sky-50/30 dark:bg-sky-900/10' : '' ?>"><?= $fmt($row["{$m}_rev"] ?? 0) ?></td>
                        <td class="px-2 py-3 text-right font-mono tabular-nums text-gray-500 dark:text-gray-400 <?= $i % 2 === 0 ? 'bg-sky-50/30 dark:bg-sky-900/10' : '' ?>"><?= $fmt2($asp($row["{$m}_rev"] ?? 0, $row["{$m}_qty"] ?? 0)) ?></td>
                    <?php endforeach; ?>
                    <td class="px-2 py-3 text-right font-mono font-bold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800/40"><?= $fmt2($row['total_qty'] ?? 0) ?></td>
                    <td class="px-2 py-3 text-right font-mono font-bold text-primary dark:text-primary bg-gray-50 dark:bg-gray-800/40"><?= $fmt($row['total_rev'] ?? 0) ?></td>
                    <td class="px-2 py-3 text-right font-mono font-semibold text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/40"><?= $fmt2($asp($row['total_rev'] ?? 0, $row['total_qty'] ?? 0)) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="bg-primary/5 dark:bg-primary/10 border-t-2 border-primary/20 font-bold text-gray-900 dark:text-white">
                    <td class="px-4 py-3.5 sticky left-0 z-10 bg-gray-50 dark:bg-gray-900 font-extrabold">TOTAL</td>
                    <?php foreach ($months as $i => $m): ?>
                        <td class="px-2 py-3.5 text-right font-mono <?= $i % 2 === 0 ? 'bg-sky-50/30 dark:bg-sky-900/10' : '' ?>"><?= $fmt2($totQty[$m]) ?></td>
                        <td class="px-2 py-3.5 text-right font-mono <?= $i % 2 === 0 ? 'bg-sky-50/30 dark:bg-sky-900/10' : '' ?>"><?= $fmt($totRev[$m]) ?></td>
                        <td class="px-2 py-3.5 text-right font-mono text-gray-500 dark:text-gray-400 <?= $i % 2 === 0 ? 'bg-sky-50/30 dark:bg-sky-900/10' : '' ?>"><?= $fmt2($asp($totRev[$m], $totQty[$m])) ?></td>
                    <?php endforeach; ?>
                    <td class="px-2 py-3.5 text-right font-mono"><?= $fmt2($totQtyAll) ?></td>
                    <td class="px-2 py-3.5 text-right font-mono text-primary font-extrabold"><?= $fmt($totRevAll) ?></td>
                    <td class="px-2 py-3.5 text-right font-mono"><?= $fmt2($asp($totRevAll, $totQtyAll)) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php
};

$renderEmpty = function (string $title): void {
    ?>
    <div class="px-6 py-20 text-center text-gray-400 dark:text-gray-500 space-y-3">
        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800/60 flex items-center justify-center mx-auto text-gray-400 dark:text-gray-500">
            <i class="fa-solid fa-folder-open text-xl"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-gray-700 dark:text-gray-300"><?= esc($title) ?></p>
        </div>
    </div>
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
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                Rekap revenue domestic & international, breakdown per channel, delivery & customer claim, serta alokasi reclass diskon bulanan.
                Tahun Anggaran <span class="font-bold text-brand-500"><?= esc($workingYear) ?></span>
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <button @click="$dispatch('open-upload-modal', { type: 'domestic' })"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4.5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-xs hover:bg-emerald-700 active:scale-[0.98] transition-all">
                <i class="fa-solid fa-upload text-sm"></i>
                Upload Excel
            </button>
            <a href="<?= base_url('sales/exportExcel') ?>"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 dark:bg-gray-800 px-4.5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-xs hover:bg-gray-800 dark:hover:bg-gray-700 active:scale-[0.98] transition-all">
                <i class="fa-solid fa-file-export text-sm"></i>
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
    <!-- TAB: SUMMARY (ringkasan)                                    -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'summary'" class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-5 flex items-center gap-4">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400"><i class="fa-solid fa-truck-fast"></i></span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Domestic</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white"><?= $fmt($summary['domestic']['total'] ?? 0) ?></p>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-5 flex items-center gap-4">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-500 dark:bg-sky-500/10 dark:text-sky-400"><i class="fa-solid fa-plane-up"></i></span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">International</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white"><?= $fmt($summary['export']['total'] ?? 0) ?></p>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-5 flex items-center gap-4">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400"><i class="fa-solid fa-coins"></i></span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Sales</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white"><?= $fmt($summary['total']['total'] ?? 0) ?></p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Rekap Revenue Domestic (Rp)
                </h3>
            </div>
            <?php $renderRevTable([['label' => 'Domestic Sales', 'data' => $summary['domestic']]]); ?>
        </div>

        <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    Rekap Revenue Export (Rp)
                </h3>
            </div>
            <?php $renderRevTable([['label' => 'Export Sales', 'data' => $summary['export']]]); ?>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20 flex items-center justify-between gap-3">
                    <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        Rekap per Country (Rp)
                    </h3>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400"><?= $fmt(array_sum(array_column($country, 'total'))) ?></span>
                </div>
                <?php empty($country) ? $renderEmpty('Belum ada data summary per country') : $renderRevTable(array_map(fn ($r) => ['label' => ($r['label'] ?? '-'), 'data' => $r], $country), false); ?>
            </div>
            <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20 flex items-center justify-between gap-3">
                    <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        Rekap per Region (Rp)
                    </h3>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400"><?= $fmt(array_sum(array_column($region, 'total'))) ?></span>
                </div>
                <?php empty($region) ? $renderEmpty('Belum ada data summary per region') : $renderRevTable(array_map(fn ($r) => ['label' => ($r['label'] ?? '-'), 'data' => $r], $region), false); ?>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB: DOMESTIC (per channel)                                  -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'domestic'" x-cloak class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden transition-all">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 gap-3 bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Transaksi Domestic per Channel
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">QTY, Revenue, dan ASP/kg per channel dari <code class="font-mono text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-[11px]">yp_plan__trans_sales_domestic</code></p>
            </div>
        </div>
        <?php $renderChannelTable($domestic); ?>
    </div>

    <!-- ============================================================ -->
    <!-- TAB: INTERNATIONAL (per channel)                             -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'international'" x-cloak class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden transition-all">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 gap-3 bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    Transaksi International per Channel
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">QTY, Revenue, dan ASP/kg per channel dari <code class="font-mono text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-[11px]">yp_plan__trans_sales_export</code></p>
            </div>
        </div>
        <?php $renderChannelTable($export); ?>
    </div>

    <!-- ============================================================ -->
    <!-- TAB: DELIVERY & CUSTOMER CLAIM                               -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'delivery'" x-cloak class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden transition-all">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 gap-3 bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Delivery Exp & Customer Claim
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Agregasi tahunan per tipe (DEL/SAL) dari <code class="font-mono text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-[11px]">yp_plan__trans_delivery_customer</code></p>
            </div>
        </div>
        <?php if (empty($delivery)): ?>
            <?php $renderEmpty('Belum ada data delivery & customer claim'); ?>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-gray-100/80 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 text-[10px] font-bold uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-5 py-3.5 text-left">Tipe</th>
                        <th class="px-5 py-3.5 text-left">Kategori</th>
                        <th class="px-5 py-3.5 text-right">Tahun 1</th>
                        <th class="px-5 py-3.5 text-right">Tahun 2</th>
                        <th class="px-5 py-3.5 text-right">Tahun 3</th>
                        <th class="px-5 py-3.5 text-right">Tahun 4</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/80 dark:divide-gray-800/80">
                    <?php foreach ($delivery as $d): ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-5 py-3.5 font-bold text-gray-900 dark:text-white">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold <?= $d['tipe'] === 'DEL' ? 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' ?>"><?= esc($d['tipe']) ?></span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300 font-semibold"><?= esc($d['desc_value'] ?: '-') ?></td>
                        <td class="px-5 py-3.5 text-right font-mono tabular-nums"><?= $fmt($d['tahun_1']) ?></td>
                        <td class="px-5 py-3.5 text-right font-mono tabular-nums"><?= $fmt($d['tahun_2']) ?></td>
                        <td class="px-5 py-3.5 text-right font-mono tabular-nums"><?= $fmt($d['tahun_3']) ?></td>
                        <td class="px-5 py-3.5 text-right font-mono tabular-nums font-bold text-primary"><?= $fmt($d['tahun_4']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- ============================================================ -->
    <!-- TAB: DISCOUNT RECLASS                                        -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'reclass'" x-cloak class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 sm:p-8 shadow-xs space-y-6 transition-all">
        <div class="border-b border-gray-200/80 dark:border-gray-800 pb-5">
            <h3 class="text-base font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-receipt text-primary"></i>
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
                <i x-show="saving" class="fa-solid fa-circle-notch animate-spin"></i>
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
            { key: 'summary',       label: 'Summary' },
            { key: 'domestic',      label: 'Domestic' },
            { key: 'international', label: 'International' },
            { key: 'delivery',      label: 'Delivery & Claim' },
            { key: 'reclass',       label: 'Discount Reclass' },
        ],
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
