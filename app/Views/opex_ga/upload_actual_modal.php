<div x-data="uploadActualHandler()" 
     x-show="uploadModalOpen" 
     class="fixed inset-0 z-[999999] flex items-center justify-center p-4" 
     style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(6px);" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-cloak>

    <div class="bg-white dark:bg-boxdark rounded-2xl shadow-2xl w-full max-w-lg border border-stroke dark:border-strokedark overflow-hidden" 
         @click.outside="uploadModalOpen = false">
        
        <div class="px-6 py-4 bg-gray-50 dark:bg-meta-4/40 border-b border-stroke dark:border-strokedark flex items-center justify-between">
            <h3 class="font-bold text-gray-900 dark:text-white text-base">Upload Actual Budget Excel</h3>
            <button @click="uploadModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-meta-4 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <div class="relative border-2 border-dashed rounded-xl p-8 text-center transition-all"
                 :class="isDragging ? 'border-primary bg-primary/5' : 'border-gray-300 dark:border-strokedark hover:border-primary'"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="handleDrop($event)">
                
                <input type="file" 
                       id="fileinput" 
                       accept=".xls,.xlsx" 
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                       @change="handleFileSelect($event)">

                <template x-if="!file">
                    <div class="space-y-3 pointer-events-none">
                        <div class="w-12 h-12 mx-auto rounded-full bg-primary/10 text-primary flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">Click or Drag & Drop Excel File</p>
                            <p class="text-xs text-gray-500 mt-1">Accepts .xls / .xlsx (Max size: 300 KB)</p>
                        </div>
                    </div>
                </template>

                <template x-if="file">
                    <div class="space-y-3">
                        <div class="w-12 h-12 mx-auto rounded-full bg-success/10 text-success flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[280px] mx-auto" x-text="file.name"></p>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="(file.size / 1024).toFixed(2) + ' KB'"></p>
                        </div>
                        <button type="button" @click.stop="removeFile()" class="inline-flex items-center gap-1 text-xs text-danger hover:underline font-medium">
                            Remove File
                        </button>
                    </div>
                </template>
            </div>

            <template x-if="errorMessage">
                <div class="p-3 bg-danger/10 border border-danger/20 rounded-lg text-xs text-danger font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span x-text="errorMessage"></span>
                </div>
            </template>
        </div>

        <div class="px-6 py-4 bg-gray-50 dark:bg-meta-4/40 border-t border-stroke dark:border-strokedark flex items-center justify-end gap-3">
            <button type="button" @click="uploadModalOpen = false" class="px-4 py-2 rounded-lg text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-meta-4 transition-colors">
                Cancel
            </button>
            <button type="button" 
                    @click="uploadFile()" 
                    :disabled="!file || isUploading" 
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-success text-white text-xs md:text-sm font-medium hover:bg-opacity-90 transition-all disabled:opacity-50">
                <span x-show="isUploading" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <span x-text="isUploading ? 'Uploading...' : 'Upload Data'">Upload Data</span>
            </button>
        </div>
    </div>
</div>

<script>
function uploadActualHandler() {
    return {
        file: null,
        isDragging: false,
        isUploading: false,
        errorMessage: '',

        handleFileSelect(e) {
            const selected = e.target.files[0];
            this.validateAndSetFile(selected);
        },

        handleDrop(e) {
            this.isDragging = false;
            const dropped = e.dataTransfer.files[0];
            this.validateAndSetFile(dropped);
        },

        validateAndSetFile(file) {
            this.errorMessage = '';
            if (!file) return;

            // Validation size (300 KB)
            if (file.size > 307200) { // 300 * 1024
                this.errorMessage = 'File size exceeds max limit (300 KB)!';
                this.file = null;
                return;
            }

            this.file = file;
        },

        removeFile() {
            this.file = null;
            this.errorMessage = '';
            document.getElementById('fileinput').value = '';
        },

        uploadFile() {
            if (!this.file) return;

            if (!confirm('Are you sure you want to upload this content?')) return;

            this.isUploading = true;
            const formData = new FormData();
            formData.append('file', this.file);

            fetch('<?= base_url('opex_ga/upload_data_actual_budget'); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(respon => {
                this.isUploading = false;
                alert('Uploaded Successfully!');
                console.log(respon);
                // Option: location.reload();
            })
            .catch(err => {
                this.isUploading = false;
                alert('Upload Failed!');
                console.error(err);
            });
        }
    }
}
</script>