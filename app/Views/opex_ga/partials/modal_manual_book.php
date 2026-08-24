<div id="opexGaManualBookOverlay" class="fixed inset-0 z-[9999999] flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-200" style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(6px);">
    <div id="opexGaManualBookDialog" class="relative w-full max-w-2xl rounded-lg bg-white p-6 shadow-2xl dark:bg-boxdark transform scale-95 transition-all duration-200">
        <div class="mb-4 flex items-center justify-between border-b pb-3 dark:border-strokedark">
            <h3 class="text-base font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-book text-red-600"></i> Manual Book - Panduan Pengisian OPEX GA
            </h3>
            <button type="button" id="opexGaManualBookCloseBtn" class="text-gray-400 hover:text-black dark:hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="space-y-3 text-xs leading-relaxed text-gray-700 dark:text-gray-300 max-h-[60vh] overflow-y-auto pr-2">
            <p><strong>Langkah 1:</strong> Pilih Cost Center tujuan pada dropdown yang tersedia.</p>
            <p><strong>Langkah 2:</strong> Pilih Akun Biaya yang akan diisi, lalu klik tombol <strong>"Entry"</strong> di kolom paling kanan.</p>
            <p><strong>Langkah 3:</strong> Pada halaman detail rincian, gunakan tombol <strong>"Tambah Item"</strong> untuk menambah baris rincian baru.</p>
            <p><strong>Langkah 4:</strong> Masukkan alokasi budget per bulan (Jan-Des). Sistem akan menghitung Total secara otomatis.</p>
            <p><strong>Langkah 5:</strong> Klik tombol <strong>"Simpan Detail"</strong> di bagian bawah halaman untuk menyimpan perubahan.</p>
        </div>

        <div class="mt-6 flex justify-end border-t pt-3 dark:border-strokedark">
            <button type="button" id="opexGaManualBookCancelBtn" class="rounded bg-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-300 dark:bg-meta-4 dark:text-gray-200">
                Tutup Panduan
            </button>
        </div>
    </div>
</div>

<script>
(function() {
  const overlay = document.getElementById('opexGaManualBookOverlay');
  const dialog = document.getElementById('opexGaManualBookDialog');
  const closeBtn = document.getElementById('opexGaManualBookCloseBtn');
  const cancelBtn = document.getElementById('opexGaManualBookCancelBtn');

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

  window.openOpexGaManualBook = openModal;

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
  if (overlay) {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeModal();
    });
  }
})();
</script>
