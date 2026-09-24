<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="capexSummaryApp()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="hover:text-[#2F3185]">CAPEX</span>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold">Summary</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Summary CAPEX
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Konsolidasi belanja modal (CAPEX), rincian akuisisi, dan depresiasi aset.
            </p>
        </div>
    </div>

    <!-- Sub Tabs Navigation -->
    <div class="inline-flex max-w-full nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="CAPEX Summary Tabs">
            <button type="button" @click="activeSubTab = 'view_all'; fetchData()"
                :class="activeSubTab === 'view_all' ? 'active' : ''"
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>View CAPEX All</span>
            </button>
            <button type="button" @click="activeSubTab = 'acquisition'; fetchData()"
                :class="activeSubTab === 'acquisition' ? 'active' : ''"
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Summary Jenis Asset (Acquisition)</span>
            </button>
            <button type="button" @click="activeSubTab = 'depreciation'; fetchData()"
                :class="activeSubTab === 'depreciation' ? 'active' : ''"
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Summary Jenis Asset (Depreciation)</span>
            </button>
        </nav>
    </div>

    <div>
        <div class="space-y-6">
            <div x-show="activeSubTab === 'view_all'" x-cloak>
                <?= $this->include('capex/partials/summary_tab_view_all') ?>
            </div>

            <div x-show="activeSubTab === 'acquisition'" x-cloak>
                <?= $this->include('capex/partials/summary_tab_acquisition') ?>
            </div>

            <div x-show="activeSubTab === 'depreciation'" x-cloak>
                <?= $this->include('capex/partials/summary_tab_depreciation') ?>
            </div>
        </div>
    </div>

</div>

<script>
function capexSummaryApp() {
    return {
        activeSubTab: 'view_all',
        filterShowAll: 'ALL',
        filterShowAcq: 'ALL',
        filterShowDep: 'ALL',
        showOptions: [],
        
        viewAllData: [],
        acqData: [],
        depData: [],

        viewAllTotals: { jan:0, feb:0, mar:0, apr:0, may:0, jun:0, jul:0, aug:0, sep:0, oct:0, nov:0, dec:0, total:0 },
        acqTotals: { jan:0, feb:0, mar:0, apr:0, may:0, jun:0, jul:0, aug:0, sep:0, oct:0, nov:0, dec:0, total:0 },
        depTotals: { jan:0, feb:0, mar:0, apr:0, may:0, jun:0, jul:0, aug:0, sep:0, oct:0, nov:0, dec:0, total:0 },

        init() {
            this.fetchCostCenters();
            this.fetchData();
        },

        fetchCostCenters() {
            fetch('<?= base_url('capex/getCostCenters') ?>')
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        this.showOptions = res.options || [];
                    }
                });
        },

        fetchData() {
            const filter = this.activeSubTab === 'view_all' ? this.filterShowAll :
                           this.activeSubTab === 'acquisition' ? this.filterShowAcq : this.filterShowDep;
            
            const endpoint = this.activeSubTab === 'view_all' ? 'getSummaryViewAll' :
                            this.activeSubTab === 'acquisition' ? 'getSummaryAcquisition' : 'getSummaryDepreciation';

            fetch(`<?= base_url('capex/') ?>${endpoint}?filter_cc=${filter}`)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        const data = res.data || [];
                        if (this.activeSubTab === 'view_all') {
                            this.viewAllData = data;
                            this.viewAllTotals = this.calcTotals(data);
                        } else if (this.activeSubTab === 'acquisition') {
                            this.acqData = data;
                            this.acqTotals = this.calcTotals(data);
                        } else {
                            this.depData = data;
                            this.depTotals = this.calcTotals(data);
                        }
                    }
                });
        },

        calcTotals(data) {
            const t = { jan:0, feb:0, mar:0, apr:0, may:0, jun:0, jul:0, aug:0, sep:0, oct:0, nov:0, dec:0, total:0 };
            data.forEach(r => {
                t.jan += parseFloat(r.jan) || 0;
                t.feb += parseFloat(r.feb) || 0;
                t.mar += parseFloat(r.mar) || 0;
                t.apr += parseFloat(r.apr) || 0;
                t.may += parseFloat(r.may) || 0;
                t.jun += parseFloat(r.jun) || 0;
                t.jul += parseFloat(r.jul) || 0;
                t.aug += parseFloat(r.aug) || 0;
                t.sep += parseFloat(r.sep) || 0;
                t.oct += parseFloat(r.oct) || 0;
                t.nov += parseFloat(r.nov) || 0;
                t.dec += parseFloat(r.dec) || 0;
                t.total += parseFloat(r.total) || 0;
            });
            return t;
        },

        fmt(val) {
            const num = parseFloat(val) || 0;
            return num.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        exportExcel(tabName) {
            const filter = this.activeSubTab === 'view_all' ? this.filterShowAll :
                           this.activeSubTab === 'acquisition' ? this.filterShowAcq : this.filterShowDep;
            window.location.href = `<?= base_url('capex/exportSummaryExcel') ?>?tab=${this.activeSubTab}&filter_cc=${filter}`;
        }
    }
}
</script>
<?= $this->endSection() ?>
