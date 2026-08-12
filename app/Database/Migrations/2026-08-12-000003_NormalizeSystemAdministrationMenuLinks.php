<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Normalize legacy System Administration submenu links to their CI4 routes.
 */
class NormalizeSystemAdministrationMenuLinks extends Migration
{
    public function up(): void
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('gw_sm__menu')) {
            return;
        }

        $links = [
            ['names' => ['Konfigurasi Menu', 'Menu Configuration'], 'link' => 'sys-admin/menu'],
            ['names' => ['Role Manajemen', 'Role Management'], 'link' => 'sys-admin/role'],
            ['names' => ['Manajemen User', 'User Management'], 'link' => 'sys-admin/user'],
        ];

        foreach ($links as $item) {
            $db->table('gw_sm__menu')
                ->whereIn('menu_name_idn', $item['names'])
                ->set('menu_link', $item['link'])
                ->update();
        }
    }

    public function down(): void
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('gw_sm__menu')) {
            return;
        }

        $legacyLinks = [
            ['names' => ['Konfigurasi Menu', 'Menu Configuration'], 'link' => 'menu'],
            ['names' => ['Role Manajemen', 'Role Management'], 'link' => 'role'],
            ['names' => ['Manajemen User', 'User Management'], 'link' => 'user'],
        ];

        foreach ($legacyLinks as $item) {
            $db->table('gw_sm__menu')
                ->whereIn('menu_name_idn', $item['names'])
                ->set('menu_link', $item['link'])
                ->update();
        }
    }
}
