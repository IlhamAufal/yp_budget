<?php

namespace App\Models;

use CodeIgniter\Model;

/** Legacy user-role relation backed by gw_sm__profile. */
class UserRoleModel extends Model
{
    protected $table = 'gw_sm__profile';
    protected $primaryKey = 'profile_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = false;
    protected $allowedFields = ['profile_user_id', 'profile_role_id'];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /** @return int[] active roles only */
    public function getRoleIdsByUser(int $userId, ?string $roleType = null): array
    {
        $builder = $this->db->table('gw_sm__profile p')
            ->select('p.profile_role_id')
            ->join('gw_sm__role r', 'r.role_id = p.profile_role_id', 'inner')
            ->where('p.profile_user_id', $userId)
            ->where('r.role_active', 'Y');
        if ($roleType !== null) {
            $builder->where('r.role_type', $roleType);
        }
        $rows = $builder->get()->getResultArray();
        return array_values(array_unique(array_map(static fn(array $row): int => (int) $row['profile_role_id'], $rows)));
    }

    public function getMenuRoleIdsByUser(int $userId): array
    {
        return $this->getRoleIdsByUser($userId, 'menu');
    }

    public function getObjectRoleIdsByUser(int $userId): array
    {
        return $this->getRoleIdsByUser($userId, 'object');
    }

    public function getRolesByUser(int $userId): array
    {
        return $this->db->table('gw_sm__profile p')
            ->select('r.role_id, r.role_name_idn, r.role_name_eng, r.role_active, r.role_type')
            ->join('gw_sm__role r', 'r.role_id = p.profile_role_id', 'inner')
            ->where('p.profile_user_id', $userId)
            ->where('r.role_active', 'Y')
            ->orderBy('r.role_id', 'ASC')
            ->get()->getResultArray();
    }

    /** Validate active role/type assignments before an outer transaction starts. */
    public function validateAssignments(array $menuRoleIds, array $objectRoleIds): array
    {
        $menuRoleIds = $this->normalizeIds($menuRoleIds);
        $objectRoleIds = $this->normalizeIds($objectRoleIds);
        $ids = array_values(array_unique(array_merge($menuRoleIds, $objectRoleIds)));
        if ($ids === []) {
            return ['success' => true, 'menu_role_ids' => [], 'object_role_ids' => []];
        }

        $rows = $this->db->table('gw_sm__role')
            ->select('role_id, role_type, role_active')
            ->whereIn('role_id', $ids)
            ->where('role_active', 'Y')
            ->get()->getResultArray();
        $valid = [];
        foreach ($rows as $row) {
            $valid[(int) $row['role_id']] = $row['role_type'];
        }
        foreach ($menuRoleIds as $id) {
            if (($valid[$id] ?? null) !== 'menu') {
                return ['success' => false, 'message' => 'Menu role tidak aktif atau bertipe tidak sesuai.'];
            }
        }
        foreach ($objectRoleIds as $id) {
            if (($valid[$id] ?? null) !== 'object') {
                return ['success' => false, 'message' => 'Object role tidak aktif atau bertipe tidak sesuai.'];
            }
        }
        if (count($ids) !== count($valid)) {
            return ['success' => false, 'message' => 'Role yang dipilih tidak tersedia atau tidak aktif.'];
        }
        return ['success' => true, 'menu_role_ids' => $menuRoleIds, 'object_role_ids' => $objectRoleIds];
    }

    /** Replace both role types. Caller owns the transaction. */
    public function saveUserRoles(int $userId, array $roleIds): array
    {
        $roleIds = $this->normalizeIds($roleIds);
        $this->db->table('gw_sm__profile')->where('profile_user_id', $userId)->delete();
        foreach ($roleIds as $roleId) {
            $this->db->table('gw_sm__profile')->insert([
                'profile_user_id' => $userId,
                'profile_role_id' => $roleId,
                'profile_created_on' => date('Y-m-d H:i:s'),
            ]);
        }
        return ['success' => true, 'message' => 'Role user berhasil diperbarui (' . count($roleIds) . ' role).'];
    }

    /** @return array<int, array<string, string>> */
    public function getRoleObjectsByUser(int $userId, ?string $settingCode = null): array
    {
        $builder = $this->db->table('gw_sm__profile p')
            ->select('ro.role_object_value, s.setting_code')
            ->join('gw_sm__role r', 'r.role_id = p.profile_role_id', 'inner')
            ->join('gw_sm__role_object ro', 'ro.role_object_role_id = r.role_id', 'inner')
            ->join('gw_sm__setting s', 's.setting_id = ro.role_object_setting_id', 'left')
            ->where('p.profile_user_id', $userId)
            ->where('r.role_active', 'Y')
            ->where('r.role_type', 'object')
            ->where('ro.role_object_value IS NOT NULL', null, false);
        if ($settingCode !== null) {
            $builder->where('s.setting_code', $settingCode);
        }
        $rows = $builder->get()->getResultArray();
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['setting_code'] ?? 'OBJECT'][] = $row['role_object_value'];
        }
        $result = [];
        foreach ($grouped as $values) {
            $values = array_values(array_unique(array_filter($values)));
            if ($values !== []) {
                $result[] = ['role_object_value' => "'" . implode("','", array_map('strval', $values)) . "'"];
            }
        }
        return $result;
    }

    private function normalizeIds(array $ids): array
    {
        return array_values(array_unique(array_filter(array_map('intval', $ids), static fn(int $id): bool => $id > 0)));
    }
}
