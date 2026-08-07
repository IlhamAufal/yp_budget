<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Phase 1.1 — Seed data RBAC (idempotent + self-healing).
 *
 * Mengisi / menyelaraskan:
 *   1. gw_sm__menu            : hirarki menu sesuai sidebar aktual aplikasi
 *                               (dicocokkan 1:1 dengan sidebar.php hardcoded
 *                               lama agar transisi ke MenuBuilder seamless).
 *   2. gw_sm__menu_structure  : relasi parent ↔ child.
 *   3. gw_sm__role            : role default (Administrator & User).
 *   4. gw_sm__rolemenu        : permission role → menu.
 *   5. gw_sm__user_role       : menautkan user 'admin' ke role Administrator.
 *
 * Penggunaan:
 *   php spark db:seed RbacSeeder
 *
 * Idempotent — aman dijalankan ulang kapan saja:
 *   - Menu yang sudah ada di-UPdate, menu baru di-INSERT, menu yang tidak
 *     lagi terdefinisi (mis. 'New Head Account' lama) di-HAPUS beserta
 *     relasi structure & rolemenu-nya.
 */
class RbacSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $validMenuIds = $this->syncMenus($now);
        $this->syncRoles($now, $validMenuIds);
        $this->seedAdminUserRole();
    }

    /**
     * Daftar menu — struktur [menu_id, group, nama_idn, nama_eng, icon, link, level, order, children[]]
     * 1:1 dengan sidebar.php yang dirender dinamis (MenuBuilder).
     */
    private function menuDefinitions(): array
    {
        return [
            ['id' => 1,  'group' => 'MENU UTAMA',             'idn' => 'Dashboard',                 'eng' => 'Dashboard',                          'icon' => 'fa-solid fa-gauge-high',     'link' => 'dashboard',       'level' => '1', 'order' => '1',  'children' => []],
            ['id' => 2,  'group' => 'MENU UTAMA',             'idn' => 'Monitoring Progress Entry', 'eng' => 'Monitoring Progress Entry',          'icon' => 'fa-solid fa-clipboard-check', 'link' => 'monitoring',    'level' => '1', 'order' => '2',  'children' => []],
            ['id' => 3,  'group' => 'MENU UTAMA',             'idn' => 'Performance & Loss',        'eng' => 'P&L (Profit & Loss Consolidated)',    'icon' => 'fa-solid fa-chart-pie',      'link' => 'pl',              'level' => '1', 'order' => '3',  'children' => []],
            ['id' => 4,  'group' => 'BUDGET & PLANNING',      'idn' => 'FOH',                       'eng' => 'FOH',                                'icon' => 'fa-solid fa-industry',       'link' => '#',               'level' => '1', 'order' => '1',  'children' => [
                ['idn' => 'Entry Budget',              'eng' => 'Entry Budget',            'icon' => 'fa-regular fa-circle',    'link' => 'foh/entry',                  'level' => '2', 'order' => '1'],
                ['idn' => 'Realisasi (Actual)',        'eng' => 'Actual',                  'icon' => 'fa-regular fa-circle',    'link' => 'foh/actual',                 'level' => '2', 'order' => '2'],
                ['idn' => 'Summary',                   'eng' => 'Summary',                 'icon' => 'fa-regular fa-circle',    'link' => 'foh/summary',                'level' => '2', 'order' => '3'],
            ]],
            ['id' => 9,  'group' => 'BUDGET & PLANNING',      'idn' => 'Opex GA',                   'eng' => 'Opex GA',                            'icon' => 'fa-solid fa-building',       'link' => '#',               'level' => '1', 'order' => '2',  'children' => [
                ['idn' => 'Entry Budget',              'eng' => 'Entry Budget',            'icon' => 'fa-regular fa-circle',    'link' => 'opex-ga/entry',              'level' => '2', 'order' => '1'],
                ['idn' => 'Realisasi (Actual)',        'eng' => 'Actual',                  'icon' => 'fa-regular fa-circle',    'link' => 'opex-ga/actual',             'level' => '2', 'order' => '2'],
            ]],
            ['id' => 14, 'group' => 'BUDGET & PLANNING',      'idn' => 'Opex Selling',              'eng' => 'Opex Selling',                       'icon' => 'fa-solid fa-store',          'link' => '#',               'level' => '1', 'order' => '3',  'children' => [
                ['idn' => 'Entry Budget',              'eng' => 'Entry Budget',            'icon' => 'fa-regular fa-circle',    'link' => 'opex-selling/entry',         'level' => '2', 'order' => '1'],
                ['idn' => 'Realisasi (Actual)',        'eng' => 'Actual',                  'icon' => 'fa-regular fa-circle',    'link' => 'opex-selling/actual',        'level' => '2', 'order' => '2'],
            ]],
            ['id' => 18, 'group' => 'BUDGET & PLANNING',      'idn' => 'Capex',                     'eng' => 'CAPEX',                              'icon' => 'fa-solid fa-coins',          'link' => '#',               'level' => '1', 'order' => '4',  'children' => [
                ['idn' => 'Entry CAPEX',               'eng' => 'Entry CAPEX',            'icon' => 'fa-regular fa-circle',    'link' => 'capex/entry',                'level' => '2', 'order' => '1'],
                ['idn' => 'Summary',                   'eng' => 'Summary',                 'icon' => 'fa-regular fa-circle',    'link' => 'capex/summary',              'level' => '2', 'order' => '2'],
            ]],
            ['id' => 22, 'group' => 'BUDGET & PLANNING',      'idn' => 'Sales',                     'eng' => 'Sales',                              'icon' => 'fa-solid fa-chart-line',     'link' => '#',               'level' => '1', 'order' => '5',  'children' => [
                ['idn' => 'Entry Domestic',            'eng' => 'Domestic Entry',         'icon' => 'fa-regular fa-circle',    'link' => 'sales/domestic/entry',       'level' => '2', 'order' => '1'],
                ['idn' => 'Entry International',       'eng' => 'International Entry',     'icon' => 'fa-regular fa-circle',    'link' => 'sales/export/entry',         'level' => '2', 'order' => '2'],
            ]],
            ['id' => 30, 'group' => 'MASTER & PENGATURAN',    'idn' => 'Man Power Planning',        'eng' => 'Man Power Planning',                 'icon' => 'fa-solid fa-folder-plus',    'link' => '#',               'level' => '1', 'order' => '1',  'children' => [
                ['idn' => 'Entry MPP',                 'eng' => 'MPP Entry',               'icon' => 'fa-regular fa-circle',    'link' => 'mpp/entry',                  'level' => '2', 'order' => '1'],
                ['idn' => 'Summary',                   'eng' => 'Summary',                 'icon' => 'fa-regular fa-circle',    'link' => 'mpp/summary',                'level' => '2', 'order' => '2'],
            ]],
            ['id' => 36, 'group' => 'MASTER & PENGATURAN',    'idn' => 'Master Data',               'eng' => 'Master Data',                        'icon' => 'fa-solid fa-database',       'link' => '#',               'level' => '1', 'order' => '2',  'children' => [
                ['idn' => 'Master COA',                'eng' => 'Chart of Account',        'icon' => 'fa-regular fa-circle',    'link' => 'master/coa',                 'level' => '2', 'order' => '1'],
                ['idn' => 'Master Cost Center',        'eng' => 'Cost Center',             'icon' => 'fa-regular fa-circle',    'link' => 'master/cost-center',         'level' => '2', 'order' => '2'],
                ['idn' => 'Master Departemen',         'eng' => 'Department',              'icon' => 'fa-regular fa-circle',    'link' => 'master/department',          'level' => '2', 'order' => '3'],
                ['idn' => 'Master Product',            'eng' => 'Product',                 'icon' => 'fa-regular fa-circle',    'link' => 'master/product',             'level' => '2', 'order' => '4'],
                ['idn' => 'Master Salary MPP',         'eng' => 'Salary MPP',              'icon' => 'fa-regular fa-circle',    'link' => 'master/salary-mpp',          'level' => '2', 'order' => '5'],
                ['idn' => 'Configure Period',          'eng' => 'Configure Period',        'icon' => 'fa-regular fa-circle',    'link' => 'master/configure-period',    'level' => '2', 'order' => '6'],
            ]],
            ['id' => 43, 'group' => 'MASTER & PENGATURAN',    'idn' => 'System Administration',     'eng' => 'System Administration',              'icon' => 'fa-solid fa-user-shield',    'link' => '#',               'level' => '1', 'order' => '3',  'children' => [
                ['idn' => 'Menu Configuration',        'eng' => 'Menu Configuration',      'icon' => 'fa-regular fa-circle',    'link' => 'sys-admin/menu',             'level' => '2', 'order' => '1'],
                ['idn' => 'Role Management',           'eng' => 'Role Management',         'icon' => 'fa-regular fa-circle',    'link' => 'sys-admin/role',             'level' => '2', 'order' => '2'],
                ['idn' => 'User Management',           'eng' => 'User Management',         'icon' => 'fa-regular fa-circle',    'link' => 'sys-admin/user',             'level' => '2', 'order' => '3'],
            ]],
        ];
    }

    /**
     * Sinkronkan tabel gw_sm__menu + gw_sm__menu_structure agar SELALU
     * sama persis dengan definisi di atas (upsert + cleanup).
     *
     * @return int[] daftar menu_id valid
     */
    private function syncMenus(string $now): array
    {
        $createdBy = 'system';
        $menuId    = 44; // id child dimulai SETELAH id parent terbesar (43)
        $validIds  = [];

        foreach ($this->menuDefinitions() as $parent) {
            $parentRow = $this->menuRow($parent, $now, $createdBy);
            $parentRow['menu_id'] = (int) $parent['id'];

            $this->upsertMenu($parentRow);
            $validIds[] = (int) $parent['id'];

            // Refresh relasi structure parent (hapus dulu, insert ulang)
            $this->db->table('gw_sm__menu_structure')
                ->where('structure_menu_id', (int) $parent['id'])
                ->delete();

            foreach ($parent['children'] as $child) {
                $childRow = $this->menuRow($child, $now, $createdBy, (int) $parent['id'], $parent['group']);
                $childRow['menu_id'] = $menuId;

                $this->upsertMenu($childRow);
                $validIds[] = $menuId;

                $this->db->table('gw_sm__menu_structure')->insert([
                    'structure_menu_id'        => (int) $parent['id'],
                    'structure_child_menu_id' => $menuId,
                    'structure_created_on'    => $now,
                    'structure_created_by'    => $createdBy,
                ]);

                $menuId++;
            }
        }

        // Cleanup: hapus menu yang tidak lagi terdefinisi beserta relasinya
        $this->cleanupOrphans($validIds);

        echo 'gw_sm__menu disinkronkan (' . $this->db->table('gw_sm__menu')->countAllResults() . ' menu aktif).' . PHP_EOL;

        return $validIds;
    }

    /**
     * Susun satu baris kolom gw_sm__menu.
     */
    private function menuRow(array $def, string $now, string $createdBy, ?int $parentId = null, string $group = ''): array
    {
        $isParent = $parentId === null;

        $row = [
            'menu_name_idn'    => $def['idn'],
            'menu_name_eng'    => $def['eng'],
            'menu_name_jpn'    => '',
            'menu_parrent'     => $isParent ? 'Y' : 'N',
            'menu_active'      => 'Y',
            'menu_icon'        => $def['icon'],
            'menu_link'        => $def['link'],
            'menu_level'       => $def['level'],
            'menu_order'       => $def['order'],
            'menu_lower_level' => $isParent ? 'Y' : 'N',
            'menu_module_code' => '',
            'menu_created_on'  => $now,
            'menu_created_by'  => $createdBy,
        ];

        // Kolom menu_group tidak ada di skema legacy (keputusan user 6 Agt 2026:
        // jangan ubah struktur) — hanya disertakan bila tersedia.
        if (\App\Libraries\DbCompat::hasMenuGroup()) {
            $row['menu_group'] = $group !== '' ? $group : ($def['group'] ?? '');
        }

        return $row;
    }

    /**
     * Upsert baris menu berdasarkan menu_id (update bila ada, insert bila belum).
     */
    private function upsertMenu(array $row): void
    {
        $menuId = $row['menu_id'];

        $exists = $this->db->table('gw_sm__menu')
            ->where('menu_id', $menuId)
            ->countAllResults();

        if ($exists > 0) {
            $this->db->table('gw_sm__menu')->where('menu_id', $menuId)->update($row);
        } else {
            $this->db->table('gw_sm__menu')->insert($row);
        }
    }

    /**
     * Hapus menu hasil seeder ('system') yang tidak lagi terdefinisi beserta
     * relasi structure & rolemenu-nya. Menu custom yang dibuat via UI
     * sys-admin (menu_created_by = user) TIDAK disentuh.
     */
    private function cleanupOrphans(array $validIds): void
    {
        if (empty($validIds)) {
            return;
        }

        $staleIds = array_column($this->db->table('gw_sm__menu')
            ->select('menu_id')
            ->where('menu_created_by', 'system')
            ->whereNotIn('menu_id', $validIds)
            ->get()
            ->getResultArray(), 'menu_id');

        if (empty($staleIds)) {
            return;
        }

        $this->db->table('gw_sm__menu')->whereIn('menu_id', $staleIds)->delete();

        // Relasi structure yang menghubungkan DUA menu yang sama-sama dihapus
        $this->db->table('gw_sm__menu_structure')
            ->whereIn('structure_menu_id', $staleIds)
            ->whereIn('structure_child_menu_id', $staleIds)
            ->delete();

        $this->db->table('gw_sm__rolemenu')
            ->whereIn('rolemenu_menu_id', $staleIds)
            ->delete();
    }

    /**
     * Sinkronkan role default + permission rolemenu agar selalu konsisten
     * dengan daftar menu valid saat ini.
     */
    private function syncRoles(string $now, array $validMenuIds): void
    {
        if (empty($validMenuIds)) {
            return;
        }

        // Role 1 = Administrator (semua menu), Role 2 = User (tanpa Sys Admin)
        $roles = [
            ['role_id' => 1, 'idn' => 'Administrator', 'eng' => 'Administrator', 'type' => 'menu'],
            ['role_id' => 2, 'idn' => 'User',          'eng' => 'User',          'type' => 'menu'],
        ];

        // Menu Sys Admin: child dengan link 'sys-admin/...' + parent-nya
        $sysChildIds = array_column($this->db->table('gw_sm__menu')
            ->select('menu_id')
            ->like('menu_link', 'sys-admin', 'after')
            ->get()
            ->getResultArray(), 'menu_id');

        $sysParentIds = [];
        if (! empty($sysChildIds)) {
            $sysParentIds = array_column($this->db->table('gw_sm__menu_structure')
                ->select('structure_menu_id')
                ->whereIn('structure_child_menu_id', $sysChildIds)
                ->get()
                ->getResultArray(), 'structure_menu_id');
        }

        $excluded = array_map('intval', array_merge($sysChildIds, $sysParentIds));

        foreach ($roles as $role) {
            $roleRow = [
                'role_name_idn'   => $role['idn'],
                'role_name_eng'   => $role['eng'],
                'role_name_jpn'   => '',
                'role_active'     => 'Y',
                'role_type'       => $role['type'],
                'role_created_on' => $now,
                'role_created_by' => 'system',
            ];

            $exists = $this->db->table('gw_sm__role')
                ->where('role_id', $role['role_id'])
                ->countAllResults();

            if ($exists > 0) {
                $this->db->table('gw_sm__role')->where('role_id', $role['role_id'])->update($roleRow);
            } else {
                $roleRow['role_id'] = $role['role_id'];
                $this->db->table('gw_sm__role')->insert($roleRow);
            }

            $granted = ($role['role_id'] === 1)
                ? $validMenuIds
                : array_values(array_diff($validMenuIds, $excluded));

            // Hapus permission system yang tidak lagi berlaku
            // (hanya menyentuh menu hasil seeder, bukan menu custom buatan UI)
            $systemMenuIds = array_map('intval', array_column($this->db->table('gw_sm__menu')
                ->select('menu_id')
                ->where('menu_created_by', 'system')
                ->get()
                ->getResultArray(), 'menu_id'));

            $this->db->table('gw_sm__rolemenu')
                ->where('rolemenu_role_id', $role['role_id'])
                ->whereIn('rolemenu_menu_id', $systemMenuIds)
                ->whereNotIn('rolemenu_menu_id', $granted)
                ->delete();

            // Tambah permission yang belum ada
            $existingMenuIds = array_map('intval', array_column($this->db->table('gw_sm__rolemenu')
                ->select('rolemenu_menu_id')
                ->where('rolemenu_role_id', $role['role_id'])
                ->get()
                ->getResultArray(), 'rolemenu_menu_id'));

            foreach ($granted as $menuId) {
                if (! in_array((int) $menuId, $existingMenuIds, true)) {
                    $this->db->table('gw_sm__rolemenu')->insert([
                        'rolemenu_role_id' => $role['role_id'],
                        'rolemenu_menu_id' => (int) $menuId,
                        'rolemenu_active'  => 'Y',
                    ]);
                }
            }
        }

        echo 'Role default & permission menu disinkronkan.' . PHP_EOL;
    }

    private function seedAdminUserRole(): void
    {
        $admin = $this->db->table('gw_sm__user')
            ->where('user_username', 'admin')
            ->get()
            ->getRow();

        if (! $admin) {
            echo 'User "admin" tidak ditemukan — jalankan UserSeeder terlebih dahulu.' . PHP_EOL;

            return;
        }

        $linked = $this->db->table('gw_sm__profile')
            ->where('profile_user_id', $admin->user_id)
            ->where('profile_role_id', 1)
            ->countAllResults();

        if ($linked > 0) {
            echo 'User "admin" sudah tertaut ke role Administrator.' . PHP_EOL;

            return;
        }

        $this->db->table('gw_sm__profile')->insert([
            'profile_user_id'    => $admin->user_id,
            'profile_role_id'    => 1,
            'profile_created_on' => date('Y-m-d H:i:s'),
        ]);

        echo 'User "admin" berhasil ditautkan ke role Administrator.' . PHP_EOL;
    }
}
