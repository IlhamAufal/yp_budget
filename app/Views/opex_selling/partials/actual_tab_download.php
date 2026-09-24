<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <label for="downloadCostCenter" class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Cost Center Template</label>
        <div class="flex flex-col items-start gap-4 md:flex-row md:items-center">
            <select id="downloadCostCenter" x-model="selectedDownloadCc" @change="triggerDownloadTemplate()"
                class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs font-medium outline-none transition focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:bg-white dark:text-white md:w-1/2 lg:w-1/3">
                <option value="">-- Pilih Cost Center --</option>
                <option value="600">Domestic</option>
                <option value="700">Export</option>
            </select>
            <button type="button" @click="triggerDownloadTemplate()" :disabled="!selectedDownloadCc"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 text-xs font-semibold text-white shadow-xs active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                <i class="fa-solid fa-file-excel text-xs"></i>
                <span>Download Template Excel</span>
            </button>
        </div>
        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Template berisi seluruh COA Selling dengan kolom MAIN ACCOUNT, DESCRIPTION, JAN–AUG, dan DEPT.</p>
    </div>
</div>