<div class="space-y-4">

    <div class="rounded-2xl border border-gray-200/80 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Cost Center</label>
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <select x-model="selectedCostCenter" class="w-full max-w-lg rounded-xl border border-gray-200 bg-gray-50/50 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3.5 py-2 text-xs font-medium focus:border-brand-500 focus:bg-white outline-none transition">
                <?php foreach (($costCenterList ?? []) as $idx => $cc): ?>
                    <option value="<?= esc($cc['cost_center']) ?>"><?= ($idx + 1) . '. [' . esc($cc['cost_center_sap'] ?? $cc['cost_center']) . ']' . esc($cc['cost_desc'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" @click="loadViewData()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition active:scale-[0.98]">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Tampilkan
            </button>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse text-gray-600 dark:text-gray-300 min-w-[900px]">
                <thead class="bg-brand-500 text-white text-xs font-semibold">
                    <tr class="border-b border-brand-600 bg-brand-500 text-white font-semibold text-xs">
                        <th class="border-r border-white/20 py-3 px-4 min-w-[200px] text-white">Categories</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">Jan</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">Feb</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">Mar</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">Apr</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">May</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">Jun</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">Jul</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">Aug</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">Sep</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">Oct</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">Nov</th>
                        <th class="border-r border-white/20 py-3 px-2 text-center text-white">Dec</th>
                        <th class="py-3 px-4 text-center text-white bg-brand-600 font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                    <!-- Loading State -->
                    <template x-if="viewLoading">
                        <tr>
                            <td colspan="14" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-brand-500">
                                        <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data MPP...</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sistem sedang memproses data.</p>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!viewLoading && viewData.length === 0">
                        <tr>
                            <td colspan="14" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-users text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data MPP</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada perencanaan tenaga kerja untuk Cost Center ini.</p>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Data Rows -->
                    <template x-if="!viewLoading" x-for="(row, idx) in viewData" :key="row.tipe_id">
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-4 font-semibold text-gray-900 dark:text-white" x-text="row.tipe_name"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-center font-mono" x-text="fmtNum(row.m1)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-center font-mono" x-text="fmtNum(row.m2)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-center font-mono" x-text="fmtNum(row.m3)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-center font-mono" x-text="fmtNum(row.m4)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-center font-mono" x-text="fmtNum(row.m5)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-center font-mono" x-text="fmtNum(row.m6)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-center font-mono" x-text="fmtNum(row.m7)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-center font-mono" x-text="fmtNum(row.m8)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-center font-mono" x-text="fmtNum(row.m9)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-center font-mono" x-text="fmtNum(row.m10)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-center font-mono" x-text="fmtNum(row.m11)"></td>
                            <td class="border-r border-gray-100 dark:border-gray-800 py-2.5 px-2 text-center font-mono" x-text="fmtNum(row.m12)"></td>
                            <td class="py-2.5 px-4 text-center font-mono font-bold text-gray-900 dark:text-white bg-gray-50/70 dark:bg-gray-800/50" x-text="fmtNum(row.grand_total)"></td>
                        </tr>
                    </template>
                </tbody>

                <!-- Grand Total Row -->
                <tfoot x-show="!viewLoading && viewData.length > 0" class="bg-gray-100 dark:bg-gray-800 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs">
                    <tr>
                        <td class="py-3 px-4 uppercase border-r border-gray-300 dark:border-gray-700">Total</td>
                        <template x-for="(val, i) in viewTotals" :key="'total-'+i">
                            <td class="py-3 px-2 text-center font-mono border-r border-gray-300 dark:border-gray-700" x-text="fmtNum(val)"></td>
                        </template>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
