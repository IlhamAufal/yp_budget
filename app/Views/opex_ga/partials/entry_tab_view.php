<div class="space-y-6">

    <div class="rounded-sm border border-stroke bg-gray-50 p-4 dark:border-strokedark dark:bg-meta-4">
        <label for="selectedViewCc" class="mb-2 block text-sm font-semibold text-black dark:text-white">Cost Center</label>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-2xl">
                <select id="selectedViewCc" x-model="selectedViewCc" @change="fetchViewData()"
                    class="w-full rounded border border-stroke bg-white px-4 py-2.5 text-sm font-medium outline-none transition focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                    <option value="">-- Pilih Cost Center --</option>
                    <template x-for="item in costCenters" :key="item.id">
                        <option :value="item.id" x-text="item.text"></option>
                    </template>
                </select>
            </div>
            <button @click="exportExcel()" :disabled="!selectedViewCc || viewAccounts.length === 0"
                class="inline-flex items-center justify-center gap-2 rounded bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition shrink-0 shadow disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-emerald-600">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto border border-stroke dark:border-strokedark" style="max-height: 70vh;">
        <table class="w-full table-auto text-left text-xs">
            <thead class="sticky top-0 z-10">
                <tr class="bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300">
                    <th class="border-b border-r px-3 py-2.5 font-bold uppercase text-center">NO.</th>
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
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">SEP</th>
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">OCT</th>
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">NOV</th>
                    <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">DEC</th>
                    <th class="border-b px-3 py-2.5 text-center font-bold uppercase">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="isLoadingView">
                    <tr>
                        <td colspan="16" class="border-b py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...
                        </td>
                    </tr>
                </template>
                <template x-if="!isLoadingView && !selectedViewCc">
                    <tr>
                        <td colspan="16" class="border-b py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-hand-point-up mr-1"></i> Silakan pilih Cost Center terlebih dahulu
                        </td>
                    </tr>
                </template>
                <template x-if="!isLoadingView && selectedViewCc && viewAccounts.length === 0">
                    <tr>
                        <td colspan="16" class="border-b py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox mr-1"></i> No data available in table
                        </td>
                    </tr>
                </template>
                <template x-if="!isLoadingView">
                    <template x-for="(row, idx) in viewAccounts" :key="idx">
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="border-b border-r px-3 py-2 text-center" x-text="idx + 1"></td>
                            <td class="border-b border-r px-3 py-2 font-medium" x-text="row.main_account"></td>
                            <td class="border-b border-r px-3 py-2" x-text="row.description"></td>
                            <td class="border-b border-r px-3 py-2 text-right" x-text="formatNumber(row.jan)"></td>
                            <td class="border-b border-r px-3 py-2 text-right" x-text="formatNumber(row.feb)"></td>
                            <td class="border-b border-r px-3 py-2 text-right" x-text="formatNumber(row.mar)"></td>
                            <td class="border-b border-r px-3 py-2 text-right" x-text="formatNumber(row.apr)"></td>
                            <td class="border-b border-r px-3 py-2 text-right" x-text="formatNumber(row.may)"></td>
                            <td class="border-b border-r px-3 py-2 text-right" x-text="formatNumber(row.jun)"></td>
                            <td class="border-b border-r px-3 py-2 text-right" x-text="formatNumber(row.jul)"></td>
                            <td class="border-b border-r px-3 py-2 text-right" x-text="formatNumber(row.aug)"></td>
                            <td class="border-b border-r px-3 py-2 text-right" x-text="formatNumber(row.sep)"></td>
                            <td class="border-b border-r px-3 py-2 text-right" x-text="formatNumber(row.oct)"></td>
                            <td class="border-b border-r px-3 py-2 text-right" x-text="formatNumber(row.nov)"></td>
                            <td class="border-b border-r px-3 py-2 text-right" x-text="formatNumber(row.dec)"></td>
                            <td class="border-b px-3 py-2 text-right bg-gray-100 dark:bg-meta-4 font-bold" x-text="formatNumber(row.total)"></td>
                        </tr>
                    </template>
                </template>
            </tbody>
        </table>
    </div>
</div>