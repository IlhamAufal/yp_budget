<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="fohEntryPage()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

  <!-- HEADER -->
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span class="hover:text-[#2F3185]">FOH</span>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span class="text-[#2F3185] font-bold">Entry Budget</span>
      </div>
      <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        FOH Entry Budget
      </h1>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
        Pengisian dan peninjauan anggaran Factory Overhead per Cost Center.
      </p>
    </div>
  </div>

  <!-- TAB NAV -->
  <div class="nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
    <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="FOH Entry Tabs">
      <button
        type="button"
        @click="activeTab = 'entry'"
        :class="activeTab === 'entry' ? 'active' : ''"
        class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
        <span>Entry Budget</span>
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

      <!-- TAB: ENTRY BUDGET -->
      <div x-show="activeTab === 'entry'" x-cloak class="space-y-6">

        <!-- Info Periode Submit -->
        <div class="flex items-center justify-between rounded-2xl border p-4 text-xs font-medium shadow-xs"
             :class="configPeriod ? 'border-blue-200 bg-blue-50/70 text-blue-800 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300' : 'border-gray-200 bg-gray-50/70 text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400'">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-info text-blue-600 text-sm shrink-0" x-show="configPeriod"></i>
            <i class="fa-solid fa-clock text-gray-400 text-sm shrink-0" x-show="!configPeriod"></i>
            <div>
              <span class="font-bold" x-text="configPeriod ? 'Information:' : 'Periode Submit'"></span>
              <p class="text-xs mt-0.5" x-show="configPeriod && configPeriod.start_date">
                Periode submit data FOH dimulai pada <strong x-text="formatDate(configPeriod.start_date) + ' s/d ' + formatDate(configPeriod.end_date)"></strong>
              </p>
              <p class="text-xs mt-0.5" x-show="!configPeriod">Belum ada konfigurasi periode submit untuk modul FOH.</p>
            </div>
          </div>
        </div>

        <!-- Filter: Cost Center -->
        <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-6">
          <div class="flex flex-wrap items-end gap-4">
            <div class="w-full max-w-xl">
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Cost Center (FOH)</label>
              <div class="relative" x-data="{ openCC: false, searchCC: '' }">
                <button @click="openCC = !openCC" type="button" class="w-full flex items-center justify-between rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-left text-xs font-medium text-gray-900 dark:text-white hover:bg-white dark:hover:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 outline-none transition-colors">
                  <span x-text="selectedDept ? (selectedDept === 'ALL' ? '[ALL] Semua Cost Center' : costCenters.find(c => c.cost_center == selectedDept)?.cc_code + ' — ' + costCenters.find(c => c.cost_center == selectedDept)?.cost_desc) : '— Pilih Cost Center —'"></span>
                  <i class="fa-solid fa-chevron-down text-[10px] text-gray-400"></i>
                </button>
                <div x-show="openCC" @click.outside="openCC = false" x-transition class="absolute z-30 mt-1 w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg overflow-hidden">
                  <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                    <input type="text" x-model="searchCC" placeholder="Cari Cost Center..." class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700 px-3.5 py-2 text-xs focus:border-[#2F3185] focus:outline-none dark:text-white">
                  </div>
                  <ul class="max-h-60 overflow-auto py-1 text-xs text-gray-700 dark:text-gray-200 p-1">
                    <li @click="selectedDept = ''; openCC = false; loadHeaderData()" class="cursor-pointer px-3.5 py-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-[#2F3185] transition-colors">
                      <span class="font-semibold">— Semua Cost Center —</span>
                    </li>
                    <template x-for="cc in costCenters" :key="cc.cost_center">
                      <li @click="selectedDept = cc.cost_center; openCC = false; loadHeaderData()" class="cursor-pointer px-3.5 py-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-[#2F3185] transition-colors">
                        <span x-text="cc.cc_code + ' — ' + cc.cost_desc"></span>
                      </li>
                    </template>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- TABLE: Header Accounts -->
        <template x-if="selectedDept">
          <div class="space-y-4">
            <!-- Table Section Label (Separated from table container) -->
            <div>
              <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Daftar Akun Header FOH (Actual)</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ringkasan actual biaya per akun sebelum pengisian rincian budget.</p>
            </div>

            <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
              <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs border-collapse min-w-[1000px] whitespace-nowrap">
                  <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                      <th rowspan="2" class="border-r border-white/20 py-3 px-4 text-center w-20 text-white font-semibold">Entry</th>
                      <th rowspan="2" class="border-r border-white/20 py-3 px-4 min-w-[260px] text-white font-semibold">Expense Account</th>
                      <th colspan="8" class="border-r border-white/20 py-2 px-2 text-center text-white bg-[#25276d] font-semibold">Actual</th>
                      <th rowspan="2" class="py-3 px-4 text-right text-white bg-[#25276d] font-bold">Total</th>
                    </tr>
                    <tr class="bg-[#25276d] text-white text-xs font-semibold border-b border-white/20">
                      <template x-for="m in actualMonths" :key="m">
                        <th class="border-r border-white/20 py-2 px-2.5 text-right text-white" x-text="m"></th>
                      </template>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-if="headerRows.length === 0 && !loading">
                      <tr>
                        <td :colspan="actualMonths.length + 3" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                          <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                              <i class="fa-solid fa-industry text-xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data Header Account</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Data akan muncul setelah upload actual atau jika ada data di database.</p>
                          </div>
                        </td>
                      </tr>
                    </template>
                    <template x-if="loading">
                      <tr>
                        <td :colspan="actualMonths.length + 3" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                          <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                              <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data...</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sistem sedang memproses data header FOH.</p>
                          </div>
                        </td>
                      </tr>
                    </template>
                    <template x-for="(row, idx) in paginatedRows" :key="(currentPage - 1) * perPage + idx">
                      <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-4 text-center font-sans">
                          <a :href="`<?= base_url('foh/entry-budget-detail') ?>?header=${row.main_account}&dept=${selectedDept}&idx=${idx+1}`"
                             class="h-7 w-7 rounded-lg inline-flex items-center justify-center bg-[#2F3185] hover:bg-[#25276d] text-white shadow-xs active:scale-[0.98] transition-all">
                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                          </a>
                        </td>
                        <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-4 font-sans font-medium text-gray-900 dark:text-white" x-text="row.coa_name || row.acct_code"></td>
                        <template x-for="(m, mIdx) in ['jan','feb','mar','apr','may','jun','jul','aug']" :key="m">
                          <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="formatNumber(row[m] || 0)"></td>
                        </template>
                        <td class="py-2.5 px-4 text-right font-bold text-gray-900 dark:text-white bg-gray-50/70 dark:bg-gray-800/50" x-text="formatNumber(row.total_actual || 0)"></td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>

              <!-- Pagination Footer -->
              <template x-if="selectedDept && headerRows.length > perPage">
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 text-xs text-gray-500 dark:text-gray-400">
                  <span>
                    Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="((currentPage - 1) * perPage) + 1"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, totalRows)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalRows"></span> data
                  </span>
                  <div class="flex items-center gap-1">
                    <button @click="goPage(currentPage - 1)" :disabled="currentPage <= 1"
                      class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                      <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </button>
                    <template x-for="(p, pi) in pageNumbers()" :key="'pg_'+pi">
                      <button x-show="p !== '...'" @click="goPage(p)"
                        :class="currentPage === p ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="h-8 min-w-[32px] px-2 rounded-lg text-xs font-semibold transition flex items-center justify-center"
                        x-text="p"></button>
                      <span x-show="p === '...'" class="px-1 text-gray-400">&hellip;</span>
                    </template>
                    <button @click="goPage(currentPage + 1)" :disabled="currentPage >= totalPages"
                      class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                      <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </template>

        <!-- Empty state -->
        <template x-if="!selectedDept">
          <div class="rounded-2xl border border-gray-200/80 bg-white p-12 dark:border-gray-800 dark:bg-gray-900 text-center shadow-xs">
            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
              <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                <i class="fa-solid fa-building text-xl"></i>
              </div>
              <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Pilih Cost Center Terlebih Dahulu</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">Silakan pilih Cost Center pada dropdown di atas untuk menampilkan data entry FOH.</p>
            </div>
          </div>
        </template>

      </div>

      <!-- TAB: VIEW DATA -->
      <div x-show="activeTab === 'view'" x-cloak class="space-y-6">
        <?= $this->include('foh/partials/tab_view_data') ?>
      </div>

    </div>
  </div>
