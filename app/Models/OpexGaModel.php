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
     * Cost center Entry OPEX GA yang boleh diakses user saat ini.
     *
     * Filtering dilakukan di server dengan tiga sumber kebenaran: master
     * cost center, auth_obj session, dan periode aktif OPEX_GA. Method ini
     * sengaja juga dipakai oleh halaman lain agar dropdown tidak membocorkan
     * cost center yang tidak eligible.
     */
    public function getCostCenters(?string $year = null, ?array $authObj = null): array
    {
        $year    = $year ?: (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $authObj = $authObj ?? (array) session()->get('auth_obj');
        $allowed = $this->authorizedCostCenterValues($authObj);
        $today   = date('Y-m-d');

        if (empty($allowed)) {
            return [];
        }

        $rows = $this->db->table('gw_plan__master_cost_center')
            ->select('cost_center, COALESCE(NULLIF(cost_center_sap,\'\'), CAST(cost_center AS CHAR)) AS cc_code, cost_desc, cost_center_sap, year')
            ->where('status', 'A')
            ->where('type', 'OPEX')
            ->groupStart()
                ->where('year', (int) $year)
                ->orWhere('year IS NULL', null, false)
            ->groupEnd()
            ->orderBy('cost_center', 'ASC')
            ->get()
            ->getResultArray();

        return array_values(array_filter($rows, function (array $row) use ($allowed, $today): bool {
            $internal = trim((string) ($row['cost_center'] ?? ''));
            $sap      = trim((string) ($row['cost_center_sap'] ?? ''));

            if (! $this->isAuthorizedCostCenter($internal, $sap, $allowed)) {
                return false;
            }

            return $this->hasActiveGaPeriod($internal, $sap, $today);
        }));
    }

    /** Alias eksplisit untuk pemanggil yang membedakan dropdown Entry. */
    public function getCostCentersForEntry(?string $year = null, ?array $authObj = null): array
    {
        return $this->getCostCenters($year, $authObj);
    }

    /**
     * Resolve parameter dept (cost_center internal atau cost_center_sap)
     * ke cost_center internal. Nilai tidak dikenal dikembalikan apa adanya
     * agar resolver eligibility dapat menolaknya secara konsisten.
     */
    public function resolveDept(?string $dept): ?string
    {
        $dept = $dept === null ? null : trim($dept);
        if ($dept === null || $dept === '') {
            return $dept;
        }

        $row = $this->db->table('gw_plan__master_cost_center')
            ->select('cost_center')
            ->where('status', 'A')
            ->groupStart()
                ->where('cost_center', $dept)
                ->orWhere('cost_center_sap', $dept)
            ->groupEnd()
            ->limit(1)
            ->get()
            ->getRow();

        if ($row && $row->cost_center !== null && $row->cost_center !== '') {
            return (string) $row->cost_center;
        }

        return $dept;
    }

    /** Return internal dept only when role scope and active OPEX_GA period match. */
    public function resolveEligibleDept(?string $dept, ?string $year = null, ?array $authObj = null): ?string
    {
        $dept = trim((string) ($dept ?? ''));
        if ($dept === '') {
            return null;
        }

        $year    = $year ?: (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $authObj = $authObj ?? (array) session()->get('auth_obj');
        $allowed = $this->authorizedCostCenterValues($authObj);
        $resolved = $this->resolveDept($dept);

        $row = $this->db->table('gw_plan__master_cost_center')
            ->select('cost_center, cost_center_sap, year')
            ->where('status', 'A')
            ->where('type', 'OPEX')
            ->groupStart()
                ->where('cost_center', $resolved)
                ->orWhere('cost_center_sap', $dept)
            ->groupEnd()
            ->groupStart()
                ->where('year', (int) $year)
                ->orWhere('year IS NULL', null, false)
            ->groupEnd()
            ->limit(1)
            ->get()
            ->getRowArray();

        if (! $row) {
            return null;
        }

        $internal = trim((string) ($row['cost_center'] ?? ''));
        $sap      = trim((string) ($row['cost_center_sap'] ?? ''));
        if (empty($allowed) || ! $this->isAuthorizedCostCenter($internal, $sap, $allowed)) {
            return null;
        }

        return $this->hasActiveGaPeriod($internal, $sap) ? $internal : null;
    }

    public function isEligibleDept(?string $dept, ?string $year = null, ?array $authObj = null): bool
    {
        return $this->resolveEligibleDept($dept, $year, $authObj) !== null;
    }

    /** Parse legacy role_object_value such as '970','891' into scalar values. */
    private function authorizedCostCenterValues(array $authObj): array
    {
        $values = [];
        foreach ($authObj as $object) {
            $raw = is_array($object) ? ($object['role_object_value'] ?? '') : $object;
            if (! is_scalar($raw)) {
                continue;
            }
            $raw = trim((string) $raw);
            if ($raw === '') {
                continue;
            }

            if (preg_match_all("/'([^']+)'/", $raw, $matches)) {
                $parts = $matches[1];
            } else {
                $parts = preg_split('/\\s*,\\s*/', trim($raw, "'\\\" ")) ?: [];
            }
            foreach ($parts as $part) {
                $part = trim((string) $part, "'\\\" ");
                if ($part !== '') {
                    $values[] = $part;
                }
            }
        }

        return array_values(array_unique($values));
    }

    private function isAuthorizedCostCenter(string $internal, string $sap, array $allowed): bool
    {
        if (in_array('*', $allowed, true) || in_array('ALL', array_map('strtoupper', $allowed), true)) {
            return true;
        }

        return in_array($internal, $allowed, true) || ($sap !== '' && in_array($sap, $allowed, true));
    }

    private function hasActiveGaPeriod(string $internal, string $sap, ?string $today = null): bool
    {
        $today = $today ?: date('Y-m-d');

        return $this->db->table('yp_plan__master_period')
            ->where('tipe', 'OPEX_GA')
            ->where('status', 'A')
            ->where('begda <=', $today)
            ->where('endda >=', $today)
            ->groupStart()
                ->where('id_cost_center', '*')
                ->orWhere('id_cost_center', $internal)
                ->orWhere('id_cost_center', $sap)
            ->groupEnd()
            ->countAllResults() > 0;
    }

    /** COA akun yang benar-benar menjadi anggota pasangan header + idx. */
    private function getGroupCoas(string $header, string $headerId): array
    {
        $builder = $this->db->table('gw_plan__master_coa')
            ->select('main_account, id_acct_ext, cost_center_header, id_cost_header, cost_center_desc')
            ->where('status', 'A')
            ->groupStart()
                ->where('type', 'GA')
                ->orWhereIn('category', ['ADMINEXP', 'SALARYEXP', 'OTHERS'])
            ->groupEnd()
            ->where('cost_center_header', $header)
            ->where('id_cost_header', (int) $headerId)
            ->orderBy('main_account', 'ASC');

        return $builder->get()->getResultArray();
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
     * View Data OPEX GA. Semua kalkulasi dilakukan di server agar angka yang
     * dirender, subtotal, dan grand total memakai sumber data yang sama.
     */
    public function getEntryData(string $year, ?string $dept = null, string $source = self::SOURCE): array
    {
        if (empty($dept)) {
            return ['rows' => [], 'groups' => [], 'subtotals' => [], 'grand_total' => $this->emptyViewTotals()];
        }

        $coas = $this->db->table('gw_plan__master_coa')
            ->select('main_account, id_acct_ext, cost_center_header, id_cost_header, cost_center_desc')
            ->where('status', 'A')
            ->groupStart()
                ->where('type', 'GA')
                ->orWhereIn('category', ['ADMINEXP', 'SALARYEXP', 'OTHERS'])
            ->groupEnd()
            ->orderBy('cost_center_header', 'ASC')
            ->orderBy('id_cost_header', 'ASC')
            ->orderBy('main_account', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($coas)) {
            return ['rows' => [], 'groups' => [], 'subtotals' => [], 'grand_total' => $this->emptyViewTotals()];
        }

        $coaIds = array_values(array_unique(array_map(static fn (array $row): int => (int) $row['main_account'], $coas)));
        $actualByCoa = [];
        $actualQuery = $this->db->table('yp_plan__trans_budget_actual')
            ->select('id_coa, assumption, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12')
            ->where('id_dept', $dept)
            ->where('year_code', $year)
            ->whereIn('id_coa', $coaIds)
            ->get()
            ->getResultArray();

        foreach ($actualQuery as $actual) {
            $id = (int) $actual['id_coa'];
            if (! isset($actualByCoa[$id])) {
                $actualByCoa[$id] = array_fill_keys(array_map(static fn (int $m): string => (string) $m, range(1, 12)), 0.0);
                $actualByCoa[$id]['assumption'] = null;
            }
            for ($m = 1; $m <= 12; $m++) {
                $actualByCoa[$id][(string) $m] += (float) ($actual[(string) $m] ?? 0);
            }
            if (($actual['assumption'] ?? null) !== null && $actual['assumption'] !== '') {
                $actualByCoa[$id]['assumption'] = $actual['assumption'];
            }
        }

        $budgetBuilder = $this->db->table('yp_plan__trans_budget_entry_data t')
            ->select('t.id, t.id_coa, t.total, t.`1`, t.`2`, t.`3`, t.`4`, t.`5`, t.`6`, t.`7`, t.`8`, t.`9`, t.`10`, t.`11`, t.`12`')
            ->where('t.id_dept', $dept)
            ->where('t.year_code', $year)
            ->whereIn('t.id_coa', $coaIds)
            ->orderBy('t.id', 'DESC');
        if (\App\Libraries\DbCompat::hasEntrySource()) {
            $budgetBuilder->groupStart()
                ->where('t.source', $source)
                ->orWhere('t.source IS NULL')
            ->groupEnd();
        }
        $budgetByCoa = [];
        foreach ($budgetBuilder->get()->getResultArray() as $budget) {
            $id = (int) $budget['id_coa'];
            if (! isset($budgetByCoa[$id])) {
                $budgetByCoa[$id] = $budget;
            }
        }

        $rows = [];
        $groups = [];
        $grand = $this->emptyViewTotals();
        foreach ($coas as $coa) {
            $coaId = (int) $coa['main_account'];
            $header = trim((string) ($coa['cost_center_header'] ?? '')) ?: 'Lainnya';
            $headerId = (string) ($coa['id_cost_header'] ?? '0');
            $groupKey = $header . "\\0" . $headerId;
            $actualRaw = $actualByCoa[$coaId] ?? [];
            $budgetRaw = $budgetByCoa[$coaId] ?? [];
            $actual = $this->metricFromNumericRow($actualRaw, 1, 8, 'a');
            $budget = $this->metricFromNumericRow($budgetRaw, 1, 12, 'b');
            $row = [
                'id_coa'             => $coaId,
                'main_account'       => (string) $coaId,
                'acct_code'          => (string) ($coa['id_acct_ext'] ?: $coaId),
                'account'            => (string) ($coa['id_acct_ext'] ?: $coaId),
                'description'        => (string) ($coa['cost_center_desc'] ?? ''),
                'coa_desc'           => (string) ($coa['cost_center_desc'] ?? ''),
                'cost_center_header' => $header,
                'id_cost_header'     => $headerId,
                'entry_data_id'      => (int) ($budgetRaw['id'] ?? 0),
                'assumption'         => $actualRaw['assumption'] ?? null,
                'actual'             => $actual,
                'budget'             => $budget,
                'actual_avg'         => $actual['avg'],
                'actual_total'       => $actual['total'],
                'budget_total'       => $budget['total'],
            ];
            foreach ($actual['months'] as $month => $value) {
                $row['actual_' . $month] = $value;
            }
            foreach ($budget['months'] as $month => $value) {
                $row['budget_' . $month] = $value;
            }

            $rows[] = $row;
            if (! isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'cost_center_header' => $header,
                    'id_cost_header'     => $headerId,
                    'rows'               => [],
                    'subtotal'           => $this->emptyViewTotals(),
                ];
            }
            $groups[$groupKey]['rows'][] = $row;
            $this->addViewTotals($groups[$groupKey]['subtotal'], $actual, $budget);
            $this->addViewTotals($grand, $actual, $budget);
        }

        $groupList = array_values($groups);
        $subtotals = array_map(static fn (array $group): array => [
            'cost_center_header' => $group['cost_center_header'],
            'id_cost_header'     => $group['id_cost_header'],
            'actual'             => $group['subtotal']['actual'],
            'budget'             => $group['subtotal']['budget'],
            'actual_avg'         => $group['subtotal']['actual']['avg'],
            'actual_total'       => $group['subtotal']['actual']['total'],
            'budget_total'       => $group['subtotal']['budget']['total'],
        ], $groupList);

        return [
            'rows'       => $rows,
            'groups'     => $groupList,
            'subtotals'  => $subtotals,
            'grand_total'=> $grand,
        ];
    }

    private function emptyViewTotals(): array
    {
        return [
            'actual' => ['months' => array_fill_keys(['jan','feb','mar','apr','may','jun','jul','aug'], 0.0), 'avg' => 0.0, 'total' => 0.0],
            'budget' => ['months' => array_fill_keys(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'], 0.0), 'total' => 0.0],
        ];
    }

    private function metricFromNumericRow(array $row, int $from, int $to, string $prefix): array
    {
        $names = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
        $months = [];
        $total = 0.0;
        for ($m = $from; $m <= $to; $m++) {
            $value = (float) ($row[(string) $m] ?? 0);
            $months[$names[$m - 1]] = $value;
            $total += $value;
        }

        return ['months' => $months, 'avg' => $prefix === 'a' ? $total / 8 : 0.0, 'total' => $total];
    }

    private function addViewTotals(array &$target, array $actual, array $budget): void
    {
        foreach ($actual['months'] as $month => $value) {
            $target['actual']['months'][$month] += (float) $value;
        }
        foreach ($budget['months'] as $month => $value) {
            $target['budget']['months'][$month] += (float) $value;
        }
        $target['actual']['total'] += (float) $actual['total'];
        $target['actual']['avg'] = $target['actual']['total'] / 8;
        $target['budget']['total'] += (float) $budget['total'];
    }

    /**
     * Upsert budget satu kelompok header tanpa menghapus kelompok lain.
     * Seluruh payload divalidasi sebelum transaksi dimulai.
     */
    public function saveBudget(string $year, ?string $dept, string $header, string $headerId, array $rows, int $userId, string $source = self::SOURCE): array
    {
        $dept = trim((string) $dept);
        $header = trim($header);
        $headerId = trim($headerId);
        if ($dept === '' || $header === '' || $headerId === '' || ! preg_match('/^-?\\d+$/', $headerId)) {
            return ['success' => false, 'message' => 'Cost Center, header, dan idx wajib valid.', 'count' => 0];
        }
        if (empty($rows)) {
            return ['success' => false, 'message' => 'Payload budget tidak boleh kosong.', 'count' => 0];
        }

        $groupCoas = $this->getGroupCoas($header, $headerId);
        if (empty($groupCoas)) {
            return ['success' => false, 'message' => 'Kelompok header OPEX GA tidak ditemukan.', 'count' => 0];
        }
        $byAccount = [];
        foreach ($groupCoas as $coa) {
            $main = (string) $coa['main_account'];
            $ext = trim((string) ($coa['id_acct_ext'] ?? ''));
            $byAccount[$main] = $coa;
            if ($ext !== '') {
                $byAccount[$ext] = $coa;
            }
        }

        $validated = [];
        $seen = [];
        for ($index = 0; $index < count($rows); $index++) {
            $row = $rows[$index];
            if (! is_array($row)) {
                return ['success' => false, 'message' => "Baris budget ke-" . ($index + 1) . ' tidak valid.', 'count' => 0];
            }
            $account = '';
            foreach (['id_acct_ext', 'acct_code', 'account', 'id_coa', 'main_account'] as $field) {
                if (isset($row[$field]) && trim((string) $row[$field]) !== '') {
                    $account = trim((string) $row[$field]);
                    break;
                }
            }
            if ($account === '' || ! isset($byAccount[$account])) {
                return ['success' => false, 'message' => "Akun budget ke-" . ($index + 1) . ' tidak termasuk kelompok header.', 'count' => 0];
            }
            $coaId = (int) $byAccount[$account]['main_account'];
            if (isset($seen[$coaId])) {
                return ['success' => false, 'message' => "Akun {$account} dikirim lebih dari satu kali.", 'count' => 0];
            }
            $seen[$coaId] = true;

            $months = [];
            $total = 0.0;
            for ($m = 1; $m <= 12; $m++) {
                $monthValue = null;
                foreach (['m' . $m, (string) $m, ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'][$m - 1]] as $field) {
                    if (is_string($field) && array_key_exists($field, $row)) {
                        $monthValue = $row[$field];
                        break;
                    }
                }
                if ($monthValue === null || $monthValue === '') {
                    $monthValue = 0;
                }
                if (! is_numeric($monthValue) || ! is_finite((float) $monthValue)) {
                    return ['success' => false, 'message' => "Nilai bulan ke-{$m} pada akun {$account} tidak valid.", 'count' => 0];
                }
                $months[$m] = (float) $monthValue;
                $total += $months[$m];
            }
            $validated[] = ['id_coa' => $coaId, 'months' => $months, 'total' => $total];
        }

        $this->db->transStart();
        foreach ($validated as $entry) {
            $existingBuilder = $this->db->table('yp_plan__trans_budget_entry_data')
                ->where('id_coa', $entry['id_coa'])
                ->where('id_dept', $dept)
                ->where('year_code', $year)
                ->orderBy('id', 'DESC')
                ->limit(1);
            if (\App\Libraries\DbCompat::hasEntrySource()) {
                $existingBuilder->groupStart()
                    ->where('source', $source)
                    ->orWhere('source IS NULL')
                ->groupEnd();
            }
            $existing = $existingBuilder->get()->getRowArray();
            $data = ['total' => $entry['total'], 'created_by' => $userId, 'created_date' => date('Y-m-d H:i:s')];
            for ($m = 1; $m <= 12; $m++) {
                $data[(string) $m] = $entry['months'][$m];
            }
            if (\App\Libraries\DbCompat::hasEntrySource()) {
                $data['source'] = $source;
            }
            if (\App\Libraries\DbCompat::hasEntrySubmitStatus()) {
                $data['submit_status'] = 'DRAFT';
            }
            if ($existing) {
                $this->db->table('yp_plan__trans_budget_entry_data')->where('id', (int) $existing['id'])->update($data);
            } else {
                $this->db->table('yp_plan__trans_budget_entry_data')->insert(array_merge([
                    'id_coa' => $entry['id_coa'], 'id_dept' => (int) $dept, 'year_code' => (int) $year,
                ], $data));
            }
        }

        $headerTotal = $this->calculateGroupBudgetTotal($year, $dept, $groupCoas, $source);
        $this->upsertHeaderTotal($year, $dept, $headerId, $headerTotal, $userId);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menyimpan data OPEX GA.', 'count' => 0];
        }

        return ['success' => true, 'message' => 'Data budget OPEX GA berhasil disimpan.', 'count' => count($validated), 'header_total' => $headerTotal];
    }

    private function calculateGroupBudgetTotal(string $year, string $dept, array $groupCoas, string $source = self::SOURCE): array
    {
        $total = array_fill_keys(range(1, 12), 0.0);
        $ids = array_values(array_unique(array_map(static fn (array $coa): int => (int) $coa['main_account'], $groupCoas)));
        $builder = $this->db->table('yp_plan__trans_budget_entry_data t')
            ->select('t.id_coa, t.id, t.`1`, t.`2`, t.`3`, t.`4`, t.`5`, t.`6`, t.`7`, t.`8`, t.`9`, t.`10`, t.`11`, t.`12`')
            ->where('t.id_dept', $dept)->where('t.year_code', $year)->whereIn('t.id_coa', $ids)
            ->orderBy('t.id', 'DESC');
        if (\App\Libraries\DbCompat::hasEntrySource()) {
            $builder->groupStart()->where('t.source', $source)->orWhere('t.source IS NULL')->groupEnd();
        }
        $latest = [];
        foreach ($builder->get()->getResultArray() as $row) {
            $id = (int) ($row['id_coa'] ?? 0);
            if ($id > 0 && ! isset($latest[$id])) {
                $latest[$id] = $row;
            }
        }
        foreach ($latest as $row) {
            for ($m = 1; $m <= 12; $m++) {
                $total[$m] += (float) ($row[(string) $m] ?? 0);
            }
        }
        $total['grand_total'] = array_sum($total);

        return $total;
    }

    private function upsertHeaderTotal(string $year, string $dept, string $headerId, array $total, int $userId): void
    {
        $table = $this->db->table('yp_plan__trans_budget_total_entry_data');
        $existing = $table->where('id_cost_header', (int) $headerId)->where('id_dept', (int) $dept)->where('year_code', (int) $year)->limit(1)->get()->getRowArray();
        $data = ['id_cost_header' => (int) $headerId, 'id_dept' => (int) $dept, 'year_code' => (int) $year, 'created_by' => $userId, 'created_date' => date('Y-m-d H:i:s')];
        for ($m = 1; $m <= 12; $m++) {
            $data[(string) $m] = (float) ($total[$m] ?? 0);
        }
        $data['grand_total'] = (float) ($total['grand_total'] ?? 0);
        if ($existing) {
            $table->where('id', (int) $existing['id'])->update($data);
        } else {
            $table->insert($data);
        }
    }

    /**
     * Workflow Submit — tandai budget OPEX GA (dept & tahun) sebagai SUBMITTED.
     */
    public function submitBudget(string $year, ?string $dept, int $userId, string $source = self::SOURCE): array
    {
        if (! \App\Libraries\DbCompat::hasEntrySubmitStatus()) {
            return ['success' => false, 'message' => 'Workflow submit OPEX GA dinonaktifkan sementara (kolom submit_status belum tersedia di skema DB legacy).'];
        }

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

        if ($dept !== '') {
            $builder->where('a.id_dept', $dept);
        }

        return $builder->orderBy('a.id_coa', 'ASC')->get()->getResultArray();
    }

    /**
     * Data actual OPEX GA dengan server-side pagination (SQL LIMIT/OFFSET).
     * Hanya slice halaman yang diambil — bukan load-all.
     */
    public function getActualDataPaginated(string $year, ?string $dept = null, int $offset = 0, int $perPage = 25, string $search = ''): array
    {
        $builder = $this->db->table('yp_plan__trans_budget_actual a')
            ->select('a.id, a.id_coa, a.id_dept')
            ->select("COALESCE(c.cost_center_desc, '') AS coa_desc")
            ->select('(IFNULL(a.`1`,0)+IFNULL(a.`2`,0)+IFNULL(a.`3`,0)+IFNULL(a.`4`,0)+IFNULL(a.`5`,0)+IFNULL(a.`6`,0)+IFNULL(a.`7`,0)+IFNULL(a.`8`,0)+IFNULL(a.`9`,0)+IFNULL(a.`10`,0)+IFNULL(a.`11`,0)+IFNULL(a.`12`,0)) AS total')
            ->select("IFNULL(a.`1`,0) AS jan, IFNULL(a.`2`,0) AS feb, IFNULL(a.`3`,0) AS mar, IFNULL(a.`4`,0) AS apr, IFNULL(a.`5`,0) AS may, IFNULL(a.`6`,0) AS jun")
            ->select("IFNULL(a.`7`,0) AS jul, IFNULL(a.`8`,0) AS aug, IFNULL(a.`9`,0) AS sep, IFNULL(a.`10`,0) AS oct, IFNULL(a.`11`,0) AS nov, IFNULL(a.`12`,0) AS `dec`")
            ->join('gw_plan__master_coa c', 'c.main_account = a.id_coa', 'left')
            ->where('a.year_code', $year);

        if ($dept !== '') {
            $builder->where('a.id_dept', $dept);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('a.id_coa', $search)
                ->orLike('c.id_acct_ext', $search)
                ->orLike('c.cost_center_desc', $search)
            ->groupEnd();
        }

        return $builder->orderBy('a.id_coa', 'ASC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Jumlah total data actual sesuai filter (untuk server-side pagination).
     */
    public function countActualData(string $year, ?string $dept = null, string $search = ''): int
    {
        $builder = $this->db->table('yp_plan__trans_budget_actual a')
            ->join('gw_plan__master_coa c', 'c.main_account = a.id_coa', 'left')
            ->where('a.year_code', $year);

        if ($dept !== '') {
            $builder->where('a.id_dept', $dept);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('a.id_coa', $search)
                ->orLike('c.id_acct_ext', $search)
                ->orLike('c.cost_center_desc', $search)
            ->groupEnd();
        }

        return (int) $builder->countAllResults();
    }

    /* ------------------------------------------------------------------
     * Header Accounts (Entry Budget Detail)
     * ------------------------------------------------------------------ */

    /**
     * Entry summary satu baris per pasangan cost_center_header/id_cost_header.
     */
    public function getHeaderAccounts(string $year, ?string $dept = null, string $source = self::SOURCE): array
    {
        if (empty($dept)) {
            return [];
        }

        $coas = $this->db->table('gw_plan__master_coa')
            ->select('main_account, cost_center_header, id_cost_header')
            ->where('status', 'A')
            ->groupStart()
                ->where('type', 'GA')
                ->orWhereIn('category', ['ADMINEXP', 'SALARYEXP', 'OTHERS'])
            ->groupEnd()
            ->orderBy('cost_center_header', 'ASC')
            ->orderBy('id_cost_header', 'ASC')
            ->get()
            ->getResultArray();
        if (empty($coas)) {
            return [];
        }

        $actualByCoa = [];
        $ids = array_values(array_unique(array_map(static fn (array $coa): int => (int) $coa['main_account'], $coas)));
        foreach ($this->db->table('yp_plan__trans_budget_actual')
            ->select('id_coa, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`')
            ->where('id_dept', $dept)->where('year_code', $year)->whereIn('id_coa', $ids)
            ->get()->getResultArray() as $actual) {
            $id = (int) $actual['id_coa'];
            if (! isset($actualByCoa[$id])) {
                $actualByCoa[$id] = array_fill_keys(range(1, 8), 0.0);
            }
            for ($m = 1; $m <= 8; $m++) {
                $actualByCoa[$id][$m] += (float) ($actual[(string) $m] ?? 0);
            }
        }

        $entryByCoa = [];
        $entryBuilder = $this->db->table('yp_plan__trans_budget_entry_data t')
            ->select('t.id, t.id_coa')->where('t.id_dept', $dept)->where('t.year_code', $year)->whereIn('t.id_coa', $ids)->orderBy('t.id', 'DESC');
        if (\App\Libraries\DbCompat::hasEntrySource()) {
            $entryBuilder->groupStart()->where('t.source', $source)->orWhere('t.source IS NULL')->groupEnd();
        }
        foreach ($entryBuilder->get()->getResultArray() as $entry) {
            $entryByCoa[(int) $entry['id_coa']] = true;
        }

        $groups = [];
        foreach ($coas as $coa) {
            $header = trim((string) ($coa['cost_center_header'] ?? '')) ?: 'Lainnya';
            $headerId = (string) ($coa['id_cost_header'] ?? '0');
            $key = $header . "\\0" . $headerId;
            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'cost_center_header' => $header,
                    'id_cost_header' => $headerId,
                    'actual' => array_fill_keys(['jan','feb','mar','apr','may','jun','jul','aug'], 0.0),
                    'total_actual' => 0.0,
                    'status_entry' => 'belum',
                ];
            }
            $id = (int) $coa['main_account'];
            foreach (array_keys($groups[$key]['actual']) as $offset => $month) {
                $groups[$key]['actual'][$month] += (float) ($actualByCoa[$id][$offset + 1] ?? 0);
            }
            if (isset($entryByCoa[$id])) {
                $groups[$key]['status_entry'] = 'sudah';
            }
        }

        foreach ($groups as &$group) {
            $group['total_actual'] = array_sum($group['actual']);
            foreach ($group['actual'] as $month => $value) {
                $group[$month] = $value;
            }
            $group['total'] = $group['total_actual'];
        }
        unset($group);

        return array_values($groups);
    }

    /**
     * Matrix data per Sub-Account untuk sebuah grup Header Account OPEX GA:
     * budget (12 bulan) + actual (realisasi) dari tabel actual.
     *
     * Header dan idx dikirim sebagai pasangan grouping, bukan sebagai
     * main_account tunggal.
     */
    public function getDetailMatrix(string $year, ?string $dept, string $header, string $source = self::SOURCE, ?string $headerId = null): array
    {
        $headerId = trim((string) ($headerId ?? ''));
        if (trim($header) === '' || $headerId === '') {
            return [];
        }

        // Header dan idx adalah pasangan grouping, bukan main_account.
        $subAccounts = $this->getGroupCoas($header, $headerId);

        $result = [];
        foreach ($subAccounts as $sub) {
            $coaId = (int) $sub['main_account'];

            // Budget data
            $budget = $this->db->table('yp_plan__trans_budget_entry_data t')
                ->select("t.id AS entry_data_id,
                         IFNULL(t.`1`,0) AS b1, IFNULL(t.`2`,0) AS b2, IFNULL(t.`3`,0) AS b3,
                         IFNULL(t.`4`,0) AS b4, IFNULL(t.`5`,0) AS b5, IFNULL(t.`6`,0) AS b6,
                         IFNULL(t.`7`,0) AS b7, IFNULL(t.`8`,0) AS b8, IFNULL(t.`9`,0) AS b9,
                         IFNULL(t.`10`,0) AS b10, IFNULL(t.`11`,0) AS b11, IFNULL(t.`12`,0) AS b12,
                         IFNULL(t.total,0) AS btotal")
                ->where('t.id_coa', $coaId)
                ->where('t.id_dept', $dept)
                ->where('t.year_code', $year)
                ->orderBy('t.id', 'DESC')
                ->limit(1);

            if (\App\Libraries\DbCompat::hasEntrySource()) {
                $budget->groupStart()
                    ->where('t.source', $source)
                    ->orWhere('t.source IS NULL')
                ->groupEnd();
            }
            $budgetRow = $budget->get()->getRowArray();

            // Actual data
            $actual = $this->db->table('yp_plan__trans_budget_actual a')
                ->select("IFNULL(a.`1`,0) AS a1, IFNULL(a.`2`,0) AS a2, IFNULL(a.`3`,0) AS a3,
                         IFNULL(a.`4`,0) AS a4, IFNULL(a.`5`,0) AS a5, IFNULL(a.`6`,0) AS a6,
                         IFNULL(a.`7`,0) AS a7, IFNULL(a.`8`,0) AS a8, IFNULL(a.`9`,0) AS a9,
                         IFNULL(a.`10`,0) AS a10, IFNULL(a.`11`,0) AS a11, IFNULL(a.`12`,0) AS a12,
                         (IFNULL(a.`1`,0)+IFNULL(a.`2`,0)+IFNULL(a.`3`,0)+IFNULL(a.`4`,0)+
                          IFNULL(a.`5`,0)+IFNULL(a.`6`,0)+IFNULL(a.`7`,0)+IFNULL(a.`8`,0)+
                          IFNULL(a.`9`,0)+IFNULL(a.`10`,0)+IFNULL(a.`11`,0)+IFNULL(a.`12`,0)) AS atotal,
                         a.assumption, a.notes")
                ->where('a.id_coa', $coaId)
                ->where('a.id_dept', $dept)
                ->where('a.year_code', $year)
                ->get()
                ->getRowArray();

            $actual['atotal'] = (float) ($actual['atotal'] ?? 0);
            $entryDataId = (int) ($budgetRow['entry_data_id'] ?? 0);

            $result[] = [
                'id_coa' => $coaId,
                'main_account' => (string) $coaId,
                'acct_code' => (string) ($sub['id_acct_ext'] ?: $coaId),
                'coa_name' => (string) ($sub['cost_center_desc'] ?? ''),
                'cost_center_header' => (string) ($sub['cost_center_header'] ?? $header),
                'id_cost_header' => (string) ($sub['id_cost_header'] ?? $headerId),
                'entry_data_id' => $entryDataId,
                'budget' => $budgetRow ?: array_fill_keys(['b1','b2','b3','b4','b5','b6','b7','b8','b9','b10','b11','b12','btotal'], 0),
                'actual' => $actual ?: array_fill_keys(['a1','a2','a3','a4','a5','a6','a7','a8','a9','a10','a11','a12','atotal'], 0),
            ];
        }

        return $result;
    }

    /**
     * Batch simpan detail breakdown items (dari modal detail).
     *
     * Kolom bulan di yp_plan__trans_budget_entry_detail bernama jan..dec
     * (bukan numerik 1..12), konsisten dengan BudgetBreakdownTrait.
     *
     * Bila parent entry budget belum ada (entry_data_id = 0 atau tidak
     * ditemukan), parent dibuat otomatis dari $parentHint
     * (id_coa / id_dept / year_code) sehingga modal detail tetap bisa
     * dipakai walau budget sub-account belum pernah disimpan.
     *
     * @param array  $items      [{ nama_barang, jan..dec, sort_order }]
     * @param int    $entryDataId ID dari yp_plan__trans_budget_entry_data (0 bila belum ada)
     * @param array  $parentHint [id_coa, id_dept, year_code] untuk auto-create parent
     */
    public function saveDetailItemsBatch(int $entryDataId, array $items, int $userId, array $parentHint = []): array
    {
        // Validasi kelengkapan hint lebih awal (sebelum transaksi) untuk
        // kasus entry_data_id = 0, agar tidak membuka transaksi kosong.
        if ($entryDataId <= 0) {
            $idCoa  = (int) ($parentHint['id_coa'] ?? 0);
            $idDept = (int) ($parentHint['id_dept'] ?? 0);
            $year   = (int) ($parentHint['year_code'] ?? 0);
            if ($idCoa <= 0 || $idDept <= 0 || $year <= 0) {
                return ['success' => false, 'message' => 'Entry budget tidak ditemukan dan data parent tidak lengkap.', 'count' => 0];
            }
        }

        $this->db->transStart();

        $parent = null;
        if ($entryDataId > 0) {
            $parentBuilder = $this->db->table('yp_plan__trans_budget_entry_data')
                ->where('id', $entryDataId);
            if (\App\Libraries\DbCompat::hasEntrySource()) {
                $parentBuilder->groupStart()
                    ->where('source', self::SOURCE)
                    ->orWhere('source IS NULL')
                ->groupEnd();
            }
            $parent = $parentBuilder->get()->getRowArray();
        }

        if (! $parent) {
            // Auto-create parent dari hint agar detail bisa disimpan tanpa
            // harus mengisi budget terlebih dahulu.
            $idCoa  = (int) ($parentHint['id_coa'] ?? 0);
            $idDept = (int) ($parentHint['id_dept'] ?? 0);
            $year   = (int) ($parentHint['year_code'] ?? 0);

            if ($idCoa <= 0 || $idDept <= 0 || $year <= 0) {
                $this->db->transComplete();

                return ['success' => false, 'message' => 'Entry budget tidak ditemukan dan data parent tidak lengkap.', 'count' => 0];
            }

            // Cegah duplikat bila parent ternyata sudah ada — ambil parent
            // yang masih kompatibel dengan source modul ini.
            $existingBuilder = $this->db->table('yp_plan__trans_budget_entry_data')
                ->where('id_coa', $idCoa)
                ->where('id_dept', $idDept)
                ->where('year_code', $year)
                ->orderBy('id', 'DESC')
                ->limit(1);
            if (\App\Libraries\DbCompat::hasEntrySource()) {
                $existingBuilder->groupStart()
                    ->where('source', self::SOURCE)
                    ->orWhere('source IS NULL')
                ->groupEnd();
            }
            $existing = $existingBuilder->get()->getRowArray();

            if ($existing) {
                $parent      = $existing;
                $entryDataId = (int) $existing['id'];
            } else {
                $insertData = [
                    'id_coa'       => $idCoa,
                    'id_dept'      => $idDept,
                    'year_code'    => $year,
                    'total'        => 0,
                    'created_by'   => $userId,
                    'created_date' => date('Y-m-d H:i:s'),
                ];

                if (\App\Libraries\DbCompat::hasEntrySource()) {
                    $insertData['source'] = self::SOURCE;
                }
                if (\App\Libraries\DbCompat::hasEntrySubmitStatus()) {
                    $insertData['submit_status'] = 'DRAFT';
                }

                $this->db->table('yp_plan__trans_budget_entry_data')->insert($insertData);
                $entryDataId = (int) $this->db->insertID();
                $parent      = array_merge(['id' => $entryDataId], $insertData);
            }
        }

        // Selalu cocokkan parent yang dipakai dengan konteks request. Ini
        // mencegah entry_data_id milik cost center/tahun/COA lain dipakai
        // untuk menghapus atau menimpa breakdown.
        $expectedCoa  = (int) ($parentHint['id_coa'] ?? 0);
        $expectedDept = (int) ($parentHint['id_dept'] ?? 0);
        $expectedYear = (int) ($parentHint['year_code'] ?? 0);
        if (($expectedCoa > 0 && (int) ($parent['id_coa'] ?? 0) !== $expectedCoa)
            || ($expectedDept > 0 && (int) ($parent['id_dept'] ?? 0) !== $expectedDept)
            || ($expectedYear > 0 && (int) ($parent['year_code'] ?? 0) !== $expectedYear)) {
            $this->db->transComplete();

            return ['success' => false, 'message' => 'Parent detail tidak sesuai dengan konteks cost center, tahun, atau akun.', 'count' => 0];
        }

        $hintHeader = trim((string) ($parentHint['header'] ?? ''));
        $hintHeaderId = trim((string) ($parentHint['id_cost_header'] ?? ''));
        if ($hintHeader !== '' && $hintHeaderId !== '') {
            $parentMaster = $this->db->table('gw_plan__master_coa')
                ->select('cost_center_header, id_cost_header')
                ->where('main_account', (int) ($parent['id_coa'] ?? 0))->limit(1)->get()->getRowArray();
            if (! $parentMaster || (string) $parentMaster['cost_center_header'] !== $hintHeader || (string) $parentMaster['id_cost_header'] !== $hintHeaderId) {
                $this->db->transComplete();

                return ['success' => false, 'message' => 'Parent detail tidak termasuk kelompok header yang dipilih.', 'count' => 0];
            }
        }

        // Hapus item lama (mode replace)
        $this->db->table('yp_plan__trans_budget_entry_detail')
            ->where('entry_data_id', $entryDataId)
            ->delete();

        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
        $saved  = 0;

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $namaBarang = trim((string) ($item['nama_barang'] ?? ''));
            if ($namaBarang === '') {
                continue;
            }

            $total  = 0;
            $fields = [];
            foreach ($months as $mk) {
                $val         = (float) ($item[$mk] ?? 0);
                $fields[$mk] = $val;
                $total      += $val;
            }

            $this->db->table('yp_plan__trans_budget_entry_detail')->insert(array_merge([
                'entry_data_id' => $entryDataId,
                'id_coa'        => (int) ($parent['id_coa'] ?? 0),
                'id_dept'       => (int) ($parent['id_dept'] ?? 0),
                'year_code'     => (int) ($parent['year_code'] ?? 0),
                'nama_barang'   => $namaBarang,
                'total'         => $total,
                'sort_order'    => (int) ($item['sort_order'] ?? ($saved + 1)),
                'created_by'    => $userId,
            ], $fields));
            $saved++;
        }

        // Roll-up atomik: detail -> parent account -> total header.
        $parentTotals = $this->db->table('yp_plan__trans_budget_entry_detail')
            ->select('SUM(jan) AS jan, SUM(feb) AS feb, SUM(mar) AS mar, SUM(apr) AS apr, SUM(may) AS may, SUM(jun) AS jun, SUM(jul) AS jul, SUM(aug) AS aug, SUM(sep) AS sep, SUM(oct) AS oct, SUM(nov) AS nov, SUM(`dec`) AS `dec`')
            ->where('entry_data_id', $entryDataId)->get()->getRowArray() ?: [];
        $parentUpdate = [];
        $parentTotal = 0.0;
        foreach (['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $month) {
            $value = (float) ($parentTotals[$month] ?? 0);
            $parentUpdate[$this->monthColumn($month)] = $value;
            $parentTotal += $value;
        }
        $parentUpdate['total'] = $parentTotal;
        $this->db->table('yp_plan__trans_budget_entry_data')->where('id', $entryDataId)->update($parentUpdate);

        $groupMaster = $this->db->table('gw_plan__master_coa')
            ->select('cost_center_header, id_cost_header')->where('main_account', (int) ($parent['id_coa'] ?? 0))->limit(1)->get()->getRowArray();
        $headerTotal = null;
        if ($groupMaster && $groupMaster['cost_center_header'] !== null) {
            $groupCoas = $this->getGroupCoas((string) $groupMaster['cost_center_header'], (string) $groupMaster['id_cost_header']);
            $headerTotal = $this->calculateGroupBudgetTotal((string) ($parent['year_code'] ?? 0), (string) ($parent['id_dept'] ?? 0), $groupCoas);
            $this->upsertHeaderTotal((string) ($parent['year_code'] ?? 0), (string) ($parent['id_dept'] ?? 0), (string) $groupMaster['id_cost_header'], $headerTotal, $userId);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menyimpan detail item.', 'count' => 0];
        }

        return [
            'success' => true,
            'message' => "Detail breakdown berhasil disimpan ({$saved} item).",
            'count' => $saved,
            'parent' => $parentUpdate,
            'header_total' => $headerTotal,
        ];
    }

    private function monthColumn(string $month): string
    {
        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        return (string) (array_search($month, $months, true) + 1);
    }

    /**
     * Import actual dari Excel — lihat ActualImportTrait (Phase 2.3).
     */
}
