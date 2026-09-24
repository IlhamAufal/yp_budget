<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <label for="actualCc" class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Cost Center</label>
        <select id="actualCc" x-model="selectedActualCc" @change="fetchActualData()"
            class="w-full max-w-2xl rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs font-medium outline-none transition focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:bg-white dark:text-white">
            <option value="">-- Pilih Cost Center --</option>
            <template x-for="item in costCenters" :key="item.id">
                <option :value="item.id" x-text="item.name"></option>
            </template>
        </select>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Pilih 600 untuk Domestic atau 700 untuk Export. Data dimuat setelah pilihan dibuat.</p>
    </div>

    <div x-show="uploadFeedback" x-cloak class="rounded-2xl border border-emerald-200/80 bg-emerald-50/80 p-4 text-xs font-semibold text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300 shadow-xs" x-text="uploadFeedback"></div>
    <div x-show="errorMessage" x-cloak class="rounded-2xl border border-red-200/80 bg-red-50/80 p-4 text-xs font-semibold text-red-800 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300 shadow-xs" x-text="errorMessage"></div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a :href="selectedActualCc ? '<?= base_url('opex-selling/exportActual') ?>?cost_center=' + encodeURIComponent(selectedActualCc) : '#'"
                :class="!selectedActualCc ? 'pointer-events-none opacity-50' : ''"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 text-xs font-semibold text-white shadow-xs transition-all active:scale-[0.98]">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Data Actual</span>
            </a>

            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <span>Tampilkan</span>
                <select x-model="perPage" @change="onChangePerPage()" class="rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-xs text-gray-700 dark:text-gray-200 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:bg-white outline-none">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entri</span>
            </div>
        </div>

        <div>
            <input id="actualSearch" type="text" x-model="search" @input="onSearch()" placeholder="Pencarian akun atau deskripsi..."
                class="w-full sm:w-64 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2 text-xs text-gray-800 dark:text-gray-200 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:bg-white outline-none transition">
        </div>
    </div>

    <div class="space-y-4">
        <!-- Table Section Label (Separated from table container) -->
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Tabel Realisasi Actual OPEX Selling</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar biaya realisasi per akun dan cost center penjualan.</p>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs overflow-hidden dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300 border-collapse min-w-[1200px] whitespace-nowrap">
                    <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
                        <tr class="border-b border-white/20 bg-[#2F3185] text-white font-semibold">
                            <th class="border-r border-white/20 px-3 py-3 w-12 text-center text-white font-semibold">No.</th>
                            <th class="border-r border-white/20 px-4 py-3 min-w-[140px] text-white font-semibold">Main Account</th>
                            <th class="border-r border-white/20 px-4 py-3 min-w-[220px] text-white font-semibold">Description</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white font-semibold">Jan</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white font-semibold">Feb</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white font-semibold">Mar</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white font-semibold">Apr</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white font-semibold">May</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white font-semibold">Jun</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white font-semibold">Jul</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white font-semibold">Aug</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white bg-[#25276d] font-semibold">Avg</th>
                            <th class="px-4 py-3 text-right text-white bg-[#25276d] font-bold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs font-mono">
                        <template x-if="loading">
                            <tr>
                                <td colspan="13" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
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
                                <td colspan="13" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
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
                                <td colspan="13" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                                            <i class="fa-solid fa-chart-column text-xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Actual</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ditemukan rincian data aktual untuk cost center terpilih.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-for="entry in displayRows()" :key="entry.key">
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors" :class="entry.type === 'heading' ? 'bg-gray-50/80 dark:bg-gray-800/60 font-bold text-gray-900 dark:text-white' : (entry.type === 'subtotal' ? 'bg-gray-50 dark:bg-gray-800/60 font-semibold' : '')">
                                <td x-show="entry.type === 'heading'" colspan="13" class="px-4 py-2.5 font-bold text-xs font-sans text-[#2F3185] dark:text-indigo-400" x-text="entry.name"></td>
                                <td x-show="entry.type === 'item'" class="border-r border-gray-200 dark:border-gray-800 px-3 py-2 text-center text-gray-500 font-sans" x-text="entry.number"></td>
                                <td x-show="entry.type === 'item'" class="border-r border-gray-200 dark:border-gray-800 px-4 py-2 font-mono font-bold text-gray-900 dark:text-white" x-text="entry.row.main_account"></td>
                                <td x-show="entry.type === 'item'" class="border-r border-gray-200 dark:border-gray-800 px-4 py-2 font-sans font-medium text-gray-800 dark:text-gray-200" x-text="entry.row.cost_center_desc"></td>
                                <template x-for="month in ['jan','feb','mar','apr','may','jun','jul','aug']" :key="entry.key + '_detail_' + month">
                                    <td x-show="entry.type === 'item'" class="border-r border-gray-200 dark:border-gray-800 px-3 py-2 text-right text-gray-700 dark:text-gray-300" x-text="formatNumber(entry.row[month])"></td>
                                </template>
                                <td x-show="entry.type === 'item'" class="border-r border-gray-200 dark:border-gray-800 px-3 py-2 text-right font-semibold text-gray-800 dark:text-gray-200 bg-gray-50/50 dark:bg-gray-800/40" x-text="formatNumber(entry.row.avg)"></td>
                                <td x-show="entry.type === 'item'" class="px-4 py-2 text-right font-bold text-gray-900 dark:text-white bg-gray-50/80 dark:bg-gray-800/60" x-text="formatNumber(entry.row.total)"></td>
                                <td x-show="entry.type === 'subtotal'" colspan="3" class="border-r border-gray-200 dark:border-gray-700 px-4 py-2.5 font-sans font-bold text-gray-900 dark:text-white" x-text="'Subtotal ' + entry.name"></td>
                                <template x-for="month in ['jan','feb','mar','apr','may','jun','jul','aug']" :key="entry.key + '_subtotal_' + month">
                                    <td x-show="entry.type === 'subtotal'" class="border-r border-gray-200 dark:border-gray-700 px-3 py-2.5 text-right font-bold" x-text="formatNumber(groupTotal(entry.name, month))"></td>
                                </template>
                                <td x-show="entry.type === 'subtotal'" class="border-r border-gray-200 dark:border-gray-700 px-3 py-2.5 text-right font-bold" x-text="formatNumber(groupTotal(entry.name, 'total') / 8)"></td>
                                <td x-show="entry.type === 'subtotal'" class="px-4 py-2.5 text-right font-bold text-[#2F3185] dark:text-indigo-400" x-text="formatNumber(groupTotal(entry.name, 'total'))"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot x-show="!loading && selectedActualCc && totalData > 0">
                        <tr class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs font-mono">
                            <td colspan="3" class="border-r border-gray-300 dark:border-gray-700 px-4 py-3 text-right font-sans font-bold">Grand Total</td>
                            <template x-for="month in ['jan','feb','mar','apr','may','jun','jul','aug']" :key="'grand_' + month">
                                <td class="border-r border-gray-300 dark:border-gray-700 px-3 py-3 text-right" x-text="formatNumber(filteredTotal(month))"></td>
                            </template>
                            <td class="border-r border-gray-300 dark:border-gray-700 px-3 py-3 text-right" x-text="formatNumber(filteredTotal('total') / 8)"></td>
                            <td class="px-4 py-3 text-right text-[#2F3185] dark:text-indigo-400 font-bold" x-text="formatNumber(filteredTotal('total'))"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 text-xs text-gray-500 dark:text-gray-400">
                <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="pageFrom"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="pageTo"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalData"></span> data</div>
                <div class="flex items-center gap-1">
                    <button @click="goPage(currentPage - 1)" :disabled="currentPage <= 1" class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </button>
                    <template x-for="(page, pageIndex) in pageNumbers()" :key="'page_' + pageIndex">
                        <span>
                            <button x-show="page !== '...'" @click="goPage(page)" :class="currentPage === page ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="h-8 min-w-[32px] px-2 rounded-lg text-xs font-semibold transition flex items-center justify-center" x-text="page"></button>
                            <span x-show="page === '...'" class="px-1.5 text-gray-400">&hellip;</span>
                        </span>
                    </template>
                    <button @click="goPage(currentPage + 1)" :disabled="currentPage >= totalPages" class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>