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
  <template x-if="selectedCostCenter">
    <div class="max-w-full overflow-x-auto rounded-sm border border-stroke dark:border-strokedark">
      <table class="w-full table-auto text-left text-xs border-collapse min-w-[1300px]">
        <thead>
          <tr class="bg-gray-2 text-black dark:bg-meta-4 dark:text-white font-bold uppercase border-b border-stroke dark:border-strokedark">
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
            <template x-for="m in ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','AVG','TOTAL']" :key="'act_'+m">
              <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]" x-text="m"></th>
            </template>
            <template x-for="m in ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC','TOTAL']" :key="'bud_'+m">
              <th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]" x-text="m"></th>
            </template>
          </tr>
        </thead>
        <tbody>
          <template x-if="viewLoading">
            <tr>
              <td colspan="26" class="py-16 text-center text-gray-500 dark:text-gray-400">
                <i class="fa-solid fa-spinner fa-spin text-2xl mb-2"></i>
                <p class="font-semibold">Memuat data...</p>
              </td>
            </tr>
          </template>
          <template x-if="!viewLoading && flatRows.length === 0">
            <tr>
              <td colspan="26" class="py-16 text-center text-gray-500 dark:text-gray-400">
                <i class="fa-solid fa-inbox mr-1"></i>No data available in table
              </td>
            </tr>
          </template>

          <template x-for="(entry, eIdx) in paginatedRows" :key="'p_'+eIdx">
            <tbody>
              <!-- Category subtotal row -->
              <template x-if="entry.isCategory">
                <tr class="bg-gray-100/80 dark:bg-meta-4 font-bold text-gray-900 dark:text-white">
                  <td class="py-2.5 px-2 text-center border-b border-r border-stroke dark:border-strokedark">
                    <i class="fas fa-folder" :class="groups.indexOf(entry.group) === 0 ? 'text-amber-500' : 'text-blue-500'"></i>
                  </td>
                  <td class="py-2.5 px-3 border-b border-r border-stroke dark:border-strokedark uppercase" x-text="entry.group.header_name"></td>
                  <template x-for="i in 8" :key="'gsa_'+eIdx+'_'+i">
                    <td class="py-2.5 px-1.5 text-right border-b border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(entry.group.actual[i] || 0)"></td>
                  </template>
                  <td class="py-2.5 px-1.5 text-right border-b border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(avgActual(entry.group.actual))"></td>
                  <td class="py-2.5 px-1.5 text-right border-b border-r border-stroke dark:border-strokedark font-mono font-bold" x-text="fmtShort(entry.group.actual_total)"></td>
                  <td class="bg-gray-900 dark:bg-gray-100 w-1.5 p-0 border-b"></td>
                  <template x-for="i in 12" :key="'gsb_'+eIdx+'_'+i">
                    <td class="py-2.5 px-1.5 text-right border-b border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(entry.group.budget[i] || 0)"></td>
                  </template>
                  <td class="py-2.5 px-1.5 text-right border-b border-r border-stroke dark:border-strokedark font-mono font-bold" x-text="fmtShort(entry.group.budget_total)"></td>
                </tr>
              </template>
              <!-- Item row -->
              <template x-if="!entry.isCategory">
                <tr class="hover:bg-gray-50 dark:hover:bg-meta-4 transition-colors">
                  <td class="py-2 px-2 text-center border-b border-r border-stroke dark:border-strokedark">
                    <button type="button" @click="toggleChild(entry.item)" :class="isChildOpen(entry.item) ? 'bg-warning text-black' : 'bg-primary text-white hover:bg-opacity-90'" class="inline-flex h-5 w-5 items-center justify-center rounded text-[10px] font-bold transition-colors shadow">
                      <span x-text="isChildOpen(entry.item) ? '−' : '+'"></span>
                    </button>
                  </td>
                  <td class="py-2 px-3 border-b border-r border-stroke dark:border-strokedark text-black dark:text-white">
                    <span x-text="entry.item.acct_code"></span> - <span x-text="entry.item.coa_name"></span>
                  </td>
                  <template x-for="i in 8" :key="'ia_'+eIdx+'_'+i">
                    <td class="py-2 px-1.5 text-right border-b border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(entry.item.actual['a'+i])"></td>
                  </template>
                  <td class="py-2 px-1.5 text-right border-b border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(avgActual(entry.item.actual))"></td>
                  <td class="py-2 px-1.5 text-right border-b border-r border-stroke dark:border-strokedark font-mono font-semibold" x-text="fmtShort(entry.item.actual.atotal)"></td>
                  <td class="bg-gray-900 dark:bg-gray-100 w-1.5 p-0 border-b"></td>
                  <template x-for="i in 12" :key="'ib_'+eIdx+'_'+i">
                    <td class="py-2 px-1.5 text-right border-b border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(entry.item.budget['b'+i])"></td>
                  </template>
                  <td class="py-2 px-1.5 text-right border-b border-r border-stroke dark:border-strokedark font-mono font-semibold" x-text="fmtShort(entry.item.budget.btotal)"></td>
                </tr>
                <!-- Expanded children -->
                <tr x-show="isChildOpen(entry.item)" x-cloak class="bg-gray-1 dark:bg-meta-4/30">
                  <td colspan="26" class="p-3 border-b border-stroke dark:border-strokedark">
                    <div class="rounded border border-stroke bg-white p-3 shadow-inner dark:border-strokedark dark:bg-boxdark">
                      <h5 class="mb-2 text-[11px] font-bold uppercase text-gray-700 dark:text-gray-300">Rincian Sub-Detail — <span x-text="entry.item.acct_code + ' - ' + entry.item.coa_name"></span></h5>
                      <table class="w-full text-left text-[11px] border border-stroke dark:border-strokedark">
                        <thead>
                          <tr class="bg-gray-2 dark:bg-meta-4 font-semibold text-black dark:text-white">
                            <th class="py-1.5 px-2 border-r border-stroke dark:border-strokedark">RINCIAN DESKRIPSI</th>
                            <th class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark">JAN</th>
                            <th class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark">FEB</th>
                            <th class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark">MAR</th>
                            <th class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark">APR</th>
                            <th class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark">MAY</th>
                            <th class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark">JUN</th>
                            <th class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark">JUL</th>
                            <th class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark">AUG</th>
                            <th class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark">SEP</th>
                            <th class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark">OCT</th>
                            <th class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark">NOV</th>
                            <th class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark">DEC</th>
                            <th class="py-1.5 px-2 text-right">TOTAL</th>
                          </tr>
                        </thead>
                        <tbody>
                          <template x-if="entry.item.children.length === 0">
                            <tr>
                              <td colspan="14" class="py-3 text-center text-gray-400">Belum ada rincian</td>
                            </tr>
                          </template>
                          <template x-for="(ch, cIdx) in entry.item.children" :key="'cld_'+eIdx+'_'+cIdx">
                            <tr class="border-t border-stroke dark:border-strokedark">
                              <td class="py-1.5 px-2 border-r border-stroke dark:border-strokedark" x-text="ch.desc"></td>
                              <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(ch.jan)"></td>
                              <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(ch.feb)"></td>
                              <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(ch.mar)"></td>
                              <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(ch.apr)"></td>
                              <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(ch.may)"></td>
                              <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(ch.jun)"></td>
                              <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(ch.jul)"></td>
                              <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(ch.aug)"></td>
                              <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(ch.sep)"></td>
                              <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(ch.oct)"></td>
                              <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(ch.nov)"></td>
                              <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(ch.dec)"></td>
                              <td class="py-1.5 px-2 text-right font-bold font-mono" x-text="fmtShort(ch.total)"></td>
                            </tr>
                          </template>
                        </tbody>
                      </table>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </template>

          <!-- GRAND TOTAL -->
          <template x-if="!viewLoading && groups.length > 0">
            <tr class="bg-gray-100/80 dark:bg-meta-4 font-bold text-black dark:text-white border-t border-stroke dark:border-strokedark">
              <td colspan="2" class="py-3 px-3 border-r border-stroke dark:border-strokedark uppercase text-right">Grand Total</td>
              <template x-for="i in 8" :key="'ga_'+i">
                <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandActual(i))"></td>
              </template>
              <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandActualAvg())"></td>
              <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono font-bold" x-text="fmtShort(grandActualTotal())"></td>
              <td class="bg-gray-900 dark:bg-gray-100 w-1.5 p-0"></td>
              <template x-for="i in 12" :key="'gb_'+i">
                <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandBudget(i))"></td>
              </template>
              <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono font-bold" x-text="fmtShort(grandBudgetTotal())"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <template x-if="!viewLoading && totalRows > 0">
      <div class="flex flex-wrap items-center justify-between gap-3 border border-t-0 border-stroke dark:border-strokedark bg-white px-4 py-3 text-xs dark:bg-boxdark">
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
            <span x-show="p === '...'" class="px-1.5 text-gray-400">&hellip;</span>
            <button x-show="p !== '...'" type="button" @click="goPage(p)" :class="currentPage === p ? 'bg-primary text-white font-bold' : 'border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition'" class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
          </template>
          <button type="button" @click="goPage(currentPage + 1)" :disabled="currentPage === totalPages" class="h-7 px-2.5 rounded-lg border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-50 transition">
            <i class="fas fa-chevron-right text-[10px]"></i>
          </button>
        </div>
      </div>
    </template>
  </template>

  <template x-if="!selectedCostCenter">
    <div class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-gray-200 rounded-sm dark:border-strokedark">
      <i class="fa-solid fa-chart-column text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
      <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Silakan pilih Cost Center untuk meninjau data konsolidasi View Data.</p>
    </div>
  </template>
