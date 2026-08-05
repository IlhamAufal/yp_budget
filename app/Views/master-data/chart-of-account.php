<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-4" x-data="coaPage()">

  <!-- ============================================================ -->
  <!-- BREADCRUMB & HEADER -->
  <!-- ============================================================ -->
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors">
          <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span>Master Data</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-brand-500 font-bold">Chart of Account (COA)</span>
      </div>
      <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400">
          <i class="fa-solid fa-book-bookmark text-lg"></i>
        </span>
        Chart of Account (COA)
      </h1>
      <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
        Pengelolaan akun anggaran untuk modul FOH, OPEX GA, OPEX Selling, dan CAPEX.
      </p>
    </div>

    <!-- Quick Action Buttons -->
    <div class="flex flex-wrap items-center gap-2.5">
      <button
        type="button"
        @click="openCopyModal()"
        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 transition-all shadow-xs"
      >
        <i class="fa-solid fa-copy text-brand-500"></i>
        <span>Salin Antar Tahun</span>
      </button>

      <a
        href="<?= base_url('master/coa/export?' . http_build_query($filters ?? [])) ?>"
        class="inline-flex items-center gap-2 rounded-xl border border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-950/40 px-4 py-2.5 text-xs font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-all shadow-xs"
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
    $totalRows    = count($rows ?? []);
    $activeCount  = count(array_filter($rows ?? [], fn($r) => ($r['status'] ?? 'A') === 'A'));
    $inactiveCount = $totalRows - $activeCount;
    $selectedYear = $filters['year'] ?? session()->get('year_code') ?? date('Y');
  ?>
  <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-xl border border-gray-200/80 bg-white px-4 py-2.5 text-xs dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-50 text-blue-500 dark:bg-blue-500/10 dark:text-blue-400">
        <i class="fa-solid fa-list-ol text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Total Akun:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= number_format($totalRows) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400">
        <i class="fa-solid fa-circle-check text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Aktif:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= number_format($activeCount) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>

    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
        <i class="fa-solid fa-circle-pause text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Non-Aktif:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= number_format($inactiveCount) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>

    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-amber-50 text-amber-500 dark:bg-amber-500/10 dark:text-amber-400">
        <i class="fa-solid fa-calendar text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Tahun:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= esc($selectedYear) ?></span>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- DATA TABLE & FILTER -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <!-- Filter Bar -->
    <div class="p-3 border-b border-gray-100 dark:border-gray-800">
      <form method="GET" action="<?= base_url('master/coa') ?>" class="flex flex-wrap items-center gap-2">
        <div class="relative flex-1 min-w-[180px]">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400"></i>
          <input
            type="text"
            name="search"
            value="<?= esc($filters['search'] ?? '') ?>"
            placeholder="Cari nomor akun atau nama..."
            class="w-full rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 pl-8 pr-3 text-xs focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
          />
        </div>

        <select
          name="type"
          class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs min-w-[120px] focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
        >
          <option value="">Semua Tipe</option>
          <?php foreach (($types ?? []) as $t): ?>
            <option value="<?= esc($t) ?>" <?= ($filters['type'] ?? '') === $t ? 'selected' : '' ?>>
              <?= esc($t) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <select
          name="year"
          class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs min-w-[110px] focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
        >
          <option value="">Semua Tahun</option>
          <?php foreach (($years ?? []) as $y): ?>
            <option value="<?= esc($y) ?>" <?= (string)($filters['year'] ?? '') === (string)$y ? 'selected' : '' ?>>
              <?= esc($y) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <select
          name="status"
          class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs min-w-[120px] focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
        >
          <option value="">Semua Status</option>
          <option value="A" <?= ($filters['status'] ?? '') === 'A' ? 'selected' : '' ?>>Aktif (A)</option>
          <option value="D" <?= ($filters['status'] ?? '') === 'D' ? 'selected' : '' ?>>Non-Aktif (D)</option>
        </select>

        <button
          type="submit"
          class="rounded-lg bg-gray-900 dark:bg-brand-500 py-1.5 px-3 text-xs font-semibold text-white hover:bg-black dark:hover:bg-brand-600 transition-colors"
          title="Terapkan Filter"
        >
          <i class="fa-solid fa-filter text-[10px]"></i>
        </button>
        <a
          href="<?= base_url('master/coa') ?>"
          class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-1.5 px-2.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          title="Reset Filter"
        >
          <i class="fa-solid fa-rotate-left text-[10px]"></i>
        </a>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold capitalize tracking-normal text-gray-500 dark:text-gray-400">
            <th class="py-3.5 px-4 w-12 text-center">No.</th>
            <th class="py-3.5 px-4">Main Account</th>
            <th class="py-3.5 px-4">Header</th>
            <th class="py-3.5 px-4">Name</th>
            <th class="py-3.5 px-4">Category</th>
            <th class="py-3.5 px-4 text-right w-20">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="6" class="py-16 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-3">
                  <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400">
                    <i class="fa-solid fa-folder-open text-2xl"></i>
                  </div>
                  <p class="font-semibold text-gray-600 dark:text-gray-300">Tidak ada data akun COA ditemukan</p>
                  <p class="text-xs text-gray-400">Coba ubah kata kunci pencarian atau reset filter di atas.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($rows as $index => $r): ?>
              <tr x-show="isRowVisible(<?= $index ?>)" class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <!-- No. -->
                <td class="py-3.5 px-4 text-center text-gray-400 font-medium"><?= $index + 1 ?></td>

                <!-- Main Account -->
                <td class="py-3.5 px-4 font-mono font-bold text-gray-900 dark:text-white">
                  <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                    <i class="fa-solid fa-hashtag text-[10px] text-gray-400"></i>
                    <?= esc($r['main_account']) ?>
                  </span>
                </td>

                <!-- Header -->
                <td class="py-3.5 px-4 text-gray-600 dark:text-gray-400">
                  <?= esc($r['cost_center_header'] ?: '-') ?>
                </td>

                <!-- Name -->
                <td class="py-3.5 px-4 font-semibold text-gray-800 dark:text-gray-100">
                  <div><?= esc($r['cost_center_desc'] ?: '-') ?></div>
                  <?php if (! empty($r['cost_center_sub'])): ?>
                    <div class="text-[11px] font-normal text-gray-400 dark:text-gray-500 mt-0.5">
                      Sub: <?= esc($r['cost_center_sub']) ?>
                    </div>
                  <?php endif; ?>
                </td>

                <!-- Category -->
                <td class="py-3.5 px-4 text-gray-600 dark:text-gray-300">
                  <?= esc($r['category'] ?: ($r['type'] ?: '-')) ?>
                </td>

                <!-- Action (delete only) -->
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end">
                    <button
                      type="button"
                      @click="toggleStatus(<?= (int)$r['id_cost_center'] ?>, 'hapus')"
                      class="h-8 w-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-red-600 hover:bg-red-50 hover:border-red-200 dark:hover:bg-red-500/10 transition-colors flex items-center justify-center shadow-2xs"
                      title="Hapus Akun"
                    >
                      <i class="fa-solid fa-trash text-xs"></i>
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
    <div class="border-t border-gray-100 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
      <div class="flex items-center gap-1.5 text-xs">
        <span>Menampilkan</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalItems === 0 ? 0 : ((currentPage - 1) * perPage + 1)"></span>
        <span>-</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, totalItems)"></span>
        <span>dari</span>
        <span class="font-bold text-gray-800 dark:text-gray-200"><?= number_format($totalRows) ?></span>
        <span>akun COA</span>
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
  <!-- MODAL: TAMBAH / EDIT AKUN COA -->
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
      class="relative w-full max-w-xl rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden"
    >
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400">
            <i class="fa-solid fa-book-bookmark text-base"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="form.id ? 'Edit Akun COA' : 'Tambah Akun COA Baru'"></h3>
            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="form.id ? 'Perbarui detail data chart of account' : 'Isi formulir untuk mendaftarkan akun COA baru'"></p>
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
      <form @submit.prevent="saveCoa()" class="p-6 space-y-5">
        <input type="hidden" x-model="form.id" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <!-- Main Account Number -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Nomor Akun Utama <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              x-model="form.main_account"
              required
              placeholder="Contoh: 510101"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-mono font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <!-- SAP Code (External) -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Kode SAP / External
            </label>
            <input
              type="text"
              x-model="form.id_acct_ext"
              placeholder="Contoh: SAP-5101"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-mono text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <!-- Description / Name -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
            Nama / Deskripsi Akun <span class="text-red-500">*</span>
          </label>
          <input
            type="text"
            x-model="form.cost_center_desc"
            required
            placeholder="Contoh: Gaji & Tunjangan Pabrik"
            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-semibold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
          />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <!-- Header / Group -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Header / Kelompok Akun
            </label>
            <input
              type="text"
              x-model="form.cost_center_header"
              placeholder="Contoh: DIRECT LABOR"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <!-- Sub Account -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Sub Akun
            </label>
            <input
              type="text"
              x-model="form.cost_center_sub"
              placeholder="Contoh: PRODUCTION STAFF"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
          <!-- Tahun -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Tahun <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              x-model="form.year"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <!-- Modul / Type -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Tipe / Modul <span class="text-red-500">*</span>
            </label>
            <select
              x-model="form.type"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <option value="FOH">FOH</option>
              <option value="GA">OPEX GA</option>
              <option value="SELLING">OPEX SELLING</option>
              <option value="CAPEX">CAPEX</option>
              <option value="SALES">SALES</option>
            </select>
          </div>

          <!-- Status -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
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
            <span x-text="saving ? 'Menyimpan...' : (form.id ? 'Perbarui Akun' : 'Simpan Akun')"></span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- MODAL: SALIN AKUN ANTAR TAHUN -->
  <!-- ============================================================ -->
  <div
    x-show="copyModalOpen"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center p-4"
    style="z-index: 9999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
    @click.self="copyModalOpen = false"
    x-cloak
  >
    <div
      x-show="copyModalOpen"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95 translate-y-3"
      x-transition:enter-end="opacity-100 scale-100 translate-y-0"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100 scale-100 translate-y-0"
      x-transition:leave-end="opacity-0 scale-95 translate-y-3"
      class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden"
    >
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400">
            <i class="fa-solid fa-copy text-base"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Salin Akun Antar Tahun</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Duplikasi master akun COA untuk tahun anggaran baru</p>
          </div>
        </div>
        <button
          type="button"
          @click="copyModalOpen = false"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <i class="fa-solid fa-xmark text-base"></i>
        </button>
      </div>

      <!-- Copy Form -->
      <form @submit.prevent="executeCopy()" class="p-6 space-y-5">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Tahun Sumber <span class="text-red-500">*</span>
            </label>
            <select
              x-model="copyForm.from_year"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <?php foreach (($years ?? []) as $y): ?>
                <option value="<?= esc($y) ?>"><?= esc($y) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Tahun Target <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              x-model="copyForm.to_year"
              required
              placeholder="Contoh: <?= date('Y') + 1 ?>"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 p-3 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2">
          <i class="fa-solid fa-circle-info text-amber-500 mt-0.5 shrink-0"></i>
          <span>Akun yang sudah ada di tahun target akan <strong>dilewati otomatis</strong> sehingga tidak terjadi duplikasi.</span>
        </div>

        <!-- Form Actions -->
        <div class="pt-2 flex items-center justify-end gap-3">
          <button
            type="button"
            @click="copyModalOpen = false"
            class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="copying"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-purple-600 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500/40 disabled:opacity-50 transition-colors"
          >
            <i class="fa-solid fa-spinner fa-spin" x-show="copying"></i>
            <span x-text="copying ? 'Memproses...' : 'Mulai Salin Data'"></span>
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
  function coaPage() {
    return {
      modalOpen: false,
      copyModalOpen: false,
      saving: false,
      copying: false,

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
        main_account: '',
        id_acct_ext: '',
        cost_center_header: '',
        cost_center_sub: '',
        cost_center_desc: '',
        year: '<?= $selectedYear ?>',
        type: 'FOH',
        category: '',
        status: 'A'
      },
      copyForm: {
        from_year: '<?= $selectedYear ?>',
        to_year: '<?= (int)$selectedYear + 1 ?>'
      },

      openCreateModal() {
        this.form = {
          id: null,
          main_account: '',
          id_acct_ext: '',
          cost_center_header: '',
          cost_center_sub: '',
          cost_center_desc: '',
          year: '<?= $selectedYear ?>',
          type: 'FOH',
          category: '',
          status: 'A'
        };
        this.modalOpen = true;
      },

      openEditModal(row) {
        this.form = {
          id: row.id_cost_center,
          main_account: row.main_account,
          id_acct_ext: row.id_acct_ext || '',
          cost_center_header: row.cost_center_header || '',
          cost_center_sub: row.cost_center_sub || '',
          cost_center_desc: row.cost_center_desc || '',
          year: row.year || '<?= $selectedYear ?>',
          type: row.type || 'FOH',
          category: row.category || '',
          status: row.status || 'A'
        };
        this.modalOpen = true;
      },

      openCopyModal() {
        this.copyModalOpen = true;
      },

      async saveCoa() {
        this.saving = true;
        try {
          const res = await window.ypFetch('<?= base_url('master/api/coa/save') ?>', this.form);
          if (res.success) {
            window.showToast('success', res.message || 'Akun COA berhasil disimpan');
            this.modalOpen = false;
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal menyimpan akun COA');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan sistem');
        } finally {
          this.saving = false;
        }
      },

      async toggleStatus(id, actionText) {
        if (!window.ypConfirm(`Apakah Anda yakin ingin ${actionText} akun COA ini?`)) {
          return;
        }

        try {
          const res = await window.ypFetch('<?= base_url('master/api/coa/toggle') ?>', { id });
          if (res.success) {
            window.showToast('success', res.message || 'Status berhasil diubah');
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal mengubah status');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan saat memproses permintaan');
        }
      },

      async executeCopy() {
        if (!this.copyForm.from_year || !this.copyForm.to_year) {
          window.showToast('error', 'Tahun asal dan target harus diisi.');
          return;
        }
        if (this.copyForm.from_year === this.copyForm.to_year) {
          window.showToast('error', 'Tahun asal dan target tidak boleh sama.');
          return;
        }

        this.copying = true;
        try {
          const res = await window.ypFetch('<?= base_url('master/api/coa/copy-year') ?>', this.copyForm);
          if (res.success) {
            window.showToast('success', res.message || 'Akun COA berhasil disalin');
            this.copyModalOpen = false;
            setTimeout(() => {
              window.location.href = '<?= base_url('master/coa') ?>?year=' + this.copyForm.to_year;
            }, 800);
          } else {
            window.showToast('error', res.message || 'Gagal menyalin akun COA');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan pada server');
        } finally {
          this.copying = false;
        }
      }
    };
  }
</script>

<?= $this->endSection() ?>