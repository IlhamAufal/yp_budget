<div x-data="domesticTabContainer()" x-init="initData()" class="space-y-6">

    <div class="bg-sky-50/80 dark:bg-gray-800 p-5 rounded-xl border border-sky-100 dark:border-gray-700 shadow-sm space-y-4">
        
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            <div class="w-32 flex-shrink-0">
                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">Key Product</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 flex-1">
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">Global - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.global_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">Global - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.global_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">GUMMY - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.gummy_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">GUMMY - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.gummy_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">BOLI - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.boli_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">BOLI - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.boli_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">EXTRUDER - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.extruder_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">EXTRUDER - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.extruder_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-sky-200 dark:border-gray-700 my-2">

        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            <div class="w-32 flex-shrink-0">
                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">Channel</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 flex-1">
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">GT - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.gt_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">GT - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.gt_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">MT - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.mt_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">MT - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.mt_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">OEM - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.oem_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">OEM - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.oem_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-end justify-end">
                    <button type="button" @click="processAdjustment()" :disabled="processing" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded shadow transition-colors flex items-center gap-1.5 disabled:opacity-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                        <span x-text="processing ? 'Processing...' : 'Process'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-[11px] border-collapse min-w-[2400px]">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold uppercase border-b border-gray-300 dark:border-gray-600 text-center">
                    <tr>
                        <th class="p-2 border-r border-gray-300 dark:border-gray-600 w-10" rowspan="2">No.</th>
                        <th class="p-2 border-r border-gray-300 dark:border-gray-600 min-w-[100px]" rowspan="2">CODE INV</th>
                        <th class="p-2 border-r border-gray-300 dark:border-gray-600 min-w-[120px]" rowspan="2">KEY PRODUCT</th>
                        <th class="p-2 border-r border-gray-300 dark:border-gray-600 min-w-[100px]" rowspan="2">CODE INV</th>
                        <th class="p-2 border-r border-gray-300 dark:border-gray-600 min-w-[200px]" rowspan="2">NAME PRODUCT</th>
                        
                        <template x-for="month in monthNames" :key="month">
                            <th class="p-2 border-r border-gray-300 dark:border-gray-600 text-center" colspan="3" x-text="month"></th>
                        </template>
                        
                        <th class="p-2 border-l-2 border-gray-400 bg-gray-200 dark:bg-gray-800 text-center" colspan="3">TOTAL</th>
                    </tr>
                    <tr class="bg-gray-50 dark:bg-gray-800 text-[10px]">
                        <template x-for="i in 13" :key="i">
                            <template x-fragment>
                                <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-16 text-center">QTY</th>
                                <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-24 text-center">REVENUE</th>
                                <th class="p-1 border-r border-gray-200 dark:border-gray-700 w-20 text-center" :class="i === 13 ? 'border-r-0' : ''">ASP/kg</th>
                            </template>
                        </template>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 font-mono">
                    <template x-for="(item, idx) in items" :key="item.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="p-2 text-center border-r border-gray-200 dark:border-gray-700" x-text="idx + 1"></td>
                            <td class="p-2 border-r border-gray-200 dark:border-gray-700" x-text="item.code_inv_1"></td>
                            <td class="p-2 border-r border-gray-200 dark:border-gray-700 font-sans" x-text="item.key_product"></td>
                            <td class="p-2 border-r border-gray-200 dark:border-gray-700" x-text="item.code_inv_2"></td>
                            <td class="p-2 border-r border-gray-200 dark:border-gray-700 font-sans font-medium text-gray-900 dark:text-white" x-text="item.name_product"></td>
                            
                            <template x-for="m in 12" :key="m">
                                <template x-fragment>
                                    <td class="p-1 border-r border-gray-200 dark:border-gray-700">
                                        <input type="number" x-model.number="item.monthly[m].qty" @input="recalculateRow(item)" class="w-full text-right p-1 text-[11px] border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </td>
                                    <td class="p-1 border-r border-gray-200 dark:border-gray-700">
                                        <input type="number" x-model.number="item.monthly[m].revenue" @input="recalculateRow(item)" class="w-full text-right p-1 text-[11px] border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-emerald-600 dark:text-emerald-400 font-semibold">
                                    </td>
                                    <td class="p-1 border-r border-gray-200 dark:border-gray-700 text-right bg-gray-50/50 dark:bg-gray-800/50 font-medium" x-text="formatNumber(calculateASP(item.monthly[m].revenue, item.monthly[m].qty))"></td>
                                </template>
                            </template>

                            <td class="p-2 border-l-2 border-gray-400 bg-gray-100 dark:bg-gray-700 text-right font-bold text-gray-900 dark:text-white" x-text="formatNumber(item.total_qty)"></td>
                            <td class="p-2 border-r border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-right font-bold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(item.total_revenue)"></td>
                            <td class="p-2 bg-gray-100 dark:bg-gray-700 text-right font-bold text-gray-900 dark:text-white" x-text="formatNumber(calculateASP(item.total_revenue, item.total_qty))"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function domesticTabContainer() {
    return {
        processing: false,
        monthNames: ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'],
        adjustment: {
            global_vol: 0, global_asp: 0,
            gummy_vol: 0, gummy_asp: 0,
            boli_vol: 0, boli_asp: 0,
            extruder_vol: 0, extruder_asp: 0,
            gt_vol: 0, gt_asp: 0,
            mt_vol: 0, mt_asp: 0,
            oem_vol: 0, oem_asp: 0
        },
        items: [],

        initData() {
            this.items = [
                {
                    id: 1, 
                    code_inv_1: 'INV-01', 
                    key_product: 'GUMMY', 
                    code_inv_2: 'INV-01-A', 
                    name_product: 'Yupi Gummy Bear 100g Box',
                    total_qty: 12000, 
                    total_revenue: 120000000,
                    monthly: {
                        1: {qty: 1000, revenue: 10000000}, 2: {qty: 1000, revenue: 10000000}, 3: {qty: 1000, revenue: 10000000},
                        4: {qty: 1000, revenue: 10000000}, 5: {qty: 1000, revenue: 10000000}, 6: {qty: 1000, revenue: 10000000},
                        7: {qty: 1000, revenue: 10000000}, 8: {qty: 1000, revenue: 10000000}, 9: {qty: 1000, revenue: 10000000},
                        10: {qty: 1000, revenue: 10000000}, 11: {qty: 1000, revenue: 10000000}, 12: {qty: 1000, revenue: 10000000}
                    }
                }
            ];
            this.items.forEach(i => this.recalculateRow(i));
        },

        recalculateRow(item) {
            let sumQty = 0, sumRev = 0;
            for (let m = 1; m <= 12; m++) {
                sumQty += Number(item.monthly[m].qty || 0);
                sumRev += Number(item.monthly[m].revenue || 0);
            }
            item.total_qty = sumQty;
            item.total_revenue = sumRev;
        },

        calculateASP(revenue, qty) {
            return qty > 0 ? (revenue / qty) : 0;
        },

        formatNumber(val) {
            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(val || 0);
        },

        processAdjustment() {
            this.processing = true;
            setTimeout(() => {
                this.processing = false;
                alert('Proses kalkulasi penyesuaian rate simulasi (%) berhasil!');
            }, 600);
        }
    }
}
</script>