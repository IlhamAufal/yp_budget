<div class="space-y-6">
    <div class="flex flex-col lg:flex-row items-stretch lg:items-end justify-between gap-5 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Channel:
                </label>
                <select x-model="domesticBudgetFilters.channel" @change="loadDomesticBudget()" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2.5 px-3.5 focus:ring-2 focus:ring-[#2F3185]/20 focus:border-[#2F3185] transition-all">
                    <option value="ALL">- All Channel -</option>
                    <?php foreach ($channels ?? [] as $ch): ?>
                        <option value="<?= esc($ch['channel_code']) ?>"><?= esc($ch['channel_code']) ?> - <?= esc($ch['channel_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Pencarian Produk:
                </label>
                <input type="text" x-model="domesticBudgetFilters.search" @input.debounce.300ms="applyDomesticBudgetFilters()" placeholder="Ketik untuk mencari channel / code inv / key product / nama produk..." class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder:text-gray-400 focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-[#2F3185]/20 focus:border-[#2F3185] transition-all">
            </div>
        </div>
        <div class="shrink-0 flex items-center lg:items-end">
            <button type="button" @click="loadDomesticBudget()" class="px-5 py-2.5 bg-[#2F3185] hover:bg-[#25276d] text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 active:scale-[0.98]" :disabled="domesticBudgetLoading">
                <i class="fa-solid fa-rotate" :class="domesticBudgetLoading ? 'animate-spin' : ''"></i>
                <span x-text="domesticBudgetLoading ? 'Memuat...' : 'Muat Data'">Muat Data</span>
            </button>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Sales International - Budget (12 Bulan)</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rincian target volume (Kg), revenue (USD $), dan ASP/kg ($).</p>
        </div>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin max-h-[680px]">
            <table class="w-full text-left text-xs border-collapse min-w-[3400px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 sticky top-0 z-20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold border-b border-white/20">
                        <th class="px-3 py-3 border-r border-white/20 w-12 text-center sticky left-0 z-30 bg-[#2F3185] text-white font-semibold text-xs" rowspan="2">No.</th>
                        <th class="px-3 py-3 border-r border-white/20 min-w-[100px] text-center sticky left-12 z-30 bg-[#2F3185] text-white font-semibold text-xs" rowspan="2">Channel</th>
                        <th class="px-3 py-3 border-r border-white/20 min-w-[140px] text-white font-semibold text-xs" rowspan="2">Key Product</th>
                        <th class="px-3 py-3 border-r border-white/20 min-w-[120px] text-white font-semibold text-xs" rowspan="2">Code Inv</th>
                        <th class="px-3 py-3 border-r border-white/20 min-w-[260px] text-white font-semibold text-xs" rowspan="2">Name Product</th>
                        <template x-for="(month, idx) in monthNames" :key="'dom-budget-head-'+month">
                            <th class="px-3 py-2 border-r border-white/20 text-center text-white font-semibold text-xs bg-[#2F3185]" colspan="3" x-text="month"></th>
                        </template>
                        <th class="px-3 py-2 border-l border-white/20 bg-[#2F3185] text-center font-bold text-white text-xs" colspan="3">Total</th>
                    </tr>
                    <tr class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
                        <template x-for="col in domesticMetricCols" :key="'dom-budget-subhead-'+col.m+'-'+col.k">
                            <th class="px-2 py-2 border-r border-white/20 text-center text-white font-semibold text-xs bg-[#2F3185]" :class="col.k === 'qty' ? 'w-[88px] min-w-[88px]' : col.k === 'rev' ? 'w-[120px] min-w-[120px]' : 'w-[96px] min-w-[96px]'" x-text="col.k === 'qty' ? 'Qty (Kg)' : col.k === 'rev' ? 'Revenue ($)' : 'ASP ($/kg)'"></th>
                        </template>
                        <th class="px-2 py-2 border-r border-white/20 w-[120px] min-w-[120px] text-center bg-[#2F3185] text-white font-semibold text-xs">Tot Qty</th>
                        <th class="px-2 py-2 border-r border-white/20 w-[120px] min-w-[120px] text-center bg-[#2F3185] text-white font-semibold text-xs">Tot Rev</th>
                        <th class="px-2 py-2 border-r border-white/20 w-[120px] min-w-[120px] text-center bg-[#2F3185] text-white font-semibold text-xs">Avg ASP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-if="domesticBudgetLoading">
                        <tr>
                            <td colspan="44" class="py-12 px-4 text-center text-gray-400 font-sans">
                                <i class="fa-solid fa-spinner animate-spin text-2xl mb-3"></i>
                                <p class="text-xs">Memuat data Sales International...</p>
                            </td>
                        </tr>
                    </template>
                    <template x-if="!domesticBudgetLoading && domesticBudgetRows.length === 0">
                        <tr>
                            <td colspan="44" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-inbox text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data Sales International</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada data yang sesuai dengan filter.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(row, rowIndex) in domesticBudgetRows" :key="'dom-budget-row-'+row.kind+'-'+rowIndex">
                        <tr :class="row.kind === 'subtotal' ? 'bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold text-gray-900 dark:text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'">
                            <template x-if="row.kind === 'subtotal'"><td colspan="5" class="px-3 py-2.5 text-center font-sans font-bold" x-text="'SUBTOTAL CHANNEL ' + row.group.channel + ' (' + row.group.items.length + ' items)'"></td></template>
                            <template x-if="row.kind === 'item'"><td class="px-3 py-2 text-center border-r border-gray-200 dark:border-gray-800 text-gray-500 sticky left-0 z-10 bg-white dark:bg-gray-900" x-text="row.item.row_no"></td></template>
                            <template x-if="row.kind === 'item'"><td class="px-3 py-2 text-center border-r border-gray-200 dark:border-gray-800 font-sans font-bold text-gray-700 dark:text-gray-300 sticky left-12 z-10 bg-white dark:bg-gray-900" x-text="row.item.id_channel"></td></template>
                            <template x-if="row.kind === 'item'"><td class="px-3 py-2 border-r border-gray-200 dark:border-gray-800 font-sans text-gray-600 dark:text-gray-400" x-text="row.item.key_product || '-' "></td></template>
                            <template x-if="row.kind === 'item'"><td class="px-3 py-2 border-r border-gray-200 dark:border-gray-800 font-semibold text-gray-800 dark:text-gray-200" x-text="row.item.mid_product || '-' "></td></template>
                            <template x-if="row.kind === 'item'"><td class="px-3 py-2 border-r border-gray-200 dark:border-gray-800 font-sans font-medium text-gray-900 dark:text-white" x-text="row.item.product_name || '-' "></td></template>
                            <template x-for="col in domesticMetricCols" :key="'dom-budget-metric-'+row.kind+'-'+rowIndex+'-'+col.m+'-'+col.k">
                                <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-xs" :class="row.kind === 'subtotal' ? (col.k === 'asp' ? 'bg-gray-50/50 dark:bg-gray-800/40' : '') : (col.k === 'rev' ? 'text-emerald-600 dark:text-emerald-400' : (col.k === 'asp' ? 'bg-gray-50/50 dark:bg-gray-800/40' : ''))" x-text="row.kind === 'subtotal' ? (col.k === 'qty' ? formatNumber(row.group.monthly[col.m].qty) : col.k === 'rev' ? formatNumber(row.group.monthly[col.m].revenue) : formatDomesticASP(row.group.monthly[col.m].revenue, row.group.monthly[col.m].qty)) : (col.k === 'qty' ? formatNumber(row.item.monthly[col.m].qty) : col.k === 'rev' ? formatNumber(row.item.monthly[col.m].revenue) : formatDomesticASP(row.item.monthly[col.m].revenue, row.item.monthly[col.m].qty))"></td>
                            </template>
                            <template x-if="row.kind === 'subtotal'"><td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800 font-bold" x-text="formatNumber(row.group.total_qty)"></td></template>
                            <template x-if="row.kind === 'subtotal'"><td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800 font-bold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(row.group.total_rev)"></td></template>
                            <template x-if="row.kind === 'subtotal'"><td class="px-3 py-2 text-right font-bold text-amber-600 dark:text-amber-400" x-text="formatDomesticASP(row.group.total_rev, row.group.total_qty)"></td></template>
                            <template x-if="row.kind === 'item'"><td class="px-3 py-2 text-right border-l-2 border-[#2F3185]/30 border-r border-gray-200 dark:border-gray-800 bg-[#2F3185]/5 dark:bg-gray-800 font-bold" x-text="formatNumber(row.item.total_qty)"></td></template>
                            <template x-if="row.kind === 'item'"><td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800 bg-[#2F3185]/5 dark:bg-gray-800 font-bold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(row.item.total_rev)"></td></template>
                            <template x-if="row.kind === 'item'"><td class="px-3 py-2 text-right bg-[#2F3185]/5 dark:bg-gray-800 font-bold text-amber-600 dark:text-amber-400" x-text="formatDomesticASP(row.item.total_rev, row.item.total_qty)"></td></template>
                        </tr>
                    </template>
                    <tr x-show="!domesticBudgetLoading && domesticBudgetRows.length > 0" class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-extrabold text-gray-900 dark:text-white border-t-2 border-[#2F3185]/30">
                        <td class="px-3 py-3 text-center font-sans font-bold" colspan="5">GRAND TOTAL ALL CHANNELS</td>
                        <template x-for="col in domesticMetricCols" :key="'dom-budget-grand-'+col.m+'-'+col.k">
                            <td class="px-2 py-3 text-right border-r border-[#2F3185]/20" :class="col.k === 'asp' ? 'bg-gray-50/50 dark:bg-gray-800/40' : ''" x-text="col.k === 'qty' ? formatNumber(domesticBudgetGrandTotal.monthly[col.m]?.qty || 0) : col.k === 'rev' ? formatNumber(domesticBudgetGrandTotal.monthly[col.m]?.revenue || 0) : formatDomesticASP(domesticBudgetGrandTotal.monthly[col.m]?.revenue || 0, domesticBudgetGrandTotal.monthly[col.m]?.qty || 0)"></td>
                        </template>
                        <td class="px-3 py-3 text-right border-r border-[#2F3185]/30 font-bold" x-text="formatNumber(domesticBudgetGrandTotal.total_qty)"></td>
                        <td class="px-3 py-3 text-right border-r border-[#2F3185]/30 font-bold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(domesticBudgetGrandTotal.total_rev)"></td>
                        <td class="px-3 py-3 text-right font-bold text-amber-600 dark:text-amber-400" x-text="formatDomesticASP(domesticBudgetGrandTotal.total_rev, domesticBudgetGrandTotal.total_qty)"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>