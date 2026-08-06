<?php
$sql = file_get_contents(__DIR__ . '/../yp_budget_system.sql');
$targets = ['gw_plan__master_cost_center', 'gw_plan__master_product', 'gw_plan__master_coa'];
foreach ($targets as $t) {
    foreach (explode(';', $sql) as $stmt) {
        $stmt = trim($stmt);
        if (preg_match('/^CREATE TABLE (?:IF NOT EXISTS )?`' . preg_quote($t, '/') . '`/i', $stmt)) {
            echo "=== $t ===\n";
            $lines = explode("\n", $stmt);
            $depth = 0;
            foreach ($lines as $i => $line) {
                $depth += substr_count($line, '(') - substr_count($line, ')');
                $clean = trim($line);
                if ($clean === '' || preg_match('/^CREATE TABLE/i', $clean)) continue;
                if (preg_match('/^(PRIMARY|UNIQUE|KEY|CONSTRAINT|FOREIGN|INDEX)/', $clean)) continue;
                echo '  ' . $clean . "\n";
                if ($depth <= 0 && $i > 1) break;
            }
            echo "\n";
            break;
        }
    }
}
