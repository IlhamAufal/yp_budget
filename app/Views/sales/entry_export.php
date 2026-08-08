<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
  $workingYear = $workingYear ?? date('Y');
  $dbProductsJson      = json_encode($exportProducts ?? []);
  $dbCountryDetailJson = json_encode($exportCountryDetail ?? []);
  $dbRegionSummaryJson = json_encode($exportRegionSummary ?? []);
?>
<div x-data="exportSalesEntry(<?= htmlspecialchars($dbProductsJson, ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($dbCountryDetailJson, ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($dbRegionSummaryJson, ENT_QUOTES, 'UTF-8') ?>)" 
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
                <span class="text-primary font-bold">Sales International Entry</span>
            </div>

            <!-- Page Title -->
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 shadow-xs">
                    <i class="fa-solid fa-plane-departure text-lg"></i>
                </span>
                <div>
                    <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                        2.2 Sales International (Export) Entry
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800">
                            USD ($) · FY <?= esc($workingYear) ?>
                        </span>
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Pengelolaan target dan laporan Sales International / Export per Negara (Valas USD $).
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Summary Stats Header Badges -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                <span class="text-gray-500 dark:text-gray-400 font-medium">Tot Volume:</span>
                <span class="font-mono font-bold text-gray-900 dark:text-white" x-text="formatNumber(kpi.totalQty)">0</span>
                <span class="text-[10px] text-gray-400 font-mono">Kg</span>
            </div>
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <span class="text-gray-500 dark:text-gray-400 font-medium">Tot Revenue:</span>
                <span class="font-mono font-bold text-amber-600 dark:text-amber-400" x-text="formatValas(kpi.totalRev)">$ 0.00</span>
            </div>
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-gray-500 dark:text-gray-400 font-medium">Avg ASP:</span>
                <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="formatValas(kpi.avgAsp)">$ 0.00</span>
                <span class="text-[10px] text-gray-400 font-mono">/Kg</span>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- 2. SUB-TABS NAVIGATION BAR (6 SUB-TABS) -->
    <!-- ============================================================ -->
    <div class="bg-gray-100/80 dark:bg-gray-800/60 p-1.5 rounded-2xl border border-gray-200/80 dark:border-gray-700/60 shadow-xs">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="Export Sub Tabs">
            <button type="button" @click="subTab = 'budget'" 
                    :class="subTab === 'budget' ? 'bg-white dark:bg-gray-900 text-primary dark:text-white shadow-xs font-bold border border-gray-200/80 dark:border-gray-700' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/50'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-table-cells text-sm"></i>
                <span>Sales International - Budget ($)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-primary/10 text-primary font-bold" x-text="filteredItems.length">0</span>
            </button>

            <button type="button" @click="subTab = 'key_product'" 
                    :class="subTab === 'key_product' ? 'bg-white dark:bg-gray-900 text-primary dark:text-white shadow-xs font-bold border border-gray-200/80 dark:border-gray-700' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/50'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-award text-sm text-amber-500"></i>
                <span>Report Key Product ($)</span>
            </button>

            <button type="button" @click="subTab = 'country'" 
                    :class="subTab === 'country' ? 'bg-white dark:bg-gray-900 text-primary dark:text-white shadow-xs font-bold border border-gray-200/80 dark:border-gray-700' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/50'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-globe text-sm text-blue-500"></i>
                <span>Report Country ($)</span>
            </button>

            <button type="button" @click="subTab = 'regional'" 
                    :class="subTab === 'regional' ? 'bg-white dark:bg-gray-900 text-primary dark:text-white shadow-xs font-bold border border-gray-200/80 dark:border-gray-700' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/50'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-sm text-indigo-500"></i>
                <span>Summary Regional Area ($)</span>
            </button>

            <button type="button" @click="subTab = 'download'" 
                    :class="subTab === 'download' ? 'bg-white dark:bg-gray-900 text-primary dark:text-white shadow-xs font-bold border border-gray-200/80 dark:border-gray-700' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/50'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-sm text-emerald-600 dark:text-emerald-400"></i>
                <span>Download Template</span>
            </button>

            <button type="button" @click="subTab = 'upload'" 
                    :class="subTab === 'upload' ? 'bg-white dark:bg-gray-900 text-primary dark:text-white shadow-xs font-bold border border-gray-200/80 dark:border-gray-700' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/50'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up text-sm text-sky-600 dark:text-sky-400"></i>
                <span>Upload Data</span>
            </button>
        </nav>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 1: SALES INTERNATIONAL - BUDGET ($) -->
    <!-- ============================================================ -->
    <div x-show="subTab === 'budget'" x-cloak class="space-y-6">

        <!-- Toolbar & Filter Area -->
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <!-- Channel Filter Dropdown -->
                <div class="flex items-center gap-2 min-w-[220px]">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5 shrink-0">
                        <i class="fa-solid fa-filter text-primary"></i> Channel:
                    </label>
                    <select x-model="filters.channel" @change="applyFilters()" 
                            class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="">EXPORT (Sales International)</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="relative flex-1 min-w-[240px]">
                    <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()" 
                           placeholder="Cari SKU Code / Nama Produk Export / Key Product..." 
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-gray-400"></i>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" @click="downloadExportTemplate()" 
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-1.5 active:scale-[0.98]">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Template Data Export</span>
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
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Export Volume (QTY)</p>
                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white font-mono mt-1" x-text="formatNumber(kpi.totalQty)">0</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">Total Volume (Kg)</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <i class="fa-solid fa-weight-hanging text-lg"></i>
                </span>
            </div>

            <div class="bg-white dark:bg-gray-900 p-4.5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Export Revenue ($)</p>
                    <h3 class="text-xl font-extrabold text-amber-600 dark:text-amber-400 font-mono mt-1" x-text="formatValas(kpi.totalRev)">$ 0.00</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">Total Revenue (USD)</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fa-solid fa-dollar-sign text-lg"></i>
                </span>
            </div>

            <div class="bg-white dark:bg-gray-900 p-4.5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Average ASP ($)</p>
                    <h3 class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400 font-mono mt-1" x-text="formatValas(kpi.avgAsp)">$ 0.00</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">Rata-rata USD / Kg</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fa-solid fa-calculator text-lg"></i>
                </span>
            </div>

            <div class="bg-white dark:bg-gray-900 p-4.5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Export SKUs</p>
                    <h3 class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400 font-mono mt-1" x-text="filteredItems.length">0</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">Produk Ekspor Terdaftar</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </span>
            </div>
        </div>

        <!-- Pivot Data Table (Excel Grid) -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/40">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-table text-primary"></i>
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Target Sales International 12 Bulan (Valas USD $)</h3>
                </div>
                <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium italic">ASP Export Formula: Revenue / Qty ($/Kg)</span>
            </div>

            <div class="overflow-x-auto scrollbar-thin max-h-[600px]">
                <table class="w-full text-left text-[11px] border-collapse min-w-[2800px]">
                    <thead class="bg-gray-100/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-300 font-bold uppercase tracking-wider border-b border-gray-300 dark:border-gray-700 sticky top-0 z-20">
                        <tr>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 w-10 text-center sticky left-0 z-30 bg-gray-100 dark:bg-gray-800" rowspan="2">No.</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[90px] text-center sticky left-10 z-30 bg-gray-100 dark:bg-gray-800" rowspan="2">CHANNEL</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[130px]" rowspan="2">KEY PRODUCT</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[100px]" rowspan="2">CODE INV</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[220px]" rowspan="2">PRODUCT NAME</th>
                            
                            <template x-for="(month, idx) in monthNames" :key="month">
                                <th class="p-2 border-r border-gray-300 dark:border-gray-700 text-center" 
                                    :class="idx % 2 === 0 ? 'bg-amber-50/70 dark:bg-gray-800/80' : 'bg-gray-100/70 dark:bg-gray-800/40'" 
                                    colspan="3" x-text="month"></th>
                            </template>
                            
                            <th class="p-2 border-l-2 border-amber-500/40 bg-amber-500/10 dark:bg-amber-500/20 text-center font-extrabold text-amber-700 dark:text-amber-300" colspan="3">ANNUAL TOTAL ($)</th>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-[10px]">
                            <template x-for="(month, idx) in monthNames" :key="'sub-'+idx">
                                <template x-fragment>
                                    <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-18 text-center bg-gray-50/80 dark:bg-gray-800">QTY (Kg)</th>
                                    <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-24 text-center bg-gray-50/80 dark:bg-gray-800 text-amber-600 dark:text-amber-400">REV ($)</th>
                                    <th class="p-1 border-r border-gray-300 dark:border-gray-700 w-20 text-center bg-gray-100/50 dark:bg-gray-800/60">ASP ($)</th>
                                </template>
                            </template>
                            <th class="p-1 border-r border-gray-300 dark:border-gray-700 w-24 text-center bg-amber-500/10 text-amber-700 font-bold">TOT QTY</th>
                            <th class="p-1 border-r border-gray-300 dark:border-gray-700 w-28 text-center bg-amber-500/10 text-amber-700 font-bold">TOT REV ($)</th>
                            <th class="p-1 border-r border-gray-300 dark:border-gray-700 w-24 text-center bg-amber-500/10 text-amber-700 font-bold">AVG ASP ($)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono">
                        <template x-for="(item, idx) in filteredItems" :key="item.id || idx">
                            <tr class="hover:bg-amber-50/40 dark:hover:bg-gray-800/60 transition-colors">
                                <td class="p-2 text-center border-r border-gray-200 dark:border-gray-800 sticky left-0 z-10 bg-white dark:bg-gray-900 text-gray-500" x-text="idx + 1"></td>
                                <td class="p-2 text-center border-r border-gray-200 dark:border-gray-800 font-sans font-bold text-xs sticky left-10 z-10 bg-white dark:bg-gray-900 text-amber-700 dark:text-amber-400">EXPORT</td>
                                <td class="p-2 border-r border-gray-200 dark:border-gray-800 font-sans text-xs text-gray-600 dark:text-gray-400" x-text="item.key_product || '-'"></td>
                                <td class="p-2 border-r border-gray-200 dark:border-gray-800 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="item.code_inv_1 || item.mid_product"></td>
                                <td class="p-2 border-r border-gray-200 dark:border-gray-800 font-sans font-medium text-xs text-gray-900 dark:text-white" x-text="item.product_name || item.name_product"></td>
                                
                                <template x-for="m in 12" :key="'item-m-'+m">
                                    <template x-fragment>
                                        <td class="p-1 border-r border-gray-200 dark:border-gray-800">
                                            <input type="number" x-model.number="item.monthly[m].qty" @input="recalculateRow(item)" 
                                                   class="w-full text-right p-1 text-[11px] border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-amber-500 focus:bg-white dark:focus:bg-gray-900">
                                        </td>
                                        <td class="p-1 border-r border-gray-200 dark:border-gray-800">
                                            <input type="number" step="0.01" x-model.number="item.monthly[m].revenue" @input="recalculateRow(item)" 
                                                   class="w-full text-right p-1 text-[11px] border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-amber-600 dark:text-amber-400 font-semibold focus:ring-1 focus:ring-amber-500 focus:bg-white dark:focus:bg-gray-900">
                                        </td>
                                        <td class="p-1 border-r border-gray-300 dark:border-gray-700 text-right bg-amber-50/30 dark:bg-gray-800/40 font-medium text-gray-700 dark:text-gray-300" 
                                            x-text="formatValas(calculateASP(item.monthly[m].revenue, item.monthly[m].qty))"></td>
                                    </template>
                                </template>

                                <td class="p-2 border-l-2 border-amber-500/30 border-r border-gray-200 dark:border-gray-800 bg-amber-50/10 dark:bg-gray-800 text-right font-bold text-gray-900 dark:text-white" x-text="formatNumber(item.total_qty)"></td>
                                <td class="p-2 border-r border-gray-200 dark:border-gray-800 bg-amber-50/10 dark:bg-gray-800 text-right font-bold text-amber-600 dark:text-amber-400" x-text="formatValas(item.total_revenue)"></td>
                                <td class="p-2 bg-amber-50/10 dark:bg-gray-800 text-right font-bold text-emerald-600 dark:text-emerald-400" x-text="formatValas(calculateASP(item.total_revenue, item.total_qty))"></td>
                            </tr>
                        </template>

                        <!-- Grand Total Row -->
                        <tr class="bg-amber-500/10 dark:bg-amber-500/20 font-extrabold text-gray-900 dark:text-white border-t-2 border-amber-500/30 text-xs">
                            <td class="p-3 text-center sticky left-0 z-10 bg-amber-200/50 dark:bg-amber-900/40" colspan="5">
                                TOTAL SALES INTERNATIONAL (EXPORT $)
                            </td>
                            <template x-for="m in 12" :key="'gt-m-'+m">
                                <template x-fragment>
                                    <td class="p-2 text-right border-r border-amber-500/20" x-text="formatNumber(grandTotal.monthly[m].qty)"></td>
                                    <td class="p-2 text-right border-r border-amber-500/20 text-amber-600 dark:text-amber-400 font-bold" x-text="formatValas(grandTotal.monthly[m].revenue)"></td>
                                    <td class="p-2 text-right border-r border-amber-500/30 bg-amber-500/15 dark:bg-amber-500/25" x-text="formatValas(calculateASP(grandTotal.monthly[m].revenue, grandTotal.monthly[m].qty))"></td>
                                </template>
                            </template>
                            <td class="p-3 text-right border-r border-amber-500/30 font-mono text-sm font-extrabold" x-text="formatNumber(grandTotal.total_qty)"></td>
                            <td class="p-3 text-right border-r border-amber-500/30 font-mono text-sm font-extrabold text-amber-600 dark:text-amber-400" x-text="formatValas(grandTotal.total_revenue)"></td>
                            <td class="p-3 text-right font-mono text-sm font-extrabold text-emerald-600 dark:text-emerald-400" x-text="formatValas(calculateASP(grandTotal.total_revenue, grandTotal.total_qty))"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 2: REPORT KEY PRODUCT ($) -->
    <!-- ============================================================ -->
    <div x-show="subTab === 'key_product'" x-cloak class="space-y-6">
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-award text-amber-500"></i>
                        Report Key Product ($) Export
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Rekapitulasi performa per Kategori Key Product Export dalam USD ($).</p>
                </div>
            </div>

            <!-- Key Product Table -->
            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs border-collapse min-w-[2400px]">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold uppercase tracking-wider border-b border-gray-300 dark:border-gray-700 text-center">
                        <tr>
                            <th class="p-3 border-r border-gray-300 dark:border-gray-700 w-12" rowspan="2">No.</th>
                            <th class="p-3 border-r border-gray-300 dark:border-gray-700 min-w-[180px] text-left" rowspan="2">KEY PRODUCT</th>
                            <template x-for="month in monthNames" :key="'key-'+month">
                                <th class="p-2 border-r border-gray-300 dark:border-gray-700" colspan="3" x-text="month"></th>
                            </template>
                            <th class="p-2 border-l-2 border-amber-500/40 bg-amber-500/10 text-center" colspan="3">TOTAL ($)</th>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-[10px]">
                            <template x-for="i in 13" :key="'key-sub-'+i">
                                <template x-fragment>
                                    <th class="p-1.5 border-r border-gray-200 dark:border-gray-700 w-20 text-center">QTY</th>
                                    <th class="p-1.5 border-r border-gray-200 dark:border-gray-700 w-28 text-center text-amber-600 dark:text-amber-400">REV ($)</th>
                                    <th class="p-1.5 border-r border-gray-200 dark:border-gray-700 w-20 text-center">ASP ($)</th>
                                </template>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono">
                        <template x-for="(kp, idx) in keyProductsSummary" :key="kp.name">
                            <tr :class="kp.name === 'GRAND TOTAL' ? 'bg-amber-500/10 dark:bg-amber-500/20 font-extrabold text-gray-900 dark:text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'">
                                <td class="p-2.5 text-center border-r border-gray-200 dark:border-gray-800" x-text="kp.name === 'GRAND TOTAL' ? '' : (idx + 1)"></td>
                                <td class="p-2.5 border-r border-gray-200 dark:border-gray-800 font-sans font-bold text-gray-900 dark:text-white" x-text="kp.name"></td>
                                <template x-for="m in 12" :key="'kp-m-'+m">
                                    <template x-fragment>
                                        <td class="p-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="formatNumber(kp.monthly[m].qty)"></td>
                                        <td class="p-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="formatValas(kp.monthly[m].revenue)"></td>
                                        <td class="p-2 text-right border-r border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40" x-text="formatValas(calculateASP(kp.monthly[m].revenue, kp.monthly[m].qty))"></td>
                                    </template>
                                </template>
                                <td class="p-2.5 text-right border-l-2 border-amber-500/30 border-r border-gray-200 dark:border-gray-800 font-bold" x-text="formatNumber(kp.total_qty)"></td>
                                <td class="p-2.5 text-right border-r border-gray-200 dark:border-gray-800 font-bold text-amber-600 dark:text-amber-400" x-text="formatValas(kp.total_revenue)"></td>
                                <td class="p-2.5 text-right font-bold text-emerald-600 dark:text-emerald-400" x-text="formatValas(calculateASP(kp.total_revenue, kp.total_qty))"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 3: REPORT COUNTRY ($) -->
    <!-- ============================================================ -->
    <div x-show="subTab === 'country'" x-cloak class="space-y-6">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <div class="flex items-center gap-3">
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                    <i class="fa-solid fa-globe text-primary"></i> Country:
                </label>
                <select x-model="filters.country" class="text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-3 min-w-[200px]">
                    <option value="">- All Country -</option>
                    <option value="USA">United States</option>
                    <option value="TH">Thailand</option>
                    <option value="MY">Malaysia</option>
                    <option value="TW">Taiwan</option>
                    <option value="SG">Singapore</option>
                    <option value="UK">United Kingdom</option>
                </select>
                <button type="button" @click="fetchCountryData()" class="px-3.5 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold rounded-xl transition-all shadow-xs flex items-center gap-1">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
            </div>

            <button type="button" @click="exportCountryExcel()" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Report Country Excel</span>
            </button>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800 p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-flag text-indigo-500"></i>
                    Detail Break Down Target Export per Negara (Report Country)
                </h3>
            </div>

            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs border-collapse min-w-[2700px]">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold uppercase border-b border-gray-300 dark:border-gray-700 text-center">
                        <tr>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 w-10" rowspan="2">No.</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[110px]" rowspan="2">REGION</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[130px]" rowspan="2">COUNTRY</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[100px]" rowspan="2">CODE INV</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[200px]" rowspan="2">PRODUCT NAME</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[70px]" rowspan="2">DIV</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[110px]" rowspan="2">KEY PRODUCT</th>
                            <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[90px]" rowspan="2">CURRENCY</th>
                            <template x-for="month in monthNames" :key="'c-'+month">
                                <th class="p-2 border-r border-gray-300 dark:border-gray-700 text-center" colspan="3" x-text="month"></th>
                            </template>
                            <th class="p-2 border-l-2 border-amber-500/40 bg-amber-500/10 text-center" colspan="3 font-extrabold">TOTAL ($)</th>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-[10px]">
                            <template x-for="i in 13" :key="'c-sub-'+i">
                                <template x-fragment>
                                    <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-18 text-center">QTY</th>
                                    <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-24 text-center text-amber-600 dark:text-amber-400">REV ($)</th>
                                    <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-20 text-center">ASP ($)</th>
                                </template>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-[11px]">
                        <template x-if="countryDetails.length === 0">
                            <tr>
                                <td colspan="47" class="p-12 text-center text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-globe text-2xl mb-3"></i>
                                    <p class="font-semibold text-gray-600 dark:text-gray-300">Belum ada data report country export</p>
                                    <p class="text-xs mt-1">Upload data detail transaction via tab <strong>Upload Data</strong>.</p>
                                </td>
                            </tr>
                        </template>
                        <template x-for="(c, idx) in countryDetails" :key="c.id || idx">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="p-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="idx + 1"></td>
                                <td class="p-2 font-sans font-semibold border-r border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white" x-text="c.region"></td>
                                <td class="p-2 font-sans border-r border-gray-200 dark:border-gray-800" x-text="c.country"></td>
                                <td class="p-2 border-r border-gray-200 dark:border-gray-800" x-text="c.id_inv"></td>
                                <td class="p-2 font-sans font-medium border-r border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white" x-text="c.product_name"></td>
                                <td class="p-2 border-r border-gray-200 dark:border-gray-800 text-center" x-text="c.div"></td>
                                <td class="p-2 font-sans border-r border-gray-200 dark:border-gray-800" x-text="c.key_product"></td>
                                <td class="p-2 border-r border-gray-200 dark:border-gray-800 text-center font-bold text-indigo-600 dark:text-indigo-400" x-text="c.currency || 'USD'"></td>
                                <template x-for="m in 12" :key="'c-m-'+m">
                                    <template x-fragment>
                                        <td class="p-1 text-right border-r border-gray-200 dark:border-gray-800" x-text="formatNumber(c.monthly[m].qty)"></td>
                                        <td class="p-1 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400" x-text="formatValas(c.monthly[m].revenue)"></td>
                                        <td class="p-1 text-right border-r border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30" x-text="formatValas(calculateASP(c.monthly[m].revenue, c.monthly[m].qty))"></td>
                                    </template>
                                </template>
                                <td class="p-2 text-right border-l-2 border-amber-500/30 border-r border-gray-200 dark:border-gray-800 font-bold" x-text="formatNumber(c.total_qty)"></td>
                                <td class="p-2 text-right border-r border-gray-200 dark:border-gray-800 font-bold text-amber-600 dark:text-amber-400" x-text="formatValas(c.total_revenue)"></td>
                                <td class="p-2 text-right font-bold text-emerald-600 dark:text-emerald-400" x-text="formatValas(calculateASP(c.total_revenue, c.total_qty))"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 4: SUMMARY REGIONAL AREA ($) -->
    <!-- ============================================================ -->
    <div x-show="subTab === 'regional'" x-cloak class="space-y-6">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <div class="flex items-center gap-3">
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                    <i class="fa-solid fa-chart-pie text-primary"></i> Regional Area:
                </label>
                <select x-model="filters.regional" class="text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-3 min-w-[200px]">
                    <option value="">- All Regional -</option>
                    <option value="ASIA">Asia Pacific</option>
                    <option value="AMER">Americas</option>
                    <option value="EUROPE">Europe</option>
                </select>
                <button type="button" @click="fetchRegionalData()" class="px-3.5 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold rounded-xl transition-all shadow-xs flex items-center gap-1">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
            </div>
        </div>

        <!-- Summary Regional Matrix Table -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800 p-6 space-y-4">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-list-check text-indigo-500"></i>
                Ringkasan Performansi Regional Area (Volume, Revenue $, ASP $)
            </h3>

            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs border-collapse min-w-[1600px]">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold uppercase tracking-wider border-b border-gray-300 dark:border-gray-700">
                        <tr>
                            <th class="p-3 min-w-[180px]">REGIONAL AREA</th>
                            <th class="p-3 w-28">METRICS</th>
                            <template x-for="month in monthNames" :key="'reg-head-'+month">
                                <th class="p-2.5 text-right" x-text="month"></th>
                            </template>
                            <th class="p-3 text-right font-extrabold bg-primary/10 text-primary">TOTAL ($)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono">
                        <template x-if="regionalSummaries.length === 0">
                            <tr>
                                <td colspan="14" class="p-12 text-center text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-earth-asia text-2xl mb-3"></i>
                                    <p class="font-semibold text-gray-600 dark:text-gray-300">Belum ada summary regional export</p>
                                    <p class="text-xs mt-1">Upload data detail transaction terlebih dahulu.</p>
                                </td>
                            </tr>
                        </template>
                        <template x-for="reg in regionalSummaries" :key="reg.region">
                            <template x-fragment>
                                <!-- Region Header Bar -->
                                <tr class="bg-amber-50 dark:bg-amber-950/30 border-t-2 border-amber-200 dark:border-amber-900">
                                    <td class="p-2.5 font-bold font-sans text-amber-900 dark:text-amber-200 text-xs" colspan="15" x-text="'REGIONAL: ' + reg.region"></td>
                                </tr>
                                <!-- Volume Row -->
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                                    <td class="p-2 pl-4 font-sans font-medium" rowspan="3" x-text="reg.region"></td>
                                    <td class="p-2 font-sans font-semibold text-blue-600 dark:text-blue-400">Volume (Kg)</td>
                                    <template x-for="m in 12" :key="'v-m-'+m">
                                        <td class="p-2 text-right" x-text="formatNumber(reg.monthly[m].qty)"></td>
                                    </template>
                                    <td class="p-2 text-right font-bold bg-gray-50 dark:bg-gray-800" x-text="formatNumber(reg.total_qty)"></td>
                                </tr>
                                <!-- Revenue Row -->
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                                    <td class="p-2 font-sans font-semibold text-amber-600 dark:text-amber-400">Revenue ($)</td>
                                    <template x-for="m in 12" :key="'r-m-'+m">
                                        <td class="p-2 text-right text-amber-600 dark:text-amber-400 font-semibold" x-text="formatValas(reg.monthly[m].revenue)"></td>
                                    </template>
                                    <td class="p-2 text-right font-bold text-amber-600 dark:text-amber-400 bg-gray-50 dark:bg-gray-800" x-text="formatValas(reg.total_revenue)"></td>
                                </tr>
                                <!-- ASP Row -->
                                <tr class="bg-sky-50/50 dark:bg-gray-800/30">
                                    <td class="p-2 font-sans font-semibold text-emerald-600 dark:text-emerald-400">ASP ($/Kg)</td>
                                    <template x-for="m in 12" :key="'a-m-'+m">
                                        <td class="p-2 text-right text-emerald-600 dark:text-emerald-400" x-text="formatValas(calculateASP(reg.monthly[m].revenue, reg.monthly[m].qty))"></td>
                                    </template>
                                    <td class="p-2 text-right font-bold text-emerald-600 dark:text-emerald-400 bg-gray-50 dark:bg-gray-800" x-text="formatValas(calculateASP(reg.total_revenue, reg.total_qty))"></td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Country Volume Breakdown Table (50% Width) -->
            <div class="pt-6 border-t border-gray-100 dark:border-gray-800 max-w-2xl mx-auto">
                <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3 text-center flex items-center justify-center gap-2">
                    <i class="fa-solid fa-earth-americas text-blue-500"></i>
                    Country Target Market Volume Breakdown
                </h4>
                <div class="overflow-x-auto rounded-xl border border-gray-200/80 dark:border-gray-800">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold uppercase border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="p-3">Country Target Market</th>
                                <th class="p-3 text-right">Total Volume (Kg/Box)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono">
                            <tr class="bg-amber-50 dark:bg-amber-950/30 font-extrabold text-amber-900 dark:text-amber-200">
                                <td class="p-3">Total Target Volume Export</td>
                                <td class="p-3 text-right font-bold text-sm text-amber-600 dark:text-amber-400" x-text="formatNumber(kpi.totalQty)">15,337,587.35</td>
                            </tr>
                            <template x-for="c in countrySummaries" :key="c.country">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="p-2.5 font-medium text-gray-900 dark:text-white" x-text="c.country"></td>
                                    <td class="p-2.5 text-right font-bold text-gray-700 dark:text-gray-300" x-text="formatValas(c.total_volume)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 5: DOWNLOAD TEMPLATE -->
    <!-- ============================================================ -->
    <div x-show="subTab === 'download'" x-cloak class="space-y-6">
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-6">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-400"></i>
                    Download Template Excel Sales International (Export)
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unduh berkas template resmi untuk pengisian masal data target Sales Export (Volume & Revenue USD).</p>
            </div>

            <!-- Download Action Box -->
            <div class="bg-slate-50 dark:bg-gray-800/60 p-5 rounded-xl border border-gray-200/80 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white">Template Data Upload Budget (EXPORTV2)</h4>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Template resmi format V2 untuk pengisian data detail transaksi ekspor.</p>
                </div>

                <button type="button" @click="downloadExportTemplate()" 
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-2 active:scale-[0.98]">
                    <i class="fa-solid fa-download"></i>
                    <span>Unduh Template Export (.xlsx)</span>
                </button>
            </div>

            <!-- Download Template Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-start gap-4 hover:border-emerald-300 transition-all">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 shrink-0">
                        <i class="fa-solid fa-weight-hanging text-xl"></i>
                    </span>
                    <div class="space-y-2 flex-1">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Template Volume Export (VOL - Kg)</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Template Excel khusus untuk pengisian target Volume Ekspor (Kg) per SKU per Negara.</p>
                        <button type="button" @click="downloadExportTemplate('VOL')" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            <i class="fa-solid fa-download"></i> Unduh File Volume Export (.xlsx)
                        </button>
                    </div>
                </div>

                <div class="p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-start gap-4 hover:border-emerald-300 transition-all">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 shrink-0">
                        <i class="fa-solid fa-sack-dollar text-xl"></i>
                    </span>
                    <div class="space-y-2 flex-1">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Template Revenue Export (REV - USD $)</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Template Excel khusus untuk pengisian target Revenue Ekspor (USD) per SKU per Negara.</p>
                        <button type="button" @click="downloadExportTemplate('REV')" class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600 dark:text-amber-400 hover:underline">
                            <i class="fa-solid fa-download"></i> Unduh File Revenue Export (.xlsx)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 6: UPLOAD DATA -->
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
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Upload Data Detail Transaction Export</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Unggah file Excel hasil pengisian template Sales Export (Format V2).</p>
                    </div>
                </div>

                <!-- Dropzone Box with real file input -->
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl p-8 text-center hover:border-sky-500 transition-colors bg-gray-50/50 dark:bg-gray-800/40 space-y-3">
                    <i class="fa-solid fa-file-excel text-4xl text-emerald-500"></i>
                    <div>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih file Excel / CSV untuk upload</p>
                        <p class="text-[10px] text-gray-400 mt-1">Format didukung: .xlsx, .xls, .csv (Maksimal 10MB)</p>
                    </div>
                    <input type="file" x-ref="exportUploadFile" accept=".xlsx,.xls,.csv" class="hidden">
                    <button type="button" @click="$refs.exportUploadFile.click()" class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold rounded-xl shadow-xs transition-all">
                        Pilih Berkas Excel Export
                    </button>
                    <button type="button" @click="uploadFile($refs.exportUploadFile)" 
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
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Process Summary SKU Export</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Agregasi ulang data dari transaksi negara ke tabel Summary Export SKU.</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/50 text-xs text-amber-900 dark:text-amber-200 space-y-2">
                    <p class="font-semibold flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Catatan Proses:
                    </p>
                    <p class="text-gray-600 dark:text-gray-300">
                        Proses ini akan mengonsolidasikan seluruh transaksi detail negara ke dalam ringkasan ekspor untuk tahun aktif <strong>FY <?= esc($workingYear) ?></strong>.
                    </p>
                </div>

                <button type="button" @click="processSummarySKU()" 
                        class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                    <i class="fa-solid fa-gears"></i>
                    <span>Jalankan Process Summary SKU Export</span>
                </button>
            </div>
        </div>
    </div>

