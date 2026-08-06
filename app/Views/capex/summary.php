<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
        <h1 class="text-xl font-bold text-gray-800">Executive Summary CAPEX</h1>
        <p class="text-xs text-gray-500 mt-1">Ringkasan investasi belanja modal tahun anggaran <?= esc($working_year) ?></p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
            <span class="text-xs text-gray-400 font-medium">Total Anggaran CAPEX</span>
            <h3 class="text-lg font-bold text-gray-900 mt-1">Rp <?= number_format($summary_data['total_capex'] ?? 0, 0, ',', '.') ?></h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
            <span class="text-xs text-gray-400 font-medium">Total Unit Aset Dibelikan</span>
            <h3 class="text-lg font-bold text-brand-600 mt-1"><?= number_format($summary_data['total_units'] ?? 0) ?> Unit</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
            <span class="text-xs text-gray-400 font-medium">Beban Depresiasi / Tahun</span>
            <h3 class="text-lg font-bold text-emerald-600 mt-1">Rp <?= number_format($summary_data['total_depreciation'] ?? 0, 0, ',', '.') ?></h3>
        </div>
    </div>
</div>
<?= $this->endSection() ?>