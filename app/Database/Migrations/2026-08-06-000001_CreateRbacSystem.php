<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Phase 1.1 — System Administration (RBAC).
 *
 * Membuat tabel RBAC mengikuti skema legacy (yp_budget_system.sql):
 *   - gw_sm__menu            : daftar menu aplikasi
 *   - gw_sm__menu_structure  : relasi parent-child menu
 *   - gw_sm__role            : daftar role
 *   - gw_sm__rolemenu        : permission role → menu
 *
 * Catatan (keputusan user 6 Agt 2026): DB di-copas dari sistem lama dan
 * dipakai apa adanya. Relasi user → role memakai tabel legacy
 * `gw_sm__profile` (bukan `gw_sm__user_role`) dan RBAC object-level
 * memakai `gw_sm__role_object` — keduanya TIDAK dibuat di sini (sudah
 * ada di skema legacy). Migrasi ini hanya menjamin tabel RBAC inti
 * (menu/structure/role/rolemenu) tersedia.
 *
 * Semua CREATE TABLE memakai IF NOT EXISTS agar aman bila tabel sudah
 * dibuat terlebih dahulu oleh migrasi impor SQL legacy.
 */
class CreateRbacSystem extends Migration
{
    public function up(): void
    {
        // ------------------------------------------------------------------
        // gw_sm__menu — master menu (struktur kolom mengikuti legacy)
        // ------------------------------------------------------------------
        $this->forge->addField([
            'menu_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'menu_name_idn' => [
                'type'       => 'VARCHAR',
                'constraint' => 400,
                'null'       => false,
            ],
            'menu_name_eng' => [
                'type'       => 'VARCHAR',
                'constraint' => 400,
                'null'       => false,
            ],
            'menu_name_jpn' => [
                'type'       => 'VARCHAR',
                'constraint' => 400,
                'null'       => false,
                'default'    => '',
            ],
            'menu_parrent' => [
                'type'       => "ENUM('Y','N')",
                'null'       => true,
                'default'    => 'N',
            ],
            'menu_active' => [
                'type'       => "ENUM('Y','N')",
                'null'       => true,
                'default'    => 'Y',
            ],
            'menu_icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'menu_link' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'default'    => '#',
            ],
            'menu_group' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => '',
            ],
            'menu_level' => [
                'type'       => "ENUM('1','2','3','4','5')",
                'null'       => true,
                'default'    => '1',
            ],
            'menu_order' => [
                'type'       => 'VARCHAR',
                'constraint' => 4,
                'null'       => true,
                'default'    => '0',
            ],
            'menu_lower_level' => [
                'type'       => "ENUM('Y','N')",
                'null'       => true,
                'default'    => 'N',
            ],
            'menu_module_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 4,
                'null'       => true,
            ],
            'menu_created_on' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'menu_change_on' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'menu_created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => 'system',
            ],
            'menu_change_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('menu_id', true);
        $this->forge->createTable('gw_sm__menu', true);

        // Pastikan kolom menu_group tersedia — tabel legacy (hasil impor SQL)
        // tidak memiliki kolom ini, sehingga CREATE TABLE IF NOT EXISTS di atas
        // tidak akan menambahkannya bila tabel sudah ada.
        $this->ensureColumn('gw_sm__menu', [
            'menu_group' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => '',
                'after'      => 'menu_link',
            ],
        ]);

        // ------------------------------------------------------------------
        // gw_sm__menu_structure — relasi parent ↔ child menu (mengikuti legacy)
        // ------------------------------------------------------------------
        $this->forge->addField([
            'structure_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'structure_menu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'structure_child_menu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'structure_created_on' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'structure_change_on' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'structure_created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => 'system',
            ],
            'structure_change_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('structure_id', true);
        $this->forge->addKey(['structure_menu_id', 'structure_child_menu_id']);
        $this->forge->createTable('gw_sm__menu_structure', true);

        // ------------------------------------------------------------------
        // gw_sm__role — master role (mengikuti legacy)
        // ------------------------------------------------------------------
        $this->forge->addField([
            'role_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role_name_idn' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => false,
            ],
            'role_name_eng' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => true,
                'default'    => '',
            ],
            'role_name_jpn' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => true,
                'default'    => '',
            ],
            'role_active' => [
                'type'       => "ENUM('Y','N')",
                'null'       => true,
                'default'    => 'Y',
            ],
            'role_type' => [
                'type'       => "ENUM('object','menu')",
                'null'       => true,
                'default'    => 'menu',
            ],
            'role_created_on' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'role_change_on' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'role_created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => 'system',
            ],
            'role_change_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('role_id', true);
        $this->forge->createTable('gw_sm__role', true);

        // ------------------------------------------------------------------
        // gw_sm__rolemenu — permission role → menu (mengikuti legacy)
        // ------------------------------------------------------------------
        $this->forge->addField([
            'rolemenu_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'rolemenu_role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'rolemenu_menu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'rolemenu_active' => [
                'type'       => "ENUM('Y','N')",
                'null'       => true,
                'default'    => 'Y',
            ],
        ]);
        $this->forge->addKey('rolemenu_id', true);
        $this->forge->addKey(['rolemenu_role_id', 'rolemenu_menu_id']);
        $this->forge->createTable('gw_sm__rolemenu', true);

        // ------------------------------------------------------------------
        // Relasi user → role memakai tabel legacy `gw_sm__profile` (TIDAK dibuat
        // di sini — sudah tersedia di skema legacy). RBAC object-level memakai
        // `gw_sm__role_object` + `gw_sm__setting` (legacy, tidak dibuat di sini).
        // ------------------------------------------------------------------
    }

    public function down(): void
    {
        $this->forge->dropTable('gw_sm__rolemenu', true);
        $this->forge->dropTable('gw_sm__role', true);
        $this->forge->dropTable('gw_sm__menu_structure', true);
        $this->forge->dropTable('gw_sm__menu', true);
    }

    /**
     * Tambahkan kolom hanya bila belum ada (aman untuk tabel legacy).
     */
    private function ensureColumn(string $table, array $fields): void
    {
        foreach ($fields as $fieldName => $definition) {
            $columns = $this->db->getFieldNames($table);
            if (! in_array($fieldName, $columns, true)) {
                $this->forge->addColumn($table, [$fieldName => $definition]);
            }
        }
    }
}
