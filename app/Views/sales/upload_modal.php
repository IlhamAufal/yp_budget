<div x-data="{ open: false, uploadType: 'domestic' }"
     @open-upload-modal.window="open = true; uploadType = $event.detail.type"
     x-show="open" 
     class="fixed inset-0 flex items-center justify-center p-4" 
     style="z-index: 999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
     @click.self="open = false" 
     x-cloak>

    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-2xl overflow-hidden border border-gray-200/80 dark:border-gray-800">
        <div class="flex items-center justify-between bg-gray-50/80 dark:bg-gray-800/60 px-6 py-4 border-b border-gray-200 dark:border-gray-800 rounded-t-2xl">
            <h3 class="text-base font-extrabold text-gray-900 dark:text-white">
                Upload Sales Data (<span class="capitalize text-primary" x-text="uploadType"></span>)
            </h3>
            <button type="button" @click="open = false" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="<?= base_url('sales/processUpload') ?>" method="post" enctype="multipart/form-data" class="p-6 space-y-4 rounded-b-2xl">
            <?= csrf_field() ?>
            <input type="hidden" name="upload_type" :value="uploadType">

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Select Excel File (.xlsx, .xls)</label>
                <input type="file" name="excel_file" accept=".xlsx, .xls, .csv" required
                       class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="open = false" class="rounded-lg border border-gray-300 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-medium text-white hover:bg-emerald-700">
                    Upload & Process
                </button>
            </div>
        </form>
    </div>
</div>