<div class="space-y-6">
  <div class="flex flex-wrap items-center gap-4">
    <button
      @click="openUploadModal('AXAPTA')"
      class="inline-flex items-center gap-2.5 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-xs hover:bg-blue-700 active:scale-[0.98] transition-all">
      <i class="fa-solid fa-cloud-arrow-up"></i>
      Upload Data (Dari AXAPTA)
    </button>

    <button
      @click="openUploadModal('TEMPLATE')"
      class="inline-flex items-center gap-2.5 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-xs hover:bg-indigo-700 active:scale-[0.98] transition-all">
      <i class="fa-solid fa-file-excel"></i>
      Upload Data (Dari Template Sistem Budget)
    </button>
  </div>

  <!-- UPLOAD MODAL -->
  <div x-show="isUploadModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
    <div @click.outside="isUploadModalOpen = false" x-transition class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
      <div class="flex items-center justify-between border-b pb-3 dark:border-gray-700">
        <h3 class="text-base font-bold text-gray-800 dark:text-white" x-text="uploadSource === 'AXAPTA' ? 'Upload Data FOH (AXAPTA)' : 'Upload Data FOH (Template Budget)'"></h3>
        <button @click="isUploadModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <form action="<?= base_url('foh/uploadActual') ?>" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="upload_source" :value="uploadSource">

        <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 p-6 text-center hover:border-brand-500 transition-colors">
          <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
          <span class="text-xs text-gray-500 dark:text-gray-400">Pilih berkas Excel (.xlsx / .xls)</span>
          <input type="file" name="excel_file" accept=".xlsx,.xls" required class="mt-3 text-xs text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400">
        </div>

        <div class="rounded-lg bg-amber-50 dark:bg-amber-950/30 p-3 text-xs text-amber-700 dark:text-amber-300">
          <i class="fa-solid fa-triangle-exclamation mr-1"></i>
          <strong>Perhatian:</strong> Data actual akan menimpa data sebelumnya untuk Cost Center & COA yang sama.
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" @click="isUploadModalOpen = false" class="rounded-lg px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Batal</button>
          <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-700 shadow transition-colors">
            <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Upload Data
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
