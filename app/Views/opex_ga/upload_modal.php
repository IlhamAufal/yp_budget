<div id="opexGaUploadModalOverlay"
     class="fixed inset-0 flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-200" 
     style="z-index: 9999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">

    <div id="opexGaUploadModalDialog" class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-2xl overflow-hidden border border-gray-200/80 dark:border-gray-800 transform scale-95 transition-all duration-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                Upload OPEX GA (<span id="opexGaUploadTypeText" class="text-[#2F3185] dark:text-indigo-400">opex_summary</span>)
            </h3>
            <button type="button" id="opexGaUploadCloseBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 h-8 w-8 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors flex items-center justify-center">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="<?= base_url('opexga/processUpload') ?>" method="post" enctype="multipart/form-data" class="p-6 space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" id="opexGaUploadTypeInput" name="upload_type" value="opex_summary">

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Berkas Excel (.xlsx, .xls)</label>
                <input type="file" name="excel_file" accept=".xlsx, .xls, .csv" required
                       class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#2F3185]/10 file:text-[#2F3185] hover:file:bg-[#2F3185]/20">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                <button type="button" id="opexGaUploadCancelBtn" class="rounded-xl border border-gray-300 dark:border-gray-700 px-5 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98]">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Unggah & Proses</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
  const overlay = document.getElementById('opexGaUploadModalOverlay');
  const dialog = document.getElementById('opexGaUploadModalDialog');
  const closeBtn = document.getElementById('opexGaUploadCloseBtn');
  const cancelBtn = document.getElementById('opexGaUploadCancelBtn');
  const typeText = document.getElementById('opexGaUploadTypeText');
  const typeInput = document.getElementById('opexGaUploadTypeInput');

  let isOpen = false;

  function openModal(type) {
    if (type) {
      if (typeText) typeText.textContent = type;
      if (typeInput) typeInput.value = type;
    }
    isOpen = true;
    if (overlay) {
      overlay.classList.remove('hidden');
      requestAnimationFrame(() => {
        overlay.classList.remove('opacity-0');
        if (dialog) {
          dialog.classList.remove('scale-95');
          dialog.classList.add('scale-100');
        }
      });
    }
  }

  function closeModal() {
    isOpen = false;
    if (overlay) {
      overlay.classList.add('opacity-0');
      if (dialog) {
        dialog.classList.remove('scale-100');
        dialog.classList.add('scale-95');
      }
      setTimeout(() => {
        overlay.classList.add('hidden');
      }, 200);
    }
  }

  window.addEventListener('open-upload-modal', (e) => {
    openModal(e.detail && e.detail.type);
  });

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

  if (overlay) {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeModal();
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isOpen) closeModal();
  });
})();
</script>