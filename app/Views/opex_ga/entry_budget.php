<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="opexGaEntryApp()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="hover:text-[#2F3185]">OPEX GA</span>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold">Entry Budget</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Entry / Update Budget OPEX GA
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Pengelolaan dan perincian input anggaran OPEX General & Administrative.
            </p>
        </div>
    </div>

    <!-- TAB NAV -->
    <div class="nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="OPEX GA Entry Tabs">
            <button
                type="button"
                @click="activeTab = 'entry'"
                :class="activeTab === 'entry' ? 'active' : ''"
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Entry / Update Budget</span>
            </button>
            <button
                type="button"
                @click="activeTab = 'view'"
                :class="activeTab === 'view' ? 'active' : ''"
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>View Data</span>
            </button>
        </nav>
    </div>

    <!-- TAB CONTENT -->
    <div>
        <div class="space-y-6">
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
