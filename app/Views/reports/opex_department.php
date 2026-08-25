<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$actualMonths = ['jan','feb','mar','apr','may','jun','jul','aug'];
$budgetMonths = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
$report = $report ?? ['groups' => [], 'grand_total' => ['actual' => ['months' => [], 'avg' => 0, 'total' => 0], 'budget' => ['months' => [], 'total' => 0]]];
$fmt = static fn($value): string => number_format((float) $value, 2, ',', '.');
?>
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span>Reports</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold"><?= esc($module ?? 'OPEX') ?></span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Department Report (<?= esc($module ?? 'OPEX') ?>)
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Actual Januari–Agustus dan Budget Januari–Desember untuk working year <?= esc($workingYear ?? '') ?>.
            </p>
        </div>
        <form method="get" class="flex flex-col sm:flex-row sm:items-end gap-3 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Cost Center
                </label>
                <select name="cost_center" class="block w-full sm:min-w-64 rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-medium text-gray-800 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:bg-white outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white transition">
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
            </div>
            <button class="bg-[#2F3185] hover:bg-[#25276d] text-white font-semibold rounded-xl px-5 py-2.5 shadow-xs transition-all inline-flex items-center justify-center gap-2 active:scale-[0.98] text-xs shrink-0" type="submit">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Tampilkan Report</span>
            </button>
        </form>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-6 py-4">
            <div>
                <h2 class="font-bold text-sm text-gray-900 dark:text-white">Actual vs Budget</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Subtotal dihitung server-side per cost center header.</p>
            </div>
            <span class="rounded-full bg-gray-100 dark:bg-gray-800 px-3 py-1 text-xs font-semibold text-gray-600 dark:text-gray-300">26 kolom</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[2200px] w-full border-collapse text-xs">
                <thead class="text-center font-semibold text-xs bg-[#2F3185] text-white border-b border-white/20">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th rowspan="2" class="sticky left-0 z-10 border-r border-white/20 bg-[#2F3185] px-3.5 py-3 text-left text-white font-semibold">Account</th>
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
                    <?php if (empty($report['groups'])): ?>
                        <tr>
                            <td colspan="26" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                                        <i class="fa-solid fa-building text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Report</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada data report untuk scope dan cost center yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach (($report['groups'] ?? []) as $group): ?>
                        <tr class="bg-[#2F3185]/5 dark:bg-[#2F3185]/10 font-bold font-sans text-gray-900 dark:text-white">
                            <td colspan="26" class="px-4 py-2.5 border-r border-gray-200 dark:border-gray-800">Header: <?= esc($group['cost_center_header'] ?? 'Lainnya') ?></td>
                        </tr>
                        <?php foreach (($group['rows'] ?? []) as $row): ?>
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="sticky left-0 z-[1] border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-3.5 py-2.5 font-sans font-bold text-gray-900 dark:text-white"><?= esc($row['account'] ?? '') ?></td>
                                <td class="px-3.5 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($row['description'] ?? '') ?></td>
                                <?php foreach ($actualMonths as $month): ?><td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= $fmt($row['actual']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                                <td class="px-3 py-2.5 text-right font-semibold border-r border-gray-200 dark:border-gray-800"><?= $fmt($row['actual']['avg'] ?? 0) ?></td>
                                <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800 bg-[#2F3185]/5 dark:bg-[#2F3185]/10"><?= $fmt($row['actual']['total'] ?? 0) ?></td>
                                <td class="px-3 py-2.5 text-center font-sans border-r border-gray-200 dark:border-gray-800"><?= esc((string) ($row['assumption'] ?? '—')) ?></td>
                                <?php foreach ($budgetMonths as $month): ?><td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= $fmt($row['budget']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                                <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-[#2F3185]/5 dark:bg-[#2F3185]/10"><?= $fmt($row['budget']['total'] ?? 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold font-sans text-gray-900 dark:text-white">
                            <td colspan="2" class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800">Subtotal <?= esc($group['cost_center_header'] ?? '') ?></td>
                            <?php foreach ($actualMonths as $month): ?><td class="px-3 py-2.5 text-right font-mono border-r border-gray-200 dark:border-gray-800"><?= $fmt($group['subtotal']['actual']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                            <td class="px-3 py-2.5 text-right font-mono border-r border-gray-200 dark:border-gray-800"><?= $fmt($group['subtotal']['actual']['avg'] ?? 0) ?></td>
                            <td class="px-3 py-2.5 text-right font-mono font-bold border-r border-gray-200 dark:border-gray-800 bg-[#2F3185]/10 dark:bg-[#2F3185]/30"><?= $fmt($group['subtotal']['actual']['total'] ?? 0) ?></td>
                            <td class="px-3 py-2.5 text-center border-r border-gray-200 dark:border-gray-800">—</td>
                            <?php foreach ($budgetMonths as $month): ?><td class="px-3 py-2.5 text-right font-mono border-r border-gray-200 dark:border-gray-800"><?= $fmt($group['subtotal']['budget']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                            <td class="px-3 py-2.5 text-right font-mono font-bold bg-[#2F3185]/10 dark:bg-[#2F3185]/30"><?= $fmt($group['subtotal']['budget']['total'] ?? 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-[#2F3185]/15 dark:bg-[#2F3185]/30 font-bold font-mono text-xs text-gray-900 dark:text-white border-t-2 border-[#2F3185]/30">
                    <tr>
                        <td colspan="2" class="px-3.5 py-3 font-sans font-bold border-r border-gray-200 dark:border-gray-800">Grand Total</td>
                        <?php foreach ($actualMonths as $month): ?><td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800"><?= $fmt($report['grand_total']['actual']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                        <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800"><?= $fmt($report['grand_total']['actual']['avg'] ?? 0) ?></td>
                        <td class="px-3 py-3 text-right font-bold border-r border-gray-200 dark:border-gray-800 bg-[#2F3185]/15 dark:bg-[#2F3185]/40"><?= $fmt($report['grand_total']['actual']['total'] ?? 0) ?></td>
                        <td class="px-3 py-3 text-center font-sans border-r border-gray-200 dark:border-gray-800">—</td>
                        <?php foreach ($budgetMonths as $month): ?><td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800"><?= $fmt($report['grand_total']['budget']['months'][$month] ?? 0) ?></td><?php endforeach; ?>
                        <td class="px-3 py-3 text-right font-bold bg-[#2F3185]/15 dark:bg-[#2F3185]/40"><?= $fmt($report['grand_total']['budget']['total'] ?? 0) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
