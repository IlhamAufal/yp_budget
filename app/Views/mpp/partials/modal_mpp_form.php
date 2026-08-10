<div x-show="isModalOpen"
     x-cloak
     class="fixed inset-0 z-999999 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div @click.away="isModalOpen = false"
         class="w-full max-w-6xl rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark max-h-[90vh] flex flex-col overflow-hidden">

        <div class="flex items-center justify-between border-b border-stroke px-6 py-4 dark:border-strokedark bg-gray-2 dark:bg-meta-4">
            <h3 class="font-bold text-black dark:text-white flex items-center gap-2 text-sm">
                <i class="fas fa-cog text-primary"></i>
                <span>NEW HEADCOUNTS - <span x-text="`[${getCostCenterName()}] ${modalCategory}`"></span></span>
            </h3>
            <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-5">

            <!-- Loading State -->
            <div x-show="modalLoading" class="flex items-center justify-center py-12">
                <div class="flex items-center gap-3 text-gray-500">
                    <i class="fa-solid fa-spinner fa-spin text-primary text-xl"></i>
                    <span class="text-sm font-medium">Memuat data breakdown...</span>
                </div>
            </div>

            <!-- Form Table -->
            <div x-show="!modalLoading" class="max-w-full overflow-x-auto border border-stroke rounded-sm dark:border-strokedark">
                <table class="w-full table-fixed text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-2 dark:bg-meta-4 border-b border-stroke dark:border-strokedark">
                            <th class="py-2 px-3 text-left font-bold text-black dark:text-white w-[130px] border-r border-stroke dark:border-strokedark">DESCRIPTION</th>
                            <th colspan="12" class="py-1.5 text-center font-bold text-blue-800 dark:text-blue-300 bg-blue-50/60 dark:bg-blue-950/40 border-b border-r border-stroke dark:border-strokedark">NUMBER OF HEADCOUNTS</th>
                            <th class="py-2 px-2 text-center font-bold text-black dark:text-white w-[55px]">TOTAL</th>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-meta-4/80 text-gray-600 dark:text-gray-400 font-bold uppercase text-[10px] border-b border-stroke dark:border-strokedark">
                            <th class="py-1.5 px-3 border-r border-stroke dark:border-strokedark"></th>
                            <th class="py-1.5 px-1 text-center border-r border-stroke dark:border-strokedark">JAN</th>
                            <th class="py-1.5 px-1 text-center border-r border-stroke dark:border-strokedark">FEB</th>
                            <th class="py-1.5 px-1 text-center border-r border-stroke dark:border-strokedark">MAR</th>
                            <th class="py-1.5 px-1 text-center border-r border-stroke dark:border-strokedark">APR</th>
                            <th class="py-1.5 px-1 text-center border-r border-stroke dark:border-strokedark">MAY</th>
                            <th class="py-1.5 px-1 text-center border-r border-stroke dark:border-strokedark">JUN</th>
                            <th class="py-1.5 px-1 text-center border-r border-stroke dark:border-strokedark">JUL</th>
                            <th class="py-1.5 px-1 text-center border-r border-stroke dark:border-strokedark">AUG</th>
                            <th class="py-1.5 px-1 text-center border-r border-stroke dark:border-strokedark">SEP</th>
                            <th class="py-1.5 px-1 text-center border-r border-stroke dark:border-strokedark">OCT</th>
                            <th class="py-1.5 px-1 text-center border-r border-stroke dark:border-strokedark">NOV</th>
                            <th class="py-1.5 px-1 text-center border-r border-stroke dark:border-strokedark">DEC</th>
                            <th class="py-1.5 px-2 text-center"></th>
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
                                <td class="py-2 px-2 text-center font-bold text-black dark:text-white bg-gray-50 dark:bg-meta-4" x-text="pos.months.reduce((a, b) => a + b, 0)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Note -->
            <div x-show="!modalLoading" class="space-y-2">
                <label class="block text-sm font-semibold text-black dark:text-white">
                    Note (deskripsi kebutuhan) :
                </label>
                <textarea x-model="modalNote"
                          rows="3"
                          class="w-full rounded border border-stroke bg-white py-2.5 px-4 text-sm text-black focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-boxdark dark:text-white"
                          placeholder="Masukkan deskripsi kebutuhan headcount..."></textarea>
            </div>

            <!-- Save Button -->
            <div x-show="!modalLoading">
                <button type="button"
                        @click="saveMppBreakdown()"
                        :disabled="saving"
                        class="w-full inline-flex items-center justify-center gap-2 rounded py-3 px-6 text-center font-medium text-white transition shadow-md"
                        :class="saving ? 'bg-gray-400 cursor-not-allowed' : 'bg-success hover:bg-opacity-90'">
                    <i x-show="saving" class="fa-solid fa-spinner fa-spin"></i>
                    <i x-show="!saving" class="fas fa-save"></i>
                    <span x-text="saving ? 'Menyimpan...' : 'Save'"></span>
                </button>
            </div>

        </div>
    </div>
</div>
