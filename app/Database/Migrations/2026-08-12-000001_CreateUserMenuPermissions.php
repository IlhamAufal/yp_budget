<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menyimpan override sparse permission menu per user.
 *
 * Role menu tetap menjadi baseline. Baris Y hanya menambah menu di luar
 * baseline, sedangkan baris N mencabut menu dari baseline. User tanpa baris
 * override sepenuhnya mewarisi role utama.
 */
class CreateUserMenuPermissions extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'usermenu_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'usermenu_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'usermenu_menu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'usermenu_active' => [
                'type'    => "ENUM('Y','N')",
                'null'    => false,
                'default' => 'N',
            ],
            'usermenu_created_on' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'usermenu_change_on' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'usermenu_created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => 'system',
            ],
            'usermenu_change_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('usermenu_id', true);
        $this->forge->addKey('usermenu_user_id');
        $this->forge->addKey('usermenu_menu_id');
        $this->forge->addKey(['usermenu_user_id', 'usermenu_menu_id'], false, true);
        $this->forge->createTable('gw_sm__usermenu', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('gw_sm__usermenu', true);
    }
}
