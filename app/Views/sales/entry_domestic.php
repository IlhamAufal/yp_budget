<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
  $workingYear = $workingYear ?? date('Y');
  $dbProductsJson = json_encode($domesticProducts ?? []);
  $dbRegionalJson = json_encode($regionalSummary ?? []);
?>

<!-- Highcharts CDN for trend visualization -->
<script src="https://cdn.jsdelivr.net/npm/highcharts@10.3.3/highcharts.js"></script>

<div x-data="domesticSalesEntry(<?= htmlspecialchars($dbProductsJson, ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($dbRegionalJson, ENT_QUOTES, 'UTF-8') ?>)" x-init="initData()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <div class="flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <span class="text-gray-400">/</span>
                <a href="<?= base_url('sales') ?>" class="hover:text-[#2F3185] transition-colors">Sales</a>
                <span class="text-gray-400">/</span>
                <span class="text-[#2F3185] dark:text-indigo-400 font-semibold">Sales Domestic Entry</span>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">2.1 Sales Domestic Entry</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pengelolaan target dan laporan Sales Domestic 12 Bulan (Budget, Key Product, Regional, Download & Upload).</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 text-xs"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span><span class="text-gray-500 dark:text-gray-400 font-medium">Tot Qty:</span><span class="font-mono font-bold text-xs text-gray-900 dark:text-white" x-text="formatNumber(kpi.totalQty)">0</span><span class="text-xs text-gray-400 font-mono">Kg</span></div>
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 text-xs"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span><span class="text-gray-500 dark:text-gray-400 font-medium">Tot Rev:</span><span class="font-mono font-bold text-xs text-emerald-600 dark:text-emerald-400" x-text="formatCurrency(kpi.totalRev)">Rp 0</span></div>
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 text-xs"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span><span class="text-gray-500 dark:text-gray-400 font-medium">Avg ASP:</span><span class="font-mono font-bold text-xs text-amber-600 dark:text-amber-400" x-text="formatNumber(kpi.avgAsp)">0</span><span class="text-xs text-gray-400 font-mono">Rp/kg</span></div>
        </div>
    </div>

    <!-- Sub Tabs Navigation -->
    <div class="nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="Domestic Sub Tabs">
            <button type="button" @click="setSubTab('budget')" :class="subTab === 'budget' ? 'active' : ''" class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Sales Domestic - Budget</span>
                <span class="tab-badge px-2 py-0.5 rounded-full text-[10px] font-bold" x-text="filteredItems.length">0</span>
            </button>
            <button type="button" @click="setSubTab('key_product')" :class="subTab === 'key_product' ? 'active' : ''" class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Report Key Product</span>
            </button>
            <button type="button" @click="setSubTab('regional')" :class="subTab === 'regional' ? 'active' : ''" class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Report Regional</span>
            </button>
            <button type="button" @click="setSubTab('download')" :class="subTab === 'download' ? 'active' : ''" class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Download Template</span>
            </button>
            <button type="button" @click="setSubTab('upload')" :class="subTab === 'upload' ? 'active' : ''" class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Upload Data</span>
            </button>
        </nav>
    </div>

    <!-- Domestic sub-tab content is kept in context-specific partials. -->
    <div x-show="subTab === 'budget'" x-cloak class="space-y-6"><?= $this->include('sales/partials/domestic/tab_budget') ?></div>
    <div x-show="subTab === 'key_product'" x-cloak class="space-y-6"><?= $this->include('sales/partials/domestic/tab_key_product') ?></div>
    <div x-show="subTab === 'regional'" x-cloak class="space-y-6"><?= $this->include('sales/partials/domestic/tab_regional') ?></div>
    <div x-show="subTab === 'download'" x-cloak class="space-y-6"><?= $this->include('sales/partials/domestic/tab_download') ?></div>
    <div x-show="subTab === 'upload'" x-cloak class="space-y-6"><?= $this->include('sales/partials/domestic/tab_upload') ?></div>
</div>

