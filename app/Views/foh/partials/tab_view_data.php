<div class="space-y-6">

  <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

    <div class="w-full max-w-xl" x-data="{ openViewCC: false, searchViewCC: '' }">
      <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Cost Center</label>
      <div class="relative" style="z-index: 40;">
        <button @click="openViewCC = !openViewCC" type="button" class="w-full flex items-center justify-between rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-colors">
          <span x-text="viewDept ? costCenters.find(c => c.cost_center == viewDept)?.cc_code + ' — ' + costCenters.find(c => c.cost_center == viewDept)?.cost_desc : '— Pilih Cost Center —'"></span>
          <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
        </button>
        <div x-show="openViewCC" @click.outside="openViewCC = false" x-transition class="absolute z-30 mt-1 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg">
          <div class="p-2">
            <input type="text" x-model="searchViewCC" placeholder="Cari Cost Center..." class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:text-white">
          </div>
          <ul class="max-h-60 overflow-auto py-1 text-sm text-gray-700 dark:text-gray-200">
            <template x-for="cc in costCenters.filter(c => !searchViewCC || (c.cc_code + ' ' + c.cost_desc).toLowerCase().includes(searchViewCC.toLowerCase()))" :key="cc.cost_center">
              <li @click="viewDept = cc.cost_center; openViewCC = false; loadViewData()" class="cursor-pointer px-4 py-2.5 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 transition-colors">
                <span x-text="cc.cc_code + ' — ' + cc.cost_desc"></span>
              </li>
            </template>
          </ul>
        </div>
      </div>
    </div>

    <div class="sm:self-end">
      <button
        @click="exportData()"
        :disabled="!viewDept"
        class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition-colors"
        :class="viewDept ? 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs' : 'bg-gray-200 text-gray-400 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600'">
        <i class="fa-solid fa-file-excel text-xs"></i> Export Excel
      </button>
    </div>
  </div>

  <!-- TABLE: Actual vs Budget -->
  <template x-if="viewDept">
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse min-w-[1200px]">
          <thead>
            <tr class="bg-gray-100 text-gray-700 uppercase dark:bg-gray-800 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 font-bold">
              <th rowspan="2" class="px-4 py-3 min-w-[240px] border-r">GENERAL ADMINISTRATIVE EXPENSE</th>
              <th colspan="10" class="px-4 py-2 text-center border-r bg-blue-50/60 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300">ACTUAL</th>
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

            <template x-if="viewLoading">
              <tr>
                <td colspan="24" class="py-16 text-center text-gray-400 dark:text-gray-500 font-sans">
                  <i class="fa-solid fa-spinner fa-spin text-2xl mb-3"></i>
                  <p class="font-semibold text-gray-600 dark:text-gray-300">Memuat data...</p>
                </td>
              </tr>
            </template>

            <template x-if="!viewLoading && viewRows.length === 0">
              <tr>
                <td colspan="24" class="py-16 text-center text-gray-400 dark:text-gray-500 font-sans">
                  <i class="fa-solid fa-file-circle-question text-2xl mb-3"></i>
                  <p class="font-semibold text-gray-600 dark:text-gray-300">Belum ada data untuk Cost Center ini</p>
                </td>
              </tr>
            </template>

            <template x-for="(row, idx) in viewRows" :key="idx">
              <tbody>
                <!-- Subtotal Row -->
                <tr class="bg-gray-100/70 font-sans font-bold text-gray-900 dark:bg-gray-800 dark:text-white">
                  <td class="px-4 py-2.5 border-r">
                    <span class="text-green-600 mr-1">+</span> <span x-text="row.header_name"></span>
                  </td>
                  <!-- Actual: 8 months + AVG + TOTAL -->
                  <template x-for="i in 8" :key="'act_sub_'+idx+'_'+i">
                    <td class="px-2 py-2.5 text-right border-r" x-text="fmtShort(row.actual[i] || 0)"></td>
                  </template>
                  <td class="px-2 py-2.5 text-right border-r" x-text="fmtShort(avgActual(row.actual))"></td>
                  <td class="px-2 py-2.5 text-right border-r font-bold" x-text="fmtShort(row.actual_total)"></td>
                  <!-- Budget: 12 months + TOTAL -->
                  <template x-for="i in 12" :key="'bud_sub_'+idx+'_'+i">
                    <td class="px-2 py-2.5 text-right border-r" x-text="fmtShort(row.budget[i] || 0)"></td>
                  </template>
                  <td class="px-2 py-2.5 text-right border-r font-bold" x-text="fmtShort(row.budget_total)"></td>
                </tr>
                <!-- Detail Rows -->
                <template x-for="(item, iIdx) in row.items" :key="'item_'+idx+'_'+iIdx">
                  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="px-6 py-2 font-sans text-gray-700 dark:text-gray-300 border-r">
                      <span class="text-gray-400 mr-1">↳</span> <span x-text="item.acct_code"></span>-<span x-text="item.coa_name"></span>
                    </td>
                    <template x-for="i in 8" :key="'act_d_'+idx+'_'+iIdx+'_'+i">
                      <td class="px-2 py-2 text-right border-r text-gray-500" x-text="fmtShort(item.actual['a'+i] || 0)"></td>
                    </template>
                    <td class="px-2 py-2 text-right border-r text-gray-500" x-text="fmtShort(avgActual(item.actual))"></td>
                    <td class="px-2 py-2 text-right border-r text-gray-500 font-semibold" x-text="fmtShort(item.actual.atotal || 0)"></td>
                    <template x-for="i in 12" :key="'bud_d_'+idx+'_'+iIdx+'_'+i">
                      <td class="px-2 py-2 text-right border-r text-gray-500" x-text="fmtShort(item.budget['b'+i] || 0)"></td>
                    </template>
                    <td class="px-2 py-2 text-right border-r text-gray-500 font-semibold" x-text="fmtShort(item.budget.btotal || 0)"></td>
                  </tr>
                </template>
              </tbody>
            </template>

          </tbody>
        </table>
      </div>
    </div>
  </template>

  <template x-if="!viewDept">
    <div class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-gray-200 rounded-2xl dark:border-gray-800">
      <i class="fa-solid fa-chart-column text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
      <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Silakan pilih Cost Center untuk meninjau data konsolidasi View Data.</p>
    </div>
  </template>

</div>
