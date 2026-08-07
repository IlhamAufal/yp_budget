<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{
    rows: [
        { country: 'Singapore', product: 'Product Y', volume: 500, fobUsd: 12.5, exchangeRate: 15500 }
    ],
    addRow() {
        this.rows.push({ country: '', product: '', volume: 0, fobUsd: 0, exchangeRate: 15500 });
    },
    removeRow(index) {
        this.rows.splice(index, 1);
    }
}" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">

    <!-- Page Header Card -->
    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="space-y-1.5">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Entry Sales Export</h2>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Input data transaksi penjualan ekspor berdasarkan negara tujuan, harga FOB USD, dan kurs konversi.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <button @click="addRow()" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4.5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-xs hover:bg-primary-dark active:scale-[0.98] transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Export Row
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-100/80 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 text-[11px] font-bold tracking-wider uppercase border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-5 py-3.5 min-w-[170px]">Country</th>
                        <th class="px-5 py-3.5 min-w-[200px]">Product</th>
                        <th class="px-4 py-3.5 text-right w-32">Volume</th>
                        <th class="px-4 py-3.5 text-right w-36">FOB Price ($)</th>
                        <th class="px-4 py-3.5 text-right w-40">Exchange Rate</th>
                        <th class="px-5 py-3.5 text-right w-48">Total Value (IDR)</th>
                        <th class="px-4 py-3.5 text-center w-20">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/80 dark:divide-gray-800/80">
                    <template x-for="(row, index) in rows" :key="index">
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-4 py-3"><input type="text" x-model="row.country" class="w-full rounded-lg border border-gray-300/80 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Country"></td>
                            <td class="px-4 py-3"><input type="text" x-model="row.product" class="w-full rounded-lg border border-gray-300/80 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Product Name"></td>
                            <td class="px-3 py-3"><input type="number" x-model.number="row.volume" class="w-full rounded-lg border border-gray-300/80 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-mono font-medium text-right text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"></td>
                            <td class="px-3 py-3"><input type="number" x-model.number="row.fobUsd" class="w-full rounded-lg border border-gray-300/80 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-mono font-medium text-right text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"></td>
                            <td class="px-3 py-3"><input type="number" x-model.number="row.exchangeRate" class="w-full rounded-lg border border-gray-300/80 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-mono font-medium text-right text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"></td>
                            <td class="px-5 py-3 text-right font-mono font-bold text-gray-900 dark:text-white" x-text="(row.volume * row.fobUsd * row.exchangeRate).toLocaleString('id-ID')"></td>
                            <td class="px-4 py-3 text-center">
                                <button @click="removeRow(index)" class="text-red-500 hover:text-red-700 dark:hover:text-red-400 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
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