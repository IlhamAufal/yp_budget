<div x-show="uploadModalOpen" 
     class="fixed inset-0 z-[9999999] flex items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
     x-transition x-cloak>

    <div x-data="uploadActualSellingHandler()" 
         class="bg-white dark:bg-boxdark rounded-2xl shadow-2xl w-full max-w-lg border border-stroke dark:border-strokedark overflow-hidden" 
         @click.outside="uploadModalOpen = false">
        
        <div class="px-6 py-4 bg-gray-50 dark:bg-meta-4/40 border-b border-stroke dark:border-strokedark flex items-center justify-between">
            <h3 class="font-bold text-gray-900 dark:text-white text-base">Upload Actual Budget Selling</h3>
            <button @click="uploadModalOpen = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <div class="flex rounded-xl bg-gray-100 dark:bg-meta-4 p-1">
                <button type="button" 
                        @click="uploadType = 'regular'" 
                        :class="uploadType === 'regular' ? 'bg-white dark:bg-boxdark text-primary shadow-sm' : 'text-gray-500'" 
                        class="w-1/2 py-2 text-xs font-bold rounded-lg transition-all">
                    Regular Selling
                </button>
                <button type="button" 
                        @click="uploadType = 'ap'" 
                        :class="uploadType === 'ap' ? 'bg-white dark:bg-boxdark text-primary shadow-sm' : 'text-gray-500'" 
                        class="w-1/2 py-2 text-xs font-bold rounded-lg transition-all">
                    A&P Selling
                </button>
            </div>

            <div class="relative border-2 border-dashed rounded-xl p-8 text-center transition-all border-gray-300 dark:border-strokedark hover:border-primary">
                <input type="file" id="fileInputSelling" accept=".xls,.xlsx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleFile($event)">

                <template x-if="!file">
                    <div class="space-y-2 pointer-events-none">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">Click or Drag & Drop Excel File</p>
                        <p class="text-xs text-gray-400">Target Type: <span class="font-bold text-primary uppercase" x-text="uploadType"></span> (Max: 300 KB)</p>
                    </div>
                </template>

                <template x-if="file">
                    <div class="space-y-2">
                        <p class="text-sm font-bold text-success" x-text="file.name"></p>
                        <button type="button" @click="file = null" class="text-xs text-danger hover:underline">Remove File</button>
                    </div>
                </template>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 dark:bg-meta-4/40 border-t border-stroke dark:border-strokedark flex justify-end gap-3">
            <button type="button" @click="uploadModalOpen = false" class="px-4 py-2 text-xs font-medium text-gray-600 dark:text-gray-300">Cancel</button>
            <button type="button" @click="upload()" :disabled="!file || isUploading" class="px-5 py-2 bg-success text-white text-xs font-medium rounded-lg disabled:opacity-50">
                <span x-text="isUploading ? 'Uploading...' : 'Upload Now'"></span>
            </button>
        </div>
    </div>
</div>

<script>
function uploadActualSellingHandler() {
    return {
        uploadType: 'regular',
        file: null,
        isUploading: false,

        handleFile(e) {
            this.file = e.target.files[0] || null;
        },

        upload() {
            if (!this.file) return;
            this.isUploading = true;

            const formData = new FormData();
            formData.append('file', this.file);
            formData.append('upload_type', this.uploadType);

            fetch('<?= base_url('opex_selling/uploadActual'); ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                this.isUploading = false;
                alert(data.message || 'Upload berhasil!');
                this.file = null;
                uploadModalOpen = false;
            })
            .catch(err => {
                this.isUploading = false;
                alert('Gagal mengunggah file.');
                console.error(err);
            });
        }
    }
}
</script>