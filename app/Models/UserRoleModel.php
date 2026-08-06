<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Phase 1.1 — Model relasi user → role (gw_sm__user_role).
 *
 * Tabel ini baru (tidak ada di skema legacy) dan menjadi dasar RoleFilter
 * (Phase 1.3): menentukan role apa saja yang dimiliki seorang user.
 */
class UserRoleModel extends Model
{
    protected $table            = 'gw_sm__user_role';
    protected $primaryKey       = 'user_role_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'user_role_user_id',
        'user_role_role_id',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Daftar role_id milik seorang user.
     */
    public function getRoleIdsByUser(int $userId): array
    {
        $rows = $this->db->table('gw_sm__user_role')
            ->select('user_role_role_id')
            ->where('user_role_user_id', $userId)
            ->get()
            ->getResultArray();

        return array_map(fn($r) => (int) $r['user_role_role_id'], $rows);
    }

    /**
     * Daftar role (dengan nama) milik seorang user.
     */
    public function getRolesByUser(int $userId): array
    {
        return $this->db->table('gw_sm__user_role ur')
            ->select('r.role_id, r.role_name_idn, r.role_name_eng, r.role_active')
            ->join('gw_sm__role r', 'r.role_id = ur.user_role_role_id')
            ->where('ur.user_role_user_id', $userId)
            ->orderBy('r.role_id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ganti seluruh role milik user (mode replace).
     */
    public function saveUserRoles(int $userId, array $roleIds): array
    {
        $roleIds = array_values(array_unique(array_filter(array_map('intval', $roleIds))));

        $this->db->transStart();

        $this->db->table('gw_sm__user_role')->where('user_role_user_id', $userId)->delete();

        foreach ($roleIds as $roleId) {
            $this->db->table('gw_sm__user_role')->insert([
                'user_role_user_id'    => $userId,
                'user_role_role_id'    => $roleId,
                'user_role_created_on' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menyimpan role user.'];
        }

        return [
            'success' => true,
            'message' => 'Role user berhasil diperbarui (' . count($roleIds) . ' role).',
        ];
    }
}
