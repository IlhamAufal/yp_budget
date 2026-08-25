<div class="space-y-6">
  <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
    <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight mb-1">Upload Data Actual FOH</h3>
    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-6">Pilih sumber data Excel untuk mengunggah realisasi actual FOH. Pastikan format kolom sesuai dengan template standar.</p>
    <div class="flex flex-wrap items-center gap-3">
      <button
        @click="openUploadModal('AXAPTA')"
        class="inline-flex items-center gap-2 rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs active:scale-[0.98] transition-all cursor-pointer">
        <i class="fa-solid fa-cloud-arrow-up"></i>
        <span>Upload Data (Dari AXAPTA)</span>
      </button>

      <button
        @click="openUploadModal('TEMPLATE')"
        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-xs font-semibold text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-xs active:scale-[0.98] transition-all cursor-pointer">
        <i class="fa-solid fa-file-excel text-[#2F3185] dark:text-indigo-400"></i>
        <span>Upload Data (Dari Template Sistem Budget)</span>
      </button>
    </div>
  </div>

  <!-- UPLOAD MODAL -->
  <div x-show="isUploadModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/65 backdrop-blur-xs" x-cloak>
    <div @click.outside="isUploadModalOpen = false" x-transition class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 space-y-4">
      <div class="flex items-center justify-between border-b pb-3 dark:border-gray-800">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white" x-text="uploadSource === 'AXAPTA' ? 'Upload Data FOH (AXAPTA)' : 'Upload Data FOH (Template Budget)'"></h3>
        <button @click="isUploadModalOpen = false" class="h-8 w-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>
      </div>

      <form action="<?= base_url('foh/uploadActual') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="upload_source" :value="uploadSource">

        <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700 p-6 text-center hover:border-[#2F3185] transition-colors bg-gray-50/50 dark:bg-gray-800/40">
          <i class="fa-solid fa-cloud-arrow-up text-3xl text-[#2F3185] dark:text-indigo-400 mb-2"></i>
          <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih berkas Excel (.xlsx / .xls)</span>
          <input type="file" name="excel_file" accept=".xlsx,.xls" required class="mt-3 text-xs text-gray-500 file:mr-3 file:rounded-xl file:border-0 file:bg-[#2F3185]/10 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-[#2F3185] hover:file:bg-[#2F3185]/20 dark:file:bg-[#2F3185]/20 dark:file:text-indigo-400">
        </div>

        <div class="rounded-2xl border border-amber-200/80 bg-amber-50/80 dark:border-amber-900/50 dark:bg-amber-950/30 p-3.5 text-xs text-amber-800 dark:text-amber-300">
          <i class="fa-solid fa-triangle-exclamation mr-1 text-amber-600"></i>
          <strong>Perhatian:</strong> Data actual akan menimpa data sebelumnya untuk Cost Center & COA yang sama.
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
          <button type="button" @click="isUploadModalOpen = false" class="rounded-xl border border-gray-300 dark:border-gray-700 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">Batal</button>
          <button type="submit" class="rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-4 py-2 text-xs font-semibold text-white shadow-xs transition-colors inline-flex items-center gap-2 active:scale-[0.98]">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            <span>Upload Data</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
