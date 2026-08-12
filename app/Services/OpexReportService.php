<?php

namespace App\Services;

/**
 * Normalizes Actual/Budget rows used by the OPEX department and grand reports.
 *
 * The service deliberately contains no database access. Controllers/models are
 * responsible for authorization and retrieval; this class keeps all report
 * arithmetic in one place so detail, subtotal, and grand total agree.
 */
class OpexReportService
{
    /** @var string[] */
    public const ACTUAL_MONTHS = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug'];

    /** @var string[] */
    public const BUDGET_MONTHS = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<string, mixed>
     */
    public function normalizeRows(array $rows): array
    {
        $normalized = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $actual = $this->normalizeMetric($row, 'actual', self::ACTUAL_MONTHS, 'a');
            $budget = $this->normalizeMetric($row, 'budget', self::BUDGET_MONTHS, 'b');
            $header = trim((string) $this->firstValue($row, ['cost_center_header', 'header_name', 'header'], 'Lainnya'));

            $normalized[] = [
                'account'             => (string) $this->firstValue($row, ['account', 'acct_code', 'id_acct_ext', 'id_coa', 'main_account'], ''),
                'description'         => (string) $this->firstValue($row, ['description', 'coa_desc', 'cost_center_desc', 'coa_name'], ''),
                'cost_center_header'  => $header !== '' ? $header : 'Lainnya',
                'assumption'          => $this->firstValue($row, ['assumption', 'notes_value', 'notes'], null),
                'actual'              => $actual,
                'budget'              => $budget,
            ];
        }

