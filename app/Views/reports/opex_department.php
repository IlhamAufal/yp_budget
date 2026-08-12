<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$actualMonths = ['jan','feb','mar','apr','may','jun','jul','aug'];
$budgetMonths = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
$report = $report ?? ['groups' => [], 'grand_total' => ['actual' => ['months' => [], 'avg' => 0, 'total' => 0], 'budget' => ['months' => [], 'total' => 0]]];
$fmt = static fn($value): string => number_format((float) $value, 2, ',', '.');
?>
<div class="mx-auto max-w-screen-2xl space-y-6 p-4 md:p-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-brand-500">Department Report</p>
            <h1 class="mt-1 text-2xl font-black text-gray-900 dark:text-white"><?= esc($module ?? 'OPEX') ?></h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Actual Januari–Agustus dan Budget Januari–Desember untuk working year <?= esc($workingYear ?? '') ?>.</p>
        </div>
        <form method="get" class="flex flex-col gap-2 sm:flex-row sm:items-end">
            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                Cost Center
                <select name="cost_center" class="mt-1 block min-w-64 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    <?php if (($module ?? '') === 'OPEX SELLING'): ?>
                        <option value="0" <?= (string) ($selected ?? '0') === '0' ? 'selected' : '' ?>>All Cost Center</option>
                    <?php endif; ?>
                    <?php foreach (($costCenters ?? []) as $cc): ?>
                        <?php $value = (string) ($cc['cost_center_sap'] ?? $cc['cc_code'] ?? $cc['cost_center'] ?? ''); ?>
                        <option value="<?= esc($value) ?>" <?= (string) ($selected ?? '') === $value ? 'selected' : '' ?>>
                            <?= esc($value . ' - ' . ($cc['cost_desc'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-brand-700" type="submit">Tampilkan Report</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">Actual vs Budget</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Subtotal dihitung server-side per cost center header.</p>
            </div>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">26 kolom</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[2200px] w-full border-collapse text-xs">
                <thead class="text-center font-bold uppercase tracking-wide">
                    <tr class="bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        <th rowspan="2" class="sticky left-0 z-10 border border-gray-200 bg-gray-100 px-3 py-3 text-left dark:border-gray-700 dark:bg-gray-800">Account</th>
                        <th rowspan="2" class="border border-gray-200 px-3 py-3 text-left dark:border-gray-700">Description</th>
                        <th colspan="10" class="border border-gray-200 bg-emerald-50 px-3 py-2 text-emerald-700 dark:border-gray-700 dark:bg-emerald-950/30 dark:text-emerald-300">Actual</th>
                        <th rowspan="2" class="border border-gray-200 bg-amber-50 px-3 py-3 text-amber-700 dark:border-gray-700 dark:bg-amber-950/30 dark:text-amber-300">Assumption</th>
                        <th colspan="13" class="border border-gray-200 bg-sky-50 px-3 py-2 text-sky-700 dark:border-gray-700 dark:bg-sky-950/30 dark:text-sky-300">Budget</th>
                    </tr>
                    <tr class="bg-gray-50 text-gray-600 dark:bg-gray-900 dark:text-gray-300">
                        <?php foreach ($actualMonths as $month): ?><th class="border border-gray-200 px-3 py-2 dark:border-gray-700"><?= strtoupper($month) ?></th><?php endforeach; ?>
                        <th class="border border-gray-200 px-3 py-2 dark:border-gray-700">AVG</th>
                        <th class="border border-gray-200 px-3 py-2 dark:border-gray-700">TOTAL</th>
                        <?php foreach ($budgetMonths as $month): ?><th class="border border-gray-200 px-3 py-2 dark:border-gray-700"><?= strtoupper($month) ?></th><?php endforeach; ?>
                        <th class="border border-gray-200 px-3 py-2 dark:border-gray-700">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($report['groups'])): ?>
                        <tr><td colspan="26" class="px-4 py-12 text-center text-gray-500">Tidak ada data report untuk scope dan cost center yang dipilih.</td></tr>
                    <?php endif; ?>
                    <?php foreach (($report['groups'] ?? []) as $group): ?>
                        <tr class="bg-gray-50 font-bold text-gray-700 dark:bg-gray-800/70 dark:text-gray-200">
                            <td colspan="26" class="border border-gray-200 px-3 py-2 dark:border-gray-700">Header: <?= esc($group['cost_center_header'] ?? 'Lainnya') ?></td>
                        </tr>
                        <?php foreach (($group['rows'] ?? []) as $row): ?>
                            <tr class="text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800/50">
                                <td class="sticky left-0 z-[1] border border-gray-200 bg-white px-3 py-2 font-semibold dark:border-gray-700 dark:bg-gray-900"><?= esc($row['account'] ?? '') ?></td>
                                <td class="border border-gray-200 px-3 py-2 dark:border-gray-700"><?= esc($row['description'] ?? '') ?></td>
                                <?php foreach ($actualMonths as $month): ?><td class="border border-gray-200 px-3 py-2 text-right dark:border-gray-700"><?= $fmt($row['actual']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                                <td class="border border-gray-200 bg-emerald-50/50 px-3 py-2 text-right font-semibold dark:border-gray-700 dark:bg-emerald-950/20"><?= $fmt($row['actual']['avg'] ?? 0) ?></td>
                                <td class="border border-gray-200 bg-emerald-50/50 px-3 py-2 text-right font-semibold dark:border-gray-700 dark:bg-emerald-950/20"><?= $fmt($row['actual']['total'] ?? 0) ?></td>
                                <td class="border border-gray-200 bg-amber-50/50 px-3 py-2 text-center dark:border-gray-700 dark:bg-amber-950/20"><?= esc((string) ($row['assumption'] ?? '')) ?></td>
                                <?php foreach ($budgetMonths as $month): ?><td class="border border-gray-200 px-3 py-2 text-right dark:border-gray-700"><?= $fmt($row['budget']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                                <td class="border border-gray-200 bg-sky-50/50 px-3 py-2 text-right font-semibold dark:border-gray-700 dark:bg-sky-950/20"><?= $fmt($row['budget']['total'] ?? 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-emerald-50/60 font-bold text-gray-800 dark:bg-emerald-950/20 dark:text-gray-100">
                            <td colspan="2" class="border border-gray-200 px-3 py-2 dark:border-gray-700">Subtotal <?= esc($group['cost_center_header'] ?? '') ?></td>
                            <?php foreach ($actualMonths as $month): ?><td class="border border-gray-200 px-3 py-2 text-right dark:border-gray-700"><?= $fmt($group['subtotal']['actual']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                            <td class="border border-gray-200 px-3 py-2 text-right dark:border-gray-700"><?= $fmt($group['subtotal']['actual']['avg'] ?? 0) ?></td>
                            <td class="border border-gray-200 px-3 py-2 text-right dark:border-gray-700"><?= $fmt($group['subtotal']['actual']['total'] ?? 0) ?></td>
                            <td class="border border-gray-200 px-3 py-2 text-center dark:border-gray-700">—</td>
                            <?php foreach ($budgetMonths as $month): ?><td class="border border-gray-200 px-3 py-2 text-right dark:border-gray-700"><?= $fmt($group['subtotal']['budget']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                            <td class="border border-gray-200 px-3 py-2 text-right dark:border-gray-700"><?= $fmt($group['subtotal']['budget']['total'] ?? 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-brand-50 font-black text-gray-900 dark:bg-brand-950/30 dark:text-white">
                    <tr>
                        <td colspan="2" class="border border-gray-200 px-3 py-3 dark:border-gray-700">Grand Total</td>
                        <?php foreach ($actualMonths as $month): ?><td class="border border-gray-200 px-3 py-3 text-right dark:border-gray-700"><?= $fmt($report['grand_total']['actual']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                        <td class="border border-gray-200 px-3 py-3 text-right dark:border-gray-700"><?= $fmt($report['grand_total']['actual']['avg'] ?? 0) ?></td>
                        <td class="border border-gray-200 px-3 py-3 text-right dark:border-gray-700"><?= $fmt($report['grand_total']['actual']['total'] ?? 0) ?></td>
                        <td class="border border-gray-200 px-3 py-3 text-center dark:border-gray-700">—</td>
                        <?php foreach ($budgetMonths as $month): ?><td class="border border-gray-200 px-3 py-3 text-right dark:border-gray-700"><?= $fmt($report['grand_total']['budget']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                        <td class="border border-gray-200 px-3 py-3 text-right dark:border-gray-700"><?= $fmt($report['grand_total']['budget']['total'] ?? 0) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
