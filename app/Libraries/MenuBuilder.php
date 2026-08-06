<?php

namespace App\Libraries;

use App\Models\RoleModel;

/**
 * MenuBuilder — Dynamic Sidebar Navigation (PRD Phase 1.2).
 *
 * Merender sidebar dari database (gw_sm__menu + gw_sm__menu_structure)
 * sesuai hirarki resmi Bab 2 PRD, dengan:
 *   - Filter hak akses berdasarkan role user (gw_sm__rolemenu).
 *   - Kelompok menu (menu_group), parent-child (menu_structure).
 *   - Status aktif otomatis berdasarkan URL saat ini.
 *   - Markup TailAdmin + Alpine.js yang sama dengan sidebar statis lama
 *     (dropdown expand/collapse memakai state `selected`).
 *
 * Dipanggil dari helper menu_helper.php → render_menu_sidebar().
 */
class MenuBuilder
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Ambil menu_id yang boleh diakses user berdasarkan session.
     *
     * @return array|null null = semua menu boleh diakses (admin / belum ada role)
     */
    public function getAllowedMenuIds(): ?array
    {
        $session = session();

        if (! $session->get('user_logged_in')) {
            return [];
        }

        // Admin dapat melihat seluruh menu
        if ($session->get('is_admin')) {
            return null;
        }

        $roleIds = (array) ($session->get('role_ids') ?? []);
        if (empty($roleIds)) {
            // Fail-open: user tanpa role belum dibatasi (masa migrasi RBAC)
            return null;
        }

        $roleModel = new RoleModel();
        $ids       = [];
        foreach ($roleIds as $roleId) {
            $ids = array_merge($ids, $roleModel->getMenuIdsByRole((int) $roleId));
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }

    /**
     * Render HTML sidebar lengkap (dari <nav> hingga </nav>).
     */
    public function render(): string
    {
        $allowed = $this->getAllowedMenuIds();

        if ($allowed !== null && empty($allowed)) {
            return '<nav class="px-5 py-6 text-xs text-gray-400 dark:text-gray-500">Tidak ada menu yang dapat diakses. Hubungi Administrator.</nav>';
        }

        // 1. Semua menu aktif
        $menus = $this->db->table('gw_sm__menu')
            ->where('menu_active', 'Y')
            ->orderBy('menu_group', 'ASC')
            ->orderBy('menu_order', 'ASC')
            ->orderBy('menu_id', 'ASC')
            ->get()
            ->getResultArray();

        // 2. Filter by role permission
        $menuById = [];
        foreach ($menus as $menu) {
            if ($allowed !== null && ! in_array((int) $menu['menu_id'], $allowed, true)) {
                continue;
            }
            $menuById[(int) $menu['menu_id']] = $menu;
        }

        // 3. Struktur parent-child
        $childrenByParent = [];
        $structureRows    = $this->db->table('gw_sm__menu_structure')->get()->getResultArray();
        foreach ($structureRows as $s) {
            $parentId = (int) $s['structure_menu_id'];
            $childId  = (int) $s['structure_child_menu_id'];
            if (isset($menuById[$parentId], $menuById[$childId])) {
                $childrenByParent[$parentId][$childId] = $menuById[$childId];
            }
        }
        // Sort children by menu_order
        foreach ($childrenByParent as $parentId => $children) {
            uasort($children, function ($a, $b) {
                $oa = (int) ($a['menu_order'] ?? 0);
                $ob = (int) ($b['menu_order'] ?? 0);
                return $oa <=> $ob ?: ((int) $a['menu_id'] <=> (int) $b['menu_id']);
            });
            $childrenByParent[$parentId] = $children;
        }

        // 4. Kelompokkan parent per menu_group
        $currentPath = ltrim((string) uri_string(), '/');
        $groups      = [];
        foreach ($menuById as $menuId => $menu) {
            if (isset($childrenByParent[$menuId]) || ($menu['menu_level'] ?? '2') === '1') {
                $groupKey = ! empty($menu['menu_group']) ? $menu['menu_group'] : 'MENU UTAMA';
                $groups[$groupKey][$menuId] = $menu;
            }
        }

        // 5. Tentukan grup aktif (grup yang berisi menu aktif) untuk state `selected`
        $activeGroupKey = $this->findActiveGroup($groups, $childrenByParent, $currentPath);

        // 6. Render
        $html = '<nav x-data="' . esc(json_encode(['selected' => $activeGroupKey]), 'attr') . '">';

        foreach ($groups as $groupName => $parents) {
            $html .= '<div class="mb-6">';
            $html .= '<h3 class="mb-4 text-xs font-semibold leading-[20px] text-gray-400">'
                . '<span class="menu-group-title" :class="sidebarToggle ? \'lg:hidden\' : \'\'">'
                . esc($groupName) . '</span></h3>';
            $html .= '<ul class="flex flex-col gap-1.5">';

            foreach ($parents as $menuId => $parent) {
                $children = $childrenByParent[$menuId] ?? [];
                if (! empty($children)) {
                    $html .= $this->renderParent($parent, $children, $currentPath);
                } else {
                    $html .= $this->renderLeaf($parent, $currentPath);
                }
            }

            $html .= '</ul></div>';
        }

        $html .= '</nav>';

        return $html;
    }

    /* ------------------------------------------------------------------
     * Render helpers
     * ------------------------------------------------------------------ */

    protected function renderLeaf(array $menu, string $currentPath): string
    {
        $link   = trim((string) ($menu['menu_link'] ?? '#'));
        $active = $this->isActive($link, $currentPath);
        $href   = ($link === '' || $link === '#') ? '#' : base_url($link);
        $icon   = $menu['menu_icon'] !== '' ? $menu['menu_icon'] : 'fa-regular fa-circle';

        $html = '<li>';
        $html .= '<a href="' . esc($href) . '" class="menu-item group ' . ($active ? 'menu-item-active' : 'menu-item-inactive') . '">';
        $html .= '<i class="' . esc($icon) . ' text-lg min-w-[24px] text-center '
            . ($active
                ? 'text-brand-500 dark:text-brand-400'
                : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300')
            . '"></i>';
        $html .= '<span class="menu-item-text" :class="sidebarToggle ? \'lg:hidden\' : \'\'">'
            . esc($menu['menu_name_idn']) . '</span>';
        $html .= '</a></li>';

        return $html;
    }

    protected function renderParent(array $parent, array $children, string $currentPath): string
    {
        $key         = 'menu_' . $parent['menu_id'];
        $jsKey       = "'" . $key . "'"; // literal JS string (single-quote) agar aman di dalam atribut HTML
        $groupActive = $this->isAnyChildActive($children, $currentPath);
        $icon        = $parent['menu_icon'] !== '' ? $parent['menu_icon'] : 'fa-regular fa-circle';

        $html = '<li>';
        $html .= '<a href="#" @click.prevent="selected = (selected === ' . $jsKey . ' ? \'\' : ' . $jsKey . ')" '
            . 'class="menu-item group ' . ($groupActive ? 'menu-item-active' : 'menu-item-inactive') . '" '
            . ':class="selected === ' . $jsKey . ' ? \'menu-item-active\' : \'\'">';
        $html .= '<i class="' . esc($icon) . ' text-lg min-w-[24px] text-center '
            . ($groupActive
                ? 'text-brand-500 dark:text-brand-400'
                : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300')
            . '" :class="selected === ' . $jsKey . ' ? \'text-brand-500 dark:text-brand-400\' : \'\'"></i>';
        $html .= '<span class="menu-item-text" :class="sidebarToggle ? \'lg:hidden\' : \'\'">'
            . esc($parent['menu_name_idn']) . '</span>';
        $html .= '<i class="fa-solid fa-chevron-down menu-item-arrow text-xs transition-transform duration-200" '
            . ':class="[(selected === ' . $jsKey . ') ? \'menu-item-arrow-active rotate-180\' : \'menu-item-arrow-inactive\', sidebarToggle ? \'lg:hidden\' : \'\' ]"></i>';
        $html .= '</a>';

        $html .= '<div class="overflow-hidden transition-all duration-300" '
            . ':class="(selected === ' . $jsKey . ') ? \'block\' : \'hidden\'">';
        $html .= '<ul :class="sidebarToggle ? \'lg:hidden\' : \'flex\'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">';

        foreach ($children as $child) {
            $cLink   = trim((string) ($child['menu_link'] ?? '#'));
            $cActive = $this->isActive($cLink, $currentPath);
            $cHref   = ($cLink === '' || $cLink === '#') ? '#' : base_url($cLink);

            $html .= '<li>';
            $html .= '<a href="' . esc($cHref) . '" class="menu-dropdown-item group '
                . ($cActive ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive') . '">'
                . esc($child['menu_name_idn']) . '</a>';
            $html .= '</li>';
        }

        $html .= '</ul></div></li>';

        return $html;
    }

    /* ------------------------------------------------------------------
     * Matching helpers
     * ------------------------------------------------------------------ */

    /**
     * Cek apakah link menu cocok dengan path URL saat ini (prefix match).
     */
    protected function isActive(string $link, string $path): bool
    {
        $link = trim($link, '/');
        if ($link === '' || $link === '#') {
            return false;
        }

        if ($link === 'dashboard') {
            return $path === '' || $path === 'dashboard' || str_starts_with($path, 'dashboard/');
        }

        return $path === $link || str_starts_with($path, $link . '/');
    }

    /**
     * Cek apakah salah satu child aktif.
     */
    protected function isAnyChildActive(array $children, string $path): bool
    {
        foreach ($children as $child) {
            if ($this->isActive((string) ($child['menu_link'] ?? '#'), $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Temukan key grup yang harus terbuka (mengandung menu aktif).
     */
    protected function findActiveGroup(array $groups, array $childrenByParent, string $path): string
    {
        foreach ($groups as $parents) {
            foreach ($parents as $menuId => $parent) {
                $children = $childrenByParent[$menuId] ?? [];
                if (! empty($children) && $this->isAnyChildActive($children, $path)) {
                    return 'menu_' . $menuId;
                }
            }
        }

        return '';
    }
}
