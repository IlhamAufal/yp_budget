<div x-data="intlValasSalesTab()" x-init="initData()" class="space-y-6">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Export Volume</p>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-1.5" x-text="formatNumber(calculateGrandTotalVolume()) + ' Box'">0 Box</h3>
            <p class="text-[11px] text-[#2F3185] dark:text-indigo-400 mt-1 font-medium">International Shipment Target</p>
        </div>

        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400" x-text="'Total Revenue (' + filters.currency + ')'">Total Revenue (USD)</p>
            <h3 class="text-xl font-bold text-amber-600 dark:text-amber-400 mt-1.5" x-text="getCurrencySymbol(filters.currency) + ' ' + formatNumber(calculateGrandTotalValas())">0</h3>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Valas Original Revenue</p>
        </div>

        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">IDR Equivalent (Converted)</p>
            <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1.5" x-text="'Rp ' + formatNumber(calculateGrandTotalIDR())">Rp 0</h3>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1" x-text="'At FX Rate Rp ' + formatNumber(fxRate)">At FX Rate</p>
        </div>

        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Active FX Currency</p>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-1.5" x-text="filters.currency">USD</h3>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Selected Target Currency</p>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Matriks Sales Export Valas (Multi-Currency 12 Bulan)</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rincian kuantiti, revenue valas, dan ASP per SKU ekspor per bulan.</p>
        </div>
        <span class="text-xs font-semibold text-[#2F3185] dark:text-indigo-400" x-text="'Satuan Price/Valas: ' + filters.currency"></span>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[4400px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs sticky top-0 z-20">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th rowspan="2" class="px-4 py-3 sticky left-0 z-30 bg-[#2F3185] text-white font-semibold min-w-[240px] border-r border-white/20">Product SKU / Country</th>
                        <th rowspan="2" class="px-3.5 py-3 w-32 text-right text-white font-semibold border-r border-white/20" x-text="'Unit Price (' + filters.currency + ')'">Price</th>
                        <template x-for="month in monthNames" :key="month">
                            <th colspan="3" class="px-3 py-2.5 text-center text-white font-semibold border-r border-white/20" x-text="month"></th>
                        </template>
                        <th colspan="3" class="px-3 py-2.5 text-center text-white font-bold bg-[#25276d] border-l border-white/20">Annual Total</th>
                    </tr>
                    <tr class="bg-[#25276d] text-white text-xs font-semibold">
                        <?php for ($i = 0; $i < 12; $i++): ?>
                            <th class="px-2 py-2 border-r border-white/20 w-20 text-center text-white font-semibold text-xs bg-[#25276d]">Qty (Box)</th>
                            <th class="px-2 py-2 border-r border-white/20 w-28 text-center text-white font-semibold text-xs bg-[#25276d]" x-text="'Revenue (' + filters.currency + ')'">Revenue</th>
                            <th class="px-2 py-2 border-r border-white/20 w-24 text-center text-white font-semibold text-xs bg-[#25276d]">ASP/kg</th>
                        <?php endfor; ?>
                        <th class="px-2 py-2 border-r border-white/20 w-24 text-center text-white bg-[#25276d] font-semibold text-xs">Tot Qty</th>
                        <th class="px-2 py-2 border-r border-white/20 w-32 text-center text-white bg-[#25276d] font-semibold text-xs" x-text="'Tot Rev (' + filters.currency + ')'">Tot Rev</th>
                        <th class="px-2 py-2 text-center text-white bg-[#25276d] font-semibold text-xs">Avg ASP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-for="item in filteredItems" :key="item.id">
                        <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-3 font-sans font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800">
                                <div class="font-semibold text-gray-900 dark:text-white" x-text="item.product_name"></div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5" x-text="item.product_code + ' | Dest: ' + item.destination_country"></div>
                            </td>

                            <td class="px-3.5 py-3 font-mono text-right font-bold text-amber-600 dark:text-amber-400 border-r border-gray-200 dark:border-gray-800">
                                <input type="number" step="0.01" x-model.number="item.price_valas" @input="recalculateAll()" class="w-full text-right text-xs py-1 px-2 border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-1 focus:ring-[#2F3185]/20 outline-none">
                            </td>

                            <template x-for="cell in metricCols" :key="cell.m + '-' + cell.k">
                                <td class="px-2 py-2 border-r border-gray-200 dark:border-gray-800">
                                    <template x-if="cell.k === 'qty'">
                                        <input type="number" x-model.number="item.monthly[cell.m].qty" @input="recalculateAll()" class="w-full text-right text-xs py-1 px-2 border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-1 focus:ring-[#2F3185]/20 outline-none">
                                    </template>
                                    <template x-if="cell.k === 'rev'">
                                        <div class="text-right py-1 px-2 font-mono text-amber-600 dark:text-amber-400 font-semibold" x-text="formatNumber(item.monthly[cell.m].qty * item.price_valas)"></div>
                                    </template>
                                    <template x-if="cell.k === 'asp'">
                                        <div class="text-right py-1 px-2 font-mono" x-text="formatNumber(item.monthly[cell.m].qty > 0 ? item.price_valas : 0)"></div>
                                    </template>
                                </td>
                            </template>

                            <td class="px-2 py-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-800" x-text="formatNumber(item.total_qty)"></td>
                            <td class="px-2 py-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400" x-text="formatNumber(item.total_valas)"></td>
                            <td class="px-2 py-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-800" x-text="formatNumber(item.total_qty > 0 ? item.total_valas / item.total_qty : 0)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function intlValasSalesTab() {
    return {
        saving: false,
        fxRate: 16200,
        monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        metricCols: (() => {
            const arr = [];
            for (let m = 1; m <= 12; m++) arr.push({ m, k: 'qty' }, { m, k: 'rev' }, { m, k: 'asp' });
            return arr;
        })(),
        filters: { currency: 'USD', country: 'ALL', search: '' },
        items: [],

        initData() {
            this.items = [
                {
                    id: 1,
                    product_code: 'EXP-USD-001',
                    product_name: 'Margarine Export Grade 15kg Box',
                    destination_country: 'USA',
                    price_valas: 24.50,
                    total_qty: 6000,
                    total_valas: 147000,
                    total_idr: 2381400000,
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
                const matchCountry = this.filters.country === 'ALL' || item.destination_country === this.filters.country;
                return matchSearch && matchCountry;
            });
        },

        updateExchangeRate() {
            const defaultRates = { USD: 16200, EUR: 17500, SGD: 12100, JPY: 105, RMB: 2250 };
            this.fxRate = defaultRates[this.filters.currency] || 16000;
            this.recalculateAll();
        },

        calculateRowTotal(item) {
            let sumQty = 0;
            for (let m = 1; m <= 12; m++) {
                sumQty += Number(item.monthly[m].qty || 0);
            }
            item.total_qty = sumQty;
            item.total_valas = sumQty * (item.price_valas || 0);
            item.total_idr = item.total_valas * this.fxRate;
        },

        recalculateAll() {
            this.items.forEach(item => this.calculateRowTotal(item));
        },

        calculateGrandTotalVolume() {
            return this.filteredItems.reduce((acc, curr) => acc + (curr.total_qty || 0), 0);
        },

        calculateGrandTotalValas() {
            return this.filteredItems.reduce((acc, curr) => acc + (curr.total_valas || 0), 0);
        },

        calculateGrandTotalIDR() {
            return this.filteredItems.reduce((acc, curr) => acc + (curr.total_idr || 0), 0);
        },

        getCurrencySymbol(curr) {
            const symbols = { USD: '$', EUR: '€', SGD: 'S$', JPY: '¥', RMB: '¥' };
            return symbols[curr] || '$';
        },

        formatNumber(val) {
            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(val || 0);
        },

        saveMatrixData() {
            this.saving = true;
            setTimeout(() => {
                this.saving = false;
                alert('Data International Sales (Valas) berhasil disimpan!');
            }, 600);
        },

        exportExcel() {
            alert('Exporting International Sales (Valas) data to Excel...');
        }
    }
}
</script>
