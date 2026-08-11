<div x-data="intlIdrSalesTab()" x-init="initData()" class="space-y-6">

    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 flex-1">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Rate US$ (IDR)</label>
                    <input type="number" step="1" x-model.number="rateUsd" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary font-mono font-semibold" placeholder="16200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Rate Baht Thailand (IDR)</label>
                    <input type="number" step="1" x-model.number="rateBaht" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary font-mono font-semibold" placeholder="450">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Rate Ringgit Malaysia (IDR)</label>
                    <input type="number" step="1" x-model.number="rateRinggit" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary font-mono font-semibold" placeholder="3600">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="processRates()" :disabled="processing" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition-colors disabled:opacity-50 shadow-sm">
                    <i x-show="!processing" class="fa-solid fa-gear"></i>
                    <i x-show="processing" class="fa-solid fa-spinner fa-spin"></i>
                    <span x-text="processing ? 'Processing...' : 'Process'"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total International Volume</p>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-1" x-text="formatNumber(calculateGrandTotalVolume()) + ' Box'">0 Box</h3>
            <p class="text-[11px] text-blue-600 dark:text-blue-400 mt-1">International Total Shipment</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Revenue Equivalent (IDR)</p>
            <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1" x-text="'Rp ' + formatNumber(calculateGrandTotalRevenue())">Rp 0</h3>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Export Sales Value in Local Currency</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Average Price / Box (IDR)</p>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-1" x-text="'Rp ' + formatNumber(calculateAveragePrice())">Rp 0</h3>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Weighted Converted Price</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Matriks International Sales dalam IDR (12 Bulan)</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">Konversi Otomatis dari Tab Valas</span>
        </div>

        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1500px]">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="p-3 sticky left-0 z-10 bg-gray-50 dark:bg-gray-700 min-w-[200px]">Product SKU</th>
                        <th class="p-3 w-28 text-right">Price Equivalent (IDR)</th>
                        <th class="p-3 w-20 text-center">Type</th>
                        <template x-for="(month, idx) in monthNames" :key="idx">
                            <th class="p-2 text-center w-24" x-text="month"></th>
                        </template>
                        <th class="p-3 text-right w-32 bg-gray-100 dark:bg-gray-700/80 sticky right-0">Total Year</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="item in filteredItems" :key="item.id">
                        <template x-for="dataType in ['qty', 'val']">
                            <tr :class="dataType === 'val' ? 'bg-gray-50/50 dark:bg-gray-800/50' : ''">
                                <template x-if="dataType === 'qty'">
                                    <td class="p-3 font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700" rowspan="2">
                                        <div class="font-semibold" x-text="item.product_name"></div>
                                        <div class="text-[10px] text-gray-400" x-text="item.product_code + ' | Region: ' + item.region"></div>
                                    </td>
                                </template>

                                <template x-if="dataType === 'qty'">
                                    <td class="p-3 font-mono text-right" rowspan="2" x-text="formatNumber(item.price_idr)"></td>
                                </template>

                                <td class="p-2 text-center font-medium">
                                    <span :class="dataType === 'qty' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'" class="px-2 py-0.5 rounded text-[10px] font-bold" x-text="dataType === 'qty' ? 'Vol (Box)' : 'Val (IDR)'"></span>
                                </td>

                                <template x-for="m in 12" :key="m">
                                    <td class="p-2 text-right font-mono">
                                        <span x-show="dataType === 'qty'" x-text="formatNumber(item.monthly[m].qty)"></span>
                                        <span x-show="dataType === 'val'" class="text-emerald-600 dark:text-emerald-400 font-semibold" x-text="formatNumber(item.monthly[m].qty * item.price_idr)"></span>
                                    </td>
                                </template>

                                <td class="p-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-700/50 sticky right-0" :class="dataType === 'val' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white'">
                                    <span x-text="dataType === 'qty' ? formatNumber(item.total_qty) : formatNumber(item.total_val)"></span>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function intlIdrSalesTab() {
    return {
        saving: false,
        processing: false,
        monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        rateUsd: 16200,
        rateBaht: 450,
        rateRinggit: 3600,
        filters: { region: 'ALL', search: '' },
        items: [],

        initData() {
            this.items = [
                {
                    id: 1,
                    product_code: 'EXP-USD-001',
                    product_name: 'Margarine Export Grade 15kg Box',
                    region: 'AMER',
                    price_idr: 396900,
                    total_qty: 6000,
                    total_val: 2381400000,
                    monthly: {
                        1: { qty: 500 }, 2: { qty: 500 }, 3: { qty: 500 }, 4: { qty: 500 },
                        5: { qty: 500 }, 6: { qty: 500 }, 7: { qty: 500 }, 8: { qty: 500 },
                        9: { qty: 500 }, 10: { qty: 500 }, 11: { qty: 500 }, 12: { qty: 500 }
                    }
                }
            ];
            this.recalculateAll();
        },

        get filteredItems() {
            return this.items.filter(item => {
                const matchSearch = item.product_name.toLowerCase().includes(this.filters.search.toLowerCase()) ||
                                    item.product_code.toLowerCase().includes(this.filters.search.toLowerCase());
                const matchRegion = this.filters.region === 'ALL' || item.region === this.filters.region;
                return matchSearch && matchRegion;
            });
        },

        recalculateAll() {
            this.items.forEach(item => {
                let sumQty = 0;
                for (let m = 1; m <= 12; m++) { sumQty += Number(item.monthly[m].qty || 0); }
                item.total_qty = sumQty;
                item.total_val = sumQty * item.price_idr;
            });
        },

        calculateGrandTotalVolume() {
            return this.filteredItems.reduce((acc, curr) => acc + (curr.total_qty || 0), 0);
        },

        calculateGrandTotalRevenue() {
            return this.filteredItems.reduce((acc, curr) => acc + (curr.total_val || 0), 0);
        },

        calculateAveragePrice() {
            const vol = this.calculateGrandTotalVolume();
            return vol > 0 ? this.calculateGrandTotalRevenue() / vol : 0;
        },

        formatNumber(val) {
            return new Intl.NumberFormat('id-ID').format(Math.round(val || 0));
        },

        processRates() {
            this.processing = true;
            this.recalculateAll();
            setTimeout(() => {
                this.processing = false;
                if (window.showToast) {
                    window.showToast('success', 'Rate berhasil diproses. Data IDR diperbarui.');
                } else {
                    alert('Rate berhasil diproses. Data IDR diperbarui.');
                }
            }, 400);
        },

        saveData() {
            this.saving = true;
            setTimeout(() => { this.saving = false; alert('Data International Sales (IDR) disinkronisasi!'); }, 500);
        },

        exportExcel() { alert('Exporting International Sales IDR data...'); }
    }
}
</script>