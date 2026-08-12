<div>
    <div x-show="uploadFeedback" x-cloak class="mb-4 rounded border border-success bg-success/10 px-4 py-3 text-sm text-success" x-text="uploadFeedback"></div>
    <div x-show="uploadError" x-cloak class="mb-4 rounded border border-danger bg-danger/10 px-4 py-3 text-sm text-danger" x-text="uploadError"></div>
    <div class="mb-5 rounded-sm border border-stroke bg-gray-50 p-5 dark:border-strokedark dark:bg-meta-4">
        <h3 class="mb-2 font-semibold text-black dark:text-white">Upload Actual Selling</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300">Gunakan file .xls atau .xlsx dengan kolom MAIN ACCOUNT, DESCRIPTION, JAN–AUG, dan DEPT. DEPT harus bernilai 600 atau 700.</p>
    </div>
    <div class="flex flex-wrap items-center gap-4">
        <button type="button" @click="openUploadModal('satuan_juta', 'Upload Data (Sudah Satuan Juta)')"
            class="inline-flex items-center gap-2 rounded bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-md transition-all hover:bg-brand-700 focus:outline-none">
            <i class="fas fa-upload"></i> Upload Data (Sudah Satuan Juta)
        </button>
        <button type="button" @click="openUploadModal('dalam_juta', 'Upload Data (Masih Dalam Juta)')"
            class="inline-flex items-center gap-2 rounded bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-md transition-all hover:bg-brand-700 focus:outline-none">
            <i class="fas fa-upload"></i> Upload Data (Masih Dalam Juta)
        </button>
    </div>
    <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">Sudah Satuan Juta: nilai disimpan apa adanya. Masih Dalam Juta: nilai JAN–AUG dibagi 1.000.000 sebelum disimpan.</p>
</div>