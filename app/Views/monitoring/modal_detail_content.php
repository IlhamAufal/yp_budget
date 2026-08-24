<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500">Cost Center: <span class="font-semibold text-gray-900 dark:text-white"><?= esc($id_dept) ?></span></p>
            <p class="text-xs text-gray-400">Tipe: <?= esc($type) ?></p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
        <table class="w-full text-left text-xs">
            <thead class="bg-brand-500 text-white font-semibold text-xs border-b border-brand-600">
                <tr class="bg-brand-500 text-white font-semibold">
                    <th class="px-3 py-2 border-r border-white/20 text-white">Kode Akun</th>
                    <th class="px-3 py-2 border-r border-white/20 text-white">Uraian / Remarks</th>
                    <th class="px-3 py-2 text-right text-white">Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300">
                <?php if (! empty($details)): ?>
                    <?php foreach ($details as $row): ?>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-3 py-2 border-r border-gray-200 dark:border-gray-800 font-mono"><?= esc($row['acct_code'] ?? $row['id_coa'] ?? '-'); ?></td>
                            <td class="px-3 py-2 border-r border-gray-200 dark:border-gray-800"><?= esc($row['cost_center_desc'] ?? '-'); ?></td>
                            <td class="px-3 py-2 text-right font-semibold text-gray-900 dark:text-white"><?= number_format($row['total'] ?? 0, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-400">Tidak ada data detail untuk cost center ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
