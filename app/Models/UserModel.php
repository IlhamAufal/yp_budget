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

    /* ------------------------------------------------------------------
     * Phase 1.1 — User Management (System Administration)
     * ------------------------------------------------------------------ */

    /**
     * Daftar user beserta role-nya (untuk halaman User Management).
     */
    public function getAllUsers(array $filters = []): array
    {
        $builder = $this->db->table('gw_sm__user u')
            ->select("u.*,
                GROUP_CONCAT(DISTINCT r.role_name_idn ORDER BY r.role_id SEPARATOR ', ') AS role_names,
                GROUP_CONCAT(DISTINCT r.role_id ORDER BY r.role_id SEPARATOR ',') AS role_ids")
            ->join('gw_sm__profile p', 'p.profile_user_id = u.user_id', 'left')
            ->join('gw_sm__role r', 'r.role_id = p.profile_role_id AND r.role_active = \'Y\'', 'left')
            ->groupBy('u.user_id');

        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $builder->groupStart()
                ->like('u.user_username', $search)
                ->orLike('u.user_name', $search)
                ->orLike('u.user_email', $search)
            ->groupEnd();
        }

        if (($filters['role_id'] ?? '') !== '') {
            $builder->where('p.profile_role_id', (int) $filters['role_id']);
        }

        if (($filters['status'] ?? '') !== '') {
            $builder->where('u.user_active', $filters['status']);
        }

        return $builder
            ->orderBy('u.user_id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Simpan user baru / update. Password hanya diproses saat diisi.
     *
     * @return array ['success' => bool, 'message' => string, 'id' => ?int]
     */
    public function saveUser(array $data, ?int $id = null): array
    {
        $username = trim($data['user_username'] ?? '');
        $name     = trim($data['user_name'] ?? '');

        if ($username === '') {
            return ['success' => false, 'message' => 'Username wajib diisi.'];
        }
        if ($name === '') {
            return ['success' => false, 'message' => 'Nama lengkap wajib diisi.'];
        }

        // Duplikasi username
        $dup = $this->db->table('gw_sm__user')
            ->where('user_username', $username)
            ->where('user_id !=', $id ?? 0)
            ->countAllResults();
        if ($dup > 0) {
            return ['success' => false, 'message' => 'Username sudah digunakan user lain.'];
        }

        // Password wajib saat membuat user baru
        $password = (string) ($data['user_password'] ?? '');
        if (! $id && $password === '') {
            return ['success' => false, 'message' => 'Password wajib diisi untuk user baru.'];
        }

        $this->db->transStart();

        if ($id) {
            $fields = [
                'user_username' => $username,
                'user_name'     => $name,
                'user_email'    => trim($data['user_email'] ?? '') ?: null,
                'user_active'   => ($data['user_active'] ?? 'Y') === 'Y' ? 'Y' : 'N',
                'user_block'    => ($data['user_block'] ?? 'N') === 'Y' ? 'Y' : 'N',
                'user_change_on' => date('Y-m-d H:i:s'),
                'user_change_by' => (string) session()->get('user_username'),
            ];

            // Field admin hanya diubah bila dikirim form (mencegah reset ke 'N'
            // saat field admin dihapus dari UI — admin kini ditandai via role).
            if (array_key_exists('user_admin', $data)) {
                $fields['user_admin'] = (($data['user_admin'] ?? 'N') === 'Y') ? 'Y' : 'N';
            }

            if ($password !== '') {
                $fields['user_password'] = $this->hashPassword($password);
                $fields['user_salt']     = '';
            }

            $this->db->table('gw_sm__user')->where('user_id', $id)->update($fields);
        } else {
            $fields = [
                'user_username'   => $username,
                'user_name'       => $name,
                'user_email'      => trim($data['user_email'] ?? '') ?: null,
                'user_password'   => $this->hashPassword($password),
                'user_salt'       => '',
                'user_active'     => ($data['user_active'] ?? 'Y') === 'Y' ? 'Y' : 'N',
                'user_admin'      => ($data['user_admin'] ?? 'N') === 'Y' ? 'Y' : 'N',
                'user_block'      => ($data['user_block'] ?? 'N') === 'Y' ? 'Y' : 'N',
                'user_created_on' => date('Y-m-d H:i:s'),
                'user_created_by' => (string) session()->get('user_username'),
            ];

            $this->db->table('gw_sm__user')->insert($fields);
            $id = (int) $this->db->insertID();
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menyimpan user.'];
        }

        return [
            'success' => true,
            'message' => $id ? 'User berhasil diperbarui.' : 'User baru berhasil ditambahkan.',
            'id'      => $id,
        ];
    }

    /**
     * Aktif / non-aktifkan user.
     */
    public function toggleUser(int $id): array
    {
        $user = $this->db->table('gw_sm__user')->where('user_id', $id)->get()->getRowArray();
        if (! $user) {
            return ['success' => false, 'message' => 'User tidak ditemukan.'];
        }

        $newStatus = ($user['user_active'] === 'Y') ? 'N' : 'Y';
        $this->db->table('gw_sm__user')
            ->where('user_id', $id)
            ->update([
                'user_active'   => $newStatus,
                'user_change_on' => date('Y-m-d H:i:s'),
                'user_change_by' => (string) session()->get('user_username'),
            ]);

        return [
            'success' => true,
            'message' => $newStatus === 'Y' ? 'User diaktifkan.' : 'User dinonaktifkan.',
        ];
    }

    /**
     * Hapus user (relasi role ikut terhapus).
     */
    public function deleteUser(int $id): array
    {
        $user = $this->db->table('gw_sm__user')->where('user_id', $id)->get()->getRowArray();
        if (! $user) {
            return ['success' => false, 'message' => 'User tidak ditemukan.'];
        }

        // Cegah menghapus user yang sedang login sendiri
        if ((int) session()->get('user_id') === $id) {
            return ['success' => false, 'message' => 'Anda tidak dapat menghapus akun yang sedang digunakan.'];
        }

        $this->db->transStart();

        $this->db->table('gw_sm__profile')->where('profile_user_id', $id)->delete();
        $this->db->table('gw_sm__user')->where('user_id', $id)->delete();

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menghapus user.'];
        }

        return ['success' => true, 'message' => 'User berhasil dihapus.'];
    }

    /**
     * Reset password user.
     */
    public function resetPassword(int $id, string $password): array
    {
        $user = $this->db->table('gw_sm__user')->where('user_id', $id)->get()->getRowArray();
        if (! $user) {
            return ['success' => false, 'message' => 'User tidak ditemukan.'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter.'];
        }

        $this->db->table('gw_sm__user')
            ->where('user_id', $id)
            ->update([
                'user_password'   => $this->hashPassword($password),
                'user_salt'       => '',
                'user_change_on'  => date('Y-m-d H:i:s'),
                'user_change_by'  => (string) session()->get('user_username'),
            ]);

        return ['success' => true, 'message' => 'Password user berhasil di-reset.'];
    }
}
