<div x-data="reclassTab()" x-init="initData()" class="space-y-6">

    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="w-full sm:w-56">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Cost Center Source (Asal)</label>
                <select x-model="form.source_cc" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary">
                    <option value="">-- Pilih Cost Center Source --</option>
                    <option value="CC-101">CC-101 (Sales & Marketing GT)</option>
                    <option value="CC-102">CC-102 (Sales & Marketing MT)</option>
                </select>
            </div>

            <div class="hidden sm:block pt-4">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </div>

            <div class="w-full sm:w-56">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Cost Center Target (Tujuan)</label>
                <select x-model="form.target_cc" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary">
                    <option value="">-- Pilih Cost Center Target --</option>
                    <option value="CC-201">CC-201 (Trade Promotion Expense)</option>
                    <option value="CC-202">CC-202 (A&P Commercial Allocation)</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2 self-end lg:self-auto">
            <button type="button" @click="processReclass()" :disabled="processing" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors disabled:opacity-50">
                <svg x-show="!processing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                <span x-text="processing ? 'Memproses...' : 'Process Reclass'"></span>
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Matriks Persentase & Nilai Alokasi Reclass</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">Pemberlakuan Reclass Diskon ke OPEX</span>
        </div>

        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1300px]">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="p-3 sticky left-0 z-10 bg-gray-50 dark:bg-gray-700 min-w-[220px]">Reclass Description</th>
                        <th class="p-3 w-20 text-center">Type</th>
                        <template x-for="(month, idx) in monthNames" :key="idx">
                            <th class="p-2 text-center w-24" x-text="month"></th>
                        </template>
                        <th class="p-3 text-right w-28 bg-gray-100 dark:bg-gray-700/80 sticky right-0">Total Year</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="item in reclassItems" :key="item.id">
                        <template x-for="dataType in ['pct', 'val']">
                            <tr :class="dataType === 'val' ? 'bg-gray-50/50 dark:bg-gray-800/50' : ''">
                                <template x-if="dataType === 'pct'">
                                    <td class="p-3 font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700" rowspan="2">
                                        <div class="font-semibold" x-text="item.description"></div>
                                        <div class="text-[10px] text-gray-400" x-text="item.source_cc + ' → ' + item.target_cc"></div>
                                    </td>
                                </template>

                                <td class="p-2 text-center font-medium">
                                    <span :class="dataType === 'pct' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'" class="px-2 py-0.5 rounded text-[10px] font-bold" x-text="dataType === 'pct' ? '% Rate' : 'Val (IDR)'"></span>
                                </td>

                                <template x-for="m in 12" :key="m">
                                    <td class="p-1">
                                        <template x-if="dataType === 'pct'">
                                            <input type="number" step="0.1" min="0" max="100" x-model.number="item.monthly[m].pct" @input="calculateTotal(item)" class="w-full text-right text-xs p-1.5 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
                                        </template>
                                        <template x-if="dataType === 'val'">
                                            <div class="text-right p-1.5 font-mono text-emerald-600 dark:text-emerald-400 font-medium" x-text="formatNumber(item.monthly[m].pct * 1000000)"></div>
                                        </template>
                                    </td>
                                </template>

                                <td class="p-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-700/50 sticky right-0" :class="dataType === 'val' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white'">
                                    <span x-text="dataType === 'pct' ? item.total_avg.toFixed(2) + '%' : formatNumber(item.total_val)"></span>
                                </td>
                            </tr>
                        </template>
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
        monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        form: { source_cc: 'CC-101', target_cc: 'CC-201' },
        reclassItems: [],

        initData() {
            this.reclassItems = [
                {
                    id: 1, description: 'Reclass Discount GT to Trade Promo', source_cc: 'CC-101', target_cc: 'CC-201', total_avg: 2.0, total_val: 24000000,
                    monthly: { 1:{pct:2},2:{pct:2},3:{pct:2},4:{pct:2},5:{pct:2},6:{pct:2},7:{pct:2},8:{pct:2},9:{pct:2},10:{pct:2},11:{pct:2},12:{pct:2} }
                }
            ];
            this.reclassItems.forEach(i => this.calculateTotal(i));
        },

        calculateTotal(item) {
            let sumPct = 0;
            for (let m = 1; m <= 12; m++) { sumPct += Number(item.monthly[m].pct || 0); }
            item.total_avg = sumPct / 12;
            item.total_val = sumPct * 1000000;
        },

        formatNumber(val) { return new Intl.NumberFormat('id-ID').format(val || 0); },

        processReclass() {
            if (!this.form.source_cc || !this.form.target_cc) {
                alert('Silakan pilih Cost Center Source & Target terlebih dahulu!');
                return;
            }
            this.processing = true;
            setTimeout(() => {
                this.processing = false;
                alert('Reclass berhasil diproses dan dialokasikan!');
            }, 600);
        }
    }
}
</script>