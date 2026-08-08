<div x-data="keyProductTab()" x-init="initData()" class="space-y-6">

    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                ⭐ Key Product Focus
            </span>
            <p class="text-xs text-gray-500 dark:text-gray-400">Monitoring prioritas target untuk produk utama perusahaan.</p>
        </div>

        <button type="button" @click="saveData()" :disabled="saving" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors disabled:opacity-50">
            <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            <span x-text="saving ? 'Menyimpan...' : 'Simpan Key Product'"></span>
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1400px]">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="p-3 sticky left-0 z-10 bg-gray-50 dark:bg-gray-700 min-w-[220px]">Key Product Name</th>
                        <th class="p-3 w-28 text-right">Price (IDR)</th>
                        <th class="p-3 w-20 text-center">Type</th>
                        <template x-for="(month, idx) in monthNames" :key="idx">
                            <th class="p-2 text-center w-24" x-text="month"></th>
                        </template>
                        <th class="p-3 text-right w-32 bg-gray-100 dark:bg-gray-700/80 sticky right-0">Total Target</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="item in keyProducts" :key="item.id">
                        <template x-for="dataType in ['qty', 'val']">
                            <tr :class="dataType === 'val' ? 'bg-amber-50/30 dark:bg-amber-950/10' : ''">
                                <template x-if="dataType === 'qty'">
                                    <td class="p-3 font-medium text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700" rowspan="2">
                                        <div class="font-semibold text-amber-700 dark:text-amber-400 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                            <span x-text="item.product_name"></span>
                                        </div>
                                        <div class="text-[10px] text-gray-400" x-text="item.product_code"></div>
                                    </td>
                                </template>

                                <template x-if="dataType === 'qty'">
                                    <td class="p-3 font-mono text-right font-medium" rowspan="2" x-text="formatNumber(item.price)"></td>
                                </template>

                                <td class="p-2 text-center">
                                    <span :class="dataType === 'qty' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30'" class="px-2 py-0.5 rounded text-[10px] font-bold" x-text="dataType === 'qty' ? 'Vol (Box)' : 'Val (IDR)'"></span>
                                </td>

                                <template x-for="m in 12" :key="m">
                                    <td class="p-1">
                                        <template x-if="dataType === 'qty'">
                                            <input type="number" x-model.number="item.monthly[m].qty" @input="calculateTotal(item)" class="w-full text-right text-xs p-1.5 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
                                        </template>
                                        <template x-if="dataType === 'val'">
                                            <div class="text-right p-1.5 font-mono font-medium text-amber-700 dark:text-amber-400" x-text="formatNumber(item.monthly[m].qty * item.price)"></div>
                                        </template>
                                    </td>
                                </template>

                                <td class="p-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-700/50 sticky right-0" :class="dataType === 'val' ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">
                                    <span x-text="dataType === 'qty' ? formatNumber(item.total_qty) : formatNumber(item.total_val)"></span>
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
function keyProductTab() {
    return {
        saving: false,
        monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        keyProducts: [],

        initData() {
            this.keyProducts = [
                {
                    id: 1, product_code: 'KEY-001', product_name: 'Minyak Goreng Key Brand 18L', price: 275000, total_qty: 12000, total_val: 3300000000,
                    monthly: { 1:{qty:1000},2:{qty:1000},3:{qty:1000},4:{qty:1000},5:{qty:1000},6:{qty:1000},7:{qty:1000},8:{qty:1000},9:{qty:1000},10:{qty:1000},11:{qty:1000},12:{qty:1000} }
                }
            ];
            this.keyProducts.forEach(p => this.calculateTotal(p));
        },

        calculateTotal(item) {
            let sum = 0;
            for (let m = 1; m <= 12; m++) { sum += Number(item.monthly[m].qty || 0); }
            item.total_qty = sum;
            item.total_val = sum * item.price;
        },

        formatNumber(val) { return new Intl.NumberFormat('id-ID').format(val || 0); },

        saveData() {
            this.saving = true;
            setTimeout(() => { this.saving = false; alert('Data Key Product berhasil disimpan!'); }, 500);
        }
    }
}
</script>