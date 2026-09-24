<div x-data="intlIdrSalesTab()" x-init="initData()" class="space-y-6">

    <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 flex-1">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Rate US$ (IDR)</label>
                    <input type="number" step="1" x-model.number="rateUsd" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 py-2.5 px-3.5 font-mono font-semibold" placeholder="16200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Rate Baht Thailand (IDR)</label>
                    <input type="number" step="1" x-model.number="rateBaht" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 py-2.5 px-3.5 font-mono font-semibold" placeholder="450">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Rate Ringgit Malaysia (IDR)</label>
                    <input type="number" step="1" x-model.number="rateRinggit" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 py-2.5 px-3.5 font-mono font-semibold" placeholder="3600">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="processRates()" :disabled="processing" class="px-5 py-2.5 bg-[#2F3185] hover:bg-[#25276d] text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 disabled:opacity-50 active:scale-[0.98]">
                    <i x-show="!processing" class="fa-solid fa-gear"></i>
                    <i x-show="processing" class="fa-solid fa-spinner fa-spin"></i>
                    <span x-text="processing ? 'Memproses...' : 'Proses Kurs'"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total International Volume</p>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-1.5" x-text="formatNumber(calculateGrandTotalVolume()) + ' Box'">0 Box</h3>
            <p class="text-[11px] text-[#2F3185] dark:text-indigo-400 mt-1 font-medium">International Total Shipment</p>
        </div>

        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Revenue Equivalent (IDR)</p>
            <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1.5" x-text="'Rp ' + formatNumber(calculateGrandTotalRevenue())">Rp 0</h3>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Export Sales Value in Local Currency</p>
        </div>

        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Average Price / Box (IDR)</p>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-1.5" x-text="'Rp ' + formatNumber(calculateAveragePrice())">Rp 0</h3>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Weighted Converted Price</p>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Matriks International Sales dalam IDR (12 Bulan)</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Konversi otomatis dari estimasi nilai valas ke IDR per bulan (QTY, Revenue, ASP).</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[4600px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs sticky top-0 z-20">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th rowspan="2" class="px-4 py-3 sticky left-0 z-30 bg-[#2F3185] text-white font-semibold min-w-[240px] border-r border-white/20">Product SKU</th>
                        <th rowspan="2" class="px-3 py-3 w-24 text-center text-white font-semibold border-r border-white/20">Currency</th>
                        <th rowspan="2" class="px-3.5 py-3 w-36 text-right text-white font-semibold border-r border-white/20">Price Eq (IDR)</th>
                        <template x-for="month in monthNames" :key="month">
                            <th colspan="3" class="px-3 py-2.5 text-center text-white font-semibold border-r border-white/20" x-text="month"></th>
                        </template>
                        <th colspan="3" class="px-3 py-2.5 text-center text-white font-bold bg-[#25276d] border-l border-white/20">Annual Total</th>
                    </tr>
                    <tr class="bg-[#25276d] text-white text-xs font-semibold">
                        <?php for ($i = 0; $i < 12; $i++): ?>
                            <th class="px-2 py-2 border-r border-white/20 w-20 text-center text-white font-semibold text-xs bg-[#25276d]">Qty (Box)</th>
                            <th class="px-2 py-2 border-r border-white/20 w-32 text-center text-white font-semibold text-xs bg-[#25276d]">Revenue</th>
                            <th class="px-2 py-2 border-r border-white/20 w-24 text-center text-white font-semibold text-xs bg-[#25276d]">ASP/kg</th>
                        <?php endfor; ?>
                        <th class="px-2 py-2 border-r border-white/20 w-24 text-center text-white bg-[#25276d] font-semibold text-xs">Tot Qty</th>
                        <th class="px-2 py-2 border-r border-white/20 w-36 text-center text-white bg-[#25276d] font-semibold text-xs">Tot Rev</th>
                        <th class="px-2 py-2 text-center text-white bg-[#25276d] font-semibold text-xs">Avg ASP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-for="item in filteredItems" :key="item.id">
                        <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-3 font-sans font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800">
                                <div class="font-semibold text-gray-900 dark:text-white" x-text="item.product_name"></div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5" x-text="item.product_code + ' | Region: ' + item.region"></div>
                            </td>

                            <td class="px-3 py-3 text-center font-sans font-bold text-gray-800 dark:text-gray-200 border-r border-gray-200 dark:border-gray-800" x-text="item.currency"></td>

                            <td class="px-3.5 py-3 font-mono text-right font-bold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800" x-text="formatNumber(item.price_idr)"></td>

                            <template x-for="cell in metricCols" :key="cell.m + '-' + cell.k">
                                <td class="px-2 py-2 text-right font-mono border-r border-gray-200 dark:border-gray-800">
                                    <span x-show="cell.k === 'qty'" x-text="formatNumber(item.monthly[cell.m].qty)"></span>
                                    <span x-show="cell.k === 'rev'" class="text-emerald-600 dark:text-emerald-400 font-semibold" x-text="formatNumber(item.monthly[cell.m].qty * item.price_idr)"></span>
                                    <span x-show="cell.k === 'asp'" class="text-amber-600 dark:text-amber-400" x-text="formatNumber(item.monthly[cell.m].qty > 0 ? item.price_idr : 0)"></span>
                                </td>
                            </template>

                            <td class="px-2 py-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-800" x-text="formatNumber(item.total_qty)"></td>
                            <td class="px-2 py-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400" x-text="formatNumber(item.total_val)"></td>
                            <td class="px-2 py-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-800 text-amber-600 dark:text-amber-400" x-text="formatNumber(item.total_qty > 0 ? item.total_val / item.total_qty : 0)"></td>
                        </tr>
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
        metricCols: (() => {
            const arr = [];
            for (let m = 1; m <= 12; m++) arr.push({ m, k: 'qty' }, { m, k: 'rev' }, { m, k: 'asp' });
            return arr;
        })(),
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
                    currency: 'USD',
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
