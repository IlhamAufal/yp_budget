<div class="space-y-6">
  <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs max-w-2xl">
    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Pilih Cost Center Template</label>
    <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center">
      <select x-model="selectedTemplateCC" class="w-full sm:flex-1 rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2 text-xs font-medium outline-none transition focus:border-brand-500 focus:bg-white dark:border-gray-700 dark:bg-gray-800 dark:text-white">
        <option value="">- Pilih Cost Center -</option>
        <option value="ALL">[ALL] Semua Cost Center</option>
        <template x-for="cc in costCenterList" :key="cc.cost_center">
          <option :value="cc.cost_center" x-text="(cc.cc_code || cc.cost_center) + ' — ' + cc.cost_desc"></option>
        </template>
      </select>

      <button
        @click="downloadTemplate()"
        :disabled="!selectedTemplateCC"
        :class="selectedTemplateCC ? 'bg-brand-500 hover:bg-brand-600 text-white shadow-xs cursor-pointer active:scale-[0.98]' : 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600'"
        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition-all shrink-0">
        <i class="fa-solid fa-file-excel text-xs"></i>
        Download Template Excel
      </button>
    </div>
    <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">Template berisi struktur akun FOH sesuai Cost Center yang dipilih.</p>
  </div>
</div>
