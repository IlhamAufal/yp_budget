<div class="mb-6 bg-white dark:bg-boxdark p-4 rounded-xl shadow-xs border border-gray-100 dark:border-gray-800 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div class="w-full max-w-lg">
        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Cost Center</label>
        <select x-model="viewCostCenter" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none">
            <template x-for="cc in costCenters" :key="cc.id">
                <option :value="cc.id" x-text="cc.name"></option>
            </template>
        </select>
    </div>
    <button @click="exportExcel('View by Cost Center')" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 text-xs font-semibold transition shadow-xs">
        <i class="fa-solid fa-file-excel"></i>
        Export to Excel
    </button>
</div>

<div class="rounded-xl border border-gray-200 bg-white shadow-xs dark:border-gray-800 dark:bg-boxdark overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-800">
                    <th class="p-3">DESCRIPTION</th>
                    <th class="p-3">ACCOUNT</th>
                    <th class="p-3">COST CENTER</th>
                    <th class="p-3">UNIT</th>
                    <th class="p-3">UNIT PRICE</th>
                    <th class="p-3">REMARKS</th>
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
                    <th class="p-3 text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="19" class="p-6 text-center text-gray-500 dark:text-gray-400">
                        No data available in table
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>