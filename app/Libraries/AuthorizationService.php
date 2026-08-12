<?php

namespace App\Libraries;

use App\Models\UserMenuModel;
use CodeIgniter\Database\BaseConnection;

/**
 * Resolves legacy role, module, and menu access from the database.
 *
 * Session values written here are compatibility snapshots only. Callers that
 * enforce access should refresh this service before making a decision.
 */
class AuthorizationService
{
    protected $db;
    protected $userMenuModel;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?: \Config\Database::connect();
        $this->userMenuModel = new UserMenuModel();
    }

    /**
     * @return array<string, mixed>
     */
    public function refresh(?int $userId = null): array
    {
        $userId = $userId ?: (int) session()->get('user_id');
        if ($userId <= 0) {
            throw new \RuntimeException('Authorization user id is missing.');
        }

        $context = $this->buildContext($userId);
        $this->writeSessionSnapshot($context);

        return $context;
    }

    /** @return int[] */
    public function getBaselineMenuIdsForRole(int $roleId): array
    {
        return $roleId > 0 ? $this->getBaselineMenuIds($roleId) : [];
    }

    /**
     * Resolve a form's checkbox state without changing the current session.
     *
     * @return array<string, mixed>
     */
    public function getMenuAccessState(int $userId): array
    {
        $context = $this->buildContext($userId);

        return [
            'main_menu_role_id' => $context['main_menu_role_id'],
            'baseline_menu_ids' => $context['baseline_menu_ids'],
            'override_map'      => $context['override_map'],
            'effective_menu_ids'=> $context['effective_menu_ids'],
        ];
    }

    /**
     * Exact legacy request check. A URL absent from active menu is allowed
     * after module access, matching the old guard's compatibility behavior.
     */
    public function authorizeRequest(string $method, string $uri, ?array $context = null): bool
    {
        $context = $context ?: $this->refresh();
        if (($context['is_admin'] ?? false) === true) {
            return true;
        }
        if (($context['module_access_allowed'] ?? false) !== true) {
            return false;
        }

        // Do not normalize or remove a trailing slash: legacy matching is an
        // exact menu_link lookup. The HTTP method is intentionally irrelevant
        // because the legacy table stores one link per menu, not per method.
        $uri = ltrim($uri, '/');
        $menu = $this->db->table('gw_sm__menu')
            ->select('menu_id, menu_module_code')
            ->where('menu_link', $uri)
            ->where('menu_active', 'Y')
            ->get()
            ->getRowArray();

        if (! $menu) {
            return true;
        }

        $menuModule = strtoupper(trim((string) ($menu['menu_module_code'] ?? '')));
        $activeModule = strtoupper((string) ($context['active_module_code'] ?? ''));
        if ($menuModule !== '' && $activeModule !== '' && $menuModule !== $activeModule) {
            return false;
        }

        return in_array((int) $menu['menu_id'], (array) ($context['effective_menu_ids'] ?? []), true);
    }

    /**
     * Compatibility helper retained for callers that already have a context.
     */
    public function canAccessCanonical(array $context, string $canonical): bool
    {
        if (($context['is_admin'] ?? false) === true) {
            return true;
        }
        return in_array($canonical, (array) ($context['effective_menu_links'] ?? []), true);
    }

    public function getAllowedMenuIds(): ?array
    {
        if (! session()->get('user_logged_in')) {
            return [];
        }
        try {
            $context = $this->refresh((int) session()->get('user_id'));
        } catch (\Throwable $e) {
            log_message('error', 'Unable to refresh allowed menu ids: ' . $e->getMessage());
            return [];
        }
        if ($context['is_admin']) {
            return null;
        }
        return array_values(array_map('intval', (array) ($context['effective_module_menu_ids'] ?? [])));
    }

    /**
     * Build one role-plus-override context.
     *
     * @return array<string, mixed>
     */
    private function buildContext(int $userId): array
    {
        $user = $this->db->table('gw_sm__user')
            ->where('user_id', $userId)
            ->get()->getRowArray();
        if (! $user || ! $this->isActive($user) || $this->isBlocked($user)) {
            throw new \App\Exceptions\AuthorizationException('User is inactive, blocked, or deleted.');
        }

        $roles = $this->db->table('gw_sm__profile p')
            ->select('p.profile_id, r.role_id, r.role_type, r.role_active')
            ->join('gw_sm__role r', 'r.role_id = p.profile_role_id', 'inner')
            ->where('p.profile_user_id', $userId)
            ->where('r.role_active', 'Y')
            ->orderBy('p.profile_id', 'ASC')
            ->get()->getResultArray();

        $roleIds = [];
        $objectRoleIds = [];
        $mainMenuRoleId = null;
        foreach ($roles as $role) {
            $roleId = (int) $role['role_id'];
            $roleIds[] = $roleId;
            if (($role['role_type'] ?? '') === 'object') {
                $objectRoleIds[] = $roleId;
            }
            if ($mainMenuRoleId === null && ($role['role_type'] ?? '') === 'menu') {
                $mainMenuRoleId = $roleId;
            }
        }

        $isAdmin = strtoupper((string) ($user['user_admin'] ?? 'N')) === 'Y';
        $baseline = $isAdmin || $mainMenuRoleId === null
            ? []
            : $this->getBaselineMenuIds($mainMenuRoleId);
        $overrideMap = $this->userMenuModel->getOverrideMap($userId);
        $activeMenuIds = $this->getActiveMenuIds();
        $effective = $isAdmin ? null : $this->applyOverrides($baseline, $overrideMap, $activeMenuIds);

        $allowedModules = $isAdmin ? null : $this->getAllowedModuleCodes($user);
        $activeModule = $this->resolveActiveModule($allowedModules);
        $moduleAccessAllowed = $isAdmin
            || ($activeModule !== null && in_array($activeModule, (array) $allowedModules, true));
        $moduleMenuIds = $isAdmin || $activeModule === null
            ? null
            : $this->filterMenuIdsByModule((array) $effective, $activeModule);
        $effectiveLinks = $this->getExactLinks($effective);
        $authObj = $this->getObjectScope($userId, array_values(array_unique($objectRoleIds)));

        return [
            'user'                     => $user,
            'user_id'                  => $userId,
            'role_ids'                 => array_values(array_unique($roleIds)),
            'menu_role_ids'            => $mainMenuRoleId === null ? [] : [$mainMenuRoleId],
            'main_menu_role_id'        => $mainMenuRoleId,
            'object_role_ids'          => array_values(array_unique($objectRoleIds)),
            'role_id'                  => $mainMenuRoleId,
            'auth_obj'                 => $authObj,
            'is_admin'                 => $isAdmin,
            'baseline_menu_ids'        => $baseline,
            'override_map'             => $overrideMap,
            'effective_menu_ids'       => $effective,
            'effective_module_menu_ids'=> $moduleMenuIds,
            'effective_menu_links'     => $effectiveLinks,
            'allowed_module_codes'     => $allowedModules,
            'active_module_code'       => $activeModule,
            'module_access_allowed'    => $moduleAccessAllowed,
            'has_custom_menu_access'   => $overrideMap !== [],
        ];
    }

    /** @return int[] */
    private function getBaselineMenuIds(int $roleId): array
    {
        if ($roleId === 1) {
            $rows = $this->db->table('gw_sm__menu')
                ->select('menu_id')->where('menu_active', 'Y')->get()->getResultArray();
        } else {
            $rows = $this->db->table('gw_sm__rolemenu rm')
                ->select('rm.rolemenu_menu_id AS menu_id')
                ->join('gw_sm__role r', 'r.role_id = rm.rolemenu_role_id', 'inner')
                ->join('gw_sm__menu m', 'm.menu_id = rm.rolemenu_menu_id', 'inner')
                ->where('rm.rolemenu_role_id', $roleId)
                ->where('rm.rolemenu_active', 'Y')
                ->where('r.role_active', 'Y')
                ->where('r.role_type', 'menu')
                ->where('m.menu_active', 'Y')
                ->get()->getResultArray();
        }

        return array_values(array_unique(array_map(
            static fn(array $row): int => (int) ($row['menu_id'] ?? 0),
            $rows
        )));
    }

    /** @return int[] */
    private function getActiveMenuIds(): array
    {
        $rows = $this->db->table('gw_sm__menu')
            ->select('menu_id')
            ->where('menu_active', 'Y')
            ->get()->getResultArray();
        return array_values(array_unique(array_map(
            static fn(array $row): int => (int) ($row['menu_id'] ?? 0),
            $rows
        )));
    }

    /** @param int[] $baseline @param int[] $activeMenuIds @return int[] */
    private function applyOverrides(array $baseline, array $overrides, array $activeMenuIds): array
    {
        $activeSet = array_fill_keys($activeMenuIds, true);
        $effective = [];
        foreach ($baseline as $menuId) {
            if (isset($activeSet[(int) $menuId])) {
                $effective[(int) $menuId] = true;
            }
        }
        foreach ($overrides as $menuId => $status) {
            $menuId = (int) $menuId;
            if ($menuId <= 0 || ! isset($activeSet[$menuId])) {
                continue;
            }
            if ($status === 'Y') {
                $effective[$menuId] = true;
            } else {
                unset($effective[$menuId]);
            }
        }
        return array_values(array_map('intval', array_keys($effective)));
    }

    /** @return string[]|null */
    private function getAllowedModuleCodes(array $user): array
    {
        if (! $this->db->tableExists('gw_sm__module_access')) {
            return [];
        }
        $email = trim((string) ($user['user_email'] ?? ''));
        if ($email === '') {
            return [];
        }
        $rows = $this->db->table('gw_sm__module_access')
            ->select('gw_sm__access_module_code')
            ->where('gw_sm__access_email', $email)
            ->get()->getResultArray();
        $codes = [];
        foreach ($rows as $row) {
            $code = strtoupper(trim((string) ($row['gw_sm__access_module_code'] ?? '')));
            if ($code !== '') {
                $codes[] = $code;
            }
        }
        return array_values(array_unique($codes));
    }

    /** @param string[]|null $allowed */
    private function resolveActiveModule(?array $allowed): ?string
    {
        if ($allowed === null) {
            return null;
        }
        $sessionModule = session()->get('active_module_code')
            ?: session()->get('module_code')
            ?: session()->get('module');
        return strtoupper(trim((string) ($sessionModule ?: 'CBP')));
    }

    /** @param int[] $menuIds @return int[] */
    private function filterMenuIdsByModule(array $menuIds, string $moduleCode): array
    {
        if ($menuIds === []) {
            return [];
        }
        $rows = $this->db->table('gw_sm__menu')
            ->select('menu_id')
            ->whereIn('menu_id', $menuIds)
            ->groupStart()
                ->where('menu_module_code', $moduleCode)
                ->orWhere('menu_module_code IS NULL', null, false)
                ->orWhere('menu_module_code', '')
            ->groupEnd()
            ->where('menu_active', 'Y')
            ->get()->getResultArray();
        return array_values(array_unique(array_map(
            static fn(array $row): int => (int) $row['menu_id'],
            $rows
        )));
    }

    /** @return string[] */
    private function getExactLinks(?array $menuIds): array
    {
        $query = $this->db->table('gw_sm__menu')
            ->select('menu_link')->where('menu_active', 'Y');
        if ($menuIds !== null) {
            if ($menuIds === []) {
                return [];
            }
            $query->whereIn('menu_id', $menuIds);
        }
        return array_values(array_unique(array_filter(array_map(
            static fn(array $row): string => (string) ($row['menu_link'] ?? ''),
            $query->get()->getResultArray(),
        ), static fn(string $link): bool => $link !== '' && $link !== '#')));
    }

    /** @return array<int, array<string, string>> */
    private function getObjectScope(int $userId, array $objectRoleIds): array
    {
        if ($objectRoleIds === [] || ! $this->db->tableExists('gw_sm__role_object')) {
            return [];
        }
        $rows = $this->db->table('gw_sm__role_object ro')
            ->select('ro.role_object_value, s.setting_code')
            ->join('gw_sm__role r', 'r.role_id = ro.role_object_role_id', 'inner')
            ->join('gw_sm__profile p', 'p.profile_role_id = r.role_id', 'inner')
            ->join('gw_sm__setting s', 's.setting_id = ro.role_object_setting_id', 'left')
            ->where('p.profile_user_id', $userId)
            ->whereIn('r.role_id', $objectRoleIds)
            ->where('r.role_active', 'Y')
            ->where('r.role_type', 'object')
            ->where('ro.role_object_value IS NOT NULL', null, false)
            ->get()->getResultArray();

        $grouped = [];
        foreach ($rows as $row) {
            $key = $row['setting_code'] ?? 'OBJECT';
            $value = (string) ($row['role_object_value'] ?? '');
            if ($value !== '') {
                $grouped[$key][] = $value;
            }
        }
        $result = [];
        foreach ($grouped as $key => $values) {
            $values = array_values(array_unique(array_filter($values)));
            if ($values !== []) {
                $result[] = [
                    'setting_code' => $key,
                    'role_object_value' => "'" . implode("','", $values) . "'",
                ];
            }
        }
        return $result;
    }

    private function writeSessionSnapshot(array $context): void
    {
        session()->set([
            'user_logged_in'          => true,
            'user_id'                 => $context['user_id'],
            'user_username'           => $context['user']['user_username'] ?? '',
            'user_email'              => $context['user']['user_email'] ?? '',
            'user_name'               => $context['user']['user_name'] ?? ($context['user']['user_username'] ?? ''),
            'role_ids'                => $context['role_ids'],
            'menu_role_ids'           => $context['menu_role_ids'],
            'main_menu_role_id'       => $context['main_menu_role_id'],
            'object_role_ids'         => $context['object_role_ids'],
            'role_id'                 => $context['role_id'],
            'auth_obj'                => $context['auth_obj'],
            'is_admin'                => $context['is_admin'],
            'baseline_menu_ids'       => $context['baseline_menu_ids'],
            'override_map'            => $context['override_map'],
            'effective_menu_ids'      => $context['effective_menu_ids'],
            'effective_module_menu_ids'=> $context['effective_module_menu_ids'],
            'effective_menu_links'    => $context['effective_menu_links'],
            'allowed_module_codes'    => $context['allowed_module_codes'],
            'active_module_code'      => $context['active_module_code'],
            'module_access_allowed'   => $context['module_access_allowed'],
            'has_custom_menu_access'  => $context['has_custom_menu_access'],
        ]);
    }

    private function isActive(array $user): bool
    {
        return in_array(strtoupper((string) ($user['user_active'] ?? 'N')), ['Y', '1', 'A', 'ACTIVE'], true);
    }

    private function isBlocked(array $user): bool
    {
        return strtoupper((string) ($user['user_block'] ?? 'N')) === 'Y' || ($user['user_block'] ?? 'N') == 1;
    }
}
