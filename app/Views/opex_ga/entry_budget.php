<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="opexGaEntryApp()" x-init="init()" class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <i class="fas fa-coins text-lg text-primary"></i>
                </div>
                <h2 class="text-title-md2 font-bold text-black dark:text-white">Entry / Update Budget OPEX GA</h2>
            </div>
            <nav class="mt-1">
                <ol class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    <li><a class="hover:text-primary" href="<?= base_url('dashboard') ?>"><i class="fas fa-home mr-1"></i>Home</a></li>
                    <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                    <li>4.1 OPEX - GA</li>
                    <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                    <li class="text-primary font-semibold">Entry Budget</li>
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
                <button @click="activeTab = 'entry'"
                    :class="activeTab === 'entry' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-2 py-2 text-sm font-medium transition-all duration-200">
                    <i class="fas fa-edit mr-1"></i>Entry / Update Budget
                </button>
                <button @click="activeTab = 'view'"
                    :class="activeTab === 'view' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-2 py-2 text-sm font-medium transition-all duration-200">
                    <i class="fas fa-eye mr-1"></i>View Data
                </button>
            </div>
        </div>

        <div class="p-6">
            <div x-show="activeTab === 'entry'" x-cloak>
                <?= $this->include('opex_ga/partials/entry_tab_form') ?>
            </div>

            <div x-show="activeTab === 'view'" x-cloak>
                <?= $this->include('opex_ga/partials/entry_tab_view') ?>
            </div>
        </div>
    </div>

    <?= $this->include('opex_ga/partials/modal_manual_book') ?>

</div>

<script>
function opexGaEntryApp() {
    return {
        activeTab: 'entry',
        manualBookModalOpen: false,
        costCenters: <?= json_encode(array_map(fn($cc) => [
            'id'   => $cc['cost_center'],
            'text' => ($cc['cc_code'] ?? $cc['cost_center']) . ' - ' . $cc['cost_desc'],
        ], $costCenters)) ?>,
        selectedEntryCc: '',
        selectedViewCc: '',

        entryAccounts: [],
        viewAccounts: [],

        init() {
            if (this.costCenters.length > 0) {
                this.selectedEntryCc = this.costCenters[0].id;
                this.selectedViewCc = this.costCenters[0].id;
            }
            this.fetchEntryData();
            this.fetchViewData();
        },

        fetchEntryData() {
            if (!this.selectedEntryCc) {
                this.entryAccounts = [];
                return;
            }
            fetch(`<?= base_url('opex-ga/getHeaderAccounts') ?>?dept=${this.selectedEntryCc}`)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        this.entryAccounts = (res.headers || []).map((h, i) => ({
                            idx: i + 1,
                            main_account: h.acct_code,
                            description: h.coa_desc,
                            total_budget: this.fmtShort(h.total_budget),
                            total_raw: parseFloat(h.total_budget) || 0
                        }));
                    }
                });
        },

        fetchViewData() {
            if (!this.selectedViewCc) {
                this.viewAccounts = [];
                return;
            }
            fetch(`<?= base_url('opex-ga/getEntryData') ?>?dept=${this.selectedViewCc}`)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        this.viewAccounts = (res.rows || []).map(r => ({
                            main_account: r.acct_code,
                            description: r.coa_desc,
                            jan: this.fmtShort(r.jan), feb: this.fmtShort(r.feb), mar: this.fmtShort(r.mar),
                            apr: this.fmtShort(r.apr), may: this.fmtShort(r.may), jun: this.fmtShort(r.jun),
                            jul: this.fmtShort(r.jul), aug: this.fmtShort(r.aug), sep: this.fmtShort(r.sep),
                            oct: this.fmtShort(r.oct), nov: this.fmtShort(r.nov), dec: this.fmtShort(r.dec),
                            total: this.fmtShort(r.total)
                        }));
                    }
                });
        },

        goToDetail(account) {
            const headerEncoded = encodeURIComponent(account.description);
            window.location.href = `<?= base_url('opex-ga/entry-budget-detail') ?>?header=${headerEncoded}&dept=${this.selectedEntryCc}&idx=${account.idx}`;
        },

        exportExcel() {
            window.location.href = `<?= base_url('opex-ga/entry-budget') ?>/exportExcel?cost_center=${this.selectedViewCc}`;
        },

        fmtShort(val) {
            const num = parseFloat(val) || 0;
            if (num === 0) return '0';
            return num.toLocaleString('id-ID');
        }
    }
}
</script>
<?= $this->endSection() ?>
