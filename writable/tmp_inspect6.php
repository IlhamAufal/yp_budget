<?php
$file = __DIR__ . '/../yp_budget_system_data.sql';
$sql = file_get_contents($file);

function extractInsertRows(string $sql, string $table): array {
    $rows = [];
    // split on the insert statement for this table (case-insensitive, allow multiple spaces)
    $pattern = '/insert\s+into\s+`' . preg_quote($table, '/') . '`[^;]*?;/i';
    if (!preg_match_all($pattern, $sql, $m)) return $rows;
    foreach ($m[0] as $stmt) {
        if (preg_match_all('/\(([^)]+)\)/', $stmt, $mm)) {
            foreach ($mm[1] as $r) $rows[] = $r;
        }
    }
    return $rows;
}

echo "=== yp_plan__master_period (all) ===\n";
$rows = extractInsertRows($sql, 'yp_plan__master_period');
echo "count: " . count($rows) . "\n";
foreach (array_slice($rows, 0, 12) as $r) echo "  " . substr($r, 0, 180) . "\n";

echo "\n=== gw_plan__master_department (all) ===\n";
$rows = extractInsertRows($sql, 'gw_plan__master_department');
echo "count: " . count($rows) . "\n";
foreach (array_slice($rows, 0, 25) as $r) echo "  " . substr($r, 0, 120) . "\n";

echo "\n=== gw_plan__master_coa: distinct types & years ===\n";
$rows = extractInsertRows($sql, 'gw_plan__master_coa');
echo "count: " . count($rows) . "\n";
$types = []; $years = [];
foreach ($rows as $r) {
    if (preg_match("/'([^']*)',\s*'([^']*)',\s*'([^']*)',\s*'([^']*)',\s*'([^']*)'/", $r)) {}
    if (preg_match('/,\s*\'(\d{4})\',\s*\'([^\']*)\',\s*\'([^\']*)\',\s*\'([^\']*)\'/u', $r, $m)) {
        $years[$m[1]] = true;
        $types[$m[2] . ' | ' . $m[3]] = true;
    }
}
echo "years: " . implode(', ', array_keys($years)) . "\n";
echo "main_acc_type | type: " . implode(', ', array_keys($types)) . "\n";
echo "\nsample COA rows:\n";
foreach (array_slice($rows, 0, 5) as $r) echo "  " . substr($r, 0, 250) . "\n";

echo "\n=== gw_plan__master_cc: distinct types/years ===\n";
$rows = extractInsertRows($sql, 'gw_plan__master_cc');
echo "count: " . count($rows) . "\n";
$types = []; $years = [];
foreach ($rows as $r) {
    if (preg_match('/,\s*\'(\d{4})\',\s*\'([^\']*)\',\s*\'([^\']*)\'/u', $r, $m)) {
        $years[$m[1]] = true;
        $types[$m[2] . ' | ' . $m[3]] = true;
    }
}
echo "years: " . implode(', ', array_keys($years)) . "\n";
echo "type | category: " . implode(', ', array_keys($types)) . "\n";
foreach (array_slice($rows, 0, 3) as $r) echo "  " . substr($r, 0, 200) . "\n";
