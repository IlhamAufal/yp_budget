<div class="space-y-6">

    <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex flex-col md:flex-row md:items-center gap-4">
            <label class="w-full md:w-32 text-sm font-semibold text-black dark:text-white">
                Cost Center
            </label>
            <div class="flex-1 flex gap-2">
                <select x-model="selectedCostCenter" class="w-full rounded border border-stroke bg-gray-5 py-2.5 px-4 text-sm text-black focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary">
                    <?php foreach (($costCenterList ?? []) as $idx => $cc): ?>
                        <option value="<?= esc($cc['cost_center']) ?>"><?= ($idx + 1) . '. [' . esc($cc['cost_center_sap'] ?? $cc['cost_center']) . ']' . esc($cc['cost_desc'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" @click="loadViewData()" class="inline-flex items-center justify-center rounded bg-primary px-4 py-2.5 text-white hover:bg-opacity-90 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-left text-sm">
                <thead>
                    <tr class="bg-gray-2 text-gray-500 font-bold dark:bg-meta-4 dark:text-gray-300 border-b border-stroke dark:border-strokedark">
                        <th class="py-3 px-4">CATEGORIES</th>
                        <th class="py-3 px-2 text-center w-14">JAN</th>
                        <th class="py-3 px-2 text-center w-14">FEB</th>
                        <th class="py-3 px-2 text-center w-14">MAR</th>
                        <th class="py-3 px-2 text-center w-14">APR</th>
                        <th class="py-3 px-2 text-center w-14">MAY</th>
                        <th class="py-3 px-2 text-center w-14">JUN</th>
                        <th class="py-3 px-2 text-center w-14">JUL</th>
                        <th class="py-3 px-2 text-center w-14">AUG</th>
                        <th class="py-3 px-2 text-center w-14">SEP</th>
                        <th class="py-3 px-2 text-center w-14">OCT</th>
                        <th class="py-3 px-2 text-center w-14">NOV</th>
                        <th class="py-3 px-2 text-center w-14">DEC</th>
                        <th class="py-3 px-2 text-center w-16 bg-gray-3 dark:bg-meta-4">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loading State -->
                    <template x-if="viewLoading">
                        <tr>
                            <td colspan="14" class="py-8 text-center text-gray-500">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Memuat data...
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!viewLoading && viewData.length === 0">
                        <tr>
                            <td colspan="14" class="py-8 text-center text-gray-500 italic">
                                Tidak ada data MPP untuk Cost Center ini.
                            </td>
                        </tr>
                    </template>

                    <!-- Data Rows -->
                    <template x-if="!viewLoading" x-for="(row, idx) in viewData" :key="row.tipe_id">
                        <tr class="border-b border-stroke dark:border-strokedark hover:bg-gray-5 dark:hover:bg-meta-4/50">
                            <td class="py-3 px-4 font-semibold text-black dark:text-white" x-text="row.tipe_name"></td>
                            <td class="py-3 px-2 text-center" x-text="fmtNum(row.m1)"></td>
                            <td class="py-3 px-2 text-center" x-text="fmtNum(row.m2)"></td>
                            <td class="py-3 px-2 text-center" x-text="fmtNum(row.m3)"></td>
                            <td class="py-3 px-2 text-center" x-text="fmtNum(row.m4)"></td>
                            <td class="py-3 px-2 text-center" x-text="fmtNum(row.m5)"></td>
                            <td class="py-3 px-2 text-center" x-text="fmtNum(row.m6)"></td>
                            <td class="py-3 px-2 text-center" x-text="fmtNum(row.m7)"></td>
                            <td class="py-3 px-2 text-center" x-text="fmtNum(row.m8)"></td>
                            <td class="py-3 px-2 text-center" x-text="fmtNum(row.m9)"></td>
                            <td class="py-3 px-2 text-center" x-text="fmtNum(row.m10)"></td>
                            <td class="py-3 px-2 text-center" x-text="fmtNum(row.m11)"></td>
                            <td class="py-3 px-2 text-center" x-text="fmtNum(row.m12)"></td>
                            <td class="py-3 px-2 text-center font-bold bg-gray-5 dark:bg-meta-4" x-text="fmtNum(row.grand_total)"></td>
                        </tr>
                    </template>

                    <!-- Grand Total Row -->
                    <template x-if="!viewLoading && viewData.length > 0">
                        <tr class="font-bold text-black dark:text-white bg-gray-50 dark:bg-meta-4/30">
                            <td class="py-3 px-4">TOTAL</td>
                            <template x-for="(val, i) in viewTotals" :key="'total-'+i">
                                <td class="py-3 px-2 text-center" x-text="fmtNum(val)"></td>
                            </template>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>
