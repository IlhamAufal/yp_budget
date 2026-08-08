<div class="space-y-6">

  <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    
    <div class="w-full max-w-xl" x-data="{ openCC: false, searchCC: '' }">
      <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-2">Cost Center</label>
      <div class="relative">
        <button @click="openCC = !openCC" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-left text-sm text-gray-700 shadow-sm hover:bg-gray-50 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
          <span x-text="costCenterList.find(c => c.id === selectedCostCenter)?.label || '- Pilih Cost Center -'"></span>
          <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div x-show="openCC" @click.outside="openCC = false" class="absolute z-30 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
          <div class="p-2">
            <input type="text" x-model="searchCC" placeholder="Cari Cost Center..." class="w-full rounded-md border border-gray-300 px-3 py-1.5 text-xs focus:border-brand-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
          </div>
          <ul class="max-h-60 overflow-auto py-1 text-sm text-gray-700 dark:text-gray-200">
            <template x-for="cc in costCenterList.filter(c => c.label.toLowerCase().includes(searchCC.toLowerCase()))" :key="cc.id">
              <li @click="selectedCostCenter = cc.id; openCC = false;" class="cursor-pointer px-4 py-2 hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-gray-700">
                <span x-text="cc.label"></span>
              </li>
            </template>
          </ul>
        </div>
      </div>
    </div>

    <div>
      <button 
        @click="exportDataExcel()"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export Data
      </button>
    </div>

  </div>

  <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm dark:border-gray-800">
    <table class="w-full text-left text-xs">
      <thead>
        <tr class="bg-gray-100 text-gray-700 uppercase dark:bg-gray-800 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 font-bold">
          <th rowspan="2" class="px-4 py-3 min-w-[240px] border-r">GENERAL ADMINISTRATIVE EXPENSE</th>
          <th colspan="10" class="px-4 py-2 text-center border-r bg-blue-50/60 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300">ACTUAL</th>
          <th rowspan="2" class="px-3 py-3 text-center border-r bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 min-w-[90px]">ASSUMPTION</th>
          <th colspan="13" class="px-4 py-2 text-center bg-green-50/60 text-green-800 dark:bg-green-950/40 dark:text-green-300">BUDGET</th>
        </tr>
        <tr class="bg-gray-50 text-gray-600 uppercase dark:bg-gray-800/80 dark:text-gray-400 border-b text-[10px] font-semibold">
          <template x-for="m in ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','AVG','TOTAL']" :key="'act_'+m">
            <th class="px-2 py-2 text-right min-w-[65px] border-r" x-text="m"></th>
          </template>
          <template x-for="m in ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC','TOTAL']" :key="'bud_'+m">
            <th class="px-2 py-2 text-right min-w-[65px] border-r" x-text="m"></th>
          </template>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-[11px]">
        
        <tr class="bg-gray-100/70 font-sans font-bold text-gray-900 dark:bg-gray-800 dark:text-white">
          <td class="px-4 py-2.5 border-r">➕ SUBTOTAL SALARIES</td>
          <template x-for="i in 10" :key="'act_sub_'+i">
            <td class="px-2 py-2.5 text-right border-r">0.00</td>
          </template>
          <td class="px-2 py-2.5 text-center border-r bg-amber-50/50 dark:bg-amber-950/20 font-sans">0</td>
          <template x-for="i in 13" :key="'bud_sub_'+i">
            <td class="px-2 py-2.5 text-right border-r">0.00</td>
          </template>
        </tr>

        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
          <td class="px-6 py-2 font-sans text-gray-700 dark:text-gray-300 border-r">↳ 6605011-Salaries</td>
          <template x-for="i in 10" :key="'act_d1_'+i"><td class="px-2 py-2 text-right border-r text-gray-500">0.00</td></template>
          <td class="px-2 py-2 text-center border-r bg-amber-50/30 dark:bg-amber-950/10 font-sans text-amber-700">0</td>
          <template x-for="i in 13" :key="'bud_d1_'+i"><td class="px-2 py-2 text-right border-r text-gray-500">0.00</td></template>
        </tr>

        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
          <td class="px-6 py-2 font-sans text-gray-700 dark:text-gray-300 border-r">↳ 6605012-Wages</td>
          <template x-for="i in 10" :key="'act_d2_'+i"><td class="px-2 py-2 text-right border-r text-gray-500">0.00</td></template>
          <td class="px-2 py-2 text-center border-r bg-amber-50/30 dark:bg-amber-950/10 font-sans text-amber-700">0</td>
          <template x-for="i in 13" :key="'bud_d2_'+i"><td class="px-2 py-2 text-right border-r text-gray-500">0.00</td></template>
        </tr>

      </tbody>
    </table>
  </div>

</div>