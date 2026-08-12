<div class="rounded-sm border border-stroke bg-gray-50 p-4 dark:border-strokedark dark:bg-meta-4">
    <label for="actualCc" class="mb-2 block text-sm font-semibold text-black dark:text-white">Cost Center</label>
    <select id="actualCc" x-model="selectedActualCc" @change="fetchActualData()"
        class="w-full max-w-2xl rounded border border-stroke bg-white px-4 py-2.5 text-sm font-medium outline-none transition focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
        <option value="">-- Pilih Cost Center --</option>
        <template x-for="item in costCenters" :key="item.id">
            <option :value="item.id" x-text="item.name"></option>
        </template>
    </select>
    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Pilih 600 untuk Domestic atau 700 untuk Export. Data dimuat setelah pilihan dibuat.</p>
</div>

<div x-show="uploadFeedback" x-cloak class="mt-4 rounded border border-success bg-success/10 px-4 py-3 text-sm text-success" x-text="uploadFeedback"></div>
<div x-show="errorMessage" x-cloak class="mt-4 rounded border border-danger bg-danger/10 px-4 py-3 text-sm text-danger" x-text="errorMessage"></div>

<div class="mb-4 mt-4 flex flex-wrap items-center justify-between gap-3">
    <a :href="selectedActualCc ? '<?= base_url('opex-selling/exportActual') ?>?cost_center=' + encodeURIComponent(selectedActualCc) : '#'"
        :class="!selectedActualCc ? 'pointer-events-none opacity-50' : ''"
        class="inline-flex items-center gap-2 rounded border border-stroke bg-gray-100 px-4 py-2 text-sm font-medium text-black hover:bg-gray-200 dark:border-strokedark dark:bg-meta-4 dark:text-white">
        <i class="fas fa-file-export"></i> Export Data Actual
    </a>

    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        <span>Show</span>
        <select x-model="perPage" @change="onChangePerPage()" class="rounded border border-stroke px-2 py-1 text-sm dark:border-strokedark dark:bg-boxdark dark:text-white">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <span>entries</span>
    </div>

    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        <label for="actualSearch">Search:</label>
        <input id="actualSearch" type="text" x-model="search" @input="onSearch()" placeholder="Cari account / group / deskripsi..."
            class="rounded border border-stroke px-2 py-1 text-sm outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
    </div>
</div>

