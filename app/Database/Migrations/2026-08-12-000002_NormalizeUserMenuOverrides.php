<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Compacts legacy full Y/N usermenu snapshots into sparse role deltas.
 *
 * The selected menu set is calculated before rows are removed, so the first
 * run preserves the user's effective access while making future role changes
 * inherit automatically for all non-overridden menus.
 */
class NormalizeUserMenuOverrides extends Migration
{
    public function up(): void
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('gw_sm__usermenu')) {
            return;
        }

        $userRows = $db->table('gw_sm__usermenu')
            ->select('usermenu_user_id')
            ->distinct()
            ->get()
            ->getResultArray();

        $db->transStart();
        foreach ($userRows as $userRow) {
            $userId = (int) ($userRow['usermenu_user_id'] ?? 0);
            if ($userId <= 0) {
                continue;
            }

            $role = $db->table('gw_sm__profile p')
                ->select('p.profile_role_id')
                ->join('gw_sm__role r', 'r.role_id = p.profile_role_id', 'inner')
                ->where('p.profile_user_id', $userId)
                ->where('r.role_active', 'Y')
                ->where('r.role_type', 'menu')
                ->orderBy('p.profile_id', 'ASC')
                ->get(1)
                ->getRowArray();
            $roleId = (int) ($role['profile_role_id'] ?? 0);

            if ($roleId === 1) {
                $baselineRows = $db->table('gw_sm__menu')
                    ->select('menu_id')
                    ->where('menu_active', 'Y')
                    ->get()->getResultArray();
            } elseif ($roleId > 0) {
                $baselineRows = $db->table('gw_sm__rolemenu rm')
                    ->select('rm.rolemenu_menu_id AS menu_id')
                    ->join('gw_sm__menu m', 'm.menu_id = rm.rolemenu_menu_id', 'inner')
                    ->where('rm.rolemenu_role_id', $roleId)
                    ->where('rm.rolemenu_active', 'Y')
                    ->where('m.menu_active', 'Y')
                    ->get()->getResultArray();
            } else {
                $baselineRows = [];
            }

            $baseline = array_values(array_unique(array_map(
                static fn(array $row): int => (int) $row['menu_id'],
                $baselineRows
            )));
            $selectedRows = $db->table('gw_sm__usermenu')
                ->select('usermenu_menu_id')
                ->where('usermenu_user_id', $userId)
                ->where('usermenu_active', 'Y')
                ->get()->getResultArray();
            $selected = array_values(array_unique(array_map(
                static fn(array $row): int => (int) $row['usermenu_menu_id'],
                $selectedRows
            )));

            $activeRows = $db->table('gw_sm__menu')
                ->select('menu_id')->where('menu_active', 'Y')->get()->getResultArray();
            $activeIds = array_values(array_unique(array_map(
                static fn(array $row): int => (int) $row['menu_id'],
                $activeRows
            )));
            $selectedSet = array_fill_keys($selected, true);
            $deltaIds = array_values(array_intersect(
                array_unique(array_merge(array_diff($baseline, $selected), array_diff($selected, $baseline))),
                $activeIds
            ));

            $db->table('gw_sm__usermenu')->where('usermenu_user_id', $userId)->delete();
            foreach ($deltaIds as $menuId) {
                $db->table('gw_sm__usermenu')->insert([
                    'usermenu_user_id'    => $userId,
                    'usermenu_menu_id'    => $menuId,
                    'usermenu_active'     => isset($selectedSet[$menuId]) ? 'Y' : 'N',
                    'usermenu_created_on' => date('Y-m-d H:i:s'),
                    'usermenu_created_by' => 'migration',
                ]);
            }
        }
        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException('Gagal menormalisasi snapshot gw_sm__usermenu.');
        }
    }

    public function down(): void
    {
        // The compact representation is lossless; there is no safe reverse
        // operation without recreating the role baseline at migration time.
    }
}
