<div x-data="opexSellingEntryTab()" x-init="init()" class="space-y-6">

    <div class="rounded-2xl border border-amber-200/80 bg-amber-50/70 p-4 dark:border-amber-900/50 dark:bg-amber-950/30">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 text-amber-600 dark:text-amber-400">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h4 class="font-bold text-amber-900 dark:text-amber-200 text-xs uppercase tracking-wider mb-1">Pemberitahuan penting!</h4>
                <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                    Pastikan setelah Anda menginputkan atau mengubah data pada entri budget, silakan periksa kembali kalkulasi totalnya sebelum berpindah ke modul lain.
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">
            Cost Center
        </label>

        <div class="relative w-full md:w-1/2 lg:w-1/3">
            <div
                @click="dropdownOpen = !dropdownOpen"
                class="flex w-full items-center justify-between rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2 text-xs text-gray-800 cursor-pointer dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-all focus:border-brand-500"
            >
                <span x-text="selectedCostCenter ? costCenters.find(c => c.id === selectedCostCenter)?.name || selectedCostCenter : 'Pilih Cost Center'"></span>
                <i class="fa-solid fa-chevron-down text-[10px] text-gray-400"></i>
            </div>

            <div
                x-show="dropdownOpen"
                @click.outside="dropdownOpen = false"
                x-cloak
                class="absolute left-0 top-full z-40 mt-1 w-full rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800 overflow-hidden"
            >
                <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                    <input
                        type="text"
                        x-model="searchCostCenter"
                        placeholder="Cari Cost Center..."
                        class="w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3 py-1.5 text-xs focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    >
                </div>
                <ul class="max-h-48 overflow-y-auto text-xs p-1">
                    <template x-for="item in costCenters.filter(c => c.name.toLowerCase().includes(searchCostCenter.toLowerCase()))" :key="item.id">
                        <li
                            @click="selectCostCenter(item)"
                            class="cursor-pointer px-3 py-2 rounded-lg hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-500/10 dark:hover:text-brand-400 transition-colors"
                            :class="selectedCostCenter === item.id ? 'bg-brand-50 text-brand-600 font-bold dark:bg-brand-500/10 dark:text-brand-400' : 'text-gray-700 dark:text-gray-300'"
                            x-text="item.name"
                        ></li>
                    </template>
                </ul>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs overflow-hidden dark:border-gray-800 dark:bg-gray-900">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300 border-collapse">
                <thead>
                    <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold capitalize tracking-normal text-gray-500 dark:text-gray-400">
                        <th rowspan="2" class="border-r border-gray-200/80 dark:border-gray-800 py-3 px-3 text-center w-20">Entry</th>
                        <th rowspan="2" class="border-r border-gray-200/80 dark:border-gray-800 py-3 px-3 min-w-[240px]">Selling Expense</th>
                        <th colspan="8" class="border-r border-gray-200/80 dark:border-gray-800 py-2 px-3 text-center">Actual</th>
                        <th rowspan="2" class="py-3 px-3 text-right min-w-[110px]">Total</th>
                    </tr>
                    <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold text-gray-500 dark:text-gray-400">
                        <template x-for="m in actualMonths" :key="m">
                            <th class="border-r border-gray-200/80 dark:border-gray-800 py-2 px-2 text-right uppercase" x-text="m"></th>
                        </template>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                    <template x-if="entries.length === 0">
                        <tr>
                            <td colspan="11" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-cart-shopping text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data OPEX Selling</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Pilih Cost Center pada menu di atas untuk menampilkan data akun anggaran.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-for="row in paginatedEntries" :key="row.id_cost_header + '_' + row.cost_center_header">
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-3 text-center">
                                <a
                                    :href="'<?= base_url('opex-selling/entry-budget-detail') ?>?header=' + encodeURIComponent(row.cost_center_header) + '&dept=' + encodeURIComponent(selectedCostCenter) + '&idx=' + encodeURIComponent(row.id_cost_header)"
                                    title="Entry"
                                    :class="row.indicator === 'sudah' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-brand-500 hover:bg-brand-600'"
                                    class="h-8 w-8 rounded-lg inline-flex items-center justify-center text-white shadow-2xs active:scale-[0.98] transition-all"
                                >
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                            </td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-3 font-medium text-gray-800 dark:text-gray-200" x-text="row.cost_center_header"></td>
                            <template x-for="m in actualMonthKeys" :key="m">
                                <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtBudget(row[m])"></td>
                            </template>
                            <td class="py-2.5 px-3 text-right font-bold text-gray-900 dark:text-white font-mono" x-text="fmtBudget(row.total)"></td>
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <template x-if="entries.length > 0">
                        <tr class="bg-gray-50 dark:bg-gray-800/60 font-bold text-gray-900 dark:text-white border-t border-gray-200 dark:border-gray-700 text-xs">
                            <td colspan="2" class="border-r border-gray-200 dark:border-gray-700 py-3 px-3 uppercase">TOTAL</td>
                            <template x-for="m in actualMonthKeys" :key="'ft_' + m">
                                <td class="border-r border-gray-200 dark:border-gray-700 py-3 px-2 text-right font-mono" x-text="fmtBudget(columnTotal(m))"></td>
                            </template>
                            <td class="py-3 px-3 text-right font-mono" x-text="fmtBudget(grandTotal())"></td>
                        </tr>
                    </template>
                </tfoot>
            </table>
        </div>

        <template x-if="entries.length > 0">
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 text-xs text-gray-500 dark:text-gray-400">
                <span>
                    Showing <span class="font-bold text-gray-800 dark:text-gray-200" x-text="((currentPage - 1) * perPage) + 1"></span> to
                    <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, totalRows)"></span> of
                    <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalRows"></span> entries
                </span>
                <div class="flex items-center gap-1" x-show="totalPages > 1">
                    <button type="button" @click="goPage(currentPage - 1)" :disabled="currentPage === 1"
                        class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </button>
                    <template x-for="(p, pi) in pageNumbers()" :key="'pg_' + pi">
                        <span x-show="p === '...'" class="px-1.5 text-gray-400">&hellip;</span>
                        <button x-show="p !== '...'" type="button" @click="goPage(p)"
                            :class="currentPage === p ? 'bg-brand-500 text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            class="h-8 min-w-[32px] px-2 rounded-lg text-xs font-semibold transition" x-text="p"></button>
                    </template>
                    <button type="button" @click="goPage(currentPage + 1)" :disabled="currentPage === totalPages"
                        class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function opexSellingEntryTab() {
    return {
        searchCostCenter: '',
        dropdownOpen: false,
        costCenters: <?= json_encode(array_map(fn($cc) => [
            'id'   => $cc['cost_center_sap'],
            'name' => ($cc['cost_center_sap'] ?? $cc['cost_center']) . ' - ' . $cc['cost_desc'],
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
            fetch(`<?= base_url('opex-selling/getEntryDataGrouped') ?>?dept=${encodeURIComponent(this.selectedCostCenter)}`)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        this.currentPage = 1;
                        this.entries = (res.rows || []).map(r => ({
                            cost_center_header: r.cost_center_header,
                            id_cost_header: r.id_cost_header,
                            jan: parseFloat(r.jan) || 0,
                            feb: parseFloat(r.feb) || 0,
                            mar: parseFloat(r.mar) || 0,
                            apr: parseFloat(r.apr) || 0,
                            may: parseFloat(r.may) || 0,
                            jun: parseFloat(r.jun) || 0,
                            jul: parseFloat(r.jul) || 0,
                            aug: parseFloat(r.aug) || 0,
                            total: parseFloat(r.total) || 0,
                            indicator: r.indicator || 'belum'
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
