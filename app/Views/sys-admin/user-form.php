<?php
/* Partial form Tambah / Edit User, dimuat lewat Global Modal. */
$isEdit = ! empty($user);
$menuList = $menus ?? [];
$groupedMenus = [];
$menuChildren = [];
foreach ($menuList as $menu) {
    $groupedMenus[($menu['menu_group'] ?? '') ?: 'TANPA GRUP'][] = $menu;
    if (! empty($menu['parent_id'])) {
        $menuChildren[(int) $menu['parent_id']][] = (int) $menu['menu_id'];
    }
}
$allMenuIds = array_map(static fn(array $menu): int => (int) $menu['menu_id'], $menuList);
$baselineMenuIds = array_values(array_map('intval', $isEdit ? ($user['baseline_menu_ids'] ?? []) : []));
$overrideMap = $isEdit ? ($user['override_map'] ?? []) : [];
?>

<div x-data="userFormModal()" x-init="init()">
  <form @submit.prevent="submitForm()" class="p-6 space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Username <span class="text-red-500">*</span></label>
        <input type="text" x-model="form.user_username" required maxlength="10" placeholder="Contoh: budi01" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-mono font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
      </div>
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" x-model="form.user_name" required placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Email</label>
        <input type="email" x-model="form.user_email" placeholder="Contoh: budi@company.com" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
      </div>
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Password <span class="text-red-500" x-show="!form.id">*</span><span class="normal-case font-medium" x-show="form.id"> (kosongkan jika tidak diubah)</span></label>
        <input type="password" x-model="form.user_password" :required="!form.id" minlength="6" placeholder="Minimal 6 karakter" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-mono text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
      </div>
    </div>

    <div>
      <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Menu Role <span class="text-xs font-normal normal-case text-gray-400">(opsional)</span></label>
      <div class="relative" @click.outside="roleDropdownOpen = false">
        <button type="button" @click="roleDropdownOpen = !roleDropdownOpen" class="w-full flex items-center justify-between gap-3 rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-left font-semibold text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors">
          <span class="flex items-center gap-2.5"><i class="fa-solid fa-shield-halved text-gray-400"></i><span x-text="roleLabel()" :class="form.role_id ? '' : 'text-gray-400 dark:text-gray-500'"></span></span>
          <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="roleDropdownOpen ? 'rotate-180' : ''"></i>
        </button>
        <div x-show="roleDropdownOpen" x-transition class="absolute z-20 mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-xl overflow-hidden">
          <div class="relative border-b border-gray-100 dark:border-gray-800"><i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none"></i><input type="text" x-model="roleSearch" placeholder="Cari role..." class="w-full bg-transparent py-3 pl-11 pr-4 text-sm text-gray-800 dark:text-white placeholder:text-gray-400 focus:outline-none" /></div>
          <div class="max-h-56 overflow-y-auto py-1.5">
            <template x-for="role in filteredRoles()" :key="role.role_id">
              <button type="button" @click="pickRole(role.role_id)" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 text-sm text-left transition-colors" :class="String(form.role_id) === String(role.role_id) ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"><span x-text="role.role_name_idn"></span><i class="fa-solid fa-check text-xs" x-show="String(form.role_id) === String(role.role_id)"></i></button>
            </template>
            <p x-show="filteredRoles().length === 0" class="px-4 py-6 text-center text-xs text-gray-400">Tidak ada role yang cocok.</p>
          </div>
        </div>
      </div>
      <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Pilih <strong>role menu</strong> (hak akses halaman) untuk pengguna ini.</p>
    </div>

    <div>
      <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Object Role (Scope Data)</label>
      <div class="relative" @click.outside="objRoleDropdownOpen = false">
        <button type="button" @click="objRoleDropdownOpen = !objRoleDropdownOpen" class="w-full flex items-center justify-between gap-3 rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-left font-semibold text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors">
          <span class="flex items-center gap-2.5"><i class="fa-solid fa-eye text-gray-400"></i><span x-text="objRoleLabel()" :class="form.role_id_obj ? '' : 'text-gray-400 dark:text-gray-500'"></span></span>
          <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="objRoleDropdownOpen ? 'rotate-180' : ''"></i>
        </button>
        <div x-show="objRoleDropdownOpen" x-transition class="absolute z-20 mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-xl overflow-hidden">
          <div class="relative border-b border-gray-100 dark:border-gray-800"><i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none"></i><input type="text" x-model="objRoleSearch" placeholder="Cari object role..." class="w-full bg-transparent py-3 pl-11 pr-4 text-sm text-gray-800 dark:text-white placeholder:text-gray-400 focus:outline-none" /></div>
          <div class="max-h-56 overflow-y-auto py-1.5">
            <template x-for="role in filteredObjRoles()" :key="role.role_id">
              <button type="button" @click="pickObjRole(role.role_id)" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 text-sm text-left transition-colors" :class="String(form.role_id_obj) === String(role.role_id) ? 'bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 font-semibold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"><span x-text="role.role_name_idn"></span><i class="fa-solid fa-check text-xs" x-show="String(form.role_id_obj) === String(role.role_id)"></i></button>
            </template>
            <p x-show="filteredObjRoles().length === 0" class="px-4 py-6 text-center text-xs text-gray-400">Tidak ada object role yang cocok.</p>
          </div>
        </div>
      </div>
      <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Menentukan <strong>scope data</strong> (department / cost center) yang dapat diakses.</p>
    </div>

    <section class="rounded-2xl border border-indigo-200 bg-indigo-50/40 dark:border-indigo-900/70 dark:bg-indigo-500/5 overflow-hidden">
      <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-indigo-100 dark:border-indigo-900/60">
        <div class="flex items-start gap-3">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-600 border border-indigo-100 dark:border-indigo-900 dark:bg-gray-900 dark:text-indigo-400"><i class="fa-solid fa-list-check"></i></span>
          <div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Akses Menu Efektif</h3>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Centang hasil akhir akses. Sistem hanya menyimpan perbedaannya terhadap role utama.</p>
          </div>
        </div>
        <span class="rounded-lg bg-indigo-100 px-3 py-1.5 text-xs font-bold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">Baseline role + override delta</span>
      </div>

      <div class="p-5 space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-sm text-gray-600 dark:text-gray-300"><span class="font-bold text-gray-900 dark:text-white" x-text="selectedCustomMenuCount()"></span> menu terpilih dari <?= count($menuList) ?> menu</p>
          <div class="flex items-center gap-2">
            <button type="button" @click="selectAllCustomMenus()" class="rounded-lg border border-indigo-200 bg-white px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-50 dark:border-indigo-900 dark:bg-gray-900 dark:text-indigo-300 dark:hover:bg-indigo-500/10 transition-colors"><i class="fa-solid fa-check-double text-[10px]"></i> Pilih Semua</button>
            <button type="button" @click="clearAllCustomMenus()" class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors"><i class="fa-solid fa-eraser text-[10px]"></i> Kosongkan</button>
          </div>
        </div>

        <div class="max-h-[420px] overflow-y-auto space-y-6 pr-2 -mr-2">
          <?php foreach ($groupedMenus as $group => $groupMenus): ?>
            <div>
              <div class="flex items-center justify-between mb-3"><h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-2"><i class="fa-solid fa-layer-group"></i> <?= esc($group) ?></h4><span class="text-[11px] font-semibold text-gray-400 dark:text-gray-500"><?= count($groupMenus) ?> menu</span></div>
              <div class="space-y-1.5">
                <?php foreach ($groupMenus as $menu): ?>
                  <?php $level = max(1, (int) ($menu['menu_level'] ?? 1)); ?>
                  <label class="flex items-start gap-3 rounded-xl border border-indigo-100 bg-white/70 p-3.5 cursor-pointer hover:border-indigo-300 dark:border-gray-700 dark:bg-gray-900/70 dark:hover:border-indigo-700 transition-colors" style="margin-left: <?= min(4, $level - 1) * 1.25 ?>rem">
                    <input type="checkbox" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-500 focus:ring-indigo-500/30" :checked="customMenuIds.includes(<?= (int) $menu['menu_id'] ?>)" @change="toggleCustomMenu(<?= (int) $menu['menu_id'] ?>, $event.target.checked)" />
                    <span class="flex items-center gap-2.5"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-500 dark:bg-indigo-500/10 dark:border-indigo-900"><i class="<?= esc($menu['menu_icon'] ?: 'fa-regular fa-circle') ?> text-xs"></i></span><span><span class="block text-sm font-bold text-gray-800 dark:text-gray-100"><?= esc($menu['menu_name_idn']) ?></span><span class="block text-xs text-gray-400 dark:text-gray-500"><?= esc($menu['menu_link'] ?: '-') ?></span></span></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <p class="text-xs text-indigo-700 dark:text-indigo-300 flex items-start gap-2"><i class="fa-solid fa-circle-info mt-0.5"></i><span>Menu yang sama dengan baseline role tidak membuat baris override. Menu tambahan disimpan sebagai Y, sedangkan menu role yang dicabut disimpan sebagai N.</span></p>
      </div>
    </section>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Status <span class="text-red-500">*</span></label>
        <select x-model="form.user_active" required class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"><option value="Y">Aktif (Y)</option><option value="N">Non-Aktif (N)</option></select>
      </div>
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Blokir Akun</label>
        <select x-model="form.user_block" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"><option value="N">Tidak (N)</option><option value="Y">Ya (Y)</option></select>
      </div>
    </div>

    <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 p-4 text-sm text-amber-800 dark:text-amber-300 flex items-start gap-3"><i class="fa-solid fa-circle-info text-amber-500 mt-0.5 shrink-0"></i><span>Perubahan user, role, object scope, dan akses menu berlaku pada request berikutnya.</span></div>

    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
      <button type="button" @click="Modal.close()" class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-3 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Batal</button>
      <button type="submit" :disabled="saving" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/40 disabled:opacity-50 transition-colors"><i class="fa-solid fa-spinner fa-spin" x-show="saving"></i><span x-text="saving ? 'Menyimpan...' : (form.id ? 'Perbarui User' : 'Simpan User')"></span></button>
    </div>
  </form>
