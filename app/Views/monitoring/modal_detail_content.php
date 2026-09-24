<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500">Cost Center: <span class="font-bold text-gray-900 dark:text-white"><?= esc($id_dept) ?></span></p>
            <p class="text-xs text-gray-400">Tipe: <span class="font-semibold text-[#2F3185] dark:text-indigo-400"><?= esc($type) ?></span></p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                <tr class="bg-[#2F3185] text-white font-semibold">
                    <th class="px-3.5 py-2.5 border-r border-white/20 text-white font-semibold">Kode Akun</th>
                    <th class="px-3.5 py-2.5 border-r border-white/20 text-white font-semibold">Uraian / Remarks</th>
                    <th class="px-3.5 py-2.5 text-right text-white font-semibold">Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300 text-xs">
                <?php if (! empty($details)): ?>
                    <?php foreach ($details as $row): ?>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 font-mono font-medium"><?= esc($row['acct_code'] ?? $row['id_coa'] ?? '-'); ?></td>
                            <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($row['cost_center_desc'] ?? '-'); ?></td>
                            <td class="px-3.5 py-2.5 text-right font-mono font-semibold text-gray-900 dark:text-white"><?= number_format($row['total'] ?? 0, 2); ?></td>
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
