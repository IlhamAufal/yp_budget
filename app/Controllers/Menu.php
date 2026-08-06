<?php

namespace App\Controllers;

use App\Models\MenuModel;
use App\Libraries\AuditLog;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Phase 1.1 — System Administration: Menu Configuration.
 *
 * Mengelola master menu (gw_sm__menu) yang nantinya dirender dinamis
 * oleh MenuBuilder (Phase 1.2).
 */
class Menu extends BaseController
{
    protected $menuModel;

    public function __construct()
    {
        $this->menuModel = new MenuModel();
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search') ?? '',
            'group'  => $this->request->getGet('group') ?? '',
            'status' => $this->request->getGet('status') ?? '',
        ];

        return view('sys-admin/menu', [
            'title'   => 'System Administration - Menu Configuration',
            'rows'    => $this->menuModel->getAll($filters),
            'groups'  => $this->menuModel->getGroups(),
            'parents' => $this->menuModel->getParents(),
            'filters' => $filters,
            'flash'   => $this->consumeFlash(),
        ]);
    }

    public function save(): ResponseInterface
    {
        $data = $this->request->getPost();
        $id   = ! empty($data['id']) ? (int) $data['id'] : null;
        $result = $this->menuModel->saveMenu($data, $id);

        return $this->jsonResult($result, 'sys-admin/menu/save');
    }

    public function toggle(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->menuModel->toggleMenu($id);

        return $this->jsonResult($result, 'sys-admin/menu/toggle');
    }

    public function delete(): ResponseInterface
    {
        $id     = (int) $this->request->getPost('id');
        $result = $this->menuModel->deleteMenu($id);

        return $this->jsonResult($result, 'sys-admin/menu/delete');
    }

    private function jsonResult(array $result, string $action = 'sys-admin/menu'): ResponseInterface
    {
        if ($result['success']) {
            session()->setFlashdata('sysadmin_msg', $result['message']);
            AuditLog::saved($action, 'Menu config: ' . $result['message']);
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
