<div class="space-y-6">
    <div class="flex flex-col lg:flex-row items-stretch lg:items-end justify-between gap-5 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="flex-1 max-w-2xl">
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                Regional Area:
            </label>
            <div class="flex items-center gap-3">
                <select x-model="filters.regional" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2.5 px-3.5 focus:ring-2 focus:ring-[#2F3185]/20 focus:border-[#2F3185] transition-all">
                    <option value="">- All Regional -</option>
                    <option value="ASIA">Asia Pacific</option>
                    <option value="AMER">Americas</option>
                    <option value="EUROPE">Europe</option>
                </select>
                <button type="button" @click="fetchRegionalData()" class="px-5 py-2.5 bg-[#2F3185] hover:bg-[#25276d] text-white text-xs font-semibold rounded-xl transition-all shadow-xs flex items-center justify-center gap-1.5 active:scale-[0.98] shrink-0">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Cari</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Summary Regional Area ($)</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rekapitulasi target volume dan revenue per regional area ekspor.</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 overflow-hidden shadow-xs bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th class="px-4 py-3.5 text-white font-semibold border-r border-white/20">Regional</th>
                        <th class="px-4 py-3.5 text-right text-white font-semibold min-w-[140px] border-r border-white/20">Total Qty</th>
                        <th class="px-4 py-3.5 text-right text-white font-semibold min-w-[160px] border-r border-white/20">Total Rev (USD $)</th>
                        <th class="px-4 py-3.5 text-right text-white font-semibold min-w-[140px]">Avg ASP ($/kg)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-if="regionalSummaries.length === 0">
                        <tr>
                            <td colspan="4" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-chart-pie text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Summary Regional</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Silakan pilih regional atau upload data.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-for="reg in regionalSummaries" :key="reg.region">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-sans font-semibold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800" x-text="reg.region"></td>
                            <td class="px-4 py-3 text-right font-bold border-r border-gray-200 dark:border-gray-800" x-text="formatNumber(reg.total_qty)"></td>
                            <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 font-bold border-r border-gray-200 dark:border-gray-800" x-text="formatValas(reg.total_revenue)"></td>
                            <td class="px-4 py-3 text-right text-amber-600 dark:text-amber-400 font-bold" x-text="formatValas(calculateASP(reg.total_revenue, reg.total_qty))"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
