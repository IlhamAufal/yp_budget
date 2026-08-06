<?php

namespace App\Models;

use CodeIgniter\Model;

class MppModel extends Model
{
    protected $table            = 'yp_plan__trans_mpp_header';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'year_code', 'id_dept', 'is_newlines', 'created_by', 'created_at', 'updated_at'
    ];

    /**
     * Mengambil data entry header & detail MPP berdasarkan Tahun & Department
     */
    public function getEntryData(string $yearCode, string $idDept, bool $isNewlines = false): array
    {
        $headerTable = $isNewlines ? 'yp_plan__trans_mpp_header_newlines' : 'yp_plan__trans_mpp_header';
        $detailTable = $isNewlines ? 'yp_plan__trans_mpp_detail_newlines' : 'yp_plan__trans_mpp_detail';

        $builder = $this->db->table($headerTable . ' h')
            ->select('h.id as header_id, h.year_code, h.id_dept, d.*')
            ->join($detailTable . ' d', 'd.header_id = h.id', 'left')
            ->where('h.year_code', $yearCode)
            ->where('h.id_dept', $idDept);

        return $builder->get()->getResultArray();
    }

    /**
     * Menyimpan/Update Data Entry MPP (Multi-Bulan & Tipe Karyawan)
     */
    public function saveMppBudget(string $yearCode, string $idDept, array $details, bool $isNewlines = false): bool
    {
        $headerTable = $isNewlines ? 'yp_plan__trans_mpp_header_newlines' : 'yp_plan__trans_mpp_header';
        $detailTable = $isNewlines ? 'yp_plan__trans_mpp_detail_newlines' : 'yp_plan__trans_mpp_detail';

        $this->db->transStart();

        // Check or create Header
        $header = $this->db->table($headerTable)
            ->where('year_code', $yearCode)
            ->where('id_dept', $idDept)
            ->get()->getRowArray();

        if (!$header) {
            $this->db->table($headerTable)->insert([
                'year_code'  => $yearCode,
                'id_dept'    => $idDept,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $headerId = $this->db->insertID();
        } else {
            $headerId = $header['id'];
            // Clear existing details for fresh insert/update
            $this->db->table($detailTable)->where('header_id', $headerId)->delete();
        }

        // Insert new details
        $insertData = [];
        foreach ($details as $row) {
            $insertData[] = array_merge(['header_id' => $headerId], $row);
        }

        if (!empty($insertData)) {
            $this->db->table($detailTable)->insertBatch($insertData);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * Menghitung Summary Headcount & Rekalkulasi Nominal Salary per COA
     */
    public function getSummaryWithSalary(string $yearCode, ?string $idDept = null): array
    {
        $builder = $this->db->table('yp_plan__trans_mpp_detail d')
            ->select('h.id_dept, dept.department_name, d.employee_type, s.coa_code, s.monthly_salary')
            ->select('SUM(d.m1) as jan, SUM(d.m2) as feb, SUM(d.m3) as mar, SUM(d.m4) as apr, SUM(d.m5) as may, SUM(d.m6) as jun')
            ->select('SUM(d.m7) as jul, SUM(d.m8) as aug, SUM(d.m9) as sep, SUM(d.m10) as oct, SUM(d.m11) as nov, SUM(d.m12) as dec')
            ->join('yp_plan__trans_mpp_header h', 'h.id = d.header_id')
            ->join('gw_plan__master_department dept', 'dept.department_code = h.id_dept', 'left')
            ->join('yp_plan__master_mpp_salary s', 's.employee_type = d.employee_type AND s.year_code = h.year_code', 'left')
            ->where('h.year_code', $yearCode);

        if (!empty($idDept)) {
            $builder->where('h.id_dept', $idDept);
        }

        $builder->groupBy(['h.id_dept', 'd.employee_type', 's.coa_code']);

        return $builder->get()->getResultArray();
    }

    /**
     * Sync/Push hasil kalkulasi MPP ke Tabel Transaksi OPEX (yp_plan__trans_budget_entry_data)
     */
    public function syncToOpex(string $yearCode): bool
    {
        $this->db->transStart();

        // Clean existing synced MPP entries in OPEX for the given year
        $this->db->table('yp_plan__trans_budget_entry_data')
            ->where('year_code', $yearCode)
            ->where_in('coa_code', ['7710011', '6605011', '6604010']) // COA Gaji Standard
            ->delete();

        // Calculate and push compiled nominal salary to OPEX
        $summary = $this->getSummaryWithSalary($yearCode);
        $opexBatch = [];

        foreach ($summary as $row) {
            if (empty($row['coa_code'])) continue;

            $salary = (float)($row['monthly_salary'] ?? 0);
            $opexBatch[] = [
                'year_code' => $yearCode,
                'id_dept'   => $row['id_dept'],
                'coa_code'  => $row['coa_code'],
                'm1'        => $row['jan'] * $salary,
                'm2'        => $row['feb'] * $salary,
                'm3'        => $row['mar'] * $salary,
                'm4'        => $row['apr'] * $salary,
                'm5'        => $row['may'] * $salary,
                'm6'        => $row['jun'] * $salary,
                'm7'        => $row['jul'] * $salary,
                'm8'        => $row['aug'] * $salary,
                'm9'        => $row['sep'] * $salary,
                'm10'       => $row['oct'] * $salary,
                'm11'       => $row['nov'] * $salary,
                'm12'       => $row['dec'] * $salary,
                'created_at'=> date('Y-m-d H:i:s')
            ];
        }

        if (!empty($opexBatch)) {
            $this->db->table('yp_plan__trans_budget_entry_data')->insertBatch($opexBatch);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}