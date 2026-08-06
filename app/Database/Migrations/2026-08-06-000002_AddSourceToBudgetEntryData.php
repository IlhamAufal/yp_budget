<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tambahkan kolom `source` pada yp_plan__trans_budget_entry_data.
 *
 * Kolom ini menandai asal baris data OPEX:
 *   - NULL / 'manual'  : di-entry manual lewat modul OPEX (default)
 *   - 'MPP'            : hasil push Personnel Cost dari MPP (Phase 2.2)
 *   - 'CAPEX'          : hasil push Depresiasi dari CAPEX (Phase 2.2)
 *
 * Tujuannya agar syncToOpex() hanya menimpa baris hasil sync sebelumnya,
 * TANPA menghapus data budget OPEX yang di-entry manual oleh user.
 */
class AddSourceToBudgetEntryData extends Migration
{
    public function up()
    {
        $this->ensureColumn('yp_plan__trans_budget_entry_data', [
            'source' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => null,
            ],
        ]);
    }

    public function down()
    {
        $columns = $this->db->getFieldNames('yp_plan__trans_budget_entry_data');
        if (in_array('source', $columns, true)) {
            $this->forge->dropColumn('yp_plan__trans_budget_entry_data', 'source');
        }
    }

    private function ensureColumn(string $table, array $fields): void
    {
        $columns = $this->db->getFieldNames($table);
        foreach ($fields as $fieldName => $definition) {
            if (! in_array($fieldName, $columns, true)) {
                $this->forge->addColumn($table, [$fieldName => $definition]);
            }
        }
    }
}
