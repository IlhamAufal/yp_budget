<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Libraries\AuditLog;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Phase 1.1 — System Administration: User Management.
 *
 * Mengelola user (gw_sm__user), penugasan role (gw_sm__user_role),
 * dan reset password. Role yang dipilih disimpan ke tabel relasi
 * yang akan dibaca RoleFilter (Phase 1.3).
 */
class User extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $userRoleModel;

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->roleModel     = new RoleModel();
        $this->userRoleModel = new UserRoleModel();
    }

    public function index()
    {
        $filters = [
            'search'  => $this->request->getGet('search') ?? '',
            'role_id' => $this->request->getGet('role_id') ?? '',
            'status'  => $this->request->getGet('status') ?? '',
        ];

        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 10;
        $filters['limit']  = $perPage;
        $filters['offset'] = ($page - 1) * $perPage;
        $total = $this->userModel->countUsers($filters);

        return view('sys-admin/user', [
            'title'      => 'System Administration - User Management',
            'rows'       => $this->userModel->getAllUsers($filters),
            'roles'      => $this->roleModel->getAll(['status' => 'Y']),
            'filters'    => $filters,
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $total,
            'has_filter' => ! empty(array_filter($this->request->getGet())),
            'flash'      => $this->consumeFlash(),
        ]);
    }

    /**
     * Form partial untuk Global Modal — Tambah / Edit User.
     *
     * Endpoint: GET /sys-admin/form?id=xxx (optional)
     * Hanya bisa diakses via AJAX. Mengembalikan partial view tanpa layout.
     *
     * @return string|ResponseInterface
     */
    public function formModal()
    {
        // Guard: hanya terima request AJAX
        if (! $this->request->isAJAX()) {
            return redirect()->to(base_url('sys-admin/user'));
        }

        $allRoles = $this->roleModel->getAll(['status' => 'Y']);

        // Pisahkan role menu vs object
        $roles    = array_values(array_filter($allRoles, fn($r) => ($r['role_type'] ?? 'menu') === 'menu'));
        $rolesObj = array_values(array_filter($allRoles, fn($r) => ($r['role_type'] ?? 'menu') === 'object'));

        // Format untuk frontend
        $roles    = array_map(fn($r) => ['role_id' => (int) $r['role_id'], 'role_name_idn' => $r['role_name_idn']], $roles);
        $rolesObj = array_map(fn($r) => ['role_id' => (int) $r['role_id'], 'role_name_idn' => $r['role_name_idn']], $rolesObj);

        // Jika edit mode (id parameter ada)
        $user = null;
        $id   = (int) $this->request->getGet('id');
        if ($id > 0) {
            $users = $this->userModel->getAllUsers([]);
            foreach ($users as $u) {
                if ((int) $u['user_id'] === $id) {
                    // Parse role_ids string ke array
                    $u['role_ids']     = array_filter(explode(',', $u['menu_role_ids'] ?? ''));
                    $u['obj_role_ids'] = array_filter(explode(',', $u['obj_role_ids'] ?? ''));
                    $user = $u;
                    break;
                }
            }
        }

        return view('sys-admin/user-form', [
            'roles'    => $roles,
            'rolesObj' => $rolesObj,
            'user'     => $user,
            'baseUrl'  => base_url(),
        ]);
    }

    public function save(): ResponseInterface
    {
        $data = $this->request->getPost();
        $id   = ! empty($data['id']) ? (int) $data['id'] : null;
        $result = $this->userModel->saveUser($data, $id);

        // Simpan role user (create & update) bila form mengirimkan penanda role_ids.
        // Penanda has_role_ids memungkinkan mengosongkan seluruh role sekaligus.
        if ($result['success'] && ! empty($result['id']) && $this->request->getPost('has_role_ids') !== null) {
            $roleIds = (array) $this->request->getPost('role_ids');
            $this->userRoleModel->saveUserRoles((int) $result['id'], $roleIds);
        }

        return $this->jsonResult($result, 'sys-admin/user/save');
    }

    public function toggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->userModel->toggleUser($id);

        return $this->jsonResult($result, 'sys-admin/user/toggle');
    }

    public function delete(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->userModel->deleteUser($id);

        return $this->jsonResult($result, 'sys-admin/user/delete');
    }

    /**
     * Simpan role milik user (mode replace).
     */
    public function saveRoles(): ResponseInterface
    {
        $userId  = (int) $this->request->getPost('user_id');
        $roleIds = (array) $this->request->getPost('role_ids');

        $user = $this->userModel->find($userId);
        if (! $user) {
            return $this->jsonResult(['success' => false, 'message' => 'User tidak ditemukan.']);
        }

        $result = $this->userRoleModel->saveUserRoles($userId, $roleIds);

        return $this->jsonResult($result, 'sys-admin/user/saveRoles');
    }

    public function resetPassword(): ResponseInterface
    {
        $id       = (int) $this->request->getPost('id');
        $password = (string) $this->request->getPost('user_password');
        $result   = $this->userModel->resetPassword($id, $password);

        return $this->jsonResult($result, 'sys-admin/user/resetPassword');
    }

    private function jsonResult(array $result, string $action = 'sys-admin/user'): ResponseInterface
    {
        if ($result['success']) {
            session()->setFlashdata('sysadmin_msg', $result['message']);
            AuditLog::saved($action, 'User management: ' . $result['message']);
        } else {
            session()->setFlashdata('sysadmin_err', $result['message']);
        }

        return $this->response->setJSON($result);
    }

    private function consumeFlash(): array
    {
        return [
            'success' => session()->getFlashdata('sysadmin_msg'),
            'error'   => session()->getFlashdata('sysadmin_err'),
        ];
    }
}
