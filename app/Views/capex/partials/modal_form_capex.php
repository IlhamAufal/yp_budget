<div x-show="formCapexOpen" 
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4" 
     style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(4px);"
     x-cloak>
    <div class="w-full max-w-7xl max-h-[90vh] bg-white dark:bg-boxdark rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-gray-200 dark:border-gray-800"
         @click.self="formCapexOpen = false">
        
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
            <h3 class="font-bold text-gray-800 dark:text-white text-base" x-text="`FORM CAPEX - ${activeCategory.name}`"></h3>
            <button @click="formCapexOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-4">
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-600 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                TATA CARA PENGGUNAAN / MANUAL BOOK
            </div>

            <div>
                <button @click="addRow()" class="p-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg transition inline-flex items-center gap-1 text-xs font-semibold shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Item
                </button>
            </div>

            <div class="overflow-x-auto border border-gray-200 dark:border-gray-800 rounded-lg">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-800">
                            <th class="p-2 w-10 text-center">#</th>
                            <th class="p-2 min-w-[150px]">DESCRIPTION</th>
                            <th class="p-2 min-w-[150px]">COST CENTER</th>
                            <th class="p-2 min-w-[100px]">NEW LINES</th>
                            <th class="p-2 w-16 text-right">QTY</th>
                            <th class="p-2 min-w-[120px] text-right">UNIT PRICE (IN IDR MIO)</th>
                            <th class="p-2 min-w-[120px]">REMARKS</th>
                            <th class="p-2 w-16 text-right">JAN</th>
                            <th class="p-2 w-16 text-right">FEB</th>
                            <th class="p-2 w-16 text-right">MAR</th>
                            <th class="p-2 w-16 text-right">APR</th>
                            <th class="p-2 w-16 text-right">MAY</th>
                            <th class="p-2 w-16 text-right">JUN</th>
                            <th class="p-2 w-16 text-right">JUL</th>
                            <th class="p-2 w-16 text-right">AUG</th>
                            <th class="p-2 w-16 text-right">SEP</th>
                            <th class="p-2 w-16 text-right">OCT</th>
                            <th class="p-2 w-16 text-right">NOV</th>
                            <th class="p-2 w-16 text-right">DEC</th>
                            <th class="p-2 w-20 text-right">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        <template x-for="(row, idx) in formRows" :key="idx">
                            <tr>
                                <td class="p-2 text-center">
                                    <button @click="removeRow(idx)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                                <td class="p-1"><input type="text" x-model="row.description" class="w-full rounded border border-gray-300 dark:border-gray-700 px-2 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1">
                                    <select x-model="row.costCenter" class="w-full rounded border border-gray-300 dark:border-gray-700 px-2 py-1 bg-white dark:bg-gray-900">
                                        <option value="">- Pilih -</option>
                                        <template x-for="cc in costCenters" :key="cc.id">
                                            <option :value="cc.id" x-text="cc.name"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="p-1">
                                    <select x-model="row.newLines" class="w-full rounded border border-gray-300 dark:border-gray-700 px-2 py-1 bg-white dark:bg-gray-900">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                </td>
                                <td class="p-1"><input type="number" x-model.number="row.qty" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-2 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.unitPrice" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-2 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="text" x-model="row.remarks" class="w-full rounded border border-gray-300 dark:border-gray-700 px-2 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.jan" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-1 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.feb" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-1 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.mar" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-1 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.apr" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-1 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.may" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-1 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.jun" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-1 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.jul" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-1 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.aug" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-1 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.sep" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-1 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.oct" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-1 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.nov" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-1 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-1"><input type="number" x-model.number="row.dec" class="w-full text-right rounded border border-gray-300 dark:border-gray-700 px-1 py-1 bg-white dark:bg-gray-900"></td>
                                <td class="p-2 text-right font-bold">0.00</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 flex justify-end gap-2">
            <button @click="saveFormCapex()" class="px-5 py-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold rounded-lg text-xs shadow-xs transition">
                💾 Save
            </button>
        </div>
    </div>
</div>