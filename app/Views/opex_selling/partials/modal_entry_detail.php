<div 
    x-show="itemModalOpen" 
    class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
    style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(5px);"
    x-cloak
>
    <div 
        @click.outside="itemModalOpen = false" 
        class="w-full max-w-6xl rounded-lg bg-white shadow-2xl dark:bg-boxdark border border-stroke dark:border-strokedark overflow-hidden"
    >
        <div class="flex items-center justify-between border-b border-stroke px-6 py-4 dark:border-strokedark">
            <h3 class="text-base font-bold text-black dark:text-white uppercase tracking-wide flex items-center gap-2">
                <i class="fas fa-edit text-primary"></i> ENTRY DETIL OPEX SELLING
            </h3>
            <button @click="itemModalOpen = false" class="text-gray-400 hover:text-black dark:hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-6">
            <div class="mb-4 flex justify-between items-center">
                <button 
                    type="button" 
                    @click="openAddItemModal()" 
                    class="inline-flex items-center gap-1.5 rounded bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow hover:bg-emerald-700 transition-colors"
                >
                    <i class="fas fa-plus"></i> + Tambah Baris
                </button>
            </div>

            <div class="max-w-full overflow-x-auto border border-stroke dark:border-strokedark rounded-sm max-h-[400px]">
                <table class="w-full table-auto text-left text-xs">
                    <thead class="sticky top-0 bg-gray-2 text-black dark:bg-meta-4 dark:text-white font-bold z-10 border-b border-stroke dark:border-strokedark">
                        <tr>
                            <th class="py-2.5 px-2 min-w-[180px] border-r border-stroke dark:border-strokedark">DESKRIPSI RINCIAN</th>
                            <th class="py-2.5 px-1 text-center min-w-[65px] border-r border-stroke dark:border-strokedark">JAN</th>
                            <th class="py-2.5 px-1 text-center min-w-[65px] border-r border-stroke dark:border-strokedark">FEB</th>
                            <th class="py-2.5 px-1 text-center min-w-[65px] border-r border-stroke dark:border-strokedark">MAR</th>
                            <th class="py-2.5 px-1 text-center min-w-[65px] border-r border-stroke dark:border-strokedark">APR</th>
                            <th class="py-2.5 px-1 text-center min-w-[65px] border-r border-stroke dark:border-strokedark">MAY</th>
                            <th class="py-2.5 px-1 text-center min-w-[65px] border-r border-stroke dark:border-strokedark">JUN</th>
                            <th class="py-2.5 px-1 text-center min-w-[65px] border-r border-stroke dark:border-strokedark">JUL</th>
                            <th class="py-2.5 px-1 text-center min-w-[65px] border-r border-stroke dark:border-strokedark">AUG</th>
                            <th class="py-2.5 px-1 text-center min-w-[65px] border-r border-stroke dark:border-strokedark">SEP</th>
                            <th class="py-2.5 px-1 text-center min-w-[65px] border-r border-stroke dark:border-strokedark">OCT</th>
                            <th class="py-2.5 px-1 text-center min-w-[65px] border-r border-stroke dark:border-strokedark">NOV</th>
                            <th class="py-2.5 px-1 text-center min-w-[65px] border-r border-stroke dark:border-strokedark">DEC</th>
                            <th class="py-2.5 px-2 text-right min-w-[85px] border-r border-stroke dark:border-strokedark">TOTAL</th>
                            <th class="py-2.5 px-1 text-center min-w-[50px]">ACT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, idx) in itemList" :key="idx">
                            <tr class="border-b border-stroke dark:border-strokedark">
                                <td class="p-1 border-r border-stroke dark:border-strokedark">
                                    <input type="text" x-model="row.desc" placeholder="Deskripsi..." class="w-full rounded border border-stroke px-2 py-1 text-xs focus:border-primary focus:outline-none dark:border-strokedark dark:bg-boxdark dark:text-white">
                                </td>
                                <td class="p-1 border-r border-stroke dark:border-strokedark"><input type="number" step="any" x-model.number="row.jan" class="w-full text-right rounded border border-stroke px-1 py-1 text-xs dark:border-strokedark dark:bg-boxdark dark:text-white"></td>
                                <td class="p-1 border-r border-stroke dark:border-strokedark"><input type="number" step="any" x-model.number="row.feb" class="w-full text-right rounded border border-stroke px-1 py-1 text-xs dark:border-strokedark dark:bg-boxdark dark:text-white"></td>
                                <td class="p-1 border-r border-stroke dark:border-strokedark"><input type="number" step="any" x-model.number="row.mar" class="w-full text-right rounded border border-stroke px-1 py-1 text-xs dark:border-strokedark dark:bg-boxdark dark:text-white"></td>
                                <td class="p-1 border-r border-stroke dark:border-strokedark"><input type="number" step="any" x-model.number="row.apr" class="w-full text-right rounded border border-stroke px-1 py-1 text-xs dark:border-strokedark dark:bg-boxdark dark:text-white"></td>
                                <td class="p-1 border-r border-stroke dark:border-strokedark"><input type="number" step="any" x-model.number="row.may" class="w-full text-right rounded border border-stroke px-1 py-1 text-xs dark:border-strokedark dark:bg-boxdark dark:text-white"></td>
                                <td class="p-1 border-r border-stroke dark:border-strokedark"><input type="number" step="any" x-model.number="row.jun" class="w-full text-right rounded border border-stroke px-1 py-1 text-xs dark:border-strokedark dark:bg-boxdark dark:text-white"></td>
                                <td class="p-1 border-r border-stroke dark:border-strokedark"><input type="number" step="any" x-model.number="row.jul" class="w-full text-right rounded border border-stroke px-1 py-1 text-xs dark:border-strokedark dark:bg-boxdark dark:text-white"></td>
                                <td class="p-1 border-r border-stroke dark:border-strokedark"><input type="number" step="any" x-model.number="row.aug" class="w-full text-right rounded border border-stroke px-1 py-1 text-xs dark:border-strokedark dark:bg-boxdark dark:text-white"></td>
                                <td class="p-1 border-r border-stroke dark:border-strokedark"><input type="number" step="any" x-model.number="row.sep" class="w-full text-right rounded border border-stroke px-1 py-1 text-xs dark:border-strokedark dark:bg-boxdark dark:text-white"></td>
                                <td class="p-1 border-r border-stroke dark:border-strokedark"><input type="number" step="any" x-model.number="row.oct" class="w-full text-right rounded border border-stroke px-1 py-1 text-xs dark:border-strokedark dark:bg-boxdark dark:text-white"></td>
                                <td class="p-1 border-r border-stroke dark:border-strokedark"><input type="number" step="any" x-model.number="row.nov" class="w-full text-right rounded border border-stroke px-1 py-1 text-xs dark:border-strokedark dark:bg-boxdark dark:text-white"></td>
                                <td class="p-1 border-r border-stroke dark:border-strokedark"><input type="number" step="any" x-model.number="row.dec" class="w-full text-right rounded border border-stroke px-1 py-1 text-xs dark:border-strokedark dark:bg-boxdark dark:text-white"></td>
                                <td class="p-2 border-r border-stroke dark:border-strokedark text-right font-bold text-black dark:text-white" x-text="fmtShort(row)"></td>
                                <td class="p-1 text-center">
                                    <button 
                                        type="button" 
                                        @click="itemList.splice(idx, 1)" 
                                        class="text-danger hover:text-red-700 font-bold"
                                        title="Hapus Baris"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t border-stroke pt-4 dark:border-strokedark">
                <button 
                    type="button" 
                    @click="itemModalOpen = false" 
                    class="rounded border border-stroke px-5 py-2 text-xs font-semibold text-black hover:bg-gray-100 dark:border-strokedark dark:text-white dark:hover:bg-meta-4"
                >
                    Batal
                </button>
                <button 
                    type="button" 
                    @click="saveItemModal()" 
                    class="rounded bg-brand-500 hover:bg-brand-600 px-6 py-2 text-xs font-semibold text-white shadow transition-colors"
                >
                    <i class="fas fa-save mr-1"></i> Simpan Item
                </button>
            </div>
        </div>
    </div>
</div>
