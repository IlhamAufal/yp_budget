<div x-show="uploadModalOpen" x-cloak class="fixed inset-0 z-[999999] flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px);">
    <div @click.outside="uploadModalOpen = false" class="w-full max-w-2xl overflow-hidden rounded-lg border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark">
        <div class="flex items-center justify-between border-b border-stroke px-6 py-4 dark:border-strokedark">
            <h3 class="text-base font-bold text-black dark:text-white" x-text="uploadTitle"></h3>
            <button type="button" @click="uploadModalOpen = false" class="text-gray-400 hover:text-black dark:hover:text-white" aria-label="Close">&times;</button>
        </div>

        <form action="<?= base_url('opex-selling/uploadActual') ?>" method="POST" enctype="multipart/form-data" class="p-6" @submit.prevent="submitUpload($event)">
            <?= csrf_field() ?>
            <input type="hidden" name="upload_type" :value="uploadType">
            <p class="mb-4 rounded border border-primary/20 bg-primary/5 px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                <span x-show="uploadType === 'satuan_juta'">Sudah Satuan Juta: nilai disimpan apa adanya.</span>
                <span x-show="uploadType === 'dalam_juta'">Masih Dalam Juta: nilai JAN–AUG akan dibagi 1.000.000 sebelum disimpan.</span>
            </p>

            <input type="file" name="excel_file" x-ref="uploadFileInput" @change="handleUploadFile($event)" accept=".xls,.xlsx" class="hidden" required>
            <div @dragover.prevent="" @drop.prevent="handleUploadDrop($event)" @click="$refs.uploadFileInput.click()" class="flex min-h-[180px] cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-emerald-500 bg-gray-50 p-10 text-center transition hover:bg-emerald-50 dark:bg-meta-4/20">
                <template x-if="!uploadFileName">
                    <div>
                        <i class="fas fa-file-excel mb-3 text-4xl text-emerald-600"></i>
                        <p class="font-bold text-emerald-600 dark:text-emerald-400">DRAG AND DROP FILE EXCEL</p>
                        <p class="mt-1 text-xs text-gray-500">atau klik untuk memilih .xls / .xlsx</p>
                    </div>
                </template>
                <template x-if="uploadFileName">
                    <div>
                        <i class="fas fa-file-excel mb-3 text-4xl text-emerald-600"></i>
                        <p class="font-semibold text-black dark:text-white" x-text="uploadFileName"></p>
                        <p class="mt-1 text-xs text-gray-500">Klik untuk mengganti file</p>
                    </div>
                </template>
            </div>

            <p x-show="uploadError" x-cloak class="mt-4 rounded border border-danger bg-danger/10 px-4 py-3 text-sm text-danger" x-text="uploadError"></p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="uploadModalOpen = false" class="rounded border border-stroke px-5 py-2 text-sm font-medium text-black hover:bg-gray-100 dark:border-strokedark dark:text-white dark:hover:bg-meta-4">Cancel</button>
                <button type="submit" :disabled="uploading" class="rounded bg-brand-600 px-6 py-2 text-sm font-medium text-white shadow transition-colors hover:bg-brand-700 disabled:opacity-50">
                    <span x-show="!uploading">Upload File</span>
                    <span x-show="uploading"><i class="fas fa-spinner fa-spin mr-1"></i> Memproses...</span>
                </button>
            </div>
        </form>
    </div>
</div>