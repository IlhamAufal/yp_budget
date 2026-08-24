<div class="space-y-4">
    <div class="rounded-2xl border border-gray-200/80 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="w-full max-w-sm flex items-center gap-3">
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 shrink-0">Tampilkan :</label>
            <select x-model="filterShowAcq" @change="fetchData()" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3.5 py-2 text-xs font-medium focus:border-brand-500 focus:bg-white outline-none transition">
                <template x-for="opt in showOptions" :key="opt.id">
                    <option :value="opt.id" x-text="opt.name"></option>
                </template>
            </select>
        </div>
        
        <button @click="exportExcel('Summary Acquisition')" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-xs font-bold transition shadow-xs">
            <i class="fa-solid fa-file-excel"></i>
            Export to Excel
        </button>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto" style="max-height: 70vh;">
            <table class="w-full text-left text-xs border-collapse text-gray-600 dark:text-gray-300 min-w-[1000px]">
                <thead class="sticky top-0 z-10 bg-brand-500 text-white text-xs font-semibold">
                    <tr class="border-b border-brand-600 bg-brand-500 text-white font-semibold text-xs">
                        <th class="border-r border-white/20 py-3 px-4 min-w-[220px] text-white">Jenis Asset (Acquisition)</th>
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
                    <template x-if="acqData.length === 0">
                        <tr>
                            <td colspan="14" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-arrow-up-right-dots text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Akuisisi</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada data akuisisi aset untuk filter ini.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(row, idx) in acqData" :key="idx">
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-4 font-medium text-gray-800 dark:text-gray-200" x-text="`${row.category_code} - ${row.category_name}`"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmt(row.jan)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmt(row.feb)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmt(row.mar)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmt(row.apr)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmt(row.may)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmt(row.jun)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmt(row.jul)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmt(row.aug)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmt(row.sep)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmt(row.oct)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmt(row.nov)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-right font-mono" x-text="fmt(row.dec)"></td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold text-gray-900 dark:text-white bg-gray-50/70 dark:bg-gray-800/50" x-text="fmt(row.total)"></td>
                        </tr>
                    </template>
                </tbody>
                <tfoot x-show="acqData.length > 0" class="bg-gray-100 dark:bg-gray-800 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs">
                    <tr>
                        <td class="py-3 px-4 uppercase border-r border-gray-300 dark:border-gray-700">Total Acquisition</td>
                        <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmt(acqTotals.jan)"></td>
                        <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmt(acqTotals.feb)"></td>
                        <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmt(acqTotals.mar)"></td>
                        <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmt(acqTotals.apr)"></td>
                        <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmt(acqTotals.may)"></td>
                        <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmt(acqTotals.jun)"></td>
                        <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmt(acqTotals.jul)"></td>
                        <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmt(acqTotals.aug)"></td>
                        <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmt(acqTotals.sep)"></td>
                        <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmt(acqTotals.oct)"></td>
                        <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmt(acqTotals.nov)"></td>
                        <td class="py-3 px-2 text-right font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmt(acqTotals.dec)"></td>
                        <td class="py-3 px-4 text-right font-mono text-brand-600 dark:text-brand-400 font-bold" x-text="fmt(acqTotals.total)"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
