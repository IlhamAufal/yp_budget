<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel baru CI4: `yp_plan__master_year`
 *
 * Menyimpan daftar Tahun Anggaran (Working Year) beserta status aktif & lock.
 *
 * Latar belakang: tabel legacy `yp_plan__master_period` berisi periode entry
 * per-modul (bukan daftar tahun anggaran), sehingga tidak bisa dipakai
 * langsung sebagai sumber Working Year. Sesuai PRD §6.3, tabel baru
 * ditambahkan untuk kebutuhan CI4 tanpa mengubah tabel legacy.
 */
class CreateMasterYearTable extends Migration
{
    public function up(): void
    {
        $this->forge = \Config\Database::forge();

        $this->forge->addField([
            'year_code'    => [
                'type'       => 'INT',
                'constraint' => 4,
                'unsigned'   => true,
            ],
            'period_start' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'period_end'   => [
                'type' => 'DATE',
                'null' => true,
            ],
            'form_budget'  => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'cost_center'  => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => '*',
            ],
            'status'       => [
                'type'       => 'ENUM',
                'constraint' => ['A', 'D'],
                'default'    => 'A',
            ],
            'locked'       => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('year_code');
        $this->forge->createTable('yp_plan__master_year', true);

        $this->seedYears();
    }

    public function down(): void
    {
        $this->forge = \Config\Database::forge();
        $this->forge->dropTable('yp_plan__master_year', true);
    }

    /**
     * Seed tahun anggaran: dari tahun yang ada di tabel legacy + tahun berjalan.
     * Semua query dibungkus try/catch agar aman jika tabel schema belum dibuat.
     */
    private function seedYears(): void
    {
        $db = \Config\Database::connect();
        $years = [];

        foreach (['gw_plan__master_coa', 'gw_plan__master_cost_center'] as $table) {
            try {
                $rows = $db->table($table)->select('year')->distinct()->get()->getResultArray();
                foreach ($rows as $r) {
                    if (! empty($r['year'])) {
                        $years[(int) $r['year']] = true;
                    }
                }
            } catch (\Throwable $e) {
                // Tabel belum ada → abaikan
            }
        }

        try {
            $rows = $db->table('yp_plan__master_period')->select('begda')->get()->getResultArray();
            foreach ($rows as $r) {
                if (! empty($r['begda'])) {
                    $years[(int) date('Y', strtotime($r['begda']))] = true;
                }
            }
        } catch (\Throwable $e) {
            // abaikan
        }

        // Selalu sediakan tahun berjalan ± 1 tahun dan 2 tahun ke depan
        $current = (int) date('Y');
        foreach ([$current - 1, $current, $current + 1, $current + 2] as $y) {
            $years[$y] = true;
        }

        ksort($years);

        $builder = $db->table('yp_plan__master_year');
        foreach (array_keys($years) as $y) {
            try {
                $builder->ignore(true)->insert([
                    'year_code'    => $y,
                    'period_start' => ($y === $current) ? date('Y-m-d') : null,
                    'period_end'   => ($y === $current) ? $current . '-12-31' : null,
                    'status'       => 'A',
                    'locked'       => 0,
                    'created_date' => date('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $e) {
                // Duplikat / gagal → abaikan
            }
        }
    }
}
