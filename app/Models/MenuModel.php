<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Phase 1.1 — Model menu aplikasi (tabel gw_sm__menu + gw_sm__menu_structure).
 *
 * Menyediakan CRUD menu beserta relasi parent-child. Data ini akan
 * dikonsumsi oleh MenuBuilder (Phase 1.2) untuk merender sidebar dinamis.
 */
class MenuModel extends Model
{
    protected $table            = 'gw_sm__menu';
    protected $primaryKey       = 'menu_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'menu_name_idn',
        'menu_name_eng',
        'menu_name_jpn',
        'menu_parrent',
        'menu_active',
        'menu_icon',
        'menu_link',
        'menu_level',
        'menu_order',
        'menu_lower_level',
        'menu_module_code',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Daftar menu dengan info parent (untuk tabel kelola menu).
     */
    public function getAll(array $filters = []): array
    {
        try {
            $builder = $this->db->table('gw_sm__menu m')
                ->select('m.*, p.menu_name_idn AS parent_name, p.menu_id AS parent_id')
                ->join('gw_sm__menu_structure s', 's.structure_child_menu_id = m.menu_id', 'left')
                ->join('gw_sm__menu p', 'p.menu_id = s.structure_menu_id', 'left');

        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $builder->groupStart()
                ->like('m.menu_name_idn', $search)
                ->orLike('m.menu_name_eng', $search)
                ->orLike('m.menu_link', $search)
            ->groupEnd();
        }

        if (($filters['status'] ?? '') !== '') {
            $builder->where('m.menu_active', $filters['status']);
        }

        // Kolom menu_group tidak ada di skema legacy — urutkan per level & order.
        if (! empty($filters['limit'])) {
            $builder->limit((int) $filters['limit'], (int) ($filters['offset'] ?? 0));
        }

        return $builder
            ->orderBy('m.menu_level', 'ASC')
            ->orderBy('m.menu_order', 'ASC')
            ->orderBy('m.menu_id', 'ASC')
            ->get()
            ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'MenuModel::getAll: ' . $e->getMessage());
            // Fallback: query tanpa join structure
            try {
                $fb = $this->db->table('gw_sm__menu m')->select('m.*, NULL AS parent_name, NULL AS parent_id');
                if (!empty($filters['search'])) {
                    $fb->groupStart()->like('m.menu_name_idn', $filters['search'])->orLike('m.menu_link', $filters['search'])->groupEnd();
                }
                if (!empty($filters['status'])) $fb->where('m.menu_active', $filters['status']);
                if (!empty($filters['limit'])) $fb->limit((int)$filters['limit'], (int)($filters['offset'] ?? 0));
                return $fb->orderBy('m.menu_level', 'ASC')->orderBy('m.menu_order', 'ASC')->get()->getResultArray();
            } catch (\Throwable $e2) {
                return [];
            }
        }
    }

    public function countAll(array $filters = []): int
    {
        try {
            $builder = $this->db->table('gw_sm__menu m')
                ->selectCount('DISTINCT m.menu_id', 'c')
                ->join('gw_sm__menu_structure s', 's.structure_child_menu_id = m.menu_id', 'left');

        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $builder->groupStart()
                ->like('m.menu_name_idn', $search)
                ->orLike('m.menu_name_eng', $search)
                ->orLike('m.menu_link', $search)
            ->groupEnd();
        }

        if (($filters['status'] ?? '') !== '') {
            $builder->where('m.menu_active', $filters['status']);
        }

        $row = $builder->get()->getRowArray();
        return (int) ($row['c'] ?? 0);
        } catch (\Throwable $e) {
            log_message('error', 'MenuModel::countAll: ' . $e->getMessage());
            try {
                return (int) $this->db->table('gw_sm__menu')->countAllResults();
            } catch (\Throwable $e2) {
                return 0;
            }
        }
    }

    /**
     * Grup menu unik (untuk filter & dropdown).
     *
     * Kolom menu_group tidak tersedia di skema legacy → kembalikan array kosong
     * (fitur grup menu di-nonaktifkan sementara).
     */
    public function getGroups(): array
    {
        return [];
    }

    /**
     * Menu level 1 (calon parent) — untuk dropdown pilihan parent.
     */
    public function getParents(): array
    {
        return $this->db->table('gw_sm__menu')
            ->where('menu_level', '1')
            ->orderBy('menu_order', 'ASC')
            ->orderBy('menu_id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Simpan menu baru / update. Opsional tautkan ke parent via menu_structure.
     *
     * @return array ['success' => bool, 'message' => string, 'id' => ?int]
     */
    public function saveMenu(array $data, ?int $id = null): array
    {
        $nameIdn = trim($data['menu_name_idn'] ?? '');
        if ($nameIdn === '') {
            return ['success' => false, 'message' => 'Nama menu (Indonesia) wajib diisi.'];
        }

        $this->db->transStart();

        $fields = [
            'menu_name_idn' => $nameIdn,
            'menu_name_eng' => trim($data['menu_name_eng'] ?? '') !== '' ? trim($data['menu_name_eng'] ?? '') : $nameIdn,
            'menu_name_jpn' => trim($data['menu_name_jpn'] ?? '') ?: '',
            'menu_active'   => ($data['menu_active'] ?? 'Y') === 'Y' ? 'Y' : 'N',
            'menu_icon'     => trim($data['menu_icon'] ?? '') ?: '',
            'menu_link'     => trim($data['menu_link'] ?? '#') ?: '#',
            'menu_level'    => ($data['menu_level'] ?? '1') === '2' ? '2' : '1',
            'menu_order'    => trim($data['menu_order'] ?? '') !== '' ? trim($data['menu_order'] ?? '') : '0',
        ];

        // Auto-flag menu yang punya anak
        $fields['menu_parrent'] = ($fields['menu_level'] === '1' && ! empty($data['has_children'])) ? 'Y' : 'N';

        if ($id) {
            $fields['menu_change_on'] = date('Y-m-d H:i:s');
            $fields['menu_change_by'] = (string) session()->get('user_username');
            $this->db->table('gw_sm__menu')->where('menu_id', $id)->update($fields);
        } else {
            $fields['menu_created_on'] = date('Y-m-d H:i:s');
            $fields['menu_created_by'] = (string) session()->get('user_username');
            $this->db->table('gw_sm__menu')->insert($fields);
            $id = (int) $this->db->insertID();
        }

        // Sinkronkan relasi parent-child bila parent dipilih
        $parentId = ! empty($data['parent_id']) ? (int) $data['parent_id'] : 0;

        $this->db->table('gw_sm__menu_structure')
            ->where('structure_child_menu_id', $id)
            ->delete();

        if ($parentId > 0 && $parentId !== $id) {
            $this->db->table('gw_sm__menu_structure')->insert([
                'structure_menu_id'       => $parentId,
                'structure_child_menu_id' => $id,
                'structure_created_on'    => date('Y-m-d H:i:s'),
                'structure_created_by'    => (string) session()->get('user_username'),
            ]);

            $this->db->table('gw_sm__menu')
                ->where('menu_id', $parentId)
                ->update(['menu_parrent' => 'Y']);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menyimpan menu.'];
        }

        return [
            'success' => true,
            'message' => $id ? 'Menu berhasil diperbarui.' : 'Menu baru berhasil ditambahkan.',
            'id'      => $id,
        ];
    }

    /**
     * Aktif / non-aktifkan menu.
     */
    public function toggleMenu(int $id): array
    {
        $menu = $this->db->table('gw_sm__menu')->where('menu_id', $id)->get()->getRowArray();
        if (! $menu) {
            return ['success' => false, 'message' => 'Menu tidak ditemukan.'];
        }

        $newStatus = ($menu['menu_active'] === 'Y') ? 'N' : 'Y';
        $this->db->table('gw_sm__menu')
            ->where('menu_id', $id)
            ->update([
                'menu_active'  => $newStatus,
                'menu_change_on' => date('Y-m-d H:i:s'),
                'menu_change_by' => (string) session()->get('user_username'),
            ]);

        return [
            'success' => true,
            'message' => $newStatus === 'Y' ? 'Menu diaktifkan.' : 'Menu dinonaktifkan.',
        ];
    }

    /**
     * Hapus menu beserta relasi struktur & permission role-nya.
     */
    public function deleteMenu(int $id): array
    {
        $menu = $this->db->table('gw_sm__menu')->where('menu_id', $id)->get()->getRowArray();
        if (! $menu) {
            return ['success' => false, 'message' => 'Menu tidak ditemukan.'];
        }

        // Cegah penghapusan parent yang masih punya anak
        $childCount = $this->db->table('gw_sm__menu_structure')
            ->where('structure_menu_id', $id)
            ->countAllResults();

        if ($childCount > 0) {
            return ['success' => false, 'message' => 'Menu ini memiliki sub-menu. Hapus sub-menu terlebih dahulu.'];
        }

        $this->db->transStart();

        $this->db->table('gw_sm__menu_structure')
            ->groupStart()
                ->where('structure_menu_id', $id)
                ->orWhere('structure_child_menu_id', $id)
            ->groupEnd()
            ->delete();

        $this->db->table('gw_sm__rolemenu')
            ->where('rolemenu_menu_id', $id)
            ->delete();

        $this->db->table('gw_sm__menu')->where('menu_id', $id)->delete();

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal menghapus menu.'];
        }

        return ['success' => true, 'message' => 'Menu berhasil dihapus.'];
    }
}
