<div id="capexManualBookOverlay"
     class="fixed inset-0 z-[9999999] flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-200" 
     style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(4px);">
    <div id="capexManualBookDialog" class="w-full max-w-2xl bg-white dark:bg-boxdark rounded-2xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-800 transform scale-95 transition-all duration-200">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
            <h3 class="font-bold text-gray-800 dark:text-white text-base">Panduan Pengisian Form CAPEX</h3>
            <button type="button" id="capexManualBookCloseBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6 text-sm text-gray-600 dark:text-gray-300 space-y-3">
            <p>1. Pilih Cost Center tempat alokasi biaya aset diajukan.</p>
            <p>2. Klik tombol edit biru pada kategori aset yang ingin ditambahkan.</p>
            <p>3. Isikan rincian item, kuantitas, estimasi harga satuan (Satuan Dalam Juta IDR), dan tentukan bulan perolehan aset.</p>
            <p>4. Tekan tombol <strong>Save</strong> untuk menyimpan data ke database.</p>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 flex justify-end">
            <button type="button" id="capexManualBookCancelBtn" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold rounded-lg text-xs transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
(function() {
  const overlay = document.getElementById('capexManualBookOverlay');
  const dialog = document.getElementById('capexManualBookDialog');
  const closeBtn = document.getElementById('capexManualBookCloseBtn');
  const cancelBtn = document.getElementById('capexManualBookCancelBtn');

  function openModal() {
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

  window.openCapexManualBook = openModal;

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
  if (overlay) {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeModal();
    });
  }
})();
</script>