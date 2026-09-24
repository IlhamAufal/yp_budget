<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="w-full max-w-2xl">
                <label for="selectedViewCc" class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Cost Center</label>
                <select id="selectedViewCc" x-model="selectedViewCc" @change="fetchViewData()" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs font-medium outline-none transition focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:bg-white dark:text-white">
                    <option value="">-- Pilih Cost Center --</option>
                    <template x-for="item in costCenters" :key="item.id"><option :value="item.id" x-text="item.text"></option></template>
                </select>
            </div>
            <button @click="exportExcel()" :disabled="!selectedViewCc || viewAccounts.length === 0" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 text-xs font-semibold text-white disabled:opacity-50 transition-all shadow-xs shrink-0 active:scale-[0.98]">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel</span>
            </button>
        </div>
    </div>

    <div class="space-y-4">
        <!-- Table Section Label (Separated from table container) -->
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Konsolidasi Actual vs Budget OPEX GA</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbandingan realisasi biaya actual dengan anggaran budget yang direncanakan.</p>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs overflow-hidden dark:border-gray-800 dark:bg-gray-900">
            <div class="max-w-full overflow-x-auto scrollbar-thin" style="max-height: 70vh;">
                <table class="w-full min-w-[1900px] table-auto text-left text-xs text-gray-600 dark:text-gray-300 border-collapse whitespace-nowrap">
                    <thead class="sticky top-0 z-10 bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
                        <tr class="border-b border-white/20 bg-[#2F3185] text-white font-semibold">
                            <th rowspan="2" class="border-r border-white/20 px-3 py-2.5 font-semibold text-white">Account</th>
                            <th rowspan="2" class="border-r border-white/20 px-3 py-2.5 font-semibold text-white">Cost Center Header</th>
                            <th colspan="10" class="border-r border-white/20 px-2 py-2 text-center font-semibold text-white bg-[#25276d]">Actual</th>
                            <th rowspan="2" class="border-r border-white/20 px-3 py-2.5 font-semibold text-center text-white bg-[#25276d]">Assumption</th>
                            <th colspan="13" class="px-2 py-2 text-center font-semibold text-white bg-[#25276d]">Budget</th>
                        </tr>
                        <tr class="border-b border-white/20 bg-[#25276d] text-white text-xs font-semibold">
                            <template x-for="m in actualViewMonths" :key="'ah_' + m"><th class="border-r border-white/20 px-2 py-2 text-right text-white font-semibold" x-text="m"></th></template>
                            <template x-for="m in budgetViewMonths" :key="'bh_' + m"><th class="border-r border-white/20 px-2 py-2 text-right text-white font-semibold" x-text="m"></th></template>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs font-mono">
                        <template x-if="isLoadingView">
                            <tr>
                                <td colspan="26" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                                            <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data...</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sistem sedang memproses data.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="!isLoadingView && selectedViewCc && viewAccounts.length === 0">
                            <tr>
                                <td colspan="26" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                            <i class="fa-solid fa-folder-open text-xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Ditemukan</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada rincian data budget untuk cost center terpilih.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="!isLoadingView && !selectedViewCc">
                            <tr>
                                <td colspan="26" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                            <i class="fa-solid fa-filter text-xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Pilih Cost Center Terlebih Dahulu</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Silakan pilih Cost Center pada dropdown di atas untuk melihat data.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-for="row in viewAccounts" :key="row.id_coa + '_' + row.id_cost_header">
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2 font-mono font-bold text-gray-900 dark:text-white" x-text="row.account"></td>
                                <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2 font-sans font-medium text-gray-800 dark:text-gray-200" x-text="row.cost_center_header"></td>
                                <template x-for="m in actualViewKeys" :key="'ar_' + row.id_coa + '_' + m"><td class="border-r border-gray-200 dark:border-gray-800 px-2 py-2 text-right" x-text="formatNumber(row['actual_' + m])"></td></template>
                                <td class="border-r border-gray-200 dark:border-gray-800 px-2 py-2 text-right" x-text="formatNumber(row.actual_avg)"></td>
                                <td class="border-r border-gray-200 dark:border-gray-800 px-2 py-2 text-right font-bold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(row.actual_total)"></td>
                                <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2 text-right" x-text="row.assumption ?? 0"></td>
                                <template x-for="m in budgetViewKeys" :key="'br_' + row.id_coa + '_' + m"><td class="border-r border-gray-200 dark:border-gray-800 px-2 py-2 text-right" x-text="formatNumber(row['budget_' + m])"></td></template>
                                <td class="px-2 py-2 text-right font-bold text-[#2F3185] dark:text-indigo-400" x-text="formatNumber(row.budget_total)"></td>
                            </tr>
                        </template>
                    </tbody>
                    <template x-for="subtotal in viewSubtotals" :key="'sub_' + subtotal.cost_center_header + '_' + subtotal.id_cost_header"><tbody>
                        <tr class="bg-gray-50/80 dark:bg-gray-800/50 font-bold border-t border-gray-200 dark:border-gray-700 font-mono text-xs">
                            <td colspan="2" class="border-r border-gray-200 dark:border-gray-700 px-3 py-2 font-sans font-bold" x-text="'SUBTOTAL ' + subtotal.cost_center_header"></td>
                            <template x-for="m in actualViewKeys" :key="'sa_' + m"><td class="border-r border-gray-200 dark:border-gray-700 px-2 py-2 text-right" x-text="formatNumber(subtotal.actual.months[m])"></td></template>
                            <td class="border-r border-gray-200 dark:border-gray-700 px-2 py-2 text-right" x-text="formatNumber(subtotal.actual.avg)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-700 px-2 py-2 text-right font-bold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(subtotal.actual.total)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-700 px-3 py-2 text-right">-</td>
                            <template x-for="m in budgetViewKeys" :key="'sb_' + m"><td class="border-r border-gray-200 dark:border-gray-700 px-2 py-2 text-right" x-text="formatNumber(subtotal.budget.months[m])"></td></template>
                            <td class="px-2 py-2 text-right font-bold text-[#2F3185] dark:text-indigo-400" x-text="formatNumber(subtotal.budget.total)"></td>
                        </tr>
                    </tbody></template>
                    <tfoot>
                        <tr x-show="!isLoadingView && viewAccounts.length > 0" class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs font-mono">
                            <td colspan="2" class="border-r border-gray-300 dark:border-gray-700 px-3 py-3 font-sans font-bold">Grand Total</td>
                            <template x-for="m in actualViewKeys" :key="'ga_' + m"><td class="border-r border-gray-300 dark:border-gray-700 px-2 py-3 text-right" x-text="formatNumber(viewGrandTotal.actual.months[m])"></td></template>
                            <td class="border-r border-gray-300 dark:border-gray-700 px-2 py-3 text-right" x-text="formatNumber(viewGrandTotal.actual.avg)"></td>
                            <td class="border-r border-gray-300 dark:border-gray-700 px-2 py-3 text-right font-bold text-emerald-600 dark:text-emerald-400" x-text="formatNumber(viewGrandTotal.actual.total)"></td>
                            <td class="border-r border-gray-300 dark:border-gray-700 px-3 py-3 text-right">-</td>
                            <template x-for="m in budgetViewKeys" :key="'gb_' + m"><td class="border-r border-gray-300 dark:border-gray-700 px-2 py-3 text-right" x-text="formatNumber(viewGrandTotal.budget.months[m])"></td></template>
                            <td class="px-2 py-3 text-right font-bold text-[#2F3185] dark:text-indigo-400" x-text="formatNumber(viewGrandTotal.budget.total)"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
