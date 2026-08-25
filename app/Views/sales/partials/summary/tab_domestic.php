<div class="space-y-6">
    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Sales Domestic Budget (12 Bulan)</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Konsolidasi total kuantiti, revenue, dan ASP per produk (IDR).</p>
    </div>

    <!-- Data Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin max-h-[600px]">
            <table class="w-full text-left text-xs border-collapse min-w-[1400px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 sticky top-0 z-10 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th class="px-3.5 py-3.5 border-r border-white/20 w-12 text-center text-white">No.</th>
                        <th class="px-3.5 py-3.5 border-r border-white/20 w-24 text-center text-white">Channel</th>
                        <th class="px-3.5 py-3.5 border-r border-white/20 min-w-[140px] text-white">Key Product</th>
                        <th class="px-3.5 py-3.5 border-r border-white/20 min-w-[120px] text-white">Code Inv</th>
                        <th class="px-3.5 py-3.5 border-r border-white/20 min-w-[260px] text-white">Product Name</th>
                        <th class="px-3.5 py-3.5 border-r border-white/20 w-28 text-right text-white">Tot Qty (Kg)</th>
                        <th class="px-3.5 py-3.5 border-r border-white/20 w-36 text-right text-white">Tot Rev (Rp)</th>
                        <th class="px-3.5 py-3.5 w-28 text-right text-white">ASP (Rp/kg)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs">
                    <?php if (empty($domesticProducts ?? [])): ?>
                    <tr>
                        <td colspan="8" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-inbox text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data Produk Domestic</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($domesticProducts as $idx => $p):
                        $totQty = 0; $totRev = 0;
                        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
                        foreach ($months as $mk) { $totQty += (float)($p[$mk.'_qty'] ?? 0); $totRev += (float)($p[$mk.'_rev'] ?? 0); }
                        $avgAsp = $totQty > 0 ? ($totRev / $totQty) : 0;
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-3.5 py-2.5 text-center border-r border-gray-200 dark:border-gray-800 text-gray-500"><?= $idx + 1 ?></td>
                        <td class="px-3.5 py-2.5 text-center border-r border-gray-200 dark:border-gray-800 font-sans font-bold text-gray-800 dark:text-gray-200"><?= esc($p['id_channel'] ?? 'GT') ?></td>
                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans text-gray-600 dark:text-gray-400"><?= esc($p['key_product'] ?? '-') ?></td>
                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 font-semibold text-gray-800 dark:text-gray-200"><?= esc($p['mid_product'] ?? '-') ?></td>
                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans font-medium text-gray-900 dark:text-white"><?= esc($p['product_name'] ?? '-') ?></td>
                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 text-right font-mono font-bold"><?= number_format($totQty, 2) ?></td>
                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">Rp <?= number_format($totRev, 0, ',', '.') ?></td>
                        <td class="px-3.5 py-2.5 text-right font-mono font-bold text-amber-600 dark:text-amber-400"><?= number_format($avgAsp, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
