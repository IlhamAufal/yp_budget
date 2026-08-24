<div class="space-y-6">
    <div class="flex flex-col lg:flex-row items-stretch lg:items-end justify-between gap-5 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="flex-1 max-w-2xl">
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                Regional Area:
            </label>
            <div class="flex items-center gap-3">
                <select x-model="filters.regional" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2.5 px-3.5 focus:ring-2 focus:ring-[#2F3185]/20 focus:border-[#2F3185] transition-all">
                    <option value="">- All Regional Area -</option>
                    <option value="WEST">West Region (Sumatra & West Java)</option>
                    <option value="CENTRAL">Central Region (Central Java & DIY)</option>
                    <option value="EAST">East Region (East Java, Bali & Nusa)</option>
                    <option value="OUTER">Outer Region (Kalimantan & Sulawesi)</option>
                </select>
                <button type="button" @click="fetchRegionalData()" class="px-5 py-2.5 bg-[#2F3185] hover:bg-[#25276d] text-white text-xs font-semibold rounded-xl transition-all shadow-xs flex items-center justify-center gap-1.5 active:scale-[0.98] shrink-0">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Cari</span>
                </button>
            </div>
        </div>
        <div class="shrink-0 flex items-center lg:items-end">
            <button type="button" @click="exportRegionalExcel()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all inline-flex items-center justify-center gap-2 active:scale-[0.98]">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Report Regional Excel</span>
            </button>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Detail Breakdown Data Regional Sales Domestic</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rincian performa per regional, area, dan channel Sales Domestic.</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 overflow-hidden shadow-xs bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin max-h-[680px]">
            <table class="w-full text-left text-xs border-collapse min-w-[3400px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-center text-xs sticky top-0 z-20">
                    <tr class="bg-[#2F3185] text-white font-semibold border-b border-white/20">
                        <th class="px-3 py-3.5 border-r border-white/20 w-12 text-white font-semibold text-xs sticky left-0 z-30 bg-[#2F3185]" rowspan="2">No.</th>
                        <th class="px-3 py-3.5 border-r border-white/20 min-w-[120px] text-white font-semibold text-xs sticky left-12 z-30 bg-[#2F3185]" rowspan="2">Region</th>
                        <th class="px-3 py-3.5 border-r border-white/20 min-w-[160px] text-white font-semibold text-xs" rowspan="2">Country/Area</th>
                        <th class="px-3 py-3.5 border-r border-white/20 min-w-[130px] text-white font-semibold text-xs" rowspan="2">Code Inv</th>
                        <th class="px-3 py-3.5 border-r border-white/20 min-w-[280px] text-white font-semibold text-xs" rowspan="2">Product Name</th>
                        <th class="px-3 py-3.5 border-r border-white/20 min-w-[80px] text-white font-semibold text-xs text-center" rowspan="2">Div</th>
                        <th class="px-3 py-3.5 border-r border-white/20 min-w-[130px] text-white font-semibold text-xs" rowspan="2">Key Product</th>
                        <template x-for="(month, idx) in monthNames" :key="'reg-'+month">
                            <th class="px-3 py-2 border-r border-white/20 text-center text-white font-semibold text-xs bg-[#2F3185]" colspan="3" x-text="month"></th>
                        </template>
                        <th class="px-3 py-2 border-l border-white/20 bg-[#2F3185] text-center font-bold text-white text-xs" colspan="3">Total</th>
                    </tr>
                    <tr class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
                        <?php for ($i = 0; $i < 12; $i++): ?>
                            <th class="px-2 py-2 border-r border-white/20 w-[88px] min-w-[88px] text-center text-white font-semibold text-xs bg-[#2F3185]">Qty (Kg)</th>
                            <th class="px-2 py-2 border-r border-white/20 w-[120px] min-w-[120px] text-center text-white font-semibold text-xs bg-[#2F3185]">Revenue (Rp)</th>
                            <th class="px-2 py-2 border-r border-white/20 w-[96px] min-w-[96px] text-center text-white font-semibold text-xs bg-[#2F3185]">ASP/kg</th>
                        <?php endfor; ?>
                        <th class="px-2 py-2 border-r border-white/20 w-[120px] min-w-[120px] text-center bg-[#2F3185] text-white font-semibold text-xs">Qty (Kg)</th>
                        <th class="px-2 py-2 border-r border-white/20 w-[120px] min-w-[120px] text-center bg-[#2F3185] text-white font-semibold text-xs">Revenue (Rp)</th>
                        <th class="px-2 py-2 border-r border-white/20 w-[120px] min-w-[120px] text-center bg-[#2F3185] text-white font-semibold text-xs">ASP/kg</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-if="regionalData.length === 0">
                        <tr>
                            <td colspan="44" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-earth-asia text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data Regional Domestic</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Upload data regional via tab <strong>Upload Data</strong>.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(reg, idx) in regionalData" :key="reg.id || idx">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-3 py-2 text-center border-r border-gray-200 dark:border-gray-800 text-xs sticky left-0 z-10 bg-white dark:bg-gray-900 text-gray-500" x-text="idx + 1"></td>
                            <td class="px-3 py-2 font-sans font-semibold border-r border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-xs sticky left-12 z-10 bg-white dark:bg-gray-900" x-text="reg.region"></td>
                            <td class="px-3 py-2 font-sans border-r border-gray-200 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300" x-text="reg.country"></td>
                            <td class="px-3 py-2 border-r border-gray-200 dark:border-gray-800 text-xs text-gray-800 dark:text-gray-200 font-semibold" x-text="reg.id_inv"></td>
                            <td class="px-3 py-2 font-sans font-medium border-r border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white text-xs" x-text="reg.product_name"></td>
                            <td class="px-3 py-2 border-r border-gray-200 dark:border-gray-800 text-center text-xs" x-text="reg.div"></td>
                            <td class="px-3 py-2 font-sans border-r border-gray-200 dark:border-gray-800 text-xs text-gray-600 dark:text-gray-400" x-text="reg.key_product"></td>
                            <template x-for="col in metricCols" :key="'r-'+col.m+'-'+col.k">
                                <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-xs" :class="col.k === 'asp' ? 'bg-gray-50/50 dark:bg-gray-800/30' : (col.k === 'rev' ? 'text-emerald-600 dark:text-emerald-400' : '')" x-text="col.k === 'qty' ? formatNumber(reg.monthly[col.m].qty) : col.k === 'rev' ? formatNumber(reg.monthly[col.m].revenue) : formatNumber(calculateASP(reg.monthly[col.m].revenue, reg.monthly[col.m].qty))"></td>
                            </template>
                            <td class="px-3 py-2 text-right border-l-2 border-[#2F3185]/30 border-r border-gray-200 dark:border-gray-800 font-bold text-xs bg-[#2F3185]/5 dark:bg-gray-800" x-text="formatNumber(reg.total_qty)"></td>
                            <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800 font-bold text-xs text-emerald-600 dark:text-emerald-400 bg-[#2F3185]/5 dark:bg-gray-800" x-text="formatNumber(reg.total_revenue)"></td>
                            <td class="px-3 py-2 text-right font-bold text-xs text-amber-600 dark:text-amber-400 bg-[#2F3185]/5 dark:bg-gray-800" x-text="formatNumber(calculateASP(reg.total_revenue, reg.total_qty))"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>