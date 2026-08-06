<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ 
    activeTab: 'summary',
    discountData: {
        jan: 0, feb: 0, mar: 0, apr: 0, may: 0, jun: 0,
        jul: 0, aug: 0, sep: 0, oct: 0, nov: 0, dec: 0
    },
    get totalDiscount() {
        return Object.values(this.discountData).reduce((a, b) => Number(a) + Number(b), 0);
    }
}" class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Sales Summary & Discount Reclass</h2>
            <p class="text-sm text-gray-500">Working Year: <span class="font-semibold text-primary"><?= esc($workingYear) ?></span></p>
        </div>
        
        <div class="flex items-center gap-3">
            <button @click="$dispatch('open-upload-modal', { type: 'domestic' })" 
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-xs hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload Excel
            </button>
            <a href="<?= base_url('sales/exportExcel') ?>" 
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-800 px-4 py-2.5 text-sm font-medium text-white shadow-xs hover:bg-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button @click="activeTab = 'summary'" 
                    :class="activeTab === 'summary' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Sales Summary Recap
            </button>
            <button @click="activeTab = 'reclass'" 
                    :class="activeTab === 'reclass' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Discount Reclassification
            </button>
        </nav>
    </div>

    <div x-show="activeTab === 'summary'" class="rounded-xl border border-gray-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold sticky left-0 bg-gray-50 min-w-[180px]">Channel / Category</th>
                        <th class="px-3 py-3 text-right">Jan</th>
                        <th class="px-3 py-3 text-right">Feb</th>
                        <th class="px-3 py-3 text-right">Mar</th>
                        <th class="px-3 py-3 text-right">Apr</th>
                        <th class="px-3 py-3 text-right">May</th>
                        <th class="px-3 py-3 text-right">Jun</th>
                        <th class="px-3 py-3 text-right">Jul</th>
                        <th class="px-3 py-3 text-right">Aug</th>
                        <th class="px-3 py-3 text-right">Sep</th>
                        <th class="px-3 py-3 text-right">Oct</th>
                        <th class="px-3 py-3 text-right">Nov</th>
                        <th class="px-3 py-3 text-right">Dec</th>
                        <th class="px-4 py-3 text-right font-bold bg-gray-100">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-medium text-gray-900 sticky left-0 bg-white">Domestic Sales</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 bg-gray-50">0</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="activeTab === 'reclass'" x-cloak class="rounded-xl border border-gray-200 bg-white p-6 shadow-xs space-y-4">
        <h3 class="text-base font-semibold text-gray-800">Discount Allocation Input</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <template x-for="month in ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec']" :key="month">
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase mb-1" x-text="month"></label>
                    <input type="number" x-model="discountData[month]" 
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs text-right focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </template>
        </div>
        <div class="flex items-center justify-between border-t border-gray-100 pt-4">
            <div class="text-sm">
                Total Allocation: <span class="font-bold text-primary" x-text="totalDiscount.toLocaleString()"></span>
            </div>
            <button type="button" class="rounded-lg bg-primary px-5 py-2 text-sm font-medium text-white shadow-xs hover:bg-primary-dark transition-colors">
                Save Discount Allocation
            </button>
        </div>
    </div>

</div>

<?= $this->include('sales/upload_modal') ?>
<?= $this->endSection() ?>