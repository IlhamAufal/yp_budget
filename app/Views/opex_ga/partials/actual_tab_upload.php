<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <div class="flex flex-wrap items-center gap-3">
            <button @click="openUploadModal('axapta')"
                class="inline-flex items-center gap-2 rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs active:scale-[0.98] transition-all cursor-pointer">
                <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                <span>Upload Data (Dari AXAPTA)</span>
            </button>

            <button @click="openUploadModal('template')"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-xs font-semibold text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-xs active:scale-[0.98] transition-all cursor-pointer">
                <i class="fa-solid fa-file-arrow-up text-xs text-[#2F3185]"></i>
                <span>Upload Data (Dari Template Sistem Budget)</span>
            </button>
        </div>
    </div>
</div>
