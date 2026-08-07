<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="fohSummaryPage()" class="p-4 md:p-8 mx-auto max-w-(--breakpoint-2xl) space-y-6 md:space-y-8">

  <!-- HEADER -->
  <div>
    <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-3">
      <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
      <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
      <span>FOH</span>
      <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
      <span class="text-brand-500 font-bold">Summary</span>
    </div>
    <h1 class="text-2xl md:text-3xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-4">
      <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-500 dark:bg-sky-500/10 dark:text-sky-400">
        <i class="fa-solid fa-table-cells-large text-xl"></i>
      </span>
      Summary FOH
    </h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
      Ringkasan budget Factory Overhead per Cost Center — Tahun Anggaran <span class="font-bold text-brand-500"><?= esc($workingYear) ?></span>
    </p>
  </div>

  <!-- CARD TOTAL -->
  <?php
    $grandTotal = 0;
    foreach (($summary ?? []) as $s) { $grandTotal += (float) ($s['total'] ?? 0); }
  ?>
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-5 flex items-center gap-4">
      <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400"><i class="fa-solid fa-industry"></i></span>
      <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cost Center</p>
        <p class="text-2xl font-black text-gray-900 dark:text-white"><?= count($summary ?? []) ?></p>
      </div>
    </div>
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-5 flex items-center gap-4">
      <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400"><i class="fa-solid fa-coins"></i></span>
      <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Budget FOH</p>
        <p class="text-2xl font-black text-gray-900 dark:text-white"><?= number_format($grandTotal) ?></p>
      </div>
    </div>
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-5 flex items-center gap-4">
      <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 dark:bg-amber-500/10 dark:text-amber-400"><i class="fa-solid fa-paper-plane"></i></span>
      <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sumber Data</p>
        <p class="text-lg font-black text-gray-900 dark:text-white">OPEX Engine <span class="text-xs font-bold text-emerald-500">(source: FOH)</span></p>
      </div>
    </div>
  </div>

  <!-- TABLE SUMMARY -->
  <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse min-w-[1000px]">
        <thead>
          <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200/80 dark:border-gray-800 text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <th class="py-4 px-4">Cost Center</th>
            <th class="py-4 px-4 min-w-[160px]">Deskripsi</th>
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
            <th class="py-4 px-4 text-center">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <template x-if="rows.length === 0">
            <tr>
              <td colspan="16" class="py-16 text-center text-gray-400 dark:text-gray-500">
                <i class="fa-solid fa-folder-open text-2xl mb-3"></i>
                <p class="font-semibold text-gray-600 dark:text-gray-300">Belum ada data budget FOH</p>
                <p class="text-sm">Entry budget FOH melalui menu <strong>FOH → Entry Budget</strong> terlebih dahulu.</p>
              </td>
            </tr>
          </template>
          <template x-for="(row, index) in rows" :key="row.id_dept">
            <tr x-show="isRowVisible(index)" class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
              <td class="py-3 px-4 font-mono font-bold text-gray-900 dark:text-gray-100" x-text="row.id_dept"></td>
              <td class="py-3 px-4 text-gray-700 dark:text-gray-300" x-text="row.cost_desc || '-'"></td>
              <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row.jan)"></td>
              <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row.feb)"></td>
              <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row.mar)"></td>
              <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row.apr)"></td>
              <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row.may)"></td>
              <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row.jun)"></td>
              <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row.jul)"></td>
              <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row.aug)"></td>
              <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row.sep)"></td>
              <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row.oct)"></td>
              <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row.nov)"></td>
              <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row.dec)"></td>
              <td class="py-3 px-4 text-right font-bold text-brand-500 dark:text-brand-400 bg-gray-50/70 dark:bg-gray-800/50" x-text="fmt(row.total)"></td>
              <td class="py-3 px-4 text-center">
                <span x-show="row.submitted_rows > 0" class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 px-3 py-1 text-[10px] font-bold">
                  <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> SUBMITTED
                </span>
                <span x-show="!(row.submitted_rows > 0)" class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 px-3 py-1 text-[10px] font-bold">
                  <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> DRAFT
                </span>
              </td>
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
        <span>cost center</span>
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
  function fohSummaryPage() {
    return {
      rows: <?= json_encode(array_map(fn($r) => [
        'id_dept'        => $r['id_dept'] ?? '',
        'cost_desc'      => $r['cost_desc'] ?? '',
        'jan' => (float) $r['jan'], 'feb' => (float) $r['feb'], 'mar' => (float) $r['mar'],
        'apr' => (float) $r['apr'], 'may' => (float) $r['may'], 'jun' => (float) $r['jun'],
        'jul' => (float) $r['jul'], 'aug' => (float) $r['aug'], 'sep' => (float) $r['sep'],
        'oct' => (float) $r['oct'], 'nov' => (float) $r['nov'], 'dec' => (float) $r['dec'],
        'total'          => (float) $r['total'],
        'submitted_rows' => (int) ($r['submitted_rows'] ?? 0),
      ], $summary ?? [])) ?>,

      fmt(v) {
        return Number(v || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 });
      },

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
    };
  }
</script>

<?= $this->endSection() ?>
