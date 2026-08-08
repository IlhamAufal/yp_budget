<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
  $workingYear = $workingYear ?? date('Y');
  $dbProductsJson = json_encode($domesticProducts ?? []);
  $dbRegionalJson = json_encode($regionalSummary ?? []);
?>

<!-- Highcharts CDN for trend visualization -->
<script src="https://cdn.jsdelivr.net/npm/highcharts@10.3.3/highcharts.js"></script>

<div x-data="domesticSalesEntry(<?= htmlspecialchars($dbProductsJson, ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($dbRegionalJson, ENT_QUOTES, 'UTF-8') ?>)" 
     x-init="initData()" 
     class="p-4 md:p-6 lg:p-8 space-y-6 pb-16">

    <!-- ============================================================ -->
    <!-- 1. HEADER SECTION -->
    <!-- ============================================================ -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-primary transition-colors flex items-center gap-1">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <a href="<?= base_url('sales') ?>" class="hover:text-primary transition-colors">Sales</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-primary font-bold">Sales Domestic Entry</span>
            </div>

            <!-- Page Title -->
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 shadow-xs">
                    <i class="fa-solid fa-boxes-packing text-lg"></i>
                </span>
                <div>
                    <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                        2.1 Sales Domestic Entry
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800">
                            FY <?= esc($workingYear) ?>
                        </span>
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Pengelolaan target dan laporan Sales Domestic 12 Bulan (Budget, Key Product, Regional, Download & Upload).
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Summary Stats Header Badges -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-gray-500 dark:text-gray-400 font-medium">Tot QTY:</span>
                <span class="font-mono font-bold text-gray-900 dark:text-white" x-text="formatNumber(kpi.totalQty)">0</span>
                <span class="text-[10px] text-gray-400 font-mono">Kg</span>
            </div>
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                <span class="text-gray-500 dark:text-gray-400 font-medium">Tot REV:</span>
                <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="formatCurrency(kpi.totalRev)">Rp 0</span>
            </div>
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <span class="text-gray-500 dark:text-gray-400 font-medium">Avg ASP:</span>
                <span class="font-mono font-bold text-amber-600 dark:text-amber-400" x-text="formatNumber(kpi.avgAsp)">0</span>
                <span class="text-[10px] text-gray-400 font-mono">Rp/kg</span>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- 2. SUB-TABS NAVIGATION BAR -->
    <!-- ============================================================ -->
    <div class="bg-gray-100/80 dark:bg-gray-800/60 p-1.5 rounded-2xl border border-gray-200/80 dark:border-gray-700/60 shadow-xs">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="Domestic Sub Tabs">
            <button type="button" @click="setSubTab('budget')" 
                    :class="subTab === 'budget' ? 'bg-white dark:bg-gray-900 text-primary dark:text-white shadow-xs font-bold border border-gray-200/80 dark:border-gray-700' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/50'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-table-cells text-sm"></i>
                <span>Sales Domestic - Budget</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-primary/10 text-primary font-bold" x-text="filteredItems.length">0</span>
            </button>

            <button type="button" @click="setSubTab('key_product')" 
                    :class="subTab === 'key_product' ? 'bg-white dark:bg-gray-900 text-primary dark:text-white shadow-xs font-bold border border-gray-200/80 dark:border-gray-700' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/50'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-award text-sm"></i>
                <span>Report Key Product</span>
            </button>

            <button type="button" @click="setSubTab('regional')" 
                    :class="subTab === 'regional' ? 'bg-white dark:bg-gray-900 text-primary dark:text-white shadow-xs font-bold border border-gray-200/80 dark:border-gray-700' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/50'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-earth-asia text-sm"></i>
                <span>Report Regional</span>
            </button>

            <button type="button" @click="setSubTab('download')" 
                    :class="subTab === 'download' ? 'bg-white dark:bg-gray-900 text-primary dark:text-white shadow-xs font-bold border border-gray-200/80 dark:border-gray-700' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/50'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-sm text-emerald-600 dark:text-emerald-400"></i>
                <span>Download Template</span>
            </button>

            <button type="button" @click="setSubTab('upload')" 
                    :class="subTab === 'upload' ? 'bg-white dark:bg-gray-900 text-primary dark:text-white shadow-xs font-bold border border-gray-200/80 dark:border-gray-700' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/50'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up text-sm text-sky-600 dark:text-sky-400"></i>
                <span>Upload Data</span>
            </button>
        </nav>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 1: SALES DOMESTIC - BUDGET -->
    <!-- ============================================================ -->
    <div x-show="subTab === 'budget'" x-cloak class="space-y-6">

        <!-- Toolbar & Filter Area -->
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <!-- Channel Filter Dropdown -->
                <div class="flex items-center gap-2 min-w-[200px]">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5 shrink-0">
                        <i class="fa-solid fa-filter text-primary"></i> Channel:
                    </label>
                    <select x-model="filters.channel" @change="applyFilters()" 
                            class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="">- All Channel -</option>
                        <option value="GT">GT - General Trade</option>
                        <option value="MT">MT - Modern Trade</option>
                        <option value="OEM">OEM - Original Equipment Mfg</option>
                        <option value="ECOM">ECOM - E-Commerce</option>
                        <option value="YTI">YTI - Yupi Trading International</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="relative flex-1 min-w-[220px]">
                    <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()" 
                           placeholder="Cari SKU Code / Nama Produk / Key Product..." 
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-gray-400"></i>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" @click="showChart = !showChart" 
                        :class="showChart ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 border-indigo-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border-gray-200'"
                        class="px-3.5 py-2 text-xs font-semibold rounded-xl border shadow-xs transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-chart-line"></i>
                    <span x-text="showChart ? 'Sembunyikan Grafik' : 'Tampilkan Grafik'">Tampilkan Grafik</span>
                </button>

                <button type="button" @click="downloadChannelTemplate()" 
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-1.5 active:scale-[0.98]">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Template Data Channel</span>
                </button>

                <button type="button" @click="saveChanges()" 
                        class="px-3.5 py-2 bg-primary hover:bg-primary/90 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-1.5 active:scale-[0.98]">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-900 p-4.5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold tracking-wider text-gray-500 dark:text-gray-400">Total Volume (QTY)</p>
                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white font-mono mt-1" x-text="formatNumber(kpi.totalQty)">0</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">Total Kilogram 12 Bulan</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <i class="fa-solid fa-weight-hanging text-lg"></i>
                </span>
            </div>

            <div class="bg-white dark:bg-gray-900 p-4.5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold  tracking-wider text-gray-500 dark:text-gray-400">Total Revenue</p>
                    <h3 class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400 font-mono mt-1" x-text="formatCurrency(kpi.totalRev)">Rp 0</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">Total Sales (IDR)</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fa-solid fa-sack-dollar text-lg"></i>
                </span>
            </div>

            <div class="bg-white dark:bg-gray-900 p-4.5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold  tracking-wider text-gray-500 dark:text-gray-400">Average ASP</p>
                    <h3 class="text-xl font-extrabold text-amber-600 dark:text-amber-400 font-mono mt-1" x-text="formatNumber(kpi.avgAsp)">0</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">Rata-rata Rp / Kg</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fa-solid fa-calculator text-lg"></i>
                </span>
            </div>

            <div class="bg-white dark:bg-gray-900 p-4.5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold tracking-wider text-gray-500 dark:text-gray-400">Active SKUs</p>
                    <h3 class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400 font-mono mt-1" x-text="filteredItems.length">0</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">Produk Terdaftar</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </span>
            </div>
        </div>

        <!-- Highcharts Chart Area -->
        <div x-show="showChart" x-transition class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-3">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-teal-500"></span>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white tracking-wider">SALES DOMESTIC REVENUE TREND (12 MONTHS)</h3>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="chartMetric = 'revenue'; renderChart()" 
                            :class="chartMetric === 'revenue' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'"
                            class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition-colors">Revenue (Rp)</button>
                    <button type="button" @click="chartMetric = 'qty'; renderChart()" 
                            :class="chartMetric === 'qty' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'"
                            class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition-colors">Volume (Kg)</button>
                </div>
            </div>
            <div id="domesticTrendChart" class="w-full h-72"></div>
        </div>

        <!-- Pivot Data Table (Spreadsheet Style) -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/40">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-table text-primary"></i>
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white tracking-wider">Target Sales Domestic 12 Bulan (Pivot Grid)</h3>
                </div>
                <span class="text-[11px] text-gray-500 dark:text-gray-400 italic">Input nilai QTY & Revenue untuk update ASP otomatis</span>
            </div>

            <div class="overflow-x-auto scrollbar-thin max-h-[600px]">
                <table class="w-full text-left text-[11px] border-collapse min-w-[2800px]">
                    <thead class="bg-gray-100/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-300 font-bold tracking-wider border-b border-gray-300 dark:border-gray-700 sticky top-0 z-20">
                        <tr>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 w-10 text-center sticky left-0 z-30 bg-gray-100 dark:bg-gray-800" rowspan="2">No.</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[90px] text-center sticky left-10 z-30 bg-gray-100 dark:bg-gray-800" rowspan="2">CHANNEL</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[130px]" rowspan="2">KEY PRODUCT</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[100px]" rowspan="2">CODE INV</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[220px]" rowspan="2">NAME PRODUCT</th>
                            
                            <template x-for="(month, idx) in monthNames" :key="month">
                                <th class="p-2 border-r border-gray-300 dark:border-gray-700 text-center" 
                                    :class="idx % 2 === 0 ? 'bg-sky-50/70 dark:bg-gray-800/80' : 'bg-gray-100/70 dark:bg-gray-800/40'" 
                                    colspan="3" x-text="month"></th>
                            </template>
                            
                            <th class="p-2 border-l-2 border-primary/40 bg-primary/10 dark:bg-primary/20 text-center font-extrabold text-primary dark:text-white" colspan="3">ANNUAL TOTAL</th>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-[10px]">
                            <?php for ($i = 0; $i < 12; $i++): ?>
                                <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-18 text-center bg-gray-50/80 dark:bg-gray-800">QTY (Kg)</th>
                                <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-24 text-center bg-gray-50/80 dark:bg-gray-800">REVENUE (Rp)</th>
                                <th class="p-1 border-r border-gray-300 dark:border-gray-700 w-20 text-center bg-gray-100/50 dark:bg-gray-800/60">ASP/kg</th>
                            <?php endfor; ?>
                            <th class="p-1 border-r border-gray-300 dark:border-gray-700 w-24 text-center bg-primary/10 text-primary font-bold">TOT QTY</th>
                            <th class="p-1 border-r border-gray-300 dark:border-gray-700 w-28 text-center bg-primary/10 text-primary font-bold">TOT REV</th>
                            <th class="p-1 border-r border-gray-300 dark:border-gray-700 w-24 text-center bg-primary/10 text-primary font-bold">AVG ASP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono">
                        <template x-if="filteredItems.length === 0">
                            <tr>
                                <td colspan="44" class="p-12 text-center text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-inbox text-2xl mb-3"></i>
                                    <p class="font-semibold text-gray-600 dark:text-gray-300">Belum ada data budget domestic</p>
                                    <p class="text-xs mt-1">Upload data via tab <strong>Upload Data</strong> lalu <strong>Process Summary SKU</strong>.</p>
                                </td>
                            </tr>
                        </template>
                        <!-- Iteration Grouped by Channel (flat rows: subtotal + item) -->
                        <template x-for="(row, i) in groupedFlatRows" :key="'f-'+i">
                            <tr :class="row.kind === 'subtotal' ? 'bg-amber-50/80 dark:bg-amber-950/30 border-y border-amber-200 dark:border-amber-900/50 font-bold text-amber-900 dark:text-amber-200' : 'hover:bg-sky-50/50 dark:hover:bg-gray-800/60 transition-colors'">
                                <!-- Left info cells -->
                                <template x-if="row.kind === 'subtotal'">
                                    <td class="p-2 text-center sticky left-0 z-10 bg-amber-100 dark:bg-amber-900/40" colspan="2" x-text="row.group.channel"></td>
                                    <td class="p-2" colspan="3">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-layer-group text-xs text-amber-600"></i>
                                            <span x-text="'SUBTOTAL CHANNEL ' + row.group.channel"></span>
                                            <span class="text-[10px] text-amber-700 dark:text-amber-400 font-sans font-normal" x-text="'(' + row.group.items.length + ' items)'"></span>
                                        </span>
                                    </td>
                                </template>
                                <template x-if="row.kind === 'item'">
                                    <td class="p-2 text-center border-r border-gray-200 dark:border-gray-800 sticky left-0 z-10 bg-white dark:bg-gray-900 text-gray-500" x-text="row.idx + 1"></td>
                                    <td class="p-2 text-center border-r border-gray-200 dark:border-gray-800 font-sans font-bold text-xs sticky left-10 z-10 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300" x-text="row.item.id_channel"></td>
                                    <td class="p-2 border-r border-gray-200 dark:border-gray-800 font-sans text-xs text-gray-600 dark:text-gray-400" x-text="row.item.key_product || '-'"></td>
                                    <td class="p-2 border-r border-gray-200 dark:border-gray-800 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="row.item.code_inv_1 || row.item.mid_product"></td>
                                    <td class="p-2 border-r border-gray-200 dark:border-gray-800 font-sans font-medium text-xs text-gray-900 dark:text-white" x-text="row.item.name_product || row.item.product_name"></td>
                                </template>

                                <!-- Month cells QTY/REV/ASP -->
                                <template x-for="col in metricCols" :key="'fm-'+i+'-'+col.m+'-'+col.k">
                                    <td :class="row.kind === 'subtotal' ? 'p-1 text-right border-r border-amber-200/60 dark:border-amber-900/40' + (col.k === 'asp' ? ' bg-amber-100/40 dark:bg-amber-950/40' : '') : 'p-1 border-r border-gray-200 dark:border-gray-800' + (col.k === 'asp' ? ' bg-slate-50/70 dark:bg-gray-800/40 text-right font-medium text-gray-700 dark:text-gray-300' : '')">
                                        <template x-if="row.kind === 'subtotal'">
                                            <span class="block text-right" x-text="col.k === 'qty' ? formatNumber(row.group.subtotal.monthly[col.m].qty) : col.k === 'rev' ? formatNumber(row.group.subtotal.monthly[col.m].revenue) : formatNumber(calculateASP(row.group.subtotal.monthly[col.m].revenue, row.group.subtotal.monthly[col.m].qty, row.group.channel))"></span>
                                        </template>
                                        <template x-if="row.kind === 'item' && col.k === 'qty'">
                                            <input type="number" x-model.number="row.item.monthly[col.m].qty" @input="recalculateRow(row.item)"
                                                   class="w-full text-right p-1 text-[11px] border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-primary focus:bg-white dark:focus:bg-gray-900">
                                        </template>
                                        <template x-if="row.kind === 'item' && col.k === 'rev'">
                                            <input type="number" x-model.number="row.item.monthly[col.m].revenue" @input="recalculateRow(row.item)"
                                                   class="w-full text-right p-1 text-[11px] border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold focus:ring-1 focus:ring-emerald-500 focus:bg-white dark:focus:bg-gray-900">
                                        </template>
                                        <template x-if="row.kind === 'item' && col.k === 'asp'">
                                            <span class="block text-right" x-text="formatNumber(calculateASP(row.item.monthly[col.m].revenue, row.item.monthly[col.m].qty, row.item.id_channel))"></span>
                                        </template>
                                    </td>
                                </template>

                                <!-- Total cells -->
                                <template x-if="row.kind === 'subtotal'">
                                    <td class="p-2 text-right border-r border-amber-300 font-extrabold bg-amber-100 dark:bg-amber-900/50" x-text="formatNumber(row.group.subtotal.total_qty)"></td>
                                    <td class="p-2 text-right border-r border-amber-300 font-extrabold text-emerald-700 dark:text-emerald-400 bg-amber-100 dark:bg-amber-900/50" x-text="formatNumber(row.group.subtotal.total_revenue)"></td>
                                    <td class="p-2 text-right font-extrabold bg-amber-100 dark:bg-amber-900/50" x-text="formatNumber(calculateASP(row.group.subtotal.total_revenue, row.group.subtotal.total_qty, row.group.channel))"></td>
                                </template>
                                <template x-if="row.kind === 'item'">
                                    <td class="p-2 border-l-2 border-primary/30 border-r border-gray-200 dark:border-gray-800 bg-primary/5 dark:bg-gray-800 text-right font-bold text-gray-900 dark:text-white" x-text="formatNumber(row.item.total_qty)"></td>
                                    <td class="p-2 border-r border-gray-200 dark:border-gray-800 bg-primary/5 dark:bg-gray-800 text-right font-bold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(row.item.total_revenue)"></td>
                                    <td class="p-2 bg-primary/5 dark:bg-gray-800 text-right font-bold text-amber-600 dark:text-amber-400" x-text="formatNumber(calculateASP(row.item.total_revenue, row.item.total_qty, row.item.id_channel))"></td>
                                </template>
                            </tr>
                        </template>

                        <!-- Grand Total Row -->
                        <tr class="bg-primary/10 dark:bg-primary/20 font-extrabold text-gray-900 dark:text-white border-t-2 border-primary/30 text-xs">
                            <td class="p-3 text-center sticky left-0 z-10 bg-primary/20 dark:bg-primary/30" colspan="5">GRAND TOTAL ALL CHANNELS</td>
                            <template x-for="col in metricCols" :key="'gt-'+col.m+'-'+col.k">
                                <td class="p-2 text-right border-r border-primary/20" :class="col.k === 'asp' ? 'bg-primary/15 dark:bg-primary/25' : ''" x-text="col.k === 'qty' ? formatNumber(grandTotal.monthly[col.m].qty) : col.k === 'rev' ? formatNumber(grandTotal.monthly[col.m].revenue) : formatNumber(calculateASP(grandTotal.monthly[col.m].revenue, grandTotal.monthly[col.m].qty))"></td>
                            </template>
                            <td class="p-3 text-right border-r border-primary/30 font-mono text-sm font-extrabold" x-text="formatNumber(grandTotal.total_qty)"></td>
                            <td class="p-3 text-right border-r border-primary/30 font-mono text-sm font-extrabold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(grandTotal.total_revenue)"></td>
                            <td class="p-3 text-right font-mono text-sm font-extrabold text-amber-600 dark:text-amber-400" x-text="formatNumber(calculateASP(grandTotal.total_revenue, grandTotal.total_qty))"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 2: REPORT KEY PRODUCT -->
    <!-- ============================================================ -->
    <div x-show="subTab === 'key_product'" x-cloak class="space-y-6">
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-award text-amber-500"></i>
                        Report Key Product (Domestic Hero SKUs)
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Rekapitulasi performa per Kategori Key Product (GUMMY, BOLI, MARSHMALLOW, EXTR, dll).</p>
                </div>
                <div class="flex items-center gap-2">
                    <select x-model="filters.channelKey" @change="calculateKeyProducts()" 
                            class="text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-3">
                        <option value="">- All Channel -</option>
                        <option value="GT">General Trade</option>
                        <option value="MT">Modern Trade</option>
                        <option value="OEM">OEM</option>
                        <option value="ECOM">E-Commerce</option>
                        <option value="YTI">YTI</option>
                    </select>
                </div>
            </div>

            <!-- Key Product Table -->
            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs border-collapse min-w-[2400px]">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold tracking-wider border-b border-gray-300 dark:border-gray-700 text-center">
                        <tr>
                            <th class="p-3 border-r border-gray-300 dark:border-gray-700 w-12" rowspan="2">No.</th>
                            <th class="p-3 border-r border-gray-300 dark:border-gray-700 min-w-[180px] text-left" rowspan="2">KEY PRODUCT</th>
                            <template x-for="(month, idx) in monthNames" :key="'key-'+month">
                                <th class="p-2 border-r border-gray-300 dark:border-gray-700 text-center" :class="idx % 2 === 0 ? 'bg-sky-50/70 dark:bg-gray-800/80' : 'bg-gray-100/70 dark:bg-gray-800/40'" colspan="3" x-text="month"></th>
                            </template>
                            <th class="p-2 border-l-2 border-primary/40 bg-primary/10 text-center" colspan="3">TOTAL</th>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-[10px]">
                            <?php for ($i = 0; $i < 12; $i++): ?>
                                <th class="p-1.5 border-r border-gray-200 dark:border-gray-700 w-20 text-center">QTY (Kg)</th>
                                <th class="p-1.5 border-r border-gray-200 dark:border-gray-700 w-28 text-center">REVENUE (Rp)</th>
                                <th class="p-1.5 border-r border-gray-200 dark:border-gray-700 w-20 text-center">ASP/kg</th>
                            <?php endfor; ?>
                            <th class="p-1.5 border-r border-gray-200 dark:border-gray-700 w-20 text-center bg-primary/10 font-bold">QTY (Kg)</th>
                            <th class="p-1.5 border-r border-gray-200 dark:border-gray-700 w-28 text-center bg-primary/10 font-bold">REVENUE (Rp)</th>
                            <th class="p-1.5 border-r border-gray-200 dark:border-gray-700 w-20 text-center bg-primary/10 font-bold">ASP/kg</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono">
                        <template x-for="(kp, idx) in keyProductsSummary" :key="kp.name">
                            <tr :class="kp.name === 'GRAND TOTAL' ? 'bg-primary/10 dark:bg-primary/20 font-extrabold text-gray-900 dark:text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'">
                                <td class="p-2.5 text-center border-r border-gray-200 dark:border-gray-800" x-text="kp.name === 'GRAND TOTAL' ? '' : (idx + 1)"></td>
                                <td class="p-2.5 col in metricCols" :key="'kp-'+col.m+'-'+col.k">
                                    <td class="p-2 text-right border-r border-gray-200 dark:border-gray-800" :class="col.k === 'asp' ? 'bg-gray-50/50 dark:bg-gray-800/40' : ''" x-text="col.k === 'qty' ? formatNumber(kp.monthly[col.m].qty) : col.k === 'rev' ? formatNumber(kp.monthly[col.m].revenue) : formatNumber(calculateASP(kp.monthly[col.m].revenue, kp.monthly[col.m].qty))"></tdass="p-2 text-right border-r border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40" x-text="formatNumber(calculateASP(kp.monthly[m].revenue, kp.monthly[m].qty))"></td>
                                    </template>
                                </template>
                                <td class="p-2.5 text-right border-l-2 border-primary/30 border-r border-gray-200 dark:border-gray-800 font-bold" x-text="formatNumber(kp.total_qty)"></td>
                                <td class="p-2.5 text-right border-r border-gray-200 dark:border-gray-800 font-bold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(kp.total_revenue)"></td>
                                <td class="p-2.5 text-right font-bold text-amber-600 dark:text-amber-400" x-text="formatNumber(calculateASP(kp.total_revenue, kp.total_qty))"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 3: REPORT REGIONAL -->
    <!-- ============================================================ -->
    <div x-show="subTab === 'regional'" x-cloak class="space-y-6">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <div class="flex items-center gap-3">
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                    <i class="fa-solid fa-earth-asia text-primary"></i> Regional Area:
                </label>
                <select x-model="filters.regional" class="text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-3 min-w-[180px]">
                    <option value="">- All Regional Area -</option>
                    <option value="WEST">West Region (Sumatra & West Java)</option>
                    <option value="CENTRAL">Central Region (Central Java & DIY)</option>
                    <option value="EAST">East Region (East Java, Bali & Nusa)</option>
                    <option value="OUTER">Outer Region (Kalimantan & Sulawesi)</option>
                </select>
                <button type="button" @click="fetchRegionalData()" class="px-3.5 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold rounded-xl transition-all shadow-xs flex items-center gap-1">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
            </div>

            <button type="button" @click="exportRegionalExcel()" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Report Regional Excel</span>
            </button>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800 p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                <h3 class="text-xs font-bold text-gray-900 dark:text-white tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-indigo-500"></i>
                    Detail Break Down Data Regional Sales Domestic
                </h3>
            </div>

            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs border-collapse min-w-[2600px]">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold border-b border-gray-300 dark:border-gray-700 text-center">
                        <tr>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 w-10" rowspan="2">No.</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[110px]" rowspan="2">REGION</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[130px]" rowspan="2">COUNTRY/AREA</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[100px]" rowspan="2">CODE INV</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[200px]" rowspan="2">PRODUCT NAME</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[80px]" rowspan="2">DIV</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[120px]" rowspan="2">KEY PRODUCT</th>
                            <template x-for="(month, idx) in monthNames" :key="'reg-'+month">
                                <th class="p-2 border-r border-gray-300 dark:border-gray-700 text-center" :class="idx % 2 === 0 ? 'bg-sky-50/70 dark:bg-gray-800/80' : 'bg-gray-100/70 dark:bg-gray-800/40'" colspan="3" x-text="month"></th>
                            </template>
                            <th class="p-2 border-l-2 border-primary/40 bg-primary/10 text-center" colspan="3 font-extrabold">TOTAL</th>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-[10px]">
                            <?php for ($i = 0; $i < 12; $i++): ?>
                                <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-18 text-center">QTY (Kg)</th>
                                <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-24 text-center">REVENUE (Rp)</th>
                                <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-20 text-center">ASP/kg</th>
                            <?php endfor; ?>
                            <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-18 text-center bg-primary/10 font-bold">QTY (Kg)</th>
                            <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-24 text-center bg-primary/10 font-bold">REVENUE (Rp)</th>
                            <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-20 text-center bg-primary/10 font-bold">ASP/kg</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-[11px]">
                        <template x-if="regionalData.length === 0">
                            <tr>
                                <td colspan="44" class="p-12 text-center text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-map-location-dot text-2xl mb-3"></i>
                                    <p class="font-semibold text-gray-600 dark:text-gray-300">Belum ada data regional domestic</p>
                                    <p class="text-xs mt-1">Upload data regional via tab <strong>Upload Data</strong>.</p>
                                </td>
                            </tr>
                        </template>
                        <template x-for="(reg, idx) in regionalData" :key="reg.id || idx">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="p-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="idx + 1"></td>
                                <td class="p-2 font-sans font-semibold border-r border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white" x-text="reg.region"></td>
                                <td class="p-2 font-sans border-r border-gray-200 dark:border-gray-800" x-text="reg.country"></td>
                                <td class="p-2 border-r border-gray-200 dark:border-gray-800" x-text="reg.id_inv"></td>
                                <td class="p-2 font-sans font-medium border-r border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white" x-text="reg.product_name"></td>
                                <td class="p-2 border-r border-gray-200 dark:border-gray-800 text-center" x-text="reg.div"></td>
                                <td class="p-2 font-sans border-r border-gray-200 dark:border-gray-800" x-text="reg.key_product"></td>
                                <template x-for="col in metricCols" :key="'r-'+col.m+'-'+col.k">
                                    <td class="p-1 text-right border-r border-gray-200 dark:border-gray-800" :class="col.k === 'asp' ? 'bg-gray-50/50 dark:bg-gray-800/30' : (col.k === 'rev' ? 'text-emerald-600 dark:text-emerald-400' : '')" x-text="col.k === 'qty' ? formatNumber(reg.monthly[col.m].qty) : col.k === 'rev' ? formatNumber(reg.monthly[col.m].revenue) : formatNumber(calculateASP(reg.monthly[col.m].revenue, reg.monthly[col.m].qty))"></td>
                                </template>
                                <td class="p-2 text-right border-l-2 border-primary/30 border-r border-gray-200 dark:border-gray-800 font-bold" x-text="formatNumber(reg.total_qty)"></td>
                                <td class="p-2 text-right border-r border-gray-200 dark:border-gray-800 font-bold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(reg.total_revenue)"></td>
                                <td class="p-2 text-right font-bold text-amber-600 dark:text-amber-400" x-text="formatNumber(calculateASP(reg.total_revenue, reg.total_qty))"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 4: DOWNLOAD TEMPLATE -->
    <!-- ============================================================ -->
    <div x-show="subTab === 'download'" x-cloak class="space-y-6">
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-6">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-400"></i>
                    Download Template Excel Sales Domestic
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unduh berkas template resmi untuk pengisian masal data target Sales Domestic (Volume & Revenue).</p>
            </div>

            <!-- Select Channel Card -->
            <div class="bg-slate-50 dark:bg-gray-800/60 p-4 rounded-xl border border-gray-200/80 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 shrink-0">Pilih Channel Target:</label>
                    <select x-model="selectedDownloadChannel" class="text-xs rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white py-2 px-3 min-w-[200px]">
                        <option value="GT">GT - General Trade</option>
                        <option value="MT">MT - Modern Trade</option>
                        <option value="OEM">OEM - Original Equipment Mfg</option>
                        <option value="ECOM">ECOM - E-Commerce</option>
                        <option value="YTI">YTI - Yupi Trading International</option>
                    </select>
                </div>

                <button type="button" @click="downloadChannelTemplate()" 
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-2 active:scale-[0.98]">
                    <i class="fa-solid fa-download"></i>
                    <span>Download Bundle Template (<span x-text="selectedDownloadChannel">GT</span>)</span>
                </button>
            </div>

            <!-- Download Template Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-start gap-4 hover:border-emerald-300 transition-all">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 shrink-0">
                        <i class="fa-solid fa-weight-hanging text-xl"></i>
                    </span>
                    <div class="space-y-2 flex-1">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Template Volume (VOL - Kg)</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Template Excel khusus untuk pengisian target Volume Kilogram per SKU per Bulan.</p>
                        <button type="button" @click="downloadSingleTemplate('VOL')" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                            <i class="fa-solid fa-download"></i> Unduh File Volume (.xlsx)
                        </button>
                    </div>
                </div>

                <div class="p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-start gap-4 hover:border-emerald-300 transition-all">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 shrink-0">
                        <i class="fa-solid fa-sack-dollar text-xl"></i>
                    </span>
                    <div class="space-y-2 flex-1">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Template Revenue (REV - Rp)</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Template Excel khusus untuk pengisian target Revenue Rupiah per SKU per Bulan.</p>
                        <button type="button" @click="downloadSingleTemplate('REV')" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            <i class="fa-solid fa-download"></i> Unduh File Revenue (.xlsx)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Guidelines Callout Box -->
            <div class="p-4 rounded-xl bg-sky-50 dark:bg-gray-800/80 border border-sky-100 dark:border-gray-700 space-y-2 text-xs text-sky-900 dark:text-sky-200">
                <h5 class="font-bold flex items-center gap-1.5 text-sky-800 dark:text-sky-300">
                    <i class="fa-solid fa-circle-info"></i> Petunjuk Pengisian Template Excel:
                </h5>
                <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-gray-300">
                    <li>Jangan mengubah format atau urutan kolom Kode Produk (<code class="font-mono bg-white dark:bg-gray-900 px-1 py-0.5 rounded">CODE INV</code>).</li>
                    <li>Pastikan angka yang dimasukkan berupa nilai numerik tanpa simbol mata uang atau pemisah ribuan titik.</li>
                    <li>Setelah pengisian selesai, gunakan menu <span class="font-bold">Upload Data</span> untuk mengunggah berkas.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 5: UPLOAD DATA -->
    <!-- ============================================================ -->
    <div x-show="subTab === 'upload'" x-cloak class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Upload Card -->
            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">
                        <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Upload Data Sales Domestic</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Unggah file Excel hasil pengisian template Sales Domestic.</p>
                    </div>
                </div>

                <!-- Dropzone Box with real file input -->
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl p-8 text-center hover:border-sky-500 transition-colors bg-gray-50/50 dark:bg-gray-800/40 space-y-3">
                    <i class="fa-solid fa-file-excel text-4xl text-emerald-500"></i>
                    <div>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih file Excel / CSV untuk upload</p>
                        <p class="text-[10px] text-gray-400 mt-1">Format didukung: .xlsx, .xls, .csv (Maksimal 10MB)</p>
                    </div>
                    <input type="file" x-ref="domesticUploadFile" accept=".xlsx,.xls,.csv" class="hidden">
                    <button type="button" @click="$refs.domesticUploadFile.click()" class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold rounded-xl shadow-xs transition-all">
                        Pilih Berkas Excel
                    </button>
                    <button type="button" @click="uploadFile($refs.domesticUploadFile)" 
                            class="px-4 py-2 bg-primary hover:bg-primary/90 text-white text-xs font-semibold rounded-xl shadow-xs transition-all">
                        <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Upload & Proses
                    </button>
                </div>
            </div>

            <!-- Process Summary SKU Card -->
            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                        <i class="fa-solid fa-rotate text-lg"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Process Summary SKU</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Agregasi ulang data dari Regional ke tabel Summary Domestic SKU.</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/50 text-xs text-amber-900 dark:text-amber-200 space-y-2">
                    <p class="font-semibold flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Catatan Proses:
                    </p>
                    <p class="text-gray-600 dark:text-gray-300">
                        Proses ini akan mengonsolidasikan seluruh transaksi detail regional ke dalam ringkasan channel domestic untuk tahun aktif <strong>FY <?= esc($workingYear) ?></strong>.
                    </p>
                </div>

                <button type="button" @click="processSummarySKU()" 
                        class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                    <i class="fa-solid fa-gears"></i>
                    <span>Jalankan Process Summary SKU</span>
                </button>
            </div>
        </div>
    </div>

