<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <label class="mb-2 block text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Cost Center</label>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="w-full max-w-2xl">
                <select x-model="selectedDownloadCc"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2 text-xs font-medium outline-none transition focus:border-brand-500 focus:bg-white dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">-- Pilih Cost Center --</option>
                    <template x-for="item in costCenters" :key="item.id">
                        <option :value="item.id" x-text="item.text"></option>
                    </template>
                </select>
            </div>
            <button @click="triggerDownload()"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-xs active:scale-[0.98] transition-all cursor-pointer">
                <i class="fa-solid fa-download"></i> Download Template
            </button>
        </div>
    </div>
</div>
