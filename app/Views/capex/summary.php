<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">
    <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Executive Summary CAPEX</h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Ringkasan investasi belanja modal.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-1.5">
            <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Total Anggaran CAPEX</span>
            <h3 class="text-xl font-extrabold font-mono text-gray-900 dark:text-white">Rp <?= number_format($summary_data['total_capex'] ?? 0, 0, ',', '.') ?></h3>
        </div>
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-1.5">
            <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Total Unit Aset Dibelikan</span>
            <h3 class="text-xl font-extrabold font-mono text-primary"><?= number_format($summary_data['total_units'] ?? 0) ?> Unit</h3>
        </div>
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-1.5">
            <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Beban Depresiasi / Tahun</span>
            <h3 class="text-xl font-extrabold font-mono text-emerald-600 dark:text-emerald-400">Rp <?= number_format($summary_data['total_depreciation'] ?? 0, 0, ',', '.') ?></h3>
        </div>
    </div>
</div>
<?= $this->endSection() ?>