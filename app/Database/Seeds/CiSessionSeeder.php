<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder untuk ci_sessions (percobaan login manual).
 *
 * Membuat 1 baris session yang "sudah login" supaya halaman yang dilindungi
 * AuthFilter bisa diuji tanpa lewat form login.
 *
 * Catatan:
 *  - Data session disimpan dalam format PHP serialize handler ("php"): `key|serialize(val);`
 *  - Panjang & charset session ID mengikuti logika CodeIgniter 4 (configureSidLength).
 *  - Nilai user di bawah bersifat placeholder. Ganti setelah Model User/Role tersedia (Task 2/3).
 *
 * Penggunaan:
 *   php spark db:seed CiSessionSeeder
 *   lalu set cookie : ci_session=<ID yang dicetak>
 */
class CiSessionSeeder extends Seeder
{
    public function run()
    {
        // -------------------------------------------------------------
        // Data sesi "login" (placeholder hingga Model User/Role siap)
        // -------------------------------------------------------------
        $user = [
            'user_logged_in' => true,
            'user_id'        => 1,
            'username'       => 'demo',
            'user_name'      => 'Demo Admin',
            'user_admin'     => 'Y',
            'role_id'        => 1,
            'year_code'      => (int) date('Y'),
            // Marker khusus seeder (dipakai untuk hapus session lama supaya idempoten)
            'ci_seeder'      => 'demo_login',
        ];

        // Idempoten: hapus session hasil seeder sebelumnya
        $this->db->table('ci_sessions')->like('data', 'ci_seeder|s:', 'both')
            ->delete();

        $sessionId = $this->generateSessionId();
        $data      = $this->encodeSession($user);

        $this->db->table('ci_sessions')->insert([
            'id'         => $sessionId,
            'ip_address' => '127.0.0.1',
            'timestamp'  => time(),
            'data'       => $data,
        ]);

        echo 'Session seeded.' . PHP_EOL;
        echo 'Session ID  : ' . $sessionId . PHP_EOL;
        echo 'Set cookie  : ci_session=' . $sessionId . PHP_EOL;
    }

    /**
     * Encode array session menggunakan session_encode() PHP sehingga formatnya
     * persis seperti yang diharapkan session decoder (handler "php").
     *
     * Format: key|serialize(value);  + key "internal" __ci_last_regenerate.
     *
     * Catatan: TIDAK memakai concatenation serialize() manual, karena pada sebagian
     * build PHP, serialize() dapat menambahkan ';' di akhir sehingga menghasilkan
     * delimiter ganda (`;;`) yang tidak bisa di-decode.
     */
    protected function encodeSession(array $data): string
    {
        $data['__ci_last_regenerate'] = time();

        $wasActive = session_status() === PHP_SESSION_ACTIVE;
        $savedId   = session_id();

        if ($wasActive) {
            $restore     = $_SESSION;
            $encoded     = $this->encodeWithState($data);
            $_SESSION    = $restore;
        } else {
            session_id(bin2hex(random_bytes(8)));
            session_start();
            $encoded = $this->encodeWithState($data);
            session_destroy();
        }

        // Pulihkan session_id bila environment asal sudah aktif
        if ($wasActive && $savedId !== '' && $savedId !== session_id()) {
            session_id($savedId);
        }

        return $encoded;
    }

    protected function encodeWithState(array $data): string
    {
        $_SESSION = $data;

        return (string) session_encode();
    }

    /**
     * Buat session ID yang memenuhi sidRegexp CodeIgniter 4.
     */
    protected function generateSessionId(): string
    {
        $bitsPerCharacter = (int) (ini_get('session.sid_bits_per_character') !== false
            ? ini_get('session.sid_bits_per_character')
            : 4);

        $sidLength = (int) (ini_get('session.sid_length') !== false
            ? ini_get('session.sid_length')
            : 40);

        if (($sidLength * $bitsPerCharacter) < 160) {
            $bits          = ($sidLength * $bitsPerCharacter);
            $sidLength     += (int) ceil((160 % $bits) / $bitsPerCharacter);
        }

        // Charset hex selalu subset dari semua kemungkinan sid charset CI4 (4/5/6 bits).
        $hex = '0123456789abcdef';

        $id = '';
        for ($i = 0; $i < $sidLength; $i++) {
            $id .= $hex[random_int(0, 15)];
        }

        return $id;
    }
}