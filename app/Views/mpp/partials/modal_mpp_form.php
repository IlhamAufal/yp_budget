<template x-teleport="body">
<div x-show="isModalOpen"
     x-cloak
     class="fixed inset-0 z-[9999999] flex items-center justify-center p-4"
     style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(4px);"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div @click.away="isModalOpen = false"
         class="w-full max-w-6xl rounded-2xl border border-gray-200/80 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900 max-h-[90vh] flex flex-col overflow-hidden">

        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800 shrink-0">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white text-base">
                    Breakdown Headcounts: <span class="text-[#2F3185] dark:text-indigo-400" x-text="`[${getCostCenterName()}] ${modalCategory}`"></span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Penetapan jumlah personil per posisi untuk setiap bulan.</p>
            </div>
            <button type="button" @click="isModalOpen = false" class="h-8 w-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-5 flex-1">

            <div x-show="modalLoading" class="flex items-center justify-center py-12">
                <div class="flex items-center gap-3 text-[#2F3185]">
                    <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Memuat data breakdown...</span>
                </div>
            </div>

            <div x-show="!modalLoading" class="max-w-full overflow-x-auto rounded-2xl border border-gray-200/80 dark:border-gray-800 overflow-hidden shadow-xs">
                <table class="w-full text-xs border-collapse min-w-[1000px]">
                    <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                        <tr class="bg-[#2F3185] text-white font-semibold">
                            <th class="py-3 px-4 text-left font-semibold text-white min-w-[160px] border-r border-white/20">Description</th>
                            <th colspan="12" class="py-2 text-center font-semibold text-white bg-[#25276d] border-r border-white/20">Number of Headcounts</th>
                            <th class="py-3 px-3 text-center font-bold text-white bg-[#25276d] w-[70px] border-r border-white/20">Total</th>
                            <th rowspan="2" class="py-3 px-3 text-center font-semibold text-white w-[90px]">Aksi</th>
                        </tr>
                        <tr class="bg-[#25276d] text-white font-semibold text-[11px] border-b border-white/20">
                            <th class="py-2 px-3 border-r border-white/20"></th>
                            <th class="py-2 px-1 text-center border-r border-white/20 text-white font-semibold">Jan</th>
                            <th class="py-2 px-1 text-center border-r border-white/20 text-white font-semibold">Feb</th>
                            <th class="py-2 px-1 text-center border-r border-white/20 text-white font-semibold">Mar</th>
                            <th class="py-2 px-1 text-center border-r border-white/20 text-white font-semibold">Apr</th>
                            <th class="py-2 px-1 text-center border-r border-white/20 text-white font-semibold">May</th>
                            <th class="py-2 px-1 text-center border-r border-white/20 text-white font-semibold">Jun</th>
                            <th class="py-2 px-1 text-center border-r border-white/20 text-white font-semibold">Jul</th>
                            <th class="py-2 px-1 text-center border-r border-white/20 text-white font-semibold">Aug</th>
                            <th class="py-2 px-1 text-center border-r border-white/20 text-white font-semibold">Sep</th>
                            <th class="py-2 px-1 text-center border-r border-white/20 text-white font-semibold">Oct</th>
                            <th class="py-2 px-1 text-center border-r border-white/20 text-white font-semibold">Nov</th>
                            <th class="py-2 px-1 text-center border-r border-white/20 text-white font-semibold">Dec</th>
                            <th class="py-2 px-2 text-center border-r border-white/20 bg-[#25276d]"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                        <template x-for="(pos, pIdx) in modalPositions" :key="pIdx">
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-800 font-sans font-medium text-gray-900 dark:text-white text-xs truncate" x-text="pos.position_name"></td>
                                <template x-for="mIdx in 12" :key="'m_'+pIdx+'_'+mIdx">
                                    <td class="py-1.5 px-1 border-r border-gray-200 dark:border-gray-800">
                                        <input type="number" min="0"
                                            :value="pos.months[mIdx - 1]"
                                            @input="fillRight(pIdx, mIdx - 1, $event.target.value)"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 py-1 px-1 text-center text-xs font-medium text-gray-900 dark:text-white focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:outline-none" />
                                    </td>
                                </template>
                                <td class="py-2 px-2 text-center font-bold text-[#2F3185] dark:text-indigo-400 bg-gray-50/70 dark:bg-gray-800/50" x-text="pos.months.reduce((a, b) => Number(a) + Number(b), 0)"></td>
                                <td class="py-2 px-2 text-center font-sans">
                                    <button type="button"
                                            x-show="pos.has_entry"
                                            @click.stop="deleteMppPosition(pos)"
                                            :disabled="deletingPositionId !== null"
                                            class="inline-flex items-center justify-center gap-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 px-2.5 py-1 text-[11px] font-semibold transition disabled:cursor-not-allowed disabled:opacity-50"
                                            title="Hapus entry posisi">
                                        <i x-show="deletingPositionId === pos.id_mppx" class="fa-solid fa-spinner fa-spin"></i>
                                        <i x-show="deletingPositionId !== pos.id_mppx" class="fa-solid fa-trash-can"></i>
                                        <span x-text="deletingPositionId === pos.id_mppx ? 'Menghapus...' : 'Hapus'"></span>
                                    </button>
                                    <span x-show="!pos.has_entry" class="text-[11px] text-gray-400">-</span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="!modalLoading" class="space-y-1.5">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                    Catatan (Deskripsi Kebutuhan):
                </label>
                <textarea x-model="modalNote"
                          rows="3"
                          class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 py-2.5 px-4 text-xs text-gray-900 dark:text-white focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:outline-none"
                          placeholder="Masukkan deskripsi kebutuhan headcount..."></textarea>
            </div>

        </div>

        <div x-show="!modalLoading" class="flex items-center justify-end gap-2 border-t border-gray-100 dark:border-gray-800 px-6 py-4 shrink-0">
            <button type="button" 
                    @click="isModalOpen = false"
                    class="rounded-xl border border-gray-300 dark:border-gray-700 py-2.5 px-5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                Batal
            </button>
            <button type="button"
                    @click="saveMppBreakdown()"
                    :disabled="saving"
                    class="inline-flex items-center justify-center gap-2 rounded-xl py-2.5 px-6 text-xs font-semibold text-white shadow-xs transition-all duration-200 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50 active:scale-[0.98] bg-[#2F3185] hover:bg-[#25276d]">
                <i x-show="saving" class="fa-solid fa-spinner fa-spin text-white"></i>
                <i x-show="!saving" class="fa-solid fa-floppy-disk text-white"></i>
                <span x-text="saving ? 'Menyimpan...' : 'Simpan Data'" class="text-white font-semibold"></span>
            </button>
        </div>

    </div>
</div>
</template>