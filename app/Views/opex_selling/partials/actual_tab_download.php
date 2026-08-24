<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <label for="downloadCostCenter" class="mb-2 block text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Cost Center Template</label>
        <div class="flex flex-col items-start gap-3 md:flex-row md:items-center">
            <select id="downloadCostCenter" x-model="selectedDownloadCc" @change="triggerDownloadTemplate()"
                class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2 text-xs font-medium outline-none transition focus:border-brand-500 focus:bg-white dark:border-gray-700 dark:bg-gray-800 dark:text-white md:w-1/2 lg:w-1/3">
                <option value="">-- Pilih Cost Center --</option>
                <option value="600">Domestic</option>
                <option value="700">Export</option>
            </select>
            <button type="button" @click="triggerDownloadTemplate()" :disabled="!selectedDownloadCc"
                class="inline-flex items-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-xs active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                <i class="fa-solid fa-download"></i> Download Template Excel
            </button>
        </div>
        <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">Template berisi seluruh COA Selling dengan kolom MAIN ACCOUNT, DESCRIPTION, JAN–AUG, dan DEPT.</p>
    </div>
</div>