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
            <h3 class="font-bold text-black dark:text-white flex items-center gap-2 text-sm sm:text-base">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>NEW HEADCOUNTS - <span x-text="`[${getCostCenterName()}] ${modalCategory}`"></span></span>
            </h3>
            <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-6">

            <!-- Loading State -->
            <div x-show="modalLoading" class="flex items-center justify-center py-12">
                <div class="flex items-center gap-3 text-gray-500">
                    <svg class="animate-spin h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-sm font-medium">Memuat data breakdown...</span>
                </div>
            </div>

            <!-- Form Table -->
            <div x-show="!modalLoading" class="max-w-full overflow-x-auto border border-stroke rounded-sm dark:border-strokedark">
                <table class="w-full table-auto text-sm">
                    <thead>
                        <tr class="bg-gray-2 text-gray-600 dark:bg-meta-4 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark text-center">
                            <th class="py-3 px-4 text-left min-w-[180px]">DESCRIPTION</th>
                            <th class="py-3 px-1 w-12">JAN</th>
                            <th class="py-3 px-1 w-12">FEB</th>
                            <th class="py-3 px-1 w-12">MAR</th>
                            <th class="py-3 px-1 w-12">APR</th>
                            <th class="py-3 px-1 w-12">MAY</th>
                            <th class="py-3 px-1 w-12">JUN</th>
                            <th class="py-3 px-1 w-12">JUL</th>
                            <th class="py-3 px-1 w-12">AUG</th>
                            <th class="py-3 px-1 w-12">SEP</th>
                            <th class="py-3 px-1 w-12">OCT</th>
                            <th class="py-3 px-1 w-12">NOV</th>
                            <th class="py-3 px-1 w-12">DEC</th>
                            <th class="py-3 px-2 w-16 bg-gray-3 dark:bg-meta-4">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-gray-5 dark:hover:bg-meta-4/50">
                            <td class="py-2.5 px-4 font-semibold text-black dark:text-white" x-text="modalCategory"></td>

                            <template x-for="(val, cIndex) in modalMonths" :key="cIndex">
                                <td class="py-2 px-1 text-center">
                                    <input type="number"
                                           min="0"
                                           :value="val"
                                           @input="autoFillRight(cIndex, $event.target.value)"
                                           class="w-full rounded border border-stroke bg-white py-1.5 px-1 text-center text-sm font-medium text-black focus:border-primary focus:outline-none dark:border-strokedark dark:bg-boxdark dark:text-white" />
                                </td>
                            </template>

                            <td class="py-2 px-2 text-center font-bold bg-gray-5 dark:bg-meta-4 text-black dark:text-white"
                                x-text="getModalTotal()">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div x-show="!modalLoading" class="space-y-2">
                <label class="block text-sm font-semibold text-black dark:text-white">
                    Note (deskripsi kebutuhan) :
                </label>
                <textarea x-model="modalNote"
                          rows="3"
                          class="w-full rounded border border-stroke bg-white py-2.5 px-4 text-sm text-black focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-boxdark dark:text-white"
                          placeholder="Masukkan deskripsi kebutuhan headcount..."></textarea>
            </div>

            <div x-show="!modalLoading">
                <button type="button"
                        @click="saveMppBreakdown()"
                        :disabled="saving"
                        class="w-full inline-flex items-center justify-center gap-2 rounded py-3 px-6 text-center font-medium text-white transition shadow-md"
                        :class="saving ? 'bg-gray-400 cursor-not-allowed' : 'bg-success hover:bg-opacity-90'">
                    <template x-if="saving">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                    <template x-if="!saving">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 002-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    </template>
                    <span x-text="saving ? 'Menyimpan...' : 'Save'"></span>
                </button>
            </div>

        </div>

    </div>
</div>
