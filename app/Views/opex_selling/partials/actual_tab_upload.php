<div class="space-y-6">
    <div x-show="uploadFeedback" x-cloak class="rounded-2xl border border-emerald-200/80 bg-emerald-50/80 p-4 text-xs font-semibold text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300 shadow-xs" x-text="uploadFeedback"></div>
    <div x-show="uploadError" x-cloak class="rounded-2xl border border-red-200/80 bg-red-50/80 p-4 text-xs font-semibold text-red-800 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300 shadow-xs" x-text="uploadError"></div>
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-1">Unggah Data Actual Selling</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-5">Gunakan file .xls atau .xlsx dengan kolom MAIN ACCOUNT, DESCRIPTION, JAN–AUG, dan DEPT. DEPT harus bernilai 600 atau 700.</p>
        <div class="flex flex-wrap items-center gap-3">
            <button type="button" @click="openUploadModal('satuan_juta', 'Upload Data (Sudah Satuan Juta)')"
                class="inline-flex items-center gap-2 rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs active:scale-[0.98] transition-all cursor-pointer">
                <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                <span>Upload Data (Sudah Satuan Juta)</span>
            </button>
            <button type="button" @click="openUploadModal('dalam_juta', 'Upload Data (Masih Dalam Juta)')"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-xs active:scale-[0.98] transition-all cursor-pointer">
                <i class="fa-solid fa-file-arrow-up text-xs text-[#2F3185] dark:text-indigo-400"></i>
                <span>Upload Data (Masih Dalam Juta)</span>
            </button>
        </div>
        <p class="mt-4 text-xs text-gray-400 dark:text-gray-500">Catatan: "Sudah Satuan Juta": nilai disimpan apa adanya. "Masih Dalam Juta": nilai JAN–AUG dibagi 1.000.000 sebelum disimpan.</p>
    </div>
</div>