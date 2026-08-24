<div class="space-y-4">
    <div class="rounded-2xl border border-blue-200 bg-blue-50/70 p-4 text-xs font-medium text-blue-800 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-info text-blue-600 text-sm shrink-0"></i>
            <div>
                <span class="font-bold">Information !</span>
                <p class="text-xs mt-0.5">Periode submit data CAPEX dimulai pada <strong>19 Jul 2026 s/d 31 Jul 2026</strong></p>
            </div>
        </div>
    </div>

    <div>
        <button type="button" @click="window.openManualBook && window.openManualBook()" class="w-full flex items-center justify-center gap-2 rounded-xl border border-red-200/80 bg-red-50/80 p-3 text-red-600 hover:bg-red-100/80 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300 transition font-bold text-xs shadow-xs cursor-pointer">
            <i class="fa-solid fa-book-open text-sm"></i>
            TATA CARA PENGGUNAAN / MANUAL BOOK CAPEX
        </button>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Cost Center</label>
        <select x-model="selectedCostCenter" @change="loadEntryData()" class="w-full max-w-lg rounded-xl border border-gray-200 bg-gray-50/50 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3.5 py-2 text-xs font-medium focus:border-brand-500 focus:bg-white outline-none transition">
            <template x-for="cc in costCenters" :key="cc.id">
                <option :value="cc.id" x-text="cc.name"></option>
            </template>
        </select>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse text-gray-600 dark:text-gray-300 min-w-[1000px]">
                <thead class="bg-brand-500 text-white text-xs font-semibold">
                    <tr class="border-b border-brand-600 bg-brand-500 text-white font-semibold text-xs">
                        <th class="border-r border-white/20 py-3 px-3 w-16 text-center text-white">Entry</th>
                        <th class="border-r border-white/20 py-3 px-4 min-w-[200px] text-white">Categories</th>
                        <th class="border-r border-white/20 py-3 px-2 text-right text-white">Jan</th>
                        <th class="border-r border-white/20 py-3 px-2 text-right text-white">Feb</th>
                        <th class="border-r border-white/20 py-3 px-2 text-right text-white">Mar</th>
                        <th class="border-r border-white/20 py-3 px-2 text-right text-white">Apr</th>
                        <th class="border-r border-white/20 py-3 px-2 text-right text-white">May</th>
                        <th class="border-r border-white/20 py-3 px-2 text-right text-white">Jun</th>
                        <th class="border-r border-white/20 py-3 px-2 text-right text-white">Jul</th>
                        <th class="border-r border-white/20 py-3 px-2 text-right text-white">Aug</th>
                        <th class="border-r border-white/20 py-3 px-2 text-right text-white">Sep</th>
                        <th class="border-r border-white/20 py-3 px-2 text-right text-white">Oct</th>
                        <th class="border-r border-white/20 py-3 px-2 text-right text-white">Nov</th>
                        <th class="border-r border-white/20 py-3 px-2 text-right text-white">Dec</th>
                        <th class="py-3 px-4 text-right text-white bg-brand-600 font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                    <template x-for="cat in categories" :key="cat.code">
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2 px-3 text-center">
                                <button @click="openFormCapex(cat)" class="h-8 w-8 rounded-lg inline-flex items-center justify-center bg-brand-500 hover:bg-brand-600 text-white shadow-2xs transition active:scale-[0.98]">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                            </td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-4 font-medium text-gray-800 dark:text-gray-200" x-text="`${cat.code} - ${cat.name}`"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtNumber(catVal(cat, 'jan'))"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtNumber(catVal(cat, 'feb'))"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtNumber(catVal(cat, 'mar'))"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtNumber(catVal(cat, 'apr'))"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtNumber(catVal(cat, 'may'))"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtNumber(catVal(cat, 'jun'))"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtNumber(catVal(cat, 'jul'))"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtNumber(catVal(cat, 'aug'))"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtNumber(catVal(cat, 'sep'))"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtNumber(catVal(cat, 'oct'))"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtNumber(catVal(cat, 'nov'))"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmtNumber(catVal(cat, 'dec'))"></td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold text-gray-900 dark:text-white bg-gray-50/70 dark:bg-gray-800/50" x-text="fmtNumber(catTotal(cat))"></td>
                        </tr>
                    </template>
                    <tr x-show="categories.length === 0">
                        <td colspan="15" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
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
                <tfoot class="bg-gray-100 dark:bg-gray-800 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs">
                    <tr>
                        <td colspan="2" class="py-3 px-4 uppercase border-r border-gray-300 dark:border-gray-700">Total</td>
                        <template x-for="m in monthKeys" :key="'ft_' + m">
                            <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmtNumber(categories.reduce((s, c) => s + catVal(c, m), 0))"></td>
                        </template>
                        <td class="py-3 px-4 text-right font-mono text-brand-600 dark:text-brand-400" x-text="fmtNumber(categories.reduce((s, c) => s + catTotal(c), 0))"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>