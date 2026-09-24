<?php

namespace App\Libraries;

/** Renders the effective legacy menu tree for the active module. */
class MenuBuilder
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function getAllowedMenuIds(): ?array
    {
        return (new AuthorizationService())->getAllowedMenuIds();
    }

    public function render(): string
    {
        $service = new AuthorizationService();
        $context = $service->refresh();
        $allowed = $context['is_admin'] ? null : array_values(array_map(
            'intval',
            (array) ($context['effective_module_menu_ids'] ?? [])
        ));

        if ($allowed !== null && $allowed === []) {
            return $this->emptyState();
        }

        $menus = $this->db->table('gw_sm__menu')
            ->where('menu_active', 'Y')
            ->orderBy('menu_order', 'ASC')
            ->orderBy('menu_id', 'ASC')
            ->get()->getResultArray();
        $allById = [];
        foreach ($menus as $menu) {
            $id = (int) ($menu['menu_id'] ?? 0);
            if ($id > 0 && $this->belongsToActiveModule($menu, $context['active_module_code'])) {
                $allById[$id] = $menu;
            }
        }

        $structureRows = $this->db->table('gw_sm__menu_structure')
            ->get()->getResultArray();
        $childrenByParent = [];
        $parentByChild = [];
        foreach ($structureRows as $row) {
            $parent = (int) ($row['structure_menu_id'] ?? 0);
            $child = (int) ($row['structure_child_menu_id'] ?? 0);
            if ($parent <= 0 || $child <= 0 || $parent === $child) {
                continue;
            }
            if (! isset($allById[$parent], $allById[$child])) {
                continue;
            }
            // Keep the first parent deterministically when malformed legacy
            // data links one child to multiple parents.
            if (isset($parentByChild[$child])) {
                continue;
            }
            $childrenByParent[$parent][$child] = true;
            $parentByChild[$child] = $parent;
        }

        $visibleIds = [];
        foreach ($allById as $id => $menu) {
            $link = (string) ($menu['menu_link'] ?? '#');
            $isGranted = $allowed === null || in_array($id, $allowed, true);
            if ($isGranted && $link !== '' && $link !== '#') {
                $visibleIds[$id] = true;
            }
        }

        // Ancestors are navigation-only additions; they do not become grants.
        $changed = true;
        while ($changed) {
            $changed = false;
            foreach ($parentByChild as $child => $parent) {
                if (isset($visibleIds[$child]) && ! isset($visibleIds[$parent])) {
                    $visibleIds[$parent] = true;
                    $changed = true;
                }
            }
        }

        if ($visibleIds === []) {
            return $this->emptyState();
        }

        $path = trim((string) uri_string(), '/');
        $roots = [];
        foreach (array_keys($visibleIds) as $id) {
            $parent = $parentByChild[$id] ?? null;
            $cycleRoot = $this->cycleRoot((int) $id, $parentByChild);
            if ($parent === null || ! isset($visibleIds[$parent]) || $cycleRoot === (int) $id) {
                $roots[] = (int) $id;
            }
        }
        $roots = array_values(array_unique($roots));
        usort($roots, fn(int $a, int $b): int => $this->sortMenus($allById[$a], $allById[$b]));

        $grouped = [];
        foreach ($roots as $id) {
            $menu = $allById[$id];
            $group = trim((string) ($menu['menu_group'] ?? '')) ?: 'MENU UTAMA';
            $grouped[$group][] = $id;
        }

        $html = '<nav>';
        foreach ($grouped as $groupName => $groupRoots) {
            $html .= '<div class="mb-6"><h3 class="mb-4 text-xs font-semibold leading-[20px] text-gray-400"><span class="menu-group-title">' . esc($groupName) . '</span></h3><ul class="flex flex-col gap-1.5">';
            foreach ($groupRoots as $id) {
                $html .= $this->renderNode(
                    $id,
                    $allById,
                    $childrenByParent,
                    $visibleIds,
                    $path,
                    []
                );
            }
            $html .= '</ul></div>';
        }

        return $html . '</nav>';
    }

    private function renderNode(int $id, array $allById, array $childrenByParent, array $visibleIds, string $path, array $ancestors): string
    {
        if (in_array($id, $ancestors, true) || ! isset($allById[$id], $visibleIds[$id])) {
            return '';
        }
        $menu = $allById[$id];
        $children = [];
        foreach (array_keys($childrenByParent[$id] ?? []) as $childId) {
            if (isset($visibleIds[$childId])) {
                $children[] = (int) $childId;
            }
        }
        usort($children, fn(int $a, int $b): int => $this->sortMenus($allById[$a], $allById[$b]));

        if ($children === []) {
            return $this->renderLeaf($menu, $path);
        }

        $key = 'menu_' . $id;
        $active = $this->containsActive($id, $childrenByParent, $visibleIds, $allById, $path, [$id]);
        $html = '<li class="submenu-item' . ($active ? ' submenu-open' : '') . '">'
            . '<a href="#" data-submenu-target="' . esc($key) . '" class="menu-item group ' . ($active ? 'menu-item-active' : 'menu-item-inactive') . '">'
            . '<span class="menu-item-text">' . esc($menu['menu_name_idn'] ?? '') . '</span>'
            . '<span class="menu-item-arrow"></span>'
            . '</a>'
            . '<div class="submenu-container overflow-hidden"><ul class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">';
        foreach ($children as $childId) {
            $html .= $this->renderNode($childId, $allById, $childrenByParent, $visibleIds, $path, array_merge($ancestors, [$id]));
        }
        return $html . '</ul></div></li>';
    }

    private function renderLeaf(array $menu, string $path): string
    {
        $link = (string) ($menu['menu_link'] ?? '#');
        $active = $this->isActive($link, $path);
        $href = ($link === '' || $link === '#') ? '#' : base_url(ltrim($link, '/'));
        return '<li><a href="' . esc($href) . '" class="menu-item group ' . ($active ? 'menu-item-active' : 'menu-item-inactive') . '"><span class="menu-item-text">' . esc($menu['menu_name_idn'] ?? '') . '</span></a></li>';
    }

    private function containsActive(int $id, array $childrenByParent, array $visibleIds, array $allById, string $path, array $visited): bool
    {
        foreach (array_keys($childrenByParent[$id] ?? []) as $childId) {
            $childId = (int) $childId;
            if (! isset($visibleIds[$childId]) || in_array($childId, $visited, true)) {
                continue;
            }
            if ($this->isActive((string) ($allById[$childId]['menu_link'] ?? '#'), $path)) {
                return true;
            }
            if ($this->containsActive($childId, $childrenByParent, $visibleIds, $allById, $path, array_merge($visited, [$childId]))) {
                return true;
            }
        }
        return false;
    }

    private function findActiveRoot(array $roots, array $childrenByParent, array $visibleIds, array $allById, string $path, array $visited): string
    {
        foreach ($roots as $id) {
            if ($this->containsActive($id, $childrenByParent, $visibleIds, $allById, $path, [$id])) {
                return 'menu_' . $id;
            }
        }
        return '';
    }

    private function cycleRoot(int $id, array $parentByChild): ?int
    {
        $seen = [];
        $current = $id;
        while (isset($parentByChild[$current])) {
            if (isset($seen[$current])) {
                return min(array_map('intval', array_keys($seen)));
            }
            $seen[$current] = true;
            $current = (int) $parentByChild[$current];
        }
        return null;
    }

    private function belongsToActiveModule(array $menu, ?string $activeModule): bool
    {
        $module = strtoupper(trim((string) ($menu['menu_module_code'] ?? '')));
        return $activeModule === null || $module === '' || $module === strtoupper($activeModule);
    }

    private function isActive(string $link, string $path): bool
    {
        $link = trim($link, '/');
        $path = trim($path, '/');
        return $link !== '' && $link !== '#' && ($path === $link || str_starts_with($path, $link . '/'));
    }

    private function sortMenus(array $a, array $b): int
    {
        return (int) ($a['menu_order'] ?? 0) <=> (int) ($b['menu_order'] ?? 0)
            ?: (int) ($a['menu_id'] ?? 0) <=> (int) ($b['menu_id'] ?? 0);
    }

    private function emptyState(): string
    {
        return '<nav class="px-5 py-6 text-xs text-gray-400 dark:text-gray-500">Tidak ada menu yang dapat diakses. Hubungi Administrator.</nav>';
    }
}
