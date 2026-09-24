<div x-data="salesSummaryTab()" x-init="initData()" class="space-y-6">

    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Sales Grand Summary & Revenue Target</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Konsolidasi total Domestic Sales, International Sales (Valas/IDR), dan penyesuaian diskon.</p>
        </div>

        <div class="shrink-0">
            <button type="button" @click="exportExcel()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98]">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Summary</span>
            </button>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Rincian Ringkasan Sales per Segmen (12 Bulan)</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rincian target bulanan QTY, Revenue, dan ASP per segmen kanal penjualan.</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[4200px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs sticky top-0 z-20">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th rowspan="2" class="px-4 py-3 sticky left-0 z-30 bg-[#2F3185] text-white font-semibold min-w-[240px] border-r border-white/20">Segmen Penjualan</th>
                        <template x-for="month in monthNames" :key="month">
                            <th colspan="3" class="px-3 py-2.5 text-center text-white font-semibold border-r border-white/20" x-text="month"></th>
                        </template>
                        <th colspan="3" class="px-3 py-2.5 text-center text-white font-bold bg-[#25276d] border-l border-white/20">Annual Total</th>
                    </tr>
                    <tr class="bg-[#25276d] text-white text-xs font-semibold">
                        <?php for ($i = 0; $i < 12; $i++): ?>
                            <th class="px-2 py-2 border-r border-white/20 w-20 text-center text-white font-semibold text-xs bg-[#25276d]">Qty</th>
                            <th class="px-2 py-2 border-r border-white/20 w-28 text-center text-white font-semibold text-xs bg-[#25276d]">Revenue</th>
                            <th class="px-2 py-2 border-r border-white/20 w-24 text-center text-white font-semibold text-xs bg-[#25276d]">ASP/kg</th>
                        <?php endfor; ?>
                        <th class="px-2 py-2 border-r border-white/20 w-24 text-center text-white bg-[#25276d] font-semibold text-xs">Tot Qty</th>
                        <th class="px-2 py-2 border-r border-white/20 w-32 text-center text-white bg-[#25276d] font-semibold text-xs">Tot Rev</th>
                        <th class="px-2 py-2 text-center text-white bg-[#25276d] font-semibold text-xs">Avg ASP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-for="row in rows" :key="row.id">
                        <tr :class="row.is_total ? 'bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold border-t-2 border-[#2F3185]/30 text-gray-900 dark:text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'">
                            <td class="px-4 py-3 sticky left-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 font-sans font-semibold" :class="row.is_total ? 'text-[#2F3185] dark:text-indigo-400 font-bold' : 'text-gray-900 dark:text-white'" x-text="row.segment_name"></td>
                            <template x-for="cell in metricCols" :key="cell.m + '-' + cell.k">
                                <td class="px-2 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="cell.k === 'qty' ? formatNumber(row.monthly[cell.m].qty) : cell.k === 'rev' ? formatNumber(row.monthly[cell.m].rev) : formatNumber(calcAsp(row.monthly[cell.m].rev, row.monthly[cell.m].qty))"></td>
                            </template>
                            <td class="px-2 py-3 text-right font-bold bg-gray-50 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-800" x-text="formatNumber(rowTotalQty(row))"></td>
                            <td class="px-2 py-3 text-right font-bold bg-gray-50 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-800" :class="row.is_total ? 'text-[#2F3185] dark:text-indigo-400' : 'text-emerald-600 dark:text-emerald-400'" x-text="formatNumber(rowTotalRev(row))"></td>
                            <td class="px-2 py-3 text-right font-bold bg-gray-50 dark:bg-gray-800 text-amber-600 dark:text-amber-400" x-text="formatNumber(calcAsp(rowTotalRev(row), rowTotalQty(row)))"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function salesSummaryTab() {
    return {
        monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        metricCols: (() => {
            const arr = [];
            for (let m = 1; m <= 12; m++) arr.push({ m, k: 'qty' }, { m, k: 'rev' }, { m, k: 'asp' });
            return arr;
        })(),
        summary: { total_volume: 18000, gross_revenue: 2597400000, total_discount: 120000000, net_sales: 2477400000 },
        rows: [],

        initData() {
            this.rows = [
                { id: 1, segment_name: 'Domestic General Trade (GT)', is_total: false, monthly: this.makeMonthly(2500, 100000000) },
                { id: 2, segment_name: 'Domestic Modern Trade (MT)', is_total: false, monthly: this.makeMonthly(2000, 80000000) },
                { id: 3, segment_name: 'International Export Sales', is_total: false, monthly: this.makeMonthly(900, 36450000) },
                { id: 4, segment_name: 'GRAND TOTAL REVENUE (IDR)', is_total: true, monthly: this.makeMonthly(5400, 216450000) }
            ];
        },

        makeMonthly(qty, rev) {
            const monthly = {};
            for (let m = 1; m <= 12; m++) monthly[m] = { qty: qty, rev: rev };
            return monthly;
        },

        rowTotalQty(row) {
            let t = 0;
            for (let m = 1; m <= 12; m++) t += Number(row.monthly[m]?.qty || 0);
            return t;
        },

        rowTotalRev(row) {
            let t = 0;
            for (let m = 1; m <= 12; m++) t += Number(row.monthly[m]?.rev || 0);
            return t;
        },

        calcAsp(rev, qty) {
            return qty > 0 ? rev / qty : 0;
        },

        formatNumber(val) { return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(Math.round(val || 0)); },
        exportExcel() { alert('Exporting Sales Summary...'); }
    }
}
</script>
