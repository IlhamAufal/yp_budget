<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * OpexGaModel — OPEX GA (PRD Phase 2.1).
 *
 * Entry budget OPEX GA disimpan ke yp_plan__trans_budget_entry_data dengan
 * source = 'OPEX' sehingga mengalir ke Monitoring & P/L. Menyediakan save
 * budget nyata, workflow submit, actual, dan breakdown sub-detail COA
 * (BudgetBreakdownTrait).
 */
class OpexGaModel extends Model
{
    use \App\Models\Traits\BudgetBreakdownTrait;
    use \App\Models\Traits\ActualImportTrait;

    /** Sumber data di yp_plan__trans_budget_entry_data. */
    public const SOURCE = 'OPEX';

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /* ------------------------------------------------------------------
     * Master
     * ------------------------------------------------------------------ */

    /**
     * Cost center tipe OPEX (dropdown entry & filter).
     */
    public function getCostCenters(): array
    {
        return $this->db->table('gw_plan__master_cost_center')
            ->select("cost_center, COALESCE(NULLIF(cost_center_sap,''), CAST(cost_center AS CHAR)) AS cc_code, cost_desc, cost_center_sap")
            ->where('status', 'A')
            ->where('type', 'OPEX')
            ->orderBy('cost_center', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * COA relevan untuk OPEX GA (type GA / kategori admin & gaji).
     */
    public function getCoas(): array
    {
        return $this->db->table('gw_plan__master_coa')
            ->select("main_account, COALESCE(NULLIF(id_acct_ext,''), CAST(main_account AS CHAR)) AS acct_code, cost_center_desc")
            ->groupStart()
                ->where('type', 'GA')
                ->orWhereIn('category', ['ADMINEXP', 'SALARYEXP', 'OTHERS'])
            ->groupEnd()
            ->where('status', 'A')
            ->orderBy('main_account', 'ASC')
            ->get()
            ->getResultArray();
    }

    /* ------------------------------------------------------------------
     * Entry Budget
     * ------------------------------------------------------------------ */

    /**
     * Data budget OPEX GA tersimpan per tahun (+ opsional cost center).
     */
    public function getEntryData(string $year, ?string $dept = null, string $source = self::SOURCE): array
    {
        $builder = $this->db->table('yp_plan__trans_budget_entry_data t')
            ->select('t.id, t.id_coa, t.id_dept')
            ->select("COALESCE(NULLIF(c.id_acct_ext,''), c.main_account, 0) AS acct_code")
            ->select("COALESCE(c.cost_center_desc, '') AS coa_desc")
            ->select('IFNULL(t.total,0) AS total, COALESCE(t.submit_status, \'DRAFT\') AS submit_status')
            ->select("IFNULL(t.`1`,0) AS jan, IFNULL(t.`2`,0) AS feb, IFNULL(t.`3`,0) AS mar, IFNULL(t.`4`,0) AS apr, IFNULL(t.`5`,0) AS may, IFNULL(t.`6`,0) AS jun")
            ->select("IFNULL(t.`7`,0) AS jul, IFNULL(t.`8`,0) AS aug, IFNULL(t.`9`,0) AS sep, IFNULL(t.`10`,0) AS oct, IFNULL(t.`11`,0) AS nov, IFNULL(t.`12`,0) AS `dec`")
            ->join('gw_plan__master_coa c', 'c.main_account = t.id_coa', 'left')
            ->where('t.year_code', $year);

        // Tampilkan baris modern (source sesuai) + baris legacy (source NULL)
        $builder->groupStart()
            ->where('t.source', $source)
            ->orWhere('t.source IS NULL')
        ->groupEnd();

        if (! empty($dept)) {
            $builder->where('t.id_dept', $dept);
        }

        return $builder->orderBy('t.id_coa', 'ASC')->get()->getResultArray();
    }

    /**
     * Simpan / timpa seluruh baris budget OPEX GA untuk sebuah cost center.
     *
     * @return array ['success' => bool, 'message' => string, 'count' => int]
     */
    public function saveBudget(string $year, ?string $dept, array $rows, int $userId, string $source = self::SOURCE): array
    {
        if (empty($dept)) {
            return ['success' => false, 'message' => 'Cost Center wajib dipilih.', 'count' => 0];
        }

        $this->db->transStart();

        $delete = $this->db->table('yp_plan__trans_budget_entry_data')
            ->where('year_code', $year)
            ->where('id_dept', $dept)
            ->groupStart()
                ->where('source', $source)
                ->orWhere('source IS NULL')
            ->groupEnd();
        $delete->delete();

        // Kolom bulan bernomor 1..12 harus di-escape backtick (numeric identifier)
        $sql = 'INSERT INTO yp_plan__trans_budget_entry_data
                (id_coa, id_dept, total, year_code, created_by, created_date, source, submit_status,
                 `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $saved = 0;
        foreach ($rows as $r) {
            $coa = (int) ($r['id_coa'] ?? 0);
            if ($coa <= 0) {
                continue;
            }

            $total = 0;
            $vals  = [];
            for ($m = 1; $m <= 12; $m++) {
                $val = (float) ($r['m' . $m] ?? 0);
                $vals[] = $val;
                $total += $val;
            }

            $this->db->query($sql, array_merge([
                $coa,
                (int) $dept,
                $total,
                $year,
                $userId,
                date('Y-m-d H:i:s'),
                $source,
                'DRAFT',
            ], $vals));
            $saved++;
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menyimpan data OPEX GA.', 'count' => 0];
        }

        return ['success' => true, 'message' => "Data OPEX GA berhasil disimpan ({$saved} baris).", 'count' => $saved];
    }

    /**
     * Workflow Submit — tandai budget OPEX GA (dept & tahun) sebagai SUBMITTED.
     */
    public function submitBudget(string $year, ?string $dept, int $userId, string $source = self::SOURCE): array
    {
        $builder = $this->db->table('yp_plan__trans_budget_entry_data')
            ->where('year_code', $year)
            ->groupStart()
                ->where('source', $source)
                ->orWhere('source IS NULL')
            ->groupEnd();

        if (! empty($dept)) {
            $builder->where('id_dept', $dept);
        }

        $result = $builder->update([
            'submit_status' => 'SUBMITTED',
            'submit_by'     => $userId,
            'submit_date'   => date('Y-m-d H:i:s'),
        ]);

        if ($result === false) {
            return ['success' => false, 'message' => 'Gagal melakukan submit OPEX GA.'];
        }

        return ['success' => true, 'message' => 'Budget OPEX GA berhasil di-submit untuk persetujuan.'];
    }

    /* ------------------------------------------------------------------
     * Actual
     * ------------------------------------------------------------------ */

    /**
     * Data actual OPEX GA per tahun (+ opsional cost center).
     */
    public function getActualData(string $year, ?string $dept = null): array
    {
        $builder = $this->db->table('yp_plan__trans_budget_actual a')
            ->select('a.id, a.id_coa, a.id_dept')
            ->select("COALESCE(c.cost_center_desc, '') AS coa_desc")
            ->select('(IFNULL(a.`1`,0)+IFNULL(a.`2`,0)+IFNULL(a.`3`,0)+IFNULL(a.`4`,0)+IFNULL(a.`5`,0)+IFNULL(a.`6`,0)+IFNULL(a.`7`,0)+IFNULL(a.`8`,0)+IFNULL(a.`9`,0)+IFNULL(a.`10`,0)+IFNULL(a.`11`,0)+IFNULL(a.`12`,0)) AS total, a.assumption, a.notes')
            ->select("IFNULL(a.`1`,0) AS jan, IFNULL(a.`2`,0) AS feb, IFNULL(a.`3`,0) AS mar, IFNULL(a.`4`,0) AS apr, IFNULL(a.`5`,0) AS may, IFNULL(a.`6`,0) AS jun")
            ->select("IFNULL(a.`7`,0) AS jul, IFNULL(a.`8`,0) AS aug, IFNULL(a.`9`,0) AS sep, IFNULL(a.`10`,0) AS oct, IFNULL(a.`11`,0) AS nov, IFNULL(a.`12`,0) AS `dec`")
            ->join('gw_plan__master_coa c', 'c.main_account = a.id_coa', 'left')
            ->where('a.year_code', $year);

        if (! empty($dept)) {
            $builder->where('a.id_dept', $dept);
        }

        return $builder->orderBy('a.id_coa', 'ASC')->get()->getResultArray();
    }

    /**
     * Import actual dari Excel — lihat ActualImportTrait (Phase 2.3).
     */
}
