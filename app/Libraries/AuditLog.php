<?php

namespace App\Libraries;

use Config\Database;

/**
 * AuditLog — Pencatatan aktivitas user ke tabel gw_sm__logs (PRD Phase 4.2).
 *
 * Skema legacy: id, type, user, function, message, create_date.
 * Dipanggil statis dari controller setelah aksi sukses (atau gagal bila perlu):
 *   AuditLog::log('SAVE', 'foh/saveBudget', "Budget FOH disimpan ...");
 *
 * Seluruh error ditelan (try/catch) supaya logging tidak pernah
 * memblokir proses utama aplikasi.
 */
class AuditLog
{
    /**
     * Catat satu baris log aktivitas.
     *
     * @param string      $type     Kategori: LOGIN, LOGOUT, SAVE, SUBMIT, DELETE,
     *                              UPLOAD, EXPORT, RBAC, ADJUSTMENT, NOTES, dll.
     * @param string      $function Nama fungsi/halaman (contoh: 'foh/saveBudget').
     * @param string      $message  Pesan detail aktivitas.
     * @param string|null $userId   ID user; default dari session('user_id').
     */
    public static function log(string $type, string $function, string $message, ?string $userId = null): void
    {
        try {
            $db   = Database::connect();
            $uid  = $userId ?? (string) (session()->get('user_id') ?? '');

            $db->table('gw_sm__logs')->insert([
                'type'        => mb_substr($type, 0, 140),
                'user'        => mb_substr($uid, 0, 14),
                'function'    => mb_substr($function, 0, 140),
                'message'     => mb_substr($message, 0, 65535),
                'create_date' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'AuditLog::log: ' . $e->getMessage());
        }
    }

    /**
     * Helper ringkas untuk aksi simpan yang sukses.
     */
    public static function saved(string $function, string $detail): void
    {
        self::log('SAVE', $function, $detail);
    }

    /**
     * Helper ringkas untuk aksi submit workflow yang sukses.
     */
    public static function submitted(string $function, string $detail): void
    {
        self::log('SUBMIT', $function, $detail);
    }
}
