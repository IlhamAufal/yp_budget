<div class="space-y-6">

  <!-- FILTER: Cost Center -->
  <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-5">
    <div class="flex flex-wrap items-end gap-4">
      <div class="flex-1 min-w-[260px]">
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Cost Center (FOH)</label>
        <div class="relative" x-data="{ openCC: false, searchCC: '' }">
          <button @click="openCC = !openCC" type="button" class="w-full flex items-center justify-between rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-colors">
            <span x-text="selectedCostCenter ? (selectedCostCenter.cc_code || selectedCostCenter.cost_center) + ' — ' + selectedCostCenter.cost_desc : '— Semua Cost Center —'"></span>
            <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
          </button>

          <div x-show="openCC" @click.outside="openCC = false" x-transition class="absolute z-30 mt-1 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg">
            <div class="p-2">
              <input type="text" x-model="searchCC" placeholder="Cari Cost Center..." class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:text-white">
            </div>
            <ul class="max-h-60 overflow-auto py-1 text-sm text-gray-700 dark:text-gray-200">
              <li @click="selectedCostCenter = null; openCC = false; fetchActualData()" class="cursor-pointer px-4 py-2.5 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 transition-colors">
                <span class="font-semibold">— Semua Cost Center —</span>
              </li>
              <template x-for="cc in filteredCostCenters(searchCC)" :key="cc.cost_center">
                <li @click="selectedCostCenter = cc; openCC = false; fetchActualData()" class="cursor-pointer px-4 py-2.5 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 transition-colors">
                  <span x-text="(cc.cc_code || cc.cost_center) + ' — ' + cc.cost_desc"></span>
                </li>
              </template>
            </ul>
          </div>
        </div>
      </div>
      <button
        type="button"
        @click="fetchActualData()"
        class="inline-flex items-center gap-2 rounded-xl bg-gray-900 dark:bg-brand-500 px-5 py-3 text-sm font-semibold text-white hover:bg-black dark:hover:bg-brand-600 transition-colors">
        <i class="fa-solid fa-rotate text-xs"></i> Muat Data
      </button>
    </div>
  </div>

  <!-- TABLE -->
  <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse min-w-[1000px]">
        <thead>
          <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200/80 dark:border-gray-800 text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <th class="py-4 px-4">NO.</th>
            <th class="py-4 px-4">MAIN ACCOUNT</th>
            <th class="py-4 px-4 min-w-[180px]">DESCRIPTION</th>
            <template x-for="m in months" :key="m">
              <th class="py-4 px-2 text-right" x-text="m"></th>
            </template>
            <th class="py-4 px-4 text-right bg-gray-100/70 dark:bg-gray-800">GRAND TOTAL</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <template x-if="actualTableData.length === 0 && !loading">
            <tr>
              <td :colspan="months.length + 3" class="py-16 text-center text-gray-400 dark:text-gray-500">
                <i class="fa-solid fa-file-circle-question text-2xl mb-3"></i>
                <p class="font-semibold text-gray-600 dark:text-gray-300" x-text="selectedCostCenter ? 'Belum ada data actual untuk Cost Center ini' : 'Silakan pilih Cost Center terlebih dahulu'"></p>
                <p class="text-sm" x-text="selectedCostCenter ? 'Data akan muncul setelah proses upload.' : 'Gunakan dropdown di atas untuk memilih Cost Center.'"></p>
              </td>
            </tr>
          </template>
          <template x-if="loading">
            <tr>
              <td :colspan="months.length + 3" class="py-16 text-center text-gray-400 dark:text-gray-500">
                <i class="fa-solid fa-spinner fa-spin text-2xl mb-3"></i>
                <p class="font-semibold text-gray-600 dark:text-gray-300">Memuat data...</p>
              </td>
            </tr>
          </template>
          <template x-for="(row, idx) in actualTableData" :key="idx">
            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
              <td class="py-2.5 px-4 text-gray-500" x-text="(currentPage - 1) * perPage + idx + 1"></td>
              <td class="py-2.5 px-4 font-mono font-semibold text-gray-900 dark:text-gray-100" x-text="row.main_account"></td>
              <td class="py-2.5 px-4 text-gray-700 dark:text-gray-300" x-text="row.description || '-'"></td>
              <template x-for="(m, mIdx) in months" :key="m">
                <td class="py-2.5 px-2 text-right font-mono" x-text="formatNumber(row[m.toLowerCase()] || row.monthly?.[mIdx + 1] || 0)"></td>
              </template>
              <td class="py-2.5 px-4 text-right font-mono font-bold text-brand-500 dark:text-brand-400 bg-gray-50/70 dark:bg-gray-800/50" x-text="formatNumber(row.grand_total)"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Pagination Footer -->
    <div
      x-show="total > 0"
      class="border-t border-gray-100 dark:border-gray-800 p-5 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400"
    >
      <span>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="total === 0 ? 0 : ((currentPage - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, total)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200" x-text="total"></span> data</span>
      <div class="flex items-center gap-2" x-show="totalPages > 1">
        <button type="button" @click="goPage(currentPage - 1)" :disabled="currentPage <= 1" class="h-9 px-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-sm font-semibold flex items-center gap-2"><i class="fa-solid fa-chevron-left text-xs"></i><span class="hidden sm:inline">Sebelumnya</span></button>
        <template x-for="p in totalPages" :key="p">
          <button type="button" @click="goPage(p)" :class="currentPage === p ? 'bg-brand-500 text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="h-9 min-w-[36px] px-2 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center" x-text="p"></button>
        </template>
        <button type="button" @click="goPage(currentPage + 1)" :disabled="currentPage >= totalPages" class="h-9 px-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-sm font-semibold flex items-center gap-2"><span class="hidden sm:inline">Berikutnya</span><i class="fa-solid fa-chevron-right text-xs"></i></button>
      </div>
    </div>
  </div>

</div>
