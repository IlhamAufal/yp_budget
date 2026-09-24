<div class="space-y-6">
    <div class="rounded-2xl border border-blue-200 bg-blue-50/70 p-4 text-xs font-medium text-blue-800 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300 flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-info text-blue-600 text-sm shrink-0"></i>
            <div>
                <span class="font-bold">Information:</span>
                <p class="text-xs mt-0.5">Periode submit data CAPEX dimulai pada <strong>19 Jul 2026 s/d 31 Jul 2026</strong></p>
            </div>
        </div>
    </div>

    <div>
        <button type="button" @click="window.openManualBook && window.openManualBook()" class="w-full flex items-center justify-center gap-2 rounded-xl border border-rose-200/80 bg-rose-50/80 p-3 text-rose-600 hover:bg-rose-100/80 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-300 transition font-bold text-xs shadow-xs cursor-pointer active:scale-[0.99]">
            <i class="fa-solid fa-book-open text-sm"></i>
            <span>TATA CARA PENGGUNAAN / MANUAL BOOK CAPEX</span>
        </button>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Cost Center</label>
        <select x-model="selectedCostCenter" @change="loadEntryData()" class="w-full max-w-lg rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs font-medium focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 outline-none transition">
            <template x-for="cc in costCenters" :key="cc.id">
                <option :value="cc.id" x-text="cc.name"></option>
            </template>
        </select>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Rincian Anggaran Belanja Modal (CAPEX) 12 Bulan</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar alokasi anggaran per kategori aset tetap.</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1100px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th class="border-r border-white/20 py-3.5 px-3 w-16 text-center text-white font-semibold">Entry</th>
                        <th class="border-r border-white/20 py-3.5 px-4 min-w-[220px] text-white font-semibold">Categories</th>
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
                    <template x-for="cat in categories" :key="cat.code">
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-3 text-center">
                                <button @click="openFormCapex(cat)" class="h-7 w-7 rounded-lg inline-flex items-center justify-center bg-[#2F3185] hover:bg-[#25276d] text-white shadow-xs transition active:scale-[0.98]">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                </button>
                            </td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-4 font-sans font-medium text-gray-900 dark:text-white" x-text="`${cat.code} - ${cat.name}`"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmtNumber(catVal(cat, 'jan'))"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmtNumber(catVal(cat, 'feb'))"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmtNumber(catVal(cat, 'mar'))"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmtNumber(catVal(cat, 'apr'))"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmtNumber(catVal(cat, 'may'))"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmtNumber(catVal(cat, 'jun'))"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmtNumber(catVal(cat, 'jul'))"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmtNumber(catVal(cat, 'aug'))"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmtNumber(catVal(cat, 'sep'))"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmtNumber(catVal(cat, 'oct'))"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmtNumber(catVal(cat, 'nov'))"></td>
                            <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right" x-text="fmtNumber(catVal(cat, 'dec'))"></td>
                            <td class="py-2.5 px-4 text-right font-bold text-gray-900 dark:text-white bg-gray-50/70 dark:bg-gray-800/50" x-text="fmtNumber(catTotal(cat))"></td>
                        </tr>
                    </template>
                    <tr x-show="categories.length === 0">
                        <td colspan="15" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-cubes-stacked text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Kategori Aset</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Data kategori aset belum terkonfigurasi di sistem.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold text-gray-900 dark:text-white border-t-2 border-[#2F3185]/30 text-xs font-mono">
                    <tr>
                        <td colspan="2" class="py-3 px-4 font-sans border-r border-gray-300 dark:border-gray-700">Total CAPEX</td>
                        <template x-for="m in monthKeys" :key="'ft_' + m">
                            <td class="py-3 px-2.5 text-right border-r border-gray-300 dark:border-gray-700" x-text="fmtNumber(categories.reduce((s, c) => s + catVal(c, m), 0))"></td>
                        </template>
                        <td class="py-3 px-4 text-right text-[#2F3185] dark:text-indigo-400" x-text="fmtNumber(categories.reduce((s, c) => s + catTotal(c), 0))"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>