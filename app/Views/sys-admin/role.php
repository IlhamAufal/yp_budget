<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6" x-data="rolePage()">

  <!-- ============================================================ -->
  <!-- BREADCRUMB & HEADER -->
  <!-- ============================================================ -->
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">
          Dashboard
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span>System Administration</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-[#2F3185] font-bold">Role Management</span>
      </div>
      <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        Role Management
      </h1>
      <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
        Kelola role pengguna dan permission akses menu (RBAC).
      </p>
    </div>

    <!-- Quick Action -->
    <div class="flex flex-wrap items-center gap-3">
      <button
        type="button"
        @click="openCreateModal()"
        class="bg-[#2F3185] hover:bg-[#25276d] text-white font-semibold rounded-xl px-5 py-2.5 shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98] text-xs"
      >
        <i class="fa-solid fa-plus"></i>
        <span>Tambah Role</span>
      </button>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- DATA TABLE & FILTER -->
  <!-- ============================================================ -->
  <div class="space-y-4">
    <!-- Filter Bar -->
    <div class="p-5 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
      <form method="GET" action="<?= base_url('sys-admin/role') ?>" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[240px]">
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pencarian Role</label>
          <input
            type="text"
            name="search"
            value="<?= esc($filters['search'] ?? '') ?>"
            placeholder="Cari nama role..."
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 py-2.5 px-3.5 text-xs text-gray-800 focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white transition-all shadow-xs"
          />
        </div>

        <div class="min-w-[160px]">
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
          <select
            name="status"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 py-2.5 px-3.5 text-xs text-gray-800 focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white transition-colors"
          >
            <option value="">Semua Status</option>
            <option value="Y" <?= ($filters['status'] ?? '') === 'Y' ? 'selected' : '' ?>>Aktif (Y)</option>
            <option value="N" <?= ($filters['status'] ?? '') === 'N' ? 'selected' : '' ?>>Non-Aktif (N)</option>
          </select>
        </div>

        <button
          type="submit"
          class="bg-[#2F3185] hover:bg-[#25276d] text-white font-semibold rounded-xl px-5 py-2.5 shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98] text-xs"
          title="Terapkan Filter"
        >
          <i class="fa-solid fa-filter text-xs"></i>
          <span>Filter</span>
        </button>
        <?php if (! empty($has_filter)): ?>
        <a
          href="<?= base_url('sys-admin/role') ?>"
          class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 py-2.5 px-4 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          title="Reset Filter"
        >
          <i class="fa-solid fa-rotate-left text-xs"></i>
        </a>
        <?php endif; ?>
      </form>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
            <tr class="bg-[#2F3185] text-white font-semibold">
              <th class="py-3 px-5 w-12 text-center text-white font-semibold">No.</th>
              <th class="py-3 px-5 text-white font-semibold">Nama Role</th>
              <th class="py-3 px-5 text-center text-white font-semibold">Jumlah User</th>
              <th class="py-3 px-5 text-center text-white font-semibold">Status</th>
              <th class="py-3 px-5 text-right w-44 text-white font-semibold">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs">
            <?php if (empty($rows)): ?>
              <tr>
                <td colspan="5" class="py-20 text-center text-gray-400 dark:text-gray-500 font-sans">
                  <div class="flex flex-col items-center justify-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                      <i class="fa-solid fa-user-shield text-xl"></i>
                    </div>
                    <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">Tidak ada role ditemukan</p>
                    <p class="text-xs text-gray-400">Coba ubah kata kunci pencarian atau reset filter di atas.</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($rows as $index => $r): ?>
                <tr x-show="isRowVisible(<?= $index ?>)" class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                  <td class="py-3.5 px-5 text-center text-gray-400 font-medium"><?= $index + 1 ?></td>

                  <!-- Nama Role -->
                  <td class="py-3.5 px-5">
                    <div class="flex items-center gap-3">
                      <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#2F3185]/10 text-[#2F3185] dark:bg-[#2F3185]/20 dark:text-indigo-400">
                        <i class="fa-solid fa-user-tag text-xs"></i>
                      </span>
                      <div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100"><?= esc($r['role_name_idn']) ?></div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"><?= esc($r['role_name_eng'] ?: '-') ?></div>
                      </div>
                    </div>
                  </td>

                  <!-- Jumlah User -->
                  <td class="py-3.5 px-5 text-center">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                      <i class="fa-solid fa-users text-[10px]"></i>
                      <?= (int)($r['user_count'] ?? 0) ?>
                    </span>
                  </td>

                  <!-- Status -->
                  <td class="py-3.5 px-5 text-center">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold <?= ($r['role_active'] ?? 'Y') === 'Y' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' ?>">
                      <span class="h-1.5 w-1.5 rounded-full <?= ($r['role_active'] ?? 'Y') === 'Y' ? 'bg-emerald-500' : 'bg-red-500' ?>"></span>
                      <?= ($r['role_active'] ?? 'Y') === 'Y' ? 'Aktif' : 'Non-Aktif' ?>
                    </span>
                  </td>

                  <!-- Action -->
                  <td class="py-3.5 px-5">
                    <div class="flex items-center justify-end gap-2">
                      <button
                        type="button"
                        @click="openPermissionModal(<?= (int)$r['role_id'] ?>, <?= htmlspecialchars(json_encode($r['role_name_idn']), ENT_QUOTES, 'UTF-8') ?>)"
                        class="h-8 w-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#2F3185] hover:bg-[#2F3185]/10 dark:hover:bg-[#2F3185]/20 dark:text-indigo-400 transition-colors flex items-center justify-center shadow-2xs"
                        title="Atur Permission Menu (<?= (int)($r['menu_count'] ?? 0) ?> menu)"
                      >
                        <i class="fa-solid fa-list-check text-xs"></i>
                      </button>
                      <button
                        type="button"
                        @click="openEditModal(<?= htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8') ?>)"
                        class="h-8 w-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#2F3185] hover:bg-[#2F3185]/10 dark:hover:bg-[#2F3185]/20 dark:text-indigo-400 transition-colors flex items-center justify-center shadow-2xs"
                        title="Edit Role"
                      >
                        <i class="fa-solid fa-pen text-xs"></i>
                      </button>
                      <button
                        type="button"
                        @click="toggleStatus(<?= (int)$r['role_id'] ?>, '<?= ($r['role_active'] ?? 'Y') === 'Y' ? 'nonaktifkan' : 'aktifkan' ?>')"
                        class="h-8 w-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 <?= ($r['role_active'] ?? 'Y') === 'Y' ? 'text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10' : 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10' ?> transition-colors flex items-center justify-center shadow-2xs"
                        title="Aktif / Nonaktifkan"
                      >
                        <i class="fa-solid fa-power-off text-xs"></i>
                      </button>
                      <button
                        type="button"
                        @click="deleteRole(<?= (int)$r['role_id'] ?>)"
                        class="h-8 w-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-red-600 hover:bg-red-50 hover:border-red-200 dark:hover:bg-red-500/10 transition-colors flex items-center justify-center shadow-2xs"
                        title="Hapus Role"
                      >
                        <i class="fa-solid fa-trash text-xs"></i>
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
        $pages   = max(1, (int) ceil(($total ?? 0) / max(1, $perPage ?? 10)));
        $from    = ($total ?? 0) > 0 ? (($page - 1) * $perPage + 1) : 0;
        $to      = min($page * $perPage, $total ?? 0);
        $winStart = max(1, min($page - 2, max(1, $pages - 4)));
        $winEnd   = min($pages, $winStart + 4);
      ?>
      <?php if (($total ?? 0) > 0): ?>
      <div class="border-t border-gray-200 dark:border-gray-800 p-4 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
        <span>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200"><?= $from ?></span> - <span class="font-bold text-gray-800 dark:text-gray-200"><?= $to ?></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= number_format($total) ?></span> role</span>
        <div class="flex items-center gap-1.5">
          <a href="?page=<?= max(1, $page - 1) ?>" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 <?= $page <= 1 ? 'opacity-40 pointer-events-none' : '' ?> transition-colors text-xs font-semibold flex items-center gap-1">Prev</a>
          <?php for ($p = $winStart; $p <= $winEnd; $p++): ?>
            <a href="?page=<?= $p ?>" class="h-8 min-w-[32px] px-2 rounded-lg text-xs font-semibold transition-colors flex items-center justify-center <?= $p === $page ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' ?>"><?= $p ?></a>
          <?php endfor; ?>
          <a href="?page=<?= min($pages, $page + 1) ?>" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 <?= $page >= $pages ? 'opacity-40 pointer-events-none' : '' ?> transition-colors text-xs font-semibold flex items-center gap-1">Next</a>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- MODAL: TAMBAH / EDIT ROLE -->
  <!-- ============================================================ -->
  <template x-teleport="body">
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
      class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden"
    >
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
        <div>
          <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="form.id ? 'Edit Role' : 'Tambah Role Baru'"></h3>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="form.id ? 'Perbarui detail role' : 'Isi formulir untuk mendaftarkan role baru'"></p>
        </div>
        <button
          type="button"
          @click="modalOpen = false"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>
      </div>

      <!-- Modal Form -->
      <form @submit.prevent="saveRole()" class="p-6 space-y-4">
        <input type="hidden" x-model="form.id" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
              Nama Role (Indonesia) <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              x-model="form.role_name_idn"
              required
              placeholder="Contoh: Supervisor Budget"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-medium text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
              Nama Role (English)
            </label>
            <input
              type="text"
              x-model="form.role_name_eng"
              placeholder="Contoh: Budget Supervisor"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
              Status <span class="text-red-500">*</span>
            </label>
            <select
              x-model="form.role_active"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <option value="Y">Aktif (Y)</option>
              <option value="N">Non-Aktif (N)</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
              Tipe Role
            </label>
            <select
              x-model="form.role_type"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <option value="menu">Menu</option>
              <option value="object">Object</option>
            </select>
          </div>
        </div>

        <div class="rounded-xl bg-[#2F3185]/5 dark:bg-[#2F3185]/10 border border-[#2F3185]/20 p-3.5 text-xs text-[#2F3185] dark:text-indigo-300 flex items-start gap-2.5">
          <i class="fa-solid fa-circle-info text-[#2F3185] dark:text-indigo-400 mt-0.5 shrink-0"></i>
          <span>Setelah role dibuat, atur <strong>permission menu</strong> melalui tombol action permission pada baris role tersebut.</span>
        </div>

        <!-- Form Actions -->
        <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
          <button
            type="button"
            @click="modalOpen = false"
            class="px-5 py-2.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="bg-[#2F3185] hover:bg-[#25276d] text-white font-semibold rounded-xl px-5 py-2.5 shadow-xs transition-all inline-flex items-center justify-center gap-2 active:scale-[0.98] text-xs disabled:opacity-50"
          >
            <i class="fa-solid fa-spinner fa-spin" x-show="saving"></i>
            <span x-text="saving ? 'Menyimpan...' : (form.id ? 'Perbarui Role' : 'Simpan Role')"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
  </template>

  <!-- ============================================================ -->
  <!-- MODAL: PERMISSION MENU -->
  <!-- ============================================================ -->
  <template x-teleport="body">
  <div
    x-show="permModalOpen"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center p-4"
    style="z-index: 9999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
    @click.self="permModalOpen = false"
    x-cloak
  >
    <div
      x-show="permModalOpen"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95 translate-y-3"
      x-transition:enter-end="opacity-100 scale-100 translate-y-0"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100 scale-100 translate-y-0"
      x-transition:leave-end="opacity-0 scale-95 translate-y-3"
      class="relative w-full max-w-3xl rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden"
    >
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
        <div>
          <h3 class="text-base font-bold text-gray-900 dark:text-white">
            Permission Menu — <span x-text="permForm.roleName" class="text-[#2F3185] dark:text-indigo-400"></span>
          </h3>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Centang menu yang boleh diakses oleh role ini.</p>
        </div>
        <button
          type="button"
          @click="permModalOpen = false"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="p-6">
        <!-- Toolbar -->
        <div class="flex items-center justify-between mb-4">
          <p class="text-xs text-gray-500 dark:text-gray-400">
            <span class="font-bold text-gray-800 dark:text-gray-200" x-text="selectedMenuCount()"></span> menu terpilih dari <?= count($menus ?? []) ?> menu
          </p>
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="selectAllMenus()"
              class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <i class="fa-solid fa-check-double text-[10px]"></i> Pilih Semua
            </button>
            <button
              type="button"
              @click="clearAllMenus()"
              class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <i class="fa-solid fa-eraser text-[10px]"></i> Kosongkan
            </button>
          </div>
        </div>

        <!-- Menu Groups -->
        <div class="max-h-[420px] overflow-y-auto space-y-5 pr-2 -mr-2">
          <?php
            // Kolom menu_group tidak selalu ada di skema legacy → fallback 'TANPA GRUP'.
            $grouped = [];
            foreach (($menus ?? []) as $m) {
                $grouped[($m['menu_group'] ?? '') ?: 'TANPA GRUP'][] = $m;
            }
          ?>
          <?php foreach ($grouped as $group => $menuList): ?>
            <div>
              <div class="flex items-center justify-between mb-2.5">
                <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                  <i class="fa-solid fa-layer-group text-xs text-[#2F3185] dark:text-indigo-400"></i> <?= esc($group) ?>
                </h4>
                <span class="text-[11px] font-semibold text-gray-400 dark:text-gray-500"><?= count($menuList) ?> menu</span>
              </div>
              <div class="space-y-1.5">
                <?php foreach ($menuList as $m): ?>
                  <?php if (($m['menu_level'] ?? '1') === '1'): ?>
                    <!-- Parent -->
                    <label class="flex items-start gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-800/50 p-3 cursor-pointer hover:border-[#2F3185]/40 dark:hover:border-[#2F3185]/60 transition-colors">
                      <input
                        type="checkbox"
                        class="mt-0.5 h-4 w-4 rounded border-gray-300 text-[#2F3185] focus:ring-[#2F3185]/30"
                        :checked="permForm.menuIds.includes(<?= (int)$m['menu_id'] ?>)"
                        @change="toggleMenu(<?= (int)$m['menu_id'] ?>, $event.target.checked)"
                      />
                      <span class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-300">
                          <i class="<?= esc($m['menu_icon'] ?: 'fa-regular fa-circle') ?> text-xs"></i>
                        </span>
                        <span>
                          <span class="block text-xs font-bold text-gray-800 dark:text-gray-100"><?= esc($m['menu_name_idn']) ?></span>
                          <span class="block text-[11px] text-gray-400 dark:text-gray-500"><?= esc($m['menu_name_eng'] ?: '-') ?></span>
                        </span>
                      </span>
                    </label>

                    <!-- Children -->
                    <?php foreach ($menuList as $c): ?>
                      <?php if (($c['menu_level'] ?? '1') === '2' && ($c['parent_id'] ?? null) == $m['menu_id']): ?>
                        <label class="flex items-start gap-3 rounded-xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 p-2.5 pl-8 ml-5 cursor-pointer hover:border-[#2F3185]/30 dark:hover:border-[#2F3185]/50 transition-colors">
                          <input
                            type="checkbox"
                            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-[#2F3185] focus:ring-[#2F3185]/30"
                            :checked="permForm.menuIds.includes(<?= (int)$c['menu_id'] ?>)"
                            @change="toggleMenu(<?= (int)$c['menu_id'] ?>, $event.target.checked)"
                          />
                          <span>
                            <span class="block text-xs font-semibold text-gray-700 dark:text-gray-200">
                              <i class="fa-solid fa-angle-right text-[10px] text-gray-400 mr-1"></i><?= esc($c['menu_name_idn']) ?>
                            </span>
                            <span class="block text-[11px] text-gray-400 dark:text-gray-500 font-mono"><?= esc($c['menu_link'] ?: '-') ?></span>
                          </span>
                        </label>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Modal Footer -->
        <div class="pt-4 flex items-center justify-between gap-3 border-t border-gray-100 dark:border-gray-800 mt-4">
          <p class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1.5">
            <i class="fa-solid fa-shield-halved text-gray-400"></i>
            Menyimpan permission akan menggantikan konfigurasi sebelumnya.
          </p>
          <div class="flex items-center gap-3">
            <button
              type="button"
              @click="permModalOpen = false"
              class="px-5 py-2.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 transition-colors"
            >
              Batal
            </button>
            <button
              type="button"
              @click="saveMenus()"
              :disabled="permSaving"
              class="bg-[#2F3185] hover:bg-[#25276d] text-white font-semibold rounded-xl px-5 py-2.5 shadow-xs transition-all inline-flex items-center justify-center gap-2 active:scale-[0.98] text-xs disabled:opacity-50"
            >
              <i class="fa-solid fa-spinner fa-spin" x-show="permSaving"></i>
              <span x-text="permSaving ? 'Menyimpan...' : 'Simpan Permission'"></span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  </template>

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


</div>

<!-- ============================================================ -->
<!-- ALPINE COMPONENT SCRIPT -->
<!-- ============================================================ -->
<script>
  function rolePage() {
    return {
      modalOpen: false,
      permModalOpen: false,
      saving: false,
      permSaving: false,

      // Pagination
      currentPage: 1,
      perPage: 10,
      totalItems: <?= (int) ($total ?? 0) ?>,
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
        role_name_idn: '',
        role_name_eng: '',
        role_name_jpn: '',
        role_active: 'Y',
        role_type: 'menu'
      },

      // Permission
      permForm: {
        roleId: null,
        roleName: '',
        menuIds: []
      },

      openCreateModal() {
        this.form = {
          id: null,
          role_name_idn: '',
          role_name_eng: '',
          role_name_jpn: '',
          role_active: 'Y',
          role_type: 'menu'
        };
        this.modalOpen = true;
      },

      openEditModal(row) {
        this.form = {
          id: row.role_id,
          role_name_idn: row.role_name_idn || '',
          role_name_eng: row.role_name_eng || '',
          role_name_jpn: row.role_name_jpn || '',
          role_active: row.role_active || 'Y',
          role_type: row.role_type || 'menu'
        };
        this.modalOpen = true;
      },

      async saveRole() {
        this.saving = true;
        try {
          const res = await window.ypFetch('<?= base_url('sys-admin/api/role/save') ?>', this.form);
          if (res.success) {
            window.showToast('success', res.message || 'Role berhasil disimpan');
            this.modalOpen = false;
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal menyimpan role');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan sistem');
        } finally {
          this.saving = false;
        }
      },

      toggleStatus(id, actionText) {
        const active = actionText === 'nonaktifkan';
        window.openConfirmDialog({
          title: active ? 'Nonaktifkan Role' : 'Aktifkan Role',
          message: active
            ? 'Role ini tidak dapat digunakan oleh user hingga diaktifkan kembali. Lanjutkan?'
            : 'Role ini kembali dapat digunakan oleh user. Lanjutkan?',
          icon: 'fa-power-off',
          tone: active ? 'warning' : 'primary',
          confirmText: active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
          onConfirm: async () => {
            try {
              const res = await window.ypFetch('<?= base_url('sys-admin/api/role/toggle') ?>', { id });
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

      deleteRole(id) {
        window.openConfirmDialog({
          title: 'Hapus Role',
          message: 'Apakah Anda yakin ingin menghapus role ini? Seluruh user dengan role ini akan kehilangan aksesnya.',
          icon: 'fa-trash',
          tone: 'danger',
          confirmText: 'Ya, Hapus',
          onConfirm: async () => {
            try {
              const res = await window.ypFetch('<?= base_url('sys-admin/api/role/delete') ?>', { id });
              if (res.success) {
                window.showToast('success', res.message || 'Role berhasil dihapus');
                setTimeout(() => window.location.reload(), 600);
              } else {
                window.showToast('error', res.message || 'Gagal menghapus role');
              }
            } catch (e) {
              window.showToast('error', 'Terjadi kesalahan saat memproses permintaan');
            }
          }
        });
      },

      /* ---------------- Permission Menu ---------------- */

      async openPermissionModal(roleId, roleName) {
        this.permForm.roleId = roleId;
        this.permForm.roleName = roleName;
        this.permForm.menuIds = [];

        try {
          const res = await window.ypFetch('<?= base_url('sys-admin/api/role/menus') ?>', { role_id: roleId });
          if (res.success) {
            this.permForm.menuIds = res.menu_ids || [];
          } else {
            window.showToast('error', res.message || 'Gagal memuat permission menu');
            return;
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan saat memuat permission menu');
          return;
        }

        this.permModalOpen = true;
      },

      toggleMenu(menuId, checked) {
        if (checked) {
          if (!this.permForm.menuIds.includes(menuId)) {
            this.permForm.menuIds.push(menuId);
          }
        } else {
          this.permForm.menuIds = this.permForm.menuIds.filter(id => id !== menuId);
        }
      },

      selectedMenuCount() {
        return this.permForm.menuIds.length;
      },

      selectAllMenus() {
        this.permForm.menuIds = <?= json_encode(array_map(fn($m) => (int) $m['menu_id'], $menus ?? [])) ?>;
      },

      clearAllMenus() {
        this.permForm.menuIds = [];
      },

      async saveMenus() {
        this.permSaving = true;
        try {
          const body = new URLSearchParams();
          body.append('role_id', this.permForm.roleId);
          this.permForm.menuIds.forEach(id => body.append('menu_ids[]', id));

          const res = await window.ypFetch('<?= base_url('sys-admin/api/role/menus/save') ?>', body);
          if (res.success) {
            window.showToast('success', res.message || 'Permission menu berhasil disimpan');
            this.permModalOpen = false;
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal menyimpan permission menu');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan saat menyimpan permission');
        } finally {
          this.permSaving = false;
        }
      }
    };
  }
</script>

<?= $this->endSection() ?>