        return $this->summarize($normalized);
    }

    /**
     * Rebuild report totals from already-normalized rows.
     *
     * @param array<int, array<string, mixed>> $rows
     * @return array<string, mixed>
     */
    public function summarize(array $rows): array
    {
        $groups = [];
        $grand = $this->emptyTotals();

        foreach ($rows as $row) {
            $header = trim((string) ($row['cost_center_header'] ?? 'Lainnya')) ?: 'Lainnya';
            if (! isset($groups[$header])) {
                $groups[$header] = [
                    'cost_center_header' => $header,
                    'rows'               => [],
                    'subtotal'           => $this->emptyTotals(),
                ];
            }

            $groups[$header]['rows'][] = $row;
            $this->addTotals($groups[$header]['subtotal'], $row);
            $this->addTotals($grand, $row);
        }

        $groupList = array_values($groups);
        foreach ($groupList as &$group) {
            $group['subtotal'] = $this->finalizeTotals($group['subtotal']);
        }
        unset($group);

        return [
            'rows'        => array_values($rows),
            'groups'      => $groupList,
            'subtotals'   => array_map(static function (array $group): array {
                return [
                    'cost_center_header' => $group['cost_center_header'],
                    'actual'             => $group['subtotal']['actual'],
                    'budget'             => $group['subtotal']['budget'],
                ];
            }, $groupList),
            'grand_total' => $this->finalizeTotals($grand),
        ];
    }

    /**
     * Combine module totals for Grand OPEX.
     *
     * @param array<string, array<string, mixed>> $reports
     * @return array<string, mixed>
     */
    public function combine(array $reports): array
    {
        $modules = [];
        $grand = $this->emptyTotals();

        foreach ($reports as $module => $report) {
            $totals = $this->normalizeTotals((array) ($report['grand_total'] ?? []));
            $modules[] = [
                'account'            => strtoupper((string) $module),
                'description'        => strtoupper((string) $module),
                'cost_center_header' => strtoupper((string) $module),
                'assumption'         => null,
                'actual'             => $totals['actual'],
                'budget'             => $totals['budget'],
            ];
            $this->addTotals($grand, ['actual' => $totals['actual'], 'budget' => $totals['budget']]);
        }

        $grand = $this->finalizeTotals($grand);

        return [
            'rows'        => $modules,
            'groups'      => array_map(static function (array $row): array {
                return [
                    'cost_center_header' => $row['cost_center_header'],
                    'rows'               => [$row],
                    'subtotal'           => ['actual' => $row['actual'], 'budget' => $row['budget']],
                ];
            }, $modules),
            'subtotals'   => array_map(static function (array $row): array {
                return [
                    'cost_center_header' => $row['cost_center_header'],
                    'actual'             => $row['actual'],
                    'budget'             => $row['budget'],
                ];
            }, $modules),
            'grand_total' => $grand,
        ];
    }

    /** @return array<string, mixed> */
    public function emptyTotals(): array
    {
        return [
            'actual' => [
                'months' => array_fill_keys(self::ACTUAL_MONTHS, 0.0),
                'avg'    => 0.0,
                'total'  => 0.0,
            ],
            'budget' => [
                'months' => array_fill_keys(self::BUDGET_MONTHS, 0.0),
                'total'  => 0.0,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @param string[] $months
     * @return array<string, mixed>
     */
    private function normalizeMetric(array $row, string $nestedKey, array $months, string $prefix): array
    {
        $nested = isset($row[$nestedKey]) && is_array($row[$nestedKey]) ? $row[$nestedKey] : [];
        $source = isset($nested['months']) && is_array($nested['months']) ? $nested['months'] : $nested;
        $values = [];
        $total = 0.0;

        foreach ($months as $index => $month) {
            $number = $this->number($this->firstValue($source, [$month, (string) ($index + 1), $prefix . ($index + 1)], null));
            if ($nested === []) {
                $candidates = $prefix === 'a'
                    ? [$nestedKey . '_' . $month, strtoupper($month), $month, 'a' . ($index + 1)]
                    : [$nestedKey . '_' . $month, 'isi_' . ($index + 1), 'b' . ($index + 1), 'budget_' . $month, $month];
                $number = $this->number($this->firstValue($row, $candidates, 0));
            }
            $values[$month] = $number;
            $total += $number;
        }

        return [
            'months' => $values,
            'avg'    => $prefix === 'a' ? $total / 8 : 0.0,
            'total'  => $total,
        ];
    }

    /**
     * @param array<string, mixed> $target
     * @param array<string, mixed> $row
     */
    private function addTotals(array &$target, array $row): void
    {
        $normalized = $this->normalizeTotals([
            'actual' => (array) ($row['actual'] ?? []),
            'budget' => (array) ($row['budget'] ?? []),
        ]);
        $actual = $normalized['actual'];
        $budget = $normalized['budget'];

        foreach (self::ACTUAL_MONTHS as $month) {
            $target['actual']['months'][$month] += (float) ($actual['months'][$month] ?? 0);
        }
        foreach (self::BUDGET_MONTHS as $month) {
            $target['budget']['months'][$month] += (float) ($budget['months'][$month] ?? 0);
        }
        $target['actual']['total'] += (float) ($actual['total'] ?? 0);
        $target['budget']['total'] += (float) ($budget['total'] ?? 0);
        $target['actual']['avg'] = $target['actual']['total'] / 8;
    }

    /** @param array<string, mixed> $totals */
    private function normalizeTotals(array $totals): array
    {
        $result = $this->emptyTotals();
        $actual = is_array($totals['actual'] ?? null) ? $totals['actual'] : [];
        $budget = is_array($totals['budget'] ?? null) ? $totals['budget'] : [];

        foreach (self::ACTUAL_MONTHS as $month) {
            $result['actual']['months'][$month] = $this->number(is_array($actual['months'] ?? null) ? ($actual['months'][$month] ?? 0) : 0);
        }
        foreach (self::BUDGET_MONTHS as $month) {
            $result['budget']['months'][$month] = $this->number(is_array($budget['months'] ?? null) ? ($budget['months'][$month] ?? 0) : 0);
        }
        $result['actual']['total'] = $this->number($actual['total'] ?? array_sum($result['actual']['months']));
        $result['actual']['avg'] = $result['actual']['total'] / 8;
        $result['budget']['total'] = $this->number($budget['total'] ?? array_sum($result['budget']['months']));

        return $result;
    }

    /** @param array<string, mixed> $totals */
    private function finalizeTotals(array $totals): array
    {
        return $this->normalizeTotals($totals);
    }

    /** @param array<string, mixed> $row */
    private function firstValue(array $row, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if ($key !== null && array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
                return $row[$key];
            }
        }

        return $default;
    }

    private function number($value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }
}
