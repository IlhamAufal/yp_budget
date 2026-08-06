<?php
$file = __DIR__ . '/../yp_budget_system_data.sql';
$h = fopen($file, 'r');
$targets = ['yp_plan__master_period', 'gw_plan__master_coa', 'gw_plan__master_department', 'gw_plan__master_cc', 'gw_sm__user', 'ci_sessions'];
$hits = array_fill_keys($targets, []);
$totalInsert = 0;
$lines = 0;

while (($line = fgets($h)) !== false) {
    $lines++;
    if (strpos($line, 'INSERT INTO') !== false) {
        $totalInsert++;
        foreach ($targets as $t) {
            if (strpos($line, $t) !== false) {
                if (count($hits[$t]) < 2) $hits[$t][] = substr(trim($line), 0, 300);
            }
        }
    }
}
fclose($h);

echo "Total lines: $lines, INSERT statements: $totalInsert\n\n";
foreach ($targets as $t) {
    echo "=== $t ===\n";
    if ($hits[$t]) {
        foreach ($hits[$t] as $s) echo "  " . $s . "\n";
    } else {
        echo "  (no INSERT found)\n";
    }
    echo "\n";
}
