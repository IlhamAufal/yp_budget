<div x-data="salesSummaryTab()" x-init="initData()" class="space-y-6">

    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Sales Grand Summary & Revenue Target</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Konsolidasi total Domestic Sales, International Sales (Valas/IDR), dan penyesuaian diskon.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="exportExcel()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Export Summary</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Grand Total Volume</p>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-1" x-text="formatNumber(summary.total_volume) + ' Box'">0 Box</h3>
            <p class="text-[11px] text-blue-600 dark:text-blue-400 mt-1">Domestic + Export Combined</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Gross Revenue Target (IDR)</p>
            <h3 class="text-xl font-bold text-primary mt-1" x-text="'Rp ' + formatNumber(summary.gross_revenue)">Rp 0</h3>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Before Discount & Reclass</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Discount & Reclass</p>
            <h3 class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-1" x-text="'Rp ' + formatNumber(summary.total_discount)">Rp 0</h3>
            <p class="text-[11px] text-rose-500 dark:text-rose-400 mt-1">Commercial Deductions</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Net Sales Target (IDR)</p>
            <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1" x-text="'Rp ' + formatNumber(summary.net_sales)">Rp 0</h3>
            <p class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1">Final P/L Revenue Baseline</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Rincian Ringkasan Sales per Segmen (12 Bulan)</h2>
        </div>

        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1200px]">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="p-3 sticky left-0 z-10 bg-gray-50 dark:bg-gray-700 min-w-[220px]">Segmen Penjualan</th>
                        <template x-for="(month, idx) in monthNames" :key="idx">
                            <th class="p-2 text-center w-24" x-text="month"></th>
                        </template>
                        <th class="p-3 text-right w-32 bg-gray-100 dark:bg-gray-700/80 sticky right-0">Total Year</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="row in rows" :key="row.id">
                        <tr :class="row.is_total ? 'bg-primary/5 font-bold dark:bg-primary/10' : ''">
                            <td class="p-3 sticky left-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 font-medium" :class="row.is_total ? 'text-primary font-bold' : 'text-gray-900 dark:text-white'" x-text="row.segment_name"></td>
                            <template x-for="m in 12" :key="m">
                                <td class="p-2 text-right font-mono" x-text="formatNumber(row.monthly[m])"></td>
                            </template>
                            <td class="p-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-700/50 sticky right-0" :class="row.is_total ? 'text-primary' : 'text-gray-900 dark:text-white'" x-text="formatNumber(row.total_year)"></td>
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
        summary: { total_volume: 18000, gross_revenue: 2597400000, total_discount: 120000000, net_sales: 2477400000 },
        rows: [],

        initData() {
            this.rows = [
                { id: 1, segment_name: 'Domestic General Trade (GT)', is_total: false, total_year: 1200000000, monthly: {1:100000000,2:100000000,3:100000000,4:100000000,5:100000000,6:100000000,7:100000000,8:100000000,9:100000000,10:100000000,11:100000000,12:100000000} },
                { id: 2, segment_name: 'Domestic Modern Trade (MT)', is_total: false, total_year: 960000000, monthly: {1:80000000,2:80000000,3:80000000,4:80000000,5:80000000,6:80000000,7:80000000,8:80000000,9:80000000,10:80000000,11:80000000,12:80000000} },
                { id: 3, segment_name: 'International Export Sales', is_total: false, total_year: 437400000, monthly: {1:36450000,2:36450000,3:36450000,4:36450000,5:36450000,6:36450000,7:36450000,8:36450000,9:36450000,10:36450000,11:36450000,12:36450000} },
                { id: 4, segment_name: 'GRAND TOTAL REVENUE (IDR)', is_total: true, total_year: 2597400000, monthly: {1:216450000,2:216450000,3:216450000,4:216450000,5:216450000,6:216450000,7:216450000,8:216450000,9:216450000,10:216450000,11:216450000,12:216450000} }
            ];
        },

        formatNumber(val) { return new Intl.NumberFormat('id-ID').format(Math.round(val || 0)); },
        exportExcel() { alert('Exporting Sales Summary...'); }
    }
}
</script>