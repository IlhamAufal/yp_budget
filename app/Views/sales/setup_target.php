<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ section: 'base_index' }" class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Target & Showcase Configuration</h2>
            <p class="text-sm text-gray-500">Setup Base Amount, Index Rate, and Showcase Inventory</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-xs space-y-4">
            <h3 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-2">Base & Index Target Setup</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Target Base Amount</label>
                    <input type="number" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Enter base amount">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Index Rate (%)</label>
                    <input type="number" step="0.01" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-primary focus:ring-1 focus:ring-primary" placeholder="e.g. 5.5">
                </div>
            </div>
            <button type="button" class="w-full rounded-lg bg-primary py-2 text-xs font-medium text-white hover:bg-primary-dark transition-colors">
                Update Target Index
            </button>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-xs space-y-4">
            <h3 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-2">Showcase Stock Configuration</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Initial Stock Showcase</label>
                    <input type="number" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Enter initial qty">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Monthly Increment</label>
                    <input type="number" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Enter monthly increment">
                </div>
            </div>
            <button type="button" class="w-full rounded-lg bg-emerald-600 py-2 text-xs font-medium text-white hover:bg-emerald-700 transition-colors">
                Update Showcase Setup
            </button>
        </div>
    </div>

</div>
<?= $this->endSection() ?>