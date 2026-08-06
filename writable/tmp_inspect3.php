<?php
$sql = file_get_contents(__DIR__ . '/../yp_budget_system.sql');

// Extract INSERT rows for yp_plan__master_period
echo "=== yp_plan__master_period INSERTs (first 15) ===\n";
$n = 0;
foreach (explode(';', $sql) as $stmt) {
    $stmt = trim($stmt);
    if (preg_match('/^INSERT INTO `yp_plan__master_period`/i', $stmt)) {
        preg_match_all('/\(([^)]+)\)/', $stmt, $m);
        foreach ($m[1] as $row) {
            echo '  ' . $row . "\n";
            if (++$n >= 15) break 2;
        }
    }
}
echo "(total shown: $n)\n\n";

// Sample COA rows
echo "=== gw_plan__master_coa INSERTs (first 8) ===\n";
$n = 0;
foreach (explode(';', $sql) as $stmt) {
    $stmt = trim($stmt);
    if (preg_match('/^INSERT INTO `gw_plan__master_coa`/i', $stmt)) {
        preg_match_all('/\(([^)]+)\)/', $stmt, $m);
        foreach ($m[1] as $row) {
            echo '  ' . substr($row, 0, 200) . "\n";
            if (++$n >= 8) break 2;
        }
    }
}
echo "(total shown: $n)\n\n";

// Department sample
echo "=== gw_plan__master_department INSERTs ===\n";
$n = 0;
foreach (explode(';', $sql) as $stmt) {
    $stmt = trim($stmt);
    if (preg_match('/^INSERT INTO `gw_plan__master_department`/i', $stmt)) {
        preg_match_all('/\(([^)]+)\)/', $stmt, $m);
        foreach ($m[1] as $row) {
            echo '  ' . substr($row, 0, 150) . "\n";
            if (++$n >= 20) break 2;
        }
    }
}
echo "(total shown: $n)\n\n";

// Count distinct begda years in period table
echo "=== Distinct years available (YEAR(begda)) ===\n";
$years = [];
foreach (explode(';', $sql) as $stmt) {
    if (preg_match('/^INSERT INTO `yp_plan__master_period`/i', trim($stmt))) {
        preg_match_all('/\'(\d{4})-(\d{2})-\d{2}/', $stmt, $m);
        foreach ($m[1] as $y) $years[$y] = true;
    }
}
echo implode(', ', array_keys($years)) . "\n";
