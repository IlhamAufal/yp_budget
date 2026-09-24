<div x-show="formCapexOpen" 
     class="fixed inset-0 z-[9999999] flex items-center justify-center p-4" 
     style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(4px);"
     x-cloak>
    <div class="w-full max-w-7xl max-h-[90vh] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-gray-200 dark:border-gray-800"
         @click.self="formCapexOpen = false">
        
        <div class="px-6 py-4 border-b border-gray-200/80 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white text-base tracking-tight" x-text="`Form CAPEX - ${activeCategory.name}`"></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Input rincian belanja modal per periode.</p>
            </div>
            <button @click="formCapexOpen = false" class="h-8 w-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-4">
            <div>
                <button @click="addRow()" class="px-4 py-2 bg-[#2F3185] hover:bg-[#25276d] text-white rounded-xl transition inline-flex items-center gap-2 text-xs font-semibold shadow-xs active:scale-[0.98]">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Item</span>
                </button>
            </div>

            <div class="overflow-x-auto border border-gray-200/80 dark:border-gray-800 rounded-2xl overflow-hidden shadow-xs">
                <table class="w-full text-left text-xs border-collapse min-w-[1400px]">
                    <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                        <tr class="bg-[#2F3185] text-white font-semibold">
                            <th rowspan="2" class="p-3 w-10 text-center border-r border-white/20 text-white font-semibold">#</th>
                            <th rowspan="2" class="p-3 min-w-[180px] border-r border-white/20 text-white font-semibold">Description</th>
                            <th rowspan="2" class="p-3 min-w-[160px] border-r border-white/20 text-white font-semibold">Cost Center</th>
                            <th rowspan="2" class="p-3 min-w-[100px] border-r border-white/20 text-white font-semibold">New Lines</th>
                            <th rowspan="2" class="p-3 w-16 text-right border-r border-white/20 text-white font-semibold">Qty</th>
                            <th rowspan="2" class="p-3 min-w-[140px] text-right border-r border-white/20 text-white font-semibold">Unit Price (Mio)</th>
                            <th rowspan="2" class="p-3 min-w-[140px] border-r border-white/20 text-white font-semibold">Remarks</th>
                            <th colspan="12" class="p-2 text-center border-r border-white/20 text-white font-semibold bg-[#25276d]">Acquisition Period</th>
                            <th rowspan="2" class="p-3 w-24 text-right text-white bg-[#25276d] font-bold">Total</th>
                        </tr>
                        <tr class="bg-[#25276d] text-white font-semibold text-xs border-b border-white/20">
                            <th class="p-2 w-16 text-right border-r border-white/20 text-white">Jan</th>
                            <th class="p-2 w-16 text-right border-r border-white/20 text-white">Feb</th>
                            <th class="p-2 w-16 text-right border-r border-white/20 text-white">Mar</th>
                            <th class="p-2 w-16 text-right border-r border-white/20 text-white">Apr</th>
                            <th class="p-2 w-16 text-right border-r border-white/20 text-white">May</th>
                            <th class="p-2 w-16 text-right border-r border-white/20 text-white">Jun</th>
                            <th class="p-2 w-16 text-right border-r border-white/20 text-white">Jul</th>
                            <th class="p-2 w-16 text-right border-r border-white/20 text-white">Aug</th>
                            <th class="p-2 w-16 text-right border-r border-white/20 text-white">Sep</th>
                            <th class="p-2 w-16 text-right border-r border-white/20 text-white">Oct</th>
                            <th class="p-2 w-16 text-right border-r border-white/20 text-white">Nov</th>
                            <th class="p-2 w-16 text-right border-r border-white/20 text-white">Dec</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        <template x-for="(row, idx) in formRows" :key="idx">
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="p-2 text-center border-r border-gray-200 dark:border-gray-800">
                                    <button @click="removeRow(idx)" class="text-rose-500 hover:text-rose-700 transition">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="text" x-model="row.description" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-2 py-1.5 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800">
                                    <select x-model="row.costCenter" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-2 py-1.5 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white">
                                        <option value="">- Pilih -</option>
                                        <template x-for="cc in costCenters" :key="cc.id">
                                            <option :value="cc.id" x-text="cc.name"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800">
                                    <select x-model="row.newLines" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-2 py-1.5 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                </td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.qty" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-2 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.unitPrice" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-2 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="text" x-model="row.remarks" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-2 py-1.5 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.jan" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-1 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.feb" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-1 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.mar" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-1 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.apr" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-1 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.may" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-1 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.jun" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-1 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.jul" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-1 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.aug" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-1 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.sep" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-1 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.oct" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-1 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.nov" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-1 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-1 border-r border-gray-200 dark:border-gray-800"><input type="number" x-model.number="row.dec" class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 px-1 py-1.5 bg-white dark:bg-gray-900 text-xs font-mono text-gray-900 dark:text-white"></td>
                                <td class="p-2 text-right font-bold font-mono text-gray-900 dark:text-white" x-text="fmtNumber(rowTotal(row))"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold text-gray-900 dark:text-white border-t-2 border-[#2F3185]/30 text-xs font-mono">
                        <tr>
                            <td colspan="7" class="p-2.5 border-t border-r border-gray-300 dark:border-gray-700 font-sans">Total Acquisition</td>
                            <template x-for="m in ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec']" :key="'tl_'+m">
                                <td class="p-2.5 text-right border-t border-r border-gray-300 dark:border-gray-700" x-text="fmtNumber(columnTotal(m))"></td>
                            </template>
                            <td class="p-2.5 text-right border-t text-[#2F3185] dark:text-indigo-400 font-bold" x-text="fmtNumber(totalColumn())"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-200/80 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 flex justify-end gap-3">
            <button @click="formCapexOpen = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl text-xs transition">
                Batal
            </button>
            <button @click="saveFormCapex()" :disabled="saving" class="px-5 py-2.5 bg-[#2F3185] hover:bg-[#25276d] text-white font-semibold rounded-xl text-xs shadow-xs transition inline-flex items-center gap-2 active:scale-[0.98] disabled:opacity-50">
                <i x-show="!saving" class="fa-solid fa-floppy-disk"></i>
                <i x-show="saving" class="fa-solid fa-spinner fa-spin"></i>
                <span x-text="saving ? 'Menyimpan...' : 'Simpan Data CAPEX'"></span>
            </button>
        </div>
    </div>
</div>