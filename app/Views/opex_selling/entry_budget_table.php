<div x-data="budgetTableSellingHandler()" class="w-full bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark shadow-default overflow-hidden">
    <form id="simpan_data_selling" @submit.prevent="saveBudget">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm border-collapse">
                <thead>
                    <tr class="bg-primary text-black text-center font-semibold">
                        <th class="py-3 px-4 border-r border-white/20 whitespace-nowrap min-w-[220px]" rowspan="2">MAIN ACCOUNT</th>
                        <th class="py-2 px-4 border-b border-white/20" colspan="12">BUDGET SELLING (MONTHLY)</th>
                        <th class="py-3 px-4 border-l border-white/20 whitespace-nowrap min-w-[120px]" rowspan="2">TOTAL</th>
                        <th class="py-3 px-4 border-l border-white/20 whitespace-nowrap min-w-[110px]" rowspan="2">DETAIL & #</th>
                    </tr>
                    <tr class="bg-primary/90 text-black text-right text-xs">
                        <?php foreach (['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'] as $m) : ?>
                            <th class="py-2 px-3 border-r border-white/10 w-20"><?= $m ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark text-gray-700 dark:text-gray-300">
                    <?php 
                    $totals = array_fill(1, 12, 0);
                    $grandTotal = 0;
                    $no = 1;
                    foreach ($filex as $row) : 
                        $rowTotal = str_replace(',', '', $row['isi_tot'] ?? 0);
                        $grandTotal += (float)$rowTotal;
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4/20 transition-colors">
                            <td class="py-2.5 px-4 font-medium text-gray-900 dark:text-white whitespace-nowrap bg-white dark:bg-boxdark sticky left-0 z-10 shadow-sm">
                                <input type="hidden" name="nomernya[]" value="<?= $no; ?>">
                                <input type="hidden" name="main_account[]" value="<?= esc($row['id_coa'] ?? $row['main_account']); ?>">
                                <input type="hidden" name="dept" value="<?= esc($dept ?? ''); ?>">
                                
                                <span class="font-bold text-primary"><?= esc($row['main_account']); ?></span> 
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">- <?= esc($row['cost_center_desc'] ?? ''); ?></span>
                            </td>

                            <?php for ($m = 1; $m <= 12; $m++) : 
                                $val = str_replace(',', '', $row['isi_' . $m] ?? 0);
                                $totals[$m] += (float)$val;
                            ?>
                                <td class="py-2.5 px-3 text-right whitespace-nowrap text-xs">
                                    <?= number_format((float)$val, 2); ?>
                                </td>
                            <?php endfor; ?>

                            <td class="py-2.5 px-4 text-right font-bold text-primary dark:text-white whitespace-nowrap bg-gray-50/50 dark:bg-meta-4/10">
                                <span><?= number_format((float)$rowTotal, 2); ?></span>
                                <input type="hidden" name="toti_aa[]" value="<?= esc($rowTotal); ?>">
                            </td>
                            <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                <button
                                    type="button"
                                    onclick="window.openSellingBreakdown && window.openSellingBreakdown(<?= (int) ($row['id'] ?? 0); ?>)"
                                    <?= empty($row['id']) ? 'disabled' : ''; ?>
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-primary dark:text-white text-xs font-medium hover:bg-primary hover:text-white dark:hover:bg-primary dark:hover:text-white transition-colors <?= empty($row['id']) ? 'opacity-40 cursor-not-allowed' : ''; ?>"
                                    title="Breakdown Detail Item"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-6 0a2 2 0 002 2h2a2 2 0 002-2m-6 0a2 2 0 012-2h2a2 2 0 012 2m-6 0h6"/></svg>
                                    Detail
                                </button>
                            </td>
                        </tr>
                    <?php 
                        $no++;
                    endforeach; 
                    ?>
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 dark:bg-meta-4 text-gray-900 dark:text-white font-bold border-t-2 border-stroke dark:border-strokedark">
                        <td class="py-3 px-4 text-right sticky left-0 z-10 bg-gray-100 dark:bg-meta-4">TOTAL SELLING</td>
                        <?php for ($m = 1; $m <= 12; $m++) : ?>
                            <td class="py-3 px-3 text-right text-xs whitespace-nowrap">
                                <?= number_format($totals[$m], 2); ?>
                            </td>
                        <?php endfor; ?>
                        <td class="py-3 px-4 text-right text-primary dark:text-success whitespace-nowrap">
                            <?= number_format($grandTotal, 2); ?>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <?php
            $pages = max(1, (int) ceil(($total ?? 0) / max(1, $perPage ?? 10)));
            $pg    = (int) ($page ?? 1);
            $prev  = max(1, $pg - 1);
            $nextP = min($pages, $pg + 1);
        ?>
        <div class="p-3 px-4 bg-gray-50 dark:bg-meta-4/30 border-t border-stroke dark:border-strokedark flex items-center justify-between text-xs text-gray-500">
            <span>Halaman <?= $pg ?> dari <?= $pages ?></span>
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="window.loadSellingBudgetPage &amp;&amp; window.loadSellingBudgetPage(<?= $prev ?>)" class="px-2.5 py-1 rounded-lg border border-stroke dark:border-strokedark hover:bg-white dark:hover:bg-meta-4 <?= $pg <= 1 ? 'opacity-40 pointer-events-none' : '' ?>">Prev</button>
                <?php for ($p = 1; $p <= $pages; $p++): ?>
                    <button type="button" onclick="window.loadSellingBudgetPage &amp;&amp; window.loadSellingBudgetPage(<?= $p ?>)" class="px-2.5 py-1 rounded-lg border <?= $p === $pg ? 'bg-primary text-white font-bold' : 'border-stroke dark:border-strokedark hover:bg-white dark:hover:bg-meta-4' ?>"><?= $p ?></button>
                <?php endfor; ?>
                <button type="button" onclick="window.loadSellingBudgetPage &amp;&amp; window.loadSellingBudgetPage(<?= $nextP ?>)" class="px-2.5 py-1 rounded-lg border border-stroke dark:border-strokedark hover:bg-white dark:hover:bg-meta-4 <?= $pg >= $pages ? 'opacity-40 pointer-events-none' : '' ?>">Next</button>
            </div>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-meta-4/30 border-t border-stroke dark:border-strokedark flex justify-end">
            <button type="submit" :disabled="isSaving" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white font-medium hover:bg-opacity-90 transition-all disabled:opacity-50">
                <span x-text="isSaving ? 'Saving...' : 'Save Budget Selling Data'">Save Budget Selling Data</span>
            </button>
        </div>
    </form>
</div>

<script>
function budgetTableSellingHandler() {
    return {
        isSaving: false,
        saveBudget() {
            if (!window.confirm('Are you sure you want to save budget selling data?')) return;

            this.isSaving = true;
            const formData = new FormData(document.getElementById('simpan_data_selling'));

            fetch('<?= base_url('opex_selling/saveBudget'); ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                this.isSaving = false;
                window.showToast ? window.showToast('success', data.message || 'Data successfully saved!') : alert('Data successfully saved!');
            })
            .catch(err => {
                this.isSaving = false;
                window.showToast ? window.showToast('error', 'An error occurred while saving.') : alert('An error occurred while saving.');
                console.error(err);
            });
        }
    }
}
</script>