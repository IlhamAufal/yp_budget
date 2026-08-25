<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs flex flex-col lg:flex-row lg:items-end justify-between gap-5">
        <div class="w-full max-w-lg">
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Cost Center</label>
            <select x-model="viewCostCenter" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs font-medium focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 outline-none transition">
                <template x-for="(cc, idx) in costCenters" :key="cc.id">
                    <option :value="cc.id" x-text="`${idx + 1}. ${cc.name}`"></option>
                </template>
            </select>
        </div>
        <div class="shrink-0">
            <button @click="exportExcel('View by Cost Center')" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98]">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export to Excel</span>
            </button>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Detail Pengajuan CAPEX per Cost Center</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar item belanja modal yang diajukan per unit kerja.</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1300px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th class="border-r border-white/20 py-3.5 px-4 min-w-[200px] text-white font-semibold">Description</th>
                        <th class="border-r border-white/20 py-3.5 px-3 text-white font-semibold">Account</th>
                        <th class="border-r border-white/20 py-3.5 px-3 text-white font-semibold">Cost Center</th>
                        <th class="border-r border-white/20 py-3.5 px-2.5 text-center text-white font-semibold">Unit</th>
                        <th class="border-r border-white/20 py-3.5 px-3 text-right text-white font-semibold">Unit Price</th>
                        <th class="border-r border-white/20 py-3.5 px-3 text-white font-semibold">Remarks</th>
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
                    <tr>
                        <td colspan="19" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-chart-column text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data CAPEX</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada data pengajuan CAPEX untuk cost center terpilih.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>