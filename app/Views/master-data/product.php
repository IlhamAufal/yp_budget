<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-4" x-data="productPage()">

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
        <span class="text-brand-500 font-bold">Master Product</span>
      </div>
      <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
          <i class="fa-solid fa-box-open text-base"></i>
        </span>
        Master Product
      </h1>
      <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
        Pengelolaan katalog SKU produk, channel distribusi, rasio kemasan (pcs/box), dan gramasi.
      </p>
    </div>

    <!-- Quick Action Buttons -->
    <div class="flex flex-wrap items-center gap-2.5">
      <a
        href="<?= base_url('master/product/export?' . http_build_query($filters ?? [])) ?>"
        class="inline-flex items-center gap-2 rounded-xl border border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-950/40 px-3.5 py-2 text-xs font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-all shadow-xs"
      >
        <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-400"></i>
        <span>Export CSV</span>
      </a>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- STATS BAR -->
  <!-- ============================================================ -->
  <?php
    $totalRows    = (int) ($total ?? 0);
    $activeCount  = count(array_filter($rows ?? [], fn($r) => ($r['status'] ?? 'A') === 'A'));
    $inactiveCount = $totalRows - $activeCount;
    $selectedYear = $filters['year'] ?? session()->get('year_code') ?? date('Y');
  ?>
  <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-xl border border-gray-200/80 bg-white px-4 py-2.5 text-xs dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
        <i class="fa-solid fa-boxes-stacked text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Total Produk:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= number_format($totalRows) ?> <span class="font-normal text-gray-400">SKU</span></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400">
        <i class="fa-solid fa-circle-check text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Aktif:</span>
      <span class="font-bold text-emerald-600 dark:text-emerald-400"><?= number_format($activeCount) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
        <i class="fa-solid fa-circle-pause text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Non-Aktif:</span>
      <span class="font-bold text-gray-600 dark:text-gray-300"><?= number_format($inactiveCount) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-amber-50 text-amber-500 dark:bg-amber-500/10 dark:text-amber-400">
        <i class="fa-solid fa-calendar text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Tahun:</span>
      <span class="font-bold text-brand-500 dark:text-brand-400"><?= esc($selectedYear) ?></span>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- TAB NAVIGATION -->
  <!-- ============================================================ -->
  <div class="rounded-xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="flex items-center gap-1 border-b border-gray-200/80 dark:border-gray-800 px-4 pt-3">
      <button @click="activeTab = 'maintenance'"
        :class="activeTab === 'maintenance' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
        class="border-b-2 px-4 pb-2.5 text-xs font-semibold transition-all">
        <i class="fa-solid fa-table-list mr-1.5"></i>Maintenance Data Product
      </button>
      <button @click="activeTab = 'upload'"
        :class="activeTab === 'upload' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
        class="border-b-2 px-4 pb-2.5 text-xs font-semibold transition-all">
        <i class="fa-solid fa-cloud-arrow-up mr-1.5"></i>Upload Data Product
      </button>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- TAB 1: MAINTENANCE DATA PRODUCT -->
  <!-- ============================================================ -->
  <div x-show="activeTab === 'maintenance'" class="space-y-4">

  <!-- ============================================================ -->
  <!-- FILTER & DATA TABLE -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <!-- Filter Bar as table header -->
    <div class="p-3 border-b border-gray-100 dark:border-gray-800">
      <form method="GET" action="<?= base_url('master/product') ?>" class="flex flex-wrap items-center gap-2">
        <!-- Search Input -->
        <div class="relative flex-1 min-w-[180px]">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400"></i>
          <input type="text" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Cari nama produk, MID, atau key..." 
            class="w-full rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 pl-9 pr-3 text-xs focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-colors" />
        </div>
        
        <!-- Filter Channel -->
        <select name="channel" class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs min-w-[140px] focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-colors">
          <option value="">Semua Channel</option>
          <?php foreach (($channels ?? []) as $ch): ?>
            <option value="<?= esc($ch) ?>" <?= ($filters['channel'] ?? '') === $ch ? 'selected' : '' ?>>
              <?= esc($ch) ?>
            </option>
          <?php endforeach; ?>
        </select>
        
        <!-- Filter Tahun -->
        <select name="year" class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs min-w-[120px] focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-colors">
          <option value="">Semua Tahun</option>
          <?php foreach (($years ?? []) as $y): ?>
            <option value="<?= esc($y) ?>" <?= (string)($filters['year'] ?? '') === (string)$y ? 'selected' : '' ?>>
              <?= esc($y) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <!-- Filter Status -->
        <select name="status" class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs min-w-[120px] focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-colors">
          <option value="">Semua Status</option>
          <option value="A" <?= ($filters['status'] ?? '') === 'A' ? 'selected' : '' ?>>Aktif (A)</option>
          <option value="D" <?= ($filters['status'] ?? '') === 'D' ? 'selected' : '' ?>>Non-Aktif (D)</option>
        </select>
        
        <button type="submit" class="rounded-lg bg-gray-900 dark:bg-brand-500 py-1.5 px-3 text-xs font-semibold text-white hover:bg-black dark:hover:bg-brand-600 transition-colors shadow-xs" title="Terapkan Filter">
          <i class="fa-solid fa-filter text-[10px]"></i>
        </button>
        <?php if (! empty($has_filter)): ?>
        <a href="<?= base_url('master/product') ?>" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-1.5 px-2.5 text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Reset Filter">
          <i class="fa-solid fa-rotate-left text-[10px]"></i>
        </a>
        <?php endif; ?>
      </form>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold capitalize tracking-normal text-gray-500 dark:text-gray-400">
            <th class="py-3 px-4 w-12 text-center">No</th>
            <th class="py-3 px-4 font-mono">ID Inventory</th>
            <th class="py-3 px-4 text-center">Channel</th>
            <th class="py-3 px-4">Key Product</th>
            <th class="py-3 px-4">Product Name</th>
            <th class="py-3 px-4 text-right">DB</th>
            <th class="py-3 px-4 text-right">PCS</th>
            <th class="py-3 px-4 text-right">GR</th>
            <th class="py-3 px-4 text-right w-28">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="9" class="py-12 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-2">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400">
                    <i class="fa-solid fa-box-open text-xl"></i>
                  </div>
                  <p class="font-semibold text-gray-600 dark:text-gray-300">Tidak ada data produk ditemukan</p>
                  <p class="text-xs text-gray-400">Coba sesuaikan filter pencarian di atas.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($rows as $index => $r): ?>
              <?php
                $ch = strtoupper(trim($r['id_channel'] ?? ''));
                $chBadge = 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300';
                if ($ch === 'GT') {
                    $chBadge = 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
                } elseif ($ch === 'MT') {
                    $chBadge = 'bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20';
                } elseif ($ch === 'OEM') {
                    $chBadge = 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
                } elseif ($ch === 'EXPORT') {
                    $chBadge = 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20';
                } elseif ($ch === 'ECOM') {
                    $chBadge = 'bg-pink-50 text-pink-700 border border-pink-200 dark:bg-pink-500/10 dark:text-pink-400 dark:border-pink-500/20';
                }
              ?>
              <tr x-show="isRowVisible(<?= $index ?>)" class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <!-- No -->
                <td class="py-3 px-4 text-center text-gray-400 font-medium"><?= $index + 1 ?></td>

                <!-- ID Inventory -->
                <td class="py-3 px-4 font-mono text-gray-800 dark:text-gray-200">
                  <?= esc($r['mid_product'] ?: '-') ?>
                </td>

                <!-- Channel -->
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold <?= $chBadge ?>">
                    <?= esc($r['id_channel'] ?: 'GT') ?>
                  </span>
                </td>

                <!-- Key Product -->
                <td class="py-3 px-4 font-mono text-gray-600 dark:text-gray-300 text-[11px]">
                  <?= esc($r['key_product'] ?: '-') ?>
                </td>

                <!-- Product Name -->
                <td class="py-3 px-4 font-bold text-gray-900 dark:text-white">
                  <?= esc($r['product_name']) ?>
                </td>

                <!-- DB (Default Box) -->
                <td class="py-3 px-4 text-right font-mono font-semibold text-gray-700 dark:text-gray-300">
                  <?= $r['db'] !== null ? number_format((float)($r['db'] ?? 0)) : '-' ?>
                </td>

                <!-- PCS -->
                <td class="py-3 px-4 text-right font-mono font-semibold text-gray-700 dark:text-gray-300">
                  <?= $r['pcs'] !== null ? number_format((float)($r['pcs'] ?? 0)) : '-' ?>
                </td>

                <!-- GR (Gramasi) -->
                <td class="py-3 px-4 text-right font-mono font-semibold text-gray-700 dark:text-gray-300">
                  <?= $r['gr'] !== null ? number_format((float)($r['gr'] ?? 0), 2) : '-' ?>
                </td>

                <!-- Action -->
                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Edit Button -->
                    <button
                      type="button"
                      @click='openEditModal(<?= json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                      class="h-7 w-7 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-brand-50 hover:text-brand-500 hover:border-brand-200 dark:hover:bg-brand-500/10 dark:hover:text-brand-400 transition-colors flex items-center justify-center shadow-2xs"
                      title="Edit Produk"
                    >
                      <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                    </button>

                    <!-- Delete Button -->
                    <button
                      type="button"
                      @click="toggleStatus(<?= (int)$r['id_product'] ?>, 'hapus')"
                      class="h-7 w-7 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-red-600 hover:bg-red-50 hover:border-red-200 dark:hover:bg-red-500/10 transition-colors flex items-center justify-center shadow-2xs"
                      title="Hapus Produk"
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
      <span>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200"><?= $from ?></span> - <span class="font-bold text-gray-800 dark:text-gray-200"><?= $to ?></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= number_format($total) ?></span> produk SKU</span>
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

  </div><!-- END TAB 1: MAINTENANCE DATA PRODUCT -->

  <!-- ============================================================ -->
  <!-- TAB 2: UPLOAD DATA PRODUCT -->
  <!-- ============================================================ -->
  <div x-show="activeTab === 'upload'" x-cloak>
    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
      <div class="p-6 space-y-6">
        <div class="flex items-center gap-3 mb-2">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
            <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Upload Data Product</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Download template, isi data produk, lalu upload kembali file Excel.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Download Template -->
          <div class="rounded-xl border border-dashed border-emerald-300 dark:border-emerald-700 bg-emerald-50/50 dark:bg-emerald-950/20 p-6 flex flex-col items-center text-center gap-3">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">
              <i class="fa-solid fa-file-excel text-2xl"></i>
            </div>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Template Data</h4>
            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-xs">Download template Excel berisi format kolom yang harus diisi untuk import data produk.</p>
            <a href="<?= base_url('master/product/download-template') ?>"
              class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 transition-colors shadow-sm mt-2">
              <i class="fa-solid fa-download"></i>
              Download Template
            </a>
          </div>

          <!-- Upload File -->
          <div class="rounded-xl border border-dashed border-blue-300 dark:border-blue-700 bg-blue-50/50 dark:bg-blue-950/20 p-6 flex flex-col items-center text-center gap-3">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
              <i class="fa-solid fa-upload text-2xl"></i>
            </div>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Upload Data</h4>
            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-xs">Upload file Excel yang sudah diisi sesuai format template untuk import data produk secara massal.</p>
            <form method="POST" action="<?= base_url('master/product/upload') ?>" enctype="multipart/form-data" class="w-full max-w-xs mt-2 space-y-3">
              <input type="file" name="excel_file" accept=".xlsx,.xls"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-xs text-gray-700 dark:text-gray-300 file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-blue-600 dark:file:bg-blue-500/10 dark:file:text-blue-400 hover:file:bg-blue-100 cursor-pointer" required />
              <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-700 transition-colors shadow-sm">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                Upload Data
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div><!-- END TAB 2: UPLOAD DATA PRODUCT -->

  <!-- ============================================================ -->
  <!-- MODAL: TAMBAH / EDIT PRODUK -->
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
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
            <i class="fa-solid fa-box-open text-base"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="form.id ? 'Edit Produk' : 'Tambah Produk Baru'"></h3>
            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="form.id ? 'Perbarui informasi produk dan spesifikasi kemasan' : 'Daftarkan SKU produk baru ke dalam sistem'"></p>
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
      <form @submit.prevent="saveProduct()" class="p-6 space-y-4">
        <input type="hidden" x-model="form.id" />

        <!-- Nama Produk -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
            Nama Produk <span class="text-red-500">*</span>
          </label>
          <input
            type="text"
            x-model="form.product_name"
            required
            placeholder="Contoh: Chocochip Cookies 100gr"
            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <!-- Channel -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Channel Distribusi <span class="text-red-500">*</span>
            </label>
            <select
              x-model="form.id_channel"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <?php foreach (($channels ?? []) as $ch): ?>
                <option value="<?= esc($ch) ?>"><?= esc($ch) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Key Product -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Key Product
            </label>
            <input
              type="text"
              x-model="form.key_product"
              placeholder="Contoh: KEY-001"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-mono text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <!-- MID Product -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              MID Product
            </label>
            <input
              type="text"
              x-model="form.mid_product"
              placeholder="Contoh: MID-202"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-mono text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <!-- Default Box (DB) -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Default Box (DB)
            </label>
            <input
              type="number"
              step="any"
              x-model="form.db"
              placeholder="0"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-mono text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <!-- Pcs / Box -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Pcs / Box
            </label>
            <input
              type="number"
              step="any"
              x-model="form.pcs"
              placeholder="0"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-mono text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <!-- Gramasi (Gr) -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Gramasi (Gr)
            </label>
            <input
              type="number"
              step="0.01"
              x-model="form.gr"
              placeholder="0.00"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-mono text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Tahun -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
              Tahun Anggaran <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              x-model="form.year"
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
            <span x-text="saving ? 'Menyimpan...' : (form.id ? 'Perbarui Produk' : 'Simpan Produk')"></span>
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
  function productPage() {
    return {
      activeTab: 'maintenance',
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
        product_name: '',
        id_channel: 'GT',
        key_product: '',
        mid_product: '',
        db: 0,
        pcs: 0,
        gr: 0,
        year: '<?= $selectedYear ?>',
        status: 'A'
      },

      openCreateModal() {
        this.form = {
          id: null,
          product_name: '',
          id_channel: 'GT',
          key_product: '',
          mid_product: '',
          db: 0,
          pcs: 0,
          gr: 0,
          year: '<?= $selectedYear ?>',
          status: 'A'
        };
        this.modalOpen = true;
      },

      openEditModal(row) {
        this.form = {
          id: row.id_product,
          product_name: row.product_name || '',
          id_channel: row.id_channel || 'GT',
          key_product: row.key_product || '',
          mid_product: row.mid_product || '',
          db: row.db || 0,
          pcs: row.pcs || 0,
          gr: row.gr || 0,
          year: row.year || '<?= $selectedYear ?>',
          status: row.status || 'A'
        };
        this.modalOpen = true;
      },

      async saveProduct() {
        this.saving = true;
        try {
          const res = await window.ypFetch('<?= base_url('master/api/product/save') ?>', this.form);
          if (res.success) {
            window.showToast('success', res.message || 'Produk berhasil disimpan');
            this.modalOpen = false;
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal menyimpan produk');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan sistem');
        } finally {
          this.saving = false;
        }
      },

      async toggleStatus(id, actionText) {
        if (!window.ypConfirm(`Apakah Anda yakin ingin ${actionText} produk ini?`)) {
          return;
        }

        try {
          const res = await window.ypFetch('<?= base_url('master/api/product/toggle') ?>', { id });
          if (res.success) {
            window.showToast('success', res.message || 'Status produk berhasil diubah');
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