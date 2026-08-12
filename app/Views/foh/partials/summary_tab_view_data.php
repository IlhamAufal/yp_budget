<div class="space-y-6">

  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
      <div class="w-full sm:w-80">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Cost Center</label>

        <div class="relative" @click.outside="openCC = false">
          <button
            @click="openCC = !openCC"
            type="button"
            class="flex w-full items-center justify-between gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <span x-text="(costCenterList.find(c => c.id === selectedCostCenter) || {}).label || 'Pilih Cost Center…'" class="truncate"></span>
            <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <div x-show="openCC" x-cloak
               class="absolute z-30 mt-1 max-h-72 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white py-1 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <template x-for="cc in costCenterList" :key="cc.id">
              <button
                @click="selectedCostCenter = cc.id; openCC = false; loadViewData()"
                class="block w-full px-3 py-2 text-left text-sm hover:bg-brand-50 dark:hover:bg-brand-900/30"
                :class="selectedCostCenter === cc.id ? 'bg-brand-50 text-brand-700 dark:bg-brand-900/30 dark:text-brand-300' : 'text-gray-700 dark:text-gray-200'"
                x-text="cc.label">
              </button>
            </template>
          </div>
        </div>
      </div>

      <button
        @click="loadViewData()"
        class="mt-5 inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 disabled:opacity-50"
        :disabled="viewDataLoading">
        <i class="fa-solid fa-magnifying-glass"></i>
        <span x-text="viewDataLoading ? 'Memuat…' : 'Tampilkan Data'"></span>
      </button>
    </div>

    <button
      @click="exportViewData()"
      class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
      <i class="fa-solid fa-file-export"></i>
      Export
    </button>
  </div>

  <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="bg-gray-50 dark:bg-gray-800">
          <th rowspan="2" class="border-r border-b border-gray-200 px-4 py-3 text-left font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-200">DESCRIPTION</th>
          <th colspan="10" class="border-r border-b border-gray-200 px-4 py-3 text-center font-semibold text-brand-700 dark:border-gray-700 dark:text-brand-300">ACTUAL</th>
          <th colspan="13" class="border-b border-gray-200 px-4 py-3 text-center font-semibold text-brand-700 dark:border-gray-700 dark:text-brand-300">BUDGET</th>
        </tr>
        <tr class="bg-gray-50 dark:bg-gray-800">
          <template x-for="c in actualCols" :key="c">
            <th class="border-r border-b border-gray-200 px-2 py-2 text-right text-xs font-semibold uppercase text-gray-500 dark:border-gray-700 dark:text-gray-400" x-text="c"></th>
          </template>
          <template x-for="(c, i) in budgetCols" :key="c">
            <th class="border-r border-b border-gray-200 px-2 py-2 text-right text-xs font-semibold uppercase text-gray-500 dark:border-gray-700 dark:text-gray-400"
                x-text="'M' + (i + 1)"></th>
          </template>
          <th class="border-b border-gray-200 px-2 py-2 text-right text-xs font-semibold uppercase text-gray-500 dark:border-gray-700 dark:text-gray-400">TOTAL</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
        <template x-if="viewDataLoading">
          <tr>
            <td colspan="25" class="px-4 py-10 text-center text-sm text-gray-400">Memuat data…</td>
          </tr>
        </template>

        <template x-if="!viewDataLoading && viewDataRows.length === 0">
          <tr>
            <td colspan="25" class="px-4 py-10 text-center text-sm text-gray-400">Silakan pilih Cost Center untuk menampilkan data Actual vs Budget.</td>
          </tr>
        </template>

        <template x-for="(group, groupName) in groupBy(viewDataRows, 'cost_center_header')" :key="'g_' + groupName">
          <template>
            <tr class="bg-gray-100/80 font-semibold text-gray-800 dark:bg-gray-800/80 dark:text-gray-100">
              <td class="border-r border-b border-gray-200 px-4 py-2.5 dark:border-gray-700">
                <span class="text-xs uppercase tracking-wider" x-text="(groupName || 'Other').toUpperCase()"></span>
              </td>
              <template x-for="c in actualCols" :key="'as_' + c">
                <td class="border-r border-b border-gray-200 px-2 py-2.5 text-right tabular-nums dark:border-gray-700" x-text="fmt(sumColumn(group, c))"></td>
              </template>
              <template x-for="c in budgetCols" :key="'bs_' + c">
                <td class="border-r border-b border-gray-200 px-2 py-2.5 text-right tabular-nums dark:border-gray-700" x-text="fmt(sumColumn(group, c))"></td>
              </template>
              <td class="border-b border-gray-200 px-2 py-2.5 text-right tabular-nums dark:border-gray-700" x-text="fmt(sumColumn(group, 'isi_tot'))"></td>
            </tr>

            <template x-for="row in group" :key="row.main_account">
              <tr class="bg-white hover:bg-brand-50/40 dark:bg-gray-900 dark:hover:bg-gray-800/60">
                <td class="border-r border-b border-gray-200 px-6 py-2 text-gray-600 dark:border-gray-700 dark:text-gray-300">
                  <span class="text-brand-600 dark:text-brand-400" x-text="'↳ ' + row.main_account + '-' + row.cost_center_desc"></span>
                </td>
                <template x-for="c in actualCols" :key="'ad_' + c">
                  <td class="border-r border-b border-gray-200 px-2 py-2 text-right tabular-nums text-gray-600 dark:border-gray-700 dark:text-gray-300" x-text="fmt(row[c])"></td>
                </template>
                <template x-for="c in budgetCols" :key="'bd_' + c">
                  <td class="border-r border-b border-gray-200 px-2 py-2 text-right tabular-nums text-gray-600 dark:border-gray-700 dark:text-gray-300" x-text="fmt(row[c])"></td>
                </template>
                <td class="border-b border-gray-200 px-2 py-2 text-right tabular-nums text-gray-600 dark:border-gray-700 dark:text-gray-300" x-text="fmt(row.isi_tot)"></td>
              </tr>
            </template>
          </template>
        </template>
      </tbody>

      <tfoot x-if="viewDataRows.length">
        <tr class="bg-brand-50/80 font-bold text-gray-900 dark:bg-brand-900/20 dark:text-white">
          <td class="border-r border-gray-200 px-4 py-2.5 dark:border-gray-700">GRAND TOTAL</td>
          <template x-for="c in actualCols" :key="'at_' + c">
            <td class="border-r border-gray-200 px-2 py-2.5 text-right tabular-nums dark:border-gray-700" x-text="fmt(sumColumn(viewDataRows, c))"></td>
          </template>
          <template x-for="c in budgetCols" :key="'bt_' + c">
            <td class="border-r border-gray-200 px-2 py-2.5 text-right tabular-nums dark:border-gray-700" x-text="fmt(sumColumn(viewDataRows, c))"></td>
          </template>
          <td class="px-2 py-2.5 text-right tabular-nums" x-text="fmt(sumColumn(viewDataRows, 'isi_tot'))"></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <p class="text-xs text-gray-400 dark:text-gray-500">AVG = rata-rata Jan–Agu. Baris subtotal dikelompokkan per cost center utama.</p>
</div>
