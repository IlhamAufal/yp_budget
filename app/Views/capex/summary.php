<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="capexSummaryApp()" x-init="init()" class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <i class="fas fa-chart-pie text-lg text-primary"></i>
                </div>
                <h2 class="text-title-md2 font-bold text-black dark:text-white">Summary Capex</h2>
            </div>
            <nav class="mt-1">
                <ol class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    <li><a class="hover:text-primary" href="<?= base_url('dashboard') ?>"><i class="fas fa-home mr-1"></i>Home</a></li>
                    <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                    <li class="text-primary font-semibold">Summary Capex</li>
                </ol>
            </nav>
        </div>
        <div class="flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-1 dark:bg-boxdark dark:text-gray-200">
            <i class="fas fa-calendar-alt text-primary"></i>
            <span>Budget Plan Year :</span>
            <span class="text-red-500 font-bold"><?= esc($workingYear) ?></span>
        </div>
    </div>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6 py-3 dark:border-strokedark">
            <div class="flex items-center gap-4">
                <button @click="activeSubTab = 'view_all'; fetchData()"
                    :class="activeSubTab === 'view_all' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-2 py-2 text-sm font-medium transition-all duration-200">
                    <i class="fas fa-list mr-1"></i>View Capex All
                </button>
                <button @click="activeSubTab = 'acquisition'; fetchData()"
                    :class="activeSubTab === 'acquisition' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-2 py-2 text-sm font-medium transition-all duration-200">
                    <i class="fas fa-arrow-up mr-1"></i>Summary Jenis Asset (Acquisition)
                </button>
                <button @click="activeSubTab = 'depreciation'; fetchData()"
                    :class="activeSubTab === 'depreciation' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-2 py-2 text-sm font-medium transition-all duration-200">
                    <i class="fas fa-arrow-down mr-1"></i>Summary Jenis Asset (Depreciation)
                </button>
            </div>
        </div>

        <div class="p-6">
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
