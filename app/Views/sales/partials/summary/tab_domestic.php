<div class="space-y-6">

    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-table text-primary"></i> Sales Domestic Budget (12 Bulan)
            </h3>
            <span class="text-[11px] text-gray-500 dark:text-gray-400 italic">ASP Formula: Revenue / Qty (Rp/Kg)</span>
        </div>
        <div class="overflow-x-auto scrollbar-thin max-h-[600px]">
            <table class="w-full text-left text-[11px] border-collapse min-w-[1400px]">
                <thead class="bg-gray-100/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-300 font-bold uppercase border-b border-gray-300 dark:border-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 w-10 text-center">No.</th>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 w-20 text-center">CHANNEL</th>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[100px]">KEY PRODUCT</th>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[80px]">CODE INV</th>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[200px]">PRODUCT NAME</th>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 w-24 text-right">TOT QTY</th>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 w-28 text-right text-emerald-700">TOT REV (Rp)</th>
                        <th class="p-2.5 w-20 text-right text-blue-700">ASP/kg</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs">
                    <?php if (empty($domesticProducts ?? [])): ?>
                    <tr><td colspan="8" class="p-8 text-center text-gray-400">Tidak ada data produk domestic.</td></tr>
                    <?php else: ?>
                    <?php foreach ($domesticProducts as $idx => $p):
                        $totQty = 0; $totRev = 0;
                        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
                        foreach ($months as $mk) { $totQty += (float)($p[$mk.'_qty'] ?? 0); $totRev += (float)($p[$mk.'_rev'] ?? 0); }
                        $avgAsp = $totQty > 0 ? ($totRev / $totQty) : 0;
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="p-2 text-center border-r border-gray-200 dark:border-gray-800 text-gray-500"><?= $idx + 1 ?></td>
                        <td class="p-2 text-center border-r border-gray-200 dark:border-gray-800 font-bold text-blue-700 dark:text-blue-400"><?= esc($p['id_channel'] ?? 'GT') ?></td>
                        <td class="p-2 border-r border-gray-200 dark:border-gray-800"><?= esc($p['key_product'] ?? '-') ?></td>
                        <td class="p-2 border-r border-gray-200 dark:border-gray-800 font-semibold"><?= esc($p['mid_product'] ?? '-') ?></td>
                        <td class="p-2 border-r border-gray-200 dark:border-gray-800 font-medium text-gray-900 dark:text-white"><?= esc($p['product_name'] ?? '-') ?></td>
                        <td class="p-2 border-r border-gray-200 dark:border-gray-800 text-right font-mono font-bold"><?= number_format($totQty, 2) ?></td>
                        <td class="p-2 border-r border-gray-200 dark:border-gray-800 text-right font-mono font-bold text-emerald-600">Rp <?= number_format($totRev, 0, ',', '.') ?></td>
                        <td class="p-2 text-right font-mono font-bold text-blue-600"><?= number_format($avgAsp, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
