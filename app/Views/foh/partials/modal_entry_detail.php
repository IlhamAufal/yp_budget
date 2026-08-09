<div x-show="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" x-cloak>
  <div @click.outside="isModalOpen = false" class="w-full max-w-5xl rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800 space-y-4">
    
    <div class="flex items-center justify-between border-b pb-3 dark:border-gray-700">
      <h3 class="text-base font-bold text-brand-600 dark:text-brand-400" x-text="`Detail Entry Budget: ${activeRow ? activeRow.account_name : ''}`"></h3>
      <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
    </div>

    <div class="rounded-lg border border-blue-200 bg-blue-50/70 p-3 text-xs text-blue-700 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300">
      💡 <strong>Tip:</strong> copy 13 kolom (Detail Item + 12 bulan) dari Excel lalu paste di salah satu kolom untuk mengisi otomatis satu baris atau lebih.
    </div>

    <div class="overflow-x-auto max-h-96 rounded-lg border border-gray-200 dark:border-gray-700">
      <table class="w-full text-left text-xs">
        <thead class="bg-gray-50 uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-300">
          <tr>
            <th class="px-3 py-2 border-b min-w-[180px]">DETAIL ITEM</th>
            <template x-for="m in months" :key="m">
              <th class="px-2 py-2 border-b text-center min-w-[75px]" x-text="m"></th>
            </template>
            <th class="px-2 py-2 border-b text-center w-10">AKSI</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <template x-for="(item, i) in detailItems" :key="i">
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <td class="p-1.5">
                <input type="text" x-model="item.name" placeholder="Nama Detail Item..." class="w-full rounded border border-gray-300 px-2 py-1 text-xs focus:border-brand-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
              </td>
              <template x-for="m in monthKeys" :key="m">
                <td class="p-1.5">
                  <input type="number" step="0.01" x-model="item[m]" class="w-full text-right rounded border border-gray-300 px-1 py-1 text-xs focus:border-brand-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono">
                </td>
              </template>
              <td class="p-1.5 text-center">
                <button @click="removeItemRow(i)" class="text-red-500 hover:text-red-700 p-1" title="Hapus Baris">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div class="flex items-center justify-between pt-2">
      <button @click="addItemRow()" class="inline-flex items-center gap-1 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 hover:bg-green-100 transition-colors dark:bg-green-950/40 dark:text-green-400">
        + Tambah Item
      </button>
      <div class="flex gap-2">
        <button @click="isModalOpen = false" class="rounded-lg border border-gray-300 px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Batal</button>
        <button @click="saveDetailItems()" class="rounded-lg bg-brand-00 px-4 py-2 text-xs font-medium text-white shadow transition-colors">💾 Simpan Detail</button>
      </div>
    </div>

  </div>
</div>