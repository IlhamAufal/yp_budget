<div x-data="{
    searchCostCenter: '',
    dropdownOpen: false,
    costCenters: [
        { id: 'Domestic', name: 'Domestic' },
        { id: 'Export', name: 'Export' }
    ],
    selectedCostCenter: 'Domestic',
    
    // Sample Data Actual
    tableData: [], // Default kosong sesuai screenshot
    
    selectCostCenter(item) {
        this.selectedCostCenter = item.id;
        this.dropdownOpen = false;
        this.fetchData();
    },
    
    fetchData() {
        // Logika Ajax fetch data berdasarkan Cost Center
        console.log('Fetching data for:', this.selectedCostCenter);
    }
}">
    <div class="mb-6 rounded-sm border border-stroke bg-gray-2 p-4 dark:border-strokedark dark:bg-meta-4">
        <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">
            Cost Center
        </label>
        
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
    </div>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <a 
                :href="'<?= base_url('opex_selling/export_actual') ?>?cost_center=' + selectedCostCenter"
                class="inline-flex items-center gap-2 rounded border border-stroke bg-gray-100 px-4 py-2 text-sm font-medium text-black hover:bg-gray-200 dark:border-strokedark dark:bg-meta-4 dark:text-white dark:hover:bg-opacity-80"
            >
                Export Data
            </a>
            <button 
                type="button" 
                class="inline-flex items-center gap-2 rounded border border-stroke bg-gray-100 px-4 py-2 text-sm font-medium text-black hover:bg-gray-200 dark:border-strokedark dark:bg-meta-4 dark:text-white dark:hover:bg-opacity-80"
            >
                Column visibility
            </button>
        </div>

        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <span>Show</span>
            <select class="rounded border border-stroke px-2 py-1 text-sm dark:border-strokedark dark:bg-boxdark dark:text-white">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span>entries</span>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto border border-stroke dark:border-strokedark">
        <table class="w-full table-auto text-left text-xs">
            <thead>
                <tr class="bg-gray-2 text-black dark:bg-meta-4 dark:text-white uppercase font-bold border-b border-stroke dark:border-strokedark">
                    <th class="py-3 px-3 min-w-[50px] border-r border-stroke dark:border-strokedark">NO.</th>
                    <th class="py-3 px-3 min-w-[150px] border-r border-stroke dark:border-strokedark">MAIN ACCOUNT</th>
                    <th class="py-3 px-3 min-w-[200px] border-r border-stroke dark:border-strokedark">DESCRIPTION</th>
                    <th class="py-3 px-3 min-w-[90px] text-right border-r border-stroke dark:border-strokedark">JAN</th>
                    <th class="py-3 px-3 min-w-[90px] text-right border-r border-stroke dark:border-strokedark">FEB</th>
                    <th class="py-3 px-3 min-w-[90px] text-right border-r border-stroke dark:border-strokedark">MAR</th>
                    <th class="py-3 px-3 min-w-[90px] text-right border-r border-stroke dark:border-strokedark">APR</th>
                    <th class="py-3 px-3 min-w-[90px] text-right border-r border-stroke dark:border-strokedark">MAY</th>
                    <th class="py-3 px-3 min-w-[90px] text-right border-r border-stroke dark:border-strokedark">JUN</th>
                    <th class="py-3 px-3 min-w-[90px] text-right border-r border-stroke dark:border-strokedark">JUL</th>
                    <th class="py-3 px-3 min-w-[90px] text-right border-r border-stroke dark:border-strokedark">AUG</th>
                    <th class="py-3 px-3 min-w-[90px] text-right border-r border-stroke dark:border-strokedark">AVG</th>
                    <th class="py-3 px-3 min-w-[100px] text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="tableData.length === 0">
                    <tr>
                        <td colspan="13" class="py-8 text-center text-gray-500 dark:text-gray-400">
                            No data available in table
                        </td>
                    </tr>
                </template>

                <template x-for="(row, index) in tableData" :key="index">
                    <tr class="border-b border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="py-2.5 px-3 border-r border-stroke dark:border-strokedark" x-text="index + 1"></td>
                        <td class="py-2.5 px-3 border-r border-stroke dark:border-strokedark font-medium" x-text="row.main_account"></td>
                        <td class="py-2.5 px-3 border-r border-stroke dark:border-strokedark" x-text="row.description"></td>
                        <td class="py-2.5 px-3 text-right border-r border-stroke dark:border-strokedark" x-text="row.jan"></td>
                        <td class="py-2.5 px-3 text-right border-r border-stroke dark:border-strokedark" x-text="row.feb"></td>
                        <td class="py-2.5 px-3 text-right border-r border-stroke dark:border-strokedark" x-text="row.mar"></td>
                        <td class="py-2.5 px-3 text-right border-r border-stroke dark:border-strokedark" x-text="row.apr"></td>
                        <td class="py-2.5 px-3 text-right border-r border-stroke dark:border-strokedark" x-text="row.may"></td>
                        <td class="py-2.5 px-3 text-right border-r border-stroke dark:border-strokedark" x-text="row.jun"></td>
                        <td class="py-2.5 px-3 text-right border-r border-stroke dark:border-strokedark" x-text="row.jul"></td>
                        <td class="py-2.5 px-3 text-right border-r border-stroke dark:border-strokedark" x-text="row.aug"></td>
                        <td class="py-2.5 px-3 text-right border-r border-stroke dark:border-strokedark font-semibold" x-text="row.avg"></td>
                        <td class="py-2.5 px-3 text-right font-bold" x-text="row.total"></td>
                    </tr>
                </template>
            </tbody>
            <tfoot>
                <tr class="bg-gray-1 dark:bg-meta-4 font-bold border-t border-stroke dark:border-strokedark">
                    <td colspan="3" class="py-3 px-3 text-right border-r border-stroke dark:border-strokedark uppercase">
                        Grand Total
                    </td>
                    <td class="py-3 px-3 text-right border-r border-stroke dark:border-strokedark">0.00</td>
                    <td class="py-3 px-3 text-right border-r border-stroke dark:border-strokedark">0.00</td>
                    <td class="py-3 px-3 text-right border-r border-stroke dark:border-strokedark">0.00</td>
                    <td class="py-3 px-3 text-right border-r border-stroke dark:border-strokedark">0.00</td>
                    <td class="py-3 px-3 text-right border-r border-stroke dark:border-strokedark">0.00</td>
                    <td class="py-3 px-3 text-right border-r border-stroke dark:border-strokedark">0.00</td>
                    <td class="py-3 px-3 text-right border-r border-stroke dark:border-strokedark">0.00</td>
                    <td class="py-3 px-3 text-right border-r border-stroke dark:border-strokedark">0.00</td>
                    <td class="py-3 px-3 text-right border-r border-stroke dark:border-strokedark">0.00</td>
                    <td class="py-3 px-3 text-right text-primary">0.00</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-gray-600 dark:text-gray-400">
        <div>
            Showing 0 to 0 of 0 entries
        </div>
        <div class="flex items-center gap-1">
            <button class="px-3 py-1.5 rounded border border-stroke text-gray-400 cursor-not-allowed dark:border-strokedark">Previous</button>
            <button class="px-3 py-1.5 rounded border border-stroke text-gray-400 cursor-not-allowed dark:border-strokedark">Next</button>
        </div>
    </div>
</div>