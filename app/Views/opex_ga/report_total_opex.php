<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="reportTotalOpexHandler()" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">

    <div class="border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 rounded-2xl p-2 shadow-xs overflow-x-auto">
        <nav class="flex gap-2 min-w-max">
            <template x-for="tab in tabs" :key="tab.id">
                <button type="button" 
                        @click="activeTab = tab.id" 
                        :class="activeTab === tab.id ? 'bg-primary text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-meta-4'"
                        class="px-4 py-2.5 rounded-xl text-xs md:text-sm font-semibold transition-all" 
                        x-text="tab.name"></button>
            </template>
        </nav>
    </div>

    <div x-show="activeTab === 'total_opex'" class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-default space-y-4">
        <div class="flex items-center gap-3">
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Data Channel:</label>
            <select x-model="selectedChannel" @change="fetchTotalOpex()" class="px-3 py-1.5 text-xs bg-gray-50 dark:bg-meta-4/40 border border-stroke dark:border-strokedark rounded-lg text-gray-900 dark:text-white">
                <option value="">- Pilih -</option>
                <option value="0">OPEX GA & Selling</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-primary text-white text-center font-semibold">
                        <th class="py-2.5 px-3 border-r border-white/20" rowspan="2">OPEX GA & Selling</th>
                        <th class="py-2 px-3 border-b border-white/20" colspan="12">BUDGET MONTHLY</th>
                        <th class="py-2.5 px-3 border-l border-white/20" rowspan="2">TOTAL</th>
                    </tr>
                    <tr class="bg-primary/90 text-white text-right text-[11px]">
                        <?php foreach (['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'] as $m) : ?>
                            <th class="py-1.5 px-2 border-r border-white/10"><?= $m ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark text-gray-700 dark:text-gray-300">
                    <template x-for="(row, idx) in totalOpexRows" :key="idx">
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4/20 transition-colors">
                            <td class="py-2 px-3 font-medium text-gray-900 dark:text-white" x-text="row.cost_center_header"></td>
                            <template x-for="m in ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec']" :key="m">
                                <td class="py-2 px-2 text-right" x-text="formatNum(row[m])"></td>
                            </template>
                            <td class="py-2 px-3 text-right font-bold text-primary" x-text="formatNum(row.total)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="activeTab === 'summary_cc'" class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-default space-y-4">
        <div class="flex items-center gap-3">
            <button type="button" @click="fetchSummaryCostCenter()" class="px-4 py-2 bg-primary text-white text-xs font-medium rounded-lg hover:bg-opacity-90 transition-colors">Load Cost Center Summary</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-primary text-white font-semibold">
                        <th class="py-2.5 px-3">Category</th>
                        <th class="py-2.5 px-3">Cost Center</th>
                        <th class="py-2.5 px-3">Description</th>
                        <th class="py-2.5 px-3 text-right">Budget Existing</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark text-gray-700 dark:text-gray-300">
                    <template x-for="(row, idx) in ccRows" :key="idx">
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4/20 transition-colors">
                            <td class="py-2 px-3 font-semibold text-primary" x-text="row.jenis ? row.jenis.toUpperCase() : ''"></td>
                            <td class="py-2 px-3" x-text="row.tipe"></td>
                            <td class="py-2 px-3" x-text="row.cost_center_desc"></td>
                            <td class="py-2 px-3 text-right font-bold" x-text="formatNum(row.total)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function reportTotalOpexHandler() {
    return {
        activeTab: 'total_opex',
        selectedChannel: '',
        tabs: [
            { id: 'total_opex', name: 'Total OPEX' },
            { id: 'summary_cc', name: 'Summary Cost Center' },
            { id: 'summary_acc', name: 'Summary Account' }
        ],
        totalOpexRows: [],
        ccRows: [],

        fetchTotalOpex() {
            if (!this.selectedChannel) return;
            fetch('<?= base_url('opex_ga/total_opex'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ dept: this.selectedChannel })
            })
            .then(res => res.json())
            .then(data => { this.totalOpexRows = data || []; });
        },

        fetchSummaryCostCenter() {
            fetch('<?= base_url('opex_ga/summary_costcenter'); ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => { this.ccRows = data || []; });
        },

        formatNum(val) {
            if (!val || isNaN(val)) return '0.00';
            return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }
}
</script>
<?= $this->endSection() ?>