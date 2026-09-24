<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$actualMonths = ['jan','feb','mar','apr','may','jun','jul','aug'];
$budgetMonths = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
$report = $report ?? ['rows' => [], 'grand_total' => ['actual' => ['months' => [], 'avg' => 0, 'total' => 0], 'budget' => ['months' => [], 'total' => 0]]];
$fmt = static fn($value): string => number_format((float) $value, 2, ',', '.');
?>
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span>Reports</span>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold">Grand OPEX</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Grand OPEX Report
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Gabungan OPEX GA dan OPEX Selling untuk working year <?= esc($workingYear ?? '') ?>.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-xs transition-colors active:scale-[0.98]">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Konsolidasi OPEX GA + OPEX Selling</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Setiap subtotal modul dan grand total menggunakan kalkulator report yang sama.</p>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <div class="overflow-x-auto scrollbar-thin">
                <table class="min-w-[2200px] w-full border-collapse text-xs">
                <thead class="text-center font-semibold text-xs bg-[#2F3185] text-white border-b border-white/20">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th rowspan="2" class="border-r border-white/20 px-3.5 py-3 text-left text-white font-semibold">Module</th>
                        <th rowspan="2" class="border-r border-white/20 px-3.5 py-3 text-left text-white min-w-[200px] font-semibold">Description</th>
                        <th colspan="10" class="border-r border-white/20 bg-[#25276d] px-3 py-2 text-white font-semibold text-center">Actual</th>
                        <th rowspan="2" class="border-r border-white/20 bg-[#25276d] px-3 py-3 text-white font-semibold text-center">Assumption</th>
                        <th colspan="13" class="bg-[#25276d] px-3 py-2 text-white font-semibold text-center">Budget</th>
                    </tr>
                    <tr class="bg-[#25276d] text-white text-xs font-semibold">
                        <?php foreach ($actualMonths as $month): ?><th class="border-r border-white/20 px-3 py-2 text-white font-semibold"><?= $month ?></th><?php endforeach; ?>
                        <th class="border-r border-white/20 px-3 py-2 text-white font-semibold">Avg</th>
                        <th class="border-r border-white/20 px-3 py-2 text-white font-semibold">Total</th>
                        <?php foreach ($budgetMonths as $month): ?><th class="border-r border-white/20 px-3 py-2 text-white font-semibold"><?= $month ?></th><?php endforeach; ?>
                        <th class="px-3 py-2 text-white font-bold bg-[#25276d]">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300 font-mono text-xs">
                    <?php foreach (($report['rows'] ?? []) as $row): ?>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-3.5 py-2.5 font-sans font-bold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800"><?= esc($row['account'] ?? '') ?></td>
                            <td class="px-3.5 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($row['description'] ?? '') ?></td>
                            <?php foreach ($actualMonths as $month): ?><td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= $fmt($row['actual']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                            <td class="px-3 py-2.5 text-right font-semibold border-r border-gray-200 dark:border-gray-800"><?= $fmt($row['actual']['avg'] ?? 0) ?></td>
                            <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800 bg-[#2F3185]/5 dark:bg-[#2F3185]/10"><?= $fmt($row['actual']['total'] ?? 0) ?></td>
                            <td class="px-3 py-2.5 text-center font-sans border-r border-gray-200 dark:border-gray-800">—</td>
                            <?php foreach ($budgetMonths as $month): ?><td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= $fmt($row['budget']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                            <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-[#2F3185]/5 dark:bg-[#2F3185]/10"><?= $fmt($row['budget']['total'] ?? 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($report['rows'])): ?>
                        <tr>
                            <td colspan="26" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                                        <i class="fa-solid fa-chart-pie text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data OPEX</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada data OPEX dalam lingkup departemen user.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold font-mono text-xs text-gray-900 dark:text-white border-t-2 border-[#2F3185]/30">
                    <tr>
                        <td colspan="2" class="px-3.5 py-3 font-sans font-bold border-r border-gray-200 dark:border-gray-800">Grand Total OPEX</td>
                        <?php foreach ($actualMonths as $month): ?><td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800"><?= $fmt($report['grand_total']['actual']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                        <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800"><?= $fmt($report['grand_total']['actual']['avg'] ?? 0) ?></td>
                        <td class="px-3 py-3 text-right font-bold border-r border-gray-200 dark:border-gray-800 bg-[#2F3185]/10 dark:bg-[#2F3185]/30"><?= $fmt($report['grand_total']['actual']['total'] ?? 0) ?></td>
                        <td class="px-3 py-3 text-center font-sans border-r border-gray-200 dark:border-gray-800">—</td>
                        <?php foreach ($budgetMonths as $month): ?><td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800"><?= $fmt($report['grand_total']['budget']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                        <td class="px-3 py-3 text-right font-bold bg-[#2F3185]/10 dark:bg-[#2F3185]/30"><?= $fmt($report['grand_total']['budget']['total'] ?? 0) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
