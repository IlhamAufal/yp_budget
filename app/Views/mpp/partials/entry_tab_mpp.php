<div class="space-y-6">

    <div x-show="periodInfo" class="flex items-center gap-3 rounded-md border border-blue-200 bg-blue-50 p-4 text-blue-800 dark:border-blue-800/30 dark:bg-blue-900/20 dark:text-blue-300">
        <svg class="h-5 w-5 shrink-0 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <div class="text-sm font-medium">
            <span class="font-bold">Information !</span>
            <p class="mt-0.5" x-text="periodInfo"></p>
        </div>
    </div>

    <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex flex-col md:flex-row md:items-center gap-4">
            <label class="w-full md:w-32 text-sm font-semibold text-black dark:text-white">
                Cost Center
            </label>
            <div class="flex-1 flex gap-2">
                <select x-model="selectedCostCenter" class="w-full rounded border border-stroke bg-gray-5 py-2.5 px-4 text-sm text-black focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary">
                    <template x-for="(item, idx) in costCenterList" :key="item.cost_center">
                        <option :value="String(item.cost_center)" x-text="`${idx + 1}. [${item.cost_center_sap}]${item.cost_desc}`"></option>
                    </template>
                </select>
                <button type="button" @click="loadMatrixData()" class="inline-flex items-center justify-center rounded bg-primary px-4 py-2.5 text-white hover:bg-opacity-90 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-left text-sm">
                <thead>
                    <tr class="bg-gray-2 text-center font-bold text-black dark:bg-meta-4 dark:text-white border-b border-stroke dark:border-strokedark">
                        <th class="py-3 px-4 w-16">ENTRY</th>
                        <th class="py-3 px-4 text-left">CATEGORIES</th>
                        <th class="py-3 px-2 w-14">JAN</th>
                        <th class="py-3 px-2 w-14">FEB</th>
                        <th class="py-3 px-2 w-14">MAR</th>
                        <th class="py-3 px-2 w-14">APR</th>
                        <th class="py-3 px-2 w-14">MAY</th>
                        <th class="py-3 px-2 w-14">JUN</th>
                        <th class="py-3 px-2 w-14">JUL</th>
                        <th class="py-3 px-2 w-14">AUG</th>
                        <th class="py-3 px-2 w-14">SEP</th>
                        <th class="py-3 px-2 w-14">OCT</th>
                        <th class="py-3 px-2 w-14">NOV</th>
                        <th class="py-3 px-2 w-14">DEC</th>
                        <th class="py-3 px-2 w-16 bg-gray-3 dark:bg-meta-4">TOTAL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    <!-- Loading State -->
                    <template x-if="loading">
                        <tr>
                            <td colspan="15" class="py-8 text-center text-gray-500">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Memuat data...
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Data Rows -->
                    <template x-if="!loading && matrixData.length === 0">
                        <tr>
                            <td colspan="15" class="py-8 text-center text-gray-500 italic">
                                Tidak ada data untuk Cost Center ini.
                            </td>
                        </tr>
                    </template>

                    <template x-if="!loading" x-for="(row, idx) in matrixData" :key="row.tipe_id">
                        <tr class="hover:bg-gray-5 dark:hover:bg-meta-4/50">
                            <td class="py-3 px-4 text-center">
                                <button @click="openFormModal(row.tipe_name, row.tipe_id)"
                                        class="inline-flex items-center justify-center rounded bg-primary p-2 text-white hover:bg-opacity-90 transition"
                                        title="Edit Entry">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                            </td>
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
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-stroke text-xs text-gray-500 dark:border-strokedark">
            <span x-show="!loading">Showing <span x-text="matrixData.length"></span> of <span x-text="matrixData.length"></span> entries</span>
        </div>
    </div>

</div>
