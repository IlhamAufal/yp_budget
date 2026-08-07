<?php

/**
 * menu_helper.php — Helper sidebar dinamis (PRD Phase 1.2).
 *
 * Wrapper ringkas di atas Library MenuBuilder. Dipakai di view:
 *   <?= render_menu_sidebar() ?>
 *
 * Helper ini di-autoload via app/Config/Autoload.php ($helpers).
 */

use App\Libraries\MenuBuilder;

if (! function_exists('render_menu_sidebar')) {
    /**
     * Render sidebar dinamis dari DB (gw_sm__menu) sesuai role user.
     */
    function render_menu_sidebar(): string
    {
        $builder = new MenuBuilder();

        return $builder->render();
    }
}

if (! function_exists('menu_allowed_ids')) {
    /**
     * Daftar menu_id yang diizinkan user saat ini (null = semua).
     */
    function menu_allowed_ids(): ?array
    {
        $builder = new MenuBuilder();

        return $builder->getAllowedMenuIds();
    }
}
