<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upload Card -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-4">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">
                    <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                </span>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Upload Data International</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Unggah file Excel hasil pengisian template Sales International.</p>
                </div>
            </div>
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl p-8 text-center space-y-3">
                <i class="fa-solid fa-file-excel text-4xl text-emerald-500"></i>
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih file Excel untuk upload</p>
                <p class="text-[10px] text-gray-400">Format: .xlsx, .xls, .csv (Max 10MB)</p>
                <input type="file" x-ref="exportUploadFile" accept=".xlsx,.xls,.csv" class="hidden">
                <div class="flex items-center justify-center gap-2">
                    <button type="button" @click="$refs.exportUploadFile.click()" class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold rounded-xl">Pilih Berkas</button>
                    <button type="button" @click="uploadFile($refs.exportUploadFile)" class="px-4 py-2 bg-primary hover:bg-primary/90 text-white text-xs font-semibold rounded-xl">
                        <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Upload & Proses
                    </button>
                </div>
            </div>
        </div>

        <!-- Process Summary Card -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-4">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fa-solid fa-rotate text-lg"></i>
                </span>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Process Summary SKU International</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Agregasi ulang data transaksi ke Summary International.</p>
                </div>
            </div>
            <div class="p-4 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/50 text-xs text-amber-900 dark:text-amber-200">
                <p class="font-semibold"><i class="fa-solid fa-triangle-exclamation text-amber-600 mr-1"></i> Catatan:</p>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Proses ini akan mengonsolidasikan seluruh data detail negara ke ringkasan international.</p>
            </div>
            <button type="button" @click="processSummarySKU()" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2">
                <i class="fa-solid fa-gears"></i> Jalankan Process Summary SKU International
            </button>
        </div>
    </div>
</div>
