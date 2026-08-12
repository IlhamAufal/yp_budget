<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="mppSummaryApp()" x-init="init()" class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <i class="fas fa-users text-lg text-primary"></i>
                </div>
                <h2 class="text-title-md2 font-bold text-black dark:text-white">Man Power Planning - Summary Headcount</h2>
            </div>
            <nav class="mt-1">
                <ol class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    <li><a class="hover:text-primary" href="<?= base_url('dashboard') ?>"><i class="fas fa-home mr-1"></i>Home</a></li>
                    <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                    <li class="text-primary font-semibold">MPP Summary</li>
                </ol>
            </nav>
        </div>
        <div class="flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-1 dark:bg-boxdark dark:text-gray-200">
            <i class="fas fa-calendar-alt text-primary"></i>
            <span>Budget Plan Year :</span>
            <span class="text-red-500 font-bold"><?= esc($workingYear) ?></span>
        </div>
    </div>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="p-6 space-y-6">

            <!-- Filter Cost Center -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="w-full sm:w-1/2 lg:w-1/3">
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-black dark:text-white">Department</label>
                    <select x-model="selectedDept" @change="fetchData()"
                        class="w-full rounded border border-stroke bg-white px-4 py-2.5 text-sm font-medium outline-none transition focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                        <option value="">-- Pilih Cost Center --</option>
                        <template x-for="item in costCenterList" :key="item.cost_center">
                            <option :value="String(item.cost_center)" x-text="`${item.no}. [${item.cost_center_sap}]${item.cost_desc}`"></option>
                        </template>
                    </select>
                </div>
                <button @click="exportData()" :disabled="!selectedDept || rows.length === 0"
                    class="inline-flex items-center justify-center gap-2 rounded bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition shrink-0 shadow disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-emerald-600">
                    <i class="fas fa-file-excel"></i> Export Data
                </button>
            </div>
            <!-- Table -->
            <div x-show="selectedDept" x-cloak class="max-w-full overflow-x-auto rounded-sm border border-stroke dark:border-strokedark">
                <table class="w-full table-auto text-left text-xs border-collapse">
                    <thead class="bg-gray-2 dark:bg-meta-4">
                        <tr class="text-black dark:text-white font-bold uppercase border-b border-stroke dark:border-strokedark">
                            <th rowspan="2" class="py-3 px-4 border-r border-stroke dark:border-strokedark min-w-[180px]">Staff</th>
                            <th colspan="12" class="py-2 px-2 text-center border-b border-r border-stroke dark:border-strokedark bg-blue-50/60 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300">Number of Headcounts</th>
                            <th rowspan="2" class="py-3 px-3 text-center border-r border-stroke dark:border-strokedark min-w-[70px] bg-gray-100 dark:bg-meta-4">TOTAL</th>
                        </tr>
                        <tr class="bg-gray-50 text-gray-600 dark:bg-meta-4/80 dark:text-gray-400 font-bold uppercase text-[10px] border-b border-stroke dark:border-strokedark">
                            <th class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[50px]">JAN</th>
                            <th class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[50px]">FEB</th>
                            <th class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[50px]">MAR</th>
                            <th class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[50px]">APR</th>
                            <th class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[50px]">MAY</th>
                            <th class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[50px]">JUN</th>
                            <th class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[50px]">JUL</th>
                            <th class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[50px]">AUG</th>
                            <th class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[50px]">SEP</th>
                            <th class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[50px]">OCT</th>
                            <th class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[50px]">NOV</th>
                            <th class="py-2 px-2 text-center border-r border-stroke dark:border-strokedark min-w-[50px]">DEC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr x-show="loading" x-cloak>
                            <td colspan="14" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                <i class="fa-solid fa-spinner fa-spin text-xl mb-2"></i>
                                <p class="font-semibold text-sm">Memuat data...</p>
                            </td>
                        </tr>
                        <tr x-show="!loading && rows.length === 0" x-cloak>
                            <td colspan="14" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                <i class="fa-solid fa-inbox text-xl mb-2"></i>
                                <p class="font-semibold text-sm">Tidak ada data headcount untuk cost center ini.</p>
                            </td>
                        </tr>
                        <template x-for="(row, idx) in rows" :key="idx">
                            <tr class="border-b border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4 transition-colors">
                                <td class="py-2.5 px-4 border-r border-stroke dark:border-strokedark font-medium text-black dark:text-white" x-text="row.tipe_name"></td>
                                <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="row.m1"></td>
                                <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="row.m2"></td>
                                <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="row.m3"></td>
                                <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="row.m4"></td>
                                <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="row.m5"></td>
                                <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="row.m6"></td>
                                <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="row.m7"></td>
                                <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="row.m8"></td>
                                <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="row.m9"></td>
                                <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="row.m10"></td>
                                <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="row.m11"></td>
                                <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="row.m12"></td>
                                <td class="py-2.5 px-3 text-center border-r border-stroke dark:border-strokedark font-mono font-bold bg-gray-50 dark:bg-meta-4" x-text="row.grand_total"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr x-show="!loading && rows.length > 0" class="bg-gray-100/80 dark:bg-meta-4 font-bold text-black dark:text-white border-t-2 border-stroke dark:border-strokedark">
                            <td class="py-3 px-4 border-r border-stroke dark:border-strokedark uppercase text-right">Grand Total</td>
                            <td class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="totals[0]"></td>
                            <td class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="totals[1]"></td>
                            <td class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="totals[2]"></td>
                            <td class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="totals[3]"></td>
                            <td class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="totals[4]"></td>
                            <td class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="totals[5]"></td>
                            <td class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="totals[6]"></td>
                            <td class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="totals[7]"></td>
                            <td class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="totals[8]"></td>
                            <td class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="totals[9]"></td>
                            <td class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="totals[10]"></td>
                            <td class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark font-mono" x-text="totals[11]"></td>
                            <td class="py-3 px-3 text-center border-r border-stroke dark:border-strokedark font-mono font-bold bg-gray-100 dark:bg-meta-4" x-text="totals[12]"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Empty state when no CC selected -->
            <div x-show="!selectedDept" class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-gray-200 rounded-sm dark:border-strokedark">
                <i class="fa-solid fa-users text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Silakan pilih Department untuk melihat data Summary Headcount.</p>
            </div>

        </div>
    </div>
