<template x-teleport="body">
<div x-show="uploadModalOpen" x-cloak class="fixed inset-0 z-[9999999] flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(4px);" @click.self="uploadModalOpen = false">
    <div class="w-full max-w-xl overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="uploadTitle"></h3>
            <button type="button" @click="uploadModalOpen = false" class="h-8 w-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-label="Close">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="<?= base_url('opex-selling/uploadActual') ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" @submit.prevent="submitUpload($event)">
            <?= csrf_field() ?>
            <input type="hidden" name="upload_type" :value="uploadType">
            <p class="rounded-xl border border-[#2F3185]/20 bg-[#2F3185]/5 px-4 py-3 text-xs text-gray-700 dark:text-gray-300">
                <span x-show="uploadType === 'satuan_juta'"><strong>Sudah Satuan Juta:</strong> nilai disimpan apa adanya ke database.</span>
                <span x-show="uploadType === 'dalam_juta'"><strong>Masih Dalam Juta:</strong> nilai JAN–AUG akan dibagi 1.000.000 sebelum disimpan.</span>
            </p>

            <input type="file" name="excel_file" x-ref="uploadFileInput" @change="handleUploadFile($event)" accept=".xls,.xlsx" class="hidden" required>
            <div @dragover.prevent="" @drop.prevent="handleUploadDrop($event)" @click="$refs.uploadFileInput.click()" class="flex min-h-[180px] cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 hover:border-[#2F3185] bg-gray-50 dark:bg-gray-800/50 p-8 text-center transition">
                <template x-if="!uploadFileName">
                    <div class="space-y-2">
                        <i class="fa-solid fa-file-excel text-4xl text-emerald-600"></i>
                        <p class="text-xs font-bold text-gray-800 dark:text-gray-200">Tarik dan letakkan file Excel di sini</p>
                        <p class="text-[11px] text-gray-500">atau klik untuk memilih file (.xls / .xlsx)</p>
                    </div>
                </template>
                <template x-if="uploadFileName">
                    <div class="space-y-2">
                        <i class="fa-solid fa-file-excel text-4xl text-emerald-600"></i>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white" x-text="uploadFileName"></p>
                        <p class="text-[11px] text-[#2F3185] dark:text-indigo-400">Klik untuk mengganti file</p>
                    </div>
                </template>
            </div>

            <p x-show="uploadError" x-cloak class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs font-semibold text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300" x-text="uploadError"></p>
            
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                <button type="button" @click="uploadModalOpen = false" class="rounded-xl border border-gray-300 dark:border-gray-700 px-5 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Batal</button>
                <button type="submit" :disabled="uploading" class="rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98] disabled:opacity-50">
                    <span x-show="!uploading" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Unggah File</span>
                    </span>
                    <span x-show="uploading" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        <span>Memproses...</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
</template>