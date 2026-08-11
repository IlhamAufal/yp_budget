<div class="space-y-6">
    <!-- Toolbar & Filter Area -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="flex flex-wrap items-center gap-3 flex-1">
            <div class="flex items-center gap-2 min-w-[220px]">
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5 shrink-0">
                    <i class="fa-solid fa-filter text-primary"></i> Channel:
                </label>
                <select x-model="filters.channel" @change="applyFilters()" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-3">
                    <option value="">EXPORT (Sales International)</option>
                </select>
            </div>
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()" placeholder="Cari SKU / Nama Produk..." class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-gray-400"></i>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" @click="downloadExportTemplate()" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel"></i> Template Data Export
            </button>
            <button type="button" @click="saveChanges()" class="px-3.5 py-2 bg-primary hover:bg-primary/90 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800 overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/40">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-table text-primary"></i>
                <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Target Sales International 12 Bulan (Valas USD $)</h3>
            </div>
            <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium italic">ASP: Revenue / Qty ($/Kg)</span>
        </div>
        <div class="overflow-x-auto scrollbar-thin max-h-[600px]">
            <table class="w-full text-left text-[11px] border-collapse min-w-[1400px]">
                <thead class="bg-gray-100/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-300 font-bold uppercase border-b border-gray-300 dark:border-gray-700 sticky top-0 z-20">
                    <tr>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 w-10 text-center">No.</th>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 w-20 text-center">CHANNEL</th>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[100px]">KEY PRODUCT</th>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[80px]">CODE INV</th>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 min-w-[200px]">PRODUCT NAME</th>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 w-24 text-right">TOT QTY</th>
                        <th class="p-2.5 border-r border-gray-300 dark:border-gray-700 w-28 text-right text-amber-700">TOT REV ($)</th>
                        <th class="p-2.5 w-20 text-right text-emerald-700">AVG ASP ($)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs">
                    <?php if (empty($exportProducts)): ?>
                    <tr><td colspan="8" class="p-8 text-center text-gray-400">Tidak ada data produk export.</td></tr>
                    <?php else: ?>
                    <?php foreach ($exportProducts as $idx => $p):
                        $totQty = 0; $totRev = 0;
                        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
                        foreach ($months as $mk) { $totQty += (float)($p[$mk.'_qty'] ?? 0); $totRev += (float)($p[$mk.'_rev'] ?? 0); }
                        $avgAsp = $totQty > 0 ? ($totRev / $totQty) : 0;
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="p-2 text-center border-r border-gray-200 dark:border-gray-800 text-gray-500"><?= $idx + 1 ?></td>
                        <td class="p-2 text-center border-r border-gray-200 dark:border-gray-800 font-bold text-amber-700">EXPORT</td>
                        <td class="p-2 border-r border-gray-200 dark:border-gray-800"><?= esc($p['key_product'] ?? '-') ?></td>
                        <td class="p-2 border-r border-gray-200 dark:border-gray-800 font-semibold"><?= esc($p['mid_product'] ?? '-') ?></td>
                        <td class="p-2 border-r border-gray-200 dark:border-gray-800 font-medium text-gray-900 dark:text-white"><?= esc($p['product_name'] ?? '-') ?></td>
                        <td class="p-2 border-r border-gray-200 dark:border-gray-800 text-right font-mono font-bold"><?= number_format($totQty, 2) ?></td>
                        <td class="p-2 border-r border-gray-200 dark:border-gray-800 text-right font-mono font-bold text-amber-600">$ <?= number_format($totRev, 2) ?></td>
                        <td class="p-2 text-right font-mono font-bold text-emerald-600">$ <?= number_format($avgAsp, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
