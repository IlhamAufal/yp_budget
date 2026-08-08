<div 
    x-show="uploadModalOpen" 
    class="fixed inset-0 z-[999999] flex items-center justify-center p-4"
    style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px);"
    x-cloak
>
    <div 
        @click.outside="uploadModalOpen = false" 
        class="w-full max-w-2xl rounded-lg bg-white shadow-2xl dark:bg-boxdark border border-stroke dark:border-strokedark overflow-hidden"
    >
        <div class="flex items-center justify-between border-b border-stroke px-6 py-4 dark:border-strokedark">
            <h3 class="flex items-center gap-2 text-base font-bold text-black dark:text-white">
                <svg class="fill-current text-black dark:text-white" width="18" height="18" viewBox="0 0 24 24">
                    <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.95s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
                </svg>
                <span x-text="uploadTitle"></span>
            </h3>
            <button 
                @click="uploadModalOpen = false" 
                class="text-gray-400 hover:text-black dark:hover:text-white"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form 
            action="<?= base_url('opex_selling/upload_actual_process') ?>" 
            method="POST" 
            enctype="multipart/form-data" 
            class="p-6"
            x-data="{
                isDragging: false,
                fileName: '',
                handleFileDrop(e) {
                    this.isDragging = false;
                    if (e.dataTransfer.files.length > 0) {
                        this.$refs.fileInput.files = e.dataTransfer.files;
                        this.fileName = e.dataTransfer.files[0].name;
                    }
                },
                handleFileSelect(e) {
                    if (e.target.files.length > 0) {
                        this.fileName = e.target.files[0].name;
                    }
                }
            }"
        >
            <?= csrf_field() ?>
            <input type="hidden" name="upload_type" :value="uploadType">

            <div class="mb-4">
                <label 
                    for="file_upload_input"
                    class="block w-full text-center rounded bg-brand-600 py-2.5 font-bold text-white uppercase shadow hover:bg-brand-700 cursor-pointer transition-colors"
                >
                    SEARCH
                </label>
                <input 
                    type="file" 
                    id="file_upload_input" 
                    name="excel_file" 
                    x-ref="fileInput" 
                    @change="handleFileSelect" 
                    accept=".xls,.xlsx" 
                    class="hidden" 
                    required
                >
            </div>

            <div 
                @dragover.prevent="isDragging = true" 
                @dragleave.prevent="isDragging = false" 
                @drop.prevent="handleFileDrop($event)"
                :class="isDragging ? 'border-brand-600 bg-brand-50/20' : 'border-emerald-500 bg-gray-50 dark:bg-meta-4/20'"
                class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed p-12 text-center transition-all cursor-pointer min-h-[220px]"
                @click="$refs.fileInput.click()"
            >
                <template x-if="!fileName">
                    <p class="text-xl font-bold tracking-wide text-emerald-600 dark:text-emerald-400">
                        DRAG AND DROP A EXCEL FILE
                    </p>
                </template>

                <template x-if="fileName">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="text-brand-600" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        <p class="text-sm font-semibold text-black dark:text-white" x-text="fileName"></p>
                        <span class="text-xs text-brand-600">Click or Drag to replace</span>
                    </div>
                </template>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button 
                    type="button" 
                    @click="uploadModalOpen = false" 
                    class="rounded border border-stroke px-5 py-2 text-sm font-medium text-black hover:bg-gray-100 dark:border-strokedark dark:text-white dark:hover:bg-meta-4"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="rounded bg-brand-600 px-6 py-2 text-sm font-medium text-white hover:bg-brand-700 transition-colors shadow"
                >
                    Upload File
                </button>
            </div>
        </form>
    </div>
</div>