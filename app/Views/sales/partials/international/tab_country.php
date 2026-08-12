<div class="space-y-6">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="flex items-center gap-3">
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-300"><i class="fa-solid fa-globe text-primary mr-1"></i> Country:</label>
            <select x-model="filters.country" class="text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 py-2 px-3 min-w-[200px]">
                <option value="">- All Country -</option>
                <option value="USA">United States</option>
                <option value="TH">Thailand</option>
                <option value="MY">Malaysia</option>
                <option value="TW">Taiwan</option>
            </select>
            <button type="button" @click="fetchCountryData()" class="px-3.5 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold rounded-xl flex items-center gap-1">
                <i class="fa-solid fa-magnifying-glass"></i> Cari
            </button>
        </div>
        <button type="button" @click="exportCountryExcel()" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl flex items-center gap-1.5">
            <i class="fa-solid fa-file-excel"></i> Export International Country Excel
        </button>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800 p-6">
        <h3 class="text-xs font-bold uppercase tracking-wider mb-4"><i class="fa-solid fa-flag text-indigo-500 mr-1"></i> Detail International per Negara</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-800 font-bold border-b border-gray-300 dark:border-gray-700">
                    <tr>
                        <th class="p-2">No.</th><th class="p-2">Region</th><th class="p-2">Country</th><th class="p-2">Product</th><th class="p-2">Key Product</th><th class="p-2 text-right">Tot QTY</th><th class="p-2 text-right">Tot REV ($)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    <template x-if="countryDetails.length === 0">
                        <tr><td colspan="7" class="p-8 text-center text-gray-400">Belum ada data report country.</td></tr>
                    </template>
                    <template x-for="(c, idx) in countryDetails" :key="c.id || idx">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="p-2 text-center" x-text="idx + 1"></td>
                            <td class="p-2 font-semibold" x-text="c.region"></td>
                            <td class="p-2" x-text="c.country"></td>
                            <td class="p-2" x-text="c.product_name"></td>
                            <td class="p-2" x-text="c.key_product"></td>
                            <td class="p-2 text-right font-mono" x-text="formatNumber(c.total_qty)"></td>
                            <td class="p-2 text-right font-mono text-amber-600" x-text="formatValas(c.total_revenue)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
