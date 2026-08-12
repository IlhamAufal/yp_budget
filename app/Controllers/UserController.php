<?php

namespace App\Controllers;

use App\Libraries\AuditLog;
use App\Libraries\AuthorizationService;
use App\Models\MenuModel;
use App\Models\RoleModel;
use App\Models\UserMenuModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * System Administration: User Management.
 *
 * Mengelola akun user, role legacy (gw_sm__profile), dan override access
 * menu/feature per user (gw_sm__usermenu).
 */
class UserController extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $userRoleModel;
    protected $menuModel;
    protected $userMenuModel;

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->roleModel     = new RoleModel();
        $this->userRoleModel = new UserRoleModel();
        $this->menuModel     = new MenuModel();
        $this->userMenuModel = new UserMenuModel();
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
     */
    public function formModal()
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to(base_url('sys-admin/user'));
        }

        $allRoles = $this->roleModel->getAll(['status' => 'Y']);
        $roles = array_values(array_filter(
            $allRoles,
            static fn(array $role): bool => ($role['role_type'] ?? 'menu') === 'menu'
        ));
        $rolesObj = array_values(array_filter(
            $allRoles,
            static fn(array $role): bool => ($role['role_type'] ?? 'menu') === 'object'
        ));

        $roles = array_map(static fn(array $role): array => [
            'role_id'       => (int) $role['role_id'],
            'role_name_idn' => $role['role_name_idn'],
        ], $roles);
        $rolesObj = array_map(static fn(array $role): array => [
            'role_id'       => (int) $role['role_id'],
            'role_name_idn' => $role['role_name_idn'],
        ], $rolesObj);

        $menuService = new AuthorizationService();
        $roleBaselines = [];
        foreach ($roles as $role) {
            $roleBaselines[(int) $role['role_id']] = $menuService->getBaselineMenuIdsForRole((int) $role['role_id']);
        }

        $user = null;
        $id   = (int) $this->request->getGet('id');
        if ($id > 0) {
            foreach ($this->userModel->getAllUsers([]) as $candidate) {
                if ((int) $candidate['user_id'] !== $id) {
                    continue;
                }

                $candidate['role_ids']     = array_filter(explode(',', $candidate['menu_role_ids'] ?? ''));
                $candidate['obj_role_ids'] = array_filter(explode(',', $candidate['obj_role_ids'] ?? ''));
                $menuAccess = $menuService->getMenuAccessState($id);
                $candidate['main_menu_role_id']      = $menuAccess['main_menu_role_id'];
                if ($candidate['main_menu_role_id'] !== null) {
                    $candidate['role_ids'] = array_values(array_unique(array_merge(
                        [(string) $candidate['main_menu_role_id']],
                        $candidate['role_ids']
                    )));
                }
                $candidate['baseline_menu_ids']      = $menuAccess['baseline_menu_ids'];
                $candidate['override_map']           = $menuAccess['override_map'];
                $candidate['effective_menu_ids']     = $menuAccess['effective_menu_ids'] ?? [];
                // Kept as a compatibility flag for older modal consumers; the
                // form now always edits the final effective checkbox state.
                $candidate['has_custom_menu_access'] = true;
                $candidate['custom_menu_ids']        = $candidate['effective_menu_ids'];
                $user = $candidate;
                break;
            }
        }

        return view('sys-admin/user-form', [
            'roles'    => $roles,
            'rolesObj' => $rolesObj,
            'roleBaselines' => $roleBaselines,
            'menus'    => $this->menuModel->getAll(['status' => 'Y']),
            'user'     => $user,
            'baseUrl'  => base_url(),
        ]);
    }

    public function save(): ResponseInterface
    {
        $data                = $this->request->getPost();
        $id                  = ! empty($data['id']) ? (int) $data['id'] : null;
        $hasRoleUpdate       = $this->request->getPost('has_role_ids') !== null;
        $hasCustomMenuUpdate = $this->request->getPost('has_custom_menu_access') !== null;
        $menuRoleIds         = array_values(array_unique(array_filter(array_map('intval', (array) $this->request->getPost('menu_role_ids')))));
        $objectRoleIds       = array_values(array_unique(array_filter(array_map('intval', (array) $this->request->getPost('object_role_ids')))));
        $roleIds             = array_values(array_unique(array_merge($menuRoleIds, $objectRoleIds)));

        if ($hasRoleUpdate) {
            $assignment = $this->userRoleModel->validateAssignments($menuRoleIds, $objectRoleIds);
            if (! $assignment['success']) {
                return $this->jsonResult($assignment, 'sys-admin/user/save');
            }
        }

        // Validasi hanya memastikan role yang dikirim benar-benar aktif dan
        // bertipe sesuai. User tanpa menu role tetap valid bila checkbox akhir
        // menyimpan minimal satu override Y.
        if ($hasRoleUpdate) {
            $activeRoles = $this->roleModel->getAll(['status' => 'Y']);
            $rolesById   = [];
            foreach ($activeRoles as $role) {
                $rolesById[(int) $role['role_id']] = $role;
            }

            foreach ($roleIds as $roleId) {
                if (! isset($rolesById[$roleId])) {
                    return $this->jsonResult([
                        'success' => false,
                        'message' => 'Role yang dipilih tidak tersedia atau tidak aktif.',
                    ], 'sys-admin/user/save');
                }
            }

        }

        // Hindari perubahan user/role parsial bila migration akses khusus belum diterapkan.
        if ($hasCustomMenuUpdate && ! $this->userMenuModel->isAvailable()) {
            return $this->jsonResult([
                'success' => false,
                'message' => 'Tabel akses khusus user belum tersedia. Jalankan migration database terlebih dahulu.',
            ], 'sys-admin/user/save');
        }

        // Semua model memakai koneksi yang sama; transaction ini menjadi satu-satunya boundary commit.
        $db = \Config\Database::connect();
        $db->transStart();

        $result = $this->userModel->saveUser($data, $id);
        if (! $result['success'] || empty($result['id'])) {
            $db->transRollback();
            return $this->jsonResult($result, 'sys-admin/user/save');
        }

        $userId = (int) $result['id'];
        if ($hasRoleUpdate) {
            $roleResult = $this->userRoleModel->saveUserRoles($userId, $roleIds);
            if (! $roleResult['success']) {
                $db->transRollback();
                return $this->jsonResult($roleResult, 'sys-admin/user/save');
            }
        }

        if ($hasCustomMenuUpdate) {
            $menuAccess = (new AuthorizationService())->getMenuAccessState($userId);
            $selectedMenuIds = (array) $this->request->getPost('menu_ids');
            $menuResult = $this->userMenuModel->saveConfiguration(
                $userId,
                (array) ($menuAccess['baseline_menu_ids'] ?? []),
                $selectedMenuIds
            );
            if (! $menuResult['success']) {
                $db->transRollback();
                return $this->jsonResult($menuResult, 'sys-admin/user/save');
            }
            $result['message'] = $menuResult['message'];
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            return $this->jsonResult([
                'success' => false,
                'message' => 'Gagal menyimpan user beserta konfigurasi aksesnya.',
            ], 'sys-admin/user/save');
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

    /** Simpan role milik user (mode replace). */
    public function saveRoles(): ResponseInterface
    {
        $userId = (int) $this->request->getPost('user_id');
        $user = $this->userModel->find($userId);
        if (! $user) {
            return $this->jsonResult(['success' => false, 'message' => 'User tidak ditemukan.']);
        }

        $menuRoleIds = (array) $this->request->getPost('menu_role_ids');
        $objectRoleIds = (array) $this->request->getPost('object_role_ids');
        $assignment = $this->userRoleModel->validateAssignments($menuRoleIds, $objectRoleIds);
        if (! $assignment['success']) {
            return $this->jsonResult($assignment, 'sys-admin/user/saveRoles');
        }

        $db = \Config\Database::connect();
        $db->transStart();
        $result = $this->userRoleModel->saveUserRoles($userId, array_merge(
            $assignment['menu_role_ids'],
            $assignment['object_role_ids']
        ));
        $db->transComplete();
        if ($db->transStatus() === false) {
            return $this->jsonResult(['success' => false, 'message' => 'Gagal menyimpan role user.'], 'sys-admin/user/saveRoles');
        }

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
