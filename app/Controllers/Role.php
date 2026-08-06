<?php

namespace App\Controllers;

use App\Models\MenuModel;
use App\Models\RoleModel;
use App\Libraries\AuditLog;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Phase 1.1 — System Administration: Role Management.
 *
 * Mengelola role (gw_sm__role) beserta permission menu-nya
 * (gw_sm__rolemenu). Dasar RBAC untuk RoleFilter (Phase 1.3).
 */
class Role extends BaseController
{
    protected $roleModel;
    protected $menuModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->menuModel = new MenuModel();
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search') ?? '',
            'status' => $this->request->getGet('status') ?? '',
        ];

        return view('sys-admin/role', [
            'title'      => 'System Administration - Role Management',
            'rows'       => $this->roleModel->getAll($filters),
            'menus'      => $this->menuModel->getAll(['status' => 'Y']),
            'filters'    => $filters,
            'has_filter' => ! empty(array_filter($this->request->getGet())),
            'flash'      => $this->consumeFlash(),
        ]);
    }

    public function save(): ResponseInterface
    {
        $data = $this->request->getPost();
        $id   = ! empty($data['id']) ? (int) $data['id'] : null;
        $result = $this->roleModel->saveRole($data, $id);

        return $this->jsonResult($result, 'sys-admin/role/save');
    }

    public function toggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->roleModel->toggleRole($id);

        return $this->jsonResult($result, 'sys-admin/role/toggle');
    }

    public function delete(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->roleModel->deleteRole($id);

        return $this->jsonResult($result, 'sys-admin/role/delete');
    }

    /**
     * Ambil daftar menu_id yang diizinkan sebuah role (untuk modal permission).
     */
    public function menus(): ResponseInterface
    {
        $roleId = (int) $this->request->getPost('role_id');

        $role = $this->roleModel->find($roleId);
        if (! $role) {
            return $this->jsonResult(['success' => false, 'message' => 'Role tidak ditemukan.']);
        }

        return $this->response->setJSON([
            'success'  => true,
            'menu_ids' => $this->roleModel->getMenuIdsByRole($roleId),
        ]);
    }

    /**
     * Simpan permission menu sebuah role (mode replace).
     */
    public function saveMenus(): ResponseInterface
    {
        $roleId  = (int) $this->request->getPost('role_id');
        $menuIds = (array) $this->request->getPost('menu_ids');

        $role = $this->roleModel->find($roleId);
        if (! $role) {
            return $this->jsonResult(['success' => false, 'message' => 'Role tidak ditemukan.']);
        }

        $result = $this->roleModel->saveRoleMenus($roleId, $menuIds);

        return $this->jsonResult($result, 'sys-admin/role/saveMenus');
    }

    private function jsonResult(array $result, string $action = 'sys-admin/role'): ResponseInterface
    {
        if ($result['success']) {
            session()->setFlashdata('sysadmin_msg', $result['message']);
            AuditLog::saved($action, 'Role config: ' . $result['message']);
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
