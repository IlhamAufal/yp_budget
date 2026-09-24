<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="fohDetailEntry()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

  <!-- HEADER -->
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <a href="<?= base_url('foh/entry-budget') ?>" class="hover:text-[#2F3185] transition-colors">FOH</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span>Entry Budget</span>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span class="text-[#2F3185] font-bold" x-text="headerName"></span>
      </div>
      <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white" x-text="headerName"></h1>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="deptLabel"></p>
    </div>
    <div class="flex items-center gap-3">
      <a href="<?= base_url('foh/entry-budget') ?>" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-xs transition-all active:scale-[0.98]">
        <i class="fa-solid fa-arrow-left text-[11px]"></i>
        <span>Kembali</span>
      </a>
    </div>
  </div>

  <!-- Table Section Label (Separated from table container) -->
  <div>
    <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Rincian Sub-Account FOH (Budget vs Actual)</h3>
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Penetapan alokasi anggaran bulanan per sub-account dalam satuan jutaan rupiah.</p>
  </div>

  <!-- TABLE: Matrix Budget + Actual -->
  <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
    <div class="overflow-x-auto scrollbar-thin">
      <table class="w-full text-left text-xs border-collapse min-w-[1300px] whitespace-nowrap">
        <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
          <tr class="bg-[#2F3185] text-white border-b border-white/20 font-semibold text-xs">
            <th rowspan="2" class="py-3 px-4 text-center w-16 border-r border-white/20 text-white font-semibold">Detail</th>
            <th rowspan="2" class="py-3 px-4 min-w-[260px] border-r border-white/20 text-white font-semibold">Main Account</th>
            <th colspan="12" class="py-2 px-4 text-center bg-[#25276d] text-white border-r border-white/20 font-semibold">Budget (Dalam Jutaan)</th>
            <th colspan="12" class="py-2 px-4 text-center bg-[#25276d] text-white font-semibold">Actual (Dalam Jutaan)</th>
          </tr>
          <tr class="bg-[#25276d] text-white border-b border-white/20 text-xs font-semibold">
            <template x-for="m in months" :key="m">
              <th class="py-2 px-2.5 text-right border-r border-white/20 text-white font-semibold" x-text="m"></th>
            </template>
            <template x-for="m in months" :key="'act_'+m">
              <th class="py-2 px-2.5 text-right border-r border-white/20 text-white font-semibold" x-text="m"></th>
            </template>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
          <template x-if="matrixRows.length === 0 && !loading">
            <tr>
              <td colspan="26" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                    <i class="fa-solid fa-table-cells-large text-xl"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data Sub-Account</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ditemukan rincian sub-account pada header ini.</p>
                </div>
              </td>
            </tr>
          </template>
          <template x-if="loading">
            <tr>
              <td colspan="26" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                    <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data Matriks...</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sistem sedang memproses rincian FOH.</p>
                </div>
              </td>
            </tr>
          </template>
          <template x-for="(row, idx) in matrixRows" :key="idx">
            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
              <td class="py-2.5 px-4 text-center border-r border-gray-200 dark:border-gray-800 font-sans">
                <button @click="openModalDetail(row, idx)" title="Edit Detail Item" class="h-7 w-7 rounded-lg inline-flex items-center justify-center bg-[#2F3185] hover:bg-[#25276d] text-white shadow-xs active:scale-[0.98] transition-all">
                  <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                </button>
              </td>
              <td class="py-2.5 px-4 font-sans font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800" x-text="row.acct_code + ' - ' + row.coa_name"></td>
              <template x-for="m in 12" :key="m">
                <td class="py-2.5 px-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="formatNumber(row.budget['b'+m] || 0)"></td>
              </template>
              <template x-for="m in 12" :key="'act_'+m">
                <td class="py-2.5 px-2.5 text-right border-r border-gray-200 dark:border-gray-800"
                    :class="hasActual(row) ? 'text-gray-700 dark:text-gray-300' : 'text-blue-500 dark:text-blue-400 italic'"
                    x-text="formatNumber(getActualValue(row, m))"></td>
              </template>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>


</div>

<script>
function fohDetailEntry() {
  const params = new URLSearchParams(window.location.search);
  return {
    headerAccount: params.get('header') || '',
    dept: params.get('dept') || '',
    idx: params.get('idx') || '1',
    headerName: '',
    deptLabel: '',
    months: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
    matrixRows: [],
    loading: false,

    async init() {
      // Reload matriks setelah modal detail menyimpan data
      window.addEventListener('foh-detail-saved', () => this.loadMatrix());

      // Load cost center label
      try {
        const ccRes = await fetch('<?= base_url('foh/getEntryData') ?>?dept=<?= esc($dept) ?>', {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        // We just need the header name
        this.headerName = decodeURIComponent(this.headerAccount);
        this.deptLabel = this.dept;
      } catch(e) {}

      await this.loadMatrix();
    },

    async loadMatrix() {
      if (!this.headerAccount || !this.dept) return;
      this.loading = true;
      try {
        const res = await fetch(`<?= base_url('foh/getDetailMatrix') ?>?dept=${encodeURIComponent(this.dept)}&header=${encodeURIComponent(this.headerAccount)}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        this.matrixRows = data.matrix || [];
      } catch (e) {
        console.error('Gagal memuat matriks:', e);
        this.matrixRows = [];
      } finally {
        this.loading = false;
      }
    },

    openModalDetail(row, idx) {
      const url = `<?= base_url('foh/detail-entry-modal') ?>?entry_data_id=${row.entry_data_id || 0}&id_coa=${encodeURIComponent(row.main_account || '')}&dept=${encodeURIComponent(this.dept)}`;
      window.Modal.show({
        url: url,
        title: 'Detail Entry Budget: ' + (row.coa_name || ''),
        size: 'xl'
      });
    },

    formatNumber(val) {
      return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(val || 0);
    },

    hasActual(row) {
      // Check if actual data from yp_plan__trans_budget_actual has any non-zero value
      for (let m = 1; m <= 12; m++) {
        if (parseFloat(row.actual['a'+m]) !== 0) return true;
      }
      return false;
    },

    getActualValue(row, m) {
      // Use actual if available, otherwise use simulated from entry_detail
      if (this.hasActual(row)) {
        return row.actual['a'+m] || 0;
      }
      return row.simulated ? (row.simulated['a'+m] || 0) : 0;
    }
  }
}
</script>

<?= $this->endSection() ?>
