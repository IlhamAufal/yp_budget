<div class="space-y-6">
    <?php
    $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
    $monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    // Aturan ASP identik dengan pivot Sales Domestic - Budget:
    // (Revenue / Qty) x faktor — faktor 1 untuk channel YTI, selain itu 1000.
    $calcAsp = function (float $rev, float $qty, string $channel = ''): float {
        if ($qty <= 0) return 0.0;
        $factor = strtoupper($channel) === 'YTI' ? 1 : 1000;
        return ($rev / $qty) * $factor;
    };
    ?>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Sales Domestic Budget (12 Bulan)</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Konsolidasi kuantiti, revenue, dan ASP per produk per bulan (IDR).</p>
    </div>

    <!-- Data Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin max-h-[600px]">
            <table class="w-full text-left text-xs border-collapse min-w-[4600px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 sticky top-0 z-20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th rowspan="2" class="px-3.5 py-3 border-r border-white/20 w-12 text-center sticky left-0 z-30 bg-[#2F3185] text-white font-semibold">No.</th>
                        <th rowspan="2" class="px-3.5 py-3 border-r border-white/20 w-24 text-center sticky left-12 z-30 bg-[#2F3185] text-white font-semibold">Channel</th>
                        <th rowspan="2" class="px-3.5 py-3 border-r border-white/20 min-w-[140px] text-white">Key Product</th>
                        <th rowspan="2" class="px-3.5 py-3 border-r border-white/20 min-w-[120px] text-white">Code Inv</th>
                        <th rowspan="2" class="px-3.5 py-3 border-r border-white/20 min-w-[260px] text-white">Product Name</th>
                        <?php foreach ($monthLabels as $ml): ?>
                            <th colspan="3" class="px-3 py-2.5 border-r border-white/20 text-center text-white font-semibold"><?= $ml ?></th>
                        <?php endforeach; ?>
                        <th colspan="3" class="px-3 py-2.5 text-center text-white font-bold bg-[#25276d] border-l border-white/20">Annual Total</th>
                    </tr>
                    <tr class="bg-[#25276d] text-white text-xs font-semibold">
                        <?php for ($i = 0; $i < 12; $i++): ?>
                            <th class="px-2 py-2 border-r border-white/20 w-20 text-center text-white font-semibold text-xs bg-[#25276d]">Qty</th>
                            <th class="px-2 py-2 border-r border-white/20 w-32 text-center text-white font-semibold text-xs bg-[#25276d]">Revenue</th>
                            <th class="px-2 py-2 border-r border-white/20 w-24 text-center text-white font-semibold text-xs bg-[#25276d]">ASP/kg</th>
                        <?php endfor; ?>
                        <th class="px-2 py-2 border-r border-white/20 w-24 text-center text-white bg-[#25276d] font-semibold text-xs">Tot Qty</th>
                        <th class="px-2 py-2 border-r border-white/20 w-36 text-center text-white bg-[#25276d] font-semibold text-xs">Tot Rev</th>
                        <th class="px-2 py-2 text-center text-white bg-[#25276d] font-semibold text-xs">Avg ASP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs">
                    <?php if (empty($domesticProducts ?? [])): ?>
                    <tr>
                        <td colspan="44" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
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
                        $channel = (string) ($p['id_channel'] ?? 'GT');
                        $totQty = 0.0; $totRev = 0.0;
                        foreach ($months as $mk) { $totQty += (float)($p[$mk.'_qty'] ?? 0); $totRev += (float)($p[$mk.'_rev'] ?? 0); }
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-3.5 py-2.5 text-center border-r border-gray-200 dark:border-gray-800 text-gray-500 sticky left-0 z-10 bg-white dark:bg-gray-900"><?= $idx + 1 ?></td>
                        <td class="px-3.5 py-2.5 text-center border-r border-gray-200 dark:border-gray-800 font-sans font-bold text-gray-800 dark:text-gray-200 sticky left-12 z-10 bg-white dark:bg-gray-900"><?= esc($channel) ?></td>
                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans text-gray-600 dark:text-gray-400"><?= esc($p['key_product'] ?? '-') ?></td>
                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 font-semibold text-gray-800 dark:text-gray-200"><?= esc($p['mid_product'] ?? '-') ?></td>
                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans font-medium text-gray-900 dark:text-white"><?= esc($p['product_name'] ?? '-') ?></td>
                        <?php foreach ($months as $mk):
                            $mQty = (float)($p[$mk.'_qty'] ?? 0);
                            $mRev = (float)($p[$mk.'_rev'] ?? 0);
                        ?>
                            <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800 font-mono"><?= number_format($mQty, 2) ?></td>
                            <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800 font-mono"><?= number_format($mRev, 0, ',', '.') ?></td>
                            <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800 font-mono text-amber-600 dark:text-amber-400"><?= number_format($calcAsp($mRev, $mQty, $channel), 2) ?></td>
                        <?php endforeach; ?>
                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800 font-mono font-bold bg-gray-50 dark:bg-gray-800"><?= number_format($totQty, 2) ?></td>
                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800 font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-gray-50 dark:bg-gray-800"><?= number_format($totRev, 0, ',', '.') ?></td>
                        <td class="px-2 py-2.5 text-right font-mono font-bold text-amber-600 dark:text-amber-400 bg-gray-50 dark:bg-gray-800"><?= number_format($calcAsp($totRev, $totQty, $channel), 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
