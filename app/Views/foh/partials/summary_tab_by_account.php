<div class="space-y-6">

  <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
    <div>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Summary by Account</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">Total budget existing per kode account (tahun berjalan)</p>
    </div>
    <button
      @click="loadSummaryAccount()"
      class="mt-2 inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 disabled:opacity-50"
      :disabled="accLoading">
      <i class="fa-solid fa-magnifying-glass"></i>
      <span x-text="accLoading ? 'Memuat…' : 'View Data'"></span>
    </button>
  </div>

  <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="bg-gray-50 dark:bg-gray-800">
          <th class="hidden"></th>
          <th class="border-r border-b border-gray-200 px-4 py-3 text-left font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-200">COST CENTER</th>
          <th class="border-r border-b border-gray-200 px-4 py-3 text-left font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-200">DESCRIPTION</th>
          <th class="border-b border-gray-200 px-4 py-3 text-right font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-200">BUDGET EXISTING</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
        <template x-if="accLoading">
          <tr><td colspan="4" class="px-4 py-10 text-center text-sm text-gray-400">Memuat data…</td></tr>
        </template>

        <template x-if="!accLoading && accountRows.length === 0">
          <tr><td colspan="4" class="px-4 py-10 text-center text-sm text-gray-400">Tekan tombol View Data untuk memuat ringkasan.</td></tr>
        </template>

        <template x-for="(group, jenisName) in groupBy(accountRows, 'jenis')" :key="'ac_' + jenisName">
          <template>
            <template x-for="row in group" :key="row.cost_center">
              <tr class="bg-white hover:bg-brand-50/40 dark:bg-gray-900 dark:hover:bg-gray-800/60">
                <td class="hidden" x-text="(row.jenis || '').toUpperCase()"></td>
                <td class="border-r border-b border-gray-200 px-4 py-2 font-medium text-brand-600 dark:border-gray-700 dark:text-brand-400" x-text="(row.tipe || '')"></td>
                <td class="border-r border-b border-gray-200 px-4 py-2 text-gray-600 dark:border-gray-700 dark:text-gray-300" x-text="row.cost_center_desc"></td>
                <td class="border-b border-gray-200 px-4 py-2 text-right tabular-nums text-gray-600 dark:border-gray-700 dark:text-gray-300" x-text="fmt(row.total)"></td>
              </tr>
            </template>

            <tr class="bg-gray-100/80 font-semibold text-gray-800 dark:bg-gray-800/80 dark:text-gray-100">
              <td colspan="3" class="border-r border-b border-gray-200 px-4 py-2.5 dark:border-gray-700">
                <span class="text-xs uppercase tracking-wider" x-text="'Subtotal ' + (jenisName || 'Other').toUpperCase()"></span>
              </td>
              <td class="border-b border-gray-200 px-4 py-2.5 text-right tabular-nums dark:border-gray-700" x-text="fmt(sumColumn(group, 'total'))"></td>
            </tr>
          </template>
        </template>
      </tbody>

      <tfoot x-if="accountRows.length">
        <tr class="bg-brand-50/80 font-bold text-gray-900 dark:bg-brand-900/20 dark:text-white">
          <td colspan="3" class="border-r border-gray-200 px-4 py-2.5 dark:border-gray-700">GRAND TOTAL</td>
          <td class="px-4 py-2.5 text-right tabular-nums" x-text="fmt(sumColumn(accountRows, 'total'))"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
