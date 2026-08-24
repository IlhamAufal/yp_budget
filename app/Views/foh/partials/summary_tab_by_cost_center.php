<div class="space-y-6">

  <div class="rounded-2xl border border-gray-200/80 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 shadow-xs flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h3 class="text-xs font-bold uppercase tracking-wider text-gray-800 dark:text-gray-200">Summary by Cost Center</h3>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total aktual vs budget existing per cost center (tahun berjalan)</p>
    </div>
    <button
      @click="loadSummaryCostCenter()"
      class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition-all active:scale-[0.98] disabled:opacity-50"
      :disabled="ccLoading">
      <i class="fa-solid fa-magnifying-glass text-xs"></i>
      <span x-text="ccLoading ? 'Memuat…' : 'Tampilkan Data'"></span>
    </button>
  </div>

  <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="overflow-x-auto" style="max-height: 70vh;">
      <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300 border-collapse min-w-[900px]">
        <thead class="bg-brand-500 text-white text-xs font-semibold">
          <tr class="border-b border-brand-600 bg-brand-500 text-white font-semibold text-xs">
            <th class="hidden"></th>
            <th class="border-r border-white/20 px-4 py-3 text-white font-semibold">Account</th>
            <th class="border-r border-white/20 px-4 py-3 text-white font-semibold">Cost Center</th>
            <th class="border-r border-white/20 px-4 py-3 text-white font-semibold">Description</th>
            <th class="border-r border-white/20 px-4 py-3 text-right text-white font-semibold">Actual</th>
            <th class="px-4 py-3 text-right text-white font-semibold">Budget Existing</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
          <template x-if="ccLoading">
            <tr>
              <td colspan="5" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-brand-500">
                    <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data...</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sedang memproses ringkasan per Cost Center.</p>
                </div>
              </td>
            </tr>
          </template>

          <template x-if="!ccLoading && costCenterRows.length === 0">
            <tr>
              <td colspan="5" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                    <i class="fa-solid fa-sitemap text-xl"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Tekan tombol Tampilkan Data untuk memuat ringkasan Cost Center.</p>
                </div>
              </td>
            </tr>
          </template>

          <template x-for="(group, jenisName) in groupBy(costCenterRows, 'jenis')" :key="'cc_' + jenisName">
            <tbody>
              <template x-for="row in group" :key="row.main_account">
                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors border-b border-gray-100 dark:border-gray-800">
                  <td class="hidden" x-text="(row.jenis || '').toUpperCase()"></td>
                  <td class="border-r border-gray-100 dark:border-gray-800 px-4 py-2 font-mono font-medium text-brand-600 dark:text-brand-400" x-text="row.main_account + '-' + row.cost_center_desc"></td>
                  <td class="border-r border-gray-100 dark:border-gray-800 px-4 py-2 text-gray-800 dark:text-gray-200 font-medium" x-text="(row.tipe || '') + ' ' + (row.cost_desc || '')"></td>
                  <td class="border-r border-gray-100 dark:border-gray-800 px-4 py-2 text-gray-600 dark:text-gray-300" x-text="row.cost_desc"></td>
                  <td class="border-r border-gray-100 dark:border-gray-800 px-4 py-2 text-right font-mono text-gray-700 dark:text-gray-300" x-text="fmt(row.total_actual)"></td>
                  <td class="px-4 py-2 text-right font-mono text-gray-700 dark:text-gray-300" x-text="fmt(row.total_budget)"></td>
                </tr>
              </template>

              <tr class="bg-gray-50 dark:bg-gray-800/60 font-bold text-gray-900 dark:text-white border-y border-gray-200 dark:border-gray-700">
                <td colspan="3" class="border-r border-gray-200 dark:border-gray-700 px-4 py-2.5">
                  <span class="text-xs uppercase tracking-wider" x-text="'Subtotal ' + (jenisName || 'Other').toUpperCase()"></span>
                </td>
                <td class="border-r border-gray-200 dark:border-gray-700 px-4 py-2.5 text-right font-mono" x-text="fmt(sumColumn(group, 'total_actual'))"></td>
                <td class="px-4 py-2.5 text-right font-mono" x-text="fmt(sumColumn(group, 'total_budget'))"></td>
              </tr>
            </tbody>
          </template>
        </tbody>

        <tfoot x-show="costCenterRows.length > 0">
          <tr class="bg-gray-100 dark:bg-gray-800 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs">
            <td colspan="3" class="border-r border-gray-300 dark:border-gray-700 px-4 py-2.5 uppercase">Grand Total</td>
            <td class="border-r border-gray-300 dark:border-gray-700 px-4 py-2.5 text-right font-mono" x-text="fmt(sumColumn(costCenterRows, 'total_actual'))"></td>
            <td class="px-4 py-2.5 text-right font-mono text-brand-600 dark:text-brand-400 font-bold" x-text="fmt(sumColumn(costCenterRows, 'total_budget'))"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

</div>
