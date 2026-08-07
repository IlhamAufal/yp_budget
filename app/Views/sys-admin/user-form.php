<?php
/* =====================================================================
 * sys-admin/user-form.php — PARTIAL FORM: Tambah / Edit User
 *
 * View partial ini dimuat via AJAX ke dalam Global Modal.
 * TIDAK menggunakan extend layout — hanya berisi HTML form.
 *
 * Data yang diterima dari controller:
 *   $roles       — array role (menu type) untuk dropdown
 *   $rolesObj    — array role (object type) untuk dropdown
 *   $user        — data user existing (null jika create)
 *   $baseUrl     — base_url() untuk endpoint API
 * ===================================================================== */

$isEdit = !empty($user);
?>

<div x-data="userFormModal()" x-init="init()">
  <form @submit.prevent="submitForm()" class="p-6 space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Username -->
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

      <!-- Nama Lengkap -->
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
      <!-- Email -->
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

      <!-- Password -->
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
          placeholder="Minimal 6 karakter"
          class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-mono text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
        />
      </div>
    </div>

    <!-- Role Assignment (Menu Role) -->
    <div>
      <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
        Role Pengguna <span class="text-red-500">*</span>
      </label>
      <div class="relative" @click.outside="roleDropdownOpen = false">
        <button
          type="button"
          @click="roleDropdownOpen = !roleDropdownOpen"
          class="w-full flex items-center justify-between gap-3 rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-left font-semibold text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
        >
          <span class="flex items-center gap-2.5">
            <i class="fa-solid fa-shield-halved text-gray-400"></i>
            <span x-text="roleLabel()" :class="form.role_id ? '' : 'text-gray-400 dark:text-gray-500'"></span>
          </span>
          <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="roleDropdownOpen ? 'rotate-180' : ''"></i>
        </button>

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
              class="w-full bg-transparent py-3 pl-11 pr-4 text-sm text-gray-800 dark:text-white placeholder:text-gray-400 focus:outline-none"
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
                <span x-text="role.role_name_idn"></span>
                <i class="fa-solid fa-check text-xs" x-show="String(form.role_id) === String(role.role_id)"></i>
              </button>
            </template>
            <p x-show="filteredRoles().length === 0" class="px-4 py-6 text-center text-xs text-gray-400">Tidak ada role yang cocok.</p>
          </div>
        </div>
      </div>
      <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Pilih <strong>role menu</strong> (hak akses halaman) untuk pengguna ini.</p>
    </div>

    <!-- Object Role (Scope Data) -->
    <div>
      <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
        Object Role (Scope Data)
      </label>
      <div class="relative" @click.outside="objRoleDropdownOpen = false">
        <button
          type="button"
          @click="objRoleDropdownOpen = !objRoleDropdownOpen"
          class="w-full flex items-center justify-between gap-3 rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-left font-semibold text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
        >
          <span class="flex items-center gap-2.5">
            <i class="fa-solid fa-eye text-gray-400"></i>
            <span x-text="objRoleLabel()" :class="form.role_id_obj ? '' : 'text-gray-400 dark:text-gray-500'"></span>
          </span>
          <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="objRoleDropdownOpen ? 'rotate-180' : ''"></i>
        </button>

        <div
          x-show="objRoleDropdownOpen"
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
              x-model="objRoleSearch"
              placeholder="Cari object role..."
              class="w-full bg-transparent py-3 pl-11 pr-4 text-sm text-gray-800 dark:text-white placeholder:text-gray-400 focus:outline-none"
            />
          </div>
          <div class="max-h-56 overflow-y-auto py-1.5">
            <template x-for="role in filteredObjRoles()" :key="role.role_id">
              <button
                type="button"
                @click="pickObjRole(role.role_id)"
                class="w-full flex items-center justify-between gap-3 px-4 py-2.5 text-sm text-left transition-colors"
                :class="String(form.role_id_obj) === String(role.role_id) ? 'bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 font-semibold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
              >
                <span x-text="role.role_name_idn"></span>
                <i class="fa-solid fa-check text-xs" x-show="String(form.role_id_obj) === String(role.role_id)"></i>
              </button>
            </template>
            <p x-show="filteredObjRoles().length === 0" class="px-4 py-6 text-center text-xs text-gray-400">Tidak ada object role yang cocok.</p>
          </div>
        </div>
      </div>
      <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Menentukan <strong>scope data</strong> (department / cost center) yang dapat diakses.</p>
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
    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
      <button
        type="button"
        @click="Modal.close()"
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

