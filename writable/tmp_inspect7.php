<?php
$file = __DIR__ . '/../yp_budget_system_data.sql';
$sql = file_get_contents($file);

function extractInsertRows(string $sql, string $table): array {
    $rows = [];
    $pattern = '/insert\s+into\s+`' . preg_quote($table, '/') . '`[^;]*?;/i';
    if (!preg_match_all($pattern, $sql, $m)) return $rows;
    foreach ($m[0] as $stmt) {
        if (preg_match_all('/\(([^)]+)\)/', $stmt, $mm)) {
            foreach ($mm[1] as $r) $rows[] = $r;
        }
    }
    return $rows;
}

// COA: parse full rows (skip header row that starts with backtick)
$rows = extractInsertRows($sql, 'gw_plan__master_coa');
$years = []; $types = []; $statuses = [];
$dataRows = array_values(array_filter($rows, fn($r) => strpos($r, '`') !== 0));
echo "=== COA data rows: " . count($dataRows) . " ===\n";
foreach ($dataRows as $r) {
    // fields: id_cost_center, main_account, id_cost_header, id_acct_ext, cost_center_header, cost_center_sub, cost_center_desc, year, main_acc_type, type, category, status, updated_date, created_date
    if (preg_match("/^(\d+),(\d+),(\d+|NULL),(NULL|'[^']*'),('[^']*'),('[^']*'),('[^']*'),(\d{4}),('[^']*'),('[^']*'),('[^']*'),('[A-Za-z]')/", $r, $m)) {
        $years[$m[8]] = ($years[$m[8]] ?? 0) + 1;
        $types[$m[9] . ' || ' . $m[10]] = true;
        $statuses[$m[12]] = ($statuses[$m[12]] ?? 0) + 1;
    }
}
echo "year counts: " . json_encode($years) . "\n";
echo "main_acc_type || type: " . implode('; ', array_keys($types)) . "\n";
echo "status counts: " . json_encode($statuses) . "\n\n";

// Cost center tables
foreach (['gw_plan__master_cc', 'gw_plan__master_cost_center', 'gw_plan__master_cost_center_copy'] as $t) {
    $rows = extractInsertRows($sql, $t);
    $dataRows = array_values(array_filter($rows, fn($r) => strpos($r, '`') !== 0));
    echo "=== $t : " . count($dataRows) . " rows ===\n";
    foreach (array_slice($dataRows, 0, 3) as $r) echo "  " . substr($r, 0, 220) . "\n";
    echo "\n";
}
