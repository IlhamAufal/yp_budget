<div class="mb-6 bg-white dark:bg-boxdark p-4 rounded-xl shadow-xs border border-gray-100 dark:border-gray-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="w-full max-w-xs flex items-center gap-3">
        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 flex-shrink-0">Show :</label>
        <select x-model="filterShowAll" @change="fetchData()" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none">
            <template x-for="opt in showOptions" :key="opt.id">
                <option :value="opt.id" x-text="opt.name"></option>
            </template>
        </select>
    </div>
    
    <button @click="exportExcel('View Capex All')" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 text-white px-4 py-2 text-xs font-medium hover:bg-emerald-700 transition shadow-xs">
        <i class="fas fa-file-excel"></i> Export to Excel
    </button>
</div>

<div class="rounded-xl border border-gray-200 bg-white shadow-xs dark:border-gray-800 dark:bg-boxdark overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-800">
                    <th class="p-3 whitespace-nowrap">DESCRIPTION</th>
                    <th class="p-3 whitespace-nowrap">ACCOUNT</th>
                    <th class="p-3 whitespace-nowrap">COST CENTER</th>
                    <th class="p-3 text-right whitespace-nowrap">QTY</th>
                    <th class="p-3 text-right whitespace-nowrap">UNIT PRICE (MIO)</th>
                    <th class="p-3 whitespace-nowrap">REMARKS</th>
                    <th class="p-3 text-right">JAN</th>
                    <th class="p-3 text-right">FEB</th>
                    <th class="p-3 text-right">MAR</th>
                    <th class="p-3 text-right">APR</th>
                    <th class="p-3 text-right">MAY</th>
                    <th class="p-3 text-right">JUN</th>
                    <th class="p-3 text-right">JUL</th>
                    <th class="p-3 text-right">AUG</th>
                    <th class="p-3 text-right">SEP</th>
                    <th class="p-3 text-right">OCT</th>
                    <th class="p-3 text-right">NOV</th>
                    <th class="p-3 text-right">DEC</th>
                    <th class="p-3 text-right font-bold">TOTAL</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                <template x-if="viewAllData.length === 0">
                    <tr>
                        <td colspan="19" class="p-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox mr-1"></i>No data available in table
                        </td>
                    </tr>
                </template>
                <template x-for="(row, idx) in viewAllData" :key="idx">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="p-3 font-medium text-gray-800 dark:text-gray-200" x-text="row.description"></td>
                        <td class="p-3" x-text="row.account"></td>
                        <td class="p-3" x-text="row.cost_center"></td>
                        <td class="p-3 text-right" x-text="row.qty"></td>
                        <td class="p-3 text-right" x-text="fmt(row.unit_price)"></td>
                        <td class="p-3" x-text="row.remarks"></td>
                        <td class="p-3 text-right" x-text="fmt(row.jan)"></td>
                        <td class="p-3 text-right" x-text="fmt(row.feb)"></td>
                        <td class="p-3 text-right" x-text="fmt(row.mar)"></td>
                        <td class="p-3 text-right" x-text="fmt(row.apr)"></td>
                        <td class="p-3 text-right" x-text="fmt(row.may)"></td>
                        <td class="p-3 text-right" x-text="fmt(row.jun)"></td>
                        <td class="p-3 text-right" x-text="fmt(row.jul)"></td>
                        <td class="p-3 text-right" x-text="fmt(row.aug)"></td>
                        <td class="p-3 text-right" x-text="fmt(row.sep)"></td>
                        <td class="p-3 text-right" x-text="fmt(row.oct)"></td>
                        <td class="p-3 text-right" x-text="fmt(row.nov)"></td>
                        <td class="p-3 text-right" x-text="fmt(row.dec)"></td>
                        <td class="p-3 text-right font-bold" x-text="fmt(row.total)"></td>
                    </tr>
                </template>
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 dark:bg-gray-900/50 font-bold border-t-2 border-gray-300 dark:border-gray-700">
                    <td colspan="6" class="p-3">TOTAL</td>
                    <td class="p-3 text-right" x-text="fmt(viewAllTotals.jan)"></td>
                    <td class="p-3 text-right" x-text="fmt(viewAllTotals.feb)"></td>
                    <td class="p-3 text-right" x-text="fmt(viewAllTotals.mar)"></td>
                    <td class="p-3 text-right" x-text="fmt(viewAllTotals.apr)"></td>
                    <td class="p-3 text-right" x-text="fmt(viewAllTotals.may)"></td>
                    <td class="p-3 text-right" x-text="fmt(viewAllTotals.jun)"></td>
                    <td class="p-3 text-right" x-text="fmt(viewAllTotals.jul)"></td>
                    <td class="p-3 text-right" x-text="fmt(viewAllTotals.aug)"></td>
                    <td class="p-3 text-right" x-text="fmt(viewAllTotals.sep)"></td>
                    <td class="p-3 text-right" x-text="fmt(viewAllTotals.oct)"></td>
                    <td class="p-3 text-right" x-text="fmt(viewAllTotals.nov)"></td>
                    <td class="p-3 text-right" x-text="fmt(viewAllTotals.dec)"></td>
                    <td class="p-3 text-right text-primary" x-text="fmt(viewAllTotals.total)"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