</div>

<script>
function opexSellingViewTab() {
    return {
        searchCostCenter: '',
        dropdownOpen: false,
        costCenters: <?= json_encode(array_map(fn($cc) => [
            'id'   => $cc['cost_center'],
            'name' => ($cc['cc_code'] ?? $cc['cost_center']) . ' - ' . $cc['cost_desc'],
        ], $costCenters), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
        selectedCostCenter: '',

        groups: [],
        ccInfo: null,
        viewLoading: false,
        expandedChildren: [],

        // Pagination
        currentPage: 1,
        perPage: 10,

        get ccLabel() {
            return this.ccInfo ? `[${this.ccInfo.cc_code}] ${(this.ccInfo.cost_desc || '').toUpperCase()}` : '';
        },

        get flatRows() {
            const out = [];
            for (const g of this.groups) {
                out.push({ isCategory: true, group: g, item: null });
                for (const item of g.items) {
                    out.push({ isCategory: false, group: g, item: item });
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

        toggleChild(item) {
            const key = String(item.acct_code);
            if (this.expandedChildren.includes(key)) {
                this.expandedChildren = this.expandedChildren.filter(k => k !== key);
            } else {
                this.expandedChildren.push(key);
            }
        },
        isChildOpen(item) { return this.expandedChildren.includes(String(item.acct_code)); },

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
                    const groups = [];
                    for (const g of (data.groups || [])) {
                        const budget = {}, actual = {};
                        for (let m = 1; m <= 12; m++) { budget[m] = 0; actual[m] = 0; }
                        let budgetTotal = 0, actualTotal = 0;
                        for (const item of g.items) {
                            for (let m = 1; m <= 12; m++) {
                                budget[m] += parseFloat(item.budget?.['b' + m] || 0);
                                actual[m] += parseFloat(item.actual?.['a' + m] || 0);
                            }
                            budgetTotal += parseFloat(item.budget?.btotal || 0);
                            actualTotal += parseFloat(item.actual?.atotal || 0);
                        }
                        groups.push({
                            header_name: g.header_name,
                            budget, budget_total: budgetTotal,
                            actual, actual_total: actualTotal,
                            items: g.items
                        });
                    }
                    this.groups = groups;
                    this.currentPage = 1;
                    this.expandedChildren = [];
                }
            } catch (e) {
                console.error('Gagal memuat view data:', e);
                this.groups = [];
            } finally {
                this.viewLoading = false;
            }
        },

        grandActual(m) { return this.groups.reduce((s, g) => s + (parseFloat(g.actual[m] || 0)), 0); },
        grandActualAvg() { let s = 0; for (let i = 1; i <= 8; i++) s += this.grandActual(i); return s / 8; },
        grandActualTotal() { return this.groups.reduce((s, g) => s + (parseFloat(g.actual_total || 0)), 0); },
        grandBudget(m) { return this.groups.reduce((s, g) => s + (parseFloat(g.budget[m] || 0)), 0); },
        grandBudgetTotal() { return this.groups.reduce((s, g) => s + (parseFloat(g.budget_total || 0)), 0); },

        avgActual(actualObj) {
            if (!actualObj) return 0;
            let sum = 0;
            for (let i = 1; i <= 8; i++) sum += parseFloat(actualObj['a' + i] || actualObj[i] || 0);
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