<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="opexGaEntryApp()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span>OPEX GA</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-brand-500 font-bold">Entry Budget</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 shadow-xs">
                    <i class="fa-solid fa-coins text-base"></i>
                </span>
                Entry / Update Budget OPEX GA
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Pengelolaan dan perincian input anggaran OPEX General & Administrative.
            </p>
        </div>
        <div class="flex items-center gap-2.5 rounded-xl border border-gray-200/80 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-xs dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <i class="fa-solid fa-calendar-days text-brand-500"></i>
            <span>Budget Plan Year :</span>
            <span class="text-brand-600 dark:text-brand-400 font-bold"><?= esc($workingYear ?? '') ?></span>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="border-b border-gray-100 dark:border-gray-800 px-6 pt-3">
            <div class="flex items-center gap-4">
                <button @click="activeTab = 'entry'"
                    :class="activeTab === 'entry' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-pen-to-square mr-1.5"></i>Entry / Update Budget
                </button>
                <button @click="activeTab = 'view'"
                    :class="activeTab === 'view' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-eye mr-1.5"></i>View Data
                </button>
            </div>
        </div>

        <div class="p-5 md:p-6">
            <div x-show="activeTab === 'entry'" x-cloak>
                <?= $this->include('opex_ga/partials/entry_tab_form') ?>
            </div>

            <div x-show="activeTab === 'view'" x-cloak>
                <?= $this->include('opex_ga/partials/entry_tab_view') ?>
            </div>
        </div>
    </div>

    <?= $this->include('partials/manual_book_modal', [
        'mbTitle' => 'Manual Book - Input OPEX GA',
        'mbPdfUrl' => base_url('assets/docs/manual_book/MANUAL BOOK - BUDGET SYSTEM - INPUT OPEX GA.pdf'),
        'mbPdfExists' => is_file(FCPATH . 'assets/docs/manual_book/MANUAL BOOK - BUDGET SYSTEM - INPUT OPEX GA.pdf'),
    ]) ?>

</div>

<script>
function opexGaEntryApp() {
    return {
        activeTab: 'entry',
        manualBookModalOpen: false,
        costCenters: <?= json_encode(array_map(fn($cc) => [
            'id'   => $cc['cost_center_sap'] ?? $cc['cost_center'],
            'text' => ($cc['cc_code'] ?? $cc['cost_center']) . ' - ' . $cc['cost_desc'],
        ], $costCenters ?? [])) ?>,
        selectedEntryCc: '',
        selectedViewCc: '',

        actualMonths: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG'],
        actualMonthKeys: ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug'],

        entryAccounts: [],
        viewAccounts: [],
        viewSubtotals: [],
        viewGrandTotal: { actual: { months: {}, avg: 0, total: 0 }, budget: { months: {}, total: 0 } },
        isLoadingView: false,
        actualViewMonths: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','AVG','TOTAL'],
        actualViewKeys: ['jan','feb','mar','apr','may','jun','jul','aug'],
        budgetViewMonths: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC','TOTAL'],
        budgetViewKeys: ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'],

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
            fetch(`<?= base_url('opex-ga/getHeaderAccounts') ?>?dept=${encodeURIComponent(this.selectedEntryCc)}`)
                .then(r => r.json())
                .then(res => {
                    this.currentPage = 1;
                    this.entryAccounts = res.status === 'success' ? (res.headers || []).map(h => ({
                        cost_center_header: h.cost_center_header,
                        id_cost_header: h.id_cost_header,
                        actual: h.actual || {},
                        total_actual: parseFloat(h.total_actual) || 0,
                        status_entry: h.status_entry || 'belum'
                    })) : [];
                })
                .catch(() => { this.entryAccounts = []; });
        },

        fetchViewData() {
            if (!this.selectedViewCc) {
                this.viewAccounts = [];
                this.viewSubtotals = [];
                return;
            }
            this.isLoadingView = true;
            fetch(`<?= base_url('opex-ga/getEntryData') ?>?dept=${encodeURIComponent(this.selectedViewCc)}`)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        this.viewAccounts = res.rows || [];
                        this.viewSubtotals = res.subtotals || [];
                        this.viewGrandTotal = res.grand_total || this.viewGrandTotal;
                    } else {
                        this.viewAccounts = [];
                        this.viewSubtotals = [];
                    }
                })
                .catch(() => { this.viewAccounts = []; this.viewSubtotals = []; })
                .finally(() => { this.isLoadingView = false; });
        },

        detailUrl(row) {
            return `<?= base_url('opex-ga/entry-budget-detail') ?>?header=${encodeURIComponent(row.cost_center_header)}&dept=${encodeURIComponent(this.selectedEntryCc)}&idx=${encodeURIComponent(row.id_cost_header)}`;
        },

        goToDetail(account) {
            window.location.href = this.detailUrl(account);
        },

        exportExcel() {
            window.location.href = `<?= base_url('opex-ga/entry-budget') ?>/exportExcel?cost_center=${this.selectedViewCc}`;
        },

        fmtShort(val) {
            const num = parseFloat(val) || 0;
            if (num === 0) return '0';
            return num.toLocaleString('id-ID');
        },

        fmtActual(val) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(val) || 0);
        },

        formatNumber(val) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(val) || 0);
        },

        columnTotal(key) {
            return this.entryAccounts.reduce((sum, r) => sum + (parseFloat(r.actual?.[key]) || 0), 0);
        },

        grandTotal() {
            return this.entryAccounts.reduce((sum, r) => sum + (parseFloat(r.total_actual) || 0), 0);
        },

        // Pagination
        currentPage: 1,
        perPage: 10,
        get totalRows() { return this.entryAccounts.length; },
        get totalPages() { return Math.ceil(this.totalRows / this.perPage) || 1; },
        get paginatedEntries() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.entryAccounts.slice(start, start + this.perPage);
        },
        goPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
        },
        pageNumbers() {
            const pages = [];
            const total = this.totalPages;
            const current = this.currentPage;
            if (total <= 7) {
                for (let i = 1; i <= total; i++) pages.push(i);
            } else {
                pages.push(1);
                if (current > 3) pages.push('...');
                const start = Math.max(2, current - 1);
                const end = Math.min(total - 1, current + 1);
                for (let i = start; i <= end; i++) pages.push(i);
                if (current < total - 2) pages.push('...');
                pages.push(total);
            }
            return pages;
        }
    }
}
</script>
<?= $this->endSection() ?>
