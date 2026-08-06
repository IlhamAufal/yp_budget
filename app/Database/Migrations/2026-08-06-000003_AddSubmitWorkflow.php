<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Phase 2.1 — Workflow Submit pada form entry budget (OPEX GA & FOH).
 *
 * Menambahkan kolom status submit ke yp_plan__trans_budget_entry_data:
 *   - submit_status : DRAFT → SUBMITTED → APPROVED
 *   - submit_by     : user yang melakukan submit
 *   - submit_date   : timestamp submit
 *
 * Kolom ditambahkan aman (hanya bila belum ada) agar tidak merusak
 * tabel legacy yang sudah berisi data.
 */
class AddSubmitWorkflow extends Migration
{
    public function up(): void
    {
        $this->ensureColumns('yp_plan__trans_budget_entry_data', [
            'submit_status' => [
                'type'    => "ENUM('DRAFT','SUBMITTED','APPROVED')",
                'null'    => true,
                'default' => 'DRAFT',
            ],
            'submit_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'submit_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down(): void
    {
        $columns = $this->db->getFieldNames('yp_plan__trans_budget_entry_data');

        foreach (['submit_status', 'submit_by', 'submit_date'] as $column) {
            if (in_array($column, $columns, true)) {
                $this->forge->dropColumn('yp_plan__trans_budget_entry_data', $column);
            }
        }
    }

    /**
     * Tambahkan kolom hanya bila belum ada (aman untuk tabel legacy).
     */
    private function ensureColumns(string $table, array $fields): void
    {
        $columns = $this->db->getFieldNames($table);

        foreach ($fields as $fieldName => $definition) {
            if (! in_array($fieldName, $columns, true)) {
                $this->forge->addColumn($table, [$fieldName => $definition]);
            }
        }
    }
}
