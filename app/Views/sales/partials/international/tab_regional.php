<div class="space-y-6">
    <div class="flex items-center justify-between gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="flex items-center gap-3">
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-300"><i class="fa-solid fa-chart-pie text-primary mr-1"></i> Regional:</label>
            <select x-model="filters.regional" class="text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 py-2 px-3 min-w-[200px]">
                <option value="">- All Regional -</option>
                <option value="ASIA">Asia Pacific</option>
                <option value="AMER">Americas</option>
                <option value="EUROPE">Europe</option>
            </select>
            <button type="button" @click="fetchRegionalData()" class="px-3.5 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold rounded-xl flex items-center gap-1">
                <i class="fa-solid fa-magnifying-glass"></i> Cari
            </button>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800 p-6">
        <h3 class="text-xs font-bold uppercase tracking-wider mb-4"><i class="fa-solid fa-list-check text-indigo-500 mr-1"></i> Summary Regional Area</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-800 font-bold border-b border-gray-300 dark:border-gray-700">
                    <tr><th class="p-3">Regional</th><th class="p-3 text-right">Total QTY</th><th class="p-3 text-right">Total REV ($)</th><th class="p-3 text-right">AVG ASP ($)</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    <template x-if="regionalSummaries.length === 0">
                        <tr><td colspan="4" class="p-8 text-center text-gray-400">Belum ada summary regional.</td></tr>
                    </template>
                    <template x-for="reg in regionalSummaries" :key="reg.region">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="p-2.5 font-bold" x-text="reg.region"></td>
                            <td class="p-2.5 text-right font-mono" x-text="formatNumber(reg.total_qty)"></td>
                            <td class="p-2.5 text-right font-mono text-amber-600" x-text="formatValas(reg.total_revenue)"></td>
                            <td class="p-2.5 text-right font-mono text-emerald-600" x-text="formatValas(calculateASP(reg.total_revenue, reg.total_qty))"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
