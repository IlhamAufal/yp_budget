<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 md:p-8 mx-auto max-w-(--breakpoint-2xl) space-y-6 md:space-y-8" x-data="userPage()">

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
        <span class="text-brand-500 font-bold">User Management</span>
      </div>
      <h1 class="text-2xl md:text-3xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-4">
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400">
          <i class="fa-solid fa-users-gear text-xl"></i>
        </span>
        User Management
      </h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
        Kelola akun pengguna, penugasan role, dan hak akses sistem.
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
        <span>Tambah User</span>
      </button>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- DATA TABLE & FILTER -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <!-- Filter Bar -->
    <div class="p-5 border-b border-gray-100 dark:border-gray-800">
      <form method="GET" action="<?= base_url('sys-admin/user') ?>" class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[240px]">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
            <i class="fa-solid fa-magnifying-glass text-xs text-gray-400 dark:text-gray-500"></i>
          </div>
          <input
            type="text"
            name="search"
            value="<?= esc($filters['search'] ?? '') ?>"
            placeholder="Cari username, nama, atau email..."
            class="w-full rounded-xl border border-gray-200/80 bg-gray-50/60 py-2.5 pl-10 pr-4 text-xs md:text-sm font-medium text-gray-800 placeholder:text-gray-400/80 focus:border-brand-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700/80 dark:bg-gray-800/80 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-brand-400 transition-all duration-200 shadow-xs"
          />
        </div>

        <select
          name="role_id"
          class="rounded-xl border border-gray-200 bg-gray-50/50 py-2.5 px-4 text-sm min-w-[160px] focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
        >
          <option value="">Semua Role</option>
          <?php foreach (($roles ?? []) as $rl): ?>
            <option value="<?= (int)$rl['role_id'] ?>" <?= (string)($filters['role_id'] ?? '') === (string)$rl['role_id'] ? 'selected' : '' ?>>
              <?= esc($rl['role_name_idn']) ?>
            </option>
          <?php endforeach; ?>
        </select>

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
          href="<?= base_url('sys-admin/user') ?>"
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
            <th class="py-4 px-5">User</th>
            <th class="py-4 px-5">Role</th>
            <th class="py-4 px-5 text-center">Status</th>
            <th class="py-4 px-5 text-right w-44">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="5" class="py-20 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-4">
                  <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400">
                    <i class="fa-solid fa-users-gear text-2xl"></i>
                  </div>
                  <p class="font-semibold text-gray-600 dark:text-gray-300">Tidak ada user ditemukan</p>
                  <p class="text-sm text-gray-400">Coba ubah kata kunci pencarian atau reset filter di atas.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($rows as $index => $r): ?>
              <?php
                $userRoleIds = array_filter(array_map('intval', explode(',', $r['role_ids'] ?? '')));
              ?>
              <tr x-show="isRowVisible(<?= $index ?>)" class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <td class="py-4 px-5 text-center text-gray-400 font-medium"><?= $index + 1 ?></td>

                <!-- User -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400 font-bold">
                      <?= esc(strtoupper(substr($r['user_name'] ?? $r['user_username'], 0, 1))) ?>
                    </span>
                    <div>
                      <div class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <?= esc($r['user_name']) ?>
                        <?php if ((int) session()->get('user_id') === (int) $r['user_id']): ?>
                          <span class="rounded-md bg-brand-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-brand-500 dark:bg-brand-500/10 dark:text-brand-400">Anda</span>
                        <?php endif; ?>
                      </div>
                      <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        <span class="font-mono">@<?= esc($r['user_username']) ?></span>
                        <?php if (! empty($r['user_email'])): ?>
                          <span>· <?= esc($r['user_email']) ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Role (mahkota bila role admin / user_admin) -->
                <td class="py-4 px-5">
                  <?php
                    $isAdminFlag = (($r['user_admin'] ?? 'N') === 'Y');
                  ?>
                  <?php if (empty($userRoleIds)): ?>
                    <?php if ($isAdminFlag): ?>
                      <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                        <i class="fa-solid fa-crown text-[10px]"></i> Admin
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                        <i class="fa-solid fa-user-slash text-[10px]"></i>
                        Tanpa Role
                      </span>
                    <?php endif; ?>
                  <?php else: ?>
                    <div class="flex flex-wrap items-center gap-1.5">
                      <?php foreach ($userRoleIds as $rid): ?>
                        <?php
                          $roleName = '';
                          foreach (($roles ?? []) as $rl) {
                              if ((int) $rl['role_id'] === $rid) {
                                  $roleName = $rl['role_name_idn'];
                                  break;
                              }
                          }
                          $isAdminRole = $isAdminFlag || (stripos($roleName, 'admin') !== false);
                        ?>
                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold <?= $isAdminRole ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400' ?>">
                          <?php if ($isAdminRole): ?>
                            <i class="fa-solid fa-crown text-[10px]"></i>
                          <?php else: ?>
                            <i class="fa-solid fa-user-tag text-[10px]"></i>
                          <?php endif; ?>
                          <?= esc($roleName ?: 'Role #' . $rid) ?>
                        </span>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </td>

                <!-- Status -->
                <td class="py-4 px-5 text-center">
                  <?php
                    $isBlocked = ($r['user_block'] ?? 'N') === 'Y';
                    $isActive  = ($r['user_active'] ?? 'Y') === 'Y';
                  ?>
                  <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold <?= $isBlocked ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : ($isActive ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400') ?>">
                    <span class="h-1.5 w-1.5 rounded-full <?= $isBlocked ? 'bg-red-500' : ($isActive ? 'bg-emerald-500' : 'bg-gray-400') ?>"></span>
                    <?= $isBlocked ? 'Diblokir' : ($isActive ? 'Aktif' : 'Non-Aktif') ?>
                  </span>
                </td>

                <!-- Action -->
                <td class="py-4 px-5">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      type="button"
                      @click="openResetModal(<?= (int)$r['user_id'] ?>, <?= htmlspecialchars(json_encode($r['user_name']), ENT_QUOTES, 'UTF-8') ?>)"
                      class="h-9 w-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors flex items-center justify-center shadow-2xs"
                      title="Reset Password"
                    >
                      <i class="fa-solid fa-key text-sm"></i>
                    </button>
                    <button
                      type="button"
                      @click="openEditModal(<?= htmlspecialchars(json_encode([
                        'user_id'      => (int) $r['user_id'],
                        'user_username'=> $r['user_username'],
                        'user_name'    => $r['user_name'],
                        'user_email'   => $r['user_email'],
                        'user_active'  => $r['user_active'],
                        'user_admin'   => $r['user_admin'],
                        'user_block'   => $r['user_block'],
                        'role_ids'     => array_values($userRoleIds),
                      ]), ENT_QUOTES, 'UTF-8') ?>)"
                      class="h-9 w-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-brand-500 hover:bg-brand-50 hover:border-brand-200 dark:hover:bg-brand-500/10 transition-colors flex items-center justify-center shadow-2xs"
                      title="Edit User"
                    >
                      <i class="fa-solid fa-pen text-sm"></i>
                    </button>
                    <button
                      type="button"
                      @click="toggleStatus(<?= (int)$r['user_id'] ?>, '<?= ($r['user_active'] ?? 'Y') === 'Y' ? 'nonaktifkan' : 'aktifkan' ?>')"
                      class="h-9 w-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 <?= ($r['user_active'] ?? 'Y') === 'Y' ? 'text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10' : 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10' ?> transition-colors flex items-center justify-center shadow-2xs"
                      title="Aktif / Nonaktifkan"
                    >
                      <i class="fa-solid fa-power-off text-sm"></i>
                    </button>
                    <button
                      type="button"
                      @click="deleteUser(<?= (int)$r['user_id'] ?>)"
                      class="h-9 w-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-red-600 hover:bg-red-50 hover:border-red-200 dark:hover:bg-red-500/10 transition-colors flex items-center justify-center shadow-2xs"
                      title="Hapus User"
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
        <span>user</span>
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
  <!-- MODAL: TAMBAH / EDIT USER -->
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
            <i class="fa-solid fa-users-gear text-lg"></i>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="form.id ? 'Edit User' : 'Tambah User Baru'"></h3>
            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="form.id ? 'Perbarui detail akun pengguna' : 'Isi formulir untuk mendaftarkan pengguna baru'"></p>
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
      <form @submit.prevent="saveUser()" class="p-6 space-y-6">
        <input type="hidden" x-model="form.id" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Username <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              x-model="form.user_username"
              required
              maxlength="10"
              placeholder="Contoh: budi01"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-mono font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              x-model="form.user_name"
              required
              placeholder="Contoh: Budi Santoso"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Email
            </label>
            <input
              type="email"
              x-model="form.user_email"
              placeholder="Contoh: budi@company.com"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Password <span class="text-red-500" x-show="!form.id">*</span>
              <span class="normal-case font-medium" x-show="form.id">(kosongkan jika tidak diubah)</span>
            </label>
            <input
              type="password"
              x-model="form.user_password"
              :required="!form.id"
              minlength="6"
              placeholder="<?= 'Minimal 6 karakter' ?>"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-mono text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            />
          </div>
        </div>

        <!-- Role Assignment (satu role per user) -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
            Role Pengguna <span class="text-red-500">*</span>
          </label>
          <div class="relative" @click.outside="roleDropdownOpen = false">
            <!-- Trigger -->
            <button
              type="button"
              @click="roleDropdownOpen = !roleDropdownOpen"
              class="w-full flex items-center justify-between gap-3 rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-left font-semibold text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <span class="flex items-center gap-2.5">
                <i class="fa-solid fa-user-tag text-gray-400"></i>
                <span x-text="roleLabel()" :class="form.role_id ? '' : 'text-gray-400 dark:text-gray-500'"></span>
              </span>
              <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="roleDropdownOpen ? 'rotate-180' : ''"></i>
            </button>

            <!-- Dropdown + Search (search muncul saat dropdown dibuka) -->
            <div
              x-show="roleDropdownOpen"
              x-transition:enter="transition ease-out duration-150"
              x-transition:enter-start="opacity-0 scale-95"
              x-transition:enter-end="opacity-100 scale-100"
              x-transition:leave="transition ease-in duration-100"
              x-transition:leave-start="opacity-100 scale-100"
              x-transition:leave-end="opacity-0 scale-95"
              class="absolute z-20 mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-xl overflow-hidden"
            >
              <div class="relative border-b border-gray-100 dark:border-gray-800">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none"></i>
                <input
                  type="text"
                  x-model="roleSearch"
                  placeholder="Cari role..."
                  class="w-full bg-transparent py-3 pl-10 pr-4 text-sm text-gray-800 dark:text-white placeholder:text-gray-400 focus:outline-none"
                />
              </div>
              <div class="max-h-56 overflow-y-auto py-1.5">
                <template x-for="role in filteredRoles()" :key="role.role_id">
                  <button
                    type="button"
                    @click="pickRole(role.role_id)"
                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 text-sm text-left transition-colors"
                    :class="String(form.role_id) === String(role.role_id) ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                  >
                    <span class="flex items-center gap-2.5">
                      <i class="fa-solid fa-user-tag text-xs text-gray-400"></i>
                      <span x-text="role.role_name_idn"></span>
                    </span>
                    <i class="fa-solid fa-check text-xs" x-show="String(form.role_id) === String(role.role_id)"></i>
                  </button>
                </template>
                <p x-show="filteredRoles().length === 0" class="px-4 py-6 text-center text-xs text-gray-400">Tidak ada role yang cocok.</p>
              </div>
            </div>
          </div>
          <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Pilih satu role untuk pengguna ini.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <!-- Status -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Status <span class="text-red-500">*</span>
            </label>
            <select
              x-model="form.user_active"
              required
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <option value="Y">Aktif (Y)</option>
              <option value="N">Non-Aktif (N)</option>
            </select>
          </div>

          <!-- Block -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
              Blokir Akun
            </label>
            <select
              x-model="form.user_block"
              class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
            >
              <option value="N">Tidak (N)</option>
              <option value="Y">Ya (Y)</option>
            </select>
          </div>
        </div>

        <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 p-4 text-sm text-amber-800 dark:text-amber-300 flex items-start gap-3">
          <i class="fa-solid fa-circle-info text-amber-500 mt-0.5 shrink-0"></i>
          <span>Perubahan role baru berlaku saat pengguna <strong>login ulang</strong>.</span>
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
            <span x-text="saving ? 'Menyimpan...' : (form.id ? 'Perbarui User' : 'Simpan User')"></span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- MODAL: RESET PASSWORD -->
  <!-- ============================================================ -->
  <div
    x-show="resetModalOpen"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center p-4"
    style="z-index: 9999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
    @click.self="resetModalOpen = false"
    x-cloak
  >
    <div
      x-show="resetModalOpen"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95 translate-y-3"
      x-transition:enter-end="opacity-100 scale-100 translate-y-0"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100 scale-100 translate-y-0"
      x-transition:leave-end="opacity-0 scale-95 translate-y-3"
      class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden"
    >
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
        <div class="flex items-center gap-4">
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
            <i class="fa-solid fa-key text-lg"></i>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Reset Password</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Atur ulang kata sandi untuk <span x-text="resetForm.userName" class="font-semibold text-gray-700 dark:text-gray-200"></span></p>
          </div>
        </div>
        <button
          type="button"
          @click="resetModalOpen = false"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <!-- Reset Form -->
      <form @submit.prevent="doResetPassword()" class="p-6 space-y-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
            Password Baru <span class="text-red-500">*</span>
          </label>
          <input
            type="password"
            x-model="resetForm.user_password"
            required
            minlength="6"
            placeholder="Minimal 6 karakter"
            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-mono text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
          />
        </div>

        <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 p-4 text-sm text-amber-800 dark:text-amber-300 flex items-start gap-3">
          <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 shrink-0"></i>
          <span>Pengguna harus menggunakan password baru ini saat login berikutnya.</span>
        </div>

        <!-- Form Actions -->
        <div class="pt-4 flex items-center justify-end gap-3">
          <button
            type="button"
            @click="resetModalOpen = false"
            class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-3 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="resetting"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500/40 disabled:opacity-50 transition-colors"
          >
            <i class="fa-solid fa-spinner fa-spin" x-show="resetting"></i>
            <span x-text="resetting ? 'Memproses...' : 'Reset Password'"></span>
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
  function userPage() {
    return {
      modalOpen: false,
      resetModalOpen: false,
      saving: false,
      resetting: false,

      roleOptions: <?= json_encode(array_map(fn($rl) => ['role_id' => (int) $rl['role_id'], 'role_name_idn' => $rl['role_name_idn']], $roles ?? [])) ?>,

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
        user_username: '',
        user_name: '',
        user_email: '',
        user_password: '',
        user_active: 'Y',
        user_block: 'N',
        role_id: ''
      },

      // Dropdown role (searchable)
      roleDropdownOpen: false,
      roleSearch: '',

      resetForm: {
        userId: null,
        userName: '',
        user_password: ''
      },

      openCreateModal() {
        this.form = {
          id: null,
          user_username: '',
          user_name: '',
          user_email: '',
          user_password: '',
          user_active: 'Y',
          user_block: 'N',
          role_id: ''
        };
        this.roleSearch = '';
        this.modalOpen = true;
      },

      openEditModal(row) {
        this.form = {
          id: row.user_id,
          user_username: row.user_username || '',
          user_name: row.user_name || '',
          user_email: row.user_email || '',
          user_password: '',
          user_active: row.user_active || 'Y',
          user_block: row.user_block || 'N',
          // Single role: ambil role pertama milik user
          role_id: Array.isArray(row.role_ids) && row.role_ids.length ? String(row.role_ids[0]) : ''
        };
        this.roleSearch = '';
        this.modalOpen = true;
      },

      roleLabel() {
        if (!this.form.role_id) {
          return '— Pilih Role —';
        }
        const found = this.roleOptions.find(r => String(r.role_id) === String(this.form.role_id));
        return found ? found.role_name_idn : 'Role #' + this.form.role_id;
      },

      filteredRoles() {
        const q = (this.roleSearch || '').toLowerCase().trim();
        if (!q) {
          return this.roleOptions;
        }
        return this.roleOptions.filter(r =>
          String(r.role_name_idn || '').toLowerCase().includes(q)
        );
      },

      pickRole(id) {
        this.form.role_id = String(id);
        this.roleDropdownOpen = false;
        this.roleSearch = '';
      },

      async saveUser() {
        this.saving = true;
        try {
          const body = new URLSearchParams();
          body.append('has_role_ids', '1');
          for (const [key, value] of Object.entries(this.form)) {
            if (value !== null && value !== undefined && value !== '') {
              body.append(key, value);
            }
          }
          if (this.form.role_id) {
            body.append('role_ids[]', this.form.role_id);
          }
          const res = await window.ypFetch('<?= base_url('sys-admin/api/user/save') ?>', body);
          if (res.success) {
            window.showToast('success', res.message || 'User berhasil disimpan');
            this.modalOpen = false;
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal menyimpan user');
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
          title: active ? 'Nonaktifkan User' : 'Aktifkan User',
          message: active
            ? 'User ini tidak dapat login hingga diaktifkan kembali. Lanjutkan?'
            : 'User ini kembali dapat login ke sistem. Lanjutkan?',
          icon: 'fa-power-off',
          tone: active ? 'warning' : 'primary',
          confirmText: active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
          onConfirm: async () => {
            try {
              const res = await window.ypFetch('<?= base_url('sys-admin/api/user/toggle') ?>', { id });
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

      deleteUser(id) {
        window.openConfirmDialog({
          title: 'Hapus User',
          message: 'Apakah Anda yakin ingin menghapus user ini? Seluruh data role-nya akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.',
          icon: 'fa-trash',
          tone: 'danger',
          confirmText: 'Ya, Hapus',
          onConfirm: async () => {
            try {
              const res = await window.ypFetch('<?= base_url('sys-admin/api/user/delete') ?>', { id });
              if (res.success) {
                window.showToast('success', res.message || 'User berhasil dihapus');
                setTimeout(() => window.location.reload(), 600);
              } else {
                window.showToast('error', res.message || 'Gagal menghapus user');
              }
            } catch (e) {
              window.showToast('error', 'Terjadi kesalahan saat memproses permintaan');
            }
          }
        });
      },

      openResetModal(userId, userName) {
        this.resetForm = {
          userId,
          userName,
          user_password: ''
        };
        this.resetModalOpen = true;
      },

      async doResetPassword() {
        if (this.resetForm.user_password.length < 6) {
          window.showToast('error', 'Password minimal 6 karakter.');
          return;
        }
        this.resetting = true;
        try {
          const res = await window.ypFetch('<?= base_url('sys-admin/api/user/reset-password') ?>', {
            id: this.resetForm.userId,
            user_password: this.resetForm.user_password
          });
          if (res.success) {
            window.showToast('success', res.message || 'Password berhasil di-reset');
            this.resetModalOpen = false;
          } else {
            window.showToast('error', res.message || 'Gagal me-reset password');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan saat me-reset password');
        } finally {
          this.resetting = false;
        }
      }
    };
  }
</script>

<?= $this->endSection() ?>
