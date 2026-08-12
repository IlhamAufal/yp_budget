<div class="space-y-6">
    <div class="rounded-sm border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 text-red-600 dark:text-red-400"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="text-xs text-red-700 dark:text-red-300/80">
                <h4 class="text-sm font-bold text-red-800 dark:text-red-300">Peringatan / Warning Information:</h4>
                <p class="mt-1">Pastikan Cost Center yang dipilih sesuai otorisasi Anda. Gunakan tombol Entry untuk membuka seluruh akun dalam kelompok header.</p>
            </div>
        </div>
    </div>

    <div class="rounded-sm border border-stroke bg-gray-50 p-4 dark:border-strokedark dark:bg-meta-4">
        <label class="mb-2 block text-sm font-semibold text-black dark:text-white">Cost Center</label>
        <select x-model="selectedEntryCc" @change="fetchEntryData()"
            class="w-full max-w-2xl rounded border border-stroke bg-white px-4 py-2.5 text-sm font-medium outline-none transition focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
            <option value="">-- Pilih Cost Center --</option>
            <template x-for="item in costCenters" :key="item.id"><option :value="item.id" x-text="item.text"></option></template>
        </select>
    </div>

    <div class="max-w-full overflow-x-auto border border-stroke dark:border-strokedark">
        <table class="w-full table-auto text-left text-xs">
            <thead>
                <tr class="bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300">
                    <th rowspan="2" class="border-b border-r px-4 py-2.5 text-center font-bold uppercase">ENTRY</th>
                    <th rowspan="2" class="border-b border-r px-4 py-2.5 font-bold uppercase min-w-[240px]">GENERAL ADMINISTRATIVE EXPENSE</th>
                    <th colspan="8" class="border-b border-r px-2 py-2.5 text-center font-bold uppercase">ACTUAL</th>
                    <th rowspan="2" class="border-b px-4 py-2.5 text-right font-bold uppercase">TOTAL</th>
                </tr>
                <tr class="bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300">
                    <template x-for="m in actualMonths" :key="m"><th class="border-b border-r px-2 py-2 text-center font-bold uppercase" x-text="m"></th></template>
                </tr>
            </thead>
            <tbody>
                <template x-if="entryAccounts.length === 0"><tr><td colspan="11" class="border-b py-6 text-center text-gray-500 dark:text-gray-400"><i class="fas fa-inbox mr-1"></i>No data available in table</td></tr></template>
                <template x-for="row in paginatedEntries" :key="row.cost_center_header + '_' + row.id_cost_header">
                    <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="border-b border-r px-4 py-2.5 text-center">
                            <a :href="detailUrl(row)" :title="row.status_entry === 'sudah' ? 'Update Entry' : 'Entry'"
                                :class="row.status_entry === 'sudah' ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'"
                                class="inline-flex items-center justify-center rounded p-1.5 text-white shadow active:scale-[0.98] transition-all"><i class="fas fa-edit text-[11px]"></i></a>
                        </td>
                        <td class="border-b border-r px-4 py-2.5 font-medium">
                            <span x-text="row.cost_center_header"></span>
                            <span class="ml-2 rounded px-1.5 py-0.5 text-[10px] font-semibold" :class="row.status_entry === 'sudah' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'" x-text="row.status_entry"></span>
                        </td>
                        <template x-for="m in actualMonthKeys" :key="m"><td class="border-b border-r px-2 py-2 text-right font-mono" x-text="fmtActual(row.actual[m])"></td></template>
                        <td class="border-b px-4 py-2.5 text-right font-bold" x-text="fmtActual(row.total_actual)"></td>
                    </tr>
                </template>
            </tbody>
            <tfoot>
                <template x-if="entryAccounts.length > 0"><tr class="bg-gray-100 font-bold text-gray-900 dark:bg-meta-4 dark:text-white">
                    <td colspan="2" class="border-t border-r px-4 py-3 uppercase">TOTAL</td>
                    <template x-for="m in actualMonthKeys" :key="'ft_' + m"><td class="border-t border-r px-2 py-3 text-right" x-text="fmtActual(columnTotal(m))"></td></template>
                    <td class="border-t px-4 py-3 text-right" x-text="fmtActual(grandTotal())"></td>
                </tr></template>
            </tfoot>
        </table>
    </div>

    <template x-if="entryAccounts.length > 0"><div class="flex flex-wrap items-center justify-between gap-3 border border-stroke bg-white px-4 py-3 text-xs dark:border-strokedark dark:bg-boxdark">
        <span class="text-gray-600 dark:text-gray-400">Showing <span class="font-bold" x-text="((currentPage - 1) * perPage) + 1"></span> to <span class="font-bold" x-text="Math.min(currentPage * perPage, totalRows)"></span> of <span class="font-bold" x-text="totalRows"></span> entries</span>
        <div class="flex items-center gap-1" x-show="totalPages > 1">
            <button type="button" @click="goPage(currentPage - 1)" :disabled="currentPage === 1" class="h-7 rounded border px-2.5 disabled:opacity-40"><i class="fas fa-chevron-left text-[10px]"></i></button>
            <template x-for="(p, pi) in pageNumbers()" :key="'pg_' + pi"><button type="button" @click="goPage(p)" x-text="p" class="h-7 min-w-[28px] rounded border px-1.5" :class="currentPage === p ? 'bg-primary text-white' : ''"></button></template>
            <button type="button" @click="goPage(currentPage + 1)" :disabled="currentPage === totalPages" class="h-7 rounded border px-2.5 disabled:opacity-40"><i class="fas fa-chevron-right text-[10px]"></i></button>
        </div>
    </div></template>
</div>
