<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="sellingReportHandler()" class="space-y-6">

    <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-default flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
            <label class="text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Cost Center:</label>
            <select x-model="selectedCostCenter" @change="fetchData()"
                    class="w-full sm:w-80 px-3.5 py-2 text-xs md:text-sm bg-gray-50 dark:bg-meta-4/40 border border-stroke dark:border-strokedark rounded-lg text-gray-900 dark:text-white focus:border-primary focus:outline-none">
                <option value="">- Pilih Cost Center -</option>
                <?php $no = 1; foreach ($dept as $depts): ?>
                    <option value="<?= esc($depts['cost_center']); ?>"><?= $no; ?>. <?= esc($depts['cost_desc']); ?></option>
                <?php $no++; endforeach; ?>
            </select>
        </div>
    </div>

    <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-default overflow-hidden">
        <div class="px-5 py-4 border-b border-stroke dark:border-strokedark">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                OPEX Selling Report: <span class="text-primary" x-text="costCenterLabel || 'All Cost Center'"></span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-primary text-white text-center font-semibold">
                        <th class="py-3 px-3 border-r border-white/20 whitespace-nowrap min-w-[200px]">MAIN ACCOUNT</th>
                        <th class="py-2 px-3 border-b border-white/20" colspan="12">BUDGET (MONTHLY)</th>
                        <th class="py-3 px-3 border-l border-white/20 min-w-[120px]">TOTAL</th>
                    </tr>
                    <tr class="bg-primary/90 text-white text-right text-[11px]">
                        <?php foreach (['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'] as $m): ?>
                            <th class="py-1.5 px-2 border-r border-white/10"><?= $m ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark text-gray-700 dark:text-gray-300">
                    <template x-if="isLoading">
                        <tr>
                            <td colspan="14" class="py-8 text-center text-gray-500">
                                <div class="inline-flex items-center gap-2">
                                    <span class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></span>
                                    <span>Memuat Data Laporan...</span>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="!isLoading && rows.length === 0">
                        <tr>
                            <td colspan="14" class="py-8 text-center text-gray-400">Silakan pilih Cost Center untuk menampilkan data laporan.</td>
                        </tr>
                    </template>
                    <template x-for="(row, idx) in rows" :key="idx">
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4/20 transition-colors">
                            <td class="py-2 px-3 font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-boxdark z-10 shadow-xs" x-text="row.main_account + ' - ' + row.cost_center_desc"></td>
                            <template x-for="m in 12" :key="m">
                                <td class="py-2 px-2 text-right" x-text="formatNum(row['isi_' + m])"></td>
                            </template>
                            <td class="py-2 px-3 text-right font-bold text-primary" x-text="formatNum(row.isi_tot)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function sellingReportHandler() {
    return {
        selectedCostCenter: '',
        costCenterLabel: '',
        isLoading: false,
        rows: [],

        fetchData() {
            if (!this.selectedCostCenter) return;
            this.isLoading = true;

            const selectEl = document.querySelector('[x-model="selectedCostCenter"]');
            this.costCenterLabel = selectEl.options[selectEl.selectedIndex].text;

            fetch('<?= base_url('opex_selling/cari_actual_table'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ dept: this.selectedCostCenter })
            })
            .then(res => res.json())
            .then(data => {
                this.rows = Array.isArray(data) ? data : [];
                this.isLoading = false;
            })
            .catch(err => {
                this.isLoading = false;
                console.error(err);
            });
        },

        formatNum(val) {
            if (!val || isNaN(val)) return '0.00';
            return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }
}
</script>
<?= $this->endSection() ?>
