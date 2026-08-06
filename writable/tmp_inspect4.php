<?php
$file = __DIR__ . '/../yp_budget_system_data.sql';
$h = fopen($file, 'r');
$targets = ['yp_plan__master_period', 'gw_plan__master_coa', 'gw_plan__master_department', 'gw_plan__master_cc'];
$counts = array_fill_keys($targets, 0);
$samples = array_fill_keys($targets, []);

while (($line = fgets($h)) !== false) {
    foreach ($targets as $t) {
        if (strpos($line, 'INSERT INTO `' . $t . '`') !== false) {
            $counts[$t]++;
            if (count($samples[$t]) < 3) {
                $samples[$t][] = substr(trim($line), 0, 400);
            }
        }
    }
}
fclose($h);

foreach ($targets as $t) {
    echo "=== $t ===\n";
    echo "INSERT statements: {$counts[$t]}\n";
    foreach ($samples[$t] as $s) {
        echo "  " . $s . "\n";
    }
    echo "\n";
}
