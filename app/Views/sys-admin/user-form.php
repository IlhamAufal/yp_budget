<?php
/* Partial form Tambah / Edit User, dimuat lewat Global Modal (Vanilla JS). */
$isEdit = ! empty($user);
$baseUrl = base_url();
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
$initialMenuIds = array_values(array_map('intval', $isEdit ? ($user['custom_menu_ids'] ?? []) : $baselineMenuIds));

$selectedRoleId = $isEdit && ! empty($user['role_ids'][0]) ? (string) $user['role_ids'][0] : '';
$selectedObjRoleId = $isEdit && ! empty($user['obj_role_ids'][0]) ? (string) $user['obj_role_ids'][0] : '';

$roleBaselines = $roleBaselines ?? [];
$roleOptions = $roles ?? [];
$roleOptionsObj = $rolesObj ?? [];
?>

<div id="userFormContainer">
  <form id="userFormElement" class="p-6 space-y-4">
    <input type="hidden" id="uf_user_id" value="<?= $isEdit ? (int) $user['user_id'] : '' ?>">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Username <span class="text-red-500">*</span></label>
        <input type="text" id="uf_user_username" required maxlength="10" value="<?= esc($isEdit ? ($user['user_username'] ?? '') : '') ?>" placeholder="Contoh: budi01" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-mono font-bold text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" id="uf_user_name" required value="<?= esc($isEdit ? ($user['user_name'] ?? '') : '') ?>" placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-semibold text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
        <input type="email" id="uf_user_email" value="<?= esc($isEdit ? ($user['user_email'] ?? '') : '') ?>" placeholder="Contoh: budi@company.com" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password <span class="text-red-500" id="uf_password_required_star"><?= $isEdit ? '' : '*' ?></span><span class="normal-case font-medium text-gray-400" id="uf_password_hint"><?= $isEdit ? ' (kosongkan jika tidak diubah)' : '' ?></span></label>
        <input type="password" id="uf_user_password" <?= $isEdit ? '' : 'required' ?> minlength="6" placeholder="Minimal 6 karakter" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-mono text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
      </div>
    </div>

    <!-- Menu Role Dropdown -->
    <div>
      <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Menu Role <span class="text-xs font-normal normal-case text-gray-400">(opsional)</span></label>
      <div class="relative" id="uf_role_dropdown_container">
        <input type="hidden" id="uf_role_id" value="<?= esc($selectedRoleId) ?>">
        <button type="button" id="uf_role_toggle_btn" class="w-full flex items-center justify-between gap-3 rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs text-left font-semibold text-gray-900 focus:border-[#2F3185] focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors">
          <span class="flex items-center gap-2.5">
            <i class="fa-solid fa-shield-halved text-gray-400"></i>
            <span id="uf_role_label" class="<?= $selectedRoleId ? '' : 'text-gray-400 dark:text-gray-500' ?>">— Pilih Role —</span>
          </span>
          <i id="uf_role_arrow" class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform"></i>
        </button>
        <div id="uf_role_menu" class="absolute z-20 mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-xl overflow-hidden hidden">
          <div class="relative border-b border-gray-100 dark:border-gray-800">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none"></i>
            <input type="text" id="uf_role_search" placeholder="Cari role..." class="w-full bg-transparent py-2.5 pl-10 pr-4 text-xs text-gray-800 dark:text-white placeholder:text-gray-400 focus:outline-none" />
          </div>
          <div id="uf_role_list" class="max-h-56 overflow-y-auto py-1.5">
            <?php foreach ($roleOptions as $role): ?>
              <button type="button" data-role-id="<?= (int) $role['role_id'] ?>" data-role-name="<?= esc($role['role_name_idn']) ?>" class="uf-role-option w-full flex items-center justify-between gap-3 px-4 py-2 text-xs text-left transition-colors text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                <span><?= esc($role['role_name_idn']) ?></span>
                <i class="fa-solid fa-check text-xs uf-role-check <?= (string)$selectedRoleId === (string)$role['role_id'] ? '' : 'hidden' ?>"></i>
              </button>
            <?php endforeach; ?>
            <p id="uf_role_empty" class="px-4 py-6 text-center text-xs text-gray-400 hidden">Tidak ada role yang cocok.</p>
          </div>
        </div>
      </div>
      <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Pilih <strong>role menu</strong> (hak akses halaman) untuk pengguna ini.</p>
    </div>

    <!-- Object Role Dropdown -->
    <div>
      <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Object Role (Scope Data)</label>
      <div class="relative" id="uf_obj_role_dropdown_container">
        <input type="hidden" id="uf_obj_role_id" value="<?= esc($selectedObjRoleId) ?>">
        <button type="button" id="uf_obj_role_toggle_btn" class="w-full flex items-center justify-between gap-3 rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs text-left font-semibold text-gray-900 focus:border-[#2F3185] focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors">
          <span class="flex items-center gap-2.5">
            <i class="fa-solid fa-eye text-gray-400"></i>
            <span id="uf_obj_role_label" class="<?= $selectedObjRoleId ? '' : 'text-gray-400 dark:text-gray-500' ?>">— Pilih Object Role —</span>
          </span>
          <i id="uf_obj_role_arrow" class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform"></i>
        </button>
        <div id="uf_obj_role_menu" class="absolute z-20 mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-xl overflow-hidden hidden">
          <div class="relative border-b border-gray-100 dark:border-gray-800">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none"></i>
            <input type="text" id="uf_obj_role_search" placeholder="Cari object role..." class="w-full bg-transparent py-2.5 pl-10 pr-4 text-xs text-gray-800 dark:text-white placeholder:text-gray-400 focus:outline-none" />
          </div>
          <div id="uf_obj_role_list" class="max-h-56 overflow-y-auto py-1.5">
            <?php foreach ($roleOptionsObj as $role): ?>
              <button type="button" data-obj-role-id="<?= (int) $role['role_id'] ?>" data-obj-role-name="<?= esc($role['role_name_idn']) ?>" class="uf-obj-role-option w-full flex items-center justify-between gap-3 px-4 py-2 text-xs text-left transition-colors text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                <span><?= esc($role['role_name_idn']) ?></span>
                <i class="fa-solid fa-check text-xs uf-obj-role-check <?= (string)$selectedObjRoleId === (string)$role['role_id'] ? '' : 'hidden' ?>"></i>
              </button>
            <?php endforeach; ?>
            <p id="uf_obj_role_empty" class="px-4 py-6 text-center text-xs text-gray-400 hidden">Tidak ada object role yang cocok.</p>
          </div>
        </div>
      </div>
      <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Menentukan <strong>scope data</strong> (department / cost center) yang dapat diakses.</p>
    </div>

    <!-- Akses Menu Efektif -->
    <section class="rounded-2xl border border-indigo-200 bg-indigo-50/40 dark:border-indigo-900/70 dark:bg-indigo-500/5 overflow-hidden">
      <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between border-b border-indigo-100 dark:border-indigo-900/60">
        <div class="flex items-start gap-2.5">
          <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-600 border border-indigo-100 dark:border-indigo-900 dark:bg-gray-900 dark:text-indigo-400"><i class="fa-solid fa-list-check text-xs"></i></span>
          <div>
            <h3 class="text-xs font-bold text-gray-900 dark:text-white">Akses Menu Efektif</h3>
            <p class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400">Centang hasil akhir akses. Sistem hanya menyimpan perbedaannya terhadap role utama.</p>
          </div>
        </div>
        <span class="rounded-lg bg-indigo-100 px-2.5 py-1 text-[11px] font-bold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">Baseline role + override delta</span>
      </div>

      <div class="p-4 space-y-4">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-xs text-gray-600 dark:text-gray-300"><span id="uf_custom_menu_count" class="font-bold text-gray-900 dark:text-white"><?= count($initialMenuIds) ?></span> menu terpilih dari <?= count($menuList) ?> menu</p>
          <div class="flex items-center gap-2">
            <button type="button" id="uf_select_all_menus_btn" class="rounded-xl border border-indigo-200 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-50 dark:border-indigo-900 dark:bg-gray-900 dark:text-indigo-300 dark:hover:bg-indigo-500/10 transition-colors"><i class="fa-solid fa-check-double text-[10px]"></i> Pilih Semua</button>
            <button type="button" id="uf_clear_all_menus_btn" class="rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors"><i class="fa-solid fa-eraser text-[10px]"></i> Kosongkan</button>
          </div>
        </div>

        <div class="max-h-[420px] overflow-y-auto space-y-5 pr-2 -mr-2">
          <?php foreach ($groupedMenus as $group => $groupMenus): ?>
            <div>
              <div class="flex items-center justify-between mb-2.5">
                <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2"><i class="fa-solid fa-layer-group text-xs text-[#2F3185] dark:text-indigo-400"></i> <?= esc($group) ?></h4>
                <span class="text-[11px] font-semibold text-gray-400 dark:text-gray-500"><?= count($groupMenus) ?> menu</span>
              </div>
              <div class="space-y-1.5">
                <?php foreach ($groupMenus as $menu): ?>
                  <?php $level = max(1, (int) ($menu['menu_level'] ?? 1)); ?>
                  <label class="flex items-start gap-3 rounded-xl border border-indigo-100 bg-white/70 p-3 cursor-pointer hover:border-indigo-300 dark:border-gray-700 dark:bg-gray-900/70 dark:hover:border-indigo-700 transition-colors" style="margin-left: <?= min(4, $level - 1) * 1.25 ?>rem">
                    <input type="checkbox" name="menu_ids[]" value="<?= (int) $menu['menu_id'] ?>" class="uf-menu-checkbox mt-0.5 h-4 w-4 rounded border-gray-300 text-[#2F3185] focus:ring-[#2F3185]/30" <?= in_array((int) $menu['menu_id'], $initialMenuIds, true) ? 'checked' : '' ?> />
                    <span class="flex items-center gap-2.5">
                      <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50 border border-indigo-100 text-[#2F3185] dark:bg-indigo-500/10 dark:border-indigo-900">
                        <i class="<?= esc($menu['menu_icon'] ?: 'fa-regular fa-circle') ?> text-xs"></i>
                      </span>
                      <span>
                        <span class="block text-xs font-bold text-gray-800 dark:text-gray-100"><?= esc($menu['menu_name_idn']) ?></span>
                        <span class="block text-[11px] text-gray-400 dark:text-gray-500"><?= esc($menu['menu_link'] ?: '-') ?></span>
                      </span>
                    </span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <p class="text-[11px] text-indigo-700 dark:text-indigo-300 flex items-start gap-2"><i class="fa-solid fa-circle-info mt-0.5"></i><span>Menu yang sama dengan baseline role tidak membuat baris override. Menu tambahan disimpan sebagai Y, sedangkan menu role yang dicabut disimpan sebagai N.</span></p>
      </div>
    </section>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Status <span class="text-red-500">*</span></label>
        <select id="uf_user_active" required class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors">
          <option value="Y" <?= ($isEdit ? ($user['user_active'] ?? 'Y') : 'Y') === 'Y' ? 'selected' : '' ?>>Aktif (Y)</option>
          <option value="N" <?= ($isEdit ? ($user['user_active'] ?? 'Y') : 'Y') === 'N' ? 'selected' : '' ?>>Non-Aktif (N)</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Blokir Akun</label>
        <select id="uf_user_block" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors">
          <option value="N" <?= ($isEdit ? ($user['user_block'] ?? 'N') : 'N') === 'N' ? 'selected' : '' ?>>Tidak (N)</option>
          <option value="Y" <?= ($isEdit ? ($user['user_block'] ?? 'N') : 'N') === 'Y' ? 'selected' : '' ?>>Ya (Y)</option>
        </select>
      </div>
    </div>

    <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 p-3.5 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
      <i class="fa-solid fa-circle-info text-amber-500 mt-0.5 shrink-0"></i>
      <span>Perubahan user, role, object scope, dan akses menu berlaku pada request berikutnya.</span>
    </div>

    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
      <button type="button" id="uf_cancel_btn" class="px-5 py-2.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 transition-colors">Batal</button>
      <button type="submit" id="uf_submit_btn" class="bg-[#2F3185] hover:bg-[#25276d] text-white font-semibold rounded-xl px-5 py-2.5 shadow-xs transition-all inline-flex items-center justify-center gap-2 active:scale-[0.98] text-xs disabled:opacity-50">
        <i id="uf_spinner" class="fa-solid fa-spinner fa-spin hidden"></i>
        <span id="uf_submit_text"><?= $isEdit ? 'Perbarui User' : 'Simpan User' ?></span>
      </button>
    </div>
  </form>
</div>

<script>
(function() {
  const roleBaselines = <?= json_encode($roleBaselines ?? [], JSON_HEX_TAG | JSON_HEX_APOS) ?>;
  const overrideMap = <?= json_encode($overrideMap, JSON_HEX_TAG | JSON_HEX_APOS) ?>;
  const roles = <?= json_encode($roles ?? [], JSON_HEX_TAG | JSON_HEX_APOS) ?>;
  const rolesObj = <?= json_encode($rolesObj ?? [], JSON_HEX_TAG | JSON_HEX_APOS) ?>;

  const form = document.getElementById('userFormElement');
  const userId = document.getElementById('uf_user_id').value;
  const cancelBtn = document.getElementById('uf_cancel_btn');
  const submitBtn = document.getElementById('uf_submit_btn');
  const spinner = document.getElementById('uf_spinner');
  const submitText = document.getElementById('uf_submit_text');
  const countEl = document.getElementById('uf_custom_menu_count');
  const menuCheckboxes = document.querySelectorAll('.uf-menu-checkbox');

  // Set title if editing
  const userNameVal = document.getElementById('uf_user_name').value;
  if (userId && window.Modal && typeof window.Modal.setTitle === 'function') {
    window.Modal.setTitle('Edit User — ' + userNameVal);
  }

  // Update initial labels
  const roleInput = document.getElementById('uf_role_id');
  const roleLabel = document.getElementById('uf_role_label');
  if (roleInput.value) {
    const found = roles.find(r => String(r.role_id) === String(roleInput.value));
    if (found) roleLabel.textContent = found.role_name_idn;
  }

  const objRoleInput = document.getElementById('uf_obj_role_id');
  const objRoleLabel = document.getElementById('uf_obj_role_label');
  if (objRoleInput.value) {
    const found = rolesObj.find(r => String(r.role_id) === String(objRoleInput.value));
    if (found) objRoleLabel.textContent = found.role_name_idn;
  }

  // Menu Role Dropdown Toggle & Search
  const roleToggleBtn = document.getElementById('uf_role_toggle_btn');
  const roleMenu = document.getElementById('uf_role_menu');
  const roleSearch = document.getElementById('uf_role_search');
  const roleArrow = document.getElementById('uf_role_arrow');
  const roleOptionsBtns = document.querySelectorAll('.uf-role-option');

  if (roleToggleBtn) {
    roleToggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isHidden = roleMenu.classList.contains('hidden');
      if (isHidden) {
        roleMenu.classList.remove('hidden');
        roleArrow.classList.add('rotate-180');
        if (roleSearch) roleSearch.focus();
      } else {
        roleMenu.classList.add('hidden');
        roleArrow.classList.remove('rotate-180');
      }
    });
  }

  if (roleSearch) {
    roleSearch.addEventListener('input', (e) => {
      const q = e.target.value.toLowerCase().trim();
      let matches = 0;
      roleOptionsBtns.forEach(btn => {
        const name = (btn.getAttribute('data-role-name') || '').toLowerCase();
        if (!q || name.includes(q)) {
          btn.classList.remove('hidden');
          matches++;
        } else {
          btn.classList.add('hidden');
        }
      });
      const emptyEl = document.getElementById('uf_role_empty');
      if (emptyEl) {
        if (matches === 0) emptyEl.classList.remove('hidden');
        else emptyEl.classList.add('hidden');
      }
    });
  }

  roleOptionsBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const rId = btn.getAttribute('data-role-id');
      const rName = btn.getAttribute('data-role-name');
      roleInput.value = rId;
      roleLabel.textContent = rName;
      roleLabel.classList.remove('text-gray-400', 'dark:text-gray-500');

      document.querySelectorAll('.uf-role-check').forEach(chk => chk.classList.add('hidden'));
      const activeChk = btn.querySelector('.uf-role-check');
      if (activeChk) activeChk.classList.remove('hidden');

      roleMenu.classList.add('hidden');
      roleArrow.classList.remove('rotate-180');

      // Update checkboxes according to baseline role
      const baseline = (roleBaselines[String(rId)] || []).map(Number);
      const effective = new Set(baseline);
      Object.entries(overrideMap || {}).forEach(([menuId, status]) => {
        if (status === 'Y') effective.add(Number(menuId));
        if (status === 'N') effective.delete(Number(menuId));
      });

      menuCheckboxes.forEach(cb => {
        cb.checked = effective.has(Number(cb.value));
      });
      updateCount();
    });
  });

  // Object Role Dropdown Toggle & Search
  const objRoleToggleBtn = document.getElementById('uf_obj_role_toggle_btn');
  const objRoleMenu = document.getElementById('uf_obj_role_menu');
  const objRoleSearch = document.getElementById('uf_obj_role_search');
  const objRoleArrow = document.getElementById('uf_obj_role_arrow');
  const objRoleOptionsBtns = document.querySelectorAll('.uf-obj-role-option');

  if (objRoleToggleBtn) {
    objRoleToggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isHidden = objRoleMenu.classList.contains('hidden');
      if (isHidden) {
        objRoleMenu.classList.remove('hidden');
        objRoleArrow.classList.add('rotate-180');
        if (objRoleSearch) objRoleSearch.focus();
      } else {
        objRoleMenu.classList.add('hidden');
        objRoleArrow.classList.remove('rotate-180');
      }
    });
  }

  if (objRoleSearch) {
    objRoleSearch.addEventListener('input', (e) => {
      const q = e.target.value.toLowerCase().trim();
      let matches = 0;
      objRoleOptionsBtns.forEach(btn => {
        const name = (btn.getAttribute('data-obj-role-name') || '').toLowerCase();
        if (!q || name.includes(q)) {
          btn.classList.remove('hidden');
          matches++;
        } else {
          btn.classList.add('hidden');
        }
      });
      const emptyEl = document.getElementById('uf_obj_role_empty');
      if (emptyEl) {
        if (matches === 0) emptyEl.classList.remove('hidden');
        else emptyEl.classList.add('hidden');
      }
    });
  }

  objRoleOptionsBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const rId = btn.getAttribute('data-obj-role-id');
      const rName = btn.getAttribute('data-obj-role-name');
      objRoleInput.value = rId;
      objRoleLabel.textContent = rName;
      objRoleLabel.classList.remove('text-gray-400', 'dark:text-gray-500');

      document.querySelectorAll('.uf-obj-role-check').forEach(chk => chk.classList.add('hidden'));
      const activeChk = btn.querySelector('.uf-obj-role-check');
      if (activeChk) activeChk.classList.remove('hidden');

      objRoleMenu.classList.add('hidden');
      objRoleArrow.classList.remove('rotate-180');
    });
  });

  // Close dropdowns on outside click
  document.addEventListener('click', (e) => {
    const roleContainer = document.getElementById('uf_role_dropdown_container');
    if (roleContainer && !roleContainer.contains(e.target)) {
      if (roleMenu) roleMenu.classList.add('hidden');
      if (roleArrow) roleArrow.classList.remove('rotate-180');
    }
    const objRoleContainer = document.getElementById('uf_obj_role_dropdown_container');
    if (objRoleContainer && !objRoleContainer.contains(e.target)) {
      if (objRoleMenu) objRoleMenu.classList.add('hidden');
      if (objRoleArrow) objRoleArrow.classList.remove('rotate-180');
    }
  });

  // Custom Menus
  function updateCount() {
    let checkedCount = 0;
    menuCheckboxes.forEach(cb => {
      if (cb.checked) checkedCount++;
    });
    if (countEl) countEl.textContent = checkedCount;
  }

  menuCheckboxes.forEach(cb => {
    cb.addEventListener('change', updateCount);
  });

  const selectAllBtn = document.getElementById('uf_select_all_menus_btn');
  if (selectAllBtn) {
    selectAllBtn.addEventListener('click', () => {
      menuCheckboxes.forEach(cb => cb.checked = true);
      updateCount();
    });
  }

  const clearAllBtn = document.getElementById('uf_clear_all_menus_btn');
  if (clearAllBtn) {
    clearAllBtn.addEventListener('click', () => {
      menuCheckboxes.forEach(cb => cb.checked = false);
      updateCount();
    });
  }

  // Cancel Button
  if (cancelBtn) {
    cancelBtn.addEventListener('click', () => {
      if (window.Modal && typeof window.Modal.close === 'function') {
        window.Modal.close();
      }
    });
  }

  // Form Submit
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      submitBtn.disabled = true;
      if (spinner) spinner.classList.remove('hidden');
      if (submitText) submitText.textContent = 'Menyimpan...';

      try {
        const body = new URLSearchParams();
        body.append('has_role_ids', '1');
        body.append('has_custom_menu_access', '1');

        if (userId) body.append('id', userId);
        body.append('user_username', document.getElementById('uf_user_username').value.trim());
        body.append('user_name', document.getElementById('uf_user_name').value.trim());
        body.append('user_email', document.getElementById('uf_user_email').value.trim());

        const passVal = document.getElementById('uf_user_password').value.trim();
        if (passVal) body.append('user_password', passVal);

        body.append('user_active', document.getElementById('uf_user_active').value);
        body.append('user_block', document.getElementById('uf_user_block').value);

        if (roleInput.value) {
          body.append('menu_role_ids[]', roleInput.value);
        }
        if (objRoleInput.value) {
          body.append('object_role_ids[]', objRoleInput.value);
        }

        menuCheckboxes.forEach(cb => {
          if (cb.checked) {
            body.append('menu_ids[]', cb.value);
          }
        });

        const res = await window.ypFetch('<?= $baseUrl ?>sys-admin/api/user/save', body);
        if (res && res.success) {
          if (window.Modal && typeof window.Modal.close === 'function') {
            window.Modal.close();
          }
          if (window.showToast) window.showToast('success', res.message || 'User berhasil disimpan');
          window.dispatchEvent(new CustomEvent('modal-saved', { detail: { entity: 'user' } }));
        } else {
          if (window.showToast) window.showToast('error', (res && res.message) || 'Gagal menyimpan user');
        }
      } catch (err) {
        if (window.showToast) window.showToast('error', 'Terjadi kesalahan sistem');
      } finally {
        submitBtn.disabled = false;
        if (spinner) spinner.classList.add('hidden');
        if (submitText) submitText.textContent = userId ? 'Perbarui User' : 'Simpan User';
      }
    });
  }
})();
</script>
