<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="reportDepartmentHandler()" class="space-y-6">

    <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-default flex flex-col md:flex-row items-md-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
            <label for="typex" class="text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Cost Center:</label>
            <select id="typex" 
                    x-model="selectedCostCenter" 
                    @change="fetchData()" 
                    class="w-full sm:w-80 px-3.5 py-2 text-xs md:text-sm bg-gray-50 dark:bg-meta-4/40 border border-stroke dark:border-strokedark rounded-lg text-gray-900 dark:text-white focus:border-primary focus:outline-none">
                <option value="">- Pilih Cost Center -</option>
                <option value="0">[All Cost Center] Include New Lines</option>
                <option value="1">[All Cost Center] Only New Lines</option>
                <option value="2">[All Cost Center] Exclude New Lines</option>
                <?php $no = 1; foreach ($dept as $depts) : ?>
                    <option value="<?= esc($depts['cost_center']); ?>"><?= $no; ?>. <?= esc($depts['cost_desc']); ?></option>
                <?php $no++; endforeach; ?>
            </select>
        </div>

        <div class="flex items-center gap-2 self-end md:self-auto">
            <button type="button" @click="toggleChart('show')" class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary text-xs font-medium hover:bg-primary/20 transition-colors">Show Chart</button>
            <button type="button" @click="toggleChart('hide')" class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300 text-xs font-medium hover:bg-gray-200 transition-colors">Hide Chart</button>
            <button type="button" @click="exportExcel()" class="px-3 py-1.5 rounded-lg bg-success text-white text-xs font-medium hover:bg-opacity-90 transition-colors">Export Excel</button>
        </div>
    </div>

    <div x-show="showChart" x-transition class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-default">
        <div id="chartContainer" class="w-full min-h-[350px]"></div>
    </div>

    <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-default overflow-hidden">
        <div class="px-5 py-4 border-b border-stroke dark:border-strokedark flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                General Administrative Expense: <span class="text-primary" x-text="costCenterLabel || 'All Cost Center'"></span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-primary text-white text-center font-semibold">
                        <th class="py-3 px-3 border-r border-white/20 whitespace-nowrap min-w-[200px]" rowspan="2">General Administrative Expense</th>
                        <th class="py-3 px-3 border-r border-white/20 whitespace-nowrap" rowspan="2">Header</th>
                        <th class="py-2 px-3 border-b border-white/20" colspan="10">ACTUAL</th>
                        <th class="py-3 px-3 border-r border-l border-white/20 bg-amber-500 text-white min-w-[120px]" rowspan="2">ASSUMPTION</th>
                        <th class="py-2 px-3 border-b border-white/20" colspan="13">BUDGET</th>
                    </tr>
                    <tr class="bg-primary/90 text-white text-right text-[11px]">
                        <?php foreach (['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','AVG','TOT'] as $m) : ?>
                            <th class="py-1.5 px-2 border-r border-white/10"><?= $m ?></th>
                        <?php endforeach; ?>
                        <?php foreach (['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC','TOTAL'] as $m) : ?>
                            <th class="py-1.5 px-2 border-r border-white/10"><?= $m ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark text-gray-700 dark:text-gray-300">
                    <template x-if="isLoading">
                        <tr>
                            <td colspan="26" class="py-8 text-center text-gray-500">
                                <div class="inline-flex items-center gap-2">
                                    <span class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></span>
                                    <span>Memuat Data Laporan...</span>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="!isLoading && rows.length === 0">
                        <tr>
                            <td colspan="26" class="py-8 text-center text-gray-400">Silakan pilih Cost Center untuk menampilkan data laporan.</td>
                        </tr>
                    </template>
                    <template x-for="(row, idx) in rows" :key="idx">
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4/20 transition-colors">
                            <td class="py-2 px-3 font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-boxdark z-10 shadow-xs" x-text="row.main_account + ' - ' + row.cost_center_desc"></td>
                            <td class="py-2 px-3 text-gray-500" x-text="row.cost_center_header"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.JAN)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.FEB)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.MAR)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.APR)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.MAY)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.JUN)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.JUL)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.AUG)"></td>
                            <td class="py-2 px-2 text-right font-medium" x-text="formatNum(row.AVG)"></td>
                            <td class="py-2 px-2 text-right font-bold text-primary" x-text="formatNum(row.TOT)"></td>
                            <td class="py-2 px-2 bg-amber-50 dark:bg-amber-900/10 text-amber-700 dark:text-amber-300 font-medium" x-text="row.assumption || '-'"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.isi_1)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.isi_2)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.isi_3)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.isi_4)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.isi_5)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.isi_6)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.isi_7)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.isi_8)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.isi_9)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.isi_10)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.isi_11)"></td>
                            <td class="py-2 px-2 text-right" x-text="formatNum(row.isi_12)"></td>
                            <td class="py-2 px-2 text-right font-bold text-primary" x-text="formatNum(row.isi_tot)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/highcharts@10.3.3/highcharts.js"></script>
<script>
function reportDepartmentHandler() {
    return {
        selectedCostCenter: '',
        costCenterLabel: '',
        isLoading: false,
        showChart: true,
        rows: [],

        fetchData() {
            if (!this.selectedCostCenter) return;
            this.isLoading = true;

            const selectEl = document.getElementById('typex');
            this.costCenterLabel = selectEl.options[selectEl.selectedIndex].text;

            fetch('<?= base_url('opex_ga/cari_actual_table'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ dept: this.selectedCostCenter })
            })
            .then(res => res.json())
            .then(data => {
                this.rows = data || [];
                this.isLoading = false;
                this.renderChart();
            })
            .catch(err => {
                this.isLoading = false;
                console.error(err);
            });
        },

        formatNum(val) {
            if (!val || isNaN(val)) return '0.00';
            return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        toggleChart(action) {
            this.showChart = (action === 'show');
        },

        exportExcel() {
            if (!this.selectedCostCenter) {
                alert('Pilih Cost Center terlebih dahulu!');
                return;
            }
            window.location.href = '<?= base_url('opex_ga/export_template_opex_ga/'); ?>/' + this.selectedCostCenter;
        },

        renderChart() {
            if (!this.showChart || this.rows.length === 0) return;

            Highcharts.chart('chartContainer', {
                chart: { type: 'line', backgroundColor: 'transparent' },
                title: { text: 'OPEX GA SUMMARY BUDGET', style: { fontSize: '14px', fontWeight: 'bold' } },
                xAxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] },
                yAxis: { title: { text: 'Amount (IDR)' } },
                series: [{
                    name: 'Budget Monthly',
                    data: [1,2,3,4,5,6,7,8,9,10,11,12].map(m => {
                        return this.rows.reduce((acc, r) => acc + (parseFloat(r['isi_' + m]) || 0), 0);
                    })
                }]
            });
        }
    }
}
</script>
<?= $this->endSection() ?>