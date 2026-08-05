<?php
  /* -------------------------------------------------------
   * Modal: Pilih Tahun Anggaran (Working Year)
   * General Component — dirender di main.php sebelum layout wrapper.
   * Event trigger dari JS: window.dispatchEvent(new CustomEvent('open-year-modal'))
   * ------------------------------------------------------- */
  use App\Models\PeriodModel;

  $periodModel = new PeriodModel();
  $activeYears = $periodModel->getActiveYears();
  $currentYear = session()->get('year_code');
  $mustForce   = empty($currentYear);
?>

<!-- ================================================================ -->
<!-- GLOBAL MODAL: PILIH TAHUN ANGGARAN (WORKING YEAR)               -->
<!-- Diletakkan di luar layout wrapper agar backdrop blur tidak       -->
<!-- terpotong oleh overflow:hidden pada container layout.            -->
<!-- ================================================================ -->

<?php /*
  CATATAN Z-INDEX:
  - Preloader   : z-999999  (style.css)
  - Modal       : z-[9999999] (di atas preloader & navbar)
  - Toast       : z-[99999999] (selalu di atas semua)
*/ ?>

<div
  id="modal-year-root"
  x-data="{
    open: <?= $mustForce ? 'true' : 'false' ?>,
    forced: <?= $mustForce ? 'true' : 'false' ?>,
    selectedYear: '<?= $currentYear ?? ($activeYears[0] ?? date('Y')) ?>'
  }"
  x-init="
    /* Kunci scroll body saat modal terbuka */
    $watch('open', val => {
      document.body.style.overflow = val ? 'hidden' : '';
    });
    if (open) document.body.style.overflow = 'hidden';

    /* Trigger buka modal dari JS/tombol navbar — gunakan $data agar reaktif */
    window.addEventListener('open-year-modal', () => {
      $data.open = true;
    });
  "
  @keydown.escape.window="if (!forced) open = false"
>
  <!-- Backdrop -->
  <div
    x-show="open"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center p-4"
    style="z-index: 9999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
    @click.self="if (!forced) open = false"
    x-cloak
  >
    <!-- Modal Card — tanpa x-show sendiri, cukup kontrol dari backdrop -->
    <div
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95 translate-y-3"
      x-transition:enter-end="opacity-100 scale-100 translate-y-0"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100 scale-100 translate-y-0"
      x-transition:leave-end="opacity-0 scale-95 translate-y-3"
      class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800"
      style="z-index: 9999999;"
    >
      <!-- Header -->
      <div class="flex items-start justify-between p-6 pb-5 border-b border-gray-100 dark:border-gray-800">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400 shrink-0">
            <i class="fa-solid fa-calendar-check text-lg"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">
              Pilih Tahun Anggaran
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
              <?= $mustForce
                ? 'Anda wajib memilih <strong>Working Year</strong> untuk melanjutkan.'
                : 'Ganti Tahun Anggaran (Working Year) aktif.' ?>
            </p>
          </div>
        </div>

        <!-- Close button — hanya tampil jika tidak forced -->
        <template x-if="!forced">
          <button
            type="button"
            @click="open = false"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 p-1.5 rounded-lg transition-colors shrink-0 ml-2"
          >
            <i class="fa-solid fa-xmark text-base"></i>
          </button>
        </template>
      </div>

      <!-- Form Body -->
      <form action="<?= base_url('set-year') ?>" method="POST" class="p-6 space-y-4">
        <?= csrf_field() ?>

        <div>
          <label for="year_code_select" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">
            Tahun Anggaran <span class="text-red-500">*</span>
          </label>
          <select
            id="year_code_select"
            name="year_code"
            x-model="selectedYear"
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
          <div class="flex items-start gap-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 p-3.5 text-xs text-amber-800 dark:text-amber-300">
            <i class="fa-solid fa-circle-info text-amber-500 dark:text-amber-400 mt-0.5 shrink-0"></i>
            <span>Modal ini <strong>wajib diisi</strong>. Anda tidak dapat menutupnya sebelum memilih tahun dan menekan <strong>GO!</strong></span>
          </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="pt-1 flex items-center gap-3">
          <template x-if="!forced">
            <button
              type="button"
              @click="open = false"
              class="flex-1 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition-colors"
            >
              Batal
            </button>
          </template>
          <button
            type="submit"
            class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-bold text-white shadow-md hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/40 transition-colors dark:bg-brand-500 dark:hover:bg-brand-600"
          >
            <span>GO!</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
