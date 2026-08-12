<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6" x-data="{ period: '<?= (int) ($workingYear ?? date('Y')) ?>' }">

    <?php if (! empty($rolelessUsers)): ?>
    <div class="flex items-start gap-3 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-2xl p-4" x-data="{ show: true }" x-show="show" x-transition>
        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86l-8.07 14.03A2 2 0 003.93 21h16.14a2 2 0 001.73-3.11L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-300"><?= (int) $rolelessUsers ?> user aktif belum memiliki role</h3>
            <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">
                User tanpa role saat ini dapat mengakses seluruh modul (mode fail-open).
                Segera assign role agar hak akses terkunci sesuai kebijakan RBAC.
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0 mt-0.5">
            <a href="<?= base_url('sys-admin/user') ?>" class="text-xs font-medium text-amber-700 dark:text-amber-300 hover:underline">Kelola User &rarr;</a>
            <button @click="show = false" class="text-amber-500 hover:text-amber-700 dark:hover:text-amber-300" aria-label="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">
                    YP Budget Management Center
                </h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Data
                </span>
            </div>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-1">
                Ringkasan alokasi budget, komposisi modul, dan status progress input — data riil sistem.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700/50 px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-600/60">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Tahun Anggaran:</span>
                <span class="text-xs font-bold text-gray-800 dark:text-white"><?= (int) ($workingYear ?? date('Y')) ?></span>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- KARTU STATISTIK (data riil) -->
    <!-- ============================================================ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total OPEX GA & Selling</span>
                <h3 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white mt-1">
                    Rp <?= number_format($stats['opex'] ?? 0, 0, ',', '.') ?>
                </h3>
                <span class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 inline-flex items-center gap-1 mt-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <?= number_format($stats['budget_entries'] ?? 0) ?> baris entry budget
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total CAPEX Proposed</span>
                <h3 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white mt-1">
                    Rp <?= number_format($stats['capex'] ?? 0, 0, ',', '.') ?>
                </h3>
                <span class="text-[11px] font-medium text-amber-600 dark:text-amber-400 inline-flex items-center gap-1 mt-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?= number_format($stats['capex_assets'] ?? 0) ?> aset diajukan
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>

        <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Target Sales Consolidated</span>
                <h3 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white mt-1">
                    Rp <?= number_format($stats['sales'] ?? 0, 0, ',', '.') ?>
                </h3>
                <span class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 inline-flex items-center gap-1 mt-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Domestik & Ekspor
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
        </div>

        <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Plan Headcount (MPP)</span>
                <h3 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white mt-1">
                    <?= number_format($stats['mpp'] ?? 0) ?> Org
                </h3>
                <span class="text-[11px] font-medium text-purple-600 dark:text-purple-400 inline-flex items-center gap-1 mt-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Termasuk New Headcount
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- CHART SECTION (Highcharts) -->
    <!-- ============================================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Tren Budget Bulanan -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-700/60">
                <h3 class="text-base font-bold text-gray-800 dark:text-white">Tren Budget Bulanan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Alokasi budget keseluruhan modul per bulan — tahun <?= (int) ($workingYear ?? date('Y')) ?>.</p>
            </div>
            <?php if (empty($hasBudget)): ?>
                <div class="p-10 text-center">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-700/50 text-gray-400 mb-2">
                        <i class="fa-solid fa-chart-column text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Belum ada data budget untuk tahun ini</p>
                    <p class="text-xs text-gray-400 mt-1">Isi entry budget di modul OPEX GA / Selling / FOH, atau pilih tahun lain.</p>
                </div>
            <?php else: ?>
                <div id="chartBudgetMonthly" class="p-3 h-[300px]"></div>
            <?php endif; ?>
        </div>

        <!-- Komposisi Anggaran per Modul -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-700/60">
                <h3 class="text-base font-bold text-gray-800 dark:text-white">Komposisi Anggaran per Modul</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Distribusi alokasi budget berdasar sumber modul (OPEX GA, Selling, FOH, CAPEX, MPP).</p>
            </div>
            <?php if (empty($composition)): ?>
                <div class="p-10 text-center">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-700/50 text-gray-400 mb-2">
                        <i class="fa-solid fa-chart-pie text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Belum ada data komposisi</p>
                    <p class="text-xs text-gray-400 mt-1">Data muncul setelah entry budget tersedia untuk tahun tersebut.</p>
                </div>
            <?php else: ?>
                <div id="chartComposition" class="p-3 h-[300px]"></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- PINTASAN FITUR (Grid 3 cols x 2 rows) -->
    <!-- ============================================================ -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid text-amber-500 text-xs"></i> Pintasan Fitur Utama
            </h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 [grid-template-columns:repeat(1,minmax(0,1fr))] sm:[grid-template-columns:repeat(2,minmax(0,1fr))] md:[grid-template-columns:repeat(3,minmax(0,1fr))]">
            <a href="<?= base_url('opex-ga/entry-budget') ?>" class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs hover:border-emerald-500 hover:shadow-md transition-all flex items-center justify-between group">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 block transition-colors">OPEX GA</span>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400">Entry Beban Umum & Admin</span>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-gray-300 dark:text-gray-600 group-hover:text-emerald-500 group-hover:translate-x-0.5 transition-all"></i>
            </a>

            <a href="<?= base_url('opex-selling/entry-budget') ?>" class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs hover:border-blue-500 hover:shadow-md transition-all flex items-center justify-between group">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 block transition-colors">OPEX Selling</span>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400">Entry Biaya Penjualan</span>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-gray-300 dark:text-gray-600 group-hover:text-blue-500 group-hover:translate-x-0.5 transition-all"></i>
            </a>

            <a href="<?= base_url('capex/entry') ?>" class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs hover:border-amber-500 hover:shadow-md transition-all flex items-center justify-between group">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 block transition-colors">CAPEX Entry</span>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400">Pengajuan Aset Modal</span>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-gray-300 dark:text-gray-600 group-hover:text-amber-500 group-hover:translate-x-0.5 transition-all"></i>
            </a>

            <a href="<?= base_url('mpp/entry') ?>" class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs hover:border-purple-500 hover:shadow-md transition-all flex items-center justify-between group">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 block transition-colors">New MPP</span>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400">Manpower Planning</span>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-gray-300 dark:text-gray-600 group-hover:text-purple-500 group-hover:translate-x-0.5 transition-all"></i>
            </a>

            <a href="<?= base_url('sales') ?>" class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs hover:border-teal-500 hover:shadow-md transition-all flex items-center justify-between group">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 012 2h2a2 2 0 002-2z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 block transition-colors">Sales Plan</span>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400">Target Domestik & Ekspor</span>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-gray-300 dark:text-gray-600 group-hover:text-teal-500 group-hover:translate-x-0.5 transition-all"></i>
            </a>

            <a href="<?= base_url('pl') ?>" class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs hover:border-indigo-500 hover:shadow-md transition-all flex items-center justify-between group">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 block transition-colors">P/L Report</span>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400">Laporan Laba Rugi</span>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-gray-300 dark:text-gray-600 group-hover:text-indigo-500 group-hover:translate-x-0.5 transition-all"></i>
            </a>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STATUS PROGRESS PER COST CENTER -->
    <!-- ============================================================ -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/60 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white">Status Progress Input Per Cost Center</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pantau kelengkapan submission anggaran antar departemen — tahun <?= (int) ($workingYear ?? date('Y')) ?>.</p>
            </div>
            <a href="<?= base_url('monitoring') ?>" class="text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:underline">
                Lihat Semua Status &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/60 dark:bg-gray-700/30 text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700/60">
                        <th class="px-5 py-3">Cost Center</th>
                        <th class="px-5 py-3">Department</th>
                        <th class="px-5 py-3 text-right">OPEX GA</th>
                        <th class="px-5 py-3 text-right">CAPEX</th>
                        <th class="px-5 py-3 text-center">MPP</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    <?php if (empty($statusRows)): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400">
                                        <i class="fa-solid fa-clipboard-list text-xl"></i>
                                    </div>
                                    <p class="font-semibold text-gray-600 dark:text-gray-300">Belum ada data entry untuk tahun ini</p>
                                    <p class="text-xs text-gray-400">Entry budget akan tampil di sini setelah data tersedia.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($statusRows as $row): ?>
                            <?php
                                $badge = match ($row['status']) {
                                    'Submitted'   => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
                                    'In Progress' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
                                    default       => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                };
                            ?>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                                <td class="px-5 py-3.5 font-mono font-bold text-brand-600 dark:text-brand-400"><?= esc($row['cc_sap'] ?? $row['cost_center']) ?></td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                                    <?= esc($row['cost_desc'] !== '' ? $row['cost_desc'] : '-') ?>
                                </td>
                                <td class="px-5 py-3 text-right font-mono font-semibold text-gray-800 dark:text-gray-200">
                                    <?= number_format((float) $row['opex'], 0, ',', '.') ?>
                                </td>
                                <td class="px-5 py-3 text-right font-mono font-semibold text-gray-800 dark:text-gray-200">
                                    <?= number_format((float) $row['capex'], 0, ',', '.') ?>
                                </td>
                                <td class="px-5 py-3 text-center font-semibold text-gray-800 dark:text-gray-200">
                                    <?= number_format((int) $row['mpp']) ?> org
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold <?= $badge ?>">
                                        <?= esc($row['status']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="<?= base_url('monitoring') ?>" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-medium">Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php if (! empty($hasBudget) || ! empty($composition)): ?>
<script src="https://cdn.jsdelivr.net/npm/highcharts@10.3.3/highcharts.js"></script>
<script>
  (function () {
    if (typeof Highcharts === 'undefined') {
      return;
    }

    Highcharts.setOptions({
      credits: { enabled: false },
      lang: { thousandsSep: '.', decimalPoint: ',' }
    });

    <?php if (! empty($hasBudget)): ?>
    try {
      Highcharts.chart('chartBudgetMonthly', {
        chart: { type: 'column', backgroundColor: 'transparent' },
        title: { text: null },
        xAxis: { categories: <?= json_encode(array_column($monthly ?? [], 'label')) ?>, crosshair: true },
        yAxis: {
          title: { text: null },
          labels: { formatter: function () { return Highcharts.numberFormat(this.value, 0, ',', '.'); } }
        },
        tooltip: {
          formatter: function () {
            return '<b>' + this.x + '</b><br/>Rp ' + Highcharts.numberFormat(this.y, 0, ',', '.');
          }
        },
        plotOptions: { column: { color: '#10b981', borderRadius: 4, dataLabels: { enabled: false } } },
        series: [{
          name: 'Budget',
          data: <?= json_encode(array_map(fn ($m) => (float) $m['value'], $monthly ?? [])) ?>
        }]
      });
    } catch (e) { /* ignore */ }
    <?php endif; ?>

    <?php if (! empty($composition)): ?>
    try {
      Highcharts.chart('chartComposition', {
        chart: { type: 'pie', backgroundColor: 'transparent' },
        title: { text: null },
        tooltip: {
          formatter: function () {
            return '<b>' + this.key + '</b><br/>Rp ' + Highcharts.numberFormat(this.y, 0, ',', '.') +
                   ' (' + Highcharts.numberFormat(this.percentage, 1, ',', '.') + '%)';
          }
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
              enabled: true,
              format: '<b>{point.name}</b>: {point.percentage:.1f}%',
              style: { fontSize: '11px' }
            }
          }
        },
        series: [{
          name: 'Budget',
          colorByPoint: true,
          colors: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#06b6d4', '#64748b'],
          data: <?= json_encode(array_map(fn ($c) => ['name' => $c['label'], 'y' => (float) $c['value']], $composition ?? [])) ?>
        }]
      });
    } catch (e) { /* ignore */ }
    <?php endif; ?>
  })();
</script>
<?php endif; ?>

<?= $this->endSection() ?>
