<div x-data="keyProductTab()" x-init="initData()" class="space-y-6">

    <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-end gap-5 flex-1">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Report Key Product 12 Bulan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rekapitulasi kuantiti, revenue, dan average ASP per Key Product.</p>
            </div>
            <div class="w-full sm:w-56">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Channel</label>
                <select x-model="selectedChannel" @change="fetchData()" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 py-2.5 px-3.5 font-semibold transition-all">
                    <option value="domestic">Domestic (YTI)</option>
                    <option value="international">International</option>
                </select>
            </div>
        </div>
        <div class="shrink-0">
            <button type="button" @click="exportData()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98]">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Data</span>
            </button>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Matriks Kinerja Key Product (12 Bulan)</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Performa bulanan per kategori produk kunci.</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[2400px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th rowspan="2" class="px-3.5 py-3 text-center w-12 border-r border-white/20 text-white font-semibold">No.</th>
                        <th rowspan="2" class="px-4 py-3 min-w-[200px] border-r border-white/20 text-white font-semibold">Key Product</th>
                        <?php
                            $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                            foreach ($months as $m):
                        ?>
                        <th colspan="3" class="px-2 py-2 text-center border-r border-white/20 text-white font-semibold"><?= $m ?></th>
                        <?php endforeach; ?>
                        <th colspan="3" class="px-3 py-2 text-center bg-[#25276d] text-white font-bold">Total Year</th>
                    </tr>
                    <tr class="bg-[#25276d] text-xs font-semibold text-white/90 border-b border-white/20">
                        <?php for ($i = 0; $i < 13; $i++): ?>
                        <th class="px-2 py-2 text-center border-r border-white/20 min-w-[80px]">QTY</th>
                        <th class="px-2 py-2 text-center border-r border-white/20 min-w-[100px]">Revenue</th>
                        <th class="px-2 py-2 text-center border-r border-white/20 min-w-[80px]">ASP/kg</th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <tr x-show="rows.length === 0">
                        <td colspan="41" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-award text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data Key Product</p>
                            </div>
                        </td>
                    </tr>
                    <template x-for="(row, idx) in rows" :key="row.id">
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-3.5 py-2.5 text-center border-r border-gray-200 dark:border-gray-800 font-sans text-gray-500" x-text="idx + 1"></td>
                            <td class="px-4 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans font-semibold text-gray-900 dark:text-white" x-text="row.key_product"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[1].qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold" x-text="fmt(row.monthly[1].revenue)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="fmt(row.monthly[1].asp)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[2].qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold" x-text="fmt(row.monthly[2].revenue)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="fmt(row.monthly[2].asp)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[3].qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold" x-text="fmt(row.monthly[3].revenue)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="fmt(row.monthly[3].asp)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[4].qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold" x-text="fmt(row.monthly[4].revenue)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="fmt(row.monthly[4].asp)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[5].qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold" x-text="fmt(row.monthly[5].revenue)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="fmt(row.monthly[5].asp)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[6].qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold" x-text="fmt(row.monthly[6].revenue)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="fmt(row.monthly[6].asp)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[7].qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold" x-text="fmt(row.monthly[7].revenue)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="fmt(row.monthly[7].asp)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[8].qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold" x-text="fmt(row.monthly[8].revenue)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="fmt(row.monthly[8].asp)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[9].qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold" x-text="fmt(row.monthly[9].revenue)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="fmt(row.monthly[9].asp)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[10].qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold" x-text="fmt(row.monthly[10].revenue)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="fmt(row.monthly[10].asp)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[11].qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold" x-text="fmt(row.monthly[11].revenue)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="fmt(row.monthly[11].asp)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[12].qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-emerald-600 dark:text-emerald-400 font-semibold" x-text="fmt(row.monthly[12].revenue)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 text-amber-600 dark:text-amber-400 font-semibold" x-text="fmt(row.monthly[12].asp)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 font-bold bg-gray-50 dark:bg-gray-800" x-text="fmt(row.total_qty)"></td>
                            <td class="px-2 py-2 text-right border-r border-gray-200 dark:border-gray-800 font-bold text-emerald-600 dark:text-emerald-400 bg-gray-50 dark:bg-gray-800" x-text="fmt(row.total_revenue)"></td>
                            <td class="px-2 py-2 text-right font-bold text-amber-600 dark:text-amber-400 bg-gray-50 dark:bg-gray-800" x-text="fmt(row.total_asp)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function keyProductTab() {
    return {
        selectedChannel: 'domestic',
        rows: [],

        initData() {
            this.rows = [
                { id: 1, key_product: 'GUMMY', total_qty: 0, total_revenue: 0, total_asp: 0, monthly: this.emptyMonthly() },
                { id: 2, key_product: 'BOLI', total_qty: 0, total_revenue: 0, total_asp: 0, monthly: this.emptyMonthly() },
                { id: 3, key_product: 'EXTRUDER', total_qty: 0, total_revenue: 0, total_asp: 0, monthly: this.emptyMonthly() },
            ];
        },

        emptyMonthly() {
            const m = {};
            for (let i = 1; i <= 12; i++) m[i] = { qty: 0, revenue: 0, asp: 0 };
            return m;
        },

        fetchData() {
            // Will fetch from backend based on selectedChannel
        },

        fmt(val) { return new Intl.NumberFormat('id-ID').format(Math.round(val || 0)); },
        exportData() { alert('Exporting Key Product report...'); }
    }
}
</script>
