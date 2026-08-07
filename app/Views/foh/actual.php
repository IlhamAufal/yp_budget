<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="fohActualPage()" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">

  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span>FOH</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-brand-500 font-bold">Actual (Realisasi)</span>
      </div>
      <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white flex items-center gap-4">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400">
          <i class="fa-solid fa-chart-column text-lg"></i>
        </span>
        Actual FOH
      </h1>
      <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
        Realisasi Factory Overhead.
      </p>
    </div>
  </div>

  <!-- FILTER -->
  <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-5">
    <div class="flex flex-wrap items-end gap-4">
      <div class="flex-1 min-w-[260px]">
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Cost Center (FOH)</label>
        <select
          x-model="dept"
          @change="loadData()"
          class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-colors"
        >
          <option value="">— Semua Cost Center —</option>
          <?php foreach ($costCenters as $cc): ?>
            <option value="<?= esc($cc['cost_center']) ?>"><?= esc($cc['cc_code']) ?> — <?= esc($cc['cost_desc']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button
        type="button"
        @click="loadData()"
        class="inline-flex items-center gap-2 rounded-xl bg-gray-900 dark:bg-brand-500 px-5 py-3 text-sm font-semibold text-white hover:bg-black dark:hover:bg-brand-600 transition-colors"
      >
        <i class="fa-solid fa-rotate text-xs"></i> Muat Data
      </button>
    </div>
  </div>

  <!-- TABLE -->
  <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse min-w-[1000px]">
        <thead>
          <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200/80 dark:border-gray-800 text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <th class="py-4 px-4">Main Account</th>
            <th class="py-4 px-4 min-w-[180px]">Deskripsi</th>
            <th class="py-4 px-2 text-right">Jan</th>
            <th class="py-4 px-2 text-right">Feb</th>
            <th class="py-4 px-2 text-right">Mar</th>
            <th class="py-4 px-2 text-right">Apr</th>
            <th class="py-4 px-2 text-right">May</th>
            <th class="py-4 px-2 text-right">Jun</th>
            <th class="py-4 px-2 text-right">Jul</th>
            <th class="py-4 px-2 text-right">Aug</th>
            <th class="py-4 px-2 text-right">Sep</th>
            <th class="py-4 px-2 text-right">Oct</th>
            <th class="py-4 px-2 text-right">Nov</th>
            <th class="py-4 px-2 text-right">Dec</th>
            <th class="py-4 px-4 text-right bg-gray-100/70 dark:bg-gray-800">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <template x-if="rows.length === 0 && !loading">
            <tr>
              <td colspan="15" class="py-16 text-center text-gray-400 dark:text-gray-500">
                <i class="fa-solid fa-file-circle-question text-2xl mb-3"></i>
                <p class="font-semibold text-gray-600 dark:text-gray-300">Belum ada data actual</p>
                <p class="text-sm">Pilih cost center atau upload data actual melalui modul terkait.</p>
              </td>
            </tr>
          </template>
          <template x-for="(row, index) in rows" :key="row.id">
            <tr x-show="isRowVisible(index)" class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
              <td class="py-2.5 px-4 font-mono font-semibold text-gray-900 dark:text-gray-100" x-text="row.id_coa"></td>
              <td class="py-2.5 px-4 text-gray-700 dark:text-gray-300" x-text="row.coa_desc || '-'"></td>
              <td class="py-2.5 px-2 text-right" x-text="fmt(row.jan)"></td>
              <td class="py-2.5 px-2 text-right" x-text="fmt(row.feb)"></td>
              <td class="py-2.5 px-2 text-right" x-text="fmt(row.mar)"></td>
              <td class="py-2.5 px-2 text-right" x-text="fmt(row.apr)"></td>
              <td class="py-2.5 px-2 text-right" x-text="fmt(row.may)"></td>
              <td class="py-2.5 px-2 text-right" x-text="fmt(row.jun)"></td>
              <td class="py-2.5 px-2 text-right" x-text="fmt(row.jul)"></td>
              <td class="py-2.5 px-2 text-right" x-text="fmt(row.aug)"></td>
              <td class="py-2.5 px-2 text-right" x-text="fmt(row.sep)"></td>
              <td class="py-2.5 px-2 text-right" x-text="fmt(row.oct)"></td>
              <td class="py-2.5 px-2 text-right" x-text="fmt(row.nov)"></td>
              <td class="py-2.5 px-2 text-right" x-text="fmt(row.dec)"></td>
              <td class="py-2.5 px-4 text-right font-bold text-brand-500 dark:text-brand-400 bg-gray-50/70 dark:bg-gray-800/50" x-text="fmt(row.total)"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div
      x-show="rows.length > 0"
      class="border-t border-gray-100 dark:border-gray-800 p-5 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400"
    >
      <div class="flex items-center gap-1.5 text-sm">
        <span>Menampilkan</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="rows.length === 0 ? 0 : ((currentPage - 1) * perPage + 1)"></span>
        <span>-</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, rows.length)"></span>
        <span>dari</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="rows.length"></span>
        <span>data</span>
      </div>

      <div class="flex items-center gap-2" x-show="totalPages > 1">
        <button
          type="button"
          @click="prevPage()"
          :disabled="currentPage === 1"
          class="h-9 px-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-sm font-semibold flex items-center gap-2"
        >
          <i class="fa-solid fa-chevron-left text-xs"></i>
          <span class="hidden sm:inline">Sebelumnya</span>
        </button>

        <template x-for="(p, idx) in pageNumbers()" :key="idx">
          <div>
            <template x-if="p === '...'">
              <span class="px-2 py-1 text-gray-400 font-bold">...</span>
            </template>
            <template x-if="p !== '...'">
              <button
                type="button"
                @click="goToPage(p)"
                :class="currentPage === p ? 'bg-brand-500 text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                class="h-9 min-w-[36px] px-2 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center"
                x-text="p"
              ></button>
            </template>
          </div>
        </template>

        <button
          type="button"
          @click="nextPage()"
          :disabled="currentPage === totalPages"
          class="h-9 px-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-sm font-semibold flex items-center gap-2"
        >
          <span class="hidden sm:inline">Berikutnya</span>
          <i class="fa-solid fa-chevron-right text-xs"></i>
        </button>
      </div>
    </div>
  </div>