<div class="max-w-full overflow-x-auto border border-stroke dark:border-strokedark">
    <table class="w-full table-auto text-left text-xs">
        <thead>
            <tr class="border-b border-stroke bg-gray-2 font-bold uppercase text-black dark:border-strokedark dark:bg-meta-4 dark:text-white">
                <th class="min-w-[50px] border-r border-stroke px-3 py-3 dark:border-strokedark">NO.</th>
                <th class="min-w-[150px] border-r border-stroke px-3 py-3 dark:border-strokedark">MAIN ACCOUNT</th>
                <th class="min-w-[200px] border-r border-stroke px-3 py-3 dark:border-strokedark">DESCRIPTION</th>
                <th class="min-w-[90px] border-r border-stroke px-3 py-3 text-right dark:border-strokedark">JAN</th>
                <th class="min-w-[90px] border-r border-stroke px-3 py-3 text-right dark:border-strokedark">FEB</th>
                <th class="min-w-[90px] border-r border-stroke px-3 py-3 text-right dark:border-strokedark">MAR</th>
                <th class="min-w-[90px] border-r border-stroke px-3 py-3 text-right dark:border-strokedark">APR</th>
                <th class="min-w-[90px] border-r border-stroke px-3 py-3 text-right dark:border-strokedark">MAY</th>
                <th class="min-w-[90px] border-r border-stroke px-3 py-3 text-right dark:border-strokedark">JUN</th>
                <th class="min-w-[90px] border-r border-stroke px-3 py-3 text-right dark:border-strokedark">JUL</th>
                <th class="min-w-[90px] border-r border-stroke px-3 py-3 text-right dark:border-strokedark">AUG</th>
                <th class="min-w-[90px] border-r border-stroke px-3 py-3 text-right dark:border-strokedark">AVG</th>
                <th class="min-w-[100px] px-3 py-3 text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <template x-if="loading">
                <tr><td colspan="13" class="py-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...</td></tr>
            </template>
            <template x-if="!loading && !selectedActualCc">
                <tr><td colspan="13" class="py-8 text-center text-gray-500">Silakan pilih Cost Center.</td></tr>
            </template>
            <template x-if="!loading && selectedActualCc && totalData === 0">
                <tr><td colspan="13" class="py-8 text-center text-gray-500"><i class="fas fa-inbox mr-1"></i> No data available in table</td></tr>
            </template>

            <template x-for="entry in displayRows()" :key="entry.key">
                <tr class="border-b border-stroke dark:border-strokedark" :class="entry.type === 'heading' ? 'bg-primary/10 font-bold text-primary' : (entry.type === 'subtotal' ? 'bg-gray-1 font-semibold dark:bg-meta-4' : 'hover:bg-gray-50 dark:hover:bg-meta-4')">
                    <td x-show="entry.type === 'heading'" colspan="13" class="px-3 py-2 uppercase" x-text="entry.name"></td>
                    <td x-show="entry.type === 'item'" class="border-r border-stroke px-3 py-2.5 dark:border-strokedark" x-text="entry.number"></td>
                    <td x-show="entry.type === 'item'" class="border-r border-stroke px-3 py-2.5 font-medium dark:border-strokedark" x-text="entry.row.main_account"></td>
                    <td x-show="entry.type === 'item'" class="border-r border-stroke px-3 py-2.5 dark:border-strokedark" x-text="entry.row.cost_center_desc"></td>
                    <template x-for="month in ['jan','feb','mar','apr','may','jun','jul','aug']" :key="entry.key + '_detail_' + month">
                        <td x-show="entry.type === 'item'" class="border-r border-stroke px-3 py-2.5 text-right dark:border-strokedark" x-text="formatNumber(entry.row[month])"></td>
                    </template>
                    <td x-show="entry.type === 'item'" class="border-r border-stroke px-3 py-2.5 text-right font-semibold dark:border-strokedark" x-text="formatNumber(entry.row.avg)"></td>
                    <td x-show="entry.type === 'item'" class="px-3 py-2.5 text-right font-bold" x-text="formatNumber(entry.row.total)"></td>
                    <td x-show="entry.type === 'subtotal'" colspan="3" class="border-r border-stroke px-3 py-2.5 uppercase dark:border-strokedark" x-text="'Subtotal ' + entry.name"></td>
                    <template x-for="month in ['jan','feb','mar','apr','may','jun','jul','aug']" :key="entry.key + '_subtotal_' + month">
                        <td x-show="entry.type === 'subtotal'" class="border-r border-stroke px-3 py-2.5 text-right dark:border-strokedark" x-text="formatNumber(groupTotal(entry.name, month))"></td>
                    </template>
                    <td x-show="entry.type === 'subtotal'" class="border-r border-stroke px-3 py-2.5 text-right dark:border-strokedark" x-text="formatNumber(groupTotal(entry.name, 'total') / 8)"></td>
                    <td x-show="entry.type === 'subtotal'" class="px-3 py-2.5 text-right" x-text="formatNumber(groupTotal(entry.name, 'total'))"></td>
                </tr>
            </template>
        </tbody>
        <tfoot x-show="!loading && selectedActualCc && totalData > 0">
            <tr class="border-t border-stroke bg-gray-1 font-bold dark:border-strokedark dark:bg-meta-4">
                <td colspan="3" class="border-r border-stroke px-3 py-3 text-right uppercase dark:border-strokedark">Grand Total</td>
                <template x-for="month in ['jan','feb','mar','apr','may','jun','jul','aug']" :key="'grand_' + month">
                    <td class="border-r border-stroke px-3 py-3 text-right dark:border-strokedark" x-text="formatNumber(filteredTotal(month))"></td>
                </template>
                <td class="border-r border-stroke px-3 py-3 text-right dark:border-strokedark" x-text="formatNumber(filteredTotal('total') / 8)"></td>
                <td class="px-3 py-3 text-right text-primary" x-text="formatNumber(filteredTotal('total'))"></td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-gray-600 dark:text-gray-400">
    <div>Showing <span class="font-bold text-gray-800 dark:text-gray-200" x-text="pageFrom"></span> to <span class="font-bold text-gray-800 dark:text-gray-200" x-text="pageTo"></span> of <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalData"></span> entries</div>
    <div class="flex items-center gap-1">
        <button @click="goPage(currentPage - 1)" :disabled="currentPage <= 1" class="rounded border border-stroke px-3 py-1.5 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-strokedark">Previous</button>
        <template x-for="(page, pageIndex) in pageNumbers()" :key="'page_' + pageIndex">
            <span>
                <button x-show="page !== '...'" @click="goPage(page)" :class="currentPage === page ? 'border-primary bg-primary text-white' : 'border-stroke text-gray-600 dark:border-strokedark dark:text-gray-300'" class="min-w-[32px] rounded border px-2.5 py-1.5 text-sm font-semibold" x-text="page"></button>
                <span x-show="page === '...'" class="px-1 text-gray-400">&hellip;</span>
            </span>
        </template>
        <button @click="goPage(currentPage + 1)" :disabled="currentPage >= totalPages" class="rounded border border-stroke px-3 py-1.5 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-strokedark">Next</button>
    </div>
</div>