<div x-data="{
    searchCostCenter: '',
    dropdownOpen: false,
    costCenters: <?= json_encode(array_map(fn($cc) => [
        'id'   => $cc['cost_center'],
        'name' => ($cc['cc_code'] ?? $cc['cost_center']) . ' - ' . $cc['cost_desc'],
    ], $costCenters)) ?>,
    selectedCostCenter: '',

    expandedRows: [],

    toggleRow(id) {
        if (this.expandedRows.includes(id)) {
            this.expandedRows = this.expandedRows.filter(rowId => rowId !== id);
        } else {
            this.expandedRows.push(id);
        }
    },

    isExpanded(id) {
        return this.expandedRows.includes(id);
    },

    viewList: [],

    selectCostCenter(item) {
        this.selectedCostCenter = item.id;
        this.dropdownOpen = false;
        this.fetchViewData();
    },

    fetchViewData() {
        if (!this.selectedCostCenter) {
            this.viewList = [];
            return;
        }
        fetch(`<?= base_url('opex-selling/getViewData') ?>?dept=${this.selectedCostCenter}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    this.viewList = res.viewData || [];
                }
            });
    },

    init() {
        if (this.costCenters.length > 0) {
            this.selectedCostCenter = this.costCenters[0].id;
            this.fetchViewData();
        }
    }
}">
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
                    <th class="py-3 px-3 text-center min-w-[50px] border-r border-stroke dark:border-strokedark">ACTION</th>
                    <th class="py-3 px-3 border-r border-stroke dark:border-strokedark min-w-[150px]">MAIN ACCOUNT</th>
                    <th class="py-3 px-3 border-r border-stroke dark:border-strokedark min-w-[250px]">DESCRIPTION</th>
                    <th class="py-3 px-3 text-right min-w-[120px]">TOTAL BUDGET</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="viewList.length === 0">
                    <tr>
                        <td colspan="4" class="border-b py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox mr-1"></i>No data available in table
                        </td>
                    </tr>
                </template>
                <template x-for="item in viewList" :key="item.id">
                    <template x-id="['row-item']">
                        <tr class="border-b border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4 transition-colors">
                            <td class="py-2.5 px-3 text-center border-r border-stroke dark:border-strokedark">
                                <button 
                                    type="button" 
                                    @click="toggleRow(item.id)"
                                    :class="isExpanded(item.id) ? 'bg-warning text-black hover:bg-warning/90' : 'bg-primary text-white hover:bg-opacity-90'"
                                    class="inline-flex h-6 w-6 items-center justify-center rounded text-xs font-bold transition-colors shadow"
                                >
                                    <span x-text="isExpanded(item.id) ? '-' : '+'"></span>
                                </button>
                            </td>
                            <td class="py-2.5 px-3 border-r border-stroke dark:border-strokedark font-semibold text-black dark:text-white" x-text="item.account"></td>
                            <td class="py-2.5 px-3 border-r border-stroke dark:border-strokedark font-semibold text-black dark:text-white" x-text="item.description"></td>
                            <td class="py-2.5 px-3 text-right font-bold text-primary" x-text="item.total.toLocaleString('id-ID')"></td>
                        </tr>

                        <tr x-show="isExpanded(item.id)" x-cloak class="bg-gray-1 dark:bg-meta-4/30">
                            <td colspan="4" class="p-4 border-b border-stroke dark:border-strokedark">
                                <div class="rounded border border-stroke bg-white p-3 shadow-inner dark:border-strokedark dark:bg-boxdark">
                                    <h5 class="mb-2 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Rincian Sub-Detail:</h5>
                                    <table class="w-full text-left text-xs border border-stroke dark:border-strokedark">
                                        <thead>
                                            <tr class="bg-gray-2 dark:bg-meta-4 font-semibold text-black dark:text-white">
                                                <th class="py-2 px-2 border-r border-stroke dark:border-strokedark">RINCIAN DESKRIPSI</th>
                                                <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark">JAN</th>
                                                <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark">FEB</th>
                                                <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark">MAR</th>
                                                <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark">APR</th>
                                                <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark">MAY</th>
                                                <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark">JUN</th>
                                                <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark">JUL</th>
                                                <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark">AUG</th>
                                                <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark">SEP</th>
                                                <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark">OCT</th>
                                                <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark">NOV</th>
                                                <th class="py-2 px-1 text-right border-r border-stroke dark:border-strokedark">DEC</th>
                                                <th class="py-2 px-2 text-right">TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-if="item.children.length === 0">
                                                <tr>
                                                    <td colspan="14" class="py-3 text-center text-gray-400">Belum ada rincian</td>
                                                </tr>
                                            </template>
                                            <template x-for="(child, cIdx) in item.children" :key="cIdx">
                                                <tr class="border-t border-stroke dark:border-strokedark">
                                                    <td class="py-1.5 px-2 border-r border-stroke dark:border-strokedark" x-text="child.desc"></td>
                                                    <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark" x-text="child.jan"></td>
                                                    <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark" x-text="child.feb"></td>
                                                    <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark" x-text="child.mar"></td>
                                                    <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark" x-text="child.apr"></td>
                                                    <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark" x-text="child.may"></td>
                                                    <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark" x-text="child.jun"></td>
                                                    <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark" x-text="child.jul"></td>
                                                    <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark" x-text="child.aug"></td>
                                                    <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark" x-text="child.sep"></td>
                                                    <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark" x-text="child.oct"></td>
                                                    <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark" x-text="child.nov"></td>
                                                    <td class="py-1.5 px-1 text-right border-r border-stroke dark:border-strokedark" x-text="child.dec"></td>
                                                    <td class="py-1.5 px-2 text-right font-bold" x-text="child.total.toLocaleString('id-ID')"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </template>
                </template>
            </tbody>
        </table>
    </div>
</div>