</div>

<script>
  function fohActualPage() {
    return {
      dept: '',
      rows: [],
      loading: false,

      // Pagination
      currentPage: 1,
      perPage: 10,
      get totalPages() {
        return Math.ceil(this.rows.length / this.perPage) || 1;
      },
      isRowVisible(index) {
        return index >= (this.currentPage - 1) * this.perPage && index < this.currentPage * this.perPage;
      },
      goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
          this.currentPage = page;
        }
      },
      prevPage() {
        if (this.currentPage > 1) {
          this.currentPage--;
        }
      },
      nextPage() {
        if (this.currentPage < this.totalPages) {
          this.currentPage++;
        }
      },
      pageNumbers() {
        const total = this.totalPages;
        const current = this.currentPage;
        if (total <= 7) {
          return Array.from({ length: total }, (_, i) => i + 1);
        }
        if (current <= 4) {
          return [1, 2, 3, 4, 5, '...', total];
        }
        if (current >= total - 3) {
          return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
        }
        return [1, '...', current - 1, current, current + 1, '...', total];
      },

      fmt(v) {
        return Number(v || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 });
      },

      async loadData() {
        this.loading = true;
        const res = await window.ypFetch('<?= base_url('foh/cariActualTable') ?>', { dept: this.dept });
        this.loading = false;
        this.rows = res.rows || [];
        this.currentPage = 1;
      },
    };
  }
</script>

<?= $this->endSection() ?>
