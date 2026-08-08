<div x-show="uploadModalOpen" class="fixed inset-0 z-[999999] flex items-center justify-center p-4" x-cloak>
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="uploadModalOpen = false"></div>

    <div class="relative w-full max-w-xl rounded-lg bg-white p-6 shadow-2xl dark:bg-boxdark">
        <div class="mb-4 flex items-center justify-between border-b pb-3 dark:border-strokedark">
            <div class="flex items-center gap-2 text-sm font-bold text-black dark:text-white">
                <i class="fas fa-cloud-upload-alt text-gray-600 dark:text-gray-300"></i>
                <span>Upload Actual Budget <span x-text="uploadSourceTitle"></span></span>
            </div>
            <button @click="uploadModalOpen = false" class="text-gray-400 hover:text-black dark:hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="space-y-4">
            <div class="text-center">
                <label for="excel_file_input"
                    @dragover.prevent="isHovered = true"
                    @dragleave.prevent="isHovered = false"
                    @drop.prevent="isHovered = false; handleFileDrop($event)"
                    :class="isHovered ? 'border-emerald-500 bg-emerald-50' : 'border-dashed border-emerald-500'"
                    class="flex min-h-[160px] cursor-pointer flex-col items-center justify-center rounded-md border-2 p-6 transition">
                    
                    <button type="button" @click="$refs.fileInput.click()"
                        class="mb-3 rounded bg-emerald-500 px-6 py-2 text-xs font-bold text-white shadow hover:bg-emerald-600 transition">
                        <i class="fas fa-search mr-1"></i> SEARCH
                    </button>
                    
                    <p class="text-sm font-semibold tracking-wider text-emerald-600">
                        DRAG AND DROP A EXCEL FILE
                    </p>

                    <template x-if="selectedFile">
                        <p class="mt-2 text-xs font-medium text-gray-700 dark:text-gray-300" x-text="'File terpilih: ' + selectedFile.name"></p>
                    </template>

                    <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept=".xls,.xlsx" class="hidden" id="excel_file_input" />
                </label>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2 border-t pt-3 dark:border-strokedark">
            <button @click="uploadModalOpen = false" class="rounded px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300">
                Batal
            </button>
            <button @click="submitUpload()" class="rounded bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-opacity-90">
                <i class="fas fa-upload mr-1"></i> Unggah File
            </button>
        </div>
    </div>
</div>