</div>

<!-- ============================================================ -->
<!-- 3. ALPINE.JS CONTROLLER SCRIPT -->
<!-- ============================================================ -->
<script>
function exportSalesEntry(initialProducts = [], initialCountries = [], initialRegions = []) {
    return {
        subTab: 'budget', // 6 Sub-Tab State: 'budget', 'key_product', 'country', 'regional', 'download', 'upload'
        monthNames: ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'],
        
        filters: {
            channel: 'EXPORT',
            search: '',
            country: '',
            regional: ''
        },

        items: [],
        filteredItems: [],
        keyProductsSummary: [],
        countryDetails: [],
        regionalSummaries: [],
        countrySummaries: [],

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

        initData() {
            // Data dari DB saja — tanpa fallback mock. Kosong → tampil empty-state.
            if (Array.isArray(initialProducts) && initialProducts.length > 0) {
                this.items = initialProducts.map((p, idx) => this.transformDbProduct(p, idx + 1));
            }

            if (Array.isArray(initialCountries) && initialCountries.length > 0) {
                this.countryDetails = initialCountries.map((c, idx) => this.transformDbCountry(c, idx + 1));

                const volMap = {};
                this.countryDetails.forEach(c => {
                    volMap[c.country] = (volMap[c.country] || 0) + Number(c.total_qty || 0);
                });
                this.countrySummaries = Object.entries(volMap)
                    .map(([country, total_volume]) => ({ country, total_volume }))
                    .sort((a, b) => b.total_volume - a.total_volume);
            }

            if (Array.isArray(initialRegions) && initialRegions.length > 0) {
                this.regionalSummaries = initialRegions.map(r => this.transformDbRegion(r));
            }

            this.applyFilters();
        },

        transformDbCountry(c, id) {
            const monthly = {};
            const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            for (let m = 1; m <= 12; m++) {
                const mk = months[m - 1];
                monthly[m] = { qty: Number(c[`${mk}_qty`] || 0), revenue: Number(c[`${mk}_rev`] || 0) };
            }
            return {
                id,
                region: c.region || '',
                country: c.country || '',
                currency: c.currency || 'USD',
                id_inv: c.id_inv || '',
                product_name: c.product_name || '',
                div: c.div || '',
                key_product: c.key_product || '',
                monthly,
                total_qty: Number(c.total_qty || 0),
                total_revenue: Number(c.total_rev || 0)
            };
        },

        transformDbRegion(r) {
            const monthly = {};
            const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            for (let m = 1; m <= 12; m++) {
                const mk = months[m - 1];
                monthly[m] = { qty: Number(r[`${mk}_qty`] || 0), revenue: Number(r[`${mk}_rev`] || 0) };
            }
            return {
                region: r.region || '',
                monthly,
                total_qty: Number(r.total_qty || 0),
                total_revenue: Number(r.total_rev || 0)
            };
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
                id_channel: 'EXPORT',
                code_inv_1: p.mid_product || ('EXP-' + id),
                key_product: p.key_product || 'GUMMY',
                code_inv_2: p.mid_product || ('EXP-' + id),
                product_name: p.product_name || ('Export Item ' + id),
                monthly: monthly,
                total_qty: 0,
                total_revenue: 0
            };
            this.recalculateRow(item);
            return item;
        },

        generateMockItems() {
            const mockList = [
                { key_product: 'GUMMY EXPORT', code: 'EXP-01', name: 'Yupi Gummy Bear Export 15kg Box USA', qty: 500, rev: 12250 },
                { key_product: 'GUMMY EXPORT', code: 'EXP-02', name: 'Yupi Neon Stix Export Thailand Pack', qty: 650, rev: 15600 },
                { key_product: 'MARSHMALLOW', code: 'EXP-03', name: 'Yupi Marshmallow Choco 10kg Export MY', qty: 400, rev: 9200 },
                { key_product: 'BOLI EXPORT', code: 'EXP-04', name: 'Yupi Boli Gummy Taiwan Grade 12kg', qty: 800, rev: 18400 },
                { key_product: 'EXTR EXPORT', code: 'EXP-05', name: 'Yupi Festive Gift Box Limited USD', qty: 300, rev: 7500 }
            ];

            return mockList.map((m, idx) => {
                const monthly = {};
                for (let i = 1; i <= 12; i++) {
                    monthly[i] = { qty: m.qty, revenue: m.rev };
                }
                const item = {
                    id: idx + 1,
                    id_channel: 'EXPORT',
                    code_inv_1: m.code,
                    key_product: m.key_product,
                    code_inv_2: m.code + '-US',
                    product_name: m.name,
                    monthly: monthly,
                    total_qty: 0,
                    total_revenue: 0
                };
                this.recalculateRow(item);
                return item;
            });
        },

        generateMockCountryDetails() {
            const countries = [
                { region: 'AMERICAS', country: 'USA', currency: 'USD', code: 'EXP-01', name: 'Yupi Gummy Bear Export 15kg Box USA', div: 'EXP', key: 'GUMMY' },
                { region: 'ASIA PACIFIC', country: 'Thailand', currency: 'THB', code: 'EXP-02', name: 'Yupi Neon Stix Export Thailand Pack', div: 'EXP', key: 'GUMMY' },
                { region: 'ASIA PACIFIC', country: 'Malaysia', currency: 'MYR', code: 'EXP-03', name: 'Yupi Marshmallow Choco 10kg Export MY', div: 'EXP', key: 'MARSHMALLOW' },
                { region: 'ASIA PACIFIC', country: 'Taiwan', currency: 'USD', code: 'EXP-04', name: 'Yupi Boli Gummy Taiwan Grade 12kg', div: 'EXP', key: 'BOLI' }
            ];

            return countries.map((c, idx) => {
                const monthly = {};
                let totQ = 0, totR = 0;
                for (let m = 1; m <= 12; m++) {
                    const q = 600 * (idx + 1);
                    const r = 14000 * (idx + 1);
                    monthly[m] = { qty: q, revenue: r };
                    totQ += q;
                    totR += r;
                }
                return {
                    id: idx + 1,
                    region: c.region,
                    country: c.country,
                    currency: c.currency,
                    id_inv: c.code,
                    product_name: c.name,
                    div: c.div,
                    key_product: c.key,
                    monthly: monthly,
                    total_qty: totQ,
                    total_revenue: totR
                };
            });
        },

        generateMockRegionalSummaries() {
            const regions = ['ASIA PACIFIC', 'AMERICAS', 'EUROPE'];
            return regions.map(rName => {
                const mInit = {};
                let totQ = 0, totR = 0;
                for (let m = 1; m <= 12; m++) {
                    const q = 1200000;
                    const r = 3200000;
                    mInit[m] = { qty: q, revenue: r };
                    totQ += q;
                    totR += r;
                }
                return {
                    region: rName,
                    monthly: mInit,
                    total_qty: totQ,
                    total_revenue: totR
                };
            });
        },

        generateMockCountrySummaries() {
            return [
                { country: 'Thailand', total_volume: 4025592.45 },
                { country: 'Malaysia', total_volume: 1758585.98 },
                { country: 'USA', total_volume: 1074039.23 },
                { country: 'Taiwan', total_volume: 1059019.30 },
                { country: 'Dubai (UAE)', total_volume: 829502.00 },
                { country: 'United Kingdom', total_volume: 455800.33 },
                { country: 'Singapore', total_volume: 261759.40 },
                { country: 'China', total_volume: 239738.97 }
            ];
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
            const q = (this.filters.search || '').toLowerCase();

            this.filteredItems = this.items.filter(item => {
                const matchSearch = !q || 
                    (item.product_name || item.name_product || '').toLowerCase().includes(q) ||
                    (item.code_inv_1 || '').toLowerCase().includes(q) ||
                    (item.key_product || '').toLowerCase().includes(q);
                return matchSearch;
            });

            this.calculateKeyProducts();
            this.calculateTotals();
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
            const kpMap = {};
            this.items.forEach(item => {
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

        // ASP for Export: Revenue / Qty (without * 1000)
        calculateASP(revenue, qty) {
            return qty > 0 ? (revenue / qty) : 0;
        },

        formatNumber(val) {
            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(val || 0);
        },

        formatValas(val) {
            return '$ ' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0);
        },

        downloadExportTemplate(type = 'VOL') {
            if (window.ypToast) {
                window.ypToast.info('Mengunduh template export ' + type + '...');
            }
            window.open(`<?= base_url('sales/export_template_export') ?>/EXPORTV2?type=${type}`, '_blank');
        },

        fetchCountryData() {
            const country = this.filters.country || '';
            if (window.ypToast) window.ypToast.info('Memfilter data country ' + (country || 'ALL') + '...');
            // Country data sudah di-load dari DB initial; filter lokal saja
            if (country) {
                this.countryDetails = this.countryDetails.filter(c => c.country === country);
            } else {
                // Reload dari initial
                location.reload();
            }
        },

        exportCountryExcel() {
            window.open('<?= base_url('sales/exportCountryExcel') ?>', '_blank');
        },

        fetchRegionalData() {
            const regional = this.filters.regional || '';
            if (window.ypToast) window.ypToast.info('Memfilter data regional ' + (regional || 'ALL') + '...');
            // Regional summary sudah di-load; filter lokal
            if (regional) {
                this.regionalSummaries = this.regionalSummaries.filter(r => r.region === regional);
            } else {
                location.reload();
            }
        },

        /**
         * Simpan seluruh data budget export ke backend (batch upsert).
         */
        saveChanges() {
            if (this.items.length === 0) {
                if (window.ypToast) window.ypToast.error('Tidak ada data untuk disimpan.');
                return;
            }

            const payload = this.items.map(item => ({
                id_inv: item.code_inv_1,
                monthly: item.monthly
            }));

            if (window.ypToast) window.ypToast.info('Menyimpan data budget export...');

            fetch('<?= base_url('sales/saveExportEntry') ?>', {
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
         * Upload file Excel export via form data.
         */
        uploadFile(fileInput) {
            const file = fileInput?.files?.[0];
            if (!file) {
                if (window.ypToast) window.ypToast.error('Pilih file terlebih dahulu.');
                return;
            }

            const formData = new FormData();
            formData.append('excel_file', file);

            if (window.ypToast) window.ypToast.info('Mengupload file Sales Export...');

            fetch('<?= base_url('sales/uploadExport') ?>', {
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
            this.$dispatch('open-upload-modal', { type: 'export' });
        },

        processSummarySKU() {
            if (confirm('Yakin ingin memproses summary SKU Sales International Export? Data summary akan di-reagregasi.')) {
                if (window.ypToast) window.ypToast.info('Memproses Summary SKU Export...');

                fetch('<?= base_url('sales/proses_summary_export') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify({})
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        if (window.ypToast) window.ypToast.success(res.message || 'Proses summary SKU Export selesai');
                        location.reload();
                    } else {
                        if (window.ypToast) window.ypToast.error(res.message || 'Gagal memproses summary SKU Export');
                    }
                })
                .catch(err => {
                    console.error(err);
                    if (window.ypToast) window.ypToast.error('Error: ' + err.message);
                });
            }
        }
    };
}
</script>
<?= $this->endSection() ?>