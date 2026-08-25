<div x-data="mppSummaryApp()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="hover:text-[#2F3185]">MPP</span>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold">Summary Headcount</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                MPP Summary Headcount
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Konsolidasi dan ringkasan jumlah tenaga kerja (headcount) per departemen.
            </p>
        </div>
    </div>

    <!-- Filter Department -->
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div class="w-full max-w-lg">
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Department / Cost Center</label>
            <select x-model="selectedDept" @change="fetchData()"
                class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 dark:text-white px-3.5 py-2.5 text-xs font-medium focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:bg-white outline-none transition">
                <option value="">— Pilih Cost Center —</option>
                <template x-for="item in costCenterList" :key="item.cost_center">
                    <option :value="String(item.cost_center)" x-text="`${item.no}. [${item.cost_center_sap}] ${item.cost_desc}`"></option>
                </template>
            </select>
        </div>
        <button @click="exportData()" :disabled="!selectedDept || rows.length === 0"
            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 text-xs font-semibold transition shadow-xs disabled:opacity-40 disabled:cursor-not-allowed active:scale-[0.98] shrink-0">
            <i class="fa-solid fa-file-excel"></i>
            <span>Export Data</span>
        </button>
    </div>

    <!-- Table -->
    <div class="space-y-4">
        <!-- Table Section Label (Separated from table container) -->
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Ringkasan Headcount MPP Departemen</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Konsolidasi jumlah kebutuhan tenaga kerja bulanan per departemen.</p>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs border-collapse text-gray-600 dark:text-gray-300 min-w-[1000px] whitespace-nowrap">
                    <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
                        <tr class="border-b border-white/20 bg-[#2F3185] text-white font-semibold text-xs">
                            <th rowspan="2" class="border-r border-white/20 py-3 px-4 min-w-[200px] text-white font-semibold">Staff</th>
                            <th colspan="12" class="py-2 px-2 text-center border-r border-white/20 bg-[#25276d] text-white font-semibold">Number of Headcounts</th>
                            <th rowspan="2" class="py-3 px-4 text-center border-white/20 min-w-[75px] bg-[#25276d] text-white font-bold">Total</th>
                        </tr>
                        <tr class="border-b border-white/20 bg-[#25276d] text-white text-xs font-semibold">
                            <th class="py-2 px-2 text-center border-r border-white/20 min-w-[50px] text-white font-semibold">Jan</th>
                            <th class="py-2 px-2 text-center border-r border-white/20 min-w-[50px] text-white font-semibold">Feb</th>
                            <th class="py-2 px-2 text-center border-r border-white/20 min-w-[50px] text-white font-semibold">Mar</th>
                            <th class="py-2 px-2 text-center border-r border-white/20 min-w-[50px] text-white font-semibold">Apr</th>
                            <th class="py-2 px-2 text-center border-r border-white/20 min-w-[50px] text-white font-semibold">May</th>
                            <th class="py-2 px-2 text-center border-r border-white/20 min-w-[50px] text-white font-semibold">Jun</th>
                            <th class="py-2 px-2 text-center border-r border-white/20 min-w-[50px] text-white font-semibold">Jul</th>
                            <th class="py-2 px-2 text-center border-r border-white/20 min-w-[50px] text-white font-semibold">Aug</th>
                            <th class="py-2 px-2 text-center border-r border-white/20 min-w-[50px] text-white font-semibold">Sep</th>
                            <th class="py-2 px-2 text-center border-r border-white/20 min-w-[50px] text-white font-semibold">Oct</th>
                            <th class="py-2 px-2 text-center border-r border-white/20 min-w-[50px] text-white font-semibold">Nov</th>
                            <th class="py-2 px-2 text-center border-r border-white/20 min-w-[50px] text-white font-semibold">Dec</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs font-mono">
                        <!-- Not Selected State -->
                        <tr x-show="!selectedDept" x-cloak>
                            <td colspan="14" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-users text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Silakan Pilih Department</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Pilih salah satu Department / Cost Center di atas untuk menampilkan ringkasan headcount.</p>
                                </div>
                            </td>
                        </tr>

                        <!-- Loading State -->
                        <tr x-show="selectedDept && loading" x-cloak>
                            <td colspan="14" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                                        <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data Headcount...</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sistem sedang memproses data.</p>
                                </div>
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr x-show="selectedDept && !loading && rows.length === 0" x-cloak>
                            <td colspan="14" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-inbox text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada data headcount untuk cost center ini.</p>
                                </div>
                            </td>
                        </tr>

                        <template x-if="selectedDept && !loading" x-for="(row, idx) in rows" :key="idx">
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-800 font-sans font-medium text-gray-900 dark:text-white" x-text="row.tipe_name"></td>
                                <td class="py-2.5 px-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="row.m1"></td>
                                <td class="py-2.5 px-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="row.m2"></td>
                                <td class="py-2.5 px-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="row.m3"></td>
                                <td class="py-2.5 px-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="row.m4"></td>
                                <td class="py-2.5 px-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="row.m5"></td>
                                <td class="py-2.5 px-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="row.m6"></td>
                                <td class="py-2.5 px-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="row.m7"></td>
                                <td class="py-2.5 px-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="row.m8"></td>
                                <td class="py-2.5 px-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="row.m9"></td>
                                <td class="py-2.5 px-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="row.m10"></td>
                                <td class="py-2.5 px-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="row.m11"></td>
                                <td class="py-2.5 px-2 text-center border-r border-gray-200 dark:border-gray-800" x-text="row.m12"></td>
                                <td class="py-2.5 px-4 text-center font-bold text-[#2F3185] dark:text-indigo-400 bg-gray-50/70 dark:bg-gray-800/50" x-text="row.grand_total"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot x-show="selectedDept && !loading && rows.length > 0" class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs font-mono">
                        <tr>
                            <td class="py-3 px-4 border-r border-gray-300 dark:border-gray-700 font-sans font-bold uppercase">Grand Total</td>
                            <td class="py-3 px-2 text-center border-r border-gray-300 dark:border-gray-700" x-text="totals[0]"></td>
                            <td class="py-3 px-2 text-center border-r border-gray-300 dark:border-gray-700" x-text="totals[1]"></td>
                            <td class="py-3 px-2 text-center border-r border-gray-300 dark:border-gray-700" x-text="totals[2]"></td>
                            <td class="py-3 px-2 text-center border-r border-gray-300 dark:border-gray-700" x-text="totals[3]"></td>
                            <td class="py-3 px-2 text-center border-r border-gray-300 dark:border-gray-700" x-text="totals[4]"></td>
                            <td class="py-3 px-2 text-center border-r border-gray-300 dark:border-gray-700" x-text="totals[5]"></td>
                            <td class="py-3 px-2 text-center border-r border-gray-300 dark:border-gray-700" x-text="totals[6]"></td>
                            <td class="py-3 px-2 text-center border-r border-gray-300 dark:border-gray-700" x-text="totals[7]"></td>
                            <td class="py-3 px-2 text-center border-r border-gray-300 dark:border-gray-700" x-text="totals[8]"></td>
                            <td class="py-3 px-2 text-center border-r border-gray-300 dark:border-gray-700" x-text="totals[9]"></td>
                            <td class="py-3 px-2 text-center border-r border-gray-300 dark:border-gray-700" x-text="totals[10]"></td>
                            <td class="py-3 px-2 text-center border-r border-gray-300 dark:border-gray-700" x-text="totals[11]"></td>
                            <td class="py-3 px-4 text-center font-bold text-[#2F3185] dark:text-indigo-400" x-text="totals[12]"></td>
                        </tr>
                    </tfoot>
                </table>
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
