<div class="space-y-6">
    <div class="rounded-sm border border-stroke bg-gray-50 p-6 dark:border-strokedark dark:bg-meta-4">
        <label class="mb-3 block text-sm font-semibold text-black dark:text-white">Cost Center</label>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="w-full max-w-2xl">
                <select x-model="selectedDownloadCc"
                    class="w-full rounded border border-stroke bg-white px-4 py-2.5 text-sm font-medium outline-none transition focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                    <option value="">-- Pilih Cost Center --</option>
                    <template x-for="item in costCenters" :key="item.id">
                        <option :value="item.id" x-text="item.text"></option>
                    </template>
                </select>
            </div>
            <button @click="triggerDownload()"
                class="inline-flex items-center justify-center gap-2 rounded bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-opacity-90 transition">
                <i class="fas fa-download"></i> Download Template
            </button>
        </div>
    </div>
</div>
