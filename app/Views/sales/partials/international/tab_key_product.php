<div class="space-y-6">
    <!-- Header Section (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Report Key Product ($) International</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rekapitulasi performa per Kategori Key Product International dalam Valas USD ($).</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 overflow-hidden shadow-xs bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th class="px-4 py-3.5 text-center text-white font-semibold w-12 border-r border-white/20">No.</th>
                        <th class="px-4 py-3.5 text-white font-semibold min-w-[200px] border-r border-white/20">Key Product</th>
                        <th class="px-4 py-3.5 text-right text-white font-semibold min-w-[140px] border-r border-white/20">Total Qty</th>
                        <th class="px-4 py-3.5 text-right text-white font-semibold min-w-[160px] border-r border-white/20">Total Rev (USD $)</th>
                        <th class="px-4 py-3.5 text-right text-white font-semibold min-w-[140px]">Avg ASP ($/kg)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-for="(kp, idx) in keyProductsSummary" :key="kp.name">
                        <tr :class="kp.name === 'GRAND TOTAL' ? 'bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-extrabold text-gray-900 dark:text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'">
                            <td class="px-4 py-3 text-center border-r border-gray-200 dark:border-gray-800" x-text="kp.name === 'GRAND TOTAL' ? '' : (idx + 1)"></td>
                            <td class="px-4 py-3 font-sans font-semibold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800" x-text="kp.name"></td>
                            <td class="px-4 py-3 text-right border-r border-gray-200 dark:border-gray-800 font-bold" x-text="formatNumber(kp.total_qty)"></td>
                            <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 border-r border-gray-200 dark:border-gray-800 font-bold" x-text="formatValas(kp.total_revenue)"></td>
                            <td class="px-4 py-3 text-right text-amber-600 dark:text-amber-400 font-bold" x-text="formatValas(calculateASP(kp.total_revenue, kp.total_qty))"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
