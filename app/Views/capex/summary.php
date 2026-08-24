<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="capexSummaryApp()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span>CAPEX</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-brand-500 font-bold">Summary</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 shadow-xs">
                    <i class="fa-solid fa-chart-pie text-base"></i>
                </span>
                Summary CAPEX
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Konsolidasi belanja modal (CAPEX), rincian akuisisi, dan depresiasi aset.
            </p>
        </div>
        <div class="flex items-center gap-2.5 rounded-xl border border-gray-200/80 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-xs dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <i class="fa-solid fa-calendar-days text-brand-500"></i>
            <span>Budget Plan Year :</span>
            <span class="text-brand-600 dark:text-brand-400 font-bold"><?= esc($workingYear) ?></span>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="border-b border-gray-100 dark:border-gray-800 px-6 pt-3">
            <div class="flex flex-wrap items-center gap-4">
                <button @click="activeSubTab = 'view_all'; fetchData()"
                    :class="activeSubTab === 'view_all' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-list mr-1"></i>View Capex All
                </button>
                <button @click="activeSubTab = 'acquisition'; fetchData()"
                    :class="activeSubTab === 'acquisition' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-arrow-up-right-dots mr-1"></i>Summary Jenis Asset (Acquisition)
                </button>
                <button @click="activeSubTab = 'depreciation'; fetchData()"
                    :class="activeSubTab === 'depreciation' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-arrow-trend-down mr-1"></i>Summary Jenis Asset (Depreciation)
                </button>
            </div>
        </div>

        <div class="p-5 md:p-6 space-y-6">
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
