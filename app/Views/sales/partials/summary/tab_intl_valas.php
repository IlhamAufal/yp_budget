<div x-data="intlValasSalesTab()" x-init="initData()" class="space-y-6">

    <!-- <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="w-full sm:w-36">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Mata Uang (Valas)</label>
                <select x-model="filters.currency" @change="updateExchangeRate()" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary font-bold">
                    <option value="USD">USD ($)</option>
                    <option value="EUR">EUR (€)</option>
                    <option value="SGD">SGD (S$)</option>
                    <option value="JPY">JPY (¥)</option>
                    <option value="RMB">RMB (¥)</option>
                </select>
            </div>

            <div class="w-full sm:w-44">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Kurs FX to IDR</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-xs text-gray-400 font-semibold">Rp</span>
                    <input type="number" x-model.number="fxRate" @input="recalculateAll()" class="w-full pl-8 text-right text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary font-semibold">
                </div>
            </div>

            <div class="w-full sm:w-44">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Negara Tujuan</label>
                <select x-model="filters.country" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary">
                    <option value="ALL">All Countries</option>
                    <option value="USA">United States</option>
                    <option value="JPN">Japan</option>
                    <option value="SGP">Singapore</option>
                    <option value="CHN">China</option>
                </select>
            </div>

            <div class="w-full sm:w-48">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Cari SKU Export</label>
                <input type="text" x-model="filters.search" placeholder="Nama / Kode SKU..." class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary">
            </div>
        </div>

        <div class="flex items-center gap-2 self-end lg:self-auto">
            <button type="button" @click="exportExcel()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Export Valas Excel</span>
            </button>
            
            <button type="button" @click="saveMatrixData()" :disabled="saving" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors disabled:opacity-50">
                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span x-text="saving ? 'Menyimpan...' : 'Simpan Data Valas'"></span>
            </button>
        </div>
    </div> -->

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
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rincian kuantiti, nilai valas, dan konversi IDR per SKU ekspor.</p>
        </div>
        <span class="text-xs font-semibold text-[#2F3185] dark:text-indigo-400" x-text="'Satuan Price/Valas: ' + filters.currency"></span>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1600px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th class="px-4 py-3.5 sticky left-0 z-10 bg-[#2F3185] text-white font-semibold min-w-[240px] border-r border-white/20">Product SKU / Country</th>
                        <th class="px-3.5 py-3.5 w-32 text-right text-white font-semibold border-r border-white/20" x-text="'Unit Price (' + filters.currency + ')'">Price</th>
                        <th class="px-3 py-3.5 w-28 text-center text-white font-semibold border-r border-white/20">Data Row</th>
                        <template x-for="(month, idx) in monthNames" :key="idx">
                            <th class="px-3 py-3.5 text-center text-white font-semibold w-24 border-r border-white/20" x-text="month"></th>
                        </template>
                        <th class="px-4 py-3.5 text-right w-36 bg-[#2F3185] text-white font-bold sticky right-0">Total Year</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-for="item in filteredItems" :key="item.id">
                        <template x-for="dataType in ['qty', 'val_valas', 'val_idr']">
                            <tr :class="dataType === 'val_idr' ? 'bg-gray-50/50 dark:bg-gray-800/50' : 'hover:bg-gray-50/30 dark:hover:bg-gray-800/30'">
                                <template x-if="dataType === 'qty'">
                                    <td class="px-4 py-3 font-sans font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800" rowspan="3">
                                        <div class="font-semibold text-gray-900 dark:text-white" x-text="item.product_name"></div>
                                        <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5" x-text="item.product_code + ' | Dest: ' + item.destination_country"></div>
                                    </td>
                                </template>

                                <template x-if="dataType === 'qty'">
                                    <td class="px-3.5 py-3 font-mono text-right font-bold text-amber-600 dark:text-amber-400 border-r border-gray-200 dark:border-gray-800" rowspan="3">
                                        <input type="number" step="0.01" x-model.number="item.price_valas" @input="calculateRowTotal(item)" class="w-full text-right text-xs py-1 px-2 border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-1 focus:ring-[#2F3185]/20 outline-none">
                                    </td>
                                </template>

                                <td class="px-3 py-2.5 text-center font-sans font-medium border-r border-gray-200 dark:border-gray-800">
                                    <span :class="{
                                        'bg-blue-50 text-[#2F3185] dark:bg-blue-900/30 dark:text-blue-300': dataType === 'qty',
                                        'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300': dataType === 'val_valas',
                                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300': dataType === 'val_idr'
                                    }" class="px-2 py-0.5 rounded-md text-[10px] font-bold" 
                                    x-text="dataType === 'qty' ? 'Vol (Box)' : (dataType === 'val_valas' ? 'Val (' + filters.currency + ')' : 'Val (IDR)')"></span>
                                </td>

                                <template x-for="m in 12" :key="m">
                                    <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-800">
                                        <template x-if="dataType === 'qty'">
                                            <input type="number" x-model.number="item.monthly[m].qty" @input="calculateRowTotal(item)" class="w-full text-right text-xs py-1 px-2 border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-1 focus:ring-[#2F3185]/20 outline-none">
                                        </template>

                                        <template x-if="dataType === 'val_valas'">
                                            <div class="text-right py-1 px-2 font-mono text-amber-600 dark:text-amber-400 font-semibold" x-text="formatNumber(item.monthly[m].qty * item.price_valas)"></div>
                                        </template>

                                        <template x-if="dataType === 'val_idr'">
                                            <div class="text-right py-1 px-2 font-mono text-emerald-600 dark:text-emerald-400 font-semibold" x-text="formatNumber((item.monthly[m].qty * item.price_valas) * fxRate)"></div>
                                        </template>
                                    </td>
                                </template>

                                <td class="px-4 py-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-800 sticky right-0" :class="{
                                    'text-gray-900 dark:text-white': dataType === 'qty',
                                    'text-amber-600 dark:text-amber-400': dataType === 'val_valas',
                                    'text-emerald-600 dark:text-emerald-400': dataType === 'val_idr'
                                }">
                                    <span x-text="dataType === 'qty' ? formatNumber(item.total_qty) : (dataType === 'val_valas' ? formatNumber(item.total_valas) : formatNumber(item.total_idr))"></span>
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
function intlValasSalesTab() {
    return {
        saving: false,
        fxRate: 16200,
        monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
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