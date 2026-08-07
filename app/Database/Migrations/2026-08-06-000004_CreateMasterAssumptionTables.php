<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * CreateMasterAssumptionTables — Master Assumption (PRD Phase 2.3 gap).
 *
 * Memigrasikan 5 tabel asumsi legacy ke skema CI4:
 *   - yp_plan__master_assumption_type          (kamus tipe: KURS USD/EUR, INFLATION, GDP ...)
 *   - yp_plan__master_assumption               (nilai asumsi ekonomi per tahun)
 *   - yp_plan__master_assumption_sales_domestic(Volume/ASP per channel, mis. GT/MT/OEM)
 *   - yp_plan__master_assumption_sales_export  (Volume/ASP per produk, mis. GUMMY/BOLI)
 *   - yp_plan__master_assumption_other         (rasio FOH/selling expense, mis. Delivery, Insurance)
 *
 * Semua tabel dibuat dengan IF NOT EXISTS sehingga aman di DB yang sudah punya
 * tabel legacy-nya (data tidak dihapus).
 */
class CreateMasterAssumptionTables extends Migration
{
    public function up()
    {
        // 1. Kamus tipe asumsi
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'desc_assumption'=> ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'link_to'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status'         => ['type' => 'ENUM', 'constraint' => ['A', 'D'], 'default' => 'A', 'null' => false],
            'created_by'     => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_date'   => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('yp_plan__master_assumption_type', true);

        // 2. Nilai asumsi ekonomi per tahun (KURS, INFLATION, GDP ...)
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'desc'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'type_id'        => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'value'          => ['type' => 'DECIMAL', 'constraint' => '19,4', 'default' => 0, 'null' => true],
            'year'           => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'year_codex'     => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'status'         => ['type' => 'ENUM', 'constraint' => ['A', 'D'], 'default' => 'A', 'null' => false],
            'created_date'   => ['type' => 'TIMESTAMP', 'null' => true],
            'created_by'     => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('yp_plan__master_assumption', true);

        // 3. Asumsi Sales Domestic (Volume/ASP per channel)
        $this->forge->addField([
            'id_trans'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'key_channel'    => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'key_description'=> ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'key_indicator'  => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'key_value'      => ['type' => 'DECIMAL', 'constraint' => '19,4', 'default' => 0, 'null' => true],
            'year_code'      => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_by'     => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_date'   => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id_trans', true);
        $this->forge->createTable('yp_plan__master_assumption_sales_domestic', true);

        // 4. Asumsi Sales Export (Volume/ASP per produk)
        $this->forge->addField([
            'id_trans'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'key_channel'    => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'key_description'=> ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'key_indicator'  => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'key_value'      => ['type' => 'DECIMAL', 'constraint' => '19,4', 'default' => 0, 'null' => true],
            'year_code'      => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_by'     => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_date'   => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id_trans', true);
        $this->forge->createTable('yp_plan__master_assumption_sales_export', true);

        // 5. Asumsi lain (rasio FOH/selling expense)
        $this->forge->addField([
            'id_assump'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_assp'        => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'tipe_group'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'variable_text'  => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'value_text'     => ['type' => 'DECIMAL', 'constraint' => '19,4', 'default' => 0, 'null' => true],
            'year_code'      => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'updated_by'     => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'updated_dated'  => ['type' => 'DATETIME', 'null' => true],
            'status'         => ['type' => 'ENUM', 'constraint' => ['A', 'D'], 'default' => 'A', 'null' => false],
        ]);
        $this->forge->addKey('id_assump', true);
        $this->forge->createTable('yp_plan__master_assumption_other', true);

        // Seed kamus tipe inti bila tabel masih kosong (aman di DB yang sudah ada datanya)
        $count = $this->db->table('yp_plan__master_assumption_type')->countAllResults();
        if ($count === 0) {
            $this->db->table('yp_plan__master_assumption_type')->insertBatch([
                ['desc_assumption' => 'KURS - USD', 'status' => 'A', 'created_by' => 1],
                ['desc_assumption' => 'KURS - EUR', 'status' => 'A', 'created_by' => 1],
                ['desc_assumption' => 'INFLATION',  'status' => 'A', 'created_by' => 1],
                ['desc_assumption' => 'GDP',        'status' => 'A', 'created_by' => 1],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('yp_plan__master_assumption_other', true);
        $this->forge->dropTable('yp_plan__master_assumption_sales_export', true);
        $this->forge->dropTable('yp_plan__master_assumption_sales_domestic', true);
        $this->forge->dropTable('yp_plan__master_assumption', true);
        $this->forge->dropTable('yp_plan__master_assumption_type', true);
    }
}
