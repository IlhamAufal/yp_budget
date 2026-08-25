<div class="space-y-6">
    <div class="rounded-2xl border border-amber-200/80 bg-amber-50/70 p-4 dark:border-amber-900/50 dark:bg-amber-950/30 shadow-xs">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 text-amber-600 dark:text-amber-400"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="text-xs text-amber-800 dark:text-amber-300">
                <h4 class="text-xs font-bold text-amber-900 dark:text-amber-200">Peringatan / Warning Information:</h4>
                <p class="mt-1 leading-relaxed">Pastikan Cost Center yang dipilih sesuai otorisasi Anda. Gunakan tombol Entry untuk membuka seluruh akun dalam kelompok header.</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Cost Center</label>
        <select x-model="selectedEntryCc" @change="fetchEntryData()"
            class="w-full max-w-2xl rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs font-medium outline-none transition focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:bg-white dark:text-white">
            <option value="">-- Pilih Cost Center --</option>
            <template x-for="item in costCenters" :key="item.id"><option :value="item.id" x-text="item.text"></option></template>
        </select>
    </div>

    <div class="space-y-4">
        <!-- Table Section Label (Separated from table container) -->
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Daftar Akun Header OPEX GA (Actual)</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ringkasan actual biaya per akun sebelum pengisian rincian budget.</p>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs overflow-hidden dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300 border-collapse min-w-[1000px] whitespace-nowrap">
                    <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
                        <tr class="border-b border-white/20 bg-[#2F3185] text-white font-semibold">
                            <th rowspan="2" class="border-r border-white/20 px-4 py-3 text-center w-20 text-white font-semibold">Entry</th>
                            <th rowspan="2" class="border-r border-white/20 px-4 py-3 min-w-[260px] text-white font-semibold">General Administrative Expense</th>
                            <th colspan="8" class="border-r border-white/20 px-2 py-2 text-center bg-[#25276d] text-white font-semibold">Actual</th>
                            <th rowspan="2" class="px-4 py-3 text-right text-white bg-[#25276d] font-bold">Total</th>
                        </tr>
                        <tr class="border-b border-white/20 bg-[#25276d] text-white text-xs font-semibold">
                            <template x-for="m in actualMonths" :key="m"><th class="border-r border-white/20 px-2.5 py-2 text-right text-white font-semibold" x-text="m"></th></template>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs font-mono">
                        <template x-if="entryAccounts.length === 0">
                            <tr>
                                <td colspan="11" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                                            <i class="fa-solid fa-coins text-xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Budget OPEX GA</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Pilih Cost Center pada menu di atas untuk menampilkan data akun anggaran.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-for="row in paginatedEntries" :key="row.cost_center_header + '_' + row.id_cost_header">
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="border-r border-gray-200 dark:border-gray-800 px-4 py-2.5 text-center font-sans">
                                    <a :href="detailUrl(row)" :title="row.status_entry === 'sudah' ? 'Update Entry' : 'Entry'"
                                        :class="row.status_entry === 'sudah' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-[#2F3185] hover:bg-[#25276d]'"
                                        class="h-7 w-7 rounded-lg inline-flex items-center justify-center text-white shadow-xs active:scale-[0.98] transition-all">
                                        <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                    </a>
                                </td>
                                <td class="border-r border-gray-200 dark:border-gray-800 px-4 py-2.5 font-sans font-medium text-gray-900 dark:text-white">
                                    <span x-text="row.cost_center_header"></span>
                                    <span class="ml-2 rounded-md px-2 py-0.5 text-[10px] font-bold" :class="row.status_entry === 'sudah' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'" x-text="row.status_entry"></span>
                                </td>
                                <template x-for="m in actualMonthKeys" :key="m"><td class="border-r border-gray-200 dark:border-gray-800 px-2.5 py-2 text-right" x-text="fmtActual(row.actual[m])"></td></template>
                                <td class="px-4 py-2.5 text-right font-bold text-gray-900 dark:text-white" x-text="fmtActual(row.total_actual)"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <template x-if="entryAccounts.length > 0">
                            <tr class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs font-mono">
                                <td colspan="2" class="border-r border-gray-300 dark:border-gray-700 px-4 py-3 font-sans font-bold">Grand Total</td>
                                <template x-for="m in actualMonthKeys" :key="'ft_' + m"><td class="border-r border-gray-300 dark:border-gray-700 px-2.5 py-3 text-right" x-text="fmtActual(columnTotal(m))"></td></template>
                                <td class="px-4 py-3 text-right text-[#2F3185] dark:text-indigo-400 font-bold" x-text="fmtActual(grandTotal())"></td>
                            </tr>
                        </template>
                    </tfoot>
                </table>
            </div>

            <template x-if="entryAccounts.length > 0">
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 text-xs text-gray-500 dark:text-gray-400">
                    <span>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="((currentPage - 1) * perPage) + 1"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, totalRows)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalRows"></span> data</span>
                    <div class="flex items-center gap-1" x-show="totalPages > 1">
                        <button type="button" @click="goPage(currentPage - 1)" :disabled="currentPage === 1" class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 transition-colors"><i class="fa-solid fa-chevron-left text-[10px]"></i></button>
                        <template x-for="(p, pi) in pageNumbers()" :key="'pg_' + pi">
                            <button type="button" @click="goPage(p)" x-text="p" class="h-8 min-w-[32px] px-2 rounded-lg text-xs font-semibold transition-colors flex items-center justify-center" :class="currentPage === p ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"></button>
                        </template>
                        <button type="button" @click="goPage(currentPage + 1)" :disabled="currentPage === totalPages" class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 transition-colors"><i class="fa-solid fa-chevron-right text-[10px]"></i></button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
