<div x-data="{
    searchCostCenter: '',
    dropdownOpen: false,
    costCenters: [
        { id: 'Domestic', name: 'Domestic' },
        { id: 'Export', name: 'Export' }
    ],
    selectedCostCenter: 'Export',

    selectCostCenter(item) {
        this.selectedCostCenter = item.id;
        this.dropdownOpen = false;
        // Trigger auto download template setelah memilih opsi
        this.triggerDownload();
    },

    triggerDownload() {
        if (!this.selectedCostCenter) return;
        
        // Redirect ke endpoint download template
        window.location.href = '<?= base_url('opex_selling/download_template') ?>?cost_center=' + this.selectedCostCenter;
    }
}">
    <div class="mb-6 rounded-sm border border-stroke bg-gray-2 p-6 dark:border-strokedark dark:bg-meta-4">
        <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">
            Cost Center
        </label>
        
        <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
            <div class="relative w-full md:w-1/2 lg:w-1/3">
                <div 
                    @click="dropdownOpen = !dropdownOpen" 
                    class="flex w-full items-center justify-between rounded border border-stroke bg-white px-4 py-2.5 text-sm text-black cursor-pointer dark:border-strokedark dark:bg-boxdark dark:text-white"
                >
                    <div class="flex flex-wrap gap-1 items-center">
                        <span class="inline-flex items-center gap-1 rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-black dark:bg-meta-4 dark:text-white">
                            <span x-text="selectedCostCenter"></span>
                            <button type="button" @click.stop="selectedCostCenter = ''" class="hover:text-danger">&times;</button>
                        </span>
                    </div>
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
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
                            placeholder="Search Cost Center..." 
                            class="w-full rounded border border-stroke bg-gray-50 px-3 py-1.5 text-sm focus:border-primary focus:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white"
                        >
                    </div>
                    <ul class="max-h-48 overflow-y-auto">
                        <template x-for="item in costCenters.filter(c => c.name.toLowerCase().includes(searchCostCenter.toLowerCase()))" :key="item.id">
                            <li 
                                @click="selectCostCenter(item)" 
                                class="cursor-pointer px-4 py-2 text-sm hover:bg-primary hover:text-white dark:hover:bg-primary"
                                :class="selectedCostCenter === item.id ? 'bg-primary/10 text-primary font-bold' : 'text-black dark:text-white'"
                                x-text="item.name"
                            ></li>
                        </template>
                    </ul>
                </div>
            </div>

            <button 
                type="button" 
                @click="triggerDownload" 
                :disabled="!selectedCostCenter"
                class="inline-flex items-center gap-2 rounded bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-opacity-90 disabled:opacity-50 transition-colors"
            >
                <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24">
                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                </svg>
                Download Template Excel
            </button>
        </div>
    </div>
</div>