</div>

<!-- ============================================================ -->
<!-- 3. ALPINE.JS CONTROLLER & HIGHCHARTS SCRIPT -->
<!-- ============================================================ -->
<script>
function domesticSalesEntry(initialProducts = [], initialRegional = []) {
    return {
        subTab: 'budget', // Default active sub-tab: 'budget', 'key_product', 'regional', 'download', 'upload'
        showChart: false,
        chartMetric: 'revenue', // 'revenue' or 'qty'
        selectedDownloadChannel: 'GT',
        monthNames: ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'],

        // Flat kolom QTY/REV/ASP per bulan (tanpa x-fragment, biar render di semua build Alpine).
        metricCols: (() => {
            const arr = [];
            for (let m = 1; m <= 12; m++) {
                arr.push({ m, k: 'qty' }, { m, k: 'rev' }, { m, k: 'asp' });
            }
            return arr;
        })(),
        
        filters: {
            channel: '',
            search: '',
            channelKey: '',
            regional: ''
        },

        items: [],
        filteredItems: [],
        groupedItems: [],
        groupedFlatRows: [],
        keyProductsSummary: [],
        regionalData: [],
        
        kpi: {
            totalQty: 0,
            totalRev: 0,
            avgAsp: 0
        },

        grandTotal: {
            monthly: {},
            total_qty: 0,
            total_revenue: 0
        },

        setSubTab(tab) {
            this.subTab = tab;
            if (tab === 'budget' && this.showChart) {
                this.$nextTick(() => this.renderChart());
            }
        },

        initData() {
            // Data dari DB saja — tanpa fallback mock. Kosong → tampil empty-state.
            if (Array.isArray(initialProducts) && initialProducts.length > 0) {
                this.items = initialProducts.map((p, idx) => this.transformDbProduct(p, idx + 1));
            }

            if (Array.isArray(initialRegional) && initialRegional.length > 0) {
                this.regionalData = initialRegional.map((r, idx) => this.transformDbRegional(r, idx + 1));
            }

            this.applyFilters();
        },

        transformDbProduct(p, id) {
            const monthly = {};
            const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            
            for (let m = 1; m <= 12; m++) {
                const mk = months[m - 1];
                monthly[m] = {
                    qty: Number(p[`${mk}_qty`] || 0),
                    revenue: Number(p[`${mk}_rev`] || 0)
                };
            }

            const item = {
                id: id,
                id_channel: p.id_channel || 'GT',
                code_inv_1: p.mid_product || ('INV-' + id),
                key_product: p.key_product || 'GUMMY',
                code_inv_2: p.mid_product || ('INV-' + id),
                name_product: p.product_name || ('Product Item ' + id),
                monthly: monthly,
                total_qty: 0,
                total_revenue: 0
            };
            this.recalculateRow(item);
            return item;
        },

        generateMockItems() {
            const mockList = [
                { id_channel: 'GT', key_product: 'GUMMY', code: 'INV-01', name: 'Yupi Gummy Bear 100g Box', qty: 1200, rev: 15000000 },
                { id_channel: 'GT', key_product: 'GUMMY', code: 'INV-02', name: 'Yupi Burger 120g Pouch', qty: 1500, rev: 18000000 },
                { id_channel: 'MT', key_product: 'MARSHMALLOW', code: 'INV-03', name: 'Yupi Marshmallow Choco 80g', qty: 2000, rev: 24000000 },
                { id_channel: 'MT', key_product: 'MARSHMALLOW', code: 'INV-04', name: 'Yupi Marshmallow Twist 90g', qty: 1800, rev: 21600000 },
                { id_channel: 'OEM', key_product: 'BOLI', code: 'INV-05', name: 'Yupi Boli Gummy OEM Client A', qty: 3000, rev: 36000000 },
                { id_channel: 'ECOM', key_product: 'EXTR', code: 'INV-06', name: 'Yupi Festive Gift Box Limited', qty: 800, rev: 16000000 },
                { id_channel: 'YTI', key_product: 'GUMMY', code: 'INV-07', name: 'Yupi Neon Stix Export Grade', qty: 2500, rev: 32000000 }
            ];

            return mockList.map((m, idx) => {
                const monthly = {};
                for (let i = 1; i <= 12; i++) {
                    monthly[i] = { qty: m.qty, revenue: m.rev };
                }
                const item = {
                    id: idx + 1,
                    id_channel: m.id_channel,
                    code_inv_1: m.code,
                    key_product: m.key_product,
                    code_inv_2: m.code + '-A',
                    name_product: m.name,
                    monthly: monthly,
                    total_qty: 0,
                    total_revenue: 0
                };
                this.recalculateRow(item);
                return item;
            });
        },

        transformDbRegional(r, id) {
            const monthly = {};
            const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            for (let m = 1; m <= 12; m++) {
                const mk = months[m - 1];
                monthly[m] = {
                    qty: Number(r[`${mk}_qty`] || 0),
                    revenue: Number(r[`${mk}_rev`] || 0)
                };
            }
            return {
                id: id,
                region: r.region || 'WEST',
                country: r.country || 'INDONESIA',
                id_inv: r.id_inv || ('INV-REG-' + id),
                product_name: r.product_name || ('Regional Product ' + id),
                div: r.div || 'DOM',
                key_product: r.key_product || 'GUMMY',
                monthly: monthly,
                total_qty: Number(r.total_qty || 0),
                total_revenue: Number(r.total_revenue || 0)
            };
        },

        generateMockRegional() {
            const regions = ['WEST', 'CENTRAL', 'EAST', 'OUTER'];
            const countries = ['West Java & Jakarta', 'Central Java & DIY', 'East Java & Bali', 'Kalimantan & Sulawesi'];
            
            return [1, 2, 3, 4].map(i => {
                const monthly = {};
                let totQ = 0, totR = 0;
                for (let m = 1; m <= 12; m++) {
                    const q = 1500 * i;
                    const r = 18000000 * i;
                    monthly[m] = { qty: q, revenue: r };
                    totQ += q;
                    totR += r;
                }
                return {
                    id: i,
                    region: regions[i - 1],
                    country: countries[i - 1],
                    id_inv: 'INV-REG-0' + i,
                    product_name: 'Yupi Gummy Regional Pack ' + i,
                    div: 'DOM',
                    key_product: 'GUMMY',
                    monthly: monthly,
                    total_qty: totQ,
                    total_revenue: totR
                };
            });
        },

        recalculateRow(item) {
            let sumQty = 0, sumRev = 0;
            for (let m = 1; m <= 12; m++) {
                sumQty += Number(item.monthly[m]?.qty || 0);
                sumRev += Number(item.monthly[m]?.revenue || 0);
            }
            item.total_qty = sumQty;
            item.total_revenue = sumRev;

            this.calculateTotals();
        },

        applyFilters() {
            const ch = (this.filters.channel || '').toUpperCase();
            const q = (this.filters.search || '').toLowerCase();

            this.filteredItems = this.items.filter(item => {
                const matchCh = !ch || (item.id_channel || '').toUpperCase() === ch;
                const matchSearch = !q || 
                    (item.name_product || '').toLowerCase().includes(q) ||
                    (item.code_inv_1 || '').toLowerCase().includes(q) ||
                    (item.key_product || '').toLowerCase().includes(q);
                return matchCh && matchSearch;
            });

            this.groupItemsByChannel();
            this.calculateKeyProducts();
            this.calculateTotals();

            if (this.showChart) {
                this.renderChart();
            }
        },

        groupItemsByChannel() {
            const groupsMap = {};
            this.filteredItems.forEach(item => {
                const ch = item.id_channel || 'OTHER';
                if (!groupsMap[ch]) {
                    const initMonthly = {};
                    for (let m = 1; m <= 12; m++) initMonthly[m] = { qty: 0, revenue: 0 };
                    groupsMap[ch] = {
                        channel: ch,
                        items: [],
                        subtotal: {
                            monthly: initMonthly,
                            total_qty: 0,
                            total_revenue: 0
                        }
                    };
                }
                groupsMap[ch].items.push(item);
                
                // Aggregate subtotal
                for (let m = 1; m <= 12; m++) {
                    groupsMap[ch].subtotal.monthly[m].qty += Number(item.monthly[m]?.qty || 0);
                    groupsMap[ch].subtotal.monthly[m].revenue += Number(item.monthly[m]?.revenue || 0);
                }
                groupsMap[ch].subtotal.total_qty += Number(item.total_qty || 0);
                groupsMap[ch].subtotal.total_revenue += Number(item.total_revenue || 0);
            });

            this.groupedItems = Object.values(groupsMap);

            // Flat rows (subtotal + item) agar render satu <tr> per iterasi tanpa x-fragment.
            this.groupedFlatRows = [];
            this.groupedItems.forEach(g => {
                this.groupedFlatRows.push({ kind: 'subtotal', group: g });
                g.items.forEach((item, idx) => {
                    this.groupedFlatRows.push({ kind: 'item', group: g, item, idx });
                });
            });
        },

        calculateTotals() {
            let totQ = 0, totR = 0;
            const initMonthly = {};
            for (let m = 1; m <= 12; m++) initMonthly[m] = { qty: 0, revenue: 0 };

            this.filteredItems.forEach(item => {
                totQ += Number(item.total_qty || 0);
                totR += Number(item.total_revenue || 0);

                for (let m = 1; m <= 12; m++) {
                    initMonthly[m].qty += Number(item.monthly[m]?.qty || 0);
                    initMonthly[m].revenue += Number(item.monthly[m]?.revenue || 0);
                }
            });

            this.kpi.totalQty = totQ;
            this.kpi.totalRev = totR;
            this.kpi.avgAsp = this.calculateASP(totR, totQ);

            this.grandTotal = {
                monthly: initMonthly,
                total_qty: totQ,
                total_revenue: totR
            };
        },

        calculateKeyProducts() {
            const chFilter = (this.filters.channelKey || '').toUpperCase();
            const sourceItems = chFilter ? this.items.filter(i => i.id_channel === chFilter) : this.items;
            
            const kpMap = {};
            sourceItems.forEach(item => {
                const kpName = (item.key_product && item.key_product !== '-') ? item.key_product : 'MARSHMALLOW';
                if (!kpMap[kpName]) {
                    const mInit = {};
                    for (let m = 1; m <= 12; m++) mInit[m] = { qty: 0, revenue: 0 };
                    kpMap[kpName] = {
                        name: kpName,
                        monthly: mInit,
                        total_qty: 0,
                        total_revenue: 0
                    };
                }

                for (let m = 1; m <= 12; m++) {
                    kpMap[kpName].monthly[m].qty += Number(item.monthly[m]?.qty || 0);
                    kpMap[kpName].monthly[m].revenue += Number(item.monthly[m]?.revenue || 0);
                }
                kpMap[kpName].total_qty += Number(item.total_qty || 0);
                kpMap[kpName].total_revenue += Number(item.total_revenue || 0);
            });

            const result = Object.values(kpMap);
            
            // Add GRAND TOTAL row for Key Products
            const gtMonthly = {};
            for (let m = 1; m <= 12; m++) gtMonthly[m] = { qty: 0, revenue: 0 };
            let gtQ = 0, gtR = 0;

            result.forEach(kp => {
                for (let m = 1; m <= 12; m++) {
                    gtMonthly[m].qty += kp.monthly[m].qty;
                    gtMonthly[m].revenue += kp.monthly[m].revenue;
                }
                gtQ += kp.total_qty;
                gtR += kp.total_revenue;
            });

            result.push({
                name: 'GRAND TOTAL',
                monthly: gtMonthly,
                total_qty: gtQ,
                total_revenue: gtR
            });

            this.keyProductsSummary = result;
        },

        renderChart() {
            if (typeof Highcharts === 'undefined') return;

            const isRev = this.chartMetric === 'revenue';
            const seriesData = this.groupedItems.map(g => {
                const dataPoints = [];
                for (let m = 1; m <= 12; m++) {
                    dataPoints.push(isRev ? g.subtotal.monthly[m].revenue : g.subtotal.monthly[m].qty);
                }
                return {
                    name: 'Channel ' + g.channel,
                    data: dataPoints
                };
            });

            Highcharts.chart('domesticTrendChart', {
                chart: {
                    type: 'line',
                    backgroundColor: 'transparent',
                    style: { fontFamily: 'inherit' }
                },
                title: { text: null },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    labels: { style: { color: '#64748b', fontSize: '11px' } }
                },
                yAxis: {
                    title: { 
                        text: isRev ? 'Revenue (IDR)' : 'Volume (Kg)',
                        style: { color: '#64748b', fontSize: '11px' }
                    },
                    labels: {
                        style: { color: '#64748b', fontSize: '11px' },
                        formatter: function() {
                            return isRev ? ('Rp ' + (this.value / 1000000).toFixed(0) + 'M') : (this.value + ' kg');
                        }
                    }
                },
                legend: {
                    itemStyle: { color: '#475569', fontSize: '12px' }
                },
                credits: { enabled: false },
                tooltip: {
                    shared: true,
                    pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y:,.0f}</b><br/>'
                },
                series: seriesData
            });
        },

        calculateASP(revenue, qty, channel) {
            if (qty <= 0) return 0;
            // PRD: ASP domestic = REV/QTY × 1000 (QTY dalam Ton → Kg), kecuali channel YTI.
            const factor = String(channel || '') === 'YTI' ? 1 : 1000;
            return (revenue / qty) * factor;
        },

        formatNumber(val) {
            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(val || 0);
        },

        formatCurrency(val) {
            return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(val || 0);
        },

        downloadChannelTemplate() {
            const ch = this.selectedDownloadChannel || 'GT';
            if (window.ypToast) {
                window.ypToast.info('Mengunduh template channel ' + ch + '...');
            }
            window.open(`<?= base_url('sales/export_template_sales') ?>/${ch}?type=VOL`, '_blank');
            setTimeout(() => {
                window.open(`<?= base_url('sales/export_template_sales') ?>/${ch}?type=REV`, '_blank');
            }, 1000);
        },

        downloadSingleTemplate(type) {
            const ch = this.selectedDownloadChannel || 'GT';
            window.open(`<?= base_url('sales/export_template_sales') ?>/${ch}?type=${type}`, '_blank');
        },

        fetchRegionalData() {
            const regional = this.filters.regional || '';
            if (window.ypToast) window.ypToast.info('Memuat data regional ' + (regional || 'ALL') + '...');

            fetch(`<?= base_url('sales/getRegionalData') ?>?regional=${encodeURIComponent(regional)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success' && Array.isArray(res.data)) {
                    this.regionalData = res.data.map((r, idx) => this.transformDbRegional(r, idx + 1));
                    if (window.ypToast) window.ypToast.success('Data regional berhasil dimuat (' + res.data.length + ' baris).');
                } else {
                    if (window.ypToast) window.ypToast.error('Gagal memuat data regional.');
                }
            })
            .catch(err => {
                console.error(err);
                if (window.ypToast) window.ypToast.error('Error: ' + err.message);
            });
        },

        exportRegionalExcel() {
            const regional = this.filters.regional || '';
            window.open(`<?= base_url('sales/exportRegionalExcel') ?>?regional=${encodeURIComponent(regional)}`, '_blank');
        },

        /**
         * Simpan seluruh data budget domestic ke backend (batch upsert).
         */
        saveChanges() {
            if (this.items.length === 0) {
                if (window.ypToast) window.ypToast.error('Tidak ada data untuk disimpan.');
                return;
            }

            const payload = this.items.map(item => ({
                id_inv: item.code_inv_1,
                id_channel: item.id_channel,
                monthly: item.monthly
            }));

            if (window.ypToast) window.ypToast.info('Menyimpan data budget domestic...');

            fetch('<?= base_url('sales/saveDomesticEntry') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({ items: payload })
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    if (window.ypToast) window.ypToast.success(res.message);
                } else {
                    if (window.ypToast) window.ypToast.error(res.message || 'Gagal menyimpan.');
                }
            })
            .catch(err => {
                console.error(err);
                if (window.ypToast) window.ypToast.error('Error: ' + err.message);
            });
        },

        /**
         * Upload file Excel domestic via form data.
         */
        uploadFile(fileInput) {
            const file = fileInput?.files?.[0];
            if (!file) {
                if (window.ypToast) window.ypToast.error('Pilih file terlebih dahulu.');
                return;
            }

            const formData = new FormData();
            formData.append('excel_file', file);

            if (window.ypToast) window.ypToast.info('Mengupload file Sales Domestic...');

            fetch('<?= base_url('sales/uploadDomestic') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    if (window.ypToast) window.ypToast.success(res.message);
                    setTimeout(() => location.reload(), 800);
                } else {
                    if (window.ypToast) window.ypToast.error(res.message || 'Upload gagal.');
                }
            })
            .catch(err => {
                console.error(err);
                if (window.ypToast) window.ypToast.error('Error: ' + err.message);
            });
        },

        triggerUploadModal() {
            this.$dispatch('open-upload-modal', { type: 'domestic' });
        },

        processSummarySKU() {
            if (confirm('Yakin ingin memproses summary SKU Sales Domestic? Data summary akan di-reagregasi dari data regional.')) {
                if (window.ypToast) {
                    window.ypToast.info('Memproses Summary SKU...');
                }
                // Call server process endpoint via ypFetch
                window.ypFetch('<?= base_url('sales/proses_summary_domestic') ?>', {})
                    .then(res => {
                        if (res.success) {
                            if (window.ypToast) window.ypToast.success(res.message || 'Proses summary SKU selesai');
                            location.reload();
                        } else {
                            if (window.ypToast) window.ypToast.error(res.message || 'Gagal memproses summary SKU');
                        }
                    });
            }
        }
    };
}
</script>
<?= $this->endSection() ?>