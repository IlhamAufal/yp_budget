<div x-data="opexSellingEntryTab()" x-init="init()">

    <div class="mb-6 rounded-sm border-l-6 border-warning bg-warning/10 p-4 dark:bg-warning/20">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 text-warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h4 class="font-bold text-black dark:text-white text-sm mb-1">Pemberitahuan penting!</h4>
                <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                    Pastikan setelah Anda menginputkan atau mengubah data pada entri budget, silakan periksa kembali kalkulasi totalnya sebelum berpindah ke modul lain.
                </p>
            </div>
        </div>
    </div>

    <div class="mb-6 rounded-sm border border-stroke bg-gray-2 p-4 dark:border-strokedark dark:bg-meta-4">
        <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-black dark:text-white">
            Cost Center
        </label>

        <div class="relative w-full md:w-1/2 lg:w-1/3">
            <div
                @click="dropdownOpen = !dropdownOpen"
                class="flex w-full items-center justify-between rounded border border-stroke bg-white px-4 py-2 text-sm text-black cursor-pointer dark:border-strokedark dark:bg-boxdark dark:text-white"
            >
                <span x-text="selectedCostCenter ? costCenters.find(c => c.id === selectedCostCenter)?.name || selectedCostCenter : 'Pilih Cost Center'"></span>
                <i class="fas fa-chevron-down text-xs"></i>
            </div>

            <div
                x-show="dropdownOpen"
                @click.outside="dropdownOpen = false"
                x-cloak
                class="absolute left-0 top-full z-40 mt-1 w-full rounded border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
            >
                <div class="p-2">
                    <input
                        type="text"
                        x-model="searchCostCenter"
                        placeholder="Cari Cost Center..."
                        class="w-full rounded border border-stroke bg-gray-50 px-3 py-1.5 text-sm focus:border-primary focus:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white"
                    >
                </div>
                <ul class="max-h-48 overflow-y-auto text-xs">
                    <template x-for="item in costCenters.filter(c => c.name.toLowerCase().includes(searchCostCenter.toLowerCase()))" :key="item.id">
                        <li
                            @click="selectCostCenter(item)"
                            class="cursor-pointer px-4 py-2 hover:bg-primary hover:text-white"
                            :class="selectedCostCenter === item.id ? 'bg-primary/10 text-primary font-bold' : 'text-black dark:text-white'"
                            x-text="item.name"
                        ></li>
                    </template>
                </ul>
            </div>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto border border-stroke dark:border-strokedark rounded-sm">
        <table class="w-full table-auto text-left text-xs">
            <thead>
                <tr class="bg-gray-2 text-black dark:bg-meta-4 dark:text-white font-bold border-b border-stroke dark:border-strokedark uppercase">
                    <th rowspan="2" class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[60px]">ENTRY</th>
                    <th rowspan="2" class="py-3 px-3 border-r border-stroke dark:border-strokedark min-w-[240px]">DESCRIPTION</th>
                    <th colspan="8" class="py-2 px-3 text-center border-r border-stroke dark:border-strokedark">ACTUAL</th>
                    <th rowspan="2" class="py-3 px-3 text-right min-w-[110px]">TOTAL</th>
                </tr>
                <tr class="bg-gray-2 text-black dark:bg-meta-4 dark:text-white font-bold border-b border-stroke dark:border-strokedark uppercase">
                    <template x-for="m in actualMonths" :key="m">
                        <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark min-w-[70px]" x-text="m"></th>
                    </template>
                </tr>
            </thead>
            <tbody>
                <template x-if="entries.length === 0">
                    <tr>
                        <td colspan="11" class="border-b py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox mr-1"></i>No data available in table
                        </td>
                    </tr>
                </template>
                <template x-for="(row, idx) in paginatedEntries" :key="idx">
                    <tr class="border-b border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark">
                            <a
                                :href="'<?= base_url('opex-selling/entry-budget-detail') ?>?header=' + encodeURIComponent(row.main_account) + '&dept=' + encodeURIComponent(selectedCostCenter) + '&idx=' + (idx + 1)"
                                title="Entry"
                                class="inline-flex items-center justify-center rounded bg-blue-600 p-1.5 text-white shadow hover:bg-blue-700 active:scale-[0.98] transition-all"
                            >
                                <i class="fas fa-edit text-[11px]"></i>
                            </a>
                        </td>
                        <td class="py-2.5 px-3 border-r border-stroke dark:border-strokedark font-medium text-black dark:text-white" x-text="row.description"></td>
                        <template x-for="m in actualMonthKeys" :key="m">
                            <td class="py-2.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtBudget(row[m])"></td>
                        </template>
                        <td class="py-2.5 px-3 text-right font-bold text-black dark:text-white" x-text="fmtBudget(row.total)"></td>
                    </tr>
                </template>
            </tbody>
            <tfoot>
                <template x-if="entries.length > 0">
                    <tr class="bg-gray-2 dark:bg-meta-4 font-bold text-black dark:text-white">
                        <td colspan="2" class="py-3 px-3 border-t border-r border-stroke dark:border-strokedark uppercase">TOTAL</td>
                        <template x-for="m in actualMonthKeys" :key="'ft_' + m">
                            <td class="py-3 px-1 text-right border-t border-r border-stroke dark:border-strokedark font-bold" x-text="fmtBudget(columnTotal(m))"></td>
                        </template>
                        <td class="py-3 px-3 text-right border-t border-stroke dark:border-strokedark font-bold" x-text="fmtBudget(grandTotal())"></td>
                    </tr>
                </template>
            </tfoot>
        </table>
    </div>

    <template x-if="entries.length > 0">
        <div class="flex flex-wrap items-center justify-between gap-3 border border-stroke dark:border-strokedark bg-white px-4 py-3 text-xs dark:bg-boxdark">
            <span class="text-gray-600 dark:text-gray-400">
                Showing <span class="font-bold text-gray-800 dark:text-gray-200" x-text="((currentPage - 1) * perPage) + 1"></span> to
                <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, totalRows)"></span> of
                <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalRows"></span> entries
            </span>
            <div class="flex items-center gap-1" x-show="totalPages > 1">
                <button type="button" @click="goPage(currentPage - 1)" :disabled="currentPage === 1"
                    class="h-7 px-2.5 rounded-lg border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-50 transition">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </button>
                <template x-for="(p, pi) in pageNumbers()" :key="'pg_' + pi">
                    <span x-show="p === '...'" class="px-1.5 text-gray-400">&hellip;</span>
                    <button x-show="p !== '...'" type="button" @click="goPage(p)"
                        :class="currentPage === p ? 'bg-primary text-white font-bold' : 'border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition'"
                        class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
                </template>
                <button type="button" @click="goPage(currentPage + 1)" :disabled="currentPage === totalPages"
                    class="h-7 px-2.5 rounded-lg border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-50 transition">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
