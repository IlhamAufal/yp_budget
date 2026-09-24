<div class="space-y-6">
    <div class="flex flex-col lg:flex-row items-stretch lg:items-end justify-between gap-5 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="flex-1 max-w-2xl">
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                Country:
            </label>
            <div class="flex items-center gap-3">
                <select x-model="filters.country" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2.5 px-3.5 focus:ring-2 focus:ring-[#2F3185]/20 focus:border-[#2F3185] transition-all">
                    <option value="">- All Country -</option>
                    <option value="USA">United States</option>
                    <option value="TH">Thailand</option>
                    <option value="MY">Malaysia</option>
                    <option value="TW">Taiwan</option>
                </select>
                <button type="button" @click="fetchCountryData()" class="px-5 py-2.5 bg-[#2F3185] hover:bg-[#25276d] text-white text-xs font-semibold rounded-xl transition-all shadow-xs flex items-center justify-center gap-1.5 active:scale-[0.98] shrink-0">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Cari</span>
                </button>
            </div>
        </div>
        <div class="shrink-0 flex items-center lg:items-end">
            <button type="button" @click="exportCountryExcel()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all inline-flex items-center justify-center gap-2 active:scale-[0.98]">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Report Country Excel</span>
            </button>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Detail Sales International per Negara</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rincian performa per negara tujuan ekspor dalam Valas USD ($).</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 overflow-hidden shadow-xs bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th class="px-3.5 py-3.5 text-center text-white font-semibold w-12 border-r border-white/20">No.</th>
                        <th class="px-3.5 py-3.5 text-white font-semibold min-w-[120px] border-r border-white/20">Region</th>
                        <th class="px-3.5 py-3.5 text-white font-semibold min-w-[140px] border-r border-white/20">Country</th>
                        <th class="px-3.5 py-3.5 text-white font-semibold min-w-[240px] border-r border-white/20">Product Name</th>
                        <th class="px-3.5 py-3.5 text-white font-semibold min-w-[140px] border-r border-white/20">Key Product</th>
                        <th class="px-3.5 py-3.5 text-right text-white font-semibold min-w-[120px] border-r border-white/20">Tot Qty</th>
                        <th class="px-3.5 py-3.5 text-right text-white font-semibold min-w-[140px]">Tot Rev ($)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-if="countryDetails.length === 0">
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-earth-asia text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data Report Country</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Silakan pilih negara atau upload data.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(c, idx) in countryDetails" :key="c.id || idx">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-3.5 py-2.5 text-center text-gray-500 border-r border-gray-200 dark:border-gray-800" x-text="idx + 1"></td>
                            <td class="px-3.5 py-2.5 font-sans font-semibold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800" x-text="c.region"></td>
                            <td class="px-3.5 py-2.5 font-sans text-gray-800 dark:text-gray-200 border-r border-gray-200 dark:border-gray-800" x-text="c.country"></td>
                            <td class="px-3.5 py-2.5 font-sans font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800" x-text="c.product_name"></td>
                            <td class="px-3.5 py-2.5 font-sans text-gray-600 dark:text-gray-400 border-r border-gray-200 dark:border-gray-800" x-text="c.key_product"></td>
                            <td class="px-3.5 py-2.5 text-right font-bold border-r border-gray-200 dark:border-gray-800" x-text="formatNumber(c.total_qty)"></td>
                            <td class="px-3.5 py-2.5 text-right text-emerald-600 dark:text-emerald-400 font-bold" x-text="formatValas(c.total_revenue)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
