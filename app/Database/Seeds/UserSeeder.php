<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder untuk membuat user demo untuk testing login.
 *
 * Penggunaan:
 *   php spark db:seed UserSeeder
 */
class UserSeeder extends Seeder
{
    public function run()
    {
        // Hapus user demo jika ada
        $this->db->table('gw_sm__user')->where('user_username', 'admin')->delete();

        // Hash password
        $password = password_hash('password', PASSWORD_DEFAULT);

        // Insert user demo
        $this->db->table('gw_sm__user')->insert([
            'user_username'   => 'admin',
            'user_name'       => 'Administrator',
            'user_email'      => 'admin@example.com',
            'user_password'   => $password,
            'user_salt'       => substr(md5(uniqid()), 0, 3),
            'user_active'     => 'Y',
            'user_admin'      => 'Y',
            'user_block'      => 'N',
            'user_created_by' => 'system',
        ]);

        echo 'User demo berhasil dibuat.' . PHP_EOL;
        echo 'Username : admin' . PHP_EOL;
        echo 'Password : password' . PHP_EOL;
    }
}
