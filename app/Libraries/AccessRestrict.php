<?php

namespace App\Libraries;

use Config\Database;

/**
 * AccessRestrict — Concurrent Access Locking (PRD standar 1.7).
 *
 * Mencegah dua user berbeda mengubah Cost Center / URL yang sama pada
 * tahun anggaran yang sama secara bersamaan.
 *
 * NOTE SCHEMA: tabel gw_sm__access_restrict (legacy) memakai kolom:
 *   user_id (PK), created_date, menu_url, status ENUM('A','N'), year, type
 * Versi sebelumnya library ini memakai kolom `url`/`updated_at` yang TIDAK
 * ada di schema, sehingga check() selalu "silent-fail" (selalu diizinkan).
 * Versi ini disesuaikan dengan kolom legacy yang benar.
 */
class AccessRestrict
{
    /** Menit sebelum lock dianggap kedaluwarsa (di-release otomatis). */
    public const LOCK_TIMEOUT_MINUTES = 15;

    protected $db;
    protected $table = 'gw_sm__access_restrict';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Hapus lock yang sudah kedaluwarsa (created_date melebihi timeout).
     */
    private function purgeExpired(): void
    {
        $limit = date('Y-m-d H:i:s', strtotime('-' . self::LOCK_TIMEOUT_MINUTES . ' minutes'));

        $this->db->table($this->table)
            ->where('created_date <', $limit)
            ->delete();
    }

    /**
     * Alias kompatibel: memanggil checkLock().
     *
     * @return array ['allowed' => bool, 'accessed_by' => string|null, 'message' => string]
     */
    public function check(string $userId, string $url, int $year): array
    {
        return $this->checkLock($userId, $url, $year);
    }

    /**
     * Cek & pasang lock untuk kombinasi (user, url, year).
     *
     * Setiap form entry budget WAJIB memanggil method ini sebelum proses simpan
     * (PRD 1.7). Bila user lain sedang mengunci url+tahun yang sama, method ini
     * mengembalikan allowed=false sehingga simpan dibatalkan.
     *
     * @return array ['allowed' => bool, 'accessed_by' => string|null, 'message' => string]
     */
    public function checkLock(string $userId, string $url, int $year): array
    {
        try {
            $this->purgeExpired();

            // 1. Sudahkah URL & tahun ini sedang dikunci user lain?
            $existing = $this->db->table($this->table)
                ->where('menu_url', $url)
                ->where('year', $year)
                ->where('status', 'A')
                ->where('user_id !=', $userId)
                ->get()
                ->getRowArray();

            if ($existing) {
                $by = $existing['user_id'] ?? 'User lain';

                return [
                    'allowed'     => false,
                    'accessed_by' => $by,
                    'message'     => "Data ini sedang dikunci oleh user '{$by}' untuk tahun anggaran {$year}. Silakan tunggu hingga user selesai.",
                ];
            }

            // 2. Lepas lock lama milik user ini (PK = user_id + menu_url),
            //    lalu pasang lock baru untuk (url, year) saat ini.
            $this->db->table($this->table)->where('user_id', $userId)->delete();

            $this->db->table($this->table)->insert([
                'user_id'      => (string) $userId,
                'menu_url'     => $url,
                'status'       => 'A',
                'year'         => (int) $year,
                'created_date' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Fail-safe: bila tabel belum ada / error, tetap izinkan proses.
            log_message('error', 'AccessRestrict::checkLock error: ' . $e->getMessage());
        }

        return [
            'allowed'     => true,
            'accessed_by' => null,
            'message'     => '',
        ];
    }

    /**
     * Lepas seluruh lock milik seorang user (dipanggil saat logout).
     */
    public function release(string $userId): void
    {
        try {
            $this->db->table($this->table)
                ->where('user_id', $userId)
                ->delete();
        } catch (\Throwable $e) {
            log_message('error', 'AccessRestrict::release error: ' . $e->getMessage());
        }
    }
}
