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
     * Cari user berdasarkan username
     */
    public function getByUsername(string $username): ?array
    {
        return $this->where('user_username', $username)->first();
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
        return ($user['user_active'] ?? 'N') === 'Y';
    }

    /**
     * Cek apakah user diblokir
     */
    public function isBlocked(array $user): bool
    {
        return ($user['user_block'] ?? 'N') === 'Y';
    }

    /**
     * Hash password baru
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
