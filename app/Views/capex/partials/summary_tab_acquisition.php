<div class="mb-6 bg-white dark:bg-boxdark p-4 rounded-xl shadow-xs border border-gray-100 dark:border-gray-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="w-full max-w-xs flex items-center gap-3">
        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 flex-shrink-0">Show :</label>
        <select x-model="filterShowAcq" @change="fetchData()" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none">
            <template x-for="opt in showOptions" :key="opt.id">
                <option :value="opt.id" x-text="opt.name"></option>
            </template>
        </select>
    </div>
    
    <button @click="exportExcel('Summary Acquisition')" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 text-white px-4 py-2 text-xs font-medium hover:bg-emerald-700 transition shadow-xs">
        <i class="fas fa-file-excel"></i> Export to Excel
    </button>
</div>

<div class="rounded-xl border border-gray-200 bg-white shadow-xs dark:border-gray-800 dark:bg-boxdark overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-800">
                    <th class="p-3">JENIS ASSET (ACQUISITION)</th>
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
                <template x-if="acqData.length === 0">
                    <tr>
                        <td colspan="14" class="p-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox mr-1"></i>No data available in table
                        </td>
                    </tr>
                </template>
                <template x-for="(row, idx) in acqData" :key="idx">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="p-3 font-medium text-gray-800 dark:text-gray-200" x-text="`${row.category_code} - ${row.category_name}`"></td>
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
                    <td class="p-3">TOTAL ACQUISITION</td>
                    <td class="p-3 text-right" x-text="fmt(acqTotals.jan)"></td>
                    <td class="p-3 text-right" x-text="fmt(acqTotals.feb)"></td>
                    <td class="p-3 text-right" x-text="fmt(acqTotals.mar)"></td>
                    <td class="p-3 text-right" x-text="fmt(acqTotals.apr)"></td>
                    <td class="p-3 text-right" x-text="fmt(acqTotals.may)"></td>
                    <td class="p-3 text-right" x-text="fmt(acqTotals.jun)"></td>
                    <td class="p-3 text-right" x-text="fmt(acqTotals.jul)"></td>
                    <td class="p-3 text-right" x-text="fmt(acqTotals.aug)"></td>
                    <td class="p-3 text-right" x-text="fmt(acqTotals.sep)"></td>
                    <td class="p-3 text-right" x-text="fmt(acqTotals.oct)"></td>
                    <td class="p-3 text-right" x-text="fmt(acqTotals.nov)"></td>
                    <td class="p-3 text-right" x-text="fmt(acqTotals.dec)"></td>
                    <td class="p-3 text-right text-primary" x-text="fmt(acqTotals.total)"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
