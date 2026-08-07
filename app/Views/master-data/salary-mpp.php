<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-4" x-data="salaryMppPage()">

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
        <span class="text-brand-500 font-bold">Salary & MPP</span>
      </div>
      <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400">
          <i class="fa-solid fa-users-gear text-base"></i>
        </span>
        Master Salary & Man Power Planning (MPP)
      </h1>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
        Master rate standar gaji per jabatan/posisi, pemetaan departemen, serta klasifikasi Direct Labor, Indirect Labor, dan Staff.
      </p>
    </div>

    <!-- Quick Action Buttons -->
    <div class="flex flex-wrap items-center gap-2.5">
      <a
        href="<?= base_url('master/salary-mpp/export?' . http_build_query($filters ?? [])) ?>"
        class="inline-flex items-center gap-2 rounded-xl border border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-950/40 px-3.5 py-2 text-xs font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-all shadow-xs"
      >
        <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-400"></i>
        <span>Export CSV</span>
      </a>
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
  <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-xl border border-gray-200/80 bg-white px-4 py-2.5 text-xs dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400">
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
      <span class="font-bold text-gray-900 dark:text-white"><?= number_format($activeCount) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-amber-50 text-amber-500 dark:bg-amber-500/10 dark:text-amber-400">
        <i class="fa-solid fa-calendar text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Tahun Anggaran:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= esc($selectedYear) ?></span>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- FILTER & SEARCH BAR & DATA TABLE -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <!-- Filter Bar as table header -->
    <div class="p-3 border-b border-gray-100 dark:border-gray-800">
      <form method="GET" action="<?= base_url('master/salary-mpp') ?>" class="flex flex-wrap items-center gap-2">
        <!-- Search Input -->
        <div class="relative flex-1 min-w-[180px]">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400"></i>
          <input
            type="text"
            name="search"
            value="<?= esc($filters['search'] ?? '') ?>"
            placeholder="Cari nama jabatan, posisi, departemen..."
            class="w-full rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 pl-9 pr-3 text-xs text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-colors"
          />
        </div>

        <!-- Filter Departemen -->
        <select name="dept_id" class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs min-w-[140px] text-gray-800 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors">
          <option value="">Semua Departemen</option>
          <?php foreach (($departments ?? []) as $d): ?>
            <option value="<?= esc($d['id_dept']) ?>" <?= (string)($filters['dept_id'] ?? '') === (string)$d['id_dept'] ? 'selected' : '' ?>>
              <?= esc($d['dept_code']) ?> - <?= esc($d['dept_desc']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <!-- Filter Tipe MPP -->
        <select name="type" class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs min-w-[120px] text-gray-800 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors">
          <option value="">Semua Tipe MPP</option>
          <?php foreach (($mppTypes ?? []) as $t): ?>
            <option value="<?= esc($t['id_mpp']) ?>" <?= (string)($filters['type'] ?? '') === (string)$t['id_mpp'] ? 'selected' : '' ?>>
              <?= esc($t['desc_mpp']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <!-- Filter Tahun -->
        <select name="year" class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs min-w-[100px] text-gray-800 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors">
          <option value="">Semua Tahun</option>
          <?php foreach (($years ?? []) as $y): ?>
            <option value="<?= esc($y) ?>" <?= (string)($filters['year'] ?? '') === (string)$y ? 'selected' : '' ?>>
              <?= esc($y) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <!-- Filter Status -->
        <select name="status" class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs min-w-[100px] text-gray-800 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors">
          <option value="">Semua Status</option>
          <option value="A" <?= ($filters['status'] ?? '') === 'A' ? 'selected' : '' ?>>A</option>
          <option value="D" <?= ($filters['status'] ?? '') === 'D' ? 'selected' : '' ?>>D</option>
        </select>

        <!-- Action Buttons -->
        <button type="submit" class="rounded-lg bg-gray-900 dark:bg-brand-500 py-1.5 px-3 text-xs font-semibold text-white hover:bg-black dark:hover:bg-brand-600 transition-colors" title="Terapkan Filter">
          <i class="fa-solid fa-filter text-[10px]"></i>
        </button>
        <?php if (! empty($has_filter)): ?>
        <a href="<?= base_url('master/salary-mpp') ?>" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-1.5 px-2.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Reset Filter">
          <i class="fa-solid fa-rotate-left text-[10px]"></i>
        </a>
        <?php endif; ?>
      </form>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold capitalize tracking-normal text-gray-500 dark:text-gray-400">
            <th class="py-3 px-4">Nama Staff</th>
            <th class="py-3 px-4 text-center">Tipe Staff</th>
            <th class="py-3 px-4">Cost Center</th>
            <th class="py-3 px-4 text-right">Rate Gaji</th>
            <th class="py-3 px-4 text-right w-20">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="5" class="py-12 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-2">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400">
                    <i class="fa-solid fa-users-slash text-xl"></i>
                  </div>
                  <p class="font-semibold text-gray-600 dark:text-gray-300">Tidak ada data Salary & MPP ditemukan</p>
                  <p class="text-xs text-gray-400">Coba sesuaikan filter pencarian di atas.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($rows as $index => $r): ?>
              <?php
                $tId = (int)($r['type'] ?? 1);
                $typeBadge = 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300';
                if ($tId === 1) {
                    $typeBadge = 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
                } elseif ($tId === 2) {
                    $typeBadge = 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
                } elseif ($tId === 3) {
                    $typeBadge = 'bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20';
                }
              ?>
              <tr x-show="isRowVisible(<?= $index ?>)" class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <!-- Nama Staff -->
                <td class="py-3 px-4 font-bold text-gray-900 dark:text-white">
                  <?= esc($r['desc']) ?>
                </td>

                <!-- Tipe Staff -->
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold <?= $typeBadge ?>">
                    <?= esc($r['type_name'] ?? ('Tipe ' . $r['type'])) ?>
                  </span>
                </td>

                <!-- Cost Center -->
                <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                  <?php if (! empty($r['dept_desc'])): ?>
                    <span class="font-semibold"><?= esc($r['dept_desc']) ?></span>
                    <?php if (! empty($r['dept_code'])): ?>
                      <span class="text-gray-400 text-[11px]">(<?= esc($r['dept_code']) ?>)</span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="text-gray-400">-</span>
                  <?php endif; ?>
                </td>

                <!-- Rate Gaji -->
                <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white">
                  Rp <?= number_format((float)($r['salary'] ?? 0), 0, ',', '.') ?>
                </td>

                <!-- Action (delete only) -->
                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end">
                    <button
                      type="button"
                      @click="toggleStatus(<?= (int)$r['id'] ?>, 'hapus')"
                      class="h-7 w-7 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-red-600 hover:bg-red-50 hover:border-red-200 dark:hover:bg-red-500/10 transition-colors flex items-center justify-center shadow-2xs"
                      title="Hapus Staff MPP"
                    >
                      <i class="fa-solid fa-trash text-[11px]"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Table Footer / Pagination -->
    <?php
      $pages   = max(1, (int) ceil(($total ?? 0) / max(1, $perPage ?? 10)));
      $from    = ($total ?? 0) > 0 ? (($page - 1) * $perPage + 1) : 0;
      $to      = min($page * $perPage, $total ?? 0);
      $winStart = max(1, min($page - 2, max(1, $pages - 4)));
      $winEnd   = min($pages, $winStart + 4);
    ?>
    <?php if (($total ?? 0) > 0): ?>
    <div class="border-t border-gray-100 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
      <span>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200"><?= $from ?></span> - <span class="font-bold text-gray-800 dark:text-gray-200"><?= $to ?></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= number_format($total) ?></span> entri Salary & MPP</span>
      <div class="flex items-center gap-1">
        <a href="?page=<?= max(1, $page - 1) ?>" class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?= $page <= 1 ? 'opacity-40 pointer-events-none' : '' ?> transition-colors text-xs font-semibold flex items-center gap-1"><i class="fa-solid fa-chevron-left text-[10px]"></i><span class="hidden sm:inline">Sebelumnya</span></a>
        <?php for ($p = $winStart; $p <= $winEnd; $p++): ?>
          <a href="?page=<?= $p ?>" class="h-8 min-w-[32px] px-2 rounded-lg text-xs font-semibold transition-colors flex items-center justify-center <?= $p === $page ? 'bg-brand-500 text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>"><?= $p ?></a>
        <?php endfor; ?>
        <a href="?page=<?= min($pages, $page + 1) ?>" class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?= $page >= $pages ? 'opacity-40 pointer-events-none' : '' ?> transition-colors text-xs font-semibold flex items-center gap-1"><span class="hidden sm:inline">Berikutnya</span><i class="fa-solid fa-chevron-right text-[10px]"></i></a>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- ============================================================ -->
  <!-- MODAL: TAMBAH / EDIT SALARY MPP -->
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
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400">
            <i class="fa-solid fa-users-gear text-base"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="form.id ? 'Edit Posisi MPP' : 'Tambah Posisi MPP Baru'"></h3>
            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="form.id ? 'Perbarui rate standar gaji dan relasi departemen' : 'Daftarkan posisi jabatan dan standar gaji baru'"></p>
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
      <form @submit.prevent="saveSalaryMpp()" class="p-6 space-y-4">
        <input type="hidden" x-model="form.id" />

        <!-- Deskripsi / Posisi Jabatan -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
            Deskripsi / Posisi Jabatan <span class="text-red-500">*</span>
          </label>
          <input
            type="text"
            x-model="form.desc"
            required
            placeholder="Contoh: Operator Produksi, Staff QC, Leader Maintenance"
            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Departemen -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Departemen
            </label>
            <select
              x-model="form.dept_id"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
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
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Tipe MPP <span class="text-red-500">*</span>
            </label>
            <select
              x-model="form.type"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <?php foreach (($mppTypes ?? []) as $t): ?>
                <option value="<?= esc($t['id_mpp']) ?>"><?= esc($t['desc_mpp']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Standar Gaji (Rp) -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
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
              class="w-full rounded-xl border border-gray-300 bg-gray-50 pl-10 pr-3.5 py-2.5 text-xs font-mono font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Tahun Anggaran -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Tahun Anggaran <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              x-model="form.year_code"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

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
            <span x-text="saving ? 'Menyimpan...' : (form.id ? 'Perbarui Data' : 'Simpan Data')"></span>
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
  function salaryMppPage() {
    return {
      modalOpen: false,
      saving: false,

      // Pagination
      currentPage: 1,
      perPage: 10,
      totalItems: <?= (int)$totalRows ?>,
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