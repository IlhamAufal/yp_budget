<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs flex flex-col lg:flex-row lg:items-end justify-between gap-5">
        <div class="w-full max-w-lg">
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tampilkan:</label>
            <select x-model="filterShowDep" @change="fetchData()" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs font-medium focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 outline-none transition">
                <template x-for="opt in showOptions" :key="opt.id">
                    <option :value="opt.id" x-text="opt.name"></option>
                </template>
            </select>
        </div>
        
        <div class="shrink-0">
            <button @click="exportExcel('Summary Depreciation')" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98]">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export to Excel</span>
            </button>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Summary Berdasarkan Jenis Asset (Depreciation)</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Proyeksi beban penyusutan (depresiasi) aset tetap per kategori.</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin" style="max-height: 70vh;">
            <table class="w-full text-left text-xs border-collapse min-w-[1100px] whitespace-nowrap">
                <thead class="sticky top-0 z-10 bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th class="border-r border-white/20 py-3.5 px-4 min-w-[240px] text-white font-semibold">Jenis Asset (Depreciation)</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-right text-white font-semibold">Jan</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-right text-white font-semibold">Feb</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-right text-white font-semibold">Mar</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-right text-white font-semibold">Apr</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-right text-white font-semibold">May</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-right text-white font-semibold">Jun</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-right text-white font-semibold">Jul</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-right text-white font-semibold">Aug</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-right text-white font-semibold">Sep</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-right text-white font-semibold">Oct</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-right text-white font-semibold">Nov</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-right text-white font-semibold">Dec</th>
                        <th class="py-3.5 px-4 text-right text-white bg-[#25276d] font-bold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-if="depData.length === 0">
                        <tr>
                            <td colspan="14" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-arrow-trend-down text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Depresiasi</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada data depresiasi aset untuk filter ini.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(row, idx) in depData" :key="idx">
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-4 font-sans font-medium text-gray-900 dark:text-white" x-text="`${row.category_code} - ${row.category_name}`"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmt(row.jan)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmt(row.feb)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmt(row.mar)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmt(row.apr)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmt(row.may)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmt(row.jun)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmt(row.jul)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmt(row.aug)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmt(row.sep)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmt(row.oct)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmt(row.nov)"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmt(row.dec)"></td>
                            <td class="py-2.5 px-4 text-right font-bold text-gray-900 dark:text-white bg-gray-50/70 dark:bg-gray-800/50" x-text="fmt(row.total)"></td>
                        </tr>
                    </template>
                </tbody>
                <tfoot x-show="depData.length > 0" class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold text-gray-900 dark:text-white border-t-2 border-[#2F3185]/30 text-xs font-mono">
                    <tr>
                        <td class="py-3 px-4 font-sans border-r border-gray-300 dark:border-gray-700">Total Depreciation</td>
                        <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmt(depTotals.jan)"></td>
                        <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmt(depTotals.feb)"></td>
                        <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmt(depTotals.mar)"></td>
                        <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmt(depTotals.apr)"></td>
                        <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmt(depTotals.may)"></td>
                        <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmt(depTotals.jun)"></td>
                        <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmt(depTotals.jul)"></td>
                        <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmt(depTotals.aug)"></td>
                        <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmt(depTotals.sep)"></td>
                        <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmt(depTotals.oct)"></td>
                        <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmt(depTotals.nov)"></td>
                        <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmt(depTotals.dec)"></td>
                        <td class="py-3 px-4 text-right text-[#2F3185] dark:text-indigo-400" x-text="fmt(depTotals.total)"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
