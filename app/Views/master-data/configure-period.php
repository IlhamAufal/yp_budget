<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6" x-data="periodPage()">

  <!-- ============================================================ -->
  <!-- BREADCRUMB & HEADER -->
  <!-- ============================================================ -->
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span class="hover:text-[#2F3185]">Master Data</span>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span class="text-[#2F3185] font-bold">Configure Period</span>
      </div>
      <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        Configure Period
      </h1>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
        Konfigurasi periode buka/tutup form budget per tipe dan cost center.
      </p>
    </div>

    <div class="flex flex-wrap items-center gap-2 text-xs">
      <span class="text-gray-500 dark:text-gray-400 font-medium">Tahun Anggaran Aktif:</span>
      <span class="font-bold text-[#2F3185] dark:text-indigo-400 text-sm"><?= esc($currentWorkingYear ?? date('Y')) ?></span>
    </div>
  </div>

  <?php
    $totalYears  = (int) ($total ?? 0);
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

  <!-- ============================================================ -->
  <!-- CONFIGURE FORM -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40">
      <h2 class="text-sm font-bold text-gray-900 dark:text-white">
        Tambah Konfigurasi Periode
      </h2>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tentukan form budget, rentang waktu pengisian, dan cost center yang diizinkan.</p>
    </div>

    <form @submit.prevent="saveUpload()" class="p-6 space-y-4">

      <!-- Form Budget -->
      <div class="grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-2 sm:gap-4 items-start">
        <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 pt-2">
          Form Budget <span class="text-red-500">*</span>
        </label>
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
          <div
            class="flex flex-wrap gap-1.5 min-h-[38px] p-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus-within:border-[#2F3185] focus-within:bg-white dark:focus-within:bg-gray-900 focus-within:ring-2 focus-within:ring-[#2F3185]/20 transition-all cursor-pointer shadow-xs"
            @click="open = !open"
          >
            <template x-if="uploadForm.form_budget.length === 0">
              <span class="text-xs text-gray-400 py-0.5">-- Pilih Form Budget --</span>
            </template>
            <template x-for="val in uploadForm.form_budget" :key="val">
              <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#2F3185] text-white text-xs font-semibold shadow-xs">
                <span x-text="availableForms.find(f => f.val === val)?.label || val"></span>
                <button type="button" @click.stop="toggleFormBudget(val)" class="text-white/70 hover:text-white transition-colors">
                  <i class="fa-solid fa-xmark text-[10px]"></i>
                </button>
              </span>
            </template>
          </div>

          <div
            x-show="open"
            x-transition
            class="absolute z-50 w-full sm:w-[400px] mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1"
          >
            <template x-for="item in availableForms" :key="item.val">
              <label class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors">
                <input type="checkbox" :checked="uploadForm.form_budget.includes(item.val)" @change="toggleFormBudget(item.val)" class="rounded border-gray-300 text-[#2F3185] focus:ring-[#2F3185]">
                <span x-text="item.label"></span>
              </label>
            </template>
          </div>
        </div>
      </div>

      <!-- Tahun Anggaran -->
      <div class="grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-2 sm:gap-4 items-center">
        <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">
          Tahun Anggaran <span class="text-red-500">*</span>
        </label>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
          <input
            type="number"
            x-model.number="uploadForm.year_code"
            @change="syncPeriodToYear()"
            required
            min="2000"
            max="2100"
            class="max-w-[180px] rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs font-semibold text-gray-900 dark:text-gray-100 focus:border-[#2F3185] focus:bg-white dark:focus:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 transition-all shadow-xs"
          />
          <span class="text-xs text-gray-400">Periode otomatis mengikuti tahun ini</span>
        </div>
      </div>

      <!-- Period Datetime -->
      <div class="grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-2 sm:gap-4 items-center">
        <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Period Datetime</label>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
          <input
            type="datetime-local"
            x-model="uploadForm.period_start"
            class="flex-1 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs font-medium text-gray-900 dark:text-gray-100 focus:border-[#2F3185] focus:bg-white dark:focus:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 transition-all shadow-xs"
          />
          <span class="text-xs font-bold text-gray-400 self-center">s/d</span>
          <input
            type="datetime-local"
            x-model="uploadForm.period_end"
            class="flex-1 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs font-medium text-gray-900 dark:text-gray-100 focus:border-[#2F3185] focus:bg-white dark:focus:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 transition-all shadow-xs"
          />
        </div>
      </div>

      <!-- Cost Center -->
      <div class="grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-2 sm:gap-4 items-start">
        <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 pt-2">Cost Center</label>
        <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false">
          <div
            class="flex flex-wrap gap-1.5 min-h-[38px] p-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus-within:border-[#2F3185] focus-within:bg-white dark:focus-within:bg-gray-900 focus-within:ring-2 focus-within:ring-[#2F3185]/20 transition-all cursor-pointer shadow-xs"
            @click="open = !open"
          >
            <template x-if="uploadForm.cost_center.length === 0">
              <span class="text-xs text-gray-400 py-0.5">-- Pilih Cost Center --</span>
            </template>
            <template x-if="uploadForm.cost_center.includes('*')">
              <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#2F3185] text-white text-xs font-semibold shadow-xs">
                ALL COST CENTER
                <button type="button" @click.stop="toggleCostCenter('*')" class="text-white/70 hover:text-white transition-colors">
                  <i class="fa-solid fa-xmark text-[10px]"></i>
                </button>
              </span>
            </template>
            <template x-if="!uploadForm.cost_center.includes('*')">
              <template x-for="cc in uploadForm.cost_center" :key="cc">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#2F3185]/10 text-[#2F3185] dark:bg-[#2F3185]/20 dark:text-indigo-300 text-xs font-semibold border border-[#2F3185]/20">
                  <span x-text="cc"></span>
                  <button type="button" @click.stop="toggleCostCenter(cc)" class="text-[#2F3185] hover:text-[#25276d] transition-colors">
                    <i class="fa-solid fa-xmark text-[9px]"></i>
                  </button>
                </span>
              </template>
            </template>
          </div>

          <div
            x-show="open"
            x-transition
            class="absolute z-50 w-full sm:w-[480px] mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl"
          >
            <div class="p-2.5 border-b border-gray-100 dark:border-gray-700">
              <input
                type="text"
                x-model="search"
                placeholder="Cari cost center..."
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 px-3 py-1.5 text-xs focus:border-[#2F3185] focus:ring-1 focus:ring-[#2F3185] outline-none"
              >
            </div>

            <div class="overflow-y-auto max-h-60 p-1">
              <label class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer rounded-lg">
                <input type="checkbox" :checked="uploadForm.cost_center.includes('*')" @change="toggleAllCostCenters()" class="rounded border-gray-300 text-[#2F3185] focus:ring-[#2F3185]">
                <span>ALL COST CENTER</span>
              </label>

              <div class="h-px bg-gray-100 dark:bg-gray-700 my-1"></div>

              <?php foreach (($cc ?? []) as $idx => $dept): ?>
              <label
                x-show="search === '' || '[<?= esc($dept['cost_center_sap']) ?>] <?= esc($dept['cost_desc']) ?>'.toLowerCase().includes(search.toLowerCase())"
                class="flex items-center gap-2 px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer rounded-lg transition-colors"
              >
                <input type="checkbox" :checked="uploadForm.cost_center.includes('<?= esc($dept['cost_center_sap']) ?>')" @change="toggleCostCenter('<?= esc($dept['cost_center_sap']) ?>')" class="rounded border-gray-300 text-[#2F3185] focus:ring-[#2F3185]">
                <span><?= ($idx + 1) ?>. [<?= esc($dept['cost_center_sap']) ?>] <?= esc($dept['cost_desc']) ?></span>
              </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-2 sm:gap-4 pt-2">
        <div></div>
        <div>
          <button
            type="submit"
            :disabled="savingUpload || uploadForm.form_budget.length === 0"
            class="inline-flex items-center gap-2 rounded-xl bg-[#2F3185] hover:bg-[#25276d] text-white px-5 py-2.5 text-xs font-semibold shadow-xs focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed active:scale-[0.98] transition-all cursor-pointer"
          >
            <i class="fa-solid fa-spinner fa-spin" x-show="savingUpload"></i>
            <i class="fa-solid fa-floppy-disk" x-show="!savingUpload"></i>
            <span>Simpan Konfigurasi</span>
          </button>
        </div>
      </div>

    </form>
  </div>

  <!-- ============================================================ -->
  <!-- DATA TABLE -->
  <!-- ============================================================ -->
  <div class="space-y-4">
    <div>
      <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Daftar Konfigurasi Periode</h3>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar seluruh jadwal pembukaan dan penutupan pengisian budget.</p>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs text-gray-600 dark:text-gray-300">
          <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
            <tr class="bg-[#2F3185] text-white font-semibold text-xs">
              <th class="py-3 px-4 text-white font-semibold">Form Budget</th>
              <th class="py-3 px-4 text-white font-semibold">Begin Date</th>
              <th class="py-3 px-4 text-white font-semibold">End Date</th>
              <th class="py-3 px-4 text-white font-semibold">Cost Center</th>
              <th class="py-3 px-4 text-center w-20 text-white font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs">
            <?php if (empty($rows)): ?>
              <tr>
                <td colspan="5" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                      <i class="fa-solid fa-calendar-days text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum ada data konfigurasi periode</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tambahkan konfigurasi melalui form di atas</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($rows as $index => $r): ?>
                <?php $isCurrent = (int)($r['year_code'] ?? 0) === (int)$currentWorkingYear; ?>
                <tr class="<?= $isCurrent ? 'bg-[#2F3185]/5 dark:bg-[#2F3185]/10' : 'hover:bg-gray-50/60 dark:hover:bg-gray-800/40' ?> transition-colors">

                  <td class="py-3 px-4">
                    <div class="flex flex-wrap gap-1">
                      <?php
                        $forms = !empty($r['form_budget']) ? explode(',', $r['form_budget']) : ['ALL FORMS'];
                        foreach ($forms as $f):
                      ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                          <?= esc(trim($f)) ?>
                        </span>
                      <?php endforeach; ?>
                    </div>
                  </td>

                  <td class="py-3 px-4 text-gray-600 dark:text-gray-300 font-medium whitespace-nowrap">
                    <?= !empty($r['period_start']) ? date('d M Y, H:i', strtotime($r['period_start'])) : '<span class="text-gray-400">-</span>' ?>
                  </td>

                  <td class="py-3 px-4 text-gray-600 dark:text-gray-300 font-medium whitespace-nowrap">
                    <?= !empty($r['period_end']) ? date('d M Y, H:i', strtotime($r['period_end'])) : '<span class="text-gray-400">-</span>' ?>
                  </td>

                  <td class="py-3 px-4">
                    <?php if (($r['cost_center'] ?? '*') === '*'): ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <i class="fa-solid fa-building text-[10px]"></i> ALL COST CENTER
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#2F3185]/10 text-[#2F3185] dark:bg-[#2F3185]/20 dark:text-indigo-300 border border-[#2F3185]/20">
                        <?= esc($r['cost_center']) ?>
                      </span>
                    <?php endif; ?>
                  </td>

                  <td class="py-3 px-4 text-center">
                    <button
                      type="button"
                      @click="deleteYear(<?= (int)($r['year_code'] ?? 0) ?>)"
                      class="h-7 w-7 rounded-lg inline-flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 transition-colors shadow-xs"
                      title="Hapus"
                    >
                      <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                  </td>

                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php
        $pages   = max(1, (int) ceil(($total ?? 0) / max(1, $perPage ?? 10)));
        $from    = ($total ?? 0) > 0 ? (($page - 1) * $perPage + 1) : 0;
        $to      = min($page * $perPage, $total ?? 0);
        $winStart = max(1, min($page - 2, max(1, $pages - 4)));
        $winEnd   = min($pages, $winStart + 4);
      ?>
      <?php if (($total ?? 0) > 0): ?>
      <div class="border-t border-gray-200 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
        <span>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200"><?= $from ?></span> - <span class="font-bold text-gray-800 dark:text-gray-200"><?= $to ?></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= number_format($total) ?></span> tahun anggaran</span>
        <div class="flex items-center gap-1">
          <a href="?page=<?= max(1, $page - 1) ?>" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?= $page <= 1 ? 'opacity-40 pointer-events-none' : '' ?> transition-colors text-xs font-semibold flex items-center gap-1"><i class="fa-solid fa-chevron-left text-[10px]"></i><span class="hidden sm:inline">Sebelumnya</span></a>
          <?php for ($p = $winStart; $p <= $winEnd; $p++): ?>
            <a href="?page=<?= $p ?>" class="h-8 min-w-[32px] px-2.5 rounded-lg text-xs font-semibold transition-colors flex items-center justify-center <?= $p === $page ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>"><?= $p ?></a>
          <?php endfor; ?>
          <a href="?page=<?= min($pages, $page + 1) ?>" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?= $page >= $pages ? 'opacity-40 pointer-events-none' : '' ?> transition-colors text-xs font-semibold flex items-center gap-1"><span class="hidden sm:inline">Berikutnya</span><i class="fa-solid fa-chevron-right text-[10px]"></i></a>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- ============================================================ -->
<!-- ALPINE JS -->
<!-- ============================================================ -->
<script>
function periodPage() {
  return {
    savingUpload: false,
    currentPage: 1,
    perPage: 10,
    totalItems: <?= (int) ($total ?? 0) ?>,

    availableForms: [
      { val: 'OPEX_GA', label: 'OPEX - GA' },
      { val: 'OPEX_SELLING', label: 'OPEX - SELLING' },
      { val: 'FOH', label: 'FOH' },
      { val: 'MPP', label: 'HEADCOUNT / MPP' },
      { val: 'CAPEX', label: 'CAPEX' }
    ],

    uploadForm: {
      form_budget: [],
      cost_center: ['*'],
      year_code: new Date().getFullYear() + 1,
      period_start: '',
      period_end: ''
    },

    init() {
      this.syncPeriodToYear();
    },

    syncPeriodToYear() {
      const y = this.uploadForm.year_code || new Date().getFullYear();
      this.uploadForm.period_start = `${y}-01-01T00:00`;
      this.uploadForm.period_end = `${y}-12-31T23:59`;
    },

    toggleFormBudget(val) {
      const idx = this.uploadForm.form_budget.indexOf(val);
      if (idx === -1) {
        this.uploadForm.form_budget.push(val);
      } else {
        this.uploadForm.form_budget.splice(idx, 1);
      }
    },

    toggleCostCenter(val) {
      const idx = this.uploadForm.cost_center.indexOf(val);
      if (idx === -1) {
        // Pilih CC spesifik → lepas 'ALL COST CENTER' agar tidak dobel
        this.uploadForm.cost_center = this.uploadForm.cost_center.filter(v => v !== '*');
        this.uploadForm.cost_center.push(val);
      } else {
        this.uploadForm.cost_center.splice(idx, 1);
      }
      if (this.uploadForm.cost_center.length === 0) {
        this.uploadForm.cost_center = ['*'];
      }
    },

    toggleAllCostCenters() {
      const allIdx = this.uploadForm.cost_center.indexOf('*');
      if (allIdx !== -1) {
        this.uploadForm.cost_center = [];
      } else {
        this.uploadForm.cost_center = ['*'];
      }
    },

    async deleteYear(yearCode) {
      if (!await window.ypConfirm('Apakah Anda yakin ingin menghapus konfigurasi ini?')) return;
      try {
        const res = await window.ypFetch('<?= base_url('master/api/period/delete') ?>', { year_code: yearCode });
        if (res.success) {
          window.showToast('success', res.message || 'Konfigurasi berhasil dihapus');
          setTimeout(() => window.location.reload(), 600);
        } else {
          window.showToast('error', res.message || 'Gagal menghapus konfigurasi');
        }
      } catch (e) {
        window.showToast('error', 'Terjadi kesalahan sistem');
      }
    },

    async saveUpload() {
      this.savingUpload = true;
      try {
        const payload = {
          year_code: this.uploadForm.year_code,
          form_budget: this.uploadForm.form_budget.join(','),
          cost_center: this.uploadForm.cost_center.join(','),
          period_start: this.uploadForm.period_start,
          period_end: this.uploadForm.period_end,
          status: 'A',
          locked: 0
        };

        const res = await window.ypFetch('<?= base_url('master/api/period/save') ?>', payload);
        if (res.success) {
          window.showToast('success', res.message || 'Konfigurasi berhasil disimpan');
          setTimeout(() => window.location.reload(), 600);
        } else {
          window.showToast('error', res.message || 'Gagal menyimpan konfigurasi');
        }
      } catch (e) {
        window.showToast('error', 'Terjadi kesalahan sistem');
      } finally {
        this.savingUpload = false;
      }
    }
  };
}
</script>

<?= $this->endSection() ?>
