<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard | YP Budget',
        ];

        // Phase 1.3: peringatan untuk admin bila ada user aktif tanpa role.
        // Mode fail-open membuat user tanpa role masih bisa mengakses semua modul —
        // admin perlu segera assign role agar hak akses terkunci.
        $session = session();
        if ($session->get('is_admin')) {
            $db = \Config\Database::connect();

            $count = (int) $db->query(
                "SELECT COUNT(*) AS total
                   FROM gw_sm__user u
                  WHERE u.user_admin = 'N'
                    AND u.user_active = 'Y'
                    AND NOT EXISTS (
                        SELECT 1 FROM gw_sm__user_role ur
                         WHERE ur.user_role_user_id = u.user_id
                    )"
            )->getRow()->total;

            $data['rolelessUsers'] = $count;
        }

        return view('dashboard/index', $data);
    }
}
