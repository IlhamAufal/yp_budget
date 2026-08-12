<div class="rounded-sm border border-stroke bg-gray-50 p-6 dark:border-strokedark dark:bg-meta-4">
    <label for="downloadCostCenter" class="mb-2.5 block text-sm font-medium text-black dark:text-white">Cost Center Template</label>
    <div class="flex flex-col items-start gap-4 md:flex-row md:items-center">
        <select id="downloadCostCenter" x-model="selectedDownloadCc" @change="triggerDownloadTemplate()"
            class="w-full rounded border border-stroke bg-white px-4 py-2.5 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white md:w-1/2 lg:w-1/3">
            <option value="">-- Pilih Cost Center --</option>
            <option value="600">Domestic</option>
            <option value="700">Export</option>
        </select>
        <button type="button" @click="triggerDownloadTemplate()" :disabled="!selectedDownloadCc"
            class="inline-flex items-center gap-2 rounded bg-primary px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-opacity-90 disabled:opacity-50">
            <i class="fas fa-download"></i> Download Template Excel
        </button>
    </div>
    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Template berisi seluruh COA Selling dengan kolom MAIN ACCOUNT, DESCRIPTION, JAN–AUG, dan DEPT.</p>
</div>