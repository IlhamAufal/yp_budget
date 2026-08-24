<div class="space-y-4">
    <div class="rounded-2xl border border-gray-200/80 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <label for="actualCc" class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Cost Center</label>
        <select id="actualCc" x-model="selectedActualCc" @change="fetchActualData()"
            class="w-full max-w-2xl rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2 text-xs font-medium outline-none transition focus:border-brand-500 focus:bg-white dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            <option value="">-- Pilih Cost Center --</option>
            <template x-for="item in costCenters" :key="item.id">
                <option :value="item.id" x-text="item.name"></option>
            </template>
        </select>
        <p class="mt-1.5 text-xs text-gray-400">Pilih 600 untuk Domestic atau 700 untuk Export. Data dimuat setelah pilihan dibuat.</p>
    </div>

    <div x-show="uploadFeedback" x-cloak class="rounded-2xl border border-emerald-200/80 bg-emerald-50/80 p-4 text-xs font-semibold text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300" x-text="uploadFeedback"></div>
    <div x-show="errorMessage" x-cloak class="rounded-2xl border border-red-200/80 bg-red-50/80 p-4 text-xs font-semibold text-red-800 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300" x-text="errorMessage"></div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <a :href="selectedActualCc ? '<?= base_url('opex-selling/exportActual') ?>?cost_center=' + encodeURIComponent(selectedActualCc) : '#'"
                :class="!selectedActualCc ? 'pointer-events-none opacity-50' : ''"
                class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-950/40 px-3.5 py-2 text-xs font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-all shadow-xs">
                <i class="fa-solid fa-file-excel"></i> Export Data Actual
            </a>

            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                <span>Show</span>
                <select x-model="perPage" @change="onChangePerPage()" class="rounded-xl border border-gray-200 bg-gray-50/50 px-2.5 py-1.5 text-xs text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                <input id="actualSearch" type="text" x-model="search" @input="onSearch()" placeholder="Cari account / group / deskripsi..."
                    class="rounded-xl border border-gray-200 bg-gray-50/50 pl-8 pr-3 py-1.5 text-xs text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 focus:border-brand-500 focus:bg-white outline-none">
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs overflow-hidden dark:border-gray-800 dark:bg-gray-900">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300 border-collapse">
                <thead class="bg-brand-500 text-white text-xs font-semibold">
                    <tr class="border-b border-brand-600 bg-brand-500 text-white font-semibold">
                        <th class="border-r border-white/20 px-3 py-3 w-12 text-center text-white">No.</th>
                        <th class="border-r border-white/20 px-3 py-3 min-w-[150px] text-white">Main Account</th>
                        <th class="border-r border-white/20 px-3 py-3 min-w-[200px] text-white">Description</th>
                        <th class="border-r border-white/20 px-3 py-3 text-right text-white">Jan</th>
                        <th class="border-r border-white/20 px-3 py-3 text-right text-white">Feb</th>
                        <th class="border-r border-white/20 px-3 py-3 text-right text-white">Mar</th>
                        <th class="border-r border-white/20 px-3 py-3 text-right text-white">Apr</th>
                        <th class="border-r border-white/20 px-3 py-3 text-right text-white">May</th>
                        <th class="border-r border-white/20 px-3 py-3 text-right text-white">Jun</th>
                        <th class="border-r border-white/20 px-3 py-3 text-right text-white">Jul</th>
                        <th class="border-r border-white/20 px-3 py-3 text-right text-white">Aug</th>
                        <th class="border-r border-white/20 px-3 py-3 text-right text-white bg-brand-600">Avg</th>
                        <th class="px-3 py-3 text-right text-white bg-brand-600">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                    <template x-if="loading">
                        <tr>
                            <td colspan="13" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-brand-500">
                                        <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data...</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sistem sedang memproses data actual.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loading && !selectedActualCc">
                        <tr>
                            <td colspan="13" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-filter text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Pilih Cost Center Terlebih Dahulu</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Silakan pilih Cost Center pada dropdown di atas untuk menampilkan data.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loading && selectedActualCc && totalData === 0">
                        <tr>
                            <td colspan="13" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-chart-column text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Actual</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ditemukan rincian data aktual untuk cost center terpilih.</p>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-for="entry in displayRows()" :key="entry.key">
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors" :class="entry.type === 'heading' ? 'bg-brand-50/30 dark:bg-brand-500/5 font-bold text-brand-600 dark:text-brand-400' : (entry.type === 'subtotal' ? 'bg-gray-50 dark:bg-gray-800/60 font-semibold' : '')">
                            <td x-show="entry.type === 'heading'" colspan="13" class="px-4 py-2.5 uppercase font-bold text-xs" x-text="entry.name"></td>
                            <td x-show="entry.type === 'item'" class="border-r border-gray-100 dark:border-gray-800 px-3 py-2 text-center" x-text="entry.number"></td>
                            <td x-show="entry.type === 'item'" class="border-r border-gray-100 dark:border-gray-800 px-3 py-2 font-mono font-bold text-gray-900 dark:text-white" x-text="entry.row.main_account"></td>
                            <td x-show="entry.type === 'item'" class="border-r border-gray-100 dark:border-gray-800 px-3 py-2 font-medium text-gray-800 dark:text-gray-200" x-text="entry.row.cost_center_desc"></td>
                            <template x-for="month in ['jan','feb','mar','apr','may','jun','jul','aug']" :key="entry.key + '_detail_' + month">
                                <td x-show="entry.type === 'item'" class="border-r border-gray-100 dark:border-gray-800 px-3 py-2 text-right font-mono" x-text="formatNumber(entry.row[month])"></td>
                            </template>
                            <td x-show="entry.type === 'item'" class="border-r border-gray-100 dark:border-gray-800 px-3 py-2 text-right font-semibold font-mono bg-gray-50/50 dark:bg-gray-800/40" x-text="formatNumber(entry.row.avg)"></td>
                            <td x-show="entry.type === 'item'" class="px-3 py-2 text-right font-bold text-gray-900 dark:text-white font-mono bg-gray-50/80 dark:bg-gray-800/60" x-text="formatNumber(entry.row.total)"></td>
                            <td x-show="entry.type === 'subtotal'" colspan="3" class="border-r border-gray-200 dark:border-gray-700 px-3 py-2.5 uppercase font-bold text-gray-900 dark:text-white" x-text="'Subtotal ' + entry.name"></td>
                            <template x-for="month in ['jan','feb','mar','apr','may','jun','jul','aug']" :key="entry.key + '_subtotal_' + month">
                                <td x-show="entry.type === 'subtotal'" class="border-r border-gray-200 dark:border-gray-700 px-3 py-2.5 text-right font-mono font-bold" x-text="formatNumber(groupTotal(entry.name, month))"></td>
                            </template>
                            <td x-show="entry.type === 'subtotal'" class="border-r border-gray-200 dark:border-gray-700 px-3 py-2.5 text-right font-mono font-bold" x-text="formatNumber(groupTotal(entry.name, 'total') / 8)"></td>
                            <td x-show="entry.type === 'subtotal'" class="px-3 py-2.5 text-right font-mono font-bold text-gray-900 dark:text-white" x-text="formatNumber(groupTotal(entry.name, 'total'))"></td>
                        </tr>
                    </template>
                </tbody>
                <tfoot x-show="!loading && selectedActualCc && totalData > 0">
                    <tr class="bg-gray-100 dark:bg-gray-800 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs">
                        <td colspan="3" class="border-r border-gray-300 dark:border-gray-700 px-3 py-3 text-right uppercase">Grand Total</td>
                        <template x-for="month in ['jan','feb','mar','apr','may','jun','jul','aug']" :key="'grand_' + month">
                            <td class="border-r border-gray-300 dark:border-gray-700 px-3 py-3 text-right font-mono" x-text="formatNumber(filteredTotal(month))"></td>
                        </template>
                        <td class="border-r border-gray-300 dark:border-gray-700 px-3 py-3 text-right font-mono" x-text="formatNumber(filteredTotal('total') / 8)"></td>
                        <td class="px-3 py-3 text-right text-brand-600 dark:text-brand-400 font-mono" x-text="formatNumber(filteredTotal('total'))"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 text-xs text-gray-500 dark:text-gray-400">
            <div>Showing <span class="font-bold text-gray-800 dark:text-gray-200" x-text="pageFrom"></span> to <span class="font-bold text-gray-800 dark:text-gray-200" x-text="pageTo"></span> of <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalData"></span> entries</div>
            <div class="flex items-center gap-1">
                <button @click="goPage(currentPage - 1)" :disabled="currentPage <= 1" class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </button>
                <template x-for="(page, pageIndex) in pageNumbers()" :key="'page_' + pageIndex">
                    <span>
                        <button x-show="page !== '...'" @click="goPage(page)" :class="currentPage === page ? 'bg-brand-500 text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="h-8 min-w-[32px] px-2 rounded-lg text-xs font-semibold transition" x-text="page"></button>
                        <span x-show="page === '...'" class="px-1 text-gray-400">&hellip;</span>
                    </span>
                </template>
                <button @click="goPage(currentPage + 1)" :disabled="currentPage >= totalPages" class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
            </div>
        </div>
    </div>
</div>