</div>

<script>
function userFormModal() {
  return {
    saving: false,
    roleDropdownOpen: false,
    roleSearch: '',
    objRoleDropdownOpen: false,
    objRoleSearch: '',
    customMenuAccess: true,
    customMenuIds: <?= json_encode(array_values(array_map('intval', $isEdit ? ($user['custom_menu_ids'] ?? []) : $baselineMenuIds)), JSON_HEX_TAG | JSON_HEX_APOS) ?>,
    baselineMenuIds: <?= json_encode($baselineMenuIds, JSON_HEX_TAG | JSON_HEX_APOS) ?>,
    overrideMap: <?= json_encode($overrideMap, JSON_HEX_TAG | JSON_HEX_APOS) ?>,
    roleBaselines: <?= json_encode($roleBaselines ?? [], JSON_HEX_TAG | JSON_HEX_APOS) ?>,
    menuChildren: <?= json_encode($menuChildren, JSON_HEX_TAG | JSON_HEX_APOS) ?>,
    allMenuIds: <?= json_encode($allMenuIds, JSON_HEX_TAG | JSON_HEX_APOS) ?>,
    roleOptions: <?= json_encode($roles ?? [], JSON_HEX_TAG | JSON_HEX_APOS) ?>,
    roleOptionsObj: <?= json_encode($rolesObj ?? [], JSON_HEX_TAG | JSON_HEX_APOS) ?>,
    form: {
      id: <?= $isEdit ? (int) $user['user_id'] : 'null' ?>,
      user_username: <?= json_encode($isEdit ? ($user['user_username'] ?? '') : '', JSON_HEX_TAG) ?>,
      user_name: <?= json_encode($isEdit ? ($user['user_name'] ?? '') : '', JSON_HEX_TAG) ?>,
      user_email: <?= json_encode($isEdit ? ($user['user_email'] ?? '') : '', JSON_HEX_TAG) ?>,
      user_password: '',
      user_active: <?= json_encode($isEdit ? ($user['user_active'] ?? 'Y') : 'Y', JSON_HEX_TAG) ?>,
      user_block: <?= json_encode($isEdit ? ($user['user_block'] ?? 'N') : 'N', JSON_HEX_TAG) ?>,
      role_id: <?= json_encode($isEdit && ! empty($user['role_ids'][0]) ? (string) $user['role_ids'][0] : '', JSON_HEX_TAG) ?>,
      role_id_obj: <?= json_encode($isEdit && ! empty($user['obj_role_ids'][0]) ? (string) $user['obj_role_ids'][0] : '', JSON_HEX_TAG) ?>
    },
    init() {
      if (this.form.id) Modal.setTitle('Edit User — ' + this.form.user_name);
    },
    roleLabel() {
      if (!this.form.role_id) return '— Pilih Role —';
      const found = this.roleOptions.find(role => String(role.role_id) === String(this.form.role_id));
      return found ? found.role_name_idn : 'Role #' + this.form.role_id;
    },
    filteredRoles() {
      const keyword = (this.roleSearch || '').toLowerCase().trim();
      return keyword ? this.roleOptions.filter(role => String(role.role_name_idn || '').toLowerCase().includes(keyword)) : this.roleOptions;
    },
    pickRole(id) {
      this.form.role_id = String(id);
      this.baselineMenuIds = (this.roleBaselines[String(id)] || []).map(Number);
      const effective = new Set(this.baselineMenuIds);
      Object.entries(this.overrideMap || {}).forEach(([menuId, status]) => {
        if (status === 'Y') effective.add(Number(menuId));
        if (status === 'N') effective.delete(Number(menuId));
      });
      this.customMenuIds = [...effective];
      this.roleDropdownOpen = false;
      this.roleSearch = '';
    },
    objRoleLabel() {
      if (!this.form.role_id_obj) return '— Pilih Object Role —';
      const found = this.roleOptionsObj.find(role => String(role.role_id) === String(this.form.role_id_obj));
      return found ? found.role_name_idn : 'Role #' + this.form.role_id_obj;
    },
    filteredObjRoles() {
      const keyword = (this.objRoleSearch || '').toLowerCase().trim();
      return keyword ? this.roleOptionsObj.filter(role => String(role.role_name_idn || '').toLowerCase().includes(keyword)) : this.roleOptionsObj;
    },
    pickObjRole(id) {
      this.form.role_id_obj = String(id);
      this.objRoleDropdownOpen = false;
      this.objRoleSearch = '';
    },
    toggleCustomMenu(menuId, checked, parentId = null) {
      menuId = Number(menuId);
      if (checked && !this.customMenuIds.includes(menuId)) {
        this.customMenuIds.push(menuId);
      }
      if (!checked) {
        this.customMenuIds = this.customMenuIds.filter(id => id !== menuId);
      }
    },
    selectedCustomMenuCount() {
      return this.customMenuIds.length;
    },
    selectAllCustomMenus() {
      this.customMenuIds = [...this.allMenuIds];
    },
    clearAllCustomMenus() {
      this.customMenuIds = [];
    },
    async submitForm() {
      this.saving = true;
      try {
        const body = new URLSearchParams();
        body.append('has_role_ids', '1');
        body.append('has_custom_menu_access', '1');
        for (const [key, value] of Object.entries(this.form)) {
          if (key === 'role_id' || key === 'role_id_obj') continue;
          if (value !== null && value !== undefined && value !== '') body.append(key, value);
        }
        if (this.form.role_id) body.append('menu_role_ids[]', this.form.role_id);
        if (this.form.role_id_obj) body.append('object_role_ids[]', this.form.role_id_obj);
        if (this.customMenuAccess) this.customMenuIds.forEach(id => body.append('menu_ids[]', id));
        const res = await window.ypFetch('<?= $baseUrl ?>sys-admin/api/user/save', body);
        if (res.success) {
          Modal.close();
          window.showToast('success', res.message || 'User berhasil disimpan');
          window.dispatchEvent(new CustomEvent('modal-saved', { detail: { entity: 'user' } }));
        } else {
          window.showToast('error', res.message || 'Gagal menyimpan user');
        }
      } catch (error) {
        window.showToast('error', 'Terjadi kesalahan sistem');
      } finally {
        this.saving = false;
      }
    }
  };
}
</script>
