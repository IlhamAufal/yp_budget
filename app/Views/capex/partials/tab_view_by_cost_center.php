<div class="space-y-4">
    <div class="rounded-2xl border border-gray-200/80 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 shadow-xs flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div class="w-full max-w-lg">
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Cost Center</label>
            <select x-model="viewCostCenter" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3.5 py-2 text-xs font-medium focus:border-brand-500 focus:bg-white outline-none transition">
                <template x-for="(cc, idx) in costCenters" :key="cc.id">
                    <option :value="cc.id" x-text="`${idx + 1}. ${cc.name}`"></option>
                </template>
            </select>
        </div>
        <button @click="exportExcel('View by Cost Center')" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-xs font-bold transition shadow-xs">
            <i class="fa-solid fa-file-excel"></i>
            Export to Excel
        </button>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse text-gray-600 dark:text-gray-300 min-w-[1200px]">
                <thead class="bg-brand-500 text-white text-xs font-semibold">
                    <tr class="border-b border-brand-600 bg-brand-500 text-white font-semibold text-xs">
                        <th class="border-r border-white/20 py-3 px-4 min-w-[180px] text-white">Description</th>
                        <th class="border-r border-white/20 py-3 px-3 text-white">Account</th>
                        <th class="border-r border-white/20 py-3 px-3 text-white">Cost Center</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">Unit</th>
                        <th class="border-r border-white/20 py-3 px-3 text-right text-white">Unit Price</th>
                        <th class="border-r border-white/20 py-3 px-3 text-white">Remarks</th>
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
                    <tr>
                        <td colspan="19" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
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