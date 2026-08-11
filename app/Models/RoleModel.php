<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Phase 1.1 — Model role & permission menu (gw_sm__role + gw_sm__rolemenu).
 *
 * Permission berbasis menu: sebuah role memiliki daftar menu_id yang
 * boleh diakses (gw_sm__rolemenu). RoleFilter (Phase 1.3) akan membaca
 * data ini untuk memvalidasi hak akses URL.
 */
class RoleModel extends Model
{
    protected $table            = 'gw_sm__role';
    protected $primaryKey       = 'role_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'role_name_idn',
        'role_name_eng',
        'role_name_jpn',
        'role_active',
        'role_type',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Daftar role dengan jumlah user & jumlah menu permission.
     */
    public function getAll(array $filters = []): array
    {
        try {
            $builder = $this->db->table('gw_sm__role r')
                ->select("r.*,
                    (SELECT COUNT(*) FROM gw_sm__rolemenu rm WHERE rm.rolemenu_role_id = r.role_id AND rm.rolemenu_active = 'Y') AS menu_count,
                    (SELECT COUNT(*) FROM gw_sm__profile p WHERE p.profile_role_id = r.role_id) AS user_count");

            $search = trim($filters['search'] ?? '');
            if ($search !== '') {
                $builder->groupStart()
                    ->like('r.role_name_idn', $search)
                    ->orLike('r.role_name_eng', $search)
                ->groupEnd();
            }

            if (($filters['status'] ?? '') !== '') {
                $builder->where('r.role_active', $filters['status']);
            }

            if (! empty($filters['limit'])) {
                $builder->limit((int) $filters['limit'], (int) ($filters['offset'] ?? 0));
            }

            return $builder
                ->orderBy('r.role_id', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'RoleModel::getAll: ' . $e->getMessage());
            try {
                $fb = $this->db->table('gw_sm__role r')->select('r.*, 0 AS menu_count, 0 AS user_count');
                if (!empty($filters['status'])) $fb->where('r.role_active', $filters['status']);
                if (!empty($filters['limit'])) $fb->limit((int)$filters['limit'], (int)($filters['offset'] ?? 0));
                return $fb->orderBy('r.role_id', 'ASC')->get()->getResultArray();
            } catch (\Throwable $e2) {
                return [];
            }
        }
    }

    public function countAll(array $filters = []): int
    {
        $builder = $this->db->table('gw_sm__role r');

        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $builder->groupStart()
                ->like('r.role_name_idn', $search)
                ->orLike('r.role_name_eng', $search)
            ->groupEnd();
        }

        if (($filters['status'] ?? '') !== '') {
            $builder->where('r.role_active', $filters['status']);
        }

        return (int) $builder->countAllResults();
    }

    /**
     * Simpan role baru / update.
     *
     * @return array ['success' => bool, 'message' => string, 'id' => ?int]
     */
    public function saveRole(array $data, ?int $id = null): array
    {
        $nameIdn = trim($data['role_name_idn'] ?? '');
        if ($nameIdn === '') {
            return ['success' => false, 'message' => 'Nama role (Indonesia) wajib diisi.'];
        }

        // Cek duplikasi nama role
        $duplicate = $this->db->table('gw_sm__role')
            ->where('role_name_idn', $nameIdn)
            ->where('role_id !=', $id ?? 0)
            ->countAllResults();

        if ($duplicate > 0) {
            return ['success' => false, 'message' => 'Role dengan nama tersebut sudah ada.'];
        }

        $fields = [
            'role_name_idn' => $nameIdn,
            'role_name_eng' => trim($data['role_name_eng'] ?? '') !== '' ? trim($data['role_name_eng'] ?? '') : $nameIdn,
            'role_name_jpn' => trim($data['role_name_jpn'] ?? '') ?: '',
            'role_active'   => ($data['role_active'] ?? 'Y') === 'Y' ? 'Y' : 'N',
            'role_type'     => ($data['role_type'] ?? 'menu') === 'object' ? 'object' : 'menu',
        ];

        if ($id) {
            $fields['role_change_on'] = date('Y-m-d H:i:s');
            $fields['role_change_by'] = (string) session()->get('user_username');
            $this->db->table('gw_sm__role')->where('role_id', $id)->update($fields);
        } else {
            $fields['role_created_on'] = date('Y-m-d H:i:s');
            $fields['role_created_by'] = (string) session()->get('user_username');
            $this->db->table('gw_sm__role')->insert($fields);
            $id = (int) $this->db->insertID();
        }

        return [
            'success' => true,
            'message' => $id ? 'Role berhasil diperbarui.' : 'Role baru berhasil ditambahkan.',
            'id'      => $id,
        ];
    }

    /**
     * Aktif / non-aktifkan role.
     */
    public function toggleRole(int $id): array
    {
        $role = $this->db->table('gw_sm__role')->where('role_id', $id)->get()->getRowArray();
        if (! $role) {
            return ['success' => false, 'message' => 'Role tidak ditemukan.'];
        }

        $newStatus = ($role['role_active'] === 'Y') ? 'N' : 'Y';
        $this->db->table('gw_sm__role')
            ->where('role_id', $id)
            ->update([
                'role_active'   => $newStatus,
                'role_change_on' => date('Y-m-d H:i:s'),
                'role_change_by' => (string) session()->get('user_username'),
            ]);

        return [
            'success' => true,
            'message' => $newStatus === 'Y' ? 'Role diaktifkan.' : 'Role dinonaktifkan.',
        ];
    }

    /**
     * Hapus role beserta permission & relasi user-nya.
     */
    public function deleteRole(int $id): array
    {
        $role = $this->db->table('gw_sm__role')->where('role_id', $id)->get()->getRowArray();
        if (! $role) {
            return ['success' => false, 'message' => 'Role tidak ditemukan.'];
        }

        $this->db->transStart();

        $this->db->table('gw_sm__rolemenu')->where('rolemenu_role_id', $id)->delete();
        $this->db->table('gw_sm__profile')->where('profile_role_id', $id)->delete();
        $this->db->table('gw_sm__role')->where('role_id', $id)->delete();

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menghapus role.'];
        }

        return ['success' => true, 'message' => 'Role berhasil dihapus.'];
    }

    /* ------------------------------------------------------------------
     * Permission Role → Menu (gw_sm__rolemenu)
     * ------------------------------------------------------------------ */

    /**
     * Daftar menu_id yang diizinkan untuk sebuah role.
     */
    public function getMenuIdsByRole(int $roleId): array
    {
        $rows = $this->db->table('gw_sm__rolemenu')
            ->select('rolemenu_menu_id')
            ->where('rolemenu_role_id', $roleId)
            ->where('rolemenu_active', 'Y')
            ->get()
            ->getResultArray();

        return array_map(fn($r) => (int) $r['rolemenu_menu_id'], $rows);
    }

    /**
     * Ganti seluruh permission menu sebuah role (mode replace).
     */
    public function saveRoleMenus(int $roleId, array $menuIds): array
    {
        $menuIds = array_values(array_unique(array_filter(array_map('intval', $menuIds))));

        $this->db->transStart();

        $this->db->table('gw_sm__rolemenu')->where('rolemenu_role_id', $roleId)->delete();

        foreach ($menuIds as $menuId) {
            $this->db->table('gw_sm__rolemenu')->insert([
                'rolemenu_role_id' => $roleId,
                'rolemenu_menu_id' => $menuId,
                'rolemenu_active'  => 'Y',
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menyimpan permission menu.'];
        }

        return [
            'success' => true,
            'message' => 'Permission menu untuk role berhasil diperbarui (' . count($menuIds) . ' menu).',
        ];
    }
}
