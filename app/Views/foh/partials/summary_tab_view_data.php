<div class="space-y-6">

  <div class="rounded-2xl border border-gray-200/80 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
      <div class="w-full max-w-xl">
        <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Cost Center</label>
        <div class="relative" @click.outside="openCC = false">
          <button
            @click="openCC = !openCC"
            type="button"
            class="flex w-full items-center justify-between rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2 text-xs font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white outline-none focus:border-brand-500 focus:bg-white transition-all">
            <span x-text="(costCenterList.find(c => c.id === selectedCostCenter) || {}).label || '— Pilih Cost Center —'" class="truncate"></span>
            <i class="fa-solid fa-chevron-down text-[10px] text-gray-400"></i>
          </button>

          <div x-show="openCC" x-cloak
               class="absolute z-30 mt-1 max-h-72 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white p-1 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <template x-for="cc in costCenterList" :key="cc.id">
              <button
                @click="selectedCostCenter = cc.id; openCC = false; loadViewData()"
                class="block w-full px-3 py-2 text-left text-xs rounded-lg hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-500/10 dark:hover:text-brand-400 transition-colors"
                :class="selectedCostCenter === cc.id ? 'bg-brand-50 text-brand-600 font-bold dark:bg-brand-500/10 dark:text-brand-400' : 'text-gray-700 dark:text-gray-300'"
                x-text="cc.label">
              </button>
            </template>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="loadViewData()"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition-all active:scale-[0.98] disabled:opacity-50"
          :disabled="viewDataLoading">
          <i class="fa-solid fa-magnifying-glass text-xs"></i>
          <span x-text="viewDataLoading ? 'Memuat…' : 'Tampilkan Data'"></span>
        </button>

        <button
          @click="exportViewData()"
          :disabled="!selectedCostCenter"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-950/40 px-3.5 py-2 text-xs font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 shadow-xs transition-all disabled:opacity-50 disabled:cursor-not-allowed">
          <i class="fa-solid fa-file-excel text-xs"></i>
          Export Excel
        </button>
      </div>
    </div>
  </div>

  <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="overflow-x-auto" style="max-height: 70vh;">
      <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300 border-collapse min-w-[1200px]">
        <thead class="sticky top-0 z-10 bg-brand-500 text-white text-xs font-semibold">
          <tr class="border-b border-brand-600 bg-brand-500 text-white font-semibold text-xs">
            <th rowspan="2" class="border-r border-white/20 px-4 py-3 min-w-[240px] font-semibold text-white">Description</th>
            <th colspan="10" class="border-r border-white/20 px-4 py-2 text-center bg-emerald-700/60 text-white font-semibold">Actual</th>
            <th colspan="13" class="px-4 py-2 text-center bg-sky-700/60 text-white font-semibold">Budget</th>
          </tr>
          <tr class="border-b border-brand-600 bg-brand-600 text-white text-[10px] font-semibold">
            <template x-for="c in actualCols" :key="c">
              <th class="border-r border-white/20 px-2 py-2 text-right min-w-[60px] text-white" x-text="c"></th>
            </template>
            <template x-for="(c, i) in budgetCols" :key="c">
              <th class="border-r border-white/20 px-2 py-2 text-right min-w-[60px] text-white"
                  x-text="'M' + (i + 1)"></th>
            </template>
            <th class="px-2 py-2 text-right min-w-[60px] text-white">Total</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
          <template x-if="viewDataLoading">
            <tr>
              <td colspan="25" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-brand-500">
                    <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data...</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sistem sedang memproses data.</p>
                </div>
              </td>
            </tr>
          </template>

          <template x-if="!viewDataLoading && viewDataRows.length === 0">
            <tr>
              <td colspan="25" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                    <i class="fa-solid fa-chart-pie text-xl"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Silakan pilih Cost Center dan klik Tampilkan Data.</p>
                </div>
              </td>
            </tr>
          </template>

          <template x-for="(group, groupName) in groupBy(viewDataRows, 'cost_center_header')" :key="'g_' + groupName">
            <tbody>
              <tr class="bg-gray-50 dark:bg-gray-800/60 font-bold text-gray-900 dark:text-white border-y border-gray-200 dark:border-gray-700">
                <td class="border-r border-gray-200 dark:border-gray-700 px-4 py-2.5 uppercase">
                  <span x-text="(groupName || 'Other').toUpperCase()"></span>
                </td>
                <template x-for="c in actualCols" :key="'as_' + c">
                  <td class="border-r border-gray-200 dark:border-gray-700 px-2 py-2.5 text-right font-mono" x-text="fmt(sumColumn(group, c))"></td>
                </template>
                <template x-for="c in budgetCols" :key="'bs_' + c">
                  <td class="border-r border-gray-200 dark:border-gray-700 px-2 py-2.5 text-right font-mono" x-text="fmt(sumColumn(group, c))"></td>
                </template>
                <td class="px-2 py-2.5 text-right font-mono font-bold" x-text="fmt(sumColumn(group, 'isi_tot'))"></td>
              </tr>

              <template x-for="row in group" :key="row.main_account">
                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors border-b border-gray-100 dark:border-gray-800">
                  <td class="border-r border-gray-100 dark:border-gray-800 px-6 py-2 text-gray-800 dark:text-gray-200">
                    <span class="text-brand-600 dark:text-brand-400" x-text="'↳ ' + row.main_account + '-' + row.cost_center_desc"></span>
                  </td>
                  <template x-for="c in actualCols" :key="'ad_' + c">
                    <td class="border-r border-gray-100 dark:border-gray-800 px-2 py-2 text-right font-mono text-gray-600 dark:text-gray-300" x-text="fmt(row[c])"></td>
                  </template>
                  <template x-for="c in budgetCols" :key="'bd_' + c">
                    <td class="border-r border-gray-100 dark:border-gray-800 px-2 py-2 text-right font-mono text-gray-600 dark:text-gray-300" x-text="fmt(row[c])"></td>
                  </template>
                  <td class="px-2 py-2 text-right font-mono text-gray-800 dark:text-gray-200 font-semibold" x-text="fmt(row.isi_tot)"></td>
                </tr>
              </template>
            </tbody>
          </template>
        </tbody>

        <tfoot x-show="viewDataRows.length > 0">
          <tr class="bg-gray-100 dark:bg-gray-800 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs">
            <td class="border-r border-gray-300 dark:border-gray-700 px-4 py-2.5 uppercase">Grand Total</td>
            <template x-for="c in actualCols" :key="'at_' + c">
              <td class="border-r border-gray-300 dark:border-gray-700 px-2 py-2.5 text-right font-mono" x-text="fmt(sumColumn(viewDataRows, c))"></td>
            </template>
            <template x-for="c in budgetCols" :key="'bt_' + c">
              <td class="border-r border-gray-300 dark:border-gray-700 px-2 py-2.5 text-right font-mono" x-text="fmt(sumColumn(viewDataRows, c))"></td>
            </template>
            <td class="px-2 py-2.5 text-right font-mono text-brand-600 dark:text-brand-400 font-bold" x-text="fmt(sumColumn(viewDataRows, 'isi_tot'))"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <p class="text-xs text-gray-400 dark:text-gray-500">Catatan: AVG = rata-rata Jan–Agu. Baris subtotal dikelompokkan per cost center utama.</p>
</div>
