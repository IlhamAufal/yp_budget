<?php

namespace App\Controllers;

use App\Models\DashboardModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $workingYear = (int) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));

        $dashboard = new DashboardModel();

        $data = [
            'title'       => 'Dashboard | YP Budget',
            'workingYear' => $workingYear,
            'stats'       => $dashboard->getStats($workingYear),
            'monthly'     => $dashboard->getMonthlySeries($workingYear),
            'composition' => $dashboard->getComposition($workingYear),
            'statusRows'  => $dashboard->getStatusRows($workingYear),
            'hasBudget'   => $dashboard->hasBudgetData($workingYear),
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
                        SELECT 1 FROM gw_sm__profile p
                         WHERE p.profile_user_id = u.user_id
                    )"
            )->getRow()->total;

            $data['rolelessUsers'] = $count;
        }

        return view('dashboard/index', $data);
    }
}
