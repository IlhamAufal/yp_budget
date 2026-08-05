<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-4" x-data="periodPage()">

  <!-- ============================================================ -->
  <!-- BREADCRUMB & HEADER -->
  <!-- ============================================================ -->
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors">
          <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span>Master Data</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-brand-500 font-bold">Periode & Tahun Anggaran</span>
      </div>
      <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
          <i class="fa-solid fa-calendar-days text-base"></i>
        </span>
        Master Periode & Tahun Anggaran
      </h1>
      <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
        Konfigurasi tahun anggaran aktif (Working Year), rentang tanggal, serta proteksi penguncian data (Lock Period).
      </p>
    </div>

  <!-- Quick Action Buttons -->
  </div>

  <!-- ============================================================ -->
  <!-- STATS CARDS -->
  <!-- ============================================================ -->
  <?php
    $totalYears  = count($rows ?? []);
    $activeYear  = null;
    $lockedCount = 0;
    foreach (($rows ?? []) as $r) {
        if (($r['status'] ?? 'A') === 'A' && $activeYear === null) {
            $activeYear = $r;
        }
        if (! empty($r['locked'])) {
            $lockedCount++;
        }
    }
    $currentWorkingYear = session()->get('year_code') ?? ($activeYear['year_code'] ?? date('Y'));
  ?>
  <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-xl border border-gray-200/80 bg-white px-4 py-2.5 text-xs dark:border-gray-800 dark:bg-gray-900">
    <!-- Stat 1: Working Year Aktif -->
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400">
        <i class="fa-solid fa-calendar-check text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Working Year:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= esc($currentWorkingYear) ?></span>
    </div>

    <span class="text-gray-300 dark:text-gray-700">|</span>

    <!-- Stat 2: Total Tahun -->
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-50 text-blue-500 dark:bg-blue-500/10 dark:text-blue-400">
        <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Total Periode:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= number_format($totalYears) ?></span>
    </div>

    <span class="text-gray-300 dark:text-gray-700">|</span>

    <!-- Stat 3: Status Kunci -->
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-amber-50 text-amber-500 dark:bg-amber-500/10 dark:text-amber-400">
        <i class="fa-solid fa-lock text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Dikunci:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= number_format($lockedCount) ?></span>
    </div>

    <span class="text-gray-300 dark:text-gray-700">|</span>

    <!-- Stat 4: Rentang Aktif -->
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-purple-50 text-purple-500 dark:bg-purple-500/10 dark:text-purple-400">
        <i class="fa-solid fa-calendar-range text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Rentang:</span>
      <span class="font-bold text-gray-900 dark:text-white">
        <?php if (! empty($activeYear['period_start']) && ! empty($activeYear['period_end'])): ?>
          <?= date('d M Y', strtotime($activeYear['period_start'])) ?> - <?= date('d M Y', strtotime($activeYear['period_end'])) ?>
        <?php else: ?>
          Tahun <?= esc($currentWorkingYear) ?>
        <?php endif; ?>
      </span>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- DATA TABLE -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold capitalize tracking-normal text-gray-500 dark:text-gray-400">
            <th class="py-3.5 px-4">Form Budget</th>
            <th class="py-3.5 px-4">Begin Date</th>
            <th class="py-3.5 px-4">End Date</th>
            <th class="py-3.5 px-4 text-center">Cost Center</th>
            <th class="py-3.5 px-4 text-right w-44">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="5" class="py-12 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-2">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400">
                    <i class="fa-solid fa-calendar-days text-xl"></i>
                  </div>
                  <p class="font-semibold text-gray-600 dark:text-gray-300">Belum ada data periode anggaran</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($rows as $index => $r): ?>
              <?php
                $isCurrent = (int)$r['year_code'] === (int)$currentWorkingYear;
                $isLocked  = ! empty($r['locked']);
              ?>
              <tr x-show="isRowVisible(<?= $index ?>)" class="<?= $isCurrent ? 'bg-brand-50/20 dark:bg-brand-500/5' : 'hover:bg-gray-50/60 dark:hover:bg-gray-800/40' ?> transition-colors">
                <!-- Form Budget -->
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-2">
                    <span class="text-base font-black tracking-tight text-gray-900 dark:text-white font-mono">
                      <?= esc($r['year_code']) ?>
                    </span>
                    <?php if ($isCurrent): ?>
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-600 border border-brand-200 dark:bg-brand-500/10 dark:text-brand-400 dark:border-brand-500/20">
                        <i class="fa-solid fa-circle-check text-[9px]"></i> Working Year
                      </span>
                    <?php endif; ?>
                  </div>
                </td>

                <!-- Begin Date -->
                <td class="py-3.5 px-4 text-gray-600 dark:text-gray-300 font-medium">
                  <?= ! empty($r['period_start']) ? date('d M Y', strtotime($r['period_start'])) : '<span class="text-gray-400">-</span>' ?>
                </td>

                <!-- End Date -->
                <td class="py-3.5 px-4 text-gray-600 dark:text-gray-300 font-medium">
                  <?= ! empty($r['period_end']) ? date('d M Y', strtotime($r['period_end'])) : '<span class="text-gray-400">-</span>' ?>
                </td>

                <!-- Cost Center -->
                <td class="py-3.5 px-4 text-center text-gray-600 dark:text-gray-300">
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                    <?= esc($r['cost_center'] ?? 'All Cost Center') ?>
                  </span>
                </td>

                <!-- Action -->
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Set Active Working Year -->
                    <?php if (! $isCurrent): ?>
                      <button
                        type="button"
                        @click="setActiveYear(<?= (int)$r['year_code'] ?>)"
                        class="h-7 px-2 rounded-lg border border-brand-200 bg-brand-50 text-brand-600 hover:bg-brand-100 dark:border-brand-500/30 dark:bg-brand-500/10 dark:text-brand-400 dark:hover:bg-brand-500/20 transition-colors flex items-center gap-1 text-[11px] font-bold shadow-2xs"
                        title="Pilih sebagai Working Year"
                      >
                        <i class="fa-solid fa-arrow-pointer text-[10px]"></i>
                        <span>Pilih</span>
                      </button>
                    <?php endif; ?>

                    <!-- Toggle Lock Button -->
                    <button
                      type="button"
                      @click="toggleLock(<?= (int)$r['year_code'] ?>, <?= $isLocked ? 0 : 1 ?>)"
                      class="h-7 w-7 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 <?= $isLocked ? 'text-blue-600 hover:bg-blue-50 hover:border-blue-200 dark:hover:bg-blue-500/10' : 'text-amber-600 hover:bg-amber-50 hover:border-amber-200 dark:hover:bg-amber-500/10' ?> transition-colors flex items-center justify-center shadow-2xs"
                      title="<?= $isLocked ? 'Buka Kunci Periode' : 'Kunci Periode (Cegah Perubahan Data)' ?>"
                    >
                      <i class="fa-solid <?= $isLocked ? 'fa-lock-open' : 'fa-lock' ?> text-[11px]"></i>
                    </button>

                    <!-- Edit Button -->
                    <button
                      type="button"
                      @click='openEditModal(<?= json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                      class="h-7 w-7 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-brand-50 hover:text-brand-500 hover:border-brand-200 dark:hover:bg-brand-500/10 dark:hover:text-brand-400 transition-colors flex items-center justify-center shadow-2xs"
                      title="Edit Detail Periode"
                    >
                      <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                    </button>

                    <!-- Delete Button -->
                    <?php if (! $isCurrent): ?>
                      <button
                        type="button"
                        @click="deleteYear(<?= (int)$r['year_code'] ?>)"
                        class="h-7 w-7 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-red-600 hover:bg-red-50 hover:border-red-200 dark:hover:bg-red-500/10 transition-colors flex items-center justify-center shadow-2xs"
                        title="Hapus Tahun Anggaran"
                      >
                        <i class="fa-solid fa-trash text-[11px]"></i>
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="border-t border-gray-100 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
      <div class="flex items-center gap-1.5 text-xs">
        <span>Menampilkan</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalItems === 0 ? 0 : ((currentPage - 1) * perPage + 1)"></span>
        <span>-</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, totalItems)"></span>
        <span>dari</span>
        <span class="font-bold text-gray-800 dark:text-gray-200"><?= number_format($totalYears) ?></span>
        <span>tahun anggaran</span>
      </div>

      <div class="flex items-center gap-1" x-show="totalPages > 1">
        <!-- Prev -->
        <button
          type="button"
          @click="prevPage()"
          :disabled="currentPage === 1"
          class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-xs font-semibold flex items-center gap-1"
        >
          <i class="fa-solid fa-chevron-left text-[10px]"></i>
          <span class="hidden sm:inline">Sebelumnya</span>
        </button>

        <!-- Page Numbers -->
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
                class="h-8 min-w-[32px] px-2 rounded-lg text-xs font-semibold transition-colors flex items-center justify-center"
                x-text="p"
              ></button>
            </template>
          </div>
        </template>

        <!-- Next -->
        <button
          type="button"
          @click="nextPage()"
          :disabled="currentPage === totalPages"
          class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-xs font-semibold flex items-center gap-1"
        >
          <span class="hidden sm:inline">Berikutnya</span>
          <i class="fa-solid fa-chevron-right text-[10px]"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- MODAL: TAMBAH / EDIT PERIODE -->
  <!-- ============================================================ -->
  <div
    x-show="modalOpen"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center p-4"
    style="z-index: 9999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
    @click.self="modalOpen = false"
    x-cloak
  >
    <div
      x-show="modalOpen"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95 translate-y-3"
      x-transition:enter-end="opacity-100 scale-100 translate-y-0"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100 scale-100 translate-y-0"
      x-transition:leave-end="opacity-0 scale-95 translate-y-3"
      class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden"
    >
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
            <i class="fa-solid fa-calendar-days text-base"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="isEdit ? 'Edit Tahun Anggaran' : 'Tambah Tahun Anggaran Baru'"></h3>
            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="isEdit ? 'Perbarui rentang tanggal dan pengaturan tahun' : 'Daftarkan tahun anggaran baru untuk penyusunan budget'"></p>
          </div>
        </div>
        <button
          type="button"
          @click="modalOpen = false"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <i class="fa-solid fa-xmark text-base"></i>
        </button>
      </div>

      <!-- Modal Form -->
      <form @submit.prevent="savePeriod()" class="p-6 space-y-4">
        <!-- Year Code -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
            Tahun Anggaran <span class="text-red-500">*</span>
          </label>
          <input
            type="number"
            x-model="form.year_code"
            :readonly="isEdit"
            required
            placeholder="Contoh: <?= date('Y') + 1 ?>"
            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-mono font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            :class="isEdit ? 'opacity-70 cursor-not-allowed' : ''"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Start Date -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Tanggal Mulai Periode
            </label>
            <input
              type="date"
              x-model="form.period_start"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-semibold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <!-- End Date -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Tanggal Akhir Periode
            </label>
            <input
              type="date"
              x-model="form.period_end"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-semibold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
          <!-- Status -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Status <span class="text-red-500">*</span>
            </label>
            <select
              x-model="form.status"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <option value="A">Aktif (A)</option>
              <option value="D">Non-Aktif (D)</option>
            </select>
          </div>

          <!-- Lock Status -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Status Lock (Kunci)
            </label>
            <select
              x-model="form.locked"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <option :value="0">Terbuka (Bisa Entry/Edit)</option>
              <option :value="1">Terkunci (Read-Only)</option>
            </select>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
          <button
            type="button"
            @click="modalOpen = false"
            class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/40 disabled:opacity-50 transition-colors"
          >
            <i class="fa-solid fa-spinner fa-spin" x-show="saving"></i>
            <span x-text="saving ? 'Menyimpan...' : (isEdit ? 'Perbarui Tahun' : 'Simpan Tahun')"></span>
          </button>
        </div>
      </form>
    </div>
  </div>

