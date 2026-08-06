<div class="w-full overflow-x-auto bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark shadow-default">
    <div class="p-4 border-b border-stroke dark:border-strokedark flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
            <?= esc($header ?? 'Entry Table CAPEX'); ?>
        </h3>
        <span class="text-xs text-gray-500">Depresiasi Master: <span class="font-semibold text-primary"><?= number_format($amount ?? 0, 2); ?></span></span>
    </div>

    <table class="w-full text-left text-xs border-collapse">
        <thead>
            <tr class="bg-primary text-white text-center font-semibold">
                <th class="py-3 px-4 border-r border-white/20 whitespace-nowrap min-w-[200px]">Deskripsi Item</th>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <th class="py-2 px-3 border-r border-white/10 w-16">Bln <?= $m ?></th>
                <?php endfor; ?>
                <th class="py-3 px-4 whitespace-nowrap">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stroke dark:divide-strokedark text-gray-700 dark:text-gray-300">
            <?php if (! empty($datax)): ?>
                <?php foreach ($datax as $row): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-meta-4/20 transition-colors">
                        <td class="py-2.5 px-4 font-medium text-gray-900 dark:text-white">
                            <?= esc($row['item_desc'] ?? ''); ?>
                        </td>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <td class="py-2.5 px-3 text-right"><?= number_format((float) ($row[(string) $m] ?? 0), 2); ?></td>
                        <?php endfor; ?>
                        <td class="py-2.5 px-4 text-right font-bold text-primary">
                            <?= number_format((float) ($row['total'] ?? 0), 2); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="14" class="py-8 text-center text-gray-400">Belum ada data entry untuk kombinasi ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
