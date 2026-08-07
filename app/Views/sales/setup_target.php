<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ section: 'base_index' }" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">

    <!-- Page Header Card -->
    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="space-y-1.5">
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Target & Showcase Configuration</h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Setup Base Amount, Index Rate, and Showcase Inventory</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-xs space-y-5">
            <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Base & Index Target Setup</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Target Base Amount</label>
                    <input type="number" class="w-full rounded-xl border border-gray-300/80 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Enter base amount">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Index Rate (%)</label>
                    <input type="number" step="0.01" class="w-full rounded-xl border border-gray-300/80 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="e.g. 5.5">
                </div>
            </div>
            <button type="button" class="w-full rounded-xl bg-primary py-2.5 text-xs font-semibold text-white hover:bg-primary-dark active:scale-[0.98] transition-all cursor-pointer">
                Update Target Index
            </button>
        </div>

        <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-xs space-y-5">
            <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Showcase Stock Configuration</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Initial Stock Showcase</label>
                    <input type="number" class="w-full rounded-xl border border-gray-300/80 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Enter initial qty">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Monthly Increment</label>
                    <input type="number" class="w-full rounded-xl border border-gray-300/80 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Enter monthly increment">
                </div>
            </div>
            <button type="button" class="w-full rounded-xl bg-emerald-600 py-2.5 text-xs font-semibold text-white hover:bg-emerald-700 active:scale-[0.98] transition-all cursor-pointer">
                Update Showcase Setup
            </button>
        </div>
    </div>

</div>
<?= $this->endSection() ?>