<div class="space-y-6">

    <div class="rounded-sm border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 text-red-600 dark:text-red-400">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-red-800 dark:text-red-300">Peringatan / Warning Information:</h4>
                    <ul class="mt-1 list-disc pl-4 text-xs text-red-700 dark:text-red-300/80 space-y-1">
                        <li>Pastikan Anda memilih <strong>Cost Center</strong> yang sesuai sebelum mengisi alokasi anggaran.</li>
                        <li>Klik tombol <strong>"Entry"</strong> pada baris akun terkait untuk memasukkan/memperbarui rincian item budget.</li>
                        <li>Jangan lupa menekan tombol <strong>Simpan Detail</strong> di halaman rincian agar perubahan tersimpan permanen.</li>
                    </ul>
                </div>
            </div>

            <button @click="manualBookModalOpen = true"
                class="inline-flex shrink-0 items-center gap-1.5 rounded bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow hover:bg-red-700 transition">
                <i class="fas fa-book"></i> Manual Book
            </button>
        </div>
    </div>

    <div class="rounded-sm border border-stroke bg-gray-50 p-4 dark:border-strokedark dark:bg-meta-4">
        <label class="mb-2 block text-sm font-semibold text-black dark:text-white">Cost Center</label>
        <div class="relative w-full max-w-2xl">
            <select x-model="selectedEntryCc" @change="fetchEntryData()"
                class="w-full rounded border border-stroke bg-white px-4 py-2.5 text-sm font-medium outline-none transition focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                <option value="">-- Pilih Cost Center --</option>
                <template x-for="item in costCenters" :key="item.id">
                    <option :value="item.id" x-text="item.text"></option>
                </template>
            </select>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto border border-stroke dark:border-strokedark">
        <table class="w-full table-auto text-left text-xs">
            <thead>
                <tr class="bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300">
                    <th rowspan="2" class="border-b border-r px-4 py-2.5 font-bold uppercase text-center w-16">ENTRY</th>
                    <th rowspan="2" class="border-b border-r px-4 py-2.5 font-bold uppercase min-w-[240px]">GENERAL ADMINISTRATIVE EXPENSE</th>
                    <th colspan="8" class="border-b border-r px-4 py-2.5 font-bold uppercase text-center">ACTUAL</th>
                    <th rowspan="2" class="border-b px-4 py-2.5 font-bold uppercase text-right w-32">TOTAL</th>
                </tr>
                <tr class="bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300">
                    <template x-for="m in actualMonths" :key="m">
                        <th class="border-b border-r px-2 py-2 text-center font-bold uppercase" x-text="m"></th>
                    </template>
                </tr>
            </thead>
            <tbody>
                <template x-if="entryAccounts.length === 0">
                    <tr>
                        <td colspan="11" class="border-b py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox mr-1"></i>No data available in table
                        </td>
                    </tr>
                </template>
                <template x-for="(row, idx) in paginatedEntries" :key="row.main_account">
                    <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="border-b border-r px-4 py-2.5 text-center">
                            <button @click="goToDetail(row)" title="Entry"
                                class="inline-flex items-center justify-center rounded bg-blue-600 p-1.5 text-white shadow hover:bg-blue-700 active:scale-[0.98] transition-all">
                                <i class="fas fa-edit text-[11px]"></i>
                            </button>
                        </td>
                        <td class="border-b border-r px-4 py-2.5 font-medium" x-text="row.description"></td>
                        <template x-for="m in actualMonthKeys" :key="m">
                            <td class="border-b border-r px-2 py-2 text-right font-mono" x-text="fmtActual(row[m])"></td>
                        </template>
                        <td class="border-b px-4 py-2.5 text-right font-bold" x-text="fmtActual(row.total)"></td>
                    </tr>
                </template>
            </tbody>
            <tfoot>
                <template x-if="entryAccounts.length > 0">
                    <tr class="bg-gray-100 dark:bg-meta-4 font-bold text-gray-900 dark:text-white">
                        <td colspan="2" class="border-t border-r px-4 py-3 uppercase">TOTAL</td>
                        <template x-for="m in actualMonthKeys" :key="'ft_' + m">
                            <td class="border-t border-r px-2 py-3 text-right font-bold" x-text="fmtActual(columnTotal(m))"></td>
                        </template>
                        <td class="border-t px-4 py-3 text-right font-bold" x-text="fmtActual(grandTotal())"></td>
                    </tr>
                </template>
            </tfoot>
        </table>
    </div>

    <template x-if="entryAccounts.length > 0">
        <div class="flex flex-wrap items-center justify-between gap-3 border border-stroke dark:border-strokedark bg-white px-4 py-3 text-xs dark:bg-boxdark">
            <span class="text-gray-600 dark:text-gray-400">
                Showing <span class="font-bold text-gray-800 dark:text-gray-200" x-text="((currentPage - 1) * perPage) + 1"></span> to
                <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, totalRows)"></span> of
                <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalRows"></span> entries
            </span>
            <div class="flex items-center gap-1" x-show="totalPages > 1">
                <button type="button" @click="goPage(currentPage - 1)" :disabled="currentPage === 1"
                    class="h-7 px-2.5 rounded-lg border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-50 transition">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </button>
                <template x-for="(p, pi) in pageNumbers()" :key="'pg_' + pi">
                    <span x-show="p === '...'" class="px-1.5 text-gray-400">&hellip;</span>
                    <button x-show="p !== '...'" type="button" @click="goPage(p)"
                        :class="currentPage === p ? 'bg-primary text-white font-bold' : 'border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition'"
                        class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
                </template>
                <button type="button" @click="goPage(currentPage + 1)" :disabled="currentPage === totalPages"
                    class="h-7 px-2.5 rounded-lg border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-50 transition">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </button>
            </div>
        </div>
    </template>
</div>
