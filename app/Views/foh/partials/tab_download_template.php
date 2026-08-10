<div class="max-w-xl space-y-4">
  <div>
    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Cost Center</label>
    <select x-model="selectedTemplateCC" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-colors">
      <option value="">- Pilih Cost Center -</option>
      <option value="ALL">[ALL] Semua Cost Center</option>
      <template x-for="cc in costCenterList" :key="cc.cost_center">
        <option :value="cc.cost_center" x-text="(cc.cc_code || cc.cost_center) + ' — ' + cc.cost_desc"></option>
      </template>
    </select>
  </div>

  <div>
    <button
      @click="downloadTemplate()"
      :disabled="!selectedTemplateCC"
      :class="selectedTemplateCC ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs' : 'bg-gray-200 text-gray-400 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600'"
      class="inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold transition-all">
      <i class="fa-solid fa-file-excel text-xs"></i>
      Download Template Excel
    </button>
  </div>
</div>
