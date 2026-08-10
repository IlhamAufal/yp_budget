<div x-data="opexSellingViewTab()" x-init="init()" class="space-y-6">

  <!-- Filter -->
  <div class="mb-6 rounded-sm border border-stroke bg-gray-2 p-4 dark:border-strokedark dark:bg-meta-4">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div class="w-full md:w-1/2 lg:w-1/3">
        <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-black dark:text-white">Cost Center</label>
        <div class="relative">
          <div @click="dropdownOpen = !dropdownOpen" class="flex w-full items-center justify-between rounded border border-stroke bg-white px-4 py-2 text-sm text-black cursor-pointer dark:border-strokedark dark:bg-boxdark dark:text-white">
            <span x-text="selectedCostCenter ? costCenters.find(c => c.id === selectedCostCenter)?.name || selectedCostCenter : 'Pilih Cost Center'"></span>
            <i class="fas fa-chevron-down text-xs"></i>
          </div>
          <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-cloak class="absolute left-0 top-full z-40 mt-1 w-full rounded border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="p-2">
              <input type="text" x-model="searchCostCenter" placeholder="Cari Cost Center..." class="w-full rounded border border-stroke bg-gray-50 px-3 py-1.5 text-sm focus:border-primary focus:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white">
            </div>
            <ul class="max-h-48 overflow-y-auto text-xs">
              <template x-for="item in costCenters.filter(c => c.name.toLowerCase().includes(searchCostCenter.toLowerCase()))" :key="item.id">
                <li @click="selectCostCenter(item)" class="cursor-pointer px-4 py-2 hover:bg-primary hover:text-white" :class="selectedCostCenter === item.id ? 'bg-primary/10 text-primary font-bold' : 'text-black dark:text-white'" x-text="item.name"></li>
              </template>
            </ul>
          </div>
        </div>
      </div>
      <button @click="exportData()" :disabled="!selectedCostCenter" :class="selectedCostCenter ? 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs' : 'bg-gray-200 text-gray-400 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600'" class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-semibold transition-all">
        <i class="fa-solid fa-file-excel text-xs"></i> Export Data
      </button>
    </div>
  </div>

  <!-- Table -->
  <div x-show="selectedCostCenter" x-cloak>
    <div class="max-w-full overflow-x-auto rounded-sm border border-stroke dark:border-strokedark">
      <table class="w-full table-auto text-left text-xs border-collapse min-w-[1300px]">
        <thead class="bg-gray-2 dark:bg-meta-4">
          <tr class="text-black dark:text-white font-bold uppercase border-b border-stroke dark:border-strokedark">
            <th rowspan="2" class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark w-10"></th>
            <th rowspan="2" class="py-3 px-3 border-r border-stroke dark:border-strokedark min-w-[220px]">
              <span>Selling Expense</span>
              <span class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase mt-1" x-text="ccLabel"></span>
            </th>
            <th colspan="10" class="py-2 px-2 text-center border-b border-r border-stroke dark:border-strokedark bg-blue-50/60 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300">Actual</th>
            <th rowspan="2" class="py-3 px-1 border-b border-stroke dark:border-strokedark bg-gray-900 dark:bg-gray-100 w-1.5 p-0"></th>
            <th colspan="13" class="py-2 px-2 text-center border-b border-r border-stroke dark:border-strokedark bg-green-50/60 text-green-800 dark:bg-green-950/40 dark:text-green-300">Budget</th>
          </tr>
          <tr class="bg-gray-50 text-gray-600 dark:bg-meta-4/80 dark:text-gray-400 font-bold uppercase text-[10px] border-b border-stroke dark:border-strokedark">
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">JAN</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">FEB</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">MAR</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">APR</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">MAY</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">JUN</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">JUL</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">AUG</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">AVG</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">TOTAL</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">JAN</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">FEB</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">MAR</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">APR</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">MAY</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">JUN</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">JUL</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">AUG</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">SEP</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">OCT</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">NOV</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">DEC</th>
            <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <tr x-show="viewLoading" x-cloak>
            <td colspan="26" class="py-16 text-center text-gray-500 dark:text-gray-400">
              <i class="fa-solid fa-spinner fa-spin text-2xl mb-2"></i>
              <p class="font-semibold">Memuat data...</p>
            </td>
          </tr>
          <tr x-show="!viewLoading && flatRows.length === 0" x-cloak>
            <td colspan="26" class="py-16 text-center text-gray-500 dark:text-gray-400">
              <i class="fa-solid fa-inbox mr-1"></i>No data available in table
            </td>
          </tr>

          <template x-for="(row, rIdx) in paginatedRows" :key="'r_'+rIdx">
            <tr class="hover:bg-gray-50 dark:hover:bg-meta-4 transition-colors border-b border-stroke dark:border-strokedark">
              <td class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark">
                <button type="button" @click="toggleChild(row)" :class="isChildOpen(row) ? 'bg-warning text-black' : 'bg-primary text-white hover:bg-opacity-90'" class="inline-flex h-5 w-5 items-center justify-center rounded text-[10px] font-bold transition-colors shadow">
                  <span x-text="isChildOpen(row) ? '−' : '+'"></span>
                </button>
              </td>
              <td class="py-2 px-3 border-r border-stroke dark:border-strokedark text-black dark:text-white">
                <span x-text="row.acct_code"></span> - <span x-text="row.coa_name"></span>
              </td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.actual?.a1)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.actual?.a2)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.actual?.a3)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.actual?.a4)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.actual?.a5)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.actual?.a6)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.actual?.a7)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.actual?.a8)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(avgActual(row.actual))"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono font-semibold" x-text="fmtShort(row.actual?.atotal)"></td>
              <td class="bg-gray-900 dark:bg-gray-100 w-1.5 p-0"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.budget?.b1)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.budget?.b2)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.budget?.b3)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.budget?.b4)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.budget?.b5)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.budget?.b6)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.budget?.b7)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.budget?.b8)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.budget?.b9)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.budget?.b10)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.budget?.b11)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row.budget?.b12)"></td>
              <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono font-semibold" x-text="fmtShort(row.budget?.btotal)"></td>
            </tr>
          </template>

        </tbody>
        <tfoot>
          <tr x-show="!viewLoading && flatRows.length > 0" class="bg-gray-100/80 dark:bg-meta-4 font-bold text-black dark:text-white border-t border-stroke dark:border-strokedark">
            <td colspan="2" class="py-3 px-3 border-r border-stroke dark:border-strokedark uppercase text-right">Grand Total</td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandActual(1))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandActual(2))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandActual(3))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandActual(4))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandActual(5))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandActual(6))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandActual(7))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandActual(8))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandActualAvg())"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono font-bold" x-text="fmtShort(grandActualTotal())"></td>
            <td class="bg-gray-900 dark:bg-gray-100 w-1.5 p-0"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(1))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(2))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(3))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(4))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(5))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(6))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(7))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(8))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(9))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(10))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(11))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(12))"></td>
            <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono font-bold" x-text="fmtShort(grandBudgetTotal())"></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Pagination -->
    <div x-show="!viewLoading && totalRows > 0" x-cloak class="flex flex-wrap items-center justify-between gap-3 border border-t-0 border-stroke dark:border-strokedark bg-white px-4 py-3 text-xs dark:bg-boxdark">
      <span class="text-gray-600 dark:text-gray-400">
        Showing <span class="font-bold text-gray-800 dark:text-gray-200" x-text="((currentPage - 1) * perPage) + 1"></span> to
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, totalRows)"></span> of
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalRows"></span> entries
      </span>
      <div class="flex items-center gap-1" x-show="totalPages > 1">
        <button type="button" @click="goPage(currentPage - 1)" :disabled="currentPage === 1" class="h-7 px-2.5 rounded-lg border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-50 transition">
          <i class="fas fa-chevron-left text-[10px]"></i>
        </button>
        <template x-for="(p, pi) in pageNumbers()" :key="'pg_' + pi">
          <span>
            <span x-show="p === '...'" class="px-1.5 text-gray-400">&hellip;</span>
            <button x-show="p !== '...'" type="button" @click="goPage(p)" :class="currentPage === p ? 'bg-primary text-white font-bold' : 'border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition'" class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
          </span>
        </template>
        <button type="button" @click="goPage(currentPage + 1)" :disabled="currentPage === totalPages" class="h-7 px-2.5 rounded-lg border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-50 transition">
          <i class="fas fa-chevron-right text-[10px]"></i>
        </button>
      </div>
    </div>
  </div>

  <div x-show="!selectedCostCenter" x-cloak class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-gray-200 rounded-sm dark:border-strokedark">
    <i class="fa-solid fa-chart-column text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
    <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Silakan pilih Cost Center untuk meninjau data konsolidasi View Data.</p>
  </div>
