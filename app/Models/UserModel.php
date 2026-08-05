<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'gw_sm__user';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'user_username',
        'user_name',
        'user_email',
        'user_password',
        'user_salt',
        'user_active',
        'user_admin',
        'user_block',
    ];

    /**
     * Cari user berdasarkan email
     */
    public function getByEmail(string $email)
    {
        return $this->where('user_email', $email)
                    ->orWhere('user_username', $email)
                    ->first();
    }

    /**
     * Cari user berdasarkan username atau email
     */
    public function getByUsernameOrEmail(string $login)
    {
        $login = trim($login);

        if (empty($login)) {
            return null;
        }

        $user = $this->groupStart()
                    ->where('user_username', $login)
                    ->orWhere('user_email', $login)
                ->groupEnd()
                ->first();

        if (! $user && ! str_contains($login, '@')) {
            $user = $this->like('user_email', $login . '@', 'after')->first();
        }

        return $user;
    }

    /**
     * Verifikasi password
     */
    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }

    /**
     * Cek apakah user aktif
     */
    public function isActive(array $user): bool
    {
        $active = $user['user_active'] ?? $user['status'] ?? 'Y';
        return in_array(strtoupper((string)$active), ['Y', '1', 'A', 'ACTIVE'], true);
    }

    /**
     * Cek apakah user diblokir
     */
    public function isBlocked(array $user): bool
    {
        $blocked = $user['user_block'] ?? 'N';
        return strtoupper((string)$blocked) === 'Y' || $blocked == 1;
    }

    /**
     * Hash password baru
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
