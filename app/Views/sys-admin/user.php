<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6" x-data="userPage()" @modal-saved.window="if($event.detail.entity === 'user') setTimeout(() => location.reload(), 600)">

  <!-- ============================================================ -->
  <!-- BREADCRUMB & HEADER -->
  <!-- ============================================================ -->
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors">
          <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span>System Administration</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-brand-500 font-bold">User Management</span>
      </div>
      <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 shadow-xs">
          <i class="fa-solid fa-users-gear text-base"></i>
        </span>
        User Management
      </h1>
      <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
        Kelola akun pengguna, penugasan role, dan hak akses sistem.
      </p>
    </div>

    <!-- Quick Action -->
    <div class="flex flex-wrap items-center gap-3">
      <button
        type="button"
        @click="Modal.show({ url: '<?= base_url('sys-admin/user/form') ?>', title: 'Tambah User Baru', size: 'lg' })"
        class="inline-flex items-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition-all active:scale-[0.98]"
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
            class="w-full rounded-xl border border-gray-200/80 bg-gray-50/60 py-2.5 pl-11 pr-4 text-xs md:text-sm font-medium text-gray-800 placeholder:text-gray-400/80 focus:border-brand-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700/80 dark:bg-gray-800/80 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-brand-400 transition-all duration-200 shadow-xs"
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
        <?php if (! empty($has_filter)): ?>
        <a
          href="<?= base_url('sys-admin/user') ?>"
          class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-2.5 px-4 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          title="Reset Filter"
        >
          <i class="fa-solid fa-rotate-left text-xs"></i>
        </a>
        <?php endif; ?>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead class="bg-brand-500 text-white font-semibold text-xs border-b border-brand-600">
          <tr class="bg-brand-500 text-white font-semibold">
            <th class="py-3 px-5 w-12 text-center text-white">No.</th>
            <th class="py-3 px-5 text-white">User</th>
            <th class="py-3 px-5 text-white">Role</th>
            <th class="py-3 px-5 text-white">Object</th>
            <th class="py-3 px-5 text-center text-white">Status</th>
            <th class="py-3 px-5 text-right w-44 text-white">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="6" class="py-20 text-center text-gray-400 dark:text-gray-500">
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
                $isAdminRole = (($r['user_admin'] ?? 'N') === 'Y');
                $menuRoleIds = array_filter(array_map('intval', explode(',', $r['menu_role_ids'] ?? '')));
                $objRoleIds  = array_filter(array_map('intval', explode(',', $r['obj_role_ids'] ?? '')));
                $objRoleNames = array_values(array_filter(array_map('trim', explode(',', $r['obj_role_names'] ?? ''))));
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
                        <!-- <span class="font-mono"><?= esc($r['user_username']) ?></span> -->
                        <?php if (! empty($r['user_email'])): ?>
                          <span> <?= esc($r['user_email']) ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Role (menu role) -->
                <td class="py-4 px-5">
                  <?php if (empty($menuRoleIds)): ?>
                    <?php if ($isAdminRole): ?>
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
                      <?php foreach ($menuRoleIds as $rid): ?>
                        <?php
                          $roleName = '';
                          foreach (($roles ?? []) as $rl) {
                              if ((int) $rl['role_id'] === $rid) {
                                  $roleName = $rl['role_name_idn'];
                                  break;
                              }
                          }
                          $isMenuAdmin = $isAdminRole || (stripos($roleName, 'admin') !== false);
                        ?>
                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold <?= $isMenuAdmin ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400' ?>">
                          <i class="fa-solid rounded-full p-0.5 text-[8px] <?= $isMenuAdmin ? 'text-amber-500' : 'text-indigo-500' ?>"></i>
                          <?= esc($roleName ?: 'Role #' . $rid) ?>
                        </span>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </td>

                <!-- Object (scope data) -->
                <td class="py-4 px-5">
                  <?php if (empty($objRoleIds)): ?>
                    <span class="text-gray-300 dark:text-gray-600">—</span>
                  <?php else: ?>
                    <div class="flex flex-wrap items-center gap-1.5">
                      <?php foreach ($objRoleIds as $i => $oid): ?>
                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400" title="Object role (scope data)">
                          <i class="fa-solid text-[10px]"></i>
                          <?= esc($objRoleNames[$i] ?? ('Obj #' . $oid)) ?>
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
                      class="h-8 w-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors flex items-center justify-center shadow-2xs"
                      title="Reset Password"
                    >
                      <i class="fa-solid fa-key text-xs"></i>
                    </button>
                    <button
                      type="button"
                      @click="Modal.show({ url: '<?= base_url('sys-admin/user/form') ?>?id=<?= (int)$r['user_id'] ?>', title: 'Edit User', size: 'lg' })"
                      class="h-8 w-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-brand-500 hover:bg-brand-50 hover:border-brand-200 dark:hover:bg-brand-500/10 transition-colors flex items-center justify-center shadow-2xs"
                      title="Edit User"
                    >
                      <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                    <button
                      type="button"
                      @click="toggleStatus(<?= (int)$r['user_id'] ?>, '<?= ($r['user_active'] ?? 'Y') === 'Y' ? 'nonaktifkan' : 'aktifkan' ?>')"
                      class="h-8 w-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 <?= ($r['user_active'] ?? 'Y') === 'Y' ? 'text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10' : 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10' ?> transition-colors flex items-center justify-center shadow-2xs"
                      title="Aktif / Nonaktifkan"
                    >
                      <i class="fa-solid fa-power-off text-xs"></i>
                    </button>
                    <button
                      type="button"
                      @click="deleteUser(<?= (int)$r['user_id'] ?>)"
                      class="h-8 w-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-red-600 hover:bg-red-50 hover:border-red-200 dark:hover:bg-red-500/10 transition-colors flex items-center justify-center shadow-2xs"
                      title="Hapus User"
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
    <div class="border-t border-gray-100 dark:border-gray-800 p-5 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
      <span>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200"><?= $from ?></span> - <span class="font-bold text-gray-800 dark:text-gray-200"><?= $to ?></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= number_format($total) ?></span> user</span>
      <div class="flex items-center gap-2">
        <a href="?page=<?= max(1, $page - 1) ?>" class="h-9 px-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?= $page <= 1 ? 'opacity-40 pointer-events-none' : '' ?> transition-colors text-sm font-semibold flex items-center gap-2"><i class="fa-solid fa-chevron-left text-xs"></i><span class="hidden sm:inline">Sebelumnya</span></a>
        <?php for ($p = $winStart; $p <= $winEnd; $p++): ?>
          <a href="?page=<?= $p ?>" class="h-9 min-w-[36px] px-2 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center <?= $p === $page ? 'bg-brand-500 text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>"><?= $p ?></a>
        <?php endfor; ?>
        <a href="?page=<?= min($pages, $page + 1) ?>" class="h-9 px-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?= $page >= $pages ? 'opacity-40 pointer-events-none' : '' ?> transition-colors text-sm font-semibold flex items-center gap-2"><span class="hidden sm:inline">Berikutnya</span><i class="fa-solid fa-chevron-right text-xs"></i></a>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- ============================================================ -->
  <!-- MODAL: TAMBAH / EDIT USER                                   -->
  <!-- MIGRATED → Global Modal (Modal.show) via sys-admin/user-form.php -->
  <!-- ============================================================ -->

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


</div>

<!-- ============================================================ -->
<!-- ALPINE COMPONENT SCRIPT -->
<!-- ============================================================ -->
<script>
  function userPage() {
    return {
      // ─── Legacy props (kept to prevent Alpine errors) ───────────
      modalOpen: false,
      saving: false,
      resetting: false,

      // ─── Role data (still used by other parts if needed) ────────
      roleOptions: <?= json_encode(array_values(array_filter(array_map(fn($rl) => array_merge(['role_id' => (int) $rl['role_id'], 'role_name_idn' => $rl['role_name_idn']], ['role_type' => ($rl['role_type'] ?? 'menu')]), $roles ?? []), fn($r) => $r['role_type'] === 'menu'))) ?>,
      roleOptionsObj: <?= json_encode(array_values(array_filter(array_map(fn($rl) => array_merge(['role_id' => (int) $rl['role_id'], 'role_name_idn' => $rl['role_name_idn']], ['role_type' => ($rl['role_type'] ?? 'menu')]), $roles ?? []), fn($r) => $r['role_type'] === 'object'))) ?>,

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

      // Reset Password
      resetModalOpen: false,
      resetForm: {
        userId: null,
        userName: '',
        user_password: ''
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
