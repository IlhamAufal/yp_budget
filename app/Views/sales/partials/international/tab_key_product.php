<div class="space-y-6">
    <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="fa-solid fa-award text-amber-500"></i> Report Key Product ($) International
        </h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Rekapitulasi performa per Kategori Key Product International dalam USD ($).</p>
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-800 font-bold border-b border-gray-300 dark:border-gray-700">
                    <tr>
                        <th class="p-3">No.</th>
                        <th class="p-3">KEY PRODUCT</th>
                        <th class="p-3 text-right">Total QTY</th>
                        <th class="p-3 text-right text-amber-700">Total REV ($)</th>
                        <th class="p-3 text-right text-emerald-700">AVG ASP ($)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    <template x-for="(kp, idx) in keyProductsSummary" :key="kp.name">
                        <tr :class="kp.name === 'GRAND TOTAL' ? 'bg-amber-50 dark:bg-amber-950/30 font-extrabold' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'">
                            <td class="p-2.5 text-center" x-text="kp.name === 'GRAND TOTAL' ? '' : (idx + 1)"></td>
                            <td class="p-2.5 font-bold text-gray-900 dark:text-white" x-text="kp.name"></td>
                            <td class="p-2.5 text-right font-mono" x-text="formatNumber(kp.total_qty)"></td>
                            <td class="p-2.5 text-right font-mono text-amber-600" x-text="formatValas(kp.total_revenue)"></td>
                            <td class="p-2.5 text-right font-mono text-emerald-600" x-text="formatValas(calculateASP(kp.total_revenue, kp.total_qty))"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