</div>

<script>
function opexSellingViewTab() {
    return {
        searchCostCenter: '',
        dropdownOpen: false,
        costCenters: <?= json_encode(array_map(fn($cc) => [
            'id'   => $cc['cost_center'],
            'name' => ($cc['cc_code'] ?? $cc['cost_center']) . ' - ' . $cc['cost_desc'],
        ], $costCenters), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        selectedCostCenter: '',

        groups: [],
        ccInfo: null,
        viewLoading: false,
        expandedChildren: [],
        childrenCache: {},
        childrenLoading: {},

        // Pagination
        currentPage: 1,
        perPage: 20,

        get ccLabel() {
            return this.ccInfo ? `[${this.ccInfo.cc_code}] ${(this.ccInfo.cost_desc || '').toUpperCase()}` : '';
        },

        get flatRows() {
            const out = [];
            for (const g of this.groups) {
                for (const item of g.items) {
                    out.push(item);
                }
            }
            return out;
        },
        get totalRows() { return this.flatRows.length; },
        get totalPages() { return Math.ceil(this.totalRows / this.perPage) || 1; },
        get paginatedRows() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.flatRows.slice(start, start + this.perPage);
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

        async toggleChild(item) {
            const key = String(item.acct_code);
            if (this.expandedChildren.includes(key)) {
                this.expandedChildren = this.expandedChildren.filter(k => k !== key);
                return;
            }
            this.expandedChildren.push(key);
            // Lazy-load children if not cached
            const entryId = item.entry_data_id;
            if (entryId > 0 && !this.childrenCache[entryId]) {
                this.childrenLoading[entryId] = true;
                try {
                    const res = await fetch(`<?= base_url('opex-selling/getDetailItems') ?>?entry_data_id=${entryId}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    this.childrenCache[entryId] = (data.status === 'success') ? (data.items || []) : [];
                } catch (e) {
                    console.error('Gagal memuat detail:', e);
                    this.childrenCache[entryId] = [];
                } finally {
                    this.childrenLoading[entryId] = false;
                }
            }
        },
        isChildOpen(item) { return this.expandedChildren.includes(String(item.acct_code)); },
        getChildren(item) { return this.childrenCache[item.entry_data_id] || []; },

        selectCostCenter(item) {
            this.selectedCostCenter = item.id;
            this.dropdownOpen = false;
            this.fetchViewData();
        },

        async fetchViewData() {
            if (!this.selectedCostCenter) { this.groups = []; return; }
            this.viewLoading = true;
            try {
                const res = await fetch(`<?= base_url('opex-selling/getViewData') ?>?dept=${encodeURIComponent(this.selectedCostCenter)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.status === 'success') {
                    this.ccInfo = data.cc || null;
                    this.groups = data.groups || [];
                    this.currentPage = 1;
                    this.expandedChildren = [];
                    this.childrenCache = {};
                    this.childrenLoading = {};
                }
            } catch (e) {
                console.error('Gagal memuat view data:', e);
                this.groups = [];
            } finally {
                this.viewLoading = false;
            }
        },

        grandActual(m) {
            return this.flatRows.reduce((s, r) => s + (parseFloat(r.actual?.['a' + m] || 0)), 0);
        },
        grandActualAvg() {
            let s = 0;
            for (let i = 1; i <= 8; i++) s += this.grandActual(i);
            return s / 8;
        },
        grandActualTotal() {
            return this.flatRows.reduce((s, r) => s + (parseFloat(r.actual?.atotal || 0)), 0);
        },
        grandBudget(m) {
            return this.flatRows.reduce((s, r) => s + (parseFloat(r.budget?.['b' + m] || 0)), 0);
        },
        grandBudgetTotal() {
            return this.flatRows.reduce((s, r) => s + (parseFloat(r.budget?.btotal || 0)), 0);
        },

        avgActual(actualObj) {
            if (!actualObj) return 0;
            let sum = 0;
            for (let i = 1; i <= 8; i++) sum += parseFloat(actualObj['a' + i] || 0);
            return sum / 8;
        },

        fmtShort(val) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(val) || 0);
        },

        exportData() {
            if (!this.selectedCostCenter) return;
            window.location.href = `<?= base_url('opex-selling/exportExcel') ?>?dept=` + encodeURIComponent(this.selectedCostCenter);
        },

        init() {
            if (this.costCenters.length > 0) {
                this.selectedCostCenter = this.costCenters[0].id;
                this.fetchViewData();
            }
        }
    }
}
</script>
