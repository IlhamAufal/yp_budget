<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$actualMonths = ['jan','feb','mar','apr','may','jun','jul','aug'];
$budgetMonths = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
$report = $report ?? ['rows' => [], 'grand_total' => ['actual' => ['months' => [], 'avg' => 0, 'total' => 0], 'budget' => ['months' => [], 'total' => 0]]];
$fmt = static fn($value): string => number_format((float) $value, 2, ',', '.');
?>
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span>Reports</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-brand-500 font-bold">Grand OPEX</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 shadow-xs">
                    <i class="fa-solid fa-chart-pie text-base"></i>
                </span>
                Grand OPEX Report
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Gabungan OPEX GA dan OPEX Selling untuk working year <?= esc($workingYear ?? '') ?>.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center gap-2 rounded-xl border border-gray-200/80 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-xs hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="border-b border-gray-100 dark:border-gray-800 px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-sm text-gray-900 dark:text-white">OPEX GA + OPEX SELLING</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Setiap subtotal modul dan grand total menggunakan kalkulator report yang sama.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[2200px] w-full border-collapse text-xs">
                <thead class="text-center font-semibold text-xs bg-brand-500 text-white border-b border-brand-600">
                    <tr class="bg-brand-500 text-white font-semibold">
                        <th rowspan="2" class="border border-white/20 px-3 py-3 text-left text-white">Module</th>
                        <th rowspan="2" class="border border-white/20 px-3 py-3 text-left text-white min-w-[200px]">Description</th>
                        <th colspan="10" class="border border-white/20 bg-emerald-700/60 px-3 py-2 text-white font-semibold text-center">Actual</th>
                        <th rowspan="2" class="border border-white/20 bg-amber-700/60 px-3 py-3 text-white font-semibold text-center">Assumption</th>
                        <th colspan="13" class="border border-white/20 bg-sky-700/60 px-3 py-2 text-white font-semibold text-center">Budget</th>
                    </tr>
                    <tr class="bg-brand-600 text-white text-[11px] font-semibold">
                        <?php foreach ($actualMonths as $month): ?><th class="border border-white/20 px-3 py-2 text-white"><?= $month ?></th><?php endforeach; ?>
                        <th class="border border-white/20 px-3 py-2 text-white">Avg</th><th class="border border-white/20 px-3 py-2 text-white">Total</th>
                        <?php foreach ($budgetMonths as $month): ?><th class="border border-white/20 px-3 py-2 text-white"><?= $month ?></th><?php endforeach; ?>
                        <th class="border border-white/20 px-3 py-2 text-white">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($report['rows'] ?? []) as $row): ?>
                        <tr class="text-gray-700 dark:text-gray-300">
                            <td class="border border-gray-200 px-3 py-3 font-bold dark:border-gray-700"><?= esc($row['account'] ?? '') ?></td>
                            <td class="border border-gray-200 px-3 py-3 dark:border-gray-700"><?= esc($row['description'] ?? '') ?></td>
                            <?php foreach ($actualMonths as $month): ?><td class="border border-gray-200 px-3 py-3 text-right dark:border-gray-700"><?= $fmt($row['actual']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                            <td class="border border-gray-200 px-3 py-3 text-right font-semibold dark:border-gray-700"><?= $fmt($row['actual']['avg'] ?? 0) ?></td>
                            <td class="border border-gray-200 px-3 py-3 text-right font-semibold dark:border-gray-700"><?= $fmt($row['actual']['total'] ?? 0) ?></td>
                            <td class="border border-gray-200 px-3 py-3 text-center dark:border-gray-700">—</td>
                            <?php foreach ($budgetMonths as $month): ?><td class="border border-gray-200 px-3 py-3 text-right dark:border-gray-700"><?= $fmt($row['budget']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                            <td class="border border-gray-200 px-3 py-3 text-right font-semibold dark:border-gray-700"><?= $fmt($row['budget']['total'] ?? 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($report['rows'])): ?>
                        <tr>
                            <td colspan="26" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-chart-pie text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data OPEX</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada data OPEX dalam lingkup departemen user.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="bg-brand-50 font-black text-gray-900 dark:bg-brand-950/30 dark:text-white">
                    <tr>
                        <td colspan="2" class="border border-gray-200 px-3 py-3 dark:border-gray-700">Grand Total OPEX</td>
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
