<div class="space-y-6">

  <div class="flex items-center justify-between rounded-xl border border-blue-200 bg-blue-50/70 p-4 text-sm text-blue-800 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300">
    <div class="flex items-center gap-3">
      <svg class="h-5 w-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <div>
        <span class="font-bold">Information !</span>
        <p class="text-xs mt-0.5">Periode submit data FOH dimulai pada <strong>01 Aug 2026 s/d 31 Aug 2026</strong></p>
      </div>
    </div>
  </div>

  <div class="max-w-xl">
    <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400 mb-2">Cost Center</label>
    <div class="relative" x-data="{ openCC: false, searchCC: '' }">
      <button @click="openCC = !openCC" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-left text-sm text-gray-700 shadow-sm hover:bg-gray-50 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
        <span x-text="selectedDept ? costCenters.find(c => c.code === selectedDept)?.label : '- Pilih Cost Center -'"></span>
        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
      </button>

      <div x-show="openCC" @click.outside="openCC = false" x-cloak class="absolute z-30 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="p-2">
          <input type="text" x-model="searchCC" placeholder="Cari Cost Center..." class="w-full rounded-md border border-gray-300 px-3 py-1.5 text-xs focus:border-brand-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
        </div>
        <ul class="max-h-60 overflow-auto py-1 text-sm text-gray-700 dark:text-gray-200">
          <template x-for="cc in filteredCostCenters(searchCC)" :key="cc.code">
            <li @click="selectedDept = cc.code; openCC = false; loadHeaderData()" class="cursor-pointer px-4 py-2 hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-gray-700">
              <span x-text="cc.label"></span>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </div>

  <template x-if="selectedDept">
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
      <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
        <thead class="bg-brand-500 text-white font-semibold text-xs border-b border-brand-600">
          <tr class="bg-brand-500 text-white font-semibold">
            <th class="px-3 py-3 border-r border-white/20 text-center w-12 text-white">Entry</th>
            <th class="px-4 py-3 border-r border-white/20 text-white">Expense Account</th>
            <template x-for="m in actualMonths" :key="m">
              <th class="px-3 py-3 border-r border-white/20 text-right text-white" x-text="m"></th>
            </template>
            <th class="px-4 py-3 text-right font-bold text-white bg-brand-600">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <template x-for="(row, idx) in headerRows" :key="idx">
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <td class="px-3 py-2 text-center">
                <a :href="`<?= base_url('foh/entry_budget_detail') ?>?header=${encodeURIComponent(row.name)}&dept=${selectedDept}&idx=${idx+1}`" 
                   class="inline-flex items-center justify-center rounded-lg bg-blue-600 p-1.5 text-white shadow hover:bg-blue-700 transition-colors">
                  <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 012 2h11a2 2 0 012-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </a>
              </td>
              <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white" x-text="row.name"></td>
              <template x-for="m in 8" :key="m">
                <td class="px-3 py-2.5 text-right font-mono" x-text="formatNumber(row.actual[m] || 0)"></td>
              </template>
              <td class="px-4 py-2.5 text-right font-mono font-bold text-brand-600 dark:text-brand-400" x-text="formatNumber(row.total)"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </template>

  <template x-if="!selectedDept">
    <div class="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed border-gray-200 rounded-xl dark:border-gray-800">
      <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
      <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Silakan pilih Cost Center untuk menampilkan data entry FOH.</p>
    </div>
  </template>

</div>