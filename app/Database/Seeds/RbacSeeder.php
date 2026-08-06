<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Phase 1.1 — Seed data RBAC.
 *
 * Mengisi:
 *   1. gw_sm__menu            : hirarki menu sesuai Bab 2 PRD (dicocokkan dengan
 *                               sidebar aktual aplikasi).
 *   2. gw_sm__menu_structure  : relasi parent ↔ child.
 *   3. gw_sm__role            : role default (Administrator & User).
 *   4. gw_sm__rolemenu        : permission role → menu.
 *   5. gw_sm__user_role       : menautkan user 'admin' ke role Administrator.
 *
 * Penggunaan:
 *   php spark db:seed RbacSeeder
 *
 * Idempotent — jika menu/role sudah ada (misal seeder sudah dijalankan),
 * data tidak akan diduplikasi.
 */
class RbacSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $this->seedMenus($now);
        $this->seedRoles($now);
        $this->seedAdminUserRole();
    }

    /**
     * Daftar menu — struktur [menu_id, group, nama_idn, nama_eng, icon, link, level, order, children[]]
     */
    private function menuDefinitions(): array
    {
        return [
            ['id' => 1,  'group' => 'MENU UTAMA',             'idn' => 'Dashboard',                       'eng' => 'Dashboard',                          'icon' => 'fa-solid fa-gauge-high',     'link' => 'dashboard',                         'level' => '1', 'order' => '1',  'children' => []],
            ['id' => 2,  'group' => 'MENU UTAMA',             'idn' => 'Monitoring Progress Entry',       'eng' => 'Monitoring Progress Entry',          'icon' => 'fa-solid fa-chart-simple',   'link' => 'monitoring',                        'level' => '1', 'order' => '2',  'children' => []],
            ['id' => 3,  'group' => 'MENU UTAMA',             'idn' => 'PL',                              'eng' => 'PL (Profit & Loss Consolidated)',    'icon' => 'fa-solid fa-file-invoice',   'link' => 'pl/summary',                        'level' => '1', 'order' => '3',  'children' => []],
            ['id' => 4,  'group' => 'BUDGET & PLANNING',      'idn' => 'FOH',                             'eng' => 'FOH',                                'icon' => 'fa-solid fa-industry',       'link' => '#',                                 'level' => '1', 'order' => '1',  'children' => [
                ['idn' => 'Entry Budget',              'eng' => 'Entry Budget',            'icon' => 'fa-regular fa-circle',    'link' => 'foh/entry',                  'level' => '2', 'order' => '1'],
                ['idn' => 'Realisasi (Actual)',        'eng' => 'Actual',                  'icon' => 'fa-regular fa-circle',    'link' => 'foh/actual',                 'level' => '2', 'order' => '2'],
                ['idn' => 'Summary',                   'eng' => 'Summary',                 'icon' => 'fa-regular fa-circle',    'link' => 'foh/summary',                'level' => '2', 'order' => '3'],
                ['idn' => 'Report Departemen',         'eng' => 'Department Report',       'icon' => 'fa-regular fa-circle',    'link' => 'foh/report/department',      'level' => '2', 'order' => '4'],
            ]],
            ['id' => 9,  'group' => 'BUDGET & PLANNING',      'idn' => 'Opex GA',                         'eng' => 'Opex GA',                            'icon' => 'fa-solid fa-building',       'link' => '#',                                 'level' => '1', 'order' => '2',  'children' => [
                ['idn' => 'Entry Budget',              'eng' => 'Entry Budget',            'icon' => 'fa-regular fa-circle',    'link' => 'opex-ga/entry',              'level' => '2', 'order' => '1'],
                ['idn' => 'Realisasi (Actual)',        'eng' => 'Actual',                  'icon' => 'fa-regular fa-circle',    'link' => 'opex-ga/actual',             'level' => '2', 'order' => '2'],
                ['idn' => 'Report Departemen',         'eng' => 'Department Report',       'icon' => 'fa-regular fa-circle',    'link' => 'opex-ga/report/department',  'level' => '2', 'order' => '3'],
                ['idn' => 'Report Combined',           'eng' => 'Combined Report',         'icon' => 'fa-regular fa-circle',    'link' => 'opex-ga/report/combine',     'level' => '2', 'order' => '4'],
            ]],
            ['id' => 14, 'group' => 'BUDGET & PLANNING',      'idn' => 'Opex Selling',                    'eng' => 'Opex Selling',                       'icon' => 'fa-solid fa-store',          'link' => '#',                                 'level' => '1', 'order' => '3',  'children' => [
                ['idn' => 'Entry Budget',              'eng' => 'Entry Budget',            'icon' => 'fa-regular fa-circle',    'link' => 'opex-selling/entry',         'level' => '2', 'order' => '1'],
                ['idn' => 'Realisasi (Actual)',        'eng' => 'Actual',                  'icon' => 'fa-regular fa-circle',    'link' => 'opex-selling/actual',        'level' => '2', 'order' => '2'],
                ['idn' => 'Report Departemen',         'eng' => 'Department Report',       'icon' => 'fa-regular fa-circle',    'link' => 'opex-selling/report/department', 'level' => '2', 'order' => '3'],
            ]],
            ['id' => 18, 'group' => 'BUDGET & PLANNING',      'idn' => 'Capex',                           'eng' => 'CAPEX',                              'icon' => 'fa-solid fa-coins',          'link' => '#',                                 'level' => '1', 'order' => '4',  'children' => [
                ['idn' => 'Entry CAPEX',               'eng' => 'Entry CAPEX',            'icon' => 'fa-regular fa-circle',    'link' => 'capex/entry',                'level' => '2', 'order' => '1'],
                ['idn' => 'Summary',                   'eng' => 'Summary',                 'icon' => 'fa-regular fa-circle',    'link' => 'capex/summary',              'level' => '2', 'order' => '2'],
                ['idn' => 'Report',                    'eng' => 'Report',                  'icon' => 'fa-regular fa-circle',    'link' => 'capex/report',               'level' => '2', 'order' => '3'],
            ]],
            ['id' => 22, 'group' => 'BUDGET & PLANNING',      'idn' => 'Sales',                           'eng' => 'Sales',                              'icon' => 'fa-solid fa-chart-line',     'link' => '#',                                 'level' => '1', 'order' => '5',  'children' => [
                ['idn' => 'Entry Domestic',            'eng' => 'Domestic Entry',         'icon' => 'fa-regular fa-circle',    'link' => 'sales/domestic/entry',       'level' => '2', 'order' => '1'],
                ['idn' => 'Entry Export',              'eng' => 'Export Entry',            'icon' => 'fa-regular fa-circle',    'link' => 'sales/export/entry',         'level' => '2', 'order' => '2'],
                ['idn' => 'Summary Keseluruhan',       'eng' => 'Overall Summary',         'icon' => 'fa-regular fa-circle',    'link' => 'sales/summary',              'level' => '2', 'order' => '3'],
                ['idn' => 'Summary Domestic',          'eng' => 'Domestic Summary',        'icon' => 'fa-regular fa-circle',    'link' => 'sales/summary/domestic',     'level' => '2', 'order' => '4'],
                ['idn' => 'Summary Export',            'eng' => 'Export Summary',          'icon' => 'fa-regular fa-circle',    'link' => 'sales/summary/export',       'level' => '2', 'order' => '5'],
                ['idn' => 'Summary per Negara',        'eng' => 'Summary by Country',      'icon' => 'fa-regular fa-circle',    'link' => 'sales/summary/country',      'level' => '2', 'order' => '6'],
                ['idn' => 'Summary per Region',        'eng' => 'Summary by Region',       'icon' => 'fa-regular fa-circle',    'link' => 'sales/summary/region',       'level' => '2', 'order' => '7'],
            ]],
            ['id' => 30, 'group' => 'BUDGET & PLANNING',      'idn' => 'Man Power Planning',              'eng' => 'Man Power Planning',                 'icon' => 'fa-solid fa-users',          'link' => '#',                                 'level' => '1', 'order' => '6',  'children' => [
                ['idn' => 'Entry MPP',                 'eng' => 'MPP Entry',               'icon' => 'fa-regular fa-circle',    'link' => 'mpp/entry',                  'level' => '2', 'order' => '1'],
                ['idn' => 'Summary',                   'eng' => 'Summary',                 'icon' => 'fa-regular fa-circle',    'link' => 'mpp/summary',                'level' => '2', 'order' => '2'],
            ]],
            ['id' => 33, 'group' => 'MASTER & PENGATURAN',    'idn' => 'New Head Account',                'eng' => 'New Head Account',                   'icon' => 'fa-solid fa-folder-plus',    'link' => '#',                                 'level' => '1', 'order' => '1',  'children' => [
                ['idn' => 'Daftar Pengajuan',          'eng' => 'Submission List',        'icon' => 'fa-regular fa-circle',    'link' => 'new-head-account',           'level' => '2', 'order' => '1'],
                ['idn' => 'Pengajuan Baru',            'eng' => 'New Submission',          'icon' => 'fa-regular fa-circle',    'link' => 'new-head-account/create',    'level' => '2', 'order' => '2'],
            ]],
            ['id' => 36, 'group' => 'MASTER & PENGATURAN',    'idn' => 'Master Data',                     'eng' => 'Master Data',                        'icon' => 'fa-solid fa-database',       'link' => '#',                                 'level' => '1', 'order' => '2',  'children' => [
                ['idn' => 'Master COA',                'eng' => 'Chart of Account',        'icon' => 'fa-regular fa-circle',    'link' => 'master/coa',                 'level' => '2', 'order' => '1'],
                ['idn' => 'Master Cost Center',        'eng' => 'Cost Center',             'icon' => 'fa-regular fa-circle',    'link' => 'master/cost-center',         'level' => '2', 'order' => '2'],
                ['idn' => 'Master Departemen',         'eng' => 'Department',              'icon' => 'fa-regular fa-circle',    'link' => 'master/department',          'level' => '2', 'order' => '3'],
                ['idn' => 'Master Product',            'eng' => 'Product',                 'icon' => 'fa-regular fa-circle',    'link' => 'master/product',             'level' => '2', 'order' => '4'],
                ['idn' => 'Master Salary MPP',         'eng' => 'Salary MPP',              'icon' => 'fa-regular fa-circle',    'link' => 'master/salary-mpp',          'level' => '2', 'order' => '5'],
                ['idn' => 'Configure Period',          'eng' => 'Configure Period',        'icon' => 'fa-regular fa-circle',    'link' => 'master/configure-period',    'level' => '2', 'order' => '6'],
            ]],
            ['id' => 43, 'group' => 'MASTER & PENGATURAN',    'idn' => 'System Administration',           'eng' => 'System Administration',              'icon' => 'fa-solid fa-user-shield',    'link' => '#',                                 'level' => '1', 'order' => '3',  'children' => [
                ['idn' => 'Menu Configuration',        'eng' => 'Menu Configuration',      'icon' => 'fa-regular fa-circle',    'link' => 'sys-admin/menu',             'level' => '2', 'order' => '1'],
                ['idn' => 'Role Management',           'eng' => 'Role Management',         'icon' => 'fa-regular fa-circle',    'link' => 'sys-admin/role',             'level' => '2', 'order' => '2'],
                ['idn' => 'User Management',           'eng' => 'User Management',         'icon' => 'fa-regular fa-circle',    'link' => 'sys-admin/user',             'level' => '2', 'order' => '3'],
            ]],
        ];
    }

    private function seedMenus(string $now): void
    {
        // Jika menu sudah terisi, lewati (idempotent).
        $existing = $this->db->table('gw_sm__menu')->countAllResults();
        if ($existing > 0) {
            echo "gw_sm__menu sudah berisi {$existing} baris — lewati seeding menu." . PHP_EOL;
            return;
        }

        $createdBy = 'system';
        // Id child menu dimulai SETELAH id parent terbesar (43) agar tidak
        // bentrok dengan id parent yang di-insert eksplisit (1..43).
        $menuId    = 44;

        foreach ($this->menuDefinitions() as $parent) {
            $this->db->table('gw_sm__menu')->insert([
                'menu_id'          => (int) $parent['id'],
                'menu_name_idn'    => $parent['idn'],
                'menu_name_eng'    => $parent['eng'],
                'menu_name_jpn'    => '',
                'menu_parrent'     => empty($parent['children']) ? 'N' : 'Y',
                'menu_active'      => 'Y',
                'menu_icon'        => $parent['icon'],
                'menu_link'        => $parent['link'],
                'menu_group'       => $parent['group'],
                'menu_level'       => $parent['level'],
                'menu_order'       => $parent['order'],
                'menu_lower_level' => empty($parent['children']) ? 'N' : 'Y',
                'menu_module_code' => '',
                'menu_created_on'  => $now,
                'menu_created_by'  => $createdBy,
            ]);

            foreach ($parent['children'] as $child) {
                $this->db->table('gw_sm__menu')->insert([
                    'menu_id'          => $menuId,
                    'menu_name_idn'    => $child['idn'],
                    'menu_name_eng'    => $child['eng'],
                    'menu_name_jpn'    => '',
                    'menu_parrent'     => 'N',
                    'menu_active'      => 'Y',
                    'menu_icon'        => $child['icon'],
                    'menu_link'        => $child['link'],
                    'menu_group'       => $parent['group'],
                    'menu_level'       => $child['level'],
                    'menu_order'       => $child['order'],
                    'menu_lower_level' => 'N',
                    'menu_module_code' => '',
                    'menu_created_on'  => $now,
                    'menu_created_by'  => $createdBy,
                ]);

                $this->db->table('gw_sm__menu_structure')->insert([
                    'structure_menu_id'       => (int) $parent['id'],
                    'structure_child_menu_id' => $menuId,
                    'structure_created_on'    => $now,
                    'structure_created_by'    => $createdBy,
                ]);

                $menuId++;
            }
        }

        echo 'Hirarki menu berhasil di-seed (' . $this->db->table('gw_sm__menu')->countAllResults() . ' menu).' . PHP_EOL;
    }

    private function seedRoles(string $now): void
    {
        $existing = $this->db->table('gw_sm__role')->countAllResults();
        if ($existing > 0) {
            echo "gw_sm__role sudah berisi {$existing} baris — lewati seeding role." . PHP_EOL;
            return;
        }

        $roles = [
            ['role_id' => 1, 'idn' => 'Administrator', 'eng' => 'Administrator', 'type' => 'menu'],
            ['role_id' => 2, 'idn' => 'User',          'eng' => 'User',          'type' => 'menu'],
        ];

        $allMenuIds  = array_column($this->db->table('gw_sm__menu')->select('menu_id')->get()->getResultArray(), 'menu_id');

        // Role User: tanpa menu Sys Admin (link diawali 'sys-admin')
        $sysAdminLinks = array_column($this->db->table('gw_sm__menu')
            ->select('menu_id')
            ->like('menu_link', 'sys-admin', 'after')
            ->get()
            ->getResultArray(), 'menu_id');

        $adminMenuIds = array_values(array_filter($allMenuIds, fn($id) => ! in_array($id, $sysAdminLinks, true)));

        foreach ($roles as $role) {
            $this->db->table('gw_sm__role')->insert([
                'role_id'          => $role['role_id'],
                'role_name_idn'    => $role['idn'],
                'role_name_eng'    => $role['eng'],
                'role_name_jpn'    => '',
                'role_active'      => 'Y',
                'role_type'        => $role['type'],
                'role_created_on'  => $now,
                'role_created_by'  => 'system',
            ]);

            $grantMenuIds = $role['role_id'] === 1 ? $allMenuIds : $adminMenuIds;

            foreach ($grantMenuIds as $menuId) {
                $this->db->table('gw_sm__rolemenu')->insert([
                    'rolemenu_role_id' => $role['role_id'],
                    'rolemenu_menu_id' => $menuId,
                    'rolemenu_active'  => 'Y',
                ]);
            }
        }

        echo 'Role default & permission menu berhasil di-seed.' . PHP_EOL;
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

        $linked = $this->db->table('gw_sm__user_role')
            ->where('user_role_user_id', $admin->user_id)
            ->where('user_role_role_id', 1)
            ->countAllResults();

        if ($linked > 0) {
            echo 'User "admin" sudah tertaut ke role Administrator.' . PHP_EOL;
            return;
        }

        $this->db->table('gw_sm__user_role')->insert([
            'user_role_user_id'    => $admin->user_id,
            'user_role_role_id'    => 1,
            'user_role_created_on' => date('Y-m-d H:i:s'),
        ]);

        echo 'User "admin" berhasil ditautkan ke role Administrator.' . PHP_EOL;
    }
}
