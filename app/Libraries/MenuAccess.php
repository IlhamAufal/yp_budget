<?php

namespace App\Libraries;

/** Compatibility facade over the single database-backed authorization context. */
class MenuAccess
{
    public function getAllowedMenuIds(): ?array
    {
        if (! session()->get('user_logged_in')) {
            return [];
        }
        return (new AuthorizationService())->refresh()['effective_module_menu_ids'];
    }

    /** @return string[]|null */
    public function getAllowedModuleSegments(): ?array
    {
        $menuIds = $this->getAllowedMenuIds();
        if ($menuIds === null) {
            return null;
        }
        if ($menuIds === []) {
            return [];
        }

        $db = \Config\Database::connect();
        $rows = $db->table('gw_sm__menu')
            ->select('menu_link')
            ->whereIn('menu_id', $menuIds)
            ->where('menu_active', 'Y')
            ->get()->getResultArray();
        $segments = [];
        foreach ($rows as $row) {
            $link = (string) ($row['menu_link'] ?? '');
            if ($link !== '' && $link !== '#') {
                $segments[] = explode('/', trim($link, '/'))[0];
            }
        }
        return array_values(array_unique($segments));
    }
}
