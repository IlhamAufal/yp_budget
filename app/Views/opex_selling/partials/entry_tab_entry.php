<div x-data="{
    searchCostCenter: '',
    dropdownOpen: false,
    costCenters: <?= htmlspecialchars(json_encode(array_map(function($cc) {
        $cc = (array) $cc;
        return [
            'id'   => $cc['cost_center'] ?? '',
            'name' => trim(($cc['cc_code'] ?? $cc['cost_center'] ?? '') . ' - ' . ($cc['cost_desc'] ?? '-')),
        ];
    }, $costCenters ?? [])), ENT_QUOTES, 'UTF-8') ?>,
    selectedCostCenter: '',
    
    entries: [],

    selectCostCenter(item) {
        this.selectedCostCenter = item.id;
        this.dropdownOpen = false;
        this.fetchEntryData();
    },

    fetchEntryData() {
        if (!this.selectedCostCenter) {
            this.entries = [];
            return;
        }
        fetch(`<?= base_url('opex-selling/getHeaderAccounts') ?>?dept=${this.selectedCostCenter}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    this.entries = (res.headers || []).map((h, i) => ({
                        no: i + 1,
                        category: 'Selling',
                        main_account: h.acct_code,
                        cost_center: h.id_dept,
                        description: h.coa_desc,
                        jan: parseFloat(h.total_budget / 12) || 0,
                        feb: parseFloat(h.total_budget / 12) || 0,
                        mar: parseFloat(h.total_budget / 12) || 0,
                        apr: parseFloat(h.total_budget / 12) || 0,
                        may: parseFloat(h.total_budget / 12) || 0,
                        jun: parseFloat(h.total_budget / 12) || 0,
                        jul: parseFloat(h.total_budget / 12) || 0,
                        aug: parseFloat(h.total_budget / 12) || 0,
                        sep: parseFloat(h.total_budget / 12) || 0,
                        oct: parseFloat(h.total_budget / 12) || 0,
                        nov: parseFloat(h.total_budget / 12) || 0,
                        dec: parseFloat(h.total_budget / 12) || 0,
                        total: parseFloat(h.total_budget) || 0,
                        header_param: h.coa_desc,
                        idx_param: h.id
                    }));
                }
            });
    },

    init() {
        if (this.costCenters.length > 0) {
            this.selectedCostCenter = this.costCenters[0].id;
            this.fetchEntryData();
        }
    }
}">
    <div class="mb-6 rounded-sm border-l-6 border-warning bg-warning/10 p-4 dark:bg-warning/20">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 text-warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h4 class="font-bold text-black dark:text-white text-sm mb-1">Pemberitahuan Important!</h4>
                <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                    Pastikan setelah Anda menginputkan atau mengubah data pada entri budget, silakan periksa kembali kalkulasi totalnya sebelum berpindah ke modul lain.
                </p>
            </div>
        </div>
    </div>

    <div class="mb-6 rounded-sm border border-stroke bg-gray-2 p-4 dark:border-strokedark dark:bg-meta-4">
        <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-black dark:text-white">
            Cost Center
        </label>
        
        <div class="relative w-full md:w-1/2 lg:w-1/3">
            <div 
                @click="dropdownOpen = !dropdownOpen" 
                class="flex w-full items-center justify-between rounded border border-stroke bg-white px-4 py-2 text-sm text-black cursor-pointer dark:border-strokedark dark:bg-boxdark dark:text-white"
            >
                <span x-text="selectedCostCenter ? costCenters.find(c => c.id === selectedCostCenter)?.name || selectedCostCenter : 'Pilih Cost Center'"></span>
                <i class="fas fa-chevron-down text-xs"></i>
            </div>

            <div 
                x-show="dropdownOpen" 
                @click.outside="dropdownOpen = false" 
                x-cloak 
                class="absolute left-0 top-full z-40 mt-1 w-full rounded border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
            >
                <div class="p-2">
                    <input 
                        type="text" 
                        x-model="searchCostCenter" 
                        placeholder="Cari Cost Center..." 
                        class="w-full rounded border border-stroke bg-gray-50 px-3 py-1.5 text-sm focus:border-primary focus:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white"
                    >
                </div>
                <ul class="max-h-48 overflow-y-auto text-xs">
                    <template x-for="item in costCenters.filter(c => c.name.toLowerCase().includes(searchCostCenter.toLowerCase()))" :key="item.id">
                        <li 
                            @click="selectCostCenter(item)" 
                            class="cursor-pointer px-4 py-2 hover:bg-primary hover:text-white"
                            :class="selectedCostCenter === item.id ? 'bg-primary/10 text-primary font-bold' : 'text-black dark:text-white'"
                            x-text="item.name"
                        ></li>
                    </template>
                </ul>
            </div>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto border border-stroke dark:border-strokedark rounded-sm">
        <table class="w-full table-auto text-left text-xs">
            <thead>
                <tr class="bg-gray-2 text-black dark:bg-meta-4 dark:text-white font-bold border-b border-stroke dark:border-strokedark uppercase">
                    <th class="py-3 px-2 text-center border-r border-stroke dark:border-strokedark">NO</th>
                    <th class="py-3 px-3 border-r border-stroke dark:border-strokedark min-w-[120px]">CATEGORY</th>
                    <th class="py-3 px-3 border-r border-stroke dark:border-strokedark min-w-[100px]">MAIN ACCOUNT</th>
                    <th class="py-3 px-3 border-r border-stroke dark:border-strokedark min-w-[110px]">COST CENTER</th>
                    <th class="py-3 px-3 border-r border-stroke dark:border-strokedark min-w-[180px]">DESCRIPTION</th>
                    <th class="py-3 px-2 text-right border-r border-stroke dark:border-strokedark min-w-[70px]">JAN</th>
                    <th class="py-3 px-2 text-right border-r border-stroke dark:border-strokedark min-w-[70px]">FEB</th>
                    <th class="py-3 px-2 text-right border-r border-stroke dark:border-strokedark min-w-[70px]">MAR</th>
                    <th class="py-3 px-2 text-right border-r border-stroke dark:border-strokedark min-w-[70px]">APR</th>
                    <th class="py-3 px-2 text-right border-r border-stroke dark:border-strokedark min-w-[70px]">MAY</th>
                    <th class="py-3 px-2 text-right border-r border-stroke dark:border-strokedark min-w-[70px]">JUN</th>
                    <th class="py-3 px-2 text-right border-r border-stroke dark:border-strokedark min-w-[70px]">JUL</th>
                    <th class="py-3 px-2 text-right border-r border-stroke dark:border-strokedark min-w-[70px]">AUG</th>
                    <th class="py-3 px-2 text-right border-r border-stroke dark:border-strokedark min-w-[70px]">SEP</th>
                    <th class="py-3 px-2 text-right border-r border-stroke dark:border-strokedark min-w-[70px]">OCT</th>
                    <th class="py-3 px-2 text-right border-r border-stroke dark:border-strokedark min-w-[70px]">NOV</th>
                    <th class="py-3 px-2 text-right border-r border-stroke dark:border-strokedark min-w-[70px]">DEC</th>
                    <th class="py-3 px-3 text-right border-r border-stroke dark:border-strokedark min-w-[90px]">TOTAL</th>
                    <th class="py-3 px-2 text-center min-w-[80px]">ENTRY</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="entries.length === 0">
                    <tr>
                        <td colspan="18" class="border-b py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox mr-1"></i>No data available in table
                        </td>
                    </tr>
                </template>
                <template x-for="(row, index) in entries" :key="index">
                    <tr class="border-b border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="py-2.5 px-2 text-center border-r border-stroke dark:border-strokedark" x-text="row.no"></td>
                        <td class="py-2.5 px-3 border-r border-stroke dark:border-strokedark" x-text="row.category"></td>
                        <td class="py-2.5 px-3 border-r border-stroke dark:border-strokedark font-medium" x-text="row.main_account"></td>
                        <td class="py-2.5 px-3 border-r border-stroke dark:border-strokedark" x-text="row.cost_center"></td>
                        <td class="py-2.5 px-3 border-r border-stroke dark:border-strokedark" x-text="row.description"></td>
                        <td class="py-2.5 px-2 text-right border-r border-stroke dark:border-strokedark" x-text="row.jan.toLocaleString('id-ID')"></td>
                        <td class="py-2.5 px-2 text-right border-r border-stroke dark:border-strokedark" x-text="row.feb.toLocaleString('id-ID')"></td>
                        <td class="py-2.5 px-2 text-right border-r border-stroke dark:border-strokedark" x-text="row.mar.toLocaleString('id-ID')"></td>
                        <td class="py-2.5 px-2 text-right border-r border-stroke dark:border-strokedark" x-text="row.apr.toLocaleString('id-ID')"></td>
                        <td class="py-2.5 px-2 text-right border-r border-stroke dark:border-strokedark" x-text="row.may.toLocaleString('id-ID')"></td>
                        <td class="py-2.5 px-2 text-right border-r border-stroke dark:border-strokedark" x-text="row.jun.toLocaleString('id-ID')"></td>
                        <td class="py-2.5 px-2 text-right border-r border-stroke dark:border-strokedark" x-text="row.jul.toLocaleString('id-ID')"></td>
                        <td class="py-2.5 px-2 text-right border-r border-stroke dark:border-strokedark" x-text="row.aug.toLocaleString('id-ID')"></td>
                        <td class="py-2.5 px-2 text-right border-r border-stroke dark:border-strokedark" x-text="row.sep.toLocaleString('id-ID')"></td>
                        <td class="py-2.5 px-2 text-right border-r border-stroke dark:border-strokedark" x-text="row.oct.toLocaleString('id-ID')"></td>
                        <td class="py-2.5 px-2 text-right border-r border-stroke dark:border-strokedark" x-text="row.nov.toLocaleString('id-ID')"></td>
                        <td class="py-2.5 px-2 text-right border-r border-stroke dark:border-strokedark" x-text="row.dec.toLocaleString('id-ID')"></td>
                        <td class="py-2.5 px-3 text-right border-r border-stroke dark:border-strokedark font-bold text-black dark:text-white" x-text="row.total.toLocaleString('id-ID')"></td>
                        
                        <td class="py-2.5 px-2 text-center">
                            <a 
                                :href="'<?= base_url('opex-selling/entry-budget-detail') ?>?header=' + encodeURIComponent(row.header_param) + '&dept=' + encodeURIComponent(row.cost_center) + '&idx=' + row.idx_param"
                                class="inline-flex items-center justify-center rounded bg-primary px-2.5 py-1.5 text-xs font-medium text-white hover:bg-opacity-90 shadow-sm transition-all"
                                title="Input Detail"
                            >
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
