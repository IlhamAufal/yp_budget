<?php
  /* -------------------------------------------------------
   * Modal: Pilih Tahun Anggaran (Working Year) - Vanilla JS
   * General Component — dirender di main.php.
   * Event trigger dari JS: window.dispatchEvent(new CustomEvent('open-year-modal'))
   * atau fungsi: window.openYearModal()
   * ------------------------------------------------------- */
  use App\Models\PeriodModel;

  $periodModel = new PeriodModel();
  $activeYears = $periodModel->getActiveYears();
  $currentYear = session()->get('year_code');
  $mustForce   = empty($currentYear);
?>

<!-- ================================================================ -->
<!-- GLOBAL MODAL: PILIH TAHUN ANGGARAN (WORKING YEAR)               -->
<!-- ================================================================ -->
<div
  id="modalSelectYearOverlay"
  class="fixed inset-0 flex items-center justify-center p-4 <?= $mustForce ? '' : 'hidden opacity-0' ?> transition-opacity duration-300"
  style="z-index: 9999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
>
  <!-- Modal Card -->
  <div
    id="modalSelectYearDialog"
    class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden transform <?= $mustForce ? 'scale-100' : 'scale-95' ?> transition-all duration-300"
    style="z-index: 9999999;"
    role="dialog"
    aria-modal="true"
  >
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-800">
      <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400 shrink-0">
          <i class="fa-solid fa-calendar-check text-xl"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
            Pilih Tahun Anggaran
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            <?= $mustForce
              ? 'Anda wajib memilih <strong>Working Year</strong> untuk melanjutkan.'
              : 'Ganti Tahun Anggaran (Working Year) aktif.' ?>
          </p>
        </div>
      </div>

      <!-- Close button — hanya tampil jika tidak forced -->
      <?php if (! $mustForce): ?>
        <button
          type="button"
          id="modalSelectYearCloseBtn"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 p-2 rounded-lg transition-colors shrink-0 ml-2"
        >
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      <?php endif; ?>
    </div>

    <!-- Form Body -->
    <form action="<?= base_url('set-year') ?>" method="POST" class="p-6 space-y-6">
      <?= csrf_field() ?>

      <div>
        <label for="year_code_select" class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
          Tahun Anggaran <span class="text-red-500">*</span>
        </label>
        <select
          id="year_code_select"
          name="year_code"
          required
          class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-colors"
        >
          <?php foreach ($activeYears as $year): ?>
            <option value="<?= $year ?>" <?= $currentYear == $year ? 'selected' : '' ?>>
              Tahun Anggaran <?= $year ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Info saat forced -->
      <?php if ($mustForce): ?>
        <div class="flex items-start gap-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 p-4 text-sm text-amber-800 dark:text-amber-300">
          <i class="fa-solid fa-circle-info text-amber-500 dark:text-amber-400 mt-0.5 shrink-0"></i>
          <span>Modal ini <strong>wajib diisi</strong>. Anda tidak dapat menutupnya sebelum memilih tahun dan menekan <strong>GO!</strong></span>
        </div>
      <?php endif; ?>

      <!-- Actions -->
      <div class="pt-2 flex items-center gap-4">
        <?php if (! $mustForce): ?>
          <button
            type="button"
            id="modalSelectYearCancelBtn"
            class="flex-1 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-3 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition-colors"
          >
            Batal
          </button>
        <?php endif; ?>
        <button
          type="submit"
          class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/40 transition-colors dark:bg-brand-500 dark:hover:bg-brand-600"
        >
          <span>GO!</span>
          <i class="fa-solid fa-arrow-right text-xs"></i>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
(function() {
  const isForced = <?= $mustForce ? 'true' : 'false' ?>;
  const overlay = document.getElementById('modalSelectYearOverlay');
  const dialog = document.getElementById('modalSelectYearDialog');
  const closeBtn = document.getElementById('modalSelectYearCloseBtn');
  const cancelBtn = document.getElementById('modalSelectYearCancelBtn');

  let isOpen = isForced;

  if (isForced) {
    document.body.style.overflow = 'hidden';
  }

  function openModal() {
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
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    if (isForced) return;
    isOpen = false;
    if (overlay) {
      overlay.classList.add('opacity-0');
      if (dialog) {
        dialog.classList.remove('scale-100');
        dialog.classList.add('scale-95');
      }
      setTimeout(() => {
        overlay.classList.add('hidden');
      }, 300);
    }
    document.body.style.overflow = '';
  }

  window.openYearModal = openModal;
  window.closeYearModal = closeModal;

  window.addEventListener('open-year-modal', openModal);

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

  if (overlay) {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay && !isForced) {
        closeModal();
      }
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isOpen && !isForced) {
      closeModal();
    }
  });
})();
</script>