<script>
/**
 * userFormModal() — Alpine component untuk form User di dalam Global Modal.
 *
 * Data role di-inject dari server sebagai JSON (aman, tidak perlu fetch ulang).
 * Form submit via ypFetch ke endpoint save yang sudah ada.
 */
function userFormModal() {
  return {
    saving: false,
    roleDropdownOpen: false,
    roleSearch: '',
    objRoleDropdownOpen: false,
    objRoleSearch: '',

    // Data dari server (injected via PHP)
    roleOptions: <?= json_encode($roles ?? [], JSON_HEX_TAG | JSON_HEX_APOS) ?>,
    roleOptionsObj: <?= json_encode($rolesObj ?? [], JSON_HEX_TAG | JSON_HEX_APOS) ?>,

    // Form state
    form: {
      id: <?= $isEdit ? (int)$user['user_id'] : 'null' ?>,
      user_username: <?= json_encode($isEdit ? ($user['user_username'] ?? '') : '', JSON_HEX_TAG) ?>,
      user_name: <?= json_encode($isEdit ? ($user['user_name'] ?? '') : '', JSON_HEX_TAG) ?>,
      user_email: <?= json_encode($isEdit ? ($user['user_email'] ?? '') : '', JSON_HEX_TAG) ?>,
      user_password: '',
      user_active: <?= json_encode($isEdit ? ($user['user_active'] ?? 'Y') : 'Y', JSON_HEX_TAG) ?>,
      user_block: <?= json_encode($isEdit ? ($user['user_block'] ?? 'N') : 'N', JSON_HEX_TAG) ?>,
      role_id: <?= json_encode($isEdit && !empty($user['role_ids'][0]) ? (string)$user['role_ids'][0] : '', JSON_HEX_TAG) ?>,
      role_id_obj: <?= json_encode($isEdit && !empty($user['obj_role_ids'][0]) ? (string)$user['obj_role_ids'][0] : '', JSON_HEX_TAG) ?>
    },

    init() {
      // Update modal title based on mode
      if (this.form.id) {
        Modal.setTitle('Edit User — ' + this.form.user_name);
      }
    },

    // ─── Role Dropdown Helpers ──────────────────────────────────
    roleLabel() {
      if (!this.form.role_id) return '— Pilih Role —';
      const found = this.roleOptions.find(r => String(r.role_id) === String(this.form.role_id));
      return found ? found.role_name_idn : 'Role #' + this.form.role_id;
    },

    filteredRoles() {
      const q = (this.roleSearch || '').toLowerCase().trim();
      if (!q) return this.roleOptions;
      return this.roleOptions.filter(r => String(r.role_name_idn || '').toLowerCase().includes(q));
    },

    pickRole(id) {
      this.form.role_id = String(id);
      this.roleDropdownOpen = false;
      this.roleSearch = '';
    },

    objRoleLabel() {
      if (!this.form.role_id_obj) return '— Pilih Object Role —';
      const found = this.roleOptionsObj.find(r => String(r.role_id) === String(this.form.role_id_obj));
      return found ? found.role_name_idn : 'Role #' + this.form.role_id_obj;
    },

    filteredObjRoles() {
      const q = (this.objRoleSearch || '').toLowerCase().trim();
      if (!q) return this.roleOptionsObj;
      return this.roleOptionsObj.filter(r => String(r.role_name_idn || '').toLowerCase().includes(q));
    },

    pickObjRole(id) {
      this.form.role_id_obj = String(id);
      this.objRoleDropdownOpen = false;
      this.objRoleSearch = '';
    },

    // ─── Form Submission ────────────────────────────────────────
    async submitForm() {
      this.saving = true;
      try {
        const body = new URLSearchParams();
        body.append('has_role_ids', '1');

        for (const [key, value] of Object.entries(this.form)) {
          if (key === 'role_id' || key === 'role_id_obj') continue;
          if (value !== null && value !== undefined && value !== '') {
            body.append(key, value);
          }
        }
        if (this.form.role_id) {
          body.append('role_ids[]', this.form.role_id);
        }
        if (this.form.role_id_obj) {
          body.append('role_ids[]', this.form.role_id_obj);
        }

        const res = await window.ypFetch('<?= $baseUrl ?>sys-admin/api/user/save', body);

        if (res.success) {
          Modal.close();
          window.showToast('success', res.message || 'User berhasil disimpan');
          window.dispatchEvent(new CustomEvent('modal-saved', {
            detail: { entity: 'user' }
          }));
        } else {
          window.showToast('error', res.message || 'Gagal menyimpan user');
        }
      } catch (e) {
        window.showToast('error', 'Terjadi kesalahan sistem');
      } finally {
        this.saving = false;
      }
    }
  };
}
</script>
