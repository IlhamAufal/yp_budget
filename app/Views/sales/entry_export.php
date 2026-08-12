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
                        2.2 Sales International Entry
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800">
                            USD ($) · FY <?= esc($workingYear) ?>
                        </span>
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Pengelolaan target dan laporan Sales International per Negara (Valas USD $).
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- ============================================================ -->
    <!-- 2. SUB-TABS NAVIGATION BAR (6 SUB-TABS) -->
    <!-- ============================================================ -->
    <div class="bg-gray-100/80 dark:bg-gray-800/60 p-1.5 rounded-2xl border border-gray-200/80 dark:border-gray-700/60 shadow-xs">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="International Sub Tabs">
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
    <!-- SUB-TAB CONTENT (partials) -->
    <!-- ============================================================ -->
    <div x-show="subTab === 'budget'" x-cloak><?= $this->include('sales/partials/international/tab_budget') ?></div>
    <div x-show="subTab === 'key_product'" x-cloak><?= $this->include('sales/partials/international/tab_key_product') ?></div>
    <div x-show="subTab === 'country'" x-cloak><?= $this->include('sales/partials/international/tab_country') ?></div>
    <div x-show="subTab === 'regional'" x-cloak><?= $this->include('sales/partials/international/tab_regional') ?></div>
    <div x-show="subTab === 'download'" x-cloak><?= $this->include('sales/partials/international/tab_download') ?></div>
    <div x-show="subTab === 'upload'" x-cloak><?= $this->include('sales/partials/international/tab_upload') ?></div>

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
        domesticMetricCols: (() => {
            const columns = [];
            for (let m = 1; m <= 12; m++) columns.push({ m, k: 'qty' }, { m, k: 'rev' }, { m, k: 'asp' });
            return columns;
        })(),
        domesticBudgetItems: [],
        domesticBudgetRows: [],
        domesticBudgetGroups: [],
        domesticBudgetGrandTotal: { monthly: {}, total_qty: 0, total_rev: 0, total_asp: 0 },
        domesticBudgetFilters: { channel: 'ALL', search: '' },
        domesticBudgetLoading: false,
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
            this.loadDomesticBudget();
        },

        loadDomesticBudget() {
            this.domesticBudgetLoading = true;
            const channel = this.domesticBudgetFilters.channel === 'ALL' ? '' : this.domesticBudgetFilters.channel;

            fetch(`<?= base_url('sales/cari_domestic_sales') ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({
                    year: <?= json_encode($workingYear) ?>,
                    dept: channel
                })
            })
                .then(response => response.json().then(data => ({ ok: response.ok, data })))
                .then(({ ok, data }) => {
                    if (!ok || data.status !== 'success') {
                        throw new Error(data.message || 'Data Sales Domestic gagal dimuat.');
                    }
                    this.domesticBudgetItems = (data.data || []).map((row, index) => this.transformDomesticBudgetRow(row, index + 1));
                    this.applyDomesticBudgetFilters();
                })
                .catch(error => {
                    console.error(error);
                    this.domesticBudgetItems = [];
                    this.applyDomesticBudgetFilters();
                    if (window.ypToast) window.ypToast.error(error.message || 'Data Sales Domestic gagal dimuat.');
                })
                .finally(() => { this.domesticBudgetLoading = false; });
        },

        transformDomesticBudgetRow(row, rowNo) {
            const monthly = {};
            const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            for (let m = 1; m <= 12; m++) {
                const key = months[m - 1];
                const qty = Number(row[`${key}_qty`] || 0);
                const revenue = Number(row[`${key}_rev`] || 0);
                monthly[m] = {
                    qty,
                    revenue,
                    asp: Number(row[`${key}_asp`] || (qty > 0 ? (revenue / qty) * 1000 : 0))
                };
            }
            const totalQty = Number(row.total_qty || 0);
            const totalRev = Number(row.total_rev || 0);
            return {
                row_no: rowNo,
                id_channel: row.id_channel || '',
                key_product: row.key_product || '',
                mid_product: row.mid_product || '',
                product_name: row.product_name || '',
                monthly,
                total_qty: totalQty,
                total_rev: totalRev,
                total_asp: Number(row.total_asp || (totalQty > 0 ? (totalRev / totalQty) * 1000 : 0))
            };
        },

        applyDomesticBudgetFilters() {
            const query = (this.domesticBudgetFilters.search || '').trim().toLowerCase();
            const filtered = this.domesticBudgetItems.filter(item => {
                if (!query) return true;
                return [item.id_channel, item.key_product, item.mid_product, item.product_name]
                    .join(' ').toLowerCase().includes(query);
            });

            const groups = {};
            const grandMonthly = {};
            for (let m = 1; m <= 12; m++) grandMonthly[m] = { qty: 0, revenue: 0 };

            filtered.forEach((item, index) => {
                item.row_no = index + 1;
                const channel = item.id_channel || 'OTHER';
                if (!groups[channel]) {
                    const monthly = {};
                    for (let m = 1; m <= 12; m++) monthly[m] = { qty: 0, revenue: 0 };
                    groups[channel] = { channel, items: [], monthly, total_qty: 0, total_rev: 0 };
                }
                const group = groups[channel];
                group.items.push(item);
                group.total_qty += item.total_qty;
                group.total_rev += item.total_rev;
                for (let m = 1; m <= 12; m++) {
                    group.monthly[m].qty += item.monthly[m].qty;
                    group.monthly[m].revenue += item.monthly[m].revenue;
                    grandMonthly[m].qty += item.monthly[m].qty;
                    grandMonthly[m].revenue += item.monthly[m].revenue;
                }
            });

            const groupList = Object.values(groups);
            const grandTotal = { monthly: grandMonthly, total_qty: 0, total_rev: 0, total_asp: 0 };
            groupList.forEach(group => {
                grandTotal.total_qty += group.total_qty;
                grandTotal.total_rev += group.total_rev;
            });
            grandTotal.total_asp = grandTotal.total_qty > 0 ? (grandTotal.total_rev / grandTotal.total_qty) * 1000 : 0;

            this.domesticBudgetGroups = groupList;
            this.domesticBudgetRows = [];
            groupList.forEach(group => {
                this.domesticBudgetRows.push({ kind: 'subtotal', group });
                group.items.forEach(item => this.domesticBudgetRows.push({ kind: 'item', group, item }));
            });
            this.domesticBudgetGrandTotal = grandTotal;
        },

        formatDomesticASP(revenue, qty) {
            return this.formatNumber(qty > 0 ? (revenue / qty) * 1000 : 0);
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
                window.ypToast.info('Mengunduh template international ' + type + '...');
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
         * Simpan seluruh data budget international ke backend (batch upsert).
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

            if (window.ypToast) window.ypToast.info('Menyimpan data budget international...');

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

            if (window.ypToast) window.ypToast.info('Mengupload file Sales International...');

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
            if (confirm('Yakin ingin memproses summary SKU Sales International? Data summary akan di-reagregasi.')) {
                if (window.ypToast) window.ypToast.info('Memproses Summary SKU International...');

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
                        if (window.ypToast) window.ypToast.success(res.message || 'Proses summary SKU International selesai');
                        location.reload();
                    } else {
                        if (window.ypToast) window.ypToast.error(res.message || 'Gagal memproses summary SKU International');
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