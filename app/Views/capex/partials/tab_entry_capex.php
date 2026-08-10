<div class="mb-4 rounded-lg bg-sky-50 p-4 text-sm text-sky-800 dark:bg-sky-950 dark:text-sky-300 border border-sky-200 dark:border-sky-800 flex items-center justify-between">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
        <span><strong>Information !</strong> Periode submit data CAPEX dimulai pada 19 Jul 2026 s/d 31 Jul 2026</span>
    </div>
</div>

<div class="mb-6">
    <button @click="manualBookOpen = true" class="w-full flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 p-3 text-red-600 hover:bg-red-100 transition font-semibold text-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        TATA CARA PENGGUNAAN / MANUAL BOOK
    </button>
</div>

<div class="mb-6 bg-white dark:bg-boxdark p-4 rounded-xl shadow-xs border border-gray-100 dark:border-gray-800">
    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Cost Center</label>
    <select x-model="selectedCostCenter" class="w-full max-w-lg rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none">
        <template x-for="cc in costCenters" :key="cc.id">
            <option :value="cc.id" x-text="cc.name"></option>
        </template>
    </select>
</div>

<div class="rounded-xl border border-gray-200 bg-white shadow-xs dark:border-gray-800 dark:bg-boxdark overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-800">
                    <th class="p-3 w-12 text-center">ENTRY</th>
                    <th class="p-3">CATEGORIES</th>
                    <th class="p-3 text-right">JAN</th>
                    <th class="p-3 text-right">FEB</th>
                    <th class="p-3 text-right">MAR</th>
                    <th class="p-3 text-right">APR</th>
                    <th class="p-3 text-right">MAY</th>
                    <th class="p-3 text-right">JUN</th>
                    <th class="p-3 text-right">JUL</th>
                    <th class="p-3 text-right">AUG</th>
                    <th class="p-3 text-right">SEP</th>
                    <th class="p-3 text-right">OCT</th>
                    <th class="p-3 text-right">NOV</th>
                    <th class="p-3 text-right">DEC</th>
                    <th class="p-3 text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                <template x-for="cat in categories" :key="cat.code">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="p-2 text-center">
                            <button @click="openFormCapex(cat)" class="p-1.5 bg-brand-500 hover:bg-brand-600 text-white rounded shadow-xs transition inline-flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                        </td>
                        <td class="p-3 font-medium text-gray-800 dark:text-gray-200" x-text="`${cat.code} - ${cat.name}`"></td>
                        <td class="p-3 text-right">0</td>
                        <td class="p-3 text-right">0</td>
                        <td class="p-3 text-right">0</td>
                        <td class="p-3 text-right">0</td>
                        <td class="p-3 text-right">0</td>
                        <td class="p-3 text-right">0</td>
                        <td class="p-3 text-right">0</td>
                        <td class="p-3 text-right">0</td>
                        <td class="p-3 text-right">0</td>
                        <td class="p-3 text-right">0</td>
                        <td class="p-3 text-right">0</td>
                        <td class="p-3 text-right">0</td>
                        <td class="p-3 text-right font-bold">0</td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>