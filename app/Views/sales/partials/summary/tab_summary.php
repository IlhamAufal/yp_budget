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

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Grand Total Volume</p>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-1.5" x-text="formatNumber(summary.total_volume) + ' Box'">0 Box</h3>
            <p class="text-[11px] text-[#2F3185] dark:text-indigo-400 mt-1 font-medium">Domestic + Export Combined</p>
        </div>

        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Gross Revenue Target (IDR)</p>
            <h3 class="text-xl font-bold text-[#2F3185] dark:text-indigo-400 mt-1.5" x-text="'Rp ' + formatNumber(summary.gross_revenue)">Rp 0</h3>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Before Discount & Reclass</p>
        </div>

        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Discount & Reclass</p>
            <h3 class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-1.5" x-text="'Rp ' + formatNumber(summary.total_discount)">Rp 0</h3>
            <p class="text-[11px] text-rose-500 dark:text-rose-400 mt-1">Commercial Deductions</p>
        </div>

        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Net Sales Target (IDR)</p>
            <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1.5" x-text="'Rp ' + formatNumber(summary.net_sales)">Rp 0</h3>
            <p class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1">Final P/L Revenue Baseline</p>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Rincian Ringkasan Sales per Segmen (12 Bulan)</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rincian target bulanan per segmen kanal penjualan.</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1200px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th class="px-4 py-3.5 sticky left-0 z-10 bg-[#2F3185] text-white font-semibold min-w-[240px] border-r border-white/20">Segmen Penjualan</th>
                        <template x-for="(month, idx) in monthNames" :key="idx">
                            <th class="px-3 py-3.5 text-center text-white font-semibold w-24 border-r border-white/20" x-text="month"></th>
                        </template>
                        <th class="px-4 py-3.5 text-right w-36 bg-[#2F3185] text-white font-bold sticky right-0">Total Year</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-for="row in rows" :key="row.id">
                        <tr :class="row.is_total ? 'bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold border-t-2 border-[#2F3185]/30 text-gray-900 dark:text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'">
                            <td class="px-4 py-3 sticky left-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 font-sans font-semibold" :class="row.is_total ? 'text-[#2F3185] dark:text-indigo-400 font-bold' : 'text-gray-900 dark:text-white'" x-text="row.segment_name"></td>
                            <template x-for="m in 12" :key="m">
                                <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="formatNumber(row.monthly[m])"></td>
                            </template>
                            <td class="px-4 py-3 text-right font-bold bg-gray-50 dark:bg-gray-800 sticky right-0" :class="row.is_total ? 'text-[#2F3185] dark:text-indigo-400' : 'text-gray-900 dark:text-white'" x-text="formatNumber(row.total_year)"></td>
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