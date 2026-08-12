<div x-data="deliveryClaimTab()" x-init="initData()" class="space-y-6">

    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Pengaturan Rate Delivery & Claim (%)</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Persentase pengurang revenue dan beban alokasi pengiriman bulanan.</p>
            </div>
            <div class="w-48">
                <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-400 mb-0.5">Channel</label>
                <select x-model="selectedChannel" @change="filterByChannel()" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary font-semibold">
                    <option value="domestic">Domestic (YTI)</option>
                    <option value="international">International</option>
                </select>
            </div>
        </div>

        <button type="button" @click="saveData()" :disabled="saving" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors disabled:opacity-50">
            <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            <span x-text="saving ? 'Menyimpan...' : 'Simpan Rate'"></span>
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1200px]">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="p-3 sticky left-0 z-10 bg-gray-50 dark:bg-gray-700 min-w-[180px]">Parameter Segment</th>
                        <th class="p-3 w-28 text-center">Rate Type</th>
                        <template x-for="(month, idx) in monthNames" :key="idx">
                            <th class="p-2 text-center w-20" x-text="month"></th>
                        </template>
                        <th class="p-3 text-center w-24 bg-gray-100 dark:bg-gray-700/80 sticky right-0">Avg Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="item in filteredSegments" :key="item.id">
                        <template x-for="rateType in ['delivery', 'claim']">
                            <tr :class="rateType === 'claim' ? 'bg-gray-50/50 dark:bg-gray-800/50' : ''">
                                <template x-if="rateType === 'delivery'">
                                    <td class="p-3 font-semibold text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700" rowspan="2" x-text="item.segment_name"></td>
                                </template>

                                <td class="p-2 text-center">
                                    <span :class="rateType === 'delivery' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'" class="px-2 py-0.5 rounded text-[10px] font-bold" x-text="rateType === 'delivery' ? 'Delivery (%)' : 'Claim (%)'"></span>
                                </td>

                                <template x-for="m in 12" :key="m">
                                    <td class="p-1">
                                        <input type="number" step="0.01" min="0" max="100" 
                                            x-model.number="item.monthly[rateType][m]" 
                                            @input="calculateAvg(item, rateType)"
                                            class="w-full text-right text-xs p-1.5 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                                    </td>
                                </template>

                                <td class="p-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-700/50 sticky right-0 text-gray-900 dark:text-white" x-text="(rateType === 'delivery' ? item.avg_delivery : item.avg_claim).toFixed(2) + '%'"></td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function deliveryClaimTab() {
    return {
        saving: false,
        selectedChannel: 'domestic',
        monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        segments: [],

        initData() {
            this.segments = [
                { id: 1, segment_name: 'Domestic General Trade (GT)', channel: 'domestic', avg_delivery: 2.50, avg_claim: 0.50, monthly: { delivery: {1:2.5,2:2.5,3:2.5,4:2.5,5:2.5,6:2.5,7:2.5,8:2.5,9:2.5,10:2.5,11:2.5,12:2.5}, claim: {1:0.5,2:0.5,3:0.5,4:0.5,5:0.5,6:0.5,7:0.5,8:0.5,9:0.5,10:0.5,11:0.5,12:0.5} } },
                { id: 2, segment_name: 'Domestic Modern Trade (MT)', channel: 'domestic', avg_delivery: 3.20, avg_claim: 1.00, monthly: { delivery: {1:3.2,2:3.2,3:3.2,4:3.2,5:3.2,6:3.2,7:3.2,8:3.2,9:3.2,10:3.2,11:3.2,12:3.2}, claim: {1:1,2:1,3:1,4:1,5:1,6:1,7:1,8:1,9:1,10:1,11:1,12:1} } },
                { id: 3, segment_name: 'Export Sales All Regions', channel: 'international', avg_delivery: 5.00, avg_claim: 0.20, monthly: { delivery: {1:5,2:5,3:5,4:5,5:5,6:5,7:5,8:5,9:5,10:5,11:5,12:5}, claim: {1:0.2,2:0.2,3:0.2,4:0.2,5:0.2,6:0.2,7:0.2,8:0.2,9:0.2,10:0.2,11:0.2,12:0.2} } }
            ];
        },

        filterByChannel() {
            // Data already filtered via getter
        },

        get filteredSegments() {
            return this.segments.filter(s => s.channel === this.selectedChannel);
        },

        calculateAvg(item, rateType) {
            let sum = 0;
            for (let m = 1; m <= 12; m++) { sum += Number(item.monthly[rateType][m] || 0); }
            if (rateType === 'delivery') item.avg_delivery = sum / 12;
            else item.avg_claim = sum / 12;
        },

        saveData() {
            this.saving = true;
            setTimeout(() => { this.saving = false; alert('Delivery & Claim rates berhasil disimpan!'); }, 500);
        }
    }
}
</script>