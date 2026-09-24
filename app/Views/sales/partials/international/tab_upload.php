<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upload Card -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-5">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Upload Data Sales International</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unggah file Excel hasil pengisian template Sales International.</p>
            </div>
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl p-8 text-center hover:border-emerald-500 transition-colors bg-gray-50/50 dark:bg-gray-800/40 space-y-4">
                <div class="flex h-12 w-12 mx-auto items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fa-solid fa-file-excel text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih file Excel / CSV untuk upload</p>
                    <p class="text-xs text-gray-400 mt-1">Format didukung: .xlsx, .xls, .csv (Maksimal 10MB)</p>
                </div>
                <input type="file" x-ref="exportUploadFile" accept=".xlsx,.xls,.csv" class="hidden">
                <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                    <button type="button" @click="$refs.exportUploadFile.click()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-2 active:scale-[0.98]">
                        <i class="fa-solid fa-folder-open"></i>
                        <span>Pilih Berkas Excel</span>
                    </button>
                    <button type="button" @click="uploadFile($refs.exportUploadFile)" class="px-5 py-2.5 bg-[#2F3185] hover:bg-[#25276d] text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-2 active:scale-[0.98]">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload & Proses</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Process Summary Card -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs space-y-5 flex flex-col justify-between">
            <div class="space-y-5">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Process Summary SKU International</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Agregasi ulang data transaksi ke Summary International.</p>
                </div>
                <div class="p-4 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/50 text-xs text-amber-900 dark:text-amber-200 space-y-2">
                    <p class="font-semibold flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Catatan Proses:
                    </p>
                    <p class="text-gray-600 dark:text-gray-300">
                        Proses ini akan mengonsolidasikan seluruh data transaksi detail negara ke ringkasan international untuk tahun aktif <strong><?= esc($workingYear) ?></strong>.
                    </p>
                </div>
            </div>
            <div>
                <button type="button" @click="processSummarySKU()" class="px-5 py-2.5 bg-[#2F3185] hover:bg-[#25276d] text-white text-xs font-semibold rounded-xl shadow-xs transition-all inline-flex items-center justify-center gap-2 active:scale-[0.98]">
                    <i class="fa-solid fa-gears"></i>
                    <span>Jalankan Process Summary SKU</span>
                </button>
            </div>
        </div>
    </div>
</div>
