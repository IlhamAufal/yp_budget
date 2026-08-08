<div class="space-y-6">

  <div class="flex items-center gap-3 bg-gray-50 p-4 rounded-xl border border-gray-200 dark:bg-gray-800 dark:border-gray-700 max-w-lg">
    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">View Data</label>
    <select x-model="selectedCCViewMode" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-700 shadow-sm focus:border-brand-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
      <template x-for="mode in ccViewModes" :key="mode">
        <option :value="mode" x-text="mode"></option>
      </template>
    </select>
    <button class="inline-flex items-center justify-center rounded-lg bg-brand-600 p-2 text-white hover:bg-brand-700 shadow-sm shrink-0">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </button>
  </div>

  <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
    <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
      <thead class="bg-gray-50 uppercase text-gray-700 dark:bg-gray-800 dark:text-gray-300 font-bold border-b border-gray-200 dark:border-gray-700">
        <tr>
          <th class="px-4 py-3">CATEGORY</th>
          <th class="px-4 py-3">ACCOUNT</th>
          <th class="px-4 py-3">COST CENTER</th>
          <th class="px-4 py-3">DESCRIPTION</th>
          <th class="px-4 py-3 text-right">ACTUAL</th>
          <th class="px-4 py-3 text-right">BUDGET EXISTING</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-[11px]">
        <template x-if="true">
          <tr>
            <td colspan="6" class="px-4 py-12 text-center text-gray-400 font-sans">
              Silakan tekan tombol cari untuk memuat data ringkasan per Cost Center.
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>

</div>