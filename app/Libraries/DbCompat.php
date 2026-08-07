<?php

namespace App\Libraries;

use Config\Database;

/**
 * DbCompat — Pendeteksi kolom skema DB dengan cache per-request.
 *
 * Keputusan user 6 Agt 2026: DB di-copas dari sistem lama dan STRUKTUR
 * TIDAK BOLEH diubah (tanpa ALTER). Beberapa kolom modern tidak ada di
 * skema legacy, antara lain:
 *   - yp_plan__trans_budget_entry_data.source        (dipakai membedakan modul)
 *   - yp_plan__trans_budget_entry_data.submit_status (workflow submit)
 *   - gw_sm__menu.menu_group                         (dipakai menu page)
 *
 * Helper ini menyediakan pengecekan keberadaan kolom (cache per-request)
 * sehingga kode dapat beradaptasi: fitur yang butuh kolom tersebut
 * di-nonaktifkan sementara, tanpa merusak fungsionalitas lain.
 */
class DbCompat
{
    /** Cache: [tabel => [kolom => bool]]. */
    private static array $columns = [];

    /**
     * Cek apakah sebuah kolom ada pada tabel (hasil di-cache per-request).
     *
     * Cek keberadaan tabel via `SHOW TABLES` terlebih dahulu agar tabel yang
     * tidak ada TIDAK memicu log error DB (getFieldNames pada tabel missing
     * menghasilkan exception + log CI4).
     */
    public static function hasColumn(string $table, string $column): bool
    {
        if (! isset(self::$columns[$table])) {
            $exists = false;
            try {
                $db = Database::connect();
                $dbName = $db->getDatabase();
                $row = $db->query('SELECT COUNT(*) AS n FROM information_schema.tables WHERE table_schema = ? AND table_name = ?', [$dbName, $table])->getRowArray();
                $exists = (int) ($row['n'] ?? 0) > 0;
            } catch (\Throwable $e) {
                $exists = false;
            }

            self::$columns[$table] = [];
            if ($exists) {
                try {
                    $db   = Database::connect();
                    self::$columns[$table] = array_flip($db->getFieldNames($table));
                } catch (\Throwable $e) {
                    self::$columns[$table] = [];
                }
            }
        }

        return isset(self::$columns[$table][$column]);
    }

    /** Kolom `source` tersedia di yp_plan__trans_budget_entry_data? */
    public static function hasEntrySource(): bool
    {
        return self::hasColumn('yp_plan__trans_budget_entry_data', 'source');
    }

    /** Kolom `submit_status` tersedia di yp_plan__trans_budget_entry_data? */
    public static function hasEntrySubmitStatus(): bool
    {
        return self::hasColumn('yp_plan__trans_budget_entry_data', 'submit_status');
    }

    /** Kolom `menu_group` tersedia di gw_sm__menu? */
    public static function hasMenuGroup(): bool
    {
        return self::hasColumn('gw_sm__menu', 'menu_group');
    }

    /**
     * Ekspresi SELECT untuk kolom submit_status (fallback literal 'DRAFT'
     * bila kolom tidak ada di skema legacy).
     */
    public static function submitStatusExpr(string $alias = 't'): string
    {
        return self::hasEntrySubmitStatus()
            ? "COALESCE({$alias}.submit_status, 'DRAFT') AS submit_status"
            : "'DRAFT' AS submit_status";
    }
}