</div>

<script>
function fohEntryPage() {
  return {
    activeTab: 'entry',
    selectedDept: '',
    viewDept: '',
    configPeriod: null,
    actualMonths: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG'],
    costCenters: <?= json_encode($costCenters) ?>,
    headerRows: [],
    viewRows: [],
    loading: false,
    viewLoading: false,

    // Pagination state
    currentPage: 1,
    perPage: 10,
    get totalRows() { return this.headerRows.length; },
    get totalPages() { return Math.ceil(this.totalRows / this.perPage) || 1; },
    get paginatedRows() {
      const start = (this.currentPage - 1) * this.perPage;
      return this.headerRows.slice(start, start + this.perPage);
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

    async init() {
      await this.loadConfigPeriod();
      this.$watch('selectedDept', (val) => this.loadWorkflow());
      if (this.selectedDept) this.loadWorkflow();
    },

    // --- Workflow Approval ---
    workflow: null,
    workflowBusy: false,
    get workflowStatus() {
      return this.workflow?.status ?? null;
    },
    get workflowBadge() {
      const map = {
        'W': { bg: 'border-amber-200 bg-amber-50/70 text-amber-700 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-300', icon: 'fa-solid fa-clock text-amber-600', label: 'Waiting Approval' },
        'A': { bg: 'border-emerald-200 bg-emerald-50/70 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300', icon: 'fa-solid fa-circle-check text-emerald-500', label: 'Approved' },
        'R': { bg: 'border-rose-200 bg-rose-50/70 text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-300', icon: 'fa-solid fa-circle-xmark text-rose-500', label: 'Rejected' }
      };
      return map[this.workflowStatus] || { bg: 'border-gray-200 bg-gray-50 text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400', icon: 'fa-solid fa-clipboard-check text-gray-400', label: 'No Status' };
    },

    async loadWorkflow() {
      if (!this.selectedDept) { this.workflow = null; return; }
      try {
        const res = await fetch(`<?= base_url('foh/getStatus') ?>?dept=${encodeURIComponent(this.selectedDept)}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        this.workflow = data.workflow ?? null;
      } catch (e) { console.error('Gagal memuat status workflow:', e); this.workflow = null; }
    },

    async runWorkflow(action) {
      if (!this.selectedDept) return;
      if (action === 'reject' && !confirm('Yakin menolak budget FOH ini?')) return;
      if (action === 'approve' && !confirm('Yakin menyetujui budget FOH ini?')) return;
      this.workflowBusy = true;
      try {
        const res = await fetch(`<?= base_url('foh/') ?>` + action, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
          body: `dept=${encodeURIComponent(this.selectedDept)}`
        });
        const data = await res.json();
        if (data.status === 'success') {
          if (window.showToast) window.showToast('success', data.message);
          await this.loadWorkflow();
        } else {
          alert(data.message);
        }
      } catch (e) {
        console.error('Workflow error:', e);
        alert('Gagal memproses workflow.');
      } finally {
        this.workflowBusy = false;
      }
    },

    async loadConfigPeriod() {
      try {
        const res = await fetch('<?= base_url('foh/getConfigPeriod') ?>', {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        this.configPeriod = data.period || null;
      } catch (e) {
        console.error('Gagal memuat periode:', e);
      }
    },

    async loadHeaderData() {
      if (!this.selectedDept) return;
      this.loading = true;
      this.currentPage = 1;
      try {
        const res = await fetch(`<?= base_url('foh/getHeaderAccounts') ?>?dept=${encodeURIComponent(this.selectedDept)}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        this.headerRows = data.headers || [];
      } catch (e) {
        console.error('Gagal memuat header:', e);
        this.headerRows = [];
      } finally {
        this.loading = false;
      }
    },

    async loadViewData() {
      if (!this.viewDept) return;
      this.viewLoading = true;
      try {
        const res = await fetch(`<?= base_url('foh/getHeaderAccounts') ?>?dept=${encodeURIComponent(this.viewDept)}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        const headers = data.headers || [];

        // Build view rows with budget data
        const viewRows = [];
        for (const h of headers) {
          const matrixRes = await fetch(`<?= base_url('foh/getDetailMatrix') ?>?dept=${encodeURIComponent(this.viewDept)}&header=${encodeURIComponent(h.main_account)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          });
          const matrixData = await matrixRes.json();
          const matrix = matrixData.matrix || [];

          let budgetTotal = 0, actualTotal = 0;
          const budget = {}, actual = {};
          for (let m = 1; m <= 12; m++) { budget[m] = 0; actual[m] = 0; }

          for (const item of matrix) {
            for (let m = 1; m <= 12; m++) {
              budget[m] += parseFloat(item.budget?.['b' + m] || 0);
              actual[m] += parseFloat(item.actual?.['a' + m] || 0);
            }
            budgetTotal += parseFloat(item.budget?.btotal || 0);
            actualTotal += parseFloat(item.actual?.atotal || 0);
          }

          viewRows.push({
            header_name: h.coa_name || h.acct_code,
            acct_code: h.acct_code,
            budget, budget_total: budgetTotal,
            actual, actual_total: actualTotal,
            items: matrix
          });
        }

        this.viewRows = viewRows;
      } catch (e) {
        console.error('Gagal memuat view data:', e);
        this.viewRows = [];
      } finally {
        this.viewLoading = false;
      }
    },

    exportData() {
      if (!this.viewDept) return;
      window.location.href = `<?= base_url('foh/exportExcel') ?>?dept=` + this.viewDept;
    },

    formatNumber(val) {
      return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(val || 0);
    },

    fmtShort(val) {
      return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(val || 0);
    },

    avgActual(actualObj) {
      if (!actualObj) return 0;
      // Sum months 1-8 using keyed access (supports both 'a1'..'a8' and numeric 1..8)
      let sum = 0;
      for (let i = 1; i <= 8; i++) {
        sum += parseFloat(actualObj['a' + i] || actualObj[i] || 0);
      }
      return sum / 8;
    },

    formatDate(dateStr) {
      if (!dateStr) return '-';
      const d = new Date(dateStr);
      return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }
  }
}
</script>

<?= $this->endSection() ?>