<!-- ============================================================ -->
<!-- 3. ALPINE.JS CONTROLLER & HIGHCHARTS SCRIPT -->
<!-- ============================================================ -->
<script>
function domesticSalesEntry(initialProducts = [], initialRegional = []) {
    return {
        subTab: 'budget',
        showChart: false,
        chartMetric: 'revenue',
        selectedDownloadChannel: 'GT',
        monthNames: ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'],
        metricCols: (() => {
            const arr = [];
            for (let m = 1; m <= 12; m++) arr.push({ m, k: 'qty' }, { m, k: 'rev' }, { m, k: 'asp' });
            return arr;
        })(),
        filters: { channel: '', search: '', channelKey: '', regional: '' },
        items: [],
        filteredItems: [],
        groupedItems: [],
        groupedFlatRows: [],
        currentPage: 1,
        perPage: 10,
        pageGroups: [],
        pageFlatRows: [],
        keyProductsSummary: [],
        regionalData: [],
        kpi: { totalQty: 0, totalRev: 0, avgAsp: 0 },
        grandTotal: { monthly: {}, total_qty: 0, total_revenue: 0 },

        setSubTab(tab) {
            this.subTab = tab;
            if (tab === 'budget' && this.showChart) this.$nextTick(() => this.renderChart());
        },

        initData() {
            if (Array.isArray(initialProducts) && initialProducts.length > 0) this.items = initialProducts.map((p, idx) => this.transformDbProduct(p, idx + 1));
            if (Array.isArray(initialRegional) && initialRegional.length > 0) this.regionalData = initialRegional.map((r, idx) => this.transformDbRegional(r, idx + 1));
            this.applyFilters();
        },

        transformDbProduct(p, id) {
            const monthly = {};
            const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            for (let m = 1; m <= 12; m++) {
                const mk = months[m - 1];
                monthly[m] = { qty: Number(p[`${mk}_qty`] || 0), revenue: Number(p[`${mk}_rev`] || 0) };
            }
            const item = { id, id_channel: p.id_channel || 'GT', code_inv_1: p.mid_product || ('INV-' + id), key_product: p.key_product || 'GUMMY', code_inv_2: p.mid_product || ('INV-' + id), name_product: p.product_name || ('Product Item ' + id), monthly, total_qty: 0, total_revenue: 0 };
            this.recalculateRow(item);
            return item;
        },

        transformDbRegional(r, id) {
            const monthly = {};
            const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
            for (let m = 1; m <= 12; m++) {
                const mk = months[m - 1];
                monthly[m] = { qty: Number(r[`${mk}_qty`] || 0), revenue: Number(r[`${mk}_rev`] || 0) };
            }
            return { id, region: r.region || 'WEST', country: r.country || 'INDONESIA', id_inv: r.id_inv || ('INV-REG-' + id), product_name: r.product_name || ('Regional Product ' + id), div: r.div || 'DOM', key_product: r.key_product || 'GUMMY', monthly, total_qty: Number(r.total_qty || 0), total_revenue: Number(r.total_revenue || 0) };
        },

        generateMockItems() {
            const mockList = [
                { id_channel: 'GT', key_product: 'GUMMY', code: 'INV-01', name: 'Yupi Gummy Bear 100g Box', qty: 1200, rev: 15000000 },
                { id_channel: 'GT', key_product: 'GUMMY', code: 'INV-02', name: 'Yupi Burger 120g Pouch', qty: 1500, rev: 18000000 },
                { id_channel: 'MT', key_product: 'MARSHMALLOW', code: 'INV-03', name: 'Yupi Marshmallow Choco 80g', qty: 2000, rev: 24000000 },
                { id_channel: 'MT', key_product: 'MARSHMALLOW', code: 'INV-04', name: 'Yupi Marshmallow Twist 90g', qty: 1800, rev: 21600000 },
                { id_channel: 'OEM', key_product: 'BOLI', code: 'INV-05', name: 'Yupi Boli Gummy OEM Client A', qty: 3000, rev: 36000000 },
                { id_channel: 'ECOM', key_product: 'EXTR', code: 'INV-06', name: 'Yupi Festive Gift Box Limited', qty: 800, rev: 16000000 }
            ];
            return mockList.map((m, idx) => {
                const monthly = {};
                for (let i = 1; i <= 12; i++) monthly[i] = { qty: m.qty, revenue: m.rev };
                const item = { id: idx + 1, id_channel: m.id_channel, code_inv_1: m.code, key_product: m.key_product, code_inv_2: m.code + '-A', name_product: m.name, monthly, total_qty: 0, total_revenue: 0 };
                this.recalculateRow(item);
                return item;
            });
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
                return { id: i, region: regions[i - 1], country: countries[i - 1], id_inv: 'INV-REG-0' + i, product_name: 'Yupi Gummy Regional Pack ' + i, div: 'DOM', key_product: 'GUMMY', monthly, total_qty: totQ, total_revenue: totR };
            });
        },

        recalculateRow(item) {
            let sumQty = 0, sumRev = 0;
            for (let m = 1; m <= 12; m++) { sumQty += Number(item.monthly[m]?.qty || 0); sumRev += Number(item.monthly[m]?.revenue || 0); }
            item.total_qty = sumQty;
            item.total_revenue = sumRev;
            this.calculateTotals();
        },

        applyFilters() {
            const ch = (this.filters.channel || '').toUpperCase();
            const q = (this.filters.search || '').toLowerCase();
            this.filteredItems = this.items.filter(item => {
                const matchCh = !ch || (item.id_channel || '').toUpperCase() === ch;
                const matchSearch = !q || (item.name_product || '').toLowerCase().includes(q) || (item.code_inv_1 || '').toLowerCase().includes(q) || (item.key_product || '').toLowerCase().includes(q);
                return matchCh && matchSearch;
            });
            this.groupItemsByChannel();
            const maxPage = this.totalPages();
            if (this.currentPage > maxPage) this.currentPage = maxPage;
            this.buildPageRows();
            this.calculateKeyProducts();
            this.calculateTotals();
            if (this.showChart) this.renderChart();
        },

        // ============================================================
        // PAGINATION (client-side) — tabel Budget hanya menampilkan
        // sebagian item per halaman. KPI, Grand Total, chart, dan
        // Key Product tetap dihitung dari SELURUH filteredItems.
        // ============================================================
        totalPages() {
            return Math.max(1, Math.ceil((this.filteredItems.length || 0) / this.perPage));
        },

        buildPageRows() {
            const start = (this.currentPage - 1) * this.perPage;
            const pageItems = this.filteredItems.slice(start, start + this.perPage);

            const groupsMap = {};
            pageItems.forEach(item => {
                const ch = item.id_channel || 'OTHER';
                if (!groupsMap[ch]) {
                    const initMonthly = {};
                    for (let m = 1; m <= 12; m++) initMonthly[m] = { qty: 0, revenue: 0 };
                    groupsMap[ch] = { channel: ch, items: [], subtotal: { monthly: initMonthly, total_qty: 0, total_revenue: 0 } };
                }
                groupsMap[ch].items.push(item);
                for (let m = 1; m <= 12; m++) {
                    groupsMap[ch].subtotal.monthly[m].qty += Number(item.monthly[m]?.qty || 0);
                    groupsMap[ch].subtotal.monthly[m].revenue += Number(item.monthly[m]?.revenue || 0);
                }
                groupsMap[ch].subtotal.total_qty += Number(item.total_qty || 0);
                groupsMap[ch].subtotal.total_revenue += Number(item.total_revenue || 0);
            });

            this.pageGroups = Object.values(groupsMap);
            this.pageFlatRows = [];
            this.pageGroups.forEach(g => {
                g.items.forEach((item, idx) => this.pageFlatRows.push({ kind: 'item', group: g, item, idx }));
            });
        },

        setPage(p) {
            const total = this.totalPages();
            p = parseInt(p, 10);
            if (isNaN(p) || p < 1 || p > total || p === this.currentPage) return;
            this.currentPage = p;
            this.buildPageRows();
            const wrap = document.getElementById('domesticBudgetTableWrap');
            if (wrap) wrap.scrollTop = 0;
        },

        setPerPage(n) {
            this.perPage = parseInt(n, 10) || 10;
            this.currentPage = 1;
            this.buildPageRows();
        },

        pageInfo() {
            const total = this.filteredItems.length;
            if (total === 0) return { from: 0, to: 0, total: 0 };
            const from = (this.currentPage - 1) * this.perPage + 1;
            const to = Math.min(this.currentPage * this.perPage, total);
            return { from, to, total };
        },

        getPageList() {
            const total = this.totalPages();
            const cur = this.currentPage;
            if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
            const pages = [1];
            const start = Math.max(2, cur - 1);
            const end = Math.min(total - 1, cur + 1);
            if (start > 2) pages.push('...');
            for (let p = start; p <= end; p++) pages.push(p);
            if (end < total - 1) pages.push('...');
            pages.push(total);
            return pages;
        },

        groupItemsByChannel() {
            const groupsMap = {};
            this.filteredItems.forEach(item => {
                const ch = item.id_channel || 'OTHER';
                if (!groupsMap[ch]) {
                    const initMonthly = {};
                    for (let m = 1; m <= 12; m++) initMonthly[m] = { qty: 0, revenue: 0 };
                    groupsMap[ch] = { channel: ch, items: [], subtotal: { monthly: initMonthly, total_qty: 0, total_revenue: 0 } };
                }
                groupsMap[ch].items.push(item);
                for (let m = 1; m <= 12; m++) {
                    groupsMap[ch].subtotal.monthly[m].qty += Number(item.monthly[m]?.qty || 0);
                    groupsMap[ch].subtotal.monthly[m].revenue += Number(item.monthly[m]?.revenue || 0);
                }
                groupsMap[ch].subtotal.total_qty += Number(item.total_qty || 0);
                groupsMap[ch].subtotal.total_revenue += Number(item.total_revenue || 0);
            });
            this.groupedItems = Object.values(groupsMap);
            this.groupedFlatRows = [];
            this.groupedItems.forEach(g => {
                this.groupedFlatRows.push({ kind: 'subtotal', group: g });
                g.items.forEach((item, idx) => this.groupedFlatRows.push({ kind: 'item', group: g, item, idx }));
            });
        },

        calculateTotals() {
            let totQ = 0, totR = 0;
            const initMonthly = {};
            for (let m = 1; m <= 12; m++) initMonthly[m] = { qty: 0, revenue: 0 };
            this.filteredItems.forEach(item => {
                totQ += Number(item.total_qty || 0);
                totR += Number(item.total_revenue || 0);
                for (let m = 1; m <= 12; m++) { initMonthly[m].qty += Number(item.monthly[m]?.qty || 0); initMonthly[m].revenue += Number(item.monthly[m]?.revenue || 0); }
            });
            this.kpi.totalQty = totQ;
            this.kpi.totalRev = totR;
            this.kpi.avgAsp = this.calculateASP(totR, totQ);
            this.grandTotal = { monthly: initMonthly, total_qty: totQ, total_revenue: totR };
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
                    kpMap[kpName] = { name: kpName, monthly: mInit, total_qty: 0, total_revenue: 0 };
                }
                for (let m = 1; m <= 12; m++) { kpMap[kpName].monthly[m].qty += Number(item.monthly[m]?.qty || 0); kpMap[kpName].monthly[m].revenue += Number(item.monthly[m]?.revenue || 0); }
                kpMap[kpName].total_qty += Number(item.total_qty || 0);
                kpMap[kpName].total_revenue += Number(item.total_revenue || 0);
            });
            const result = Object.values(kpMap);
            const gtMonthly = {};
            for (let m = 1; m <= 12; m++) gtMonthly[m] = { qty: 0, revenue: 0 };
            let gtQ = 0, gtR = 0;
            result.forEach(kp => {
                for (let m = 1; m <= 12; m++) { gtMonthly[m].qty += kp.monthly[m].qty; gtMonthly[m].revenue += kp.monthly[m].revenue; }
                gtQ += kp.total_qty;
                gtR += kp.total_revenue;
            });
            result.push({ name: 'GRAND TOTAL', monthly: gtMonthly, total_qty: gtQ, total_revenue: gtR });
            this.keyProductsSummary = result;
        },

        renderChart() {
            if (typeof Highcharts === 'undefined') return;
            const isRev = this.chartMetric === 'revenue';
            const seriesData = this.groupedItems.map(g => {
                const dataPoints = [];
                for (let m = 1; m <= 12; m++) dataPoints.push(isRev ? g.subtotal.monthly[m].revenue : g.subtotal.monthly[m].qty);
                return { name: 'Channel ' + g.channel, data: dataPoints };
            });
            Highcharts.chart('domesticTrendChart', {
                chart: { type: 'line', backgroundColor: 'transparent', style: { fontFamily: 'inherit' } }, title: { text: null },
                xAxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], labels: { style: { color: '#64748b', fontSize: '11px' } } },
                yAxis: { title: { text: isRev ? 'Revenue (IDR)' : 'Volume (Kg)', style: { color: '#64748b', fontSize: '11px' } }, labels: { style: { color: '#64748b', fontSize: '11px' }, formatter: function() { return isRev ? ('Rp ' + (this.value / 1000000).toFixed(0) + 'M') : (this.value + ' kg'); } } },
                legend: { itemStyle: { color: '#475569', fontSize: '12px' } }, credits: { enabled: false }, tooltip: { shared: true, pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y:,.0f}</b><br/>' }, series: seriesData
            });
        },

        calculateASP(revenue, qty, channel) {
            if (qty <= 0) return 0;
            const factor = String(channel || '') === 'YTI' ? 1 : 1000;
            return (revenue / qty) * factor;
        },
        formatNumber(val) { return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(val || 0); },
        formatCurrency(val) { return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(val || 0); },

        downloadChannelTemplate(channel = null) {
            const ch = channel || this.filters.channel || this.selectedDownloadChannel || 'GT';
            if (window.ypToast) window.ypToast.info('Mengunduh template channel ' + ch + '...');
            window.open(`<?= base_url('sales/export_template_sales') ?>/${ch}?type=VOL`, '_blank');
            setTimeout(() => window.open(`<?= base_url('sales/export_template_sales') ?>/${ch}?type=REV`, '_blank'), 1000);
        },
        downloadSingleTemplate(type = 'VOL', channel = null) {
            const ch = channel || this.filters.channel || this.selectedDownloadChannel || 'GT';
            window.open(`<?= base_url('sales/export_template_sales') ?>/${ch}?type=${type}`, '_blank');
        },
        fetchRegionalData() {
            const regional = this.filters.regional || '';
            if (window.ypToast) window.ypToast.info('Memuat data regional ' + (regional || 'ALL') + '...');
            fetch(`<?= base_url('sales/getRegionalData') ?>?regional=${encodeURIComponent(regional)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json()).then(res => {
                    if (res.status === 'success' && Array.isArray(res.data)) {
                        this.regionalData = res.data.map((r, idx) => this.transformDbRegional(r, idx + 1));
                        if (window.ypToast) window.ypToast.success('Data regional berhasil dimuat (' + res.data.length + ' baris).');
                    } else if (window.ypToast) window.ypToast.error('Gagal memuat data regional.');
                }).catch(err => { console.error(err); if (window.ypToast) window.ypToast.error('Error: ' + err.message); });
        },
        exportRegionalExcel() {
            const regional = this.filters.regional || '';
            window.open(`<?= base_url('sales/exportRegionalExcel') ?>?regional=${encodeURIComponent(regional)}`, '_blank');
        },
        saveChanges() {
            if (this.items.length === 0) { if (window.ypToast) window.ypToast.error('Tidak ada data untuk disimpan.'); return; }
            const payload = this.items.map(item => ({ id_inv: item.code_inv_1, id_channel: item.id_channel, monthly: item.monthly }));
            if (window.ypToast) window.ypToast.info('Menyimpan data budget domestic...');
            fetch('<?= base_url('sales/saveDomesticEntry') ?>', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', '<?= csrf_token() ?>': '<?= csrf_hash() ?>' }, body: JSON.stringify({ items: payload }) })
                .then(r => r.json()).then(res => { if (res.status === 'success') { if (window.ypToast) window.ypToast.success(res.message); } else if (window.ypToast) window.ypToast.error(res.message || 'Gagal menyimpan.'); })
                .catch(err => { console.error(err); if (window.ypToast) window.ypToast.error('Error: ' + err.message); });
        },
        uploadFile(fileInput) {
            const file = fileInput?.files?.[0];
            if (!file) { if (window.ypToast) window.ypToast.error('Pilih file terlebih dahulu.'); return; }
            const formData = new FormData();
            formData.append('excel_file', file);
            if (window.ypToast) window.ypToast.info('Mengupload file Sales Domestic...');
            fetch('<?= base_url('sales/uploadDomestic') ?>', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: formData })
                .then(r => r.json()).then(res => { if (res.status === 'success') { if (window.ypToast) window.ypToast.success(res.message); setTimeout(() => location.reload(), 800); } else if (window.ypToast) window.ypToast.error(res.message || 'Upload gagal.'); })
                .catch(err => { console.error(err); if (window.ypToast) window.ypToast.error('Error: ' + err.message); });
        },
        triggerUploadModal() { this.$dispatch('open-upload-modal', { type: 'domestic' }); },
        processSummarySKU() {
            if (confirm('Yakin ingin memproses summary SKU Sales Domestic? Data summary akan di-reagregasi dari data regional.')) {
                if (window.ypToast) window.ypToast.info('Memproses Summary SKU...');
                window.ypFetch('<?= base_url('sales/proses_summary_domestic') ?>', {}).then(res => {
                    if (res.success) { if (window.ypToast) window.ypToast.success(res.message || 'Proses summary SKU selesai'); location.reload(); }
                    else if (window.ypToast) window.ypToast.error(res.message || 'Gagal memproses summary SKU');
                });
            }
        }
    };
}
</script>
<?= $this->endSection() ?>