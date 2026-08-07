<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
$labels = ['JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE','JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'];

$fmt  = fn ($v) => number_format((float) ($v ?? 0), 0, ',', '.');
$fmt2 = fn ($v) => number_format((float) ($v ?? 0), 2, ',', '.');
$asp  = fn ($rev, $qty) => ((float) $qty) > 0 ? (float) $rev / (float) $qty : 0;

// ------------------------------------------------------------------
// Tabel per-produk: QTY / REVENUE / ASP per bulan + total
// (tata letak mengikuti sistem lama: header 2 baris, kolom bulan
//  masing-masing span 3 sub-kolom QTY/REVENUE/ASP)
// ------------------------------------------------------------------
$renderProductTable = function (array $rows) use ($months, $labels, $fmt, $fmt2, $asp): void {
    ?>
    <div class="overflow-x-auto" x-ref="productTable">
        <table class="w-full text-left text-xs border-collapse min-w-[2200px]">
            <thead>
                <tr class="bg-gray-100/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 text-[10px] font-bold uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                    <th rowspan="2" class="px-4 py-3 sticky left-0 z-20 bg-gray-100 dark:bg-gray-800 min-w-[60px] border-r border-gray-200/60 dark:border-gray-700/60">No</th>
                    <th rowspan="2" class="px-4 py-3 sticky left-16 z-20 bg-gray-100 dark:bg-gray-800 min-w-[130px] border-r border-gray-200/60 dark:border-gray-700/60">Key Product</th>
                    <th rowspan="2" class="px-4 py-3 sticky left-[164px] z-20 bg-gray-100 dark:bg-gray-800 min-w-[120px] border-r border-gray-200/60 dark:border-gray-700/60">Code Inv</th>
                    <th rowspan="2" class="px-4 py-3 sticky left-[284px] z-20 bg-gray-100 dark:bg-gray-800 min-w-[190px] border-r border-gray-200/60 dark:border-gray-700/60">Name Product</th>
                    <?php foreach ($labels as $i => $lb): ?>
                        <th colspan="3" class="px-2 py-2 text-center whitespace-nowrap border-l first:border-l-0 <?= $i % 2 === 0 ? 'bg-sky-50/80 dark:bg-sky-900/30' : 'bg-blue-100/80 dark:bg-blue-900/30' ?>"><?= $lb ?></th>
                    <?php endforeach; ?>
                    <th colspan="3" class="px-2 py-2 text-center whitespace-nowrap bg-gray-300/80 dark:bg-gray-700/70">TOTAL</th>
                </tr>
                <tr class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 text-[10px] font-bold uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                    <?php for ($i = 0; $i < 13; $i++): ?>
                        <th class="px-2 py-2 text-right whitespace-nowrap <?= $i % 2 === 0 ? 'bg-sky-50/80 dark:bg-sky-900/30' : 'bg-gray-100/80 dark:bg-blue-900/30' ?>">Qty</th>
                        <th class="px-2 py-2 text-right whitespace-nowrap <?= $i % 2 === 0 ? 'bg-sky-50/80 dark:bg-sky-900/30' : 'bg-gray-100/80 dark:bg-blue-900/30' ?>">Revenue</th>
                        <th class="px-2 py-2 text-right whitespace-nowrap <?= $i % 2 === 0 ? 'bg-sky-50/80 dark:bg-sky-900/30' : 'bg-gray-100/80 dark:bg-blue-900/30' ?>">ASP/kg</th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200/80 dark:divide-gray-800/80">
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="43" class="px-6 py-20 text-center text-gray-400 dark:text-gray-500">
                            <i class="fa-solid fa-box-open text-2xl mb-3"></i>
                            <p class="font-semibold text-gray-600 dark:text-gray-300">Belum ada data transaksi</p>
                            <p class="text-xs mt-1">Silakan unggah data sales terlebih dahulu.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($rows as $row): ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400 sticky left-0 z-10 bg-white dark:bg-gray-900 border-r border-gray-200/40 dark:border-gray-800"><?= $no++ ?></td>
                        <td class="px-4 py-2.5 font-bold text-gray-900 dark:text-white sticky left-16 z-10 bg-white dark:bg-gray-900 border-r border-gray-200/40 dark:border-gray-800"><?= esc($row['key_product'] ?: '-') ?></td>
                        <td class="px-4 py-2.5 font-mono font-semibold text-gray-900 dark:text-gray-100 sticky left-[164px] z-10 bg-white dark:bg-gray-900 border-r border-gray-200/40 dark:border-gray-800"><?= esc($row['mid_product']) ?></td>
                        <td class="px-4 py-2.5 text-gray-700 dark:text-gray-300 sticky left-[284px] z-10 bg-white dark:bg-gray-900 border-r border-gray-200/40 dark:border-gray-800 whitespace-nowrap"><?= esc($row['product_name'] ?: '-') ?></td>
                        <?php foreach ($months as $i => $m): ?>
                            <td class="px-2 py-2.5 text-right font-mono tabular-nums <?= $i % 2 === 0 ? 'bg-sky-50/30 dark:bg-sky-900/10' : '' ?>"><?= $fmt2($row["{$m}_qty"]) ?></td>
                            <td class="px-2 py-2.5 text-right font-mono tabular-nums <?= $i % 2 === 0 ? 'bg-sky-50/30 dark:bg-sky-900/10' : '' ?>"><?= $fmt($row["{$m}_rev"]) ?></td>
                            <td class="px-2 py-2.5 text-right font-mono tabular-nums text-gray-500 dark:text-gray-400 <?= $i % 2 === 0 ? 'bg-sky-50/30 dark:bg-sky-900/10' : '' ?>"><?= $fmt2($asp($row["{$m}_rev"], $row["{$m}_qty"])) ?></td>
                        <?php endforeach; ?>
                        <td class="px-2 py-2.5 text-right font-mono font-bold text-gray-900 dark:text-white"><?= $fmt2($row['total_qty']) ?></td>
                        <td class="px-2 py-2.5 text-right font-mono font-bold text-brand-500 dark:text-brand-400"><?= $fmt($row['total_rev']) ?></td>
                        <td class="px-2 py-2.5 text-right font-mono font-semibold text-gray-600 dark:text-gray-300"><?= $fmt2($asp($row['total_rev'], $row['total_qty'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
};

// ------------------------------------------------------------------
// Form parameter (jumlah field pasokan retention)
// ------------------------------------------------------------------
$retentionField = function (string $id, string $label, string $xmodel, string $suffix = '%') : void {
    ?>
    <div class="flex flex-col gap-1">
        <label class="text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide whitespace-nowrap"><?= $label ?></label>
        <div class="flex items-center gap-1.5">
            <input type="number" step="any" min="0" x-model.number="<?= $xmodel ?>"
                   placeholder="0"
                   class="w-20 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1.5 text-center text-xs font-mono font-semibold text-gray-900 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-colors">
            <span class="text-sm font-bold text-gray-400"><?= $suffix ?></span>
        </div>
    </div>
    <?php
};
?>

<div x-data="salesSummary()" x-init="init()" class="space-y-4">
    <!-- ============================== Header ============================== -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-brand-500"></i> Sales Summary
                <span class="text-xs font-medium text-gray-400">Tahun <?= esc($workingYear) ?></span>
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ringkasan revenue, volume & ASP per produk</p>
        </div>
    </div>

    <!-- ============================== Tabs ============================== -->
    <div class="flex flex-wrap items-center gap-1 border-b border-gray-200 dark:border-gray-800 pb-3">
        <?php $tabs = ['summary' => 'Summary', 'domestic' => 'Domestic', 'intl_valas' => 'INTL (VALAS)', 'intl_idr' => 'INTL (IDR)']; ?>
        <?php foreach ($tabs as $key => $label): ?>
            <button type="button"
                    @click="actTab('<?= $key ?>')"
                    :class="tab === '<?= $key ?>'
                        ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30'
                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'"
                    class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wide transition-all">
                <i class="fa-solid <?= ['summary'=>'fa-chart-line','domestic'=>'fa-warehouse','intl_valas'=>'fa-plane','intl_idr'=>'fa-coins'][$key] ?> mr-1.5"></i>
                <?= $label ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="space-y-4">
        <!-- ====================== TAB: SUMMARY ====================== -->
        <div x-show="tab === 'summary'" x-transition.opacity>
            <!-- Revenue Overview -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-xl border border-gray-200/70 dark:border-gray-800 p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">
                            <i class="fa-solid fa-dollar-sign text-sky-500 mr-1.5"></i>Revenue Trend (IDR)
                        </h3>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">IDR</span>
                    </div>
                    <div id="revenueChart" class="h-72"></div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200/70 dark:border-gray-800 p-4 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-3">
                        <i class="fa-solid fa-truck-fast text-emerald-500 mr-1.5"></i>Ringkasan Tahunan
                    </h3>
                    <div class="space-y-4 bg-white/60 dark:bg-gray-800/40 rounded-lg p-3">
                        <?php
                        $sumRev = (float) ($summary['total']['total'] ?? 0);
                        $domRev = (float) ($summary['domestic']['total'] ?? 0);
                        $expRev = (float) ($summary['export']['total'] ?? 0);
                        ?>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-brand-500/10 flex items-center justify-center">
                                <i class="fa-solid fa-sack-dollar text-brand-500"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Total Revenue</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white font-mono tabular-nums">Rp <?= number_format($sumRev, 0, ',', '.') ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-sky-500/10 flex items-center justify-center">
                                <i class="fa-solid fa-warehouse text-sky-500"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Domestic</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white font-mono tabular-nums">Rp <?= number_format($domRev, 0, ',', '.') ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                                <i class="fa-solid fa-plane text-emerald-500"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">International</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white font-mono tabular-nums">Rp <?= number_format($expRev, 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================= TAB: DOMESTIC ======================= -->
        <div x-show="tab === 'domestic'" x-transition.opacity>
            <form @submit.prevent="processDomestic()" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200/70 dark:border-gray-800 p-4 shadow-sm mb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">
                        <i class="fa-solid fa-warehouse text-amber-500 mr-1.5"></i>Domestic Summary
                    </h3>
                    <button type="submit"
                            :disabled="processing"
                            class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 disabled:opacity-50 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-md shadow-brand-500/20 transition-colors">
                        <i class="fa-solid fa-play" :class="processing ? 'fa-spin' : ''"></i>
                        <span x-text="processing ? 'Processing...' : 'Process'"></span>
                    </button>
                </div>

                <!-- Parameter: Key Product -->
                <div class="mb-4">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Key Product</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php $kps = ['gc' => 'GC & Confect', 'bakery' => 'Bakery', 'beverage' => 'Beverage', 'ic' => 'Ice Cream', 'dairy' => 'Dairy', 'iodized' => 'Iodized', 'snack' => 'Snack', 'market' => 'Market'] ?>
                        <?php foreach ($kps as $k => $kp): ?>
                        <?php $retentionField('dom_kp_' . $k, $kp, 'dom.kp.' . $k) ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Parameter: Channel -->
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Channel</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
                        <?php $channels = ['retail' => 'Retail', 'gt' => 'GT', 'mt' => 'MT', 'oem' => 'OEM', 'catering' => 'Catering', 'online' => 'Online'] ?>
                        <?php foreach ($channels as $k => $ch): ?>
                        <?php $retentionField('dom_ch_' . $k, $ch, 'dom.ch_' . $k, 'Qty') ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </form>

            <?php $renderProductTable($domesticProducts); ?>
        </div>

        <!-- ====================== TAB: INTL VALAS ====================== -->
        <div x-show="tab === 'intl_valas'" x-transition.opacity>
            <form @submit.prevent="processValas()" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200/70 dark:border-gray-800 p-5 shadow-sm mb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">
                        <i class="fa-solid fa-globe text-indigo-500 mr-1.5"></i>International Summary (VALAS)
                    </h3>
                    <button type="submit"
                            :disabled="processing"
                            class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 disabled:opacity-50 text-white font-bold px-5 py-2 rounded-lg transition-colors">
                        <i class="fa-solid fa-play" :class="processing ? 'fa-spin' : ''"></i>
                        <span x-text="processing ? 'Processing...' : 'Process'">Process</span>
                    </button>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Key Product</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-4">
                        <?php foreach ($kps as $k => $kp): ?>
                        <?php $retentionField('valas_kp_' . $k, $kp, 'valas.kp_' . $k) ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </form>

            <div class="flex items-center gap-2 mb-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                <i class="fa-solid fa-tag text-indigo-400"></i> Mata uang: USD
            </div>

            <?php $renderProductTable($exportProducts); ?>
        </div>

        <!-- ======================= TAB: INTL IDR ======================= -->
        <div x-show="tab === 'intl_idr'" x-transition.opacity>
            <form @submit.prevent="processIdr()" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200/70 dark:border-gray-800 p-5 shadow-sm mb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">
                        <i class="fa-solid fa-coins text-emerald-500 mr-1.5"></i>International Summary (IDR)
                    </h3>
                    <button type="submit"
                            :disabled="processing"
                            class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 disabled:opacity-50 text-white font-bold px-5 py-2 rounded-lg transition-colors">
                        <i class="fa-solid fa-play" :class="processing ? 'fa-spin' : ''"></i>
                        <span x-text="processing ? 'Processing...' : 'Process'">Process</span>
                    </button>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Exchange Rate</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-lg">
                        <?php $retentionField('rate_usd', 'US $ / Rp', 'idr.usd', 'Rp') ?>
                        <?php $retentionField('rate_baht', 'Baht / Rp', 'idr.baht', 'Rp') ?>
                        <?php $retentionField('rate_ringgit', 'Ringgit / Rp', 'idr.ringgit', 'Rp') ?>
                    </div>
                </div>
            </form>

            <?php $renderProductTable($exportProducts); ?>
        </div>
    </div>

    <div class="pt-6">
        <button @click="exportExcel"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-lg transition-colors">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </button>
    </div>
</div>

<script>
    function salesSummary() {
        return {
            tab: 'summary',
            processing: false,
            _chart: null,

            dom: {
                kp: { gc: 0, bakery: 0, beverage: 0, ic: 0, dairy: 0, iodized: 0, snack: 0, market: 0 },
                ch_retail: 0, ch_gt: 0, ch_mt: 0, ch_oem: 0, ch_catering: 0, ch_online: 0,
            },
            valas: { kp_gc: 0, kp_bakery: 0, kp_beverage: 0, kp_ic: 0, kp_dairy: 0, kp_iodized: 0, kp_snack: 0, kp_market: 0 },
            idr: {
                usd: <?= json_encode((float) ($kurs['usd'] ?? 0)) ?>,
                baht: <?= json_encode((float) ($kurs['baht'] ?? 0)) ?>,
                ringgit: <?= json_encode((float) ($kurs['ringgit'] ?? 0)) ?>,
            },

            showTab(t) { this.tab = t; },
            actTab(t) { this.tab = t; },

            async init() {
                this.buildChart(<?= htmlspecialchars(json_encode($summary), ENT_QUOTES) ?>);
            },

            buildChart(data) {
                const labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                const s = data && data.total ? data.total : {};
                const val = k => Math.round(parseFloat(s[k] || 0));

                const series = [
                    { name: 'Grand Total', data: labels.map(l => val(l.toLowerCase())) },
                ];

                const chartT = document.getElementById('revenueChart');
                if (!chartT || typeof window.ApexCharts === 'undefined') return;
                if (this._chart) this._chart.destroy();
                this._chart = new ApexCharts(chartT, {
                    chart: { type: 'line', height: 290, toolbar: { show: false }, fontFamily: 'inherit' },
                    series,
                    xaxis: { categories: labels, labels: { style: { fontSize: '11px' } } },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    colors: ['#FF6B35'],
                    legend: { show: false },
                    tooltip: { y: { formatter: v => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v) } },
                    fill: { opacity: 1 },
                });
                this._chart.render();
            },

            async processDomestic() {
                this.processing = true;
                // Placeholder — wire to controller endpoint
                await new Promise(r => setTimeout(r, 400));
                this.processing = false;
                window.showToast('success', 'Domestic Summary berhasil diproses');
            },
            async processValas() {
                this.processing = true;
                await new Promise(r => setTimeout(r, 400));
                this.processing = false;
                window.showToast('success', 'INTL (VALAS) berhasil diproses');
            },
            async processIdr() {
                this.processing = true;
                await new Promise(r => setTimeout(r, 400));
                this.processing = false;
                window.showToast('success', 'INTL (IDR) berhasil diproses');
            },

            exportExcel() {
                window.showToast('info', 'Export Excel belum tersedia');
            },
        };
    }
</script>

<?= $this->endSection() ?>