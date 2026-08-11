<div x-data="keyProductTab()" x-init="initData()" class="space-y-6">

    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                Report Key Product
            </span>
            <div class="w-48">
                <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-400 mb-0.5">Channel</label>
                <select x-model="selectedChannel" @change="fetchData()" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary font-semibold">
                    <option value="domestic">Domestic (YTI)</option>
                    <option value="international">International</option>
                </select>
            </div>
        </div>
        <button type="button" @click="exportData()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
            <i class="fa-solid fa-file-excel text-green-600"></i>
            <span>Export Data</span>
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-[10px] border-collapse min-w-[2400px]">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 font-bold border-b border-gray-200 dark:border-gray-700">
                        <th rowspan="2" class="p-2 text-center w-10 border-r border-gray-200 dark:border-gray-700">No.</th>
                        <th rowspan="2" class="p-2 min-w-[150px] border-r border-gray-200 dark:border-gray-700">Key Product</th>
                        <?php
                            $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                            foreach ($months as $m):
                        ?>
                        <th colspan="3" class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 bg-blue-50/60 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300"><?= $m ?></th>
                        <?php endforeach; ?>
                        <th colspan="3" class="p-1.5 text-center bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white font-bold">Total</th>
                    </tr>
                    <tr class="bg-gray-100/80 dark:bg-gray-700/30 text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-700">
                        <?php for ($i = 0; $i < 13; $i++): ?>
                        <th class="p-1 text-center border-r border-gray-200 dark:border-gray-700 w-14">QTY</th>
                        <th class="p-1 text-center border-r border-gray-200 dark:border-gray-700 w-16">Revenue</th>
                        <th class="p-1 text-center border-r border-gray-200 dark:border-gray-700 w-14">ASP/kg</th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr x-show="rows.length === 0">
                        <td colspan="41" class="p-8 text-center text-gray-400">Tidak ada data</td>
                    </tr>
                    <template x-for="(row, idx) in rows" :key="row.id">
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="p-2 text-center border-r border-gray-200 dark:border-gray-700 font-medium" x-text="idx + 1"></td>
                            <td class="p-2 border-r border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white" x-text="row.key_product"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[1].qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[1].revenue)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[1].asp)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[2].qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[2].revenue)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[2].asp)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[3].qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[3].revenue)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[3].asp)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[4].qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[4].revenue)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[4].asp)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[5].qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[5].revenue)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[5].asp)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[6].qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[6].revenue)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[6].asp)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[7].qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[7].revenue)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[7].asp)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[8].qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[8].revenue)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[8].asp)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[9].qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[9].revenue)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[9].asp)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[10].qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[10].revenue)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[10].asp)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[11].qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[11].revenue)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[11].asp)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[12].qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[12].revenue)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[12].asp)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono font-bold bg-gray-50 dark:bg-gray-700/50" x-text="fmt(row.total_qty)"></td>
                            <td class="p-1 text-right border-r border-gray-200 dark:border-gray-700 font-mono font-bold bg-gray-50 dark:bg-gray-700/50" x-text="fmt(row.total_revenue)"></td>
                            <td class="p-1 text-right font-mono font-bold bg-gray-50 dark:bg-gray-700/50" x-text="fmt(row.total_asp)"></td>
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
