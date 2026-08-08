<div class="space-y-6">
    <div class="rounded-sm border border-stroke bg-gray-50 p-6 dark:border-strokedark dark:bg-meta-4">
        <div class="flex flex-wrap items-center gap-4">
            <button @click="openUploadModal('axapta')"
                class="inline-flex items-center gap-2 rounded bg-brand-600 px-5 py-3 text-sm font-medium text-white hover:bg-brand-700 transition">
                <i class="fas fa-cloud-upload-alt"></i> Upload Data (Dari AXAPTA)
            </button>

            <button @click="openUploadModal('template')"
                class="inline-flex items-center gap-2 rounded bg-brand-600 px-5 py-3 text-sm font-medium text-white hover:bg-brand-700 transition">
                <i class="fas fa-file-upload"></i> Upload Data (Dari Template Sistem Budget)
            </button>
        </div>
    </div>
</div>