</div>

<!-- ============================================================ -->
<!-- ALPINE COMPONENT SCRIPT -->
<!-- ============================================================ -->
<script>
  function periodPage() {
    return {
      modalOpen: false,
      isEdit: false,
      saving: false,

      // Pagination
      currentPage: 1,
      perPage: 10,
      totalItems: <?= (int)$totalYears ?>,
      get totalPages() {
        return Math.ceil(this.totalItems / this.perPage) || 1;
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

      form: {
        year_code: '',
        period_start: '',
        period_end: '',
        status: 'A',
        locked: 0
      },

      openCreateModal() {
        this.isEdit = false;
        const nextYear = new Date().getFullYear() + 1;
        this.form = {
          year_code: nextYear,
          period_start: `${nextYear}-01-01`,
          period_end: `${nextYear}-12-31`,
          status: 'A',
          locked: 0
        };
        this.modalOpen = true;
      },

      openEditModal(row) {
        this.isEdit = true;
        this.form = {
          year_code: row.year_code,
          period_start: row.period_start || '',
          period_end: row.period_end || '',
          status: row.status || 'A',
          locked: Number(row.locked) || 0
        };
        this.modalOpen = true;
      },

      async savePeriod() {
        this.saving = true;
        try {
          const res = await window.ypFetch('<?= base_url('master/api/period/save') ?>', this.form);
          if (res.success) {
            window.showToast('success', res.message || 'Tahun anggaran berhasil disimpan');
            this.modalOpen = false;
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal menyimpan tahun anggaran');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan sistem');
        } finally {
          this.saving = false;
        }
      },

      async setActiveYear(yearCode) {
        try {
          const res = await window.ypFetch('<?= base_url('set-year') ?>', { year_code: yearCode });
          if (res.status === 'success' || res.success) {
            window.showToast('success', `Tahun anggaran ${yearCode} berhasil diaktifkan sebagai Working Year.`);
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal mengaktifkan tahun');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan pada server');
        }
      },

      async toggleLock(yearCode, targetLocked) {
        const actionLabel = targetLocked ? 'mengunci (mencegah edit)' : 'membuka kunci';
        if (!window.ypConfirm(`Apakah Anda yakin ingin ${actionLabel} tahun anggaran ${yearCode}?`)) {
          return;
        }

        try {
          const res = await window.ypFetch('<?= base_url('master/api/period/set-locked') ?>', {
            year_code: yearCode,
            locked: targetLocked
          });
          if (res.success) {
            window.showToast('success', res.message || 'Status lock berhasil diubah');
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal mengubah status lock');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan sistem');
        }
      },

      async deleteYear(yearCode) {
        if (!window.ypConfirm(`PERINGATAN: Apakah Anda yakin ingin menghapus tahun anggaran ${yearCode}? Tindakan ini tidak dapat dibatalkan.`)) {
          return;
        }

        try {
          const res = await window.ypFetch('<?= base_url('master/api/period/delete') ?>', {
            year_code: yearCode
          });
          if (res.success) {
            window.showToast('success', res.message || 'Tahun anggaran berhasil dihapus');
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal menghapus tahun');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan sistem');
        }
      }
    };
  }
</script>

<?= $this->endSection() ?>