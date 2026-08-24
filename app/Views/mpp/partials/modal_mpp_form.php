<div x-show="isModalOpen"
     x-cloak
     class="fixed inset-0 z-[9999999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div @click.away="isModalOpen = false"
         class="w-full max-w-6xl rounded-lg border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark max-h-[90vh] flex flex-col overflow-hidden">

        <div class="flex items-center justify-between border-b border-stroke px-6 py-4 dark:border-strokedark bg-gray-100 dark:bg-meta-4 shrink-0">
            <h3 class="font-bold text-black dark:text-white flex items-center gap-2 text-sm">
                <i class="fas fa-cog text-primary"></i>
                <span>NEW HEADCOUNTS - <span x-text="`[${getCostCenterName()}] ${modalCategory}`"></span></span>
            </h3>
            <button type="button" @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-5 flex-1">

            <div x-show="modalLoading" class="flex items-center justify-center py-12">
                <div class="flex items-center gap-3 text-gray-500">
                    <i class="fa-solid fa-spinner fa-spin text-primary text-xl"></i>
                    <span class="text-sm font-medium">Memuat data breakdown...</span>
                </div>
            </div>

            <div x-show="!modalLoading" class="max-w-full overflow-x-auto border border-stroke rounded-sm dark:border-strokedark">
                <table class="w-full table-fixed text-xs border-collapse">
                    <thead class="bg-brand-500 text-white font-semibold text-xs border-b border-brand-600">
                        <tr class="bg-brand-500 text-white font-semibold">
                            <th class="py-2.5 px-3 text-left font-semibold text-white w-[140px] border-r border-white/20">Description</th>
                            <th colspan="12" class="py-1.5 text-center font-semibold text-white bg-emerald-700/60 border-r border-white/20">Number of Headcounts</th>
                            <th class="py-2.5 px-2 text-center font-semibold text-white bg-brand-700 w-[60px] border-r border-white/20">Total</th>
                            <th rowspan="2" class="py-2.5 px-2 text-center font-semibold text-white w-[90px]">Action</th>
                        </tr>
                        <tr class="bg-brand-600 text-white font-semibold text-[10px]">
                            <th class="py-1.5 px-3 border-r border-white/20"></th>
                            <th class="py-1.5 px-1 text-center border-r border-white/20 text-white">Jan</th>
                            <th class="py-1.5 px-1 text-center border-r border-white/20 text-white">Feb</th>
                            <th class="py-1.5 px-1 text-center border-r border-white/20 text-white">Mar</th>
                            <th class="py-1.5 px-1 text-center border-r border-white/20 text-white">Apr</th>
                            <th class="py-1.5 px-1 text-center border-r border-white/20 text-white">May</th>
                            <th class="py-1.5 px-1 text-center border-r border-white/20 text-white">Jun</th>
                            <th class="py-1.5 px-1 text-center border-r border-white/20 text-white">Jul</th>
                            <th class="py-1.5 px-1 text-center border-r border-white/20 text-white">Aug</th>
                            <th class="py-1.5 px-1 text-center border-r border-white/20 text-white">Sep</th>
                            <th class="py-1.5 px-1 text-center border-r border-white/20 text-white">Oct</th>
                            <th class="py-1.5 px-1 text-center border-r border-white/20 text-white">Nov</th>
                            <th class="py-1.5 px-1 text-center border-r border-white/20 text-white">Dec</th>
                            <th class="py-1.5 px-2 text-center border-r border-white/20 bg-brand-700"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(pos, pIdx) in modalPositions" :key="pIdx">
                            <tr class="border-b border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4/30">
                                <td class="py-2 px-3 border-r border-stroke dark:border-strokedark font-semibold text-black dark:text-white text-xs truncate" x-text="pos.position_name"></td>
                                <template x-for="mIdx in 12" :key="'m_'+pIdx+'_'+mIdx">
                                    <td class="py-1.5 px-0.5 border-r border-stroke dark:border-strokedark">
                                        <input type="number" min="0"
                                            :value="pos.months[mIdx - 1]"
                                            @input="fillRight(pIdx, mIdx - 1, $event.target.value)"
                                            class="w-full rounded border border-stroke bg-white py-1 px-1 text-center text-xs font-medium text-black focus:border-primary focus:outline-none dark:border-strokedark dark:bg-boxdark dark:text-white" />
                                    </td>
                                </template>
                                <td class="py-2 px-2 text-center font-bold text-black dark:text-white bg-gray-50 dark:bg-meta-4" x-text="pos.months.reduce((a, b) => Number(a) + Number(b), 0)"></td>
                                <td class="py-2 px-2 text-center">
                                    <button type="button"
                                            x-show="pos.has_entry"
                                            @click.stop="deleteMppPosition(pos)"
                                            :disabled="deletingPositionId !== null"
                                            class="inline-flex items-center justify-center gap-1 rounded bg-red-600 px-2 py-1 text-[10px] font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                                            title="Hapus entry posisi">
                                        <i x-show="deletingPositionId === pos.id_mppx" class="fa-solid fa-spinner fa-spin"></i>
                                        <i x-show="deletingPositionId !== pos.id_mppx" class="fa-solid fa-trash"></i>
                                        <span x-text="deletingPositionId === pos.id_mppx ? 'Deleting...' : 'Delete'"></span>
                                    </button>
                                    <span x-show="!pos.has_entry" class="text-[10px] text-gray-400">-</span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="!modalLoading" class="space-y-2">
                <label class="block text-sm font-semibold text-black dark:text-white">
                    Note (deskripsi kebutuhan) :
                </label>
                <textarea x-model="modalNote"
                          rows="3"
                          class="w-full rounded-md border border-stroke bg-white py-2.5 px-4 text-sm text-black focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-boxdark dark:text-white"
                          placeholder="Masukkan deskripsi kebutuhan headcount..."></textarea>
            </div>

        </div>

        <div x-show="!modalLoading" class="flex items-center justify-end gap-3 border-t border-stroke px-6 py-3.5 dark:border-strokedark bg-gray-50 dark:bg-meta-4 shrink-0">
            <button type="button" 
                    @click="isModalOpen = false"
                    class="rounded-md border border-stroke py-2 px-5 text-sm font-medium text-black hover:bg-gray-100 transition dark:border-strokedark dark:text-white dark:hover:bg-boxdark-2">
                Batal
            </button>
            <button type="button"
                    @click="saveMppBreakdown()"
                    :disabled="saving"
                    class="inline-flex items-center justify-center gap-2 rounded-md py-2 px-6 text-sm font-semibold text-white shadow-md transition-all duration-200 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                    :class="saving ? 'bg-gray-400' : 'bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800'">
                <i x-show="saving" class="fa-solid fa-spinner fa-spin text-white"></i>
                <i x-show="!saving" class="fas fa-save text-white"></i>
                <span x-text="saving ? 'Menyimpan...' : 'Save'" class="text-white font-medium"></span>
            </button>
        </div>

    </div>
</div>