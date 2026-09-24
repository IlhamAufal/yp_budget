<div class="space-y-6">
    <!-- Header Section (Separated from table container) -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Report Key Product (Domestic Hero SKUs)</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rekapitulasi performa per kategori Key Product (GUMMY, BOLI, MARSHMALLOW, EXTR, dll).</p>
        </div>
        <div class="min-w-[200px]">
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Channel:</label>
            <select x-model="filters.channelKey" @change="calculateKeyProducts()" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2.5 px-3.5 focus:ring-2 focus:ring-[#2F3185]/20 focus:border-[#2F3185]">
                <option value="">- All Channel -</option>
                <?php foreach ($channels ?? [] as $ch): ?>
                    <option value="<?= esc($ch['channel_code']) ?>"><?= esc($ch['channel_code']) ?> - <?= esc($ch['channel_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 overflow-hidden shadow-xs bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[2400px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20 text-center">
                    <tr class="bg-[#2F3185] text-white font-semibold border-b border-white/20">
                        <th class="p-3 border-r border-white/20 w-12 text-white font-semibold text-xs" rowspan="2">No.</th>
                        <th class="p-3 border-r border-white/20 min-w-[180px] text-left text-white font-semibold text-xs" rowspan="2">Key Product</th>
                        <template x-for="(month, idx) in monthNames" :key="'key-'+month">
                            <th class="p-2 border-r border-white/20 text-center text-white font-semibold text-xs bg-[#2F3185]" colspan="3" x-text="month"></th>
                        </template>
                        <th class="p-2 border-l border-white/20 bg-[#2F3185] text-center text-white font-semibold text-xs" colspan="3">Total</th>
                    </tr>
                    <tr class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
                        <?php for ($i = 0; $i < 12; $i++): ?>
                            <th class="p-2 border-r border-white/20 w-20 text-center text-white font-semibold text-xs bg-[#2F3185]">Qty</th>
                            <th class="p-2 border-r border-white/20 w-28 text-center text-white font-semibold text-xs bg-[#2F3185]">Revenue</th>
                            <th class="p-2 border-r border-white/20 w-20 text-center text-white font-semibold text-xs bg-[#2F3185]">ASP/kg</th>
                        <?php endfor; ?>
                        <th class="p-2 border-r border-white/20 w-20 text-center bg-[#2F3185] text-white font-semibold text-xs">Qty</th>
                        <th class="p-2 border-r border-white/20 w-28 text-center bg-[#2F3185] text-white font-semibold text-xs">Revenue</th>
                        <th class="p-2 border-r border-white/20 w-20 text-center bg-[#2F3185] text-white font-semibold text-xs">ASP/kg</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-for="(kp, idx) in keyProductsSummary" :key="kp.name">
                        <tr :class="kp.name === 'GRAND TOTAL' ? 'bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-extrabold text-gray-900 dark:text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'">
                            <td class="p-2.5 text-center border-r border-gray-200 dark:border-gray-800 text-xs" x-text="kp.name === 'GRAND TOTAL' ? '' : (idx + 1)"></td>
                            <td class="p-2.5 border-r border-gray-200 dark:border-gray-800 font-sans text-gray-900 dark:text-white text-xs font-semibold" x-text="kp.name"></td>
                            <template x-for="col in metricCols" :key="'kp-'+col.m+'-'+col.k">
                                <td class="p-2 text-right border-r border-gray-200 dark:border-gray-800 text-xs" :class="col.k === 'asp' ? 'bg-gray-50/50 dark:bg-gray-800/40' : ''" x-text="col.k === 'qty' ? formatNumber(kp.monthly[col.m].qty) : col.k === 'rev' ? formatNumber(kp.monthly[col.m].revenue) : formatNumber(calculateASP(kp.monthly[col.m].revenue, kp.monthly[col.m].qty))"></td>
                            </template>
                            <td class="p-2.5 text-right border-l-2 border-[#2F3185]/30 border-r border-gray-200 dark:border-gray-800 font-bold text-xs" x-text="formatNumber(kp.total_qty)"></td>
                            <td class="p-2.5 text-right border-r border-gray-200 dark:border-gray-800 font-bold text-xs text-emerald-600 dark:text-emerald-400" x-text="formatNumber(kp.total_revenue)"></td>
                            <td class="p-2.5 text-right font-bold text-xs text-amber-600 dark:text-amber-400" x-text="formatNumber(calculateASP(kp.total_revenue, kp.total_qty))"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>