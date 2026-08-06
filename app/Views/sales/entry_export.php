<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{
    rows: [
        { country: 'Singapore', product: 'Product Y', volume: 500, fobUsd: 12.5, exchangeRate: 15500 }
    ],
    addRow() {
        this.rows.push({ country: '', product: '', volume: 0, fobUsd: 0, exchangeRate: 15500 });
    }
}" class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Entry Sales Export</h2>
            <p class="text-sm text-gray-500">Working Year: <span class="font-semibold text-primary"><?= esc($workingYear) ?></span></p>
        </div>
        <button @click="addRow()" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white shadow-xs hover:bg-primary-dark transition-colors">
            + Add Export Row
        </button>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 min-w-[150px]">Country</th>
                        <th class="px-4 py-3 min-w-[180px]">Product</th>
                        <th class="px-3 py-3 text-right w-28">Volume</th>
                        <th class="px-3 py-3 text-right w-32">FOB Price ($)</th>
                        <th class="px-3 py-3 text-right w-36">Exchange Rate</th>
                        <th class="px-4 py-3 text-right w-44">Total Value (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="(row, index) in rows" :key="index">
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-2"><input type="text" x-model="row.country" class="w-full rounded border border-gray-300 px-2 py-1 text-xs"></td>
                            <td class="px-4 py-2"><input type="text" x-model="row.product" class="w-full rounded border border-gray-300 px-2 py-1 text-xs"></td>
                            <td class="px-3 py-2"><input type="number" x-model="row.volume" class="w-full rounded border border-gray-300 px-2 py-1 text-xs text-right"></td>
                            <td class="px-3 py-2"><input type="number" x-model="row.fobUsd" class="w-full rounded border border-gray-300 px-2 py-1 text-xs text-right"></td>
                            <td class="px-3 py-2"><input type="number" x-model="row.exchangeRate" class="w-full rounded border border-gray-300 px-2 py-1 text-xs text-right"></td>
                            <td class="px-4 py-2 text-right font-semibold text-gray-900" x-text="(row.volume * row.fobUsd * row.exchangeRate).toLocaleString()"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?= $this->endSection() ?>