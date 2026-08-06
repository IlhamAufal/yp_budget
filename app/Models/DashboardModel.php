<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

/**
 * DashboardModel — Data agregasi nyata untuk halaman Dashboard (PRD Phase 4.1).
 *
 * Semua query memakai skema legacy nyata:
 *   - yp_plan__trans_budget_entry_data  (budget OPEX/Selling/FOH + push CAPEX/MPP; kolom bulan 1..12)
 *   - yp_plan__trans_capex_entry_header (proposal CAPEX: unit × unit_price)
 *   - yp_plan__trans_mpp_header         (plan headcount: grand_total)
 *   - yp_plan__assump_sales             (target sales domestic + export)
 */
class DashboardModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::connect();
    }

    /** Nama bulan (untuk label chart). */
    private const MONTH_LABELS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    /** Label modul untuk komposisi (COALESCE(source, 'LEGACY')). */
    private const SOURCE_LABELS = [
        'OPEX'    => 'OPEX GA',
        'SELLING' => 'OPEX Selling',
        'FOH'     => 'FOH',
        'CAPEX'   => 'CAPEX (Depr.)',
        'MPP'     => 'MPP (Personnel)',
        'LEGACY'  => 'Legacy Data',
    ];

    /**
     * Ringkasan kartu dashboard untuk satu tahun.
     * Return: ['opex','capex','sales','mpp','budget_entries','capex_assets','mpp_rows']
     */
    public function getStats(int $year): array
    {
        return [
            'opex'         => $this->getOpexGaSellingTotal($year),
            'capex'        => $this->getCapexTotal($year),
            'sales'        => $this->getSalesTarget($year),
            'mpp'          => $this->getMppHeadcount($year),
            'budget_entries' => $this->countBudgetEntries($year),
            'capex_assets' => $this->countCapexAssets($year),
        ];
    }

    /** Total budget OPEX GA + OPEX Selling (+ data legacy tanpa source). */
    public function getOpexGaSellingTotal(int $year): float
    {
        $sql = "SELECT IFNULL(SUM(t.`1`+t.`2`+t.`3`+t.`4`+t.`5`+t.`6`+t.`7`+t.`8`+t.`9`+t.`10`+t.`11`+t.`12`),0) AS tot
                FROM yp_plan__trans_budget_entry_data t
                WHERE t.year_code = ? AND (t.source IS NULL OR t.source IN ('OPEX','SELLING'))";

        return (float) $this->scalar($sql, [$year]);
    }

    /** Nilai proposal CAPEX: SUM(unit × unit_price). */
    public function getCapexTotal(int $year): float
    {
        $sql = "SELECT IFNULL(SUM(c.unit * c.unit_price),0) AS tot
                FROM yp_plan__trans_capex_entry_header c
                WHERE c.year_code = ?";

        return (float) $this->scalar($sql, [$year]);
    }

    /** Jumlah item aset CAPEX yang diajukan. */
    public function countCapexAssets(int $year): int
    {
        $sql = "SELECT COUNT(*) AS tot FROM yp_plan__trans_capex_entry_header WHERE year_code = ?";

        return (int) $this->scalar($sql, [$year]);
    }

    /** Target sales: total nilai (Domestic + International) dari assump_sales. */
    public function getSalesTarget(int $year): float
    {
        $sql = "SELECT IFNULL(SUM(s.value_text),0) AS tot
                FROM yp_plan__assump_sales s
                WHERE s.year_code = ? AND s.type_sales IN ('Domestic','International')";

        return (float) $this->scalar($sql, [$year]);
    }

    /** Plan headcount MPP: SUM(grand_total) seluruh posisi. */
    public function getMppHeadcount(int $year): int
    {
        $sql = "SELECT IFNULL(SUM(h.grand_total),0) AS tot FROM yp_plan__trans_mpp_header h WHERE h.year_code = ?";

        return (int) $this->scalar($sql, [$year]);
    }

    /** Jumlah baris entry budget (untuk sub-label kartu). */
    public function countBudgetEntries(int $year): int
    {
        $sql = "SELECT COUNT(*) AS tot FROM yp_plan__trans_budget_entry_data WHERE year_code = ?";

        return (int) $this->scalar($sql, [$year]);
    }

    /**
     * Tren budget bulanan (12 titik) untuk chart bar.
     * Return: [ ['label' => 'Jan', 'value' => float], ... ]
     */
    public function getMonthlySeries(int $year): array
    {
        $selects = [];
        foreach (range(1, 12) as $m) {
            $selects[] = "IFNULL(SUM(t.`{$m}`),0) AS m{$m}";
        }
        $sql = "SELECT " . implode(', ', $selects) . "
                FROM yp_plan__trans_budget_entry_data t
                WHERE t.year_code = ?";

        try {
            $row = $this->db->query($sql, [$year])->getRowArray() ?? [];
        } catch (\Throwable $e) {
            log_message('error', 'DashboardModel::getMonthlySeries: ' . $e->getMessage());

            return [];
        }

        $series = [];
        foreach (range(1, 12) as $m) {
            $series[] = [
                'label' => self::MONTH_LABELS[$m - 1],
                'value' => (float) ($row['m' . $m] ?? 0),
            ];
        }

        return $series;
    }

    /**
     * Komposisi budget per modul (donut chart).
     * Return: [ ['label' => ..., 'value' => float], ... ] urut nilai turun.
     */
    public function getComposition(int $year): array
    {
        $sql = "SELECT COALESCE(t.source,'LEGACY') AS src,
                       IFNULL(SUM(t.`1`+t.`2`+t.`3`+t.`4`+t.`5`+t.`6`+t.`7`+t.`8`+t.`9`+t.`10`+t.`11`+t.`12`),0) AS tot
                FROM yp_plan__trans_budget_entry_data t
                WHERE t.year_code = ?
                GROUP BY COALESCE(t.source,'LEGACY')";

        try {
            $rows = $this->db->query($sql, [$year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'DashboardModel::getComposition: ' . $e->getMessage());

            return [];
        }

        $items = [];
        foreach ($rows as $row) {
            $src   = (string) ($row['src'] ?? 'LEGACY');
            $value = (float) ($row['tot'] ?? 0);
            if ($value == 0) {
                continue;
            }
            $items[] = [
                'label' => self::SOURCE_LABELS[$src] ?? strip_tags($src),
                'value' => $value,
            ];
        }

        usort($items, fn ($a, $b) => $b['value'] <=> $a['value']);

        return $items;
    }

    /**
     * Status progress input per cost center (top N terbesar).
     * Return: [ ['cost_center','cost_desc','opex','capex','mpp','entries','submitted','status'], ... ]
     */
    public function getStatusRows(int $year, int $limit = 8): array
    {
        // 1) Budget (OPEX/Selling/FOH + push) per cost center
        $sql = "SELECT t.id_dept AS dept,
                       COALESCE(cc.cost_desc, '') AS cost_desc,
                       IFNULL(SUM(t.`1`+t.`2`+t.`3`+t.`4`+t.`5`+t.`6`+t.`7`+t.`8`+t.`9`+t.`10`+t.`11`+t.`12`),0) AS opex,
                       COUNT(*) AS entries,
                       SUM(CASE WHEN t.submit_status IN ('SUBMITTED','APPROVED') THEN 1 ELSE 0 END) AS submitted
                FROM yp_plan__trans_budget_entry_data t
                LEFT JOIN gw_plan__master_cost_center cc ON cc.cost_center = t.id_dept
                WHERE t.year_code = ?
                GROUP BY t.id_dept, cc.cost_desc";

        try {
            $budget = $this->db->query($sql, [$year])->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'DashboardModel::getStatusRows(budget): ' . $e->getMessage());
            $budget = [];
        }

        // 2) CAPEX per cost center
        try {
            $capexRows = $this->db->query(
                "SELECT dept_id AS dept, IFNULL(SUM(unit * unit_price),0) AS capex
                   FROM yp_plan__trans_capex_entry_header WHERE year_code = ? GROUP BY dept_id",
                [$year]
            )->getResultArray();
        } catch (\Throwable $e) {
            $capexRows = [];
        }
        $capexMap = [];
        foreach ($capexRows as $r) {
            $capexMap[(string) $r['dept']] = (float) $r['capex'];
        }

        // 3) MPP per cost center
        try {
            $mppRows = $this->db->query(
                "SELECT id_dept AS dept, IFNULL(SUM(grand_total),0) AS mpp
                   FROM yp_plan__trans_mpp_header WHERE year_code = ? GROUP BY id_dept",
                [$year]
            )->getResultArray();
        } catch (\Throwable $e) {
            $mppRows = [];
        }
        $mppMap = [];
        foreach ($mppRows as $r) {
            $mppMap[(string) $r['dept']] = (int) $r['mpp'];
        }

        // Gabungkan per cost center
        $merged = [];
        foreach ($budget as $row) {
            $dept = (string) ($row['dept'] ?? '');
            if ($dept === '') {
                continue;
            }
            $merged[$dept] = [
                'cost_center' => $dept,
                'cost_desc'   => (string) ($row['cost_desc'] ?? ''),
                'opex'        => (float) ($row['opex'] ?? 0),
                'capex'       => (float) ($capexMap[$dept] ?? 0),
                'mpp'         => (int) ($mppMap[$dept] ?? 0),
                'entries'     => (int) ($row['entries'] ?? 0),
                'submitted'   => (int) ($row['submitted'] ?? 0),
            ];
        }

        // Cost center yang hanya punya CAPEX/MPP (tanpa budget)
        foreach (array_merge($capexMap, $mppMap) as $dept => $_v) {
            if (isset($merged[$dept])) {
                continue;
            }
            $desc = $this->costDesc($dept);
            $merged[$dept] = [
                'cost_center' => $dept,
                'cost_desc'   => $desc,
                'opex'        => 0.0,
                'capex'       => (float) ($capexMap[$dept] ?? 0),
                'mpp'         => (int) ($mppMap[$dept] ?? 0),
                'entries'     => 0,
                'submitted'   => 0,
            ];
        }

        // Status + urutkan
        $rows = [];
        foreach ($merged as $r) {
            if ($r['entries'] > 0 && $r['submitted'] >= $r['entries']) {
                $r['status'] = 'Submitted';
            } elseif ($r['entries'] > 0) {
                $r['status'] = 'In Progress';
            } elseif ($r['capex'] > 0 || $r['mpp'] > 0) {
                $r['status'] = 'In Progress';
            } else {
                $r['status'] = 'No Entry';
            }
            $rows[] = $r;
        }

        usort($rows, fn ($a, $b) => (($b['opex'] + $b['capex']) <=> ($a['opex'] + $a['capex'])));

        return array_slice($rows, 0, $limit);
    }

    /** Apakah tahun ini punya data budget sama sekali? */
    public function hasBudgetData(int $year): bool
    {
        return $this->countBudgetEntries($year) > 0;
    }

    /* ------------------------------------------------------------------
     * Helper
     * ------------------------------------------------------------------ */

    private function costDesc(string $dept): string
    {
        try {
            $row = $this->db->table('gw_plan__master_cost_center')
                ->select('cost_desc')
                ->where('cost_center', $dept)
                ->get()
                ->getRowArray();

            return (string) ($row['cost_desc'] ?? '');
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function scalar(string $sql, array $params)
    {
        try {
            $row = $this->db->query($sql, $params)->getRowArray();

            return $row['tot'] ?? 0;
        } catch (\Throwable $e) {
            log_message('error', 'DashboardModel::scalar: ' . $e->getMessage());

            return 0;
        }
    }
}
