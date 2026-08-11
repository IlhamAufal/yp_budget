<div x-data="discountTab()" x-init="initData()" class="space-y-6">

    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Alokasi Diskon Penjualan (Discount Allocation)</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Pengaturan persentase alokasi diskon promosi/channel per bulan.</p>
        </div>

        <button type="button" @click="saveData()" :disabled="saving" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors disabled:opacity-50">
            <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            <span x-text="saving ? 'Menyimpan...' : 'Simpan Discount'"></span>
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1300px]">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="p-3 sticky left-0 z-10 bg-gray-50 dark:bg-gray-700 min-w-[200px]">Channel / Promo Type</th>
                        <th class="p-3 w-20 text-center">Type</th>
                        <template x-for="(month, idx) in monthNames" :key="idx">
                            <th class="p-2 text-center w-24" x-text="month"></th>
                        </template>
                        <th class="p-3 text-right w-28 bg-gray-100 dark:bg-gray-700/80 sticky right-0">Total Year</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="item in discounts" :key="item.id">
                        <tr>
                            <td class="p-3 font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700" x-text="item.channel_name"></td>
                            <td class="p-2 text-center">
                                <span class="bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 px-2 py-0.5 rounded text-[10px] font-bold">% Discount</span>
                            </td>
                            <template x-for="m in 12" :key="m">
                                <td class="p-1">
                                    <input type="number" step="0.1" min="0" max="100" 
                                        x-model.number="item.monthly[m]" 
                                        @input="calculateTotal(item)"
                                        class="w-full text-right text-xs p-1.5 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                                </td>
                            </template>
                            <td class="p-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-700/50 sticky right-0 text-rose-600 dark:text-rose-400" x-text="item.total_avg.toFixed(2) + '%'"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function discountTab() {
    return {
        saving: false,
        monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        discounts: [],

        initData() {
            this.discounts = [
                { id: 1, channel_name: 'General Trade Regular Discount', total_avg: 3.0, monthly: {1:3,2:3,3:3,4:3,5:3,6:3,7:3,8:3,9:3,10:3,11:3,12:3} },
                { id: 2, channel_name: 'Modern Trade Listing & Promo Discount', total_avg: 5.5, monthly: {1:5.5,2:5.5,3:5.5,4:5.5,5:5.5,6:5.5,7:5.5,8:5.5,9:5.5,10:5.5,11:5.5,12:5.5} }
            ];
        },

        calculateTotal(item) {
            let sum = 0;
            for (let m = 1; m <= 12; m++) { sum += Number(item.monthly[m] || 0); }
            item.total_avg = sum / 12;
        },

        saveData() {
            this.saving = true;
            setTimeout(() => { this.saving = false; alert('Alokasi diskon berhasil disimpan!'); }, 500);
        }
    }
}
</script>