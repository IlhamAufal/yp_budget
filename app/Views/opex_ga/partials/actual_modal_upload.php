<template x-teleport="body">
<div x-show="uploadModalOpen" class="fixed inset-0 z-[9999999] flex items-center justify-center p-4 bg-black/65 backdrop-blur-xs" x-cloak>
    <div class="fixed inset-0" @click="uploadModalOpen = false"></div>

    <div class="relative w-full max-w-xl rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 space-y-4">
        <div class="flex items-center justify-between border-b pb-3 dark:border-gray-800">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                    Upload Actual Budget <span class="text-[#2F3185] dark:text-indigo-400" x-text="uploadSourceTitle"></span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pilih atau letakkan berkas Excel sesuai format yang ditentukan.</p>
            </div>
            <button @click="uploadModalOpen = false" class="h-8 w-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="space-y-4">
            <div class="text-center">
                <label for="excel_file_input"
                    @dragover.prevent="isHovered = true"
                    @dragleave.prevent="isHovered = false"
                    @drop.prevent="isHovered = false; handleFileDrop($event)"
                    :class="isHovered ? 'border-[#2F3185] bg-[#2F3185]/5 dark:bg-[#2F3185]/10' : 'border-dashed border-gray-300 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30'"
                    class="flex min-h-[170px] cursor-pointer flex-col items-center justify-center rounded-2xl border-2 p-6 transition">
                    
                    <button type="button" @click="$refs.fileInput.click()"
                        class="mb-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 text-xs font-semibold text-white shadow-xs transition active:scale-[0.98]">
                        <i class="fa-solid fa-folder-open mr-1.5"></i> Pilih Berkas Excel
                    </button>
                    
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                        atau drag & drop berkas Excel (.xls / .xlsx) ke area ini
                    </p>

                    <template x-if="selectedFile">
                        <div class="mt-3 inline-flex items-center gap-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 px-3.5 py-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                            <i class="fa-solid fa-file-excel"></i>
                            <span x-text="selectedFile.name"></span>
                        </div>
                    </template>

                    <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept=".xls,.xlsx" class="hidden" id="excel_file_input" />
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 border-t pt-4 dark:border-gray-800">
            <button @click="uploadModalOpen = false" class="rounded-xl border border-gray-300 dark:border-gray-700 px-5 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                Batal
            </button>
            <button @click="submitUpload()" class="rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98]">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>Unggah File</span>
            </button>
        </div>
    </div>
</div>
</template>
