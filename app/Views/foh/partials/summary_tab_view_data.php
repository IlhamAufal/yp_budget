<div class="space-y-6">

  <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
      <div class="w-full max-w-xl">
        <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Cost Center</label>
        <div class="relative" @click.outside="openCC = false">
          <button
            @click="openCC = !openCC"
            type="button"
            class="flex w-full items-center justify-between rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs font-medium text-gray-900 dark:text-white outline-none focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:bg-white transition-all">
            <span x-text="(costCenterList.find(c => c.id === selectedCostCenter) || {}).label || '— Pilih Cost Center —'" class="truncate"></span>
            <i class="fa-solid fa-chevron-down text-[10px] text-gray-400"></i>
          </button>

          <div x-show="openCC" x-cloak
               class="absolute z-30 mt-1 max-h-72 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white p-1.5 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <template x-for="cc in costCenterList" :key="cc.id">
              <button
                @click="selectedCostCenter = cc.id; openCC = false; loadViewData()"
                class="block w-full px-3.5 py-2 text-left text-xs rounded-xl hover:bg-gray-100 hover:text-[#2F3185] dark:hover:bg-gray-700 hover:text-[#2F3185] dark:hover:text-[#2F3185] transition-colors"
                :class="selectedCostCenter === cc.id ? 'bg-[#2F3185]/10 text-[#2F3185] font-bold dark:bg-[#2F3185]/20' : 'text-gray-700 dark:text-gray-300'"
                x-text="cc.label">
              </button>
            </template>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-2.5 shrink-0">
        <button
          @click="loadViewData()"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs transition-all active:scale-[0.98] disabled:opacity-50"
          :disabled="viewDataLoading">
          <i class="fa-solid fa-magnifying-glass text-xs"></i>
          <span x-text="viewDataLoading ? 'Memuat…' : 'Tampilkan Data'"></span>
        </button>

        <button
          @click="exportViewData()"
          :disabled="!selectedCostCenter"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 text-xs font-semibold shadow-xs transition-all disabled:opacity-50 disabled:cursor-not-allowed active:scale-[0.98]">
          <i class="fa-solid fa-file-excel"></i>
          <span>Export Excel</span>
        </button>
      </div>
    </div>
  </div>

  <div class="space-y-4">
    <!-- Table Section Label (Separated from table container) -->
    <div>
      <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Data Konsolidasi Actual vs Budget FOH</h3>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rincian komparasi per akun dan subtotal per kelompok Cost Center.</p>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
      <div class="overflow-x-auto scrollbar-thin" style="max-height: 70vh;">
        <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300 border-collapse min-w-[1400px] whitespace-nowrap">
          <thead class="sticky top-0 z-10 bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
            <tr class="border-b border-white/20 bg-[#2F3185] text-white font-semibold text-xs">
              <th rowspan="2" class="border-r border-white/20 px-4 py-3 min-w-[260px] font-semibold text-white">Description</th>
              <th colspan="10" class="border-r border-white/20 px-4 py-2 text-center bg-[#25276d] text-white font-semibold">Actual</th>
              <th colspan="13" class="px-4 py-2 text-center bg-[#25276d] text-white font-semibold">Budget</th>
            </tr>
            <tr class="border-b border-white/20 bg-[#25276d] text-white text-xs font-semibold">
              <template x-for="c in actualCols" :key="c">
                <th class="border-r border-white/20 px-2.5 py-2 text-right min-w-[65px] text-white" x-text="c"></th>
              </template>
              <template x-for="(c, i) in budgetCols" :key="c">
                <th class="border-r border-white/20 px-2.5 py-2 text-right min-w-[65px] text-white"
                    x-text="'M' + (i + 1)"></th>
              </template>
              <th class="px-2.5 py-2 text-right min-w-[65px] text-white font-bold">Total</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs font-mono">
            <template x-if="viewDataLoading">
              <tr>
                <td colspan="25" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
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
                <td colspan="25" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
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
                <tr class="bg-gray-50/80 dark:bg-gray-800/60 font-bold text-gray-900 dark:text-white border-y border-gray-200 dark:border-gray-800">
                  <td class="border-r border-gray-200 dark:border-gray-800 px-4 py-2.5 font-sans">
                    <span class="text-[#2F3185] dark:text-indigo-400 mr-1.5">+</span> <span x-text="groupName || 'Other'"></span>
                  </td>
                  <template x-for="c in actualCols" :key="'as_' + c">
                    <td class="border-r border-gray-200 dark:border-gray-800 px-2.5 py-2.5 text-right" x-text="fmt(sumColumn(group, c))"></td>
                  </template>
                  <template x-for="c in budgetCols" :key="'bs_' + c">
                    <td class="border-r border-gray-200 dark:border-gray-800 px-2.5 py-2.5 text-right" x-text="fmt(sumColumn(group, c))"></td>
                  </template>
                  <td class="px-2.5 py-2.5 text-right font-bold text-[#2F3185] dark:text-indigo-400" x-text="fmt(sumColumn(group, 'isi_tot'))"></td>
                </tr>

                <template x-for="row in group" :key="row.main_account">
                  <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors border-b border-gray-200 dark:border-gray-800">
                    <td class="border-r border-gray-200 dark:border-gray-800 px-6 py-2 text-gray-800 dark:text-gray-200 font-sans">
                      <span class="text-gray-400 mr-1">↳</span> <span class="text-gray-900 dark:text-white font-medium" x-text="row.main_account + ' - ' + row.cost_center_desc"></span>
                    </td>
                    <template x-for="c in actualCols" :key="'ad_' + c">
                      <td class="border-r border-gray-200 dark:border-gray-800 px-2.5 py-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row[c])"></td>
                    </template>
                    <template x-for="c in budgetCols" :key="'bd_' + c">
                      <td class="border-r border-gray-200 dark:border-gray-800 px-2.5 py-2 text-right text-gray-600 dark:text-gray-300" x-text="fmt(row[c])"></td>
                    </template>
                    <td class="px-2.5 py-2 text-right text-gray-800 dark:text-gray-200 font-semibold" x-text="fmt(row.isi_tot)"></td>
                  </tr>
                </template>
              </tbody>
            </template>
          </tbody>

          <tfoot x-show="viewDataRows.length > 0">
            <tr class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs font-mono">
              <td class="border-r border-gray-300 dark:border-gray-700 px-4 py-2.5 font-sans font-bold">Grand Total</td>
              <template x-for="c in actualCols" :key="'at_' + c">
                <td class="border-r border-gray-300 dark:border-gray-700 px-2.5 py-2.5 text-right" x-text="fmt(sumColumn(viewDataRows, c))"></td>
              </template>
              <template x-for="c in budgetCols" :key="'bt_' + c">
                <td class="border-r border-gray-300 dark:border-gray-700 px-2.5 py-2.5 text-right" x-text="fmt(sumColumn(viewDataRows, c))"></td>
              </template>
              <td class="px-2.5 py-2.5 text-right text-[#2F3185] dark:text-indigo-400 font-bold" x-text="fmt(sumColumn(viewDataRows, 'isi_tot'))"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <p class="text-xs text-gray-400 dark:text-gray-500">Catatan: AVG = rata-rata Jan–Agu. Baris subtotal dikelompokkan per cost center utama.</p>
</div>
