<div x-data="budgetTableHandler()" class="w-full bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark shadow-default overflow-hidden">
    <form id="simpan_data_<?= esc($header ?? 'default'); ?>" @submit.prevent="saveBudget">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm border-collapse">
                <thead>
                    <tr class="bg-primary text-white text-center font-semibold">
                        <th class="py-3 px-4 border-r border-white/20 whitespace-nowrap min-w-[220px]" rowspan="2">MAIN ACCOUNT</th>
                        <th class="py-2 px-4 border-b border-white/20" colspan="12">BUDGET (MONTHLY)</th>
                        <th class="py-3 px-4 border-l border-white/20 whitespace-nowrap min-w-[120px]" rowspan="2">TOTAL</th>
                    </tr>
                    <tr class="bg-primary/90 text-white text-right text-xs">
                        <th class="py-2 px-3 border-r border-white/10 w-20">JAN</th>
                        <th class="py-2 px-3 border-r border-white/10 w-20">FEB</th>
                        <th class="py-2 px-3 border-r border-white/10 w-20">MAR</th>
                        <th class="py-2 px-3 border-r border-white/10 w-20">APR</th>
                        <th class="py-2 px-3 border-r border-white/10 w-20">MAY</th>
                        <th class="py-2 px-3 border-r border-white/10 w-20">JUN</th>
                        <th class="py-2 px-3 border-r border-white/10 w-20">JUL</th>
                        <th class="py-2 px-3 border-r border-white/10 w-20">AUG</th>
                        <th class="py-2 px-3 border-r border-white/10 w-20">SEP</th>
                        <th class="py-2 px-3 border-r border-white/10 w-20">OCT</th>
                        <th class="py-2 px-3 border-r border-white/10 w-20">NOV</th>
                        <th class="py-2 px-3 border-r border-white/10 w-20">DEC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark text-gray-700 dark:text-gray-300">
                    <?php 
                    $totals = array_fill(1, 12, 0);
                    $grandTotal = 0;
                    $no = 1;
                    foreach ($filex as $index => $row) : 
                        $rowTotal = str_replace(',', '', $row['isi_tot'] ?? 0);
                        $grandTotal += (float)$rowTotal;
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4/20 transition-colors">
                            <td class="py-2.5 px-4 font-medium text-gray-900 dark:text-white whitespace-nowrap bg-white dark:bg-boxdark sticky left-0 z-10 shadow-sm">
                                <input type="hidden" name="nomernya[]" value="<?= $no; ?>">
                                <input type="hidden" name="main_account[]" value="<?= esc($row['main_account']); ?>">
                                <input type="hidden" name="idx" value="<?= esc($idx ?? ''); ?>">
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
                        </tr>
                    <?php 
                        $no++;
                    endforeach; 
                    ?>
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 dark:bg-meta-4 text-gray-900 dark:text-white font-bold border-t-2 border-stroke dark:border-strokedark">
                        <td class="py-3 px-4 text-right sticky left-0 z-10 bg-gray-100 dark:bg-meta-4">TOTAL</td>
                        <?php for ($m = 1; $m <= 12; $m++) : ?>
                            <td class="py-3 px-3 text-right text-xs whitespace-nowrap">
                                <?= number_format($totals[$m], 2); ?>
                            </td>
                        <?php endfor; ?>
                        <td class="py-3 px-4 text-right text-primary dark:text-success whitespace-nowrap">
                            <?= number_format($grandTotal, 2); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-meta-4/30 border-t border-stroke dark:border-strokedark flex justify-end">
            <button type="submit" :disabled="isSaving" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-opacity-90 transition-all disabled:opacity-50">
                <svg x-show="!isSaving" class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                    <path d="M0 2C0 .9.9 0 2 0h12l4 4v14a2 2 0 01-2 2H2a2 2 0 01-2-2V2zm12 0v4h4l-4-4zM4 16h12v-6H4v6zm2-10h6V2H6v4z"/>
                </svg>
                <span x-text="isSaving ? 'Saving...' : 'Save Budget Data'">Save Budget Data</span>
            </button>
        </div>
    </form>
</div>

<script>
function budgetTableHandler() {
    return {
        isSaving: false,
        saveBudget() {
            if (!confirm('Are you sure you want to save this budget data?')) return;
            
            this.isSaving = true;
            const formData = new FormData(document.getElementById('simpan_data_<?= esc($header ?? 'default'); ?>'));

            fetch('<?= base_url('opex_ga/save_budget'); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.isSaving = false;
                alert('Successfully saved budget data!');
            })
            .catch(err => {
                this.isSaving = false;
                alert('An error occurred while saving.');
                console.error(err);
            });
        }
    }
}
</script>