function opexSellingEntryTab() {
    return {
        searchCostCenter: '',
        dropdownOpen: false,
        costCenters: <?= json_encode(array_map(fn($cc) => [
            'id'   => $cc['cost_center'],
            'name' => ($cc['cc_code'] ?? $cc['cost_center']) . ' - ' . $cc['cost_desc'],
        ], $costCenters)) ?>,
        selectedCostCenter: '',

        actualMonths: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG'],
        actualMonthKeys: ['jan','feb','mar','apr','may','jun','jul','aug'],

        entries: [],

        // Pagination
        currentPage: 1,
        perPage: 10,
        get totalRows() { return this.entries.length; },
        get totalPages() { return Math.ceil(this.totalRows / this.perPage) || 1; },
        get paginatedEntries() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.entries.slice(start, start + this.perPage);
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
        },

        selectCostCenter(item) {
            this.selectedCostCenter = item.id;
            this.dropdownOpen = false;
            this.fetchEntryData();
        },

        fetchEntryData() {
            if (!this.selectedCostCenter) {
                this.entries = [];
                return;
            }
            fetch(`<?= base_url('opex-selling/getHeaderAccounts') ?>?dept=${this.selectedCostCenter}`)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        this.currentPage = 1;
                        this.entries = (res.headers || []).map(r => ({
                            main_account: r.main_account,
                            description: r.coa_name,
                            jan: r.jan, feb: r.feb, mar: r.mar, apr: r.apr,
                            may: r.may, jun: r.jun, jul: r.jul, aug: r.aug,
                            total: parseFloat(r.total_actual) || 0
                        }));
                    }
                });
        },

        columnTotal(key) {
            return this.entries.reduce((sum, r) => sum + (parseFloat(r[key]) || 0), 0);
        },

        grandTotal() {
            return this.entries.reduce((sum, r) => sum + (parseFloat(r.total) || 0), 0);
        },

        fmtBudget(val) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(val) || 0);
        },

        init() {
            if (this.costCenters.length > 0) {
                this.selectedCostCenter = this.costCenters[0].id;
                this.fetchEntryData();
            }
        }
    }
}
</script>
