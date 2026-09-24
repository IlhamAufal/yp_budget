<div x-data="deliveryClaimTab()" x-init="initData()" class="space-y-6">

    <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-end gap-5 flex-1">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Pengaturan Rate Delivery & Claim (%)</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Persentase pengurang revenue dan beban alokasi pengiriman bulanan.</p>
            </div>
            <div class="w-full sm:w-56">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Channel</label>
                <select x-model="selectedChannel" @change="filterByChannel()" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 py-2.5 px-3.5 font-semibold transition-all">
                    <option value="domestic">Domestic (YTI)</option>
                    <option value="international">International</option>
                </select>
            </div>
        </div>

        <div class="shrink-0">
            <button type="button" @click="saveData()" :disabled="saving" class="px-5 py-2.5 bg-[#2F3185] hover:bg-[#25276d] text-white text-xs font-semibold rounded-xl shadow-xs transition-all inline-flex items-center gap-2 disabled:opacity-50 active:scale-[0.98]">
                <i x-show="!saving" class="fa-solid fa-floppy-disk"></i>
                <i x-show="saving" class="fa-solid fa-spinner fa-spin"></i>
                <span x-text="saving ? 'Menyimpan...' : 'Simpan Rate'"></span>
            </button>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Matriks Rate Delivery & Claim Bulanan (12 Bulan)</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Alokasi persentase rate bulanan per segmen kanal.</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1200px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th class="px-4 py-3.5 sticky left-0 z-10 bg-[#2F3185] text-white font-semibold min-w-[240px] border-r border-white/20">Parameter Segment</th>
                        <th class="px-3.5 py-3.5 w-32 text-center text-white font-semibold border-r border-white/20">Rate Type</th>
                        <template x-for="(month, idx) in monthNames" :key="idx">
                            <th class="px-3.5 py-3.5 text-center text-white font-semibold w-20 border-r border-white/20" x-text="month"></th>
                        </template>
                        <th class="px-4 py-3.5 text-center w-28 bg-[#2F3185] text-white font-bold sticky right-0">Avg Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <template x-for="item in filteredSegments" :key="item.id">
                        <template x-for="rateType in ['delivery', 'claim']">
                            <tr :class="rateType === 'claim' ? 'bg-gray-50/50 dark:bg-gray-800/50' : 'hover:bg-gray-50/30 dark:hover:bg-gray-800/30'">
                                <template x-if="rateType === 'delivery'">
                                    <td class="px-4 py-3 font-sans font-semibold text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800" rowspan="2" x-text="item.segment_name"></td>
                                </template>

                                <td class="px-3 py-2 text-center font-sans border-r border-gray-200 dark:border-gray-800">
                                    <span :class="rateType === 'delivery' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'" class="px-2.5 py-0.5 rounded-md text-[10px] font-bold" x-text="rateType === 'delivery' ? 'Delivery (%)' : 'Claim (%)'"></span>
                                </td>

                                <template x-for="m in 12" :key="m">
                                    <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-800">
                                        <input type="number" step="0.01" min="0" max="100" 
                                            x-model.number="item.monthly[rateType][m]" 
                                            @input="calculateAvg(item, rateType)"
                                            class="w-full text-right text-xs py-1 px-2 border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-1 focus:ring-[#2F3185]/20 outline-none">
                                    </td>
                                </template>

                                <td class="px-4 py-3 text-center font-bold font-mono bg-gray-50 dark:bg-gray-800 sticky right-0 text-gray-900 dark:text-white" x-text="(rateType === 'delivery' ? item.avg_delivery : item.avg_claim).toFixed(2) + '%'"></td>
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