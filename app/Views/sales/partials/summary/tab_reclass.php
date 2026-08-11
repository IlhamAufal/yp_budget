<div x-data="reclassTab()" x-init="initData()" class="space-y-6">

    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-48">
                <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-400 mb-0.5">Data</label>
                <select x-model="selectedData" @change="fetchData()" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary font-semibold">
                    <option value="entry_reclass">Entry Reclass</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="processReclass()" :disabled="processing" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors disabled:opacity-50">
                <i x-show="!processing" class="fa-solid fa-arrows-rotate"></i>
                <i x-show="processing" class="fa-solid fa-spinner fa-spin"></i>
                <span x-text="processing ? 'Memproses...' : 'Process Reclass'"></span>
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1200px]">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 font-bold border-b border-gray-200 dark:border-gray-700">
                        <th rowspan="2" class="p-3 min-w-[160px] border-r border-gray-200 dark:border-gray-700">Header</th>
                        <th rowspan="2" class="p-3 min-w-[140px] border-r border-gray-200 dark:border-gray-700">Category</th>
                        <th colspan="12" class="p-2 text-center border-r border-gray-200 dark:border-gray-700 bg-green-50/60 text-green-800 dark:bg-green-950/40 dark:text-green-300">Sales Revenue</th>
                        <th rowspan="2" class="p-3 text-right w-28 bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white font-bold">Total</th>
                    </tr>
                    <tr class="bg-gray-100/80 dark:bg-gray-700/30 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-700">
                        <th class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 w-16">Jan</th>
                        <th class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 w-16">Feb</th>
                        <th class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 w-16">Mar</th>
                        <th class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 w-16">Apr</th>
                        <th class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 w-16">May</th>
                        <th class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 w-16">Jun</th>
                        <th class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 w-16">Jul</th>
                        <th class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 w-16">Aug</th>
                        <th class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 w-16">Sep</th>
                        <th class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 w-16">Oct</th>
                        <th class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 w-16">Nov</th>
                        <th class="p-1.5 text-center border-r border-gray-200 dark:border-gray-700 w-16">Dec</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr x-show="rows.length === 0">
                        <td colspan="15" class="p-8 text-center text-gray-400">Tidak ada data</td>
                    </tr>
                    <template x-for="(row, idx) in rows" :key="row.id">
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="p-2.5 border-r border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white" x-text="row.header"></td>
                            <td class="p-2.5 border-r border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300" x-text="row.category"></td>
                            <td class="p-1.5 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[1])"></td>
                            <td class="p-1.5 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[2])"></td>
                            <td class="p-1.5 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[3])"></td>
                            <td class="p-1.5 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[4])"></td>
                            <td class="p-1.5 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[5])"></td>
                            <td class="p-1.5 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[6])"></td>
                            <td class="p-1.5 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[7])"></td>
                            <td class="p-1.5 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[8])"></td>
                            <td class="p-1.5 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[9])"></td>
                            <td class="p-1.5 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[10])"></td>
                            <td class="p-1.5 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[11])"></td>
                            <td class="p-1.5 text-right border-r border-gray-200 dark:border-gray-700 font-mono" x-text="fmt(row.monthly[12])"></td>
                            <td class="p-2.5 text-right font-bold font-mono bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white" x-text="fmt(row.total)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function reclassTab() {
    return {
        processing: false,
        selectedData: 'entry_reclass',
        rows: [],

        initData() {
            this.rows = [
                { id: 1, header: 'DOM - GT', category: 'Discount', total: 0, monthly: {1:0,2:0,3:0,4:0,5:0,6:0,7:0,8:0,9:0,10:0,11:0,12:0} },
                { id: 2, header: 'DOM - MT', category: 'Discount', total: 0, monthly: {1:0,2:0,3:0,4:0,5:0,6:0,7:0,8:0,9:0,10:0,11:0,12:0} },
                { id: 3, header: 'INTL', category: 'Discount', total: 0, monthly: {1:0,2:0,3:0,4:0,5:0,6:0,7:0,8:0,9:0,10:0,11:0,12:0} },
            ];
        },

        fetchData() {
            // Fetch based on selectedData
        },

        fmt(val) { return new Intl.NumberFormat('id-ID').format(Math.round(val || 0)); },

        processReclass() {
            this.processing = true;
            setTimeout(() => {
                this.processing = false;
                if (window.showToast) {
                    window.showToast('success', 'Reclass A&P berhasil diproses.');
                } else {
                    alert('Reclass A&P berhasil diproses.');
                }
            }, 500);
        }
    }
}
</script>
