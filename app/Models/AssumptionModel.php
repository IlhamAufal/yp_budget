<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

/**
 * AssumptionModel — Master Assumption (PRD Phase 2.3 gap / Phase 3).
 *
 * Mengelola 5 tabel asumsi legacy:
 *   - yp_plan__master_assumption_type            (kamus tipe)
 *   - yp_plan__master_assumption                 (asumsi ekonomi per tahun)
 *   - yp_plan__master_assumption_sales_domestic  (Volume/ASP per channel)
 *   - yp_plan__master_assumption_sales_export    (Volume/ASP per produk)
 *   - yp_plan__master_assumption_other           (rasio FOH/selling expense)
 *
 * Semua penyimpanan memakai semantik "snapshot per tahun": data tahun tsb
 * diganti seluruhnya saat save/upload (konsisten dengan perilaku legacy).
 */
class AssumptionModel extends Model
{
    protected $db;

    public const T_TYPES    = 'yp_plan__master_assumption_type';
    public const T_ECON     = 'yp_plan__master_assumption';
    public const T_DOMESTIC = 'yp_plan__master_assumption_sales_domestic';
    public const T_EXPORT   = 'yp_plan__master_assumption_sales_export';
    public const T_OTHER    = 'yp_plan__master_assumption_other';

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::connect();
    }

    /* ------------------------------------------------------------------
     * Kamus tipe asumsi
     * ------------------------------------------------------------------ */

    public function getTypes(): array
    {
        return $this->db->table(self::T_TYPES)
            ->where('status', 'A')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Resolve type_id: angka langsung, atau cocokkan dengan desc_assumption.
     */
    public function resolveTypeId($typeIdOrDesc): ?int
    {
        if (is_numeric($typeIdOrDesc)) {
            return (int) $typeIdOrDesc;
        }

        $desc = trim((string) $typeIdOrDesc);
        if ($desc === '') {
            return null;
        }

        // Exact match dulu (deterministik); tidak ada fuzzy LIKE agar %/_ tidak
        // menangkap tipe yang salah saat upload Excel.
        $row = $this->db->table(self::T_TYPES)
            ->select('id')
            ->where('status', 'A')
            ->where('desc_assumption', $desc)
            ->get()->getRowArray();

        return $row ? (int) $row['id'] : null;
    }

    /* ------------------------------------------------------------------
     * Asumsi ekonomi (KURS, INFLATION, GDP)
     * ------------------------------------------------------------------ */

    public function getEconomic(int $year): array
    {
        return $this->db->table(self::T_ECON . ' a')
            ->select('a.*, t.desc_assumption AS type_desc')
            ->join(self::T_TYPES . ' t', 't.id = a.type_id', 'left')
            ->where('a.year', $year)
            ->where('a.status', 'A')
            ->orderBy('t.id', 'ASC')
            ->get()->getResultArray();
    }

    public function saveEconomic(int $year, array $rows, ?int $userId = null): bool
    {
        $clean = [];
        foreach ($rows as $r) {
            $desc  = trim((string) ($r['desc'] ?? ''));
            $typeId = $this->resolveTypeId($r['type_id'] ?? null);
            if ($typeId === null || $desc === '') {
                continue;
            }

            $clean[] = [
                'desc'         => $desc,
                'type_id'      => $typeId,
                'value'        => (float) ($r['value'] ?? 0),
                'year'         => $year,
                'year_codex'   => $year,
                'status'       => 'A',
                'created_date' => date('Y-m-d H:i:s'),
                'created_by'   => $userId ?? 0,
            ];
        }

        return $this->replaceYear(self::T_ECON, 'year', $year, $clean);
    }

    /**
     * Nilai KURS per mata uang untuk simulasi, mis. ['USD' => 15500, 'EUR' => 16595].
     */
    public function getKurs(int $year): array
    {
        $result = [];
        $rows = $this->db->table(self::T_ECON . ' a')
            ->select('a.value, t.desc_assumption AS type_desc')
            ->join(self::T_TYPES . ' t', 't.id = a.type_id', 'left')
            ->where('a.year', $year)
            ->where('a.status', 'A')
            ->like('t.desc_assumption', 'KURS')
            ->get()->getResultArray();

        foreach ($rows as $row) {
            $currency = strtoupper(trim(str_replace(['KURS', '-', ' ', '/'], '', (string) ($row['type_desc'] ?? ''))));
            if ($currency === '') {
                continue;
            }
            $result[$currency] = (float) ($row['value'] ?? 0);
        }

        return $result;
    }

    /* ------------------------------------------------------------------
     * Asumsi Sales Domestic (Volume/ASP per channel)
     * ------------------------------------------------------------------ */

    public function getSalesDomestic(int $year): array
    {
        return $this->db->table(self::T_DOMESTIC)
            ->where('year_code', $year)
            ->orderBy('key_channel', 'ASC')
            ->orderBy('key_description', 'ASC')
            ->get()->getResultArray();
    }

    public function saveSalesDomestic(int $year, array $rows, ?int $userId = null): bool
    {
        $clean = $this->cleanSalesRows($rows, $userId, $year);

        return $this->replaceYear(self::T_DOMESTIC, 'year_code', $year, $clean);
    }

    /* ------------------------------------------------------------------
     * Asumsi Sales Export (Volume/ASP per produk)
     * ------------------------------------------------------------------ */

    public function getSalesExport(int $year): array
    {
        return $this->db->table(self::T_EXPORT)
            ->where('year_code', $year)
            ->orderBy('key_indicator', 'ASC')
            ->orderBy('key_description', 'ASC')
            ->get()->getResultArray();
    }

    public function saveSalesExport(int $year, array $rows, ?int $userId = null): bool
    {
        $clean = $this->cleanSalesRows($rows, $userId, $year);

        return $this->replaceYear(self::T_EXPORT, 'year_code', $year, $clean);
    }

    /* ------------------------------------------------------------------
     * Asumsi lain (rasio FOH / selling expense)
     * ------------------------------------------------------------------ */

    public function getOther(int $year): array
    {
        return $this->db->table(self::T_OTHER)
            ->where('year_code', $year)
            ->where('status', 'A')
            ->orderBy('id_assp', 'ASC')
            ->get()->getResultArray();
    }

    public function saveOther(int $year, array $rows, ?int $userId = null): bool
    {
        $clean = [];
        foreach ($rows as $r) {
            $variable = trim((string) ($r['variable_text'] ?? ''));
            if ($variable === '') {
                continue;
            }

            $clean[] = [
                'id_assp'       => trim((string) ($r['id_assp'] ?? '')),
                'tipe_group'    => trim((string) ($r['tipe_group'] ?? '')),
                'variable_text' => $variable,
                'value_text'    => (float) ($r['value_text'] ?? 0),
                'year_code'     => $year,
                'updated_by'    => $userId ?? 0,
                'updated_dated' => date('Y-m-d H:i:s'),
                'status'        => 'A',
            ];
        }

        return $this->replaceYear(self::T_OTHER, 'year_code', $year, $clean);
    }

    /* ------------------------------------------------------------------
     * Umum
     * ------------------------------------------------------------------ */

    public function getYears(): array
    {
        $years = [];
        foreach ([self::T_ECON, self::T_DOMESTIC, self::T_EXPORT, self::T_OTHER] as $table) {
            $col = $table === self::T_ECON ? 'year' : 'year_code';
            $rows = $this->db->table($table)->distinct()->select($col)->get()->getResultArray();
            foreach ($rows as $row) {
                $years[] = (int) $row[$col];
            }
        }

        $years = array_unique(array_filter($years));
        rsort($years);

        return $years ?: [(int) date('Y')];
    }

    /**
     * Bersihkan baris sales (domestic/export) → siap insert.
     */
    private function cleanSalesRows(array $rows, ?int $userId, int $year): array
    {
        $clean = [];
        foreach ($rows as $r) {
            $channel = trim((string) ($r['key_channel'] ?? ''));
            $desc    = trim((string) ($r['key_description'] ?? ''));
            $ind     = trim((string) ($r['key_indicator'] ?? ''));

            if ($channel === '' && $desc === '' && $ind === '') {
                continue;
            }

            $clean[] = [
                'key_channel'     => $channel,
                'key_description' => $desc,
                'key_indicator'   => $ind,
                'key_value'       => (float) ($r['key_value'] ?? 0),
                'year_code'       => $year,
                'created_by'      => $userId ?? 0,
                'created_date'    => date('Y-m-d H:i:s'),
            ];
        }

        return $clean;
    }

    /**
     * Ganti seluruh data suatu tabel untuk satu tahun (transaksi).
     * Kolom tahun dibedakan (year / year_code).
     */
    private function replaceYear(string $table, string $yearCol, int $year, array $rows): bool
    {
        $yearKey = $yearCol;

        $this->db->transStart();

        $this->db->table($table)->where($yearKey, $year)->delete();

        if (! empty($rows)) {
            foreach ($rows as &$row) {
                $row[$yearKey] = $year;
            }
            unset($row);
            $this->db->table($table)->insertBatch($rows);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
