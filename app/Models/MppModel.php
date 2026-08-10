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

    /**
     * Daftar Department aktif untuk dropdown MPP (sebagai Cost Center)
     */
    public function getCostCentersActive(): array
    {
        return $this->db->table('gw_plan__master_department')
            ->select('id_dept, dept_code, dept_desc')
            ->where('status', 'A')
            ->orderBy('dept_code', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Ambil informasi periode submit MPP aktif dari master_period
     */
    public function getSubmitPeriod(string $yearCode): ?array
    {
        try {
            return $this->db->table('yp_plan__master_period')
                ->where('tipe', 'MPP')
                ->where('status', 'A')
                ->where('YEAR(begda)', $yearCode)
                ->orderBy('begda', 'DESC')
                ->limit(1)
                ->get()->getRowArray();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Matriks headcount per Kategori Tipe MPP untuk 12 bulan
     * Mengembalikan SEMUA tipe_mpp (termasuk yang belum ada data)
     */
    public function getMppMatrix(string $yearCode, string $idDept): array
    {
        $sql = "SELECT
                    t.id_mpp  AS tipe_id,
                    COALESCE(t.desc_mpp, 'Staff') AS tipe_name,
                    IFNULL(SUM(h.`1`),0) AS m1,  IFNULL(SUM(h.`2`),0) AS m2,
                    IFNULL(SUM(h.`3`),0) AS m3,  IFNULL(SUM(h.`4`),0) AS m4,
                    IFNULL(SUM(h.`5`),0) AS m5,  IFNULL(SUM(h.`6`),0) AS m6,
                    IFNULL(SUM(h.`7`),0) AS m7,  IFNULL(SUM(h.`8`),0) AS m8,
                    IFNULL(SUM(h.`9`),0) AS m9,  IFNULL(SUM(h.`10`),0) AS m10,
                    IFNULL(SUM(h.`11`),0) AS m11, IFNULL(SUM(h.`12`),0) AS m12,
                    IFNULL(SUM(h.grand_total), 0) AS grand_total
                FROM yp_plan__master_tipe_mpp t
                LEFT JOIN yp_plan__trans_mpp_header h
                    ON h.id_tipe = t.id_mpp AND h.year_code = ? AND h.id_dept = ?
                GROUP BY t.id_mpp, t.desc_mpp
                ORDER BY t.id_mpp";

        return $this->db->query($sql, [$yearCode, $idDept])->getResultArray();
    }

    /**
     * Detail breakdown 12 bulan per kategori (tipe_mpp) termasuk note
     */
    public function getMppCategoryDetail(string $yearCode, string $idDept, int $tipeId): array
    {
        $header = $this->db->table('yp_plan__trans_mpp_header')
            ->where('year_code', $yearCode)
            ->where('id_dept', $idDept)
            ->where('id_tipe', $tipeId)
            ->get()->getRowArray();

        $months = array_fill(0, 12, 0);
        $grandTotal = 0;

        if ($header) {
            $detail = $this->db->table('yp_plan__trans_mpp_detail')
                ->where('id_header', $header['id'])
                ->get()->getRowArray();

            if ($detail) {
                for ($m = 1; $m <= 12; $m++) {
                    $months[$m - 1] = (float) ($detail[(string) $m] ?? 0);
                }
                $grandTotal = (float) ($detail['grand_total'] ?? 0);
            }
        }

        $note = '';
        $noteRow = $this->db->table('yp_plan__trans_mpp_notes')
            ->where('id_tipe', $tipeId)
            ->where('id_dept', $idDept)
            ->where('year_code', $yearCode)
            ->get()->getRowArray();
        if ($noteRow) {
            $note = $noteRow['notes'] ?? '';
        }

        return [
            'months'      => $months,
            'grand_total' => $grandTotal,
            'note'        => $note,
        ];
    }

    /**
     * Simpan data MPP per kategori (tipe_mpp) dengan transaksi
     */
    public function saveMppCategory(string $yearCode, string $idDept, int $tipeId, array $months, string $note = ''): bool
    {
        $this->db->transStart();
        $userId = (int) (session()->get('user_id') ?? 0);

        // Cari atau buat header
        $header = $this->db->table('yp_plan__trans_mpp_header')
            ->where('year_code', $yearCode)
            ->where('id_dept', $idDept)
            ->where('id_tipe', $tipeId)
            ->get()->getRowArray();

        if (! $header) {
            $tipe = $this->db->table('yp_plan__master_tipe_mpp')
                ->where('id_mpp', $tipeId)
                ->get()->getRowArray();

            $this->db->table('yp_plan__trans_mpp_header')->insert([
                'staff_name'   => $tipe ? $tipe['desc_mpp'] : 'Staff',
                'id_tipe'      => $tipeId,
                'id_dept'      => $idDept,
                'year_code'    => $yearCode,
                'created_by'   => $userId,
                'created_date' => date('Y-m-d H:i:s'),
            ]);
            $headerId = $this->db->insertID();
        } else {
            $headerId = (int) $header['id'];
        }

        // Hapus detail lama
        $this->db->table('yp_plan__trans_mpp_detail')->where('id_header', $headerId)->delete();

        // Hitung grand total & siapkan values
        $grand = 0;
        $vals  = [];
        for ($m = 0; $m < 12; $m++) {
            $val = (float) ($months[$m] ?? 0);
            $vals[] = $val;
            $grand += $val;
        }

        // Insert detail baru
        $sql = 'INSERT INTO yp_plan__trans_mpp_detail
                (id_header, year_code, created_by, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, grand_total)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $this->db->query($sql, array_merge([$headerId, $yearCode, $userId], $vals, [$grand]));

        // Simpan / update note
        $this->db->table('yp_plan__trans_mpp_notes')
            ->where('id_tipe', $tipeId)
            ->where('id_dept', $idDept)
            ->where('year_code', $yearCode)
            ->delete();

        if ($note !== '') {
            $this->db->table('yp_plan__trans_mpp_notes')->insert([
                'id_tipe'      => $tipeId,
                'id_dept'      => $idDept,
                'year_code'    => $yearCode,
                'notes'        => $note,
                'created_by'   => $userId,
                'created_date' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * Daftar posisi per tipe MPP beserta data entry yang sudah tersimpan.
     * Sumber: yp_plan__master_group_mpp (posisi) + yp_plan__trans_mpp_header (entry).
     */
    public function getPositionsByTipe(string $yearCode, string $idDept, int $tipeId): array
    {
        $sql = "SELECT
                    a.id_mppx,
                    a.desc_mppx AS position_name,
                    IFNULL(c.`1`, 0) AS m1, IFNULL(c.`2`, 0) AS m2,
                    IFNULL(c.`3`, 0) AS m3, IFNULL(c.`4`, 0) AS m4,
                    IFNULL(c.`5`, 0) AS m5, IFNULL(c.`6`, 0) AS m6,
                    IFNULL(c.`7`, 0) AS m7, IFNULL(c.`8`, 0) AS m8,
                    IFNULL(c.`9`, 0) AS m9, IFNULL(c.`10`, 0) AS m10,
                    IFNULL(c.`11`, 0) AS m11, IFNULL(c.`12`, 0) AS m12,
                    IFNULL(c.grand_total, 0) AS grand_total
                FROM yp_plan__master_group_mpp a
                LEFT JOIN yp_plan__master_tipe_mpp b ON a.tipe_mppx = b.id_mpp
                LEFT JOIN yp_plan__trans_mpp_header c
                    ON c.staff_name = a.desc_mppx
                    AND c.id_tipe = a.tipe_mppx
                    AND c.year_code = ?
                    AND c.id_dept = ?
                WHERE a.tipe_mppx = ?
                GROUP BY a.id_mppx
                ORDER BY a.id_mppx ASC";

        $positions = $this->db->query($sql, [$yearCode, $idDept, $tipeId])->getResultArray();

        // Get note
        $note = '';
        $noteRow = $this->db->table('yp_plan__trans_mpp_notes')
            ->where('id_tipe', $tipeId)
            ->where('id_dept', $idDept)
            ->where('year_code', $yearCode)
            ->get()->getRowArray();
        if ($noteRow) {
            $note = $noteRow['notes'] ?? '';
        }

        return [
            'positions' => $positions,
            'note'      => $note,
        ];
    }

    /**
     * Simpan data MPP multi-posisi per tipe.
     * @param array $rows [{ staff_name, months: [12 values] }]
     */
    public function saveMppPositions(string $yearCode, string $idDept, int $tipeId, array $rows, string $note = ''): bool
    {
        $this->db->transStart();
        $userId = (int) (session()->get('user_id') ?? 0);

        // Hapus semua header+detail lama untuk tipe+dept+year ini
        $existingHeaders = $this->db->table('yp_plan__trans_mpp_header')
            ->select('id')
            ->where('year_code', $yearCode)
            ->where('id_dept', $idDept)
            ->where('id_tipe', $tipeId)
            ->get()->getResultArray();

        $headerIds = array_map(fn($h) => (int) $h['id'], $existingHeaders);
        if (!empty($headerIds)) {
            $this->db->table('yp_plan__trans_mpp_detail')->whereIn('id_header', $headerIds)->delete();
            $this->db->table('yp_plan__trans_mpp_header')
                ->where('year_code', $yearCode)
                ->where('id_dept', $idDept)
                ->where('id_tipe', $tipeId)
                ->delete();
        }

        // Insert per posisi
        foreach ($rows as $row) {
            $staffName = trim($row['staff_name'] ?? '');
            if ($staffName === '') continue;

            $months = $row['months'] ?? [];
            $grand = 0;
            $vals = [];
            for ($m = 0; $m < 12; $m++) {
                $val = (float) ($months[$m] ?? 0);
                $vals[] = $val;
                $grand += $val;
            }

            // Skip row jika semua 0
            if ($grand == 0 && array_sum($vals) == 0) continue;

            $sql = "INSERT INTO yp_plan__trans_mpp_header
                    (staff_name, id_tipe, id_dept, year_code,
                     `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`,
                     grand_total, created_by, created_date)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $this->db->query($sql, array_merge(
                [$staffName, $tipeId, $idDept, $yearCode],
                $vals,
                [$grand, $userId, date('Y-m-d H:i:s')]
            ));
        }

        // Simpan note
        $this->db->table('yp_plan__trans_mpp_notes')
            ->where('id_tipe', $tipeId)
            ->where('id_dept', $idDept)
            ->where('year_code', $yearCode)
            ->delete();

        if ($note !== '') {
            $this->db->table('yp_plan__trans_mpp_notes')->insert([
                'id_tipe'      => $tipeId,
                'id_dept'      => $idDept,
                'year_code'    => $yearCode,
                'notes'        => $note,
                'created_by'   => $userId,
                'created_date' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Data ringkasan untuk tab View MPP Data (read-only per kategori)
     */
    public function getViewSummary(string $yearCode, string $idDept): array
    {
        $sql = "SELECT
                    t.id_mpp  AS tipe_id,
                    COALESCE(t.desc_mpp, '') AS tipe_name,
                    IFNULL(d.`1`,0) AS m1,  IFNULL(d.`2`,0) AS m2,
                    IFNULL(d.`3`,0) AS m3,  IFNULL(d.`4`,0) AS m4,
                    IFNULL(d.`5`,0) AS m5,  IFNULL(d.`6`,0) AS m6,
                    IFNULL(d.`7`,0) AS m7,  IFNULL(d.`8`,0) AS m8,
                    IFNULL(d.`9`,0) AS m9,  IFNULL(d.`10`,0) AS m10,
                    IFNULL(d.`11`,0) AS m11, IFNULL(d.`12`,0) AS m12,
                    IFNULL(d.grand_total, 0) AS grand_total
                FROM yp_plan__trans_mpp_header h
                JOIN yp_plan__trans_mpp_detail d ON d.id_header = h.id
                LEFT JOIN yp_plan__master_tipe_mpp t ON t.id_mpp = h.id_tipe
                WHERE h.year_code = ? AND h.id_dept = ?
                ORDER BY h.id_tipe";

        return $this->db->query($sql, [$yearCode, $idDept])->getResultArray();
    }

    public function getSummaryWithSalary(string $yearCode, ?string $idDept = null, ?int $offset = null, ?int $perPage = null): array
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

        if ($perPage !== null) {
            $sql .= ' LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset;
        }

        return $this->db->query($sql, $params)->getResultArray();
    }

    public function countSummaryWithSalary(string $yearCode, ?string $idDept = null): int
    {
        $sql = "SELECT COUNT(*) AS total FROM (
                    SELECT h.id_dept,
                           COALESCE(dp.dept_desc, '') AS department_name,
                           COALESCE(tm.desc_mpp, '') AS employee_type,
                           COALESCE(s.`desc`, '') AS coa_code,
                           COALESCE(s.salary, 0) AS monthly_salary
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
                ) AS sub';

        $row = $this->db->query($sql, $params)->getRowArray();
        return (int) (reset($row) ?? 0);
    }

    public function syncToOpex(string $yearCode): bool
    {
        // Kolom source tidak ada di skema legacy → sinkronisasi di-nonaktifkan
        // sementara (keputusan user 6 Agt 2026: jangan ubah struktur DB).
        if (! \App\Libraries\DbCompat::hasEntrySource()) {
            log_message('warning', 'MppModel::syncToOpex dinonaktifkan — kolom source belum tersedia di skema DB legacy.');

            return false;
        }

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

            $opexBatch[] = [
                'id_coa'      => $defaultCoa,
                'id_dept'     => (int) $row['id_dept'],
                'total'       => $total,
                'year_code'   => $yearCode,
                'created_by'  => $userId,
                'created_date'=> date('Y-m-d H:i:s'),
                'months'      => $months,
            ];
        }

        if (! empty($opexBatch)) {
            $usedCoas = array_values(array_unique(array_column($opexBatch, 'id_coa')));
            $this->db->table('yp_plan__trans_budget_entry_data')
                ->where('year_code', $yearCode)
                ->where('source', 'MPP')
                ->whereIn('id_coa', $usedCoas)
                ->delete();

            // Kolom bulan bernomor 1..12 harus di-escape backtick (numeric identifier)
            $cols = '(`id_coa`, `id_dept`, `total`, `year_code`, `created_by`, `created_date`, `source`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`)';
            $vals = 'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = 'INSERT INTO yp_plan__trans_budget_entry_data ' . $cols . ' ' . $vals;
            foreach ($opexBatch as $row) {
                $this->db->query($stmt, array_merge([
                    (int) $row['id_coa'],
                    (int) $row['id_dept'],
                    (float) $row['total'],
                    $row['year_code'],
                    (int) $row['created_by'],
                    $row['created_date'],
                    'MPP',
                ], array_map('floatval', $row['months'] ?? [])));
            }
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
