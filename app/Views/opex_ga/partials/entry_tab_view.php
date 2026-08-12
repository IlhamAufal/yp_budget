<div class="space-y-6">
    <div class="rounded-sm border border-stroke bg-gray-50 p-4 dark:border-strokedark dark:bg-meta-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-2xl">
                <label for="selectedViewCc" class="mb-2 block text-sm font-semibold text-black dark:text-white">Cost Center</label>
                <select id="selectedViewCc" x-model="selectedViewCc" @change="fetchViewData()" class="w-full rounded border border-stroke bg-white px-4 py-2.5 text-sm font-medium dark:border-strokedark dark:bg-boxdark dark:text-white">
                    <option value="">-- Pilih Cost Center --</option>
                    <template x-for="item in costCenters" :key="item.id"><option :value="item.id" x-text="item.text"></option></template>
                </select>
            </div>
            <button @click="exportExcel()" :disabled="!selectedViewCc || viewAccounts.length === 0" class="inline-flex items-center justify-center gap-2 rounded bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50"><i class="fas fa-file-excel"></i> Export Excel</button>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto border border-stroke dark:border-strokedark" style="max-height: 70vh;">
        <table class="w-full min-w-[1900px] table-auto text-left text-xs">
            <thead class="sticky top-0 z-10">
                <tr class="bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300">
                    <th rowspan="2" class="border-b border-r px-3 py-2.5 font-bold uppercase">ACCOUNT</th>
                    <th rowspan="2" class="border-b border-r px-3 py-2.5 font-bold uppercase">COST CENTER HEADER</th>
                    <th colspan="10" class="border-b border-r px-2 py-2 text-center font-bold uppercase">ACTUAL</th>
                    <th rowspan="2" class="border-b border-r px-3 py-2.5 font-bold uppercase">ASSUMPTION</th>
                    <th colspan="13" class="border-b px-2 py-2 text-center font-bold uppercase">BUDGET</th>
                </tr>
                <tr class="bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300">
                    <template x-for="m in actualViewMonths" :key="'ah_' + m"><th class="border-b border-r px-2 py-2 text-right font-bold" x-text="m"></th></template>
                    <template x-for="m in budgetViewMonths" :key="'bh_' + m"><th class="border-b border-r px-2 py-2 text-right font-bold" x-text="m"></th></template>
                </tr>
            </thead>
            <tbody>
                <template x-if="isLoadingView"><tr><td colspan="26" class="py-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat data...</td></tr></template>
                <template x-if="!isLoadingView && selectedViewCc && viewAccounts.length === 0"><tr><td colspan="26" class="py-8 text-center text-gray-500"><i class="fas fa-inbox mr-1"></i>No data available in table</td></tr></template>
                <template x-if="!isLoadingView && !selectedViewCc"><tr><td colspan="26" class="py-8 text-center text-gray-500">Silakan pilih Cost Center terlebih dahulu</td></tr></template>
                <template x-for="row in viewAccounts" :key="row.id_coa + '_' + row.id_cost_header">
                    <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="border-b border-r px-3 py-2 font-medium" x-text="row.account"></td>
                        <td class="border-b border-r px-3 py-2" x-text="row.cost_center_header"></td>
                        <template x-for="m in actualViewKeys" :key="'ar_' + row.id_coa + '_' + m"><td class="border-b border-r px-2 py-2 text-right font-mono" x-text="formatNumber(row['actual_' + m])"></td></template>
                        <td class="border-b border-r px-2 py-2 text-right font-mono" x-text="formatNumber(row.actual_avg)"></td>
                        <td class="border-b border-r px-2 py-2 text-right font-mono" x-text="formatNumber(row.actual_total)"></td>
                        <td class="border-b border-r px-3 py-2 text-right" x-text="row.assumption ?? 0"></td>
                        <template x-for="m in budgetViewKeys" :key="'br_' + row.id_coa + '_' + m"><td class="border-b border-r px-2 py-2 text-right font-mono" x-text="formatNumber(row['budget_' + m])"></td></template>
                        <td class="border-b px-2 py-2 text-right font-mono font-bold" x-text="formatNumber(row.budget_total)"></td>
                    </tr>
                </template>
            </tbody>
            <template x-for="subtotal in viewSubtotals" :key="'sub_' + subtotal.cost_center_header + '_' + subtotal.id_cost_header"><tbody>
                <tr class="bg-gray-100 font-bold dark:bg-meta-4">
                    <td colspan="2" class="border-t border-r px-3 py-2 uppercase" x-text="'SUBTOTAL ' + subtotal.cost_center_header"></td>
                    <template x-for="m in actualViewKeys" :key="'sa_' + m"><td class="border-t border-r px-2 py-2 text-right" x-text="formatNumber(subtotal.actual.months[m])"></td></template>
                    <td class="border-t border-r px-2 py-2 text-right" x-text="formatNumber(subtotal.actual.avg)"></td><td class="border-t border-r px-2 py-2 text-right" x-text="formatNumber(subtotal.actual.total)"></td><td class="border-t border-r px-3 py-2 text-right">-</td>
                    <template x-for="m in budgetViewKeys" :key="'sb_' + m"><td class="border-t border-r px-2 py-2 text-right" x-text="formatNumber(subtotal.budget.months[m])"></td></template>
                    <td class="border-t px-2 py-2 text-right" x-text="formatNumber(subtotal.budget.total)"></td>
                </tr>
            </tbody></template>
            <tfoot>
                <tr x-show="!isLoadingView && viewAccounts.length > 0" class="bg-gray-200 font-bold dark:bg-meta-4">
                    <td colspan="2" class="border-t border-r px-3 py-3 uppercase">GRAND TOTAL</td>
                    <template x-for="m in actualViewKeys" :key="'ga_' + m"><td class="border-t border-r px-2 py-3 text-right" x-text="formatNumber(viewGrandTotal.actual.months[m])"></td></template>
                    <td class="border-t border-r px-2 py-3 text-right" x-text="formatNumber(viewGrandTotal.actual.avg)"></td><td class="border-t border-r px-2 py-3 text-right" x-text="formatNumber(viewGrandTotal.actual.total)"></td><td class="border-t border-r px-3 py-3 text-right">-</td>
                    <template x-for="m in budgetViewKeys" :key="'gb_' + m"><td class="border-t border-r px-2 py-3 text-right" x-text="formatNumber(viewGrandTotal.budget.months[m])"></td></template>
                    <td class="border-t px-2 py-3 text-right" x-text="formatNumber(viewGrandTotal.budget.total)"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
