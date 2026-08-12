<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Sparse per-user menu overrides backed by gw_sm__usermenu.
 *
 * The role menu is the baseline. This table only stores differences:
 * Y grants a menu absent from the baseline and N revokes a baseline menu.
 * With no row, the user inherits the selected role completely.
 */
class UserMenuModel extends Model
{
    protected $table            = 'gw_sm__usermenu';
    protected $primaryKey       = 'usermenu_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'usermenu_user_id',
        'usermenu_menu_id',
        'usermenu_active',
        'usermenu_created_on',
        'usermenu_change_on',
        'usermenu_created_by',
        'usermenu_change_by',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Return the persisted override map, including explicit N rows.
     *
     * @return array{is_custom: bool, menu_ids: int[], overrides: array<int, string>}
     */
    public function getConfiguration(int $userId): array
    {
        $overrides = $this->getOverrideMap($userId);

        return [
            'is_custom' => $overrides !== [],
            'menu_ids'  => array_values(array_keys(array_filter(
                $overrides,
                static fn(string $status): bool => $status === 'Y'
            ))),
            'overrides' => $overrides,
        ];
    }

    /**
     * Read all explicit Y/N overrides for one user.
     *
     * @return array<int, string>
     */
    public function getOverrideMap(int $userId): array
    {
        if ($userId <= 0 || ! $this->tableExists()) {
            return [];
        }

        $rows = $this->db->table($this->table)
            ->select('usermenu_menu_id, usermenu_active')
            ->where('usermenu_user_id', $userId)
            ->get()
            ->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $menuId = (int) ($row['usermenu_menu_id'] ?? 0);
            if ($menuId > 0) {
                $result[$menuId] = strtoupper((string) ($row['usermenu_active'] ?? 'N')) === 'Y' ? 'Y' : 'N';
            }
        }

        return $result;
    }

    /**
     * Save the final checkbox state as a sparse delta from the role baseline.
     *
     * @param int[] $baselineMenuIds Active menu ids inherited from the main role.
     * @param int[] $selectedMenuIds Final menu ids submitted by the form.
     */
    public function saveConfiguration(int $userId, array $baselineMenuIds, array $selectedMenuIds): array
    {
        if ($userId <= 0) {
            return ['success' => false, 'message' => 'User tidak valid.'];
        }
        if (! $this->tableExists()) {
            return ['success' => false, 'message' => 'Tabel akses khusus user belum tersedia. Jalankan migrasi database terlebih dahulu.'];
        }

        $baseline = $this->normalizeIds($baselineMenuIds);
        $selected = $this->normalizeIds($selectedMenuIds);
        $activeRows = $this->db->table('gw_sm__menu')
            ->select('menu_id')
            ->where('menu_active', 'Y')
            ->get()
            ->getResultArray();
        $activeIds = array_values(array_unique(array_map(
            static fn(array $row): int => (int) $row['menu_id'],
            $activeRows
        )));

        if (array_diff($selected, $activeIds) !== []) {
            return ['success' => false, 'message' => 'Menu yang dipilih tidak tersedia atau tidak aktif.'];
        }

        $selectedSet = array_fill_keys($selected, true);
        $deltaIds = array_values(array_unique(array_merge(
            array_diff($baseline, $selected),
            array_diff($selected, $baseline)
        )));
        $deltaIds = array_values(array_intersect($deltaIds, $activeIds));

        // Caller owns the transaction. This delete/insert pair is atomic when
        // called from UserController::save, and is also safe for direct use.
        $deleted = $this->db->table($this->table)
            ->where('usermenu_user_id', $userId)
            ->delete();
        if ($deleted === false) {
            return ['success' => false, 'message' => 'Gagal menghapus override akses user lama.'];
        }

        if ($deltaIds !== []) {
            $now   = date('Y-m-d H:i:s');
            $actor = (string) (session()->get('user_username') ?: 'system');
            $rows  = [];
            foreach ($deltaIds as $menuId) {
                $rows[] = [
                    'usermenu_user_id'    => $userId,
                    'usermenu_menu_id'    => $menuId,
                    'usermenu_active'     => isset($selectedSet[$menuId]) ? 'Y' : 'N',
                    'usermenu_created_on' => $now,
                    'usermenu_created_by' => $actor,
                ];
            }
            if ($this->db->table($this->table)->insertBatch($rows) === false) {
                return ['success' => false, 'message' => 'Gagal menyimpan override akses user.'];
            }
        }

        return [
            'success' => true,
            'message' => $deltaIds === []
                ? 'Akses user mengikuti baseline role; tidak ada override tersimpan.'
                : 'Override akses user berhasil disimpan (' . count($deltaIds) . ' perubahan).',
            'override_count' => count($deltaIds),
        ];
    }

    public function isAvailable(): bool
    {
        return $this->tableExists();
    }

    private function normalizeIds(array $ids): array
    {
        return array_values(array_unique(array_filter(
            array_map('intval', $ids),
            static fn(int $id): bool => $id > 0
        )));
    }

    private function tableExists(): bool
    {
        return $this->db->tableExists($this->table);
    }
}
