<div class="space-y-6">

  <!-- FILTER: Cost Center -->
  <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-6">
    <div class="flex flex-wrap items-end gap-4">
      <div class="w-full max-w-xl">
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Cost Center (FOH)</label>
        <div class="relative" x-data="{ openCC: false, searchCC: '' }">
          <button @click="openCC = !openCC" type="button" class="w-full flex items-center justify-between rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-left text-xs font-medium text-gray-900 dark:text-white hover:bg-white dark:hover:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 outline-none transition-colors">
            <span x-text="selectedCostCenter ? (selectedCostCenter.cc_code || selectedCostCenter.cost_center) + ' — ' + selectedCostCenter.cost_desc : '— Semua Cost Center —'"></span>
            <i class="fa-solid fa-chevron-down text-[10px] text-gray-400"></i>
          </button>

          <div x-show="openCC" @click.outside="openCC = false" x-transition class="absolute z-30 mt-1 w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg overflow-hidden">
            <div class="p-2 border-b border-gray-100 dark:border-gray-700">
              <input type="text" x-model="searchCC" placeholder="Cari Cost Center..." class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700 px-3.5 py-2 text-xs focus:border-[#2F3185] focus:outline-none dark:text-white">
            </div>
            <ul class="max-h-60 overflow-auto py-1 text-xs text-gray-700 dark:text-gray-200 p-1">
              <li @click="selectedCostCenter = null; openCC = false; fetchActualData()" class="cursor-pointer px-3.5 py-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-[#2F3185] transition-colors">
                <span class="font-semibold">— Semua Cost Center —</span>
              </li>
              <template x-for="cc in filteredCostCenters(searchCC)" :key="cc.cost_center">
                <li @click="selectedCostCenter = cc; openCC = false; fetchActualData()" class="cursor-pointer px-3.5 py-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-[#2F3185] transition-colors">
                  <span x-text="(cc.cc_code || cc.cost_center) + ' — ' + cc.cost_desc"></span>
                </li>
              </template>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="space-y-4">
    <!-- Table Section Label (Separated from table container) -->
    <div>
      <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Tabel Data Realisasi Actual FOH</h3>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar realisasi biaya actual FOH per akun COA.</p>
    </div>

    <!-- TABLE -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
      <div class="overflow-x-auto scrollbar-thin">
        <table class="w-full text-left text-xs border-collapse min-w-[1100px] whitespace-nowrap">
          <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
            <tr class="border-b border-white/20 bg-[#2F3185] text-white font-semibold">
              <th class="border-r border-white/20 py-3 px-3 w-12 text-center text-white font-semibold">No.</th>
              <th class="border-r border-white/20 py-3 px-4 min-w-[140px] text-white font-semibold">Main Account</th>
              <th class="border-r border-white/20 py-3 px-4 min-w-[200px] text-white font-semibold">Description</th>
              <template x-for="m in months" :key="m">
                <th class="border-r border-white/20 py-3 px-2.5 text-right text-white font-semibold" x-text="m"></th>
              </template>
              <th class="py-3 px-4 text-right text-white bg-[#25276d] font-bold">Grand Total</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
            <template x-if="actualTableData.length === 0 && !loading">
              <tr>
                <td :colspan="months.length + 4" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                      <i class="fa-solid fa-chart-column text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200" x-text="selectedCostCenter ? 'Belum Ada Data Actual' : 'Silakan Pilih Cost Center'"></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedCostCenter ? 'Data akan muncul setelah proses upload actual FOH.' : 'Gunakan dropdown di atas untuk memilih Cost Center.'"></p>
                  </div>
                </td>
              </tr>
            </template>
            <template x-if="loading">
              <tr>
                <td :colspan="months.length + 4" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                      <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data Actual...</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sistem sedang memproses data actual FOH.</p>
                  </div>
                </td>
              </tr>
            </template>
            <template x-for="(row, idx) in actualTableData" :key="idx">
              <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-3 text-center text-gray-500 font-sans" x-text="(currentPage - 1) * perPage + idx + 1"></td>
                <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-4 font-mono font-bold text-gray-900 dark:text-white" x-text="row.main_account"></td>
                <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-4 font-sans font-medium text-gray-800 dark:text-gray-200" x-text="row.description || '-'"></td>
                <template x-for="(m, mIdx) in months" :key="m">
                  <td class="border-r border-gray-200 dark:border-gray-800 py-2.5 px-2.5 text-right font-mono" x-text="formatNumber(row[m.toLowerCase()] || row.monthly?.[mIdx + 1] || 0)"></td>
                </template>
                <td class="py-2.5 px-4 text-right font-mono font-bold text-gray-900 dark:text-white bg-gray-50/70 dark:bg-gray-800/50" x-text="formatNumber(row.grand_total)"></td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <template x-if="actualTableData.length > 0">
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 text-xs text-gray-500 dark:text-gray-400">
          <div>
            Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="((currentPage - 1) * perPage) + 1"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, total)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200" x-text="total"></span> data
          </div>
          <div class="flex items-center gap-1">
            <button @click="goPage(currentPage - 1)" :disabled="currentPage <= 1"
              class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 transition">
              <i class="fa-solid fa-chevron-left text-[10px]"></i>
            </button>
            <template x-for="p in totalPages" :key="'pg_'+p">
              <button @click="goPage(p)"
                :class="currentPage === p ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                class="h-8 min-w-[32px] px-2 rounded-lg text-xs font-semibold transition flex items-center justify-center"
                x-text="p"></button>
            </template>
            <button @click="goPage(currentPage + 1)" :disabled="currentPage >= totalPages"
              class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 transition">
              <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>
