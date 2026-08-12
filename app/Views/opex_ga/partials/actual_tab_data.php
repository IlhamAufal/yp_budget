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
                <select x-model="perPage" @change="onChangePerPage()" class="rounded border border-stroke bg-white px-2 py-1 dark:border-strokedark dark:bg-boxdark dark:text-white">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Search:</label>
            <input type="text" x-model="search" @input="onSearch()" placeholder="Cari account / deskripsi..." class="rounded border border-stroke bg-white px-3 py-1 text-xs outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
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
                <template x-if="loading">
                    <tr>
                        <td colspan="13" class="border-b py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...
                        </td>
                    </tr>
                </template>
                <template x-if="!loading && tableData.length === 0">
                    <tr>
                        <td colspan="13" class="border-b py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox mr-1"></i>No data available in table
                        </td>
                    </tr>
                </template>
                <template x-if="!loading" x-for="(row, idx) in tableData" :key="row.id || idx">
                    <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="border-b border-r px-3 py-2" x-text="((currentPage - 1) * perPage) + idx + 1"></td>
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
                        <td class="border-b border-r px-3 py-2 text-right bg-gray-50 font-medium" x-text="formatNumber(row.total / 12)"></td>
                        <td class="border-b px-3 py-2 text-right bg-gray-100 font-bold" x-text="formatNumber(row.total)"></td>
                    </tr>
                </template>
            </tbody>
            <tfoot x-show="!loading && tableData.length > 0">
                <tr class="bg-gray-100 font-bold text-black dark:bg-meta-4 dark:text-white">
                    <td colspan="3" class="border-r px-3 py-2 text-right uppercase">Total Halaman Ini</td>
                    <td class="border-r px-3 py-2 text-right" x-text="formatNumber(pageTotal('jan'))"></td>
                    <td class="border-r px-3 py-2 text-right" x-text="formatNumber(pageTotal('feb'))"></td>
                    <td class="border-r px-3 py-2 text-right" x-text="formatNumber(pageTotal('mar'))"></td>
                    <td class="border-r px-3 py-2 text-right" x-text="formatNumber(pageTotal('apr'))"></td>
                    <td class="border-r px-3 py-2 text-right" x-text="formatNumber(pageTotal('may'))"></td>
                    <td class="border-r px-3 py-2 text-right" x-text="formatNumber(pageTotal('jun'))"></td>
                    <td class="border-r px-3 py-2 text-right" x-text="formatNumber(pageTotal('jul'))"></td>
                    <td class="border-r px-3 py-2 text-right" x-text="formatNumber(pageTotal('aug'))"></td>
                    <td class="border-r px-3 py-2 text-right" x-text="formatNumber(pageTotalTotal() / 12)"></td>
                    <td class="px-3 py-2 text-right" x-text="formatNumber(pageTotalTotal())"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
        <span>
            Showing <span class="font-bold text-gray-800 dark:text-gray-200" x-text="pageFrom"></span> to
            <span class="font-bold text-gray-800 dark:text-gray-200" x-text="pageTo"></span> of
            <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalData"></span> entries
        </span>
        <div class="flex items-center gap-1">
            <button @click="goPage(currentPage - 1)" :disabled="currentPage <= 1"
                class="rounded border border-stroke dark:border-strokedark px-2.5 py-1 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-meta-4 transition">
                Previous
            </button>
            <template x-for="(p, pi) in pageNumbers()" :key="'pg_' + pi">
                <button x-show="p !== '...'" @click="goPage(p)"
                    :class="currentPage === p ? 'bg-primary text-white border-primary' : 'border-stroke dark:border-strokedark text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-meta-4'"
                    class="min-w-[30px] px-2 py-1 rounded border text-xs font-semibold transition"
                    x-text="p"></button>
                <span x-show="p === '...'" class="px-1 text-gray-400">&hellip;</span>
            </template>
            <button @click="goPage(currentPage + 1)" :disabled="currentPage >= totalPages"
                class="rounded border border-stroke dark:border-strokedark px-2.5 py-1 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-meta-4 transition">
                Next
            </button>
        </div>
    </div>
</div>
