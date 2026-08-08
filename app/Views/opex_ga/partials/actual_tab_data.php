<div class="space-y-4">
    <div class="rounded-sm border border-stroke bg-gray-50 p-4 dark:border-strokedark dark:bg-meta-4">
        <label class="mb-2 block text-sm font-semibold text-black dark:text-white">Cost Center</label>
        <div class="relative w-full max-w-2xl">
            <select x-model="selectedActualCc" @change="fetchActualData()"
                class="w-full rounded border border-stroke bg-white px-4 py-2.5 text-sm font-medium outline-none transition focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                <option value="">-- Pilih Cost Center --</option>
                <template x-for="item in costCenters" :key="item.id">
                    <option :value="item.id" x-text="item.text"></option>
                </template>
            </select>
        </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <button @click="exportExcel()" class="inline-flex items-center gap-1.5 rounded border border-stroke bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200 dark:border-strokedark dark:bg-meta-4 dark:text-gray-300">
                <i class="fas fa-file-export"></i> Export Data
            </button>
            <div class="flex items-center gap-1 text-xs text-gray-500">
                <span>Show</span>
                <select class="rounded border border-stroke bg-white px-2 py-1 dark:border-strokedark dark:bg-boxdark">
                    <option>25</option>
                    <option>50</option>
                    <option>100</option>
                </select>
                <span>entries</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Search:</label>
            <input type="text" class="rounded border border-stroke bg-white px-3 py-1 text-xs outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
        </div>
    </div>

    <div class="max-w-full overflow-x-auto border border-stroke dark:border-strokedark">
        <table class="w-full table-auto text-left text-xs">
            <thead>
                <tr class="bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300">
                    <th class="border-b border-r px-3 py-2.5 font-bold uppercase">NO.</th>
                    <th class="border-b border-r px-3 py-2.5 font-bold uppercase">MAIN ACCOUNT</th>
                    <th class="border-b border-r px-3 py-2.5 font-bold uppercase">DESCRIPTION</th>
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">JAN</th>
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">FEB</th>
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">MAR</th>
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">APR</th>
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">MAY</th>
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">JUN</th>
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">JUL</th>
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">AUG</th>
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">AVG</th>
                    <th class="border-b px-3 py-2.5 text-center font-bold uppercase">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="tableData.length === 0">
                    <tr>
                        <td colspan="13" class="border-b py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox mr-1"></i>No data available in table
                        </td>
                    </tr>
                </template>
                <template x-for="(row, idx) in tableData" :key="idx">
                    <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="border-b border-r px-3 py-2" x-text="idx + 1"></td>
                        <td class="border-b border-r px-3 py-2 font-medium" x-text="row.main_account"></td>
                        <td class="border-b border-r px-3 py-2" x-text="row.description"></td>
                        <td class="border-b border-r px-3 py-2 text-right" x-text="row.jan"></td>
                        <td class="border-b border-r px-3 py-2 text-right" x-text="row.feb"></td>
                        <td class="border-b border-r px-3 py-2 text-right" x-text="row.mar"></td>
                        <td class="border-b border-r px-3 py-2 text-right" x-text="row.apr"></td>
                        <td class="border-b border-r px-3 py-2 text-right" x-text="row.may"></td>
                        <td class="border-b border-r px-3 py-2 text-right" x-text="row.jun"></td>
                        <td class="border-b border-r px-3 py-2 text-right" x-text="row.jul"></td>
                        <td class="border-b border-r px-3 py-2 text-right" x-text="row.aug"></td>
                        <td class="border-b border-r px-3 py-2 text-right bg-gray-50 font-medium" x-text="row.avg"></td>
                        <td class="border-b px-3 py-2 text-right bg-gray-100 font-bold" x-text="row.total"></td>
                    </tr>
                </template>
            </tbody>
            <tfoot>
                <tr class="bg-gray-100 font-bold text-black dark:bg-meta-4 dark:text-white">
                    <td colspan="3" class="border-r px-3 py-2 text-right uppercase">GRAND TOTAL</td>
                    <td class="border-r px-3 py-2 text-right">0.00</td>
                    <td class="border-r px-3 py-2 text-right">0.00</td>
                    <td class="border-r px-3 py-2 text-right">0.00</td>
                    <td class="border-r px-3 py-2 text-right">0.00</td>
                    <td class="border-r px-3 py-2 text-right">0.00</td>
                    <td class="border-r px-3 py-2 text-right">0.00</td>
                    <td class="border-r px-3 py-2 text-right">0.00</td>
                    <td class="border-r px-3 py-2 text-right">0.00</td>
                    <td class="border-r px-3 py-2 text-right">0.00</td>
                    <td class="px-3 py-2 text-right">0.00</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="flex items-center justify-between text-xs text-gray-500">
        <span x-text="`Showing 0 to ${tableData.length} of ${totalData} entries`"></span>
        <div class="flex items-center gap-1">
            <button class="rounded border px-2.5 py-1 disabled:opacity-50" disabled>Previous</button>
            <button class="rounded border px-2.5 py-1 disabled:opacity-50" disabled>Next</button>
        </div>
    </div>
</div>
