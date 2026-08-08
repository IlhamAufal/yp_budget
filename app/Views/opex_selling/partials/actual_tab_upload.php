<div class="p-4 rounded-sm border border-stroke bg-gray-2 dark:border-strokedark dark:bg-meta-4">
    <div class="flex flex-wrap items-center gap-4">
        <button 
            type="button" 
            @click="openUploadModal('dalam_juta', 'Upload Actual Budget Dalam Juta')" 
            class="inline-flex items-center gap-2 rounded bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-md hover:bg-brand-700 focus:outline-none transition-all"
        >
            <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24">
                <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
            </svg>
            Upload Data (Masih Dalam Juta)
        </button>

        <button 
            type="button" 
            @click="openUploadModal('satuan_juta', 'Upload Actual Budget Sudah Satuan Juta')" 
            class="inline-flex items-center gap-2 rounded bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-md hover:bg-brand-700 focus:outline-none transition-all"
        >
            <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24">
                <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
            </svg>
            Upload Data (Sudah Satuan Juta)
        </button>
    </div>
</div>