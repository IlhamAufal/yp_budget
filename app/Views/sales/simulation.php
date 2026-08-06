<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ tab: 'revenue' }" class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Sales Simulation</h2>
            <p class="text-sm text-gray-500">Revenue & Volume Target Simulation</p>
        </div>
    </div>

    <div class="inline-flex p-1 rounded-lg bg-gray-100 border border-gray-200">
        <button @click="tab = 'revenue'" 
                :class="tab === 'revenue' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 rounded-md text-xs font-semibold transition-all">
            Revenue Simulation
        </button>
        <button @click="tab = 'volume'" 
                :class="tab === 'volume' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 rounded-md text-xs font-semibold transition-all">
            Volume Simulation
        </button>
    </div>

    <div x-show="tab === 'revenue'" class="rounded-xl border border-gray-200 bg-white p-6 shadow-xs">
        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Revenue Simulation Matrix</h3>
        <p class="text-xs text-gray-500">Interactive revenue forecasting based on channel multiplier.</p>
    </div>

    <div x-show="tab === 'volume'" x-cloak class="rounded-xl border border-gray-200 bg-white p-6 shadow-xs">
        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Volume Simulation Matrix</h3>
        <p class="text-xs text-gray-500">Real-time unit quantity simulation by product line.</p>
    </div>

</div>
<?= $this->endSection() ?>