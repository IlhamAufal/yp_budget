<div x-data="reclassTab()" x-init="initData()" class="space-y-6">

    <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-end gap-5 flex-1">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Reclass A&P (Advertising & Promotion)</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Penyesuaian reklasifikasi diskon dan promosi ke akun A&P.</p>
            </div>
            <div class="w-full sm:w-56">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tipe Reklasifikasi</label>
                <select x-model="selectedData" @change="fetchData()" class="w-full text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 py-2.5 px-3.5 font-semibold transition-all">
                    <option value="entry_reclass">Entry Reclass</option>
                </select>
            </div>
        </div>

        <div class="shrink-0">
            <button type="button" @click="processReclass()" :disabled="processing" class="px-5 py-2.5 bg-[#2F3185] hover:bg-[#25276d] text-white text-xs font-semibold rounded-xl shadow-xs transition-all inline-flex items-center gap-2 disabled:opacity-50 active:scale-[0.98]">
                <i x-show="!processing" class="fa-solid fa-arrows-rotate"></i>
                <i x-show="processing" class="fa-solid fa-spinner fa-spin"></i>
                <span x-text="processing ? 'Memproses...' : 'Process Reclass'"></span>
            </button>
        </div>
    </div>

    <!-- Table Section Label (Separated from table container) -->
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Matriks Reklasifikasi Diskon A&P (12 Bulan)</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Nilai alokasi reklasifikasi bulanan per segmen kanal.</p>
    </div>

    <!-- Table Container (Round corner starts directly from thead) -->
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden bg-white dark:bg-gray-900">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs border-collapse min-w-[1200px] whitespace-nowrap">
                <thead class="bg-[#2F3185] text-white font-semibold border-b border-white/20 text-xs">
                    <tr class="bg-[#2F3185] text-white font-semibold">
                        <th rowspan="2" class="px-4 py-3 min-w-[180px] border-r border-white/20 text-white font-semibold">Header</th>
                        <th rowspan="2" class="px-4 py-3 min-w-[160px] border-r border-white/20 text-white font-semibold">Category</th>
                        <th colspan="12" class="px-3 py-2 text-center border-r border-white/20 text-white font-semibold">Sales Revenue (IDR)</th>
                        <th rowspan="2" class="px-4 py-3 text-right w-36 bg-[#2F3185] text-white font-bold sticky right-0">Total Year</th>
                    </tr>
                    <tr class="bg-[#25276d] text-xs font-semibold text-white/90 border-b border-white/20">
                        <th class="px-3 py-2 text-center border-r border-white/20 min-w-[70px]">Jan</th>
                        <th class="px-3 py-2 text-center border-r border-white/20 min-w-[70px]">Feb</th>
                        <th class="px-3 py-2 text-center border-r border-white/20 min-w-[70px]">Mar</th>
                        <th class="px-3 py-2 text-center border-r border-white/20 min-w-[70px]">Apr</th>
                        <th class="px-3 py-2 text-center border-r border-white/20 min-w-[70px]">May</th>
                        <th class="px-3 py-2 text-center border-r border-white/20 min-w-[70px]">Jun</th>
                        <th class="px-3 py-2 text-center border-r border-white/20 min-w-[70px]">Jul</th>
                        <th class="px-3 py-2 text-center border-r border-white/20 min-w-[70px]">Aug</th>
                        <th class="px-3 py-2 text-center border-r border-white/20 min-w-[70px]">Sep</th>
                        <th class="px-3 py-2 text-center border-r border-white/20 min-w-[70px]">Oct</th>
                        <th class="px-3 py-2 text-center border-r border-white/20 min-w-[70px]">Nov</th>
                        <th class="px-3 py-2 text-center border-r border-white/20 min-w-[70px]">Dec</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                    <tr x-show="rows.length === 0">
                        <td colspan="15" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-right-left text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data Reclass</p>
                            </div>
                        </td>
                    </tr>
                    <template x-for="(row, idx) in rows" :key="row.id">
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-sans font-semibold text-gray-900 dark:text-white" x-text="row.header"></td>
                            <td class="px-4 py-3 border-r border-gray-200 dark:border-gray-800 font-sans text-gray-700 dark:text-gray-300" x-text="row.category"></td>
                            <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[1])"></td>
                            <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[2])"></td>
                            <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[3])"></td>
                            <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[4])"></td>
                            <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[5])"></td>
                            <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[6])"></td>
                            <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[7])"></td>
                            <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[8])"></td>
                            <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[9])"></td>
                            <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[10])"></td>
                            <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[11])"></td>
                            <td class="px-3 py-3 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.monthly[12])"></td>
                            <td class="px-4 py-3 text-right font-bold font-mono bg-gray-50 dark:bg-gray-800 sticky right-0 text-gray-900 dark:text-white" x-text="fmt(row.total)"></td>
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
