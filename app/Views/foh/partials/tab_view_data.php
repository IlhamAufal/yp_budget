<div class="space-y-6">

<div class="space-y-6">

  <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs flex flex-col lg:flex-row lg:items-end justify-between gap-5">
    <div class="w-full max-w-xl" x-data="{ openViewCC: false, searchViewCC: '' }">
      <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Cost Center</label>
      <div class="relative" style="z-index: 40;">
        <button @click="openViewCC = !openViewCC" type="button" class="w-full flex items-center justify-between rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-left text-xs font-medium text-gray-900 dark:text-white hover:bg-white dark:hover:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 outline-none transition-colors">
          <span x-text="viewDept ? costCenters.find(c => c.cost_center == viewDept)?.cc_code + ' — ' + costCenters.find(c => c.cost_center == viewDept)?.cost_desc : '— Pilih Cost Center —'"></span>
          <i class="fa-solid fa-chevron-down text-[10px] text-gray-400"></i>
        </button>
        <div x-show="openViewCC" @click.outside="openViewCC = false" x-transition class="absolute z-30 mt-1 w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg overflow-hidden">
          <div class="p-2 border-b border-gray-100 dark:border-gray-700">
            <input type="text" x-model="searchViewCC" placeholder="Cari Cost Center..." class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700 px-3.5 py-2 text-xs focus:border-[#2F3185] focus:outline-none dark:text-white">
          </div>
          <ul class="max-h-60 overflow-auto py-1 text-xs text-gray-700 dark:text-gray-200 p-1">
            <template x-for="cc in costCenters.filter(c => !searchViewCC || (c.cc_code + ' ' + c.cost_desc).toLowerCase().includes(searchViewCC.toLowerCase()))" :key="cc.cost_center">
              <li @click="viewDept = cc.cost_center; openViewCC = false; loadViewData()" class="cursor-pointer px-3.5 py-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-[#2F3185] transition-colors">
                <span x-text="cc.cc_code + ' — ' + cc.cost_desc"></span>
              </li>
            </template>
          </ul>
        </div>
      </div>
    </div>

    <div class="shrink-0">
      <button
        @click="exportData()"
        :disabled="!viewDept"
        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
        <i class="fa-solid fa-file-excel"></i>
        <span>Export Excel</span>
      </button>
    </div>
  </div>

  <!-- TABLE: Actual vs Budget -->
  <template x-if="viewDept">
    <div class="space-y-4">
      <!-- Table Section Label (Separated from table container) -->
      <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Konsolidasi Actual vs Budget FOH (12 Bulan)</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbandingan realisasi biaya actual dengan anggaran budget yang direncanakan.</p>
      </div>

      <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin" style="max-height: 70vh;">
          <table class="w-full text-left text-xs border-collapse min-w-[1400px] whitespace-nowrap">
            <thead class="sticky top-0 z-10 bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
              <tr class="bg-[#2F3185] text-white border-b border-white/20 font-semibold text-xs">
                <th rowspan="2" class="px-4 py-3 min-w-[260px] border-r border-white/20 font-semibold text-white">General Administrative Expense</th>
                <th colspan="10" class="px-4 py-2 text-center border-r border-white/20 bg-[#25276d] text-white font-semibold">Actual</th>
                <th colspan="13" class="px-4 py-2 text-center bg-[#25276d] text-white font-semibold">Budget</th>
              </tr>
              <tr class="bg-[#25276d] text-white border-b border-white/20 text-xs font-semibold">
                <template x-for="m in ['jan','feb','mar','apr','may','jun','jul','aug']" :key="'actual_' + m">
                  <th class="px-2 py-2 text-right border-r border-white/20 text-white" x-text="m"></th>
                </template>
                <th class="px-2 py-2 text-right border-r border-white/20 text-white">Avg</th>
                <th class="px-2 py-2 text-right border-r border-white/20 text-white font-bold">Total</th>
                <template x-for="m in ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec']" :key="'budget_' + m">
                  <th class="px-2 py-2 text-right border-r border-white/20 text-white" x-text="m"></th>
                </template>
                <th class="px-2 py-2 text-right text-white font-bold">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs font-mono">

              <template x-if="viewLoading">
                <tr>
                  <td colspan="24" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                      <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                        <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                      </div>
                      <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data...</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sistem sedang mengkonsolidasikan data FOH.</p>
                    </div>
                  </td>
                </tr>
              </template>

              <template x-if="!viewLoading && viewRows.length === 0">
                <tr>
                  <td colspan="24" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                      <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                        <i class="fa-solid fa-folder-open text-xl"></i>
                      </div>
                      <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada rincian data budget untuk Cost Center ini.</p>
                    </div>
                  </td>
                </tr>
              </template>

              <template x-for="(row, idx) in viewRows" :key="idx">
                <tbody>
                  <!-- Subtotal Row -->
                  <tr class="bg-gray-50/80 dark:bg-gray-800/60 font-bold text-gray-900 dark:text-white border-y border-gray-200 dark:border-gray-800">
                    <td class="px-4 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans">
                      <span class="text-[#2F3185] dark:text-indigo-400 mr-1.5">+</span> <span x-text="row.header_name"></span>
                    </td>
                    <!-- Actual: 8 months + AVG + TOTAL -->
                    <template x-for="i in 8" :key="'act_sub_'+idx+'_'+i">
                      <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmtShort(row.actual[i] || 0)"></td>
                    </template>
                    <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400" x-text="fmtShort(avgActual(row.actual))"></td>
                    <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800 font-bold text-emerald-600 dark:text-emerald-400" x-text="fmtShort(row.actual_total)"></td>
                    <!-- Budget: 12 months + TOTAL -->
                    <template x-for="i in 12" :key="'bud_sub_'+idx+'_'+i">
                      <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmtShort(row.budget[i] || 0)"></td>
                    </template>
                    <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800 font-bold text-[#2F3185] dark:text-indigo-400" x-text="fmtShort(row.budget_total)"></td>
                  </tr>
                  <!-- Detail Rows -->
                  <template x-for="(item, iIdx) in row.items" :key="'item_'+idx+'_'+iIdx">
                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors border-b border-gray-200 dark:border-gray-800">
                      <td class="px-6 py-2 text-gray-800 dark:text-gray-200 border-r border-gray-200 dark:border-gray-800 font-sans">
                        <span class="text-gray-400 mr-1">↳</span> <span x-text="item.acct_code"></span> - <span x-text="item.coa_name"></span>
                      </td>
                      <template x-for="i in 8" :key="'act_d_'+idx+'_'+iIdx+'_'+i">
                        <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300" x-text="fmtShort(item.actual['a'+i] || 0)"></td>
                      </template>
                      <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300" x-text="fmtShort(avgActual(item.actual))"></td>
                      <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-gray-800 dark:text-gray-200 font-semibold" x-text="fmtShort(item.actual.atotal || 0)"></td>
                      <template x-for="i in 12" :key="'bud_d_'+idx+'_'+iIdx+'_'+i">
                        <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300" x-text="fmtShort(item.budget['b'+i] || 0)"></td>
                      </template>
                      <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-gray-800 dark:text-gray-200 font-semibold" x-text="fmtShort(item.budget.btotal || 0)"></td>
                    </tr>
                  </template>
                </tbody>
              </template>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </template>

  <template x-if="!viewDept">
    <div class="rounded-2xl border border-gray-200/80 bg-white p-12 dark:border-gray-800 dark:bg-gray-900 text-center shadow-xs">
      <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
          <i class="fa-solid fa-chart-column text-xl"></i>
        </div>
        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Pilih Cost Center Terlebih Dahulu</p>
        <p class="text-xs text-gray-500 dark:text-gray-400">Silakan pilih Cost Center pada dropdown di atas untuk meninjau data konsolidasi View Data.</p>
      </div>
    </div>
  </template>

</div>
