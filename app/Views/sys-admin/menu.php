<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 md:p-8 mx-auto max-w-(--breakpoint-2xl) space-y-6 md:space-y-8" x-data="menuPage()">

  <!-- ============================================================ -->
  <!-- BREADCRUMB & HEADER -->
  <!-- ============================================================ -->
  <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-3">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors">
          <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span>System Administration</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-brand-500 font-bold">Menu Configuration</span>
      </div>
      <h1 class="text-2xl md:text-3xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-4">
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400">
          <i class="fa-solid fa-bars-staggered text-xl"></i>
        </span>
        Menu Configuration
      </h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
        Kelola hirarki menu sidebar aplikasi. Menu ini dirender dinamis melalui MenuBuilder.
      </p>
    </div>

    <!-- Quick Action -->
    <div class="flex flex-wrap items-center gap-3">
      <button
        type="button"
        @click="openCreateModal()"
        class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-3 text-sm font-bold text-white shadow-md hover:bg-brand-600 transition-all"
      >
        <i class="fa-solid fa-plus"></i>
        <span>Tambah Menu</span>
      </button>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- DATA TABLE & FILTER -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <!-- Filter Bar -->
    <div class="p-5 border-b border-gray-100 dark:border-gray-800">
      <form method="GET" action="<?= base_url('sys-admin/menu') ?>" class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[240px]">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
            <i class="fa-solid fa-magnifying-glass text-xs text-gray-400 dark:text-gray-500"></i>
          </div>
          <input
            type="text"
            name="search"
            value="<?= esc($filters['search'] ?? '') ?>"
            placeholder="Cari nama menu, link, atau deskripsi..."
            class="w-full rounded-xl border border-gray-200/80 bg-gray-50/60 py-2.5 pl-10 pr-4 text-xs md:text-sm font-medium text-gray-800 placeholder:text-gray-400/80 focus:border-brand-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700/80 dark:bg-gray-800/80 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-brand-400 transition-all duration-200 shadow-xs"
          />
        </div>

        <select
          name="status"
          class="rounded-xl border border-gray-200 bg-gray-50/50 py-2.5 px-4 text-sm min-w-[140px] focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
        >
          <option value="">Semua Status</option>
          <option value="Y" <?= ($filters['status'] ?? '') === 'Y' ? 'selected' : '' ?>>Aktif (Y)</option>
          <option value="N" <?= ($filters['status'] ?? '') === 'N' ? 'selected' : '' ?>>Non-Aktif (N)</option>
        </select>

        <button
          type="submit"
          class="rounded-xl bg-gray-900 dark:bg-brand-500 py-2.5 px-5 text-sm font-semibold text-white hover:bg-black dark:hover:bg-brand-600 transition-colors"
          title="Terapkan Filter"
        >
          <i class="fa-solid fa-filter text-xs"></i>
        </button>
        <a
          href="<?= base_url('sys-admin/menu') ?>"
          class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2.5 px-4 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          title="Reset Filter"
        >
          <i class="fa-solid fa-rotate-left text-xs"></i>
        </a>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-xs font-bold capitalize tracking-normal text-gray-500 dark:text-gray-400">
            <th class="py-4 px-5 w-12 text-center">No.</th>
            <th class="py-4 px-5">Nama Menu</th>
            <th class="py-4 px-5">Link</th>
            <th class="py-4 px-5 text-center">Status</th>
            <th class="py-4 px-5 text-right w-28">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="5" class="py-20 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-4">
                  <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400">
                    <i class="fa-solid fa-bars-staggered text-2xl"></i>
                  </div>
                  <p class="font-semibold text-gray-600 dark:text-gray-300">Tidak ada menu ditemukan</p>
                  <p class="text-sm text-gray-400">Coba ubah kata kunci pencarian atau reset filter di atas.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($rows as $index => $r): ?>
              <tr x-show="isRowVisible(<?= $index ?>)" class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <td class="py-4 px-5 text-center text-gray-400 font-medium"><?= $index + 1 ?></td>

                <!-- Nama Menu -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-300">
                      <i class="<?= esc($r['menu_icon'] ?: 'fa-regular fa-circle') ?> text-sm"></i>
                    </span>
                    <div>
                      <div class="font-semibold text-gray-800 dark:text-gray-100">
                        <?= esc($r['menu_name_idn']) ?>
                        <?php if (! empty($r['parent_name'])): ?>
                          <span class="ml-1 text-[10px] font-bold uppercase tracking-wide text-brand-500 dark:text-brand-400">Sub</span>
                        <?php endif; ?>
                      </div>
                      <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        <?= esc($r['menu_name_eng'] ?: '-') ?>
                        <?php if (! empty($r['parent_name'])): ?>
                          · di bawah <span class="font-semibold text-gray-500 dark:text-gray-400"><?= esc($r['parent_name']) ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Link -->
                <td class="py-4 px-5">
                  <code class="rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-1 text-xs font-mono text-gray-600 dark:text-gray-300">
                    <?= esc($r['menu_link'] ?: '-') ?>
                  </code>
                </td>

                <!-- Status -->
                <td class="py-4 px-5 text-center">
                  <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold <?= ($r['menu_active'] ?? 'Y') === 'Y' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' ?>">
                    <span class="h-1.5 w-1.5 rounded-full <?= ($r['menu_active'] ?? 'Y') === 'Y' ? 'bg-emerald-500' : 'bg-red-500' ?>"></span>
                    <?= ($r['menu_active'] ?? 'Y') === 'Y' ? 'Aktif' : 'Non-Aktif' ?>
                  </span>
                </td>

                <!-- Action -->
                <td class="py-4 px-5">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      type="button"
                      @click="openEditModal(<?= htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8') ?>)"
                      class="h-9 w-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-brand-500 hover:bg-brand-50 hover:border-brand-200 dark:hover:bg-brand-500/10 transition-colors flex items-center justify-center shadow-2xs"
                      title="Edit Menu"
                    >
                      <i class="fa-solid fa-pen text-sm"></i>
                    </button>
                    <button
                      type="button"
                      @click="toggleStatus(<?= (int)$r['menu_id'] ?>, '<?= ($r['menu_active'] ?? 'Y') === 'Y' ? 'nonaktifkan' : 'aktifkan' ?>')"
                      class="h-9 w-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 <?= ($r['menu_active'] ?? 'Y') === 'Y' ? 'text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10' : 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10' ?> transition-colors flex items-center justify-center shadow-2xs"
                      :title="'<?= ($r['menu_active'] ?? 'Y') === 'Y' ? 'Nonaktifkan' : 'Aktifkan' ?> Menu'"
                    >
                      <i class="fa-solid fa-power-off text-sm"></i>
                    </button>
                    <button
                      type="button"
                      @click="deleteMenu(<?= (int)$r['menu_id'] ?>)"
                      class="h-9 w-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-red-600 hover:bg-red-50 hover:border-red-200 dark:hover:bg-red-500/10 transition-colors flex items-center justify-center shadow-2xs"
                      title="Hapus Menu"
                    >
                      <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Table Footer / Pagination -->
    <?php
      $totalRows = count($rows ?? []);
    ?>
    <div class="border-t border-gray-100 dark:border-gray-800 p-5 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
      <div class="flex items-center gap-1.5 text-sm">
        <span>Menampilkan</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalItems === 0 ? 0 : ((currentPage - 1) * perPage + 1)"></span>
        <span>-</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, totalItems)"></span>
        <span>dari</span>
        <span class="font-bold text-gray-800 dark:text-gray-200"><?= number_format($totalRows) ?></span>
        <span>menu</span>
      </div>

      <div class="flex items-center gap-2" x-show="totalPages > 1">
        <button
          type="button"
          @click="prevPage()"
          :disabled="currentPage === 1"
          class="h-9 px-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-sm font-semibold flex items-center gap-2"
        >
          <i class="fa-solid fa-chevron-left text-xs"></i>
          <span class="hidden sm:inline">Sebelumnya</span>
        </button>

        <template x-for="(p, idx) in pageNumbers()" :key="idx">
          <div>
            <template x-if="p === '...'">
              <span class="px-2 py-1 text-gray-400 font-bold">...</span>
            </template>
            <template x-if="p !== '...'">
              <button
                type="button"
                @click="goToPage(p)"
                :class="currentPage === p ? 'bg-brand-500 text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                class="h-9 min-w-[36px] px-2 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center"
                x-text="p"
              ></button>
            </template>
          </div>
        </template>

        <button
          type="button"
          @click="nextPage()"
          :disabled="currentPage === totalPages"
          class="h-9 px-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-sm font-semibold flex items-center gap-2"
        >
          <span class="hidden sm:inline">Berikutnya</span>
          <i class="fa-solid fa-chevron-right text-xs"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- MODAL: TAMBAH / EDIT MENU -->
  <!-- ============================================================ -->
  <div
    x-show="modalOpen"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center p-4"
    style="z-index: 9999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
    @click.self="modalOpen = false"
    x-cloak
  >
    <div
      x-show="modalOpen"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95 translate-y-3"
      x-transition:enter-end="opacity-100 scale-100 translate-y-0"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100 scale-100 translate-y-0"
      x-transition:leave-end="opacity-0 scale-95 translate-y-3"
      class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden"
    >
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
        <div class="flex items-center gap-4">
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400">
            <i class="fa-solid fa-bars-staggered text-lg"></i>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="form.id ? 'Edit Menu' : 'Tambah Menu Baru'"></h3>
            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="form.id ? 'Perbarui detail menu sidebar' : 'Isi formulir untuk mendaftarkan menu baru'"></p>
          </div>
        </div>
        <button
          type="button"
          @click="modalOpen = false"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <!-- Modal Form -->
      <form @submit.prevent="saveMenu()" class="p-6 space-y-6">
        <input type="hidden" x-model="form.id" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Nama Indonesia -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Nama Menu (Indonesia) <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              x-model="form.menu_name_idn"
              required
              placeholder="Contoh: Opex GA"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <!-- Nama Inggris -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Nama Menu (English)
            </label>
            <input
              type="text"
              x-model="form.menu_name_eng"
              placeholder="Contoh: Opex GA"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Icon -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Icon (Font Awesome)
            </label>
            <input
              type="text"
              x-model="form.menu_icon"
              placeholder="Contoh: fa-solid fa-building"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-mono text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <!-- Link -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Link / URL
            </label>
            <input
              type="text"
              x-model="form.menu_link"
              placeholder="Contoh: opex-ga/entry"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-mono text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Parent -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Parent Menu (Sub-menu)
            </label>
            <select
              x-model="form.parent_id"
              @change="syncLevelFromParent()"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <option value="">— Tidak ada (Menu Utama) —</option>
              <?php foreach (($parents ?? []) as $p): ?>
                <option value="<?= (int)$p['menu_id'] ?>"><?= esc($p['menu_name_idn']) ?></option>
              <?php endforeach; ?>
            </select>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Pilih parent untuk menjadikan menu ini sub-menu.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          <!-- Urutan -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Urutan
            </label>
            <input
              type="number"
              x-model="form.menu_order"
              min="0"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <!-- Status -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Status <span class="text-red-500">*</span>
            </label>
            <select
              x-model="form.menu_active"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <option value="Y">Aktif (Y)</option>
              <option value="N">Non-Aktif (N)</option>
            </select>
          </div>

          <!-- Level (otomatis dari parent) -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Level
            </label>
            <input
              type="text"
              disabled
              :value="(form.parent_id || form.menu_level === '2') ? 'Level 2 (Sub-menu)' : 'Level 1 (Parent)'"
              class="w-full rounded-xl border border-gray-200 bg-gray-50/60 px-4 py-3 text-sm font-bold text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 cursor-not-allowed"
            />
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Dihitung otomatis dari pilihan Parent Menu.</p>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="pt-6 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
          <button
            type="button"
            @click="modalOpen = false"
            class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-3 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/40 disabled:opacity-50 transition-colors"
          >
            <i class="fa-solid fa-spinner fa-spin" x-show="saving"></i>
            <span x-text="saving ? 'Menyimpan...' : (form.id ? 'Perbarui Menu' : 'Simpan Menu')"></span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- FLASH TOAST (hasil aksi server-side) -->
  <!-- ============================================================ -->
  <?php if (! empty($flash['success']) || ! empty($flash['error'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        <?php if (! empty($flash['success'])): ?>
          window.showToast('success', <?= json_encode($flash['success']) ?>);
        <?php endif; ?>
        <?php if (! empty($flash['error'])): ?>
          window.showToast('error', <?= json_encode($flash['error']) ?>);
        <?php endif; ?>
      });
    </script>
  <?php endif; ?>

  <!-- Confirm Modal Reusable -->
  <?= $this->include('partials/confirm_modal') ?>

</div>

<!-- ============================================================ -->
<!-- ALPINE COMPONENT SCRIPT -->
<!-- ============================================================ -->
<script>
  function menuPage() {
    return {
      modalOpen: false,
      saving: false,

      // Pagination
      currentPage: 1,
      perPage: 10,
      totalItems: <?= (int)$totalRows ?>,
      get totalPages() {
        return Math.ceil(this.totalItems / this.perPage) || 1;
      },
      isRowVisible(index) {
        return index >= (this.currentPage - 1) * this.perPage && index < this.currentPage * this.perPage;
      },
      goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
          this.currentPage = page;
        }
      },
      prevPage() {
        if (this.currentPage > 1) {
          this.currentPage--;
        }
      },
      nextPage() {
        if (this.currentPage < this.totalPages) {
          this.currentPage++;
        }
      },
      pageNumbers() {
        const total = this.totalPages;
        const current = this.currentPage;
        if (total <= 7) {
          return Array.from({ length: total }, (_, i) => i + 1);
        }
        if (current <= 4) {
          return [1, 2, 3, 4, 5, '...', total];
        }
        if (current >= total - 3) {
          return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
        }
        return [1, '...', current - 1, current, current + 1, '...', total];
      },

      form: {
        id: null,
        menu_name_idn: '',
        menu_name_eng: '',
        menu_name_jpn: '',
        menu_icon: '',
        menu_link: '#',
        parent_id: '',
        menu_level: '1',
        menu_order: '0',
        menu_active: 'Y'
      },

      openCreateModal() {
        this.form = {
          id: null,
          menu_name_idn: '',
          menu_name_eng: '',
          menu_name_jpn: '',
          menu_icon: '',
          menu_link: '#',
          parent_id: '',
          menu_level: '1',
          menu_order: '0',
          menu_active: 'Y'
        };
        this.modalOpen = true;
      },

      openEditModal(row) {
        this.form = {
          id: row.menu_id,
          menu_name_idn: row.menu_name_idn || '',
          menu_name_eng: row.menu_name_eng || '',
          menu_name_jpn: row.menu_name_jpn || '',
          menu_icon: row.menu_icon || '',
          menu_link: row.menu_link || '#',
          parent_id: row.parent_id ? String(row.parent_id) : '',
          menu_level: row.menu_level || '1',
          menu_order: row.menu_order || '0',
          menu_active: row.menu_active || 'Y'
        };
        this.modalOpen = true;
      },

      async saveMenu() {
        this.saving = true;
        try {
          const res = await window.ypFetch('<?= base_url('sys-admin/api/menu/save') ?>', this.form);
          if (res.success) {
            window.showToast('success', res.message || 'Menu berhasil disimpan');
            this.modalOpen = false;
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal menyimpan menu');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan sistem');
        } finally {
          this.saving = false;
        }
      },

      syncLevelFromParent() {
        this.form.menu_level = this.form.parent_id ? '2' : '1';
      },

      toggleStatus(id, actionText) {
        const active = actionText === 'nonaktifkan';
        window.openConfirmDialog({
          title: active ? 'Nonaktifkan Menu' : 'Aktifkan Menu',
          message: active
            ? 'Menu ini tidak akan tampil di sidebar hingga diaktifkan kembali. Lanjutkan?'
            : 'Menu ini akan kembali tampil di sidebar. Lanjutkan?',
          icon: 'fa-power-off',
          tone: active ? 'warning' : 'primary',
          confirmText: active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
          onConfirm: async () => {
            try {
              const res = await window.ypFetch('<?= base_url('sys-admin/api/menu/toggle') ?>', { id });
              if (res.success) {
                window.showToast('success', res.message || 'Status berhasil diubah');
                setTimeout(() => window.location.reload(), 600);
              } else {
                window.showToast('error', res.message || 'Gagal mengubah status');
              }
            } catch (e) {
              window.showToast('error', 'Terjadi kesalahan saat memproses permintaan');
            }
          }
        });
      },

      deleteMenu(id) {
        window.openConfirmDialog({
          title: 'Hapus Menu',
          message: 'Apakah Anda yakin ingin menghapus menu ini? Tindakan ini tidak dapat dibatalkan.',
          icon: 'fa-trash',
          tone: 'danger',
          confirmText: 'Ya, Hapus',
          onConfirm: async () => {
            try {
              const res = await window.ypFetch('<?= base_url('sys-admin/api/menu/delete') ?>', { id });
              if (res.success) {
                window.showToast('success', res.message || 'Menu berhasil dihapus');
                setTimeout(() => window.location.reload(), 600);
              } else {
                window.showToast('error', res.message || 'Gagal menghapus menu');
              }
            } catch (e) {
              window.showToast('error', 'Terjadi kesalahan saat memproses permintaan');
            }
          }
        });
      }
    };
  }
</script>

<?= $this->endSection() ?>
