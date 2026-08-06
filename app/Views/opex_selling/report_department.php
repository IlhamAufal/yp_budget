<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="sellingReportHandler()" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">

    <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
            <label class="text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Cost Center:</label>
            <select x-model="selectedCostCenter" @change="fetchData()"
                    class="w-full sm:w-80 px-3.5 py-2 text-xs md:text-sm bg-gray-50 dark:bg-meta-4/40 border border-stroke dark:border-strokedark rounded-lg text-gray-900 dark:text-white focus:border-primary focus:outline-none">
                <option value="">- Pilih Cost Center -</option>
                <?php $no = 1; foreach ($dept as $depts): ?>
                    <option value="<?= esc($depts['cost_center']); ?>"><?= $no; ?>. <?= esc($depts['cc_code']); ?> - <?= esc($depts['cost_desc']); ?></option>
                <?php $no++; endforeach; ?>
            </select>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
            <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                OPEX Selling Report: <span class="text-primary font-mono ml-1" x-text="costCenterLabel || 'All Cost Center'"></span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-gray-100/80 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 text-[11px] font-bold tracking-wider uppercase border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th rowspan="2" class="px-5 py-3.5 font-bold sticky left-0 z-10 bg-gray-100 dark:bg-gray-800 min-w-[240px] text-left border-r border-gray-200/60 dark:border-gray-700/60 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.06)]">
                            MAIN ACCOUNT
                        </th>
                        <th colspan="12" class="px-3 py-2.5 text-center border-b border-gray-200/80 dark:border-gray-700 font-bold tracking-wider">
                            BUDGET (MONTHLY)
                        </th>
                        <th rowspan="2" class="px-5 py-3.5 text-right font-extrabold bg-gray-200/60 dark:bg-gray-800 text-gray-900 dark:text-white sticky right-0 z-10 min-w-[130px] border-l border-gray-200/60 dark:border-gray-700/60 shadow-[-2px_0_4px_-2px_rgba(0,0,0,0.06)]">
                            TOTAL
                        </th>
                    </tr>
                    <tr class="bg-gray-50/80 dark:bg-gray-800/60 text-right text-[11px]">
                        <?php foreach (['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'] as $m): ?>
                            <th class="px-3.5 py-2.5 border-r border-gray-200/40 dark:border-gray-700/40 whitespace-nowrap min-w-[90px]"><?= $m ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/80 dark:divide-gray-800/80 text-gray-700 dark:text-gray-300">
                    <template x-if="isLoading">
                        <tr>
                            <td colspan="14" class="py-16 text-center text-gray-500 dark:text-gray-400">
                                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                    <span class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></span>
                                    <span class="text-xs font-semibold">Memuat Data Laporan...</span>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="!isLoading && rows.length === 0">
    <tr>
        <td colspan="14" class="px-6 py-14">
            <div class="flex flex-col items-center justify-center text-center">

                <!-- Icon -->
                <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl border border-gray-200 bg-gray-50 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <svg class="h-8 w-8 text-gray-400 dark:text-gray-500"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.7"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>

                <!-- Title -->
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200">
                    Silakan pilih Cost Center
                </h3>

                <!-- Description -->
                <p class="mt-2 max-w-md text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Pilih <span class="font-medium">Cost Center</span> di atas untuk menampilkan rincian laporan
                    <span class="font-medium">OPEX Selling</span>.
                </p>

            </div>
        </td>
    </tr>
</template>
                    <template x-for="(row, idx) in rows" :key="idx">
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-5 py-3.5 font-semibold text-gray-900 dark:text-white sticky left-0 z-10 bg-white dark:bg-gray-900 border-r border-gray-200/40 dark:border-gray-800 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.04)]" x-text="row.main_account + ' - ' + row.cost_center_desc"></td>
                            <template x-for="m in 12" :key="m">
                                <td class="px-3.5 py-3.5 text-right font-mono font-medium tabular-nums text-gray-700 dark:text-gray-300" x-text="formatNum(row['isi_' + m])"></td>
                            </template>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-gray-900 dark:text-white bg-gray-50/80 dark:bg-gray-800/40 sticky right-0 z-10 shadow-[-2px_0_4px_-2px_rgba(0,0,0,0.04)]" x-text="formatNum(row.isi_tot)"></td>
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
