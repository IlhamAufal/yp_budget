<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6" x-data="salaryMppPage()" x-init="fetchTableData()">

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
        <span class="text-[#2F3185] font-bold">Salary & MPP</span>
      </div>
      <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        Master Salary & Man Power Planning (MPP)
      </h1>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
        Master rate standar gaji per jabatan/posisi, pemetaan departemen, serta klasifikasi Direct Labor, Indirect Labor, dan Staff.
      </p>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- STATS CARDS -->
  <!-- ============================================================ -->
  <?php
    $totalRows    = (int) ($total ?? 0);
    $activeCount  = count(array_filter($rows ?? [], fn($r) => ($r['status'] ?? 'A') === 'A'));
    $totalSalary  = array_sum(array_column($rows ?? [], 'salary'));
    $avgSalary    = $totalRows > 0 ? ($totalSalary / $totalRows) : 0;
    $selectedYear = $filters['year'] ?? session()->get('year_code') ?? date('Y');
  ?>
  <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-2xl border border-gray-200/80 bg-white p-4 text-xs dark:border-gray-800 dark:bg-gray-900 shadow-xs">
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-[#2F3185]/10 text-[#2F3185] dark:bg-[#2F3185]/20 dark:text-indigo-400">
        <i class="fa-solid fa-id-badge text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Total Posisi:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= number_format($totalRows) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
        <i class="fa-solid fa-money-bill-wave text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Rata-rata Gaji:</span>
      <span class="font-bold text-gray-900 dark:text-white">Rp <?= number_format($avgSalary, 0, ',', '.') ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400">
        <i class="fa-solid fa-circle-check text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Posisi Aktif:</span>
      <span class="font-bold text-emerald-600 dark:text-emerald-400"><?= number_format($activeCount) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-amber-50 text-amber-500 dark:bg-amber-500/10 dark:text-amber-400">
        <i class="fa-solid fa-calendar text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Tahun Anggaran:</span>
      <span class="font-bold text-[#2F3185] dark:text-indigo-400"><?= esc($selectedYear) ?></span>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- FORM: MASTER SALARY (Collapsible) -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <button @click="formOpen = !formOpen" class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors cursor-pointer">
      <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
        <i class="fa-solid fa-chevron-right text-xs transition-transform" :class="formOpen ? 'rotate-90' : ''"></i>
        Input Master Salary
      </h3>
    </button>
    <div x-show="formOpen" x-cloak x-collapse class="border-t border-gray-100 dark:border-gray-800">
      <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Nama Staff (multi-select tags) -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nama Staff</label>
            <div class="flex flex-wrap gap-1.5 mb-2" x-show="selectedStaffNames.length > 0">
              <template x-for="(name, idx) in selectedStaffNames" :key="idx">
                <span class="inline-flex items-center gap-1 rounded-lg bg-[#2F3185] px-2.5 py-1 text-xs font-medium text-white shadow-xs">
                  <span x-text="name"></span>
                  <button type="button" @click="selectedStaffNames.splice(idx, 1)" class="hover:text-red-200">&times;</button>
                </span>
              </template>
            </div>
            <div class="relative" @click.away="staffDropdownOpen = false">
              <input type="text" x-model="staffSearch" @focus="staffDropdownOpen = true" placeholder="Pilih atau cari nama jabatan/posisi..." class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 py-2.5 px-3.5 text-xs text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white transition-colors" />
              <div x-show="staffDropdownOpen" class="absolute z-20 mt-1 max-h-48 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800">
                <template x-for="item in filteredStaffList" :key="item.id">
                  <div @click="addStaffName(item.desc)" class="cursor-pointer px-3.5 py-2 text-xs hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200" x-text="item.desc"></div>
                </template>
              </div>
            </div>
          </div>

          <!-- Tipe Staff -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tipe Staff</label>
            <select x-model="formData.type" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 py-2.5 px-3.5 text-xs text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white transition-colors">
              <?php foreach (($mppTypes ?? []) as $t): ?>
                <option value="<?= esc($t['id_mpp']) ?>"><?= esc($t['desc_mpp']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Department (cost_center_sap) -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Department / Cost Center</label>
            <select x-model="formData.dept_id" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 py-2.5 px-3.5 text-xs text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white transition-colors">
              <option value="">-- Pilih --</option>
              <?php foreach (($costCenters ?? []) as $idx => $cc): ?>
                <option value="<?= esc($cc['cost_center']) ?>"><?= ($idx + 1) . '. [' . esc($cc['cost_center_sap']) . '] ' . esc($cc['cost_desc']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Rate Gaji -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Rate Gaji (In IDR Mio)</label>
            <input type="number" step="any" x-model="formData.salary" placeholder="0" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 py-2.5 px-3.5 text-xs font-mono text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white transition-colors" />
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3 pt-3">
          <button type="button" @click="saveFromForm()" :disabled="saving" class="inline-flex items-center gap-2 rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white transition-all shadow-xs disabled:opacity-50 active:scale-[0.98]">
            <i class="fa-solid fa-floppy-disk"></i>
            <span x-text="saving ? 'Menyimpan...' : 'Simpan Rate'"></span>
          </button>
          <a href="<?= base_url('master/salary-mpp/export?' . http_build_query($filters ?? [])) ?>" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 text-xs font-semibold text-white transition-all shadow-xs active:scale-[0.98]">
            <i class="fa-solid fa-file-excel"></i>
            <span>Export Data</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- FILTER CARD -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
    <div class="flex flex-wrap items-center gap-3">
      <div class="flex-1 min-w-[200px]">
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pencarian Posisi</label>
        <input type="text" x-model="filter.search" @input.debounce.400ms="fetchTableData()" placeholder="Cari nama jabatan, posisi..." class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-800 placeholder-gray-400 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white transition-colors shadow-xs" />
      </div>
      <div class="min-w-[180px]">
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Cost Center</label>
        <select x-model="filter.dept_id" @change="fetchTableData()" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-800 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white transition-colors">
          <option value="">Semua Cost Center</option>
          <?php foreach (($costCenters ?? []) as $idx => $cc): ?>
            <option value="<?= esc($cc['cost_center']) ?>">[<?= esc($cc['cost_center_sap']) ?>] <?= esc($cc['cost_desc']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="min-w-[140px]">
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tipe Staff</label>
        <select x-model="filter.type" @change="fetchTableData()" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-800 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white transition-colors">
          <option value="">Semua Tipe</option>
          <?php foreach (($mppTypes ?? []) as $t): ?>
            <option value="<?= esc($t['id_mpp']) ?>"><?= esc($t['desc_mpp']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- ASYNC DATA TABLE -->
  <!-- ============================================================ -->
  <div class="space-y-4">
    <div>
      <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Daftar Rate Gaji & Posisi MPP</h3>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar seluruh standar rate gaji per posisi dan departemen.</p>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
      <!-- Table -->
      <div class="overflow-x-auto scrollbar-thin">
        <table class="w-full text-left border-collapse text-xs text-gray-600 dark:text-gray-300 min-w-[700px]">
          <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
            <tr class="bg-[#2F3185] text-white font-semibold text-xs">
              <th class="py-3 px-4 text-white font-semibold">Nama Staff</th>
              <th class="py-3 px-4 text-center text-white font-semibold">Tipe Staff</th>
              <th class="py-3 px-4 text-white font-semibold">Cost Center</th>
              <th class="py-3 px-4 text-right text-white font-semibold">Rate Gaji (In IDR Mio)</th>
              <th class="py-3 px-4 text-right w-20 text-white font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs">
            <tr x-show="tableLoading">
              <td colspan="5" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                    <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data...</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sistem sedang memproses data.</p>
                </div>
              </td>
            </tr>
            <tr x-show="!tableLoading && tableRows.length === 0">
              <td colspan="5" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                    <i class="fa-solid fa-users-gear text-xl"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Posisi / Rate Gaji</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Coba ubah kata kunci pencarian atau filter di atas.</p>
                </div>
              </td>
            </tr>
            <template x-for="(row, idx) in tableRows" :key="row.id || idx">
              <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <td class="py-3 px-4 font-medium text-gray-900 dark:text-white" x-text="row.desc"></td>
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="typeBadgeClass(row.type)" x-text="row.type_name || ('Tipe ' + row.type)"></span>
                </td>
                <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                  <span class="font-medium" x-text="row.cost_center_sap ? '[' + row.cost_center_sap + '] ' + (row.cost_desc || '') : (row.dept_desc || '-')"></span>
                </td>
                <td class="py-3 px-4 text-right font-mono text-xs font-semibold text-gray-900 dark:text-white" x-text="fmtRupiah(row.salary)"></td>
                <td class="py-3 px-4 text-right">
                  <button type="button" @click="toggleStatus(row.id, 'hapus')" class="h-7 w-7 rounded-lg inline-flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 transition-colors shadow-xs" title="Hapus">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                  </button>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div x-show="tableRows.length > 0" class="border-t border-gray-200 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 text-xs text-gray-500 dark:text-gray-400">
        <span>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="tableRows.length"></span> baris data</span>
      </div>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- MODAL: TAMBAH / EDIT SALARY MPP -->
  <!-- ============================================================ -->
  <template x-teleport="body">
  <div
    x-show="modalOpen"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center p-4 z-[9999999]"
    style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(4px);"
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
      <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-800">
        <div>
          <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="form.id ? 'Edit Posisi MPP' : 'Tambah Posisi MPP Baru'"></h3>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="form.id ? 'Perbarui rate standar gaji dan relasi departemen' : 'Daftarkan posisi jabatan dan standar gaji baru'"></p>
        </div>
        <button
          type="button"
          @click="modalOpen = false"
          class="h-8 w-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>
      </div>

      <!-- Modal Form -->
      <form @submit.prevent="saveSalaryMpp()" class="p-6 space-y-4">
        <input type="hidden" x-model="form.id" />

        <!-- Deskripsi / Posisi Jabatan -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Deskripsi / Posisi Jabatan <span class="text-red-500">*</span>
          </label>
          <input
            type="text"
            x-model="form.desc"
            required
            placeholder="Contoh: Operator Produksi, Staff QC, Leader Maintenance"
            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-medium text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Departemen -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
              Departemen
            </label>
            <select
              x-model="form.dept_id"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-semibold text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <option value="">Pilih Departemen</option>
              <?php foreach (($departments ?? []) as $d): ?>
                <option value="<?= esc($d['id_dept']) ?>">
                  <?= esc($d['dept_code']) ?> - <?= esc($d['dept_desc']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Tipe MPP -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
              Tipe MPP <span class="text-red-500">*</span>
            </label>
            <select
              x-model="form.type"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-semibold text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <?php foreach (($mppTypes ?? []) as $t): ?>
                <option value="<?= esc($t['id_mpp']) ?>"><?= esc($t['desc_mpp']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Standar Gaji (Rp) -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Standar Gaji / Rate Bulanan (Rp) <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">Rp</span>
            <input
              type="number"
              step="any"
              x-model="form.salary"
              required
              placeholder="0"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 pl-10 pr-3.5 py-2.5 text-xs font-mono font-bold text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Tahun Anggaran -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
              Tahun Anggaran <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              x-model="form.year_code"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-semibold text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <!-- Status -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
              Status <span class="text-red-500">*</span>
            </label>
            <select
              x-model="form.status"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-semibold text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <option value="A">Aktif (A)</option>
              <option value="D">Non-Aktif (D)</option>
            </select>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100 dark:border-gray-800">
          <button
            type="button"
            @click="modalOpen = false"
            class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs focus:outline-none disabled:opacity-50 transition-all active:scale-[0.98]"
          >
            <i class="fa-solid fa-spinner fa-spin" x-show="saving"></i>
            <span x-text="saving ? 'Menyimpan...' : (form.id ? 'Perbarui Data' : 'Simpan Data')"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
  </template>

</div>

<!-- ============================================================ -->
<!-- ALPINE COMPONENT SCRIPT -->
<!-- ============================================================ -->
<script>
  function salaryMppPage() {
    return {
      modalOpen: false,
      saving: false,
      formOpen: true,

      // Async table
      filter: { search: '', dept_id: '', type: '' },
      tableRows: [],
      tableLoading: false,

      // Master Salary Form
      selectedStaffNames: [],
      staffSearchQuery: '',
      staffNameOptions: <?= json_encode($staffNames ?? [], JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP) ?>,
      formData: {
        type: '<?= ($mppTypes[0]['id_mpp'] ?? 1) ?>',
        dept_id: '',
        salary: 0
      },

      get filteredStaffOptions() {
        const q = (this.staffSearchQuery || '').toLowerCase();
        return this.staffNameOptions.filter(n =>
          n.toLowerCase().includes(q) && !this.selectedStaffNames.includes(n)
        ).slice(0, 20);
      },

      addStaffName(name) {
        if (!this.selectedStaffNames.includes(name)) {
          this.selectedStaffNames.push(name);
        }
        this.staffSearchQuery = '';
      },

      async saveFromForm() {
        if (this.selectedStaffNames.length === 0) {
          window.showToast('error', 'Pilih minimal satu nama staff.');
          return;
        }
        if (!this.formData.dept_id) {
          window.showToast('error', 'Pilih department.');
          return;
        }
        this.saving = true;
        try {
          const body = new FormData();
          this.selectedStaffNames.forEach(n => body.append('names[]', n));
          body.append('type', this.formData.type);
          body.append('dept_id', this.formData.dept_id);
          body.append('salary', this.formData.salary);
          body.append('year_code', '<?= $selectedYear ?>');
          // Attach CSRF
          const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.content || 'csrf_test_name';
          const csrfHash = document.querySelector('meta[name="csrf-hash"]')?.content || (document.cookie.match(/csrf_cookie_name=([^;]+)/)?.[1] || '');
          if (csrfHash) body.append(csrfName, decodeURIComponent(csrfHash));

          const res = await window.ypFetch('<?= base_url('master/api/salary-mpp/save') ?>', body);
          if (res.success) {
            window.showToast('success', res.message || 'Data berhasil disimpan');
            this.selectedStaffNames = [];
            this.formData.salary = 0;
            this.fetchTableData();
          } else {
            window.showToast('error', res.message || 'Gagal menyimpan data');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan sistem');
        } finally {
          this.saving = false;
        }
      },

      // Table helpers
      async fetchTableData() {
        this.tableLoading = true;
        try {
          const params = new URLSearchParams({
            search: this.filter.search,
            dept_id: this.filter.dept_id,
            type: this.filter.type,
            year: '<?= $selectedYear ?>',
            status: 'A'
          });
          const res = await fetch(`<?= base_url('master/api/salary-mpp/data') ?>?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          });
          const json = await res.json();
          this.tableRows = json.data || [];
        } catch (e) {
          console.error('Error fetching salary data:', e);
          this.tableRows = [];
        } finally {
          this.tableLoading = false;
        }
      },

      typeBadgeClass(type) {
        const t = parseInt(type);
        if (t === 1) return 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
        if (t === 2) return 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
        if (t === 3) return 'bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20';
        return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300';
      },

      fmtRupiah(val) {
        return new Intl.NumberFormat('id-ID', { style: 'decimal', maximumFractionDigits: 2 }).format(parseFloat(val) || 0);
      },

      form: {
        id: null,
        desc: '',
        dept_id: '',
        type: 1,
        salary: 0,
        year_code: '<?= $selectedYear ?>',
        status: 'A'
      },

      openCreateModal() {
        this.form = {
          id: null,
          desc: '',
          dept_id: '',
          type: 1,
          salary: 0,
          year_code: '<?= $selectedYear ?>',
          status: 'A'
        };
        this.modalOpen = true;
      },

      openEditModal(row) {
        this.form = {
          id: row.id,
          desc: row.desc || '',
          dept_id: row.dept_id || '',
          type: row.type || 1,
          salary: row.salary || 0,
          year_code: row.year_code || '<?= $selectedYear ?>',
          status: row.status || 'A'
        };
        this.modalOpen = true;
      },

      async saveSalaryMpp() {
        this.saving = true;
        try {
          const res = await window.ypFetch('<?= base_url('master/api/salary-mpp/save') ?>', this.form);
          if (res.success) {
            window.showToast('success', res.message || 'Salary MPP berhasil disimpan');
            this.modalOpen = false;
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal menyimpan Salary MPP');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan sistem');
        } finally {
          this.saving = false;
        }
      },

      async toggleStatus(id, actionText) {
        if (!window.ypConfirm(`Apakah Anda yakin ingin ${actionText} data Salary MPP ini?`)) {
          return;
        }

        try {
          const res = await window.ypFetch('<?= base_url('master/api/salary-mpp/toggle') ?>', { id });
          if (res.success) {
            window.showToast('success', res.message || 'Status berhasil diubah');
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal mengubah status');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan saat memproses permintaan');
        }
      }
    };
  }
</script>

<?= $this->endSection() ?>