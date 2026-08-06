<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{
    search: '',
    rows: [
        { customer: 'Distributor A', product: 'Product X', volume: 1000, price: 15000 }
    ],
    addRow() {
        this.rows.push({ customer: '', product: '', volume: 0, price: 0 });
    },
    removeRow(index) {
        this.rows.splice(index, 1);
    }
}" class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Entry Sales Domestic</h2>
            <p class="text-sm text-gray-500">Working Year: <span class="font-semibold text-primary"><?= esc($workingYear) ?></span></p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="addRow()" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white shadow-xs hover:bg-primary-dark transition-colors">
                + Add Row
            </button>
        </div>
    </div>

    <div class="relative max-w-xs">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </span>
        <input type="text" x-model="search" placeholder="Search customer or product..." 
               class="w-full rounded-lg border border-gray-300 bg-gray-50/60 py-2 pl-11 pr-4 text-xs md:text-sm shadow-xs focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary">
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 min-w-[200px]">Customer</th>
                        <th class="px-4 py-3 min-w-[200px]">Product Group</th>
                        <th class="px-3 py-3 text-right w-32">Volume</th>
                        <th class="px-3 py-3 text-right w-36">Unit Price</th>
                        <th class="px-4 py-3 text-right w-40">Total Value</th>
                        <th class="px-3 py-3 text-center w-16">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="(row, index) in rows" :key="index">
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-2">
                                <input type="text" x-model="row.customer" class="w-full rounded border border-gray-300 px-2 py-1 text-xs focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Customer Name">
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" x-model="row.product" class="w-full rounded border border-gray-300 px-2 py-1 text-xs focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Product Group">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" x-model="row.volume" class="w-full rounded border border-gray-300 px-2 py-1 text-xs text-right focus:border-primary focus:ring-1 focus:ring-primary">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" x-model="row.price" class="w-full rounded border border-gray-300 px-2 py-1 text-xs text-right focus:border-primary focus:ring-1 focus:ring-primary">
                            </td>
                            <td class="px-4 py-2 text-right font-semibold text-gray-900" x-text="(row.volume * row.price).toLocaleString()">
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button @click="removeRow(index)" class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?= $this->endSection() ?>