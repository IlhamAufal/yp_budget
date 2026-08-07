<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model relasi user → role (gw_sm__profile).
 *
 * Keputusan user 6 Agt 2026: struktur RBAC mengikuti skema legacy — relasi
 * user↔role disimpan di tabel `gw_sm__profile` (profile_user_id →
 * profile_role_id), bukan tabel baru `gw_sm__user_role`. Model ini menjadi
 * dasar LoginController (load role) & RoleFilter (validasi hak akses).
 */
class UserRoleModel extends Model
{
    protected $table            = 'gw_sm__profile';
    protected $primaryKey       = 'profile_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'profile_user_id',
        'profile_role_id',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Daftar role_id milik seorang user (dari gw_sm__profile).
     */
    public function getRoleIdsByUser(int $userId): array
    {
        $rows = $this->db->table('gw_sm__profile p')
            ->select('p.profile_role_id')
            ->where('p.profile_user_id', $userId)
            ->get()
            ->getResultArray();

        return array_map(fn($r) => (int) $r['profile_role_id'], $rows);
    }

    /**
     * Daftar role (dengan nama) milik seorang user.
     */
    public function getRolesByUser(int $userId): array
    {
        return $this->db->table('gw_sm__profile p')
            ->select('r.role_id, r.role_name_idn, r.role_name_eng, r.role_active')
            ->join('gw_sm__role r', 'r.role_id = p.profile_role_id')
            ->where('p.profile_user_id', $userId)
            ->orderBy('r.role_id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ganti seluruh role milik user (mode replace) di gw_sm__profile.
     */
    public function saveUserRoles(int $userId, array $roleIds): array
    {
        $roleIds = array_values(array_unique(array_filter(array_map('intval', $roleIds))));

        $this->db->transStart();

        $this->db->table('gw_sm__profile')->where('profile_user_id', $userId)->delete();

        foreach ($roleIds as $roleId) {
            $this->db->table('gw_sm__profile')->insert([
                'profile_user_id'    => $userId,
                'profile_role_id'    => $roleId,
                'profile_created_on' => date('Y-m-d H:i:s'),
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

    /* ------------------------------------------------------------------
     * RBAC Object-Level (gw_sm__role_object) — Phase legacy
     * ------------------------------------------------------------------ */

    /**
     * Muat hak objek milik user dari gw_sm__role_object (via profile).
     *
     * Skema legacy: role object mengacu ke gw_sm__setting (COST_CENTER,
     * DIVISION, dll) dengan value kode objek. Dikembalikan dalam bentuk
     * yang sama dengan sesi legacy `auth_obj` agar controller existing
     * (mis. CapexController::getUserDeptList) tetap bekerja:
     *   [ ['role_object_value' => "'970','891',..."], ... ]
     *
     * @param string|null $settingCode filter setting (mis. 'COST_CENTER').
     */
    public function getRoleObjectsByUser(int $userId, ?string $settingCode = null): array
    {
        $builder = $this->db->table('gw_sm__profile p')
            ->select('ro.role_object_value, s.setting_code, s.setting_table')
            ->join('gw_sm__role_object ro', 'ro.role_object_role_id = p.profile_role_id', 'inner')
            ->join('gw_sm__setting s', 's.setting_id = ro.role_object_setting_id', 'left')
            ->where('p.profile_user_id', $userId)
            ->where('ro.role_object_value IS NOT NULL', null, false);

        if ($settingCode !== null) {
            $builder->where('s.setting_code', $settingCode);
        }

        $rows = $builder->get()->getResultArray();

        // Kelompokkan per setting_code agar auth_obj menyerupai struktur legacy.
        $grouped = [];
        foreach ($rows as $row) {
            $key = $row['setting_code'] ?? 'OBJECT';
            if (! isset($grouped[$key])) {
                $grouped[$key] = [];
            }
            $grouped[$key][] = $row['role_object_value'];
        }

        $authObj = [];
        foreach ($grouped as $values) {
            $values = array_unique(array_filter($values));
            if (empty($values)) {
                continue;
            }
            $authObj[] = [
                'role_object_value' => "'" . implode("','", array_map(fn($v) => (string) $v, $values)) . "'",
            ];
        }

        return $authObj;
    }
}
