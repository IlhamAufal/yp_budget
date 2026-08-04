<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Impor schema dari yp_budget_system.sql.
 *
 * Malas: satu file migrasi yang eksekusi CREATE TABLE langsung dari dump,
 * daripada menulis ulang 90 tabel pakai Forge. Struktur tabel = file SQL
 * itu sendiri sebagai sumber kebenaran.
 *
 * ponytail: kalau butuh ubah schema per-tabel, pisah jadi migrasi per tabel
 * dan hapus file ini.
 */
class CreateBudgetSystemFromSql extends Migration
{
    private const DB_NAME = 'yp_budget_system';

    public function up(): void
    {
        $sqlFile = dirname(APPPATH) . DIRECTORY_SEPARATOR . 'yp_budget_system.sql';

        if (! is_file($sqlFile)) {
            throw new \RuntimeException('File SQL tidak ditemukan: ' . $sqlFile);
        }

        $db = \Config\Database::connect();

        $db->query('CREATE DATABASE IF NOT EXISTS `' . self::DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
        $db->query('USE `' . self::DB_NAME . '`');

        foreach ($this->parseStatements(file_get_contents($sqlFile)) as $statement) {
            $db->query($statement);
        }
    }

    public function down(): void
    {
        $sqlFile = dirname(APPPATH) . DIRECTORY_SEPARATOR . 'yp_budget_system.sql';

        if (! is_file($sqlFile)) {
            return;
        }

        $db = \Config\Database::connect();
        $db->query('USE `' . self::DB_NAME . '`');
        $db->query('SET FOREIGN_KEY_CHECKS = 0');

        $tables = [];

        foreach ($this->parseStatements(file_get_contents($sqlFile)) as $statement) {
            if (preg_match('/^CREATE TABLE IF NOT EXISTS `([^`]+)`/i', $statement, $m)
                || preg_match('/^CREATE TABLE `([^`]+)`/i', $statement, $m)) {
                $tables[] = $m[1];
            }
        }

        foreach (array_reverse($tables) as $table) {
            $db->query('DROP TABLE IF EXISTS `' . $table . '`');
        }

        $db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * Pecah dump SQL jadi pernyataan, buang baris pragma/komentar.
     *
     * @return string[]
     */
    private function parseStatements(string $sql): array
    {
        $statements = [];

        foreach (explode(';', $sql) as $statement) {
            $statement = trim($statement);

            if ($statement === '') {
                continue;
            }

            // pragma MySQL/MariaDB & komentar
            if (str_starts_with($statement, 'SET ')
                || str_starts_with($statement, '/*!')
                || str_starts_with($statement, '/*')
                || preg_match('/^CREATE DATABASE/i', $statement)
                || preg_match('/^USE /i', $statement)) {
                continue;
            }

            $statements[] = $statement;
        }

        return $statements;
    }
}
