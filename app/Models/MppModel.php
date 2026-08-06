<?php

namespace App\Models;

use CodeIgniter\Model;

class MppModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getEntryData(string $yearCode, string $idDept, bool $isNewlines = false): array
    {
        $sql = "SELECT h.id AS header_id,
                       COALESCE(tm.desc_mpp, h.staff_name, 'Staff') AS employee_type,
                       IFNULL(d.`1`,0) AS m1,  IFNULL(d.`2`,0) AS m2,
                       IFNULL(d.`3`,0) AS m3,  IFNULL(d.`4`,0) AS m4,
                       IFNULL(d.`5`,0) AS m5,  IFNULL(d.`6`,0) AS m6,
                       IFNULL(d.`7`,0) AS m7,  IFNULL(d.`8`,0) AS m8,
                       IFNULL(d.`9`,0) AS m9,  IFNULL(d.`10`,0) AS m10,
                       IFNULL(d.`11`,0) AS m11, IFNULL(d.`12`,0) AS m12
                FROM yp_plan__trans_mpp_header h
                LEFT JOIN yp_plan__trans_mpp_detail d ON d.id_header = h.id
                LEFT JOIN yp_plan__master_tipe_mpp tm ON tm.id_mpp = h.id_tipe
                WHERE h.year_code = ? AND h.id_dept = ?
                ORDER BY h.id";

        return $this->db->query($sql, [$yearCode, $idDept])->getResultArray();
    }

    public function saveMppBudget(string $yearCode, string $idDept, array $details, bool $isNewlines = false): bool
    {
        $this->db->transStart();

        $userId = (int) (session()->get('user_id') ?? 0);

        foreach ($details as $row) {
            $employeeType = trim((string) ($row['employee_type'] ?? 'Staff'));
            if ($employeeType === '') {
                continue;
            }

            $tipe = $this->db->table('yp_plan__master_tipe_mpp')
                ->select('id_mpp')
                ->where('desc_mpp', $employeeType)
                ->get()->getRowArray();
            $idTipe = $tipe ? (int) $tipe['id_mpp'] : 1;

            $header = $this->db->table('yp_plan__trans_mpp_header')
                ->where('year_code', $yearCode)
                ->where('id_dept', $idDept)
                ->where('id_tipe', $idTipe)
                ->get()->getRowArray();

            if (! $header) {
                $this->db->table('yp_plan__trans_mpp_header')->insert([
                    'staff_name'  => $employeeType,
                    'id_tipe'     => $idTipe,
                    'id_dept'     => $idDept,
                    'year_code'   => $yearCode,
                    'created_by'  => $userId,
                    'created_date'=> date('Y-m-d H:i:s'),
                ]);
                $headerId = $this->db->insertID();
            } else {
                $headerId = (int) $header['id'];
            }

            $this->db->table('yp_plan__trans_mpp_detail')->where('id_header', $headerId)->delete();

            $grand = 0;
            $vals  = [];
            for ($m = 1; $m <= 12; $m++) {
                $val = (float) ($row['m' . $m] ?? 0);
                $vals[] = $val;
                $grand += $val;
            }

            // Kolom bulan bernomor 1..12 di-escape backtick (numeric identifier)
            $sql = 'INSERT INTO yp_plan__trans_mpp_detail
                    (id_header, year_code, created_by, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, grand_total)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

            $this->db->query($sql, array_merge([$headerId, $yearCode, $userId], $vals, [$grand]));
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    public function getSummaryWithSalary(string $yearCode, ?string $idDept = null): array
    {
        $sql = "SELECT h.id_dept,
                       COALESCE(dp.dept_desc, '') AS department_name,
                       COALESCE(tm.desc_mpp, '') AS employee_type,
                       COALESCE(s.`desc`, '') AS coa_code,
                       COALESCE(s.salary, 0) AS monthly_salary,
                       IFNULL(SUM(d.`1`),0)  AS jan, IFNULL(SUM(d.`2`),0)  AS feb,
                       IFNULL(SUM(d.`3`),0)  AS mar, IFNULL(SUM(d.`4`),0)  AS apr,
                       IFNULL(SUM(d.`5`),0)  AS may, IFNULL(SUM(d.`6`),0)  AS jun,
                       IFNULL(SUM(d.`7`),0)  AS jul, IFNULL(SUM(d.`8`),0)  AS aug,
                       IFNULL(SUM(d.`9`),0)  AS sep, IFNULL(SUM(d.`10`),0) AS oct,
                       IFNULL(SUM(d.`11`),0) AS nov, IFNULL(SUM(d.`12`),0) AS `dec`
                FROM yp_plan__trans_mpp_header h
                JOIN yp_plan__trans_mpp_detail d ON d.id_header = h.id
                LEFT JOIN gw_plan__master_department dp ON dp.id_dept = h.id_dept
                LEFT JOIN yp_plan__master_tipe_mpp tm ON tm.id_mpp = h.id_tipe
                LEFT JOIN yp_plan__master_mpp_salary s
                       ON s.dept_id = h.id_dept AND s.type = h.id_tipe
                      AND s.year_code = h.year_code AND s.status = 'A'
                WHERE h.year_code = ?";

        $params = [$yearCode];

        if (! empty($idDept)) {
            $sql .= ' AND h.id_dept = ?';
            $params[] = $idDept;
        }

        $sql .= ' GROUP BY h.id_dept, dp.dept_desc, tm.desc_mpp, s.`desc`, s.salary
                  ORDER BY h.id_dept, tm.desc_mpp';

        return $this->db->query($sql, $params)->getResultArray();
    }

    public function syncToOpex(string $yearCode): bool
    {
        $this->db->transStart();

        $userId = (int) (session()->get('user_id') ?? 0);
        $summary = $this->getSummaryWithSalary($yearCode);

        $coaMap = [];
        $coas = $this->db->table('gw_plan__master_coa')
            ->select('main_account, type')
            ->where('category', 'SALARYEXP')
            ->where('status', 'A')
            ->get()->getResultArray();
        foreach ($coas as $c) {
            $coaMap[$c['type']] = (int) $c['main_account'];
        }
        $defaultCoa = $coaMap['OPEX'] ?? $coaMap['GA'] ?? 0;

        $opexBatch = [];
        $keys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
        foreach ($summary as $row) {
            if ($defaultCoa <= 0) {
                continue;
            }

            $salary = (float) ($row['monthly_salary'] ?? 0);
            $total  = 0;
            $months = [];
            for ($m = 1; $m <= 12; $m++) {
                $val = (float) ($row[$keys[$m - 1]] ?? 0) * $salary;
                $months[(string) $m] = $val;
                $total += $val;
            }

            $opexBatch[] = array_merge([
                'id_coa'      => $defaultCoa,
                'id_dept'     => (int) $row['id_dept'],
                'total'       => $total,
                'year_code'   => $yearCode,
                'created_by'  => $userId,
                'created_date'=> date('Y-m-d H:i:s'),
            ], $months);
        }

        if (! empty($opexBatch)) {
            $usedCoas = array_values(array_unique(array_column($opexBatch, 'id_coa')));
            $this->db->table('yp_plan__trans_budget_entry_data')
                ->where('year_code', $yearCode)
                ->whereIn('id_coa', $usedCoas)
                ->delete();

            $this->db->table('yp_plan__trans_budget_entry_data')->insertBatch($opexBatch);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