</div>

<script>
function mppSummaryApp() {
    return {
        costCenterList: <?= json_encode(array_map(fn($d, $idx) => [
            'cost_center'     => $d['cost_center'],
            'cost_center_sap' => $d['cost_center_sap'] ?? $d['cost_center'],
            'cost_desc'       => $d['cost_desc'] ?? '',
            'no'              => $idx + 1,
        ], $costCenterList ?? [], array_keys($costCenterList ?? [])), JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP) ?>,
        selectedDept: '',
        rows: [],
        totals: Array(13).fill(0),
        loading: false,

        init() {},

        async fetchData() {
            if (!this.selectedDept) {
                this.rows = [];
                this.totals = Array(13).fill(0);
                return;
            }
            this.loading = true;
            try {
                const res = await fetch(`<?= base_url('mpp/getViewData') ?>?id_dept=${encodeURIComponent(this.selectedDept)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                if (json.status === 'success') {
                    this.rows = json.data || [];
                    this.totals = json.totals || Array(13).fill(0);
                }
            } catch (e) {
                console.error('Gagal memuat data:', e);
                this.rows = [];
                this.totals = Array(13).fill(0);
            } finally {
                this.loading = false;
            }
        },

        exportData() {
            if (!this.selectedDept || this.rows.length === 0) return;
            // Build CSV and download
            const months = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
            let csv = 'Staff,' + months.join(',') + ',TOTAL\n';
            for (const row of this.rows) {
                csv += `"${row.tipe_name}",${row.m1},${row.m2},${row.m3},${row.m4},${row.m5},${row.m6},${row.m7},${row.m8},${row.m9},${row.m10},${row.m11},${row.m12},${row.grand_total}\n`;
            }
            csv += `"Grand Total",${this.totals.join(',')}\n`;

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            const ccName = this.costCenterList.find(c => String(c.cost_center) === this.selectedDept)?.cost_desc || this.selectedDept;
            a.download = `MPP_Summary_${ccName.replace(/\s+/g, '_')}.csv`;
            a.click();
            URL.revokeObjectURL(url);
        }
    }
}
</script>
<?= $this->endSection() ?>
