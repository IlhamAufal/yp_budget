<div x-data="opexSellingViewTab()" x-init="init()" class="space-y-6">

  <!-- Filter -->
  <div class="mb-6 rounded-sm border border-stroke bg-gray-2 p-4 dark:border-strokedark dark:bg-meta-4">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div class="w-full md:w-1/2 lg:w-1/3">
        <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-black dark:text-white">Cost Center</label>
        <div class="relative">
          <div @click="dropdownOpen = !dropdownOpen" class="flex w-full items-center justify-between rounded border border-stroke bg-white px-4 py-2 text-sm text-black cursor-pointer dark:border-strokedark dark:bg-boxdark dark:text-white">
            <span x-text="selectedCostCenter !== '' ? costCenters.find(c => c.id === selectedCostCenter)?.name || selectedCostCenter : 'Pilih Cost Center'"></span>
            <i class="fas fa-chevron-down text-xs"></i>
          </div>
          <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-cloak class="absolute left-0 top-full z-40 mt-1 w-full rounded border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="p-2">
              <input type="text" x-model="searchCostCenter" placeholder="Cari Cost Center..." class="w-full rounded border border-stroke bg-gray-50 px-3 py-1.5 text-sm focus:border-primary focus:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white">
            </div>
            <ul class="max-h-48 overflow-y-auto text-xs">
              <template x-for="item in costCenters.filter(c => c.name.toLowerCase().includes(searchCostCenter.toLowerCase()))" :key="item.id">
                <li @click="selectCostCenter(item)" class="cursor-pointer px-4 py-2 hover:bg-primary hover:text-white" :class="selectedCostCenter === item.id ? 'bg-primary/10 text-primary font-bold' : 'text-black dark:text-white'" x-text="item.name"></li>
              </template>
            </ul>
          </div>
        </div>
      </div>
      <button @click="exportData()" :disabled="selectedCostCenter === ''" :class="selectedCostCenter !== '' ? 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs' : 'bg-gray-200 text-gray-400 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600'" class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-semibold transition-all">
        <i class="fa-solid fa-file-excel text-xs"></i> Export Data
      </button>
    </div>
  </div>

  <!-- Flat View Data -->
  <div x-show="selectedCostCenter !== ''" x-cloak>
    <div class="max-w-full overflow-x-auto rounded-sm border border-stroke dark:border-strokedark">
      <table class="w-full table-auto text-left text-xs border-collapse min-w-[1300px]">
        <thead class="bg-gray-2 dark:bg-meta-4">
          <tr class="text-black dark:text-white font-bold uppercase border-b border-stroke dark:border-strokedark">
            <th rowspan="2" class="py-3 px-3 border-r border-stroke dark:border-strokedark min-w-[240px]">
              <span>Selling Expense</span>
              <span class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase mt-1" x-text="selectedCostCenter === '0' ? '[ALL COST CENTER]' : selectedCostCenter"></span>
            </th>
            <th colspan="10" class="py-2 px-2 text-center border-b border-r border-stroke dark:border-strokedark bg-blue-50/60 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300">Actual</th>
            <th rowspan="2" class="py-3 px-1 border-b border-stroke dark:border-strokedark bg-gray-900 dark:bg-gray-100 w-1.5 p-0"></th>
            <th colspan="13" class="py-2 px-2 text-center border-b border-r border-stroke dark:border-strokedark bg-green-50/60 text-green-800 dark:bg-green-950/40 dark:text-green-300">Budget</th>
          </tr>
          <tr class="bg-gray-50 text-gray-600 dark:bg-meta-4/80 dark:text-gray-400 font-bold uppercase text-[10px] border-b border-stroke dark:border-strokedark">
            <template x-for="m in actualMonths" :key="'actual_head_' + m"><th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]" x-text="m"></th></template>
            <template x-for="m in budgetHeaders" :key="'budget_head_' + m.key"><th class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark min-w-[60px]" x-text="m.label"></th></template>
          </tr>
        </thead>
        <tbody>
          <tr x-show="viewLoading" x-cloak>
            <td colspan="25" class="py-16 text-center text-gray-500 dark:text-gray-400">
              <i class="fa-solid fa-spinner fa-spin text-2xl mb-2"></i>
              <p class="font-semibold">Memuat data...</p>
            </td>
          </tr>
          <tr x-show="!viewLoading && viewDataRows.length === 0" x-cloak>
            <td colspan="25" class="py-16 text-center text-gray-500 dark:text-gray-400">
              <i class="fa-solid fa-inbox mr-1"></i>No data available in table
            </td>
          </tr>

        </tbody>

        <template x-for="group in groupedRows" :key="group.header">
          <tbody>
            <tr class="bg-gray-100/80 dark:bg-meta-4 font-bold text-black dark:text-white border-y border-stroke dark:border-strokedark">
                <td class="py-2.5 px-3 border-r border-stroke dark:border-strokedark uppercase" x-text="'SUBTOTAL ' + group.header.toUpperCase()"></td>
                <template x-for="m in actualMonths" :key="'subtotal_actual_' + group.header + '_' + m">
                  <td class="py-2.5 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(groupTotal(group.rows, m))"></td>
                </template>
                <td class="bg-gray-900 dark:bg-gray-100 w-1.5 p-0"></td>
                <template x-for="m in budgetMonths" :key="'subtotal_budget_' + group.header + '_' + m">
                  <td class="py-2.5 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(groupTotal(group.rows, m))"></td>
                </template>
              </tr>

              <template x-for="row in group.rows" :key="group.header + '_' + row.main_account">
                <tr class="hover:bg-gray-50 dark:hover:bg-meta-4 transition-colors border-b border-stroke dark:border-strokedark">
                  <td class="py-2 px-3 border-r border-stroke dark:border-strokedark text-black dark:text-white">
                    <span class="mr-1 text-gray-400">↳</span><span x-text="row.main_account + '-' + row.cost_center_desc"></span>
                  </td>
                  <template x-for="m in actualMonths" :key="'row_actual_' + row.main_account + '_' + m">
                    <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row[m])"></td>
                  </template>
                  <td class="bg-gray-900 dark:bg-gray-100 w-1.5 p-0"></td>
                  <template x-for="m in budgetMonths" :key="'row_budget_' + row.main_account + '_' + m">
                    <td class="py-2 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono text-gray-600 dark:text-gray-300" x-text="fmtShort(row[m])"></td>
                  </template>
                </tr>
              </template>
          </tbody>
        </template>
        <tfoot>
          <tr x-show="!viewLoading && viewDataRows.length > 0" class="bg-gray-100/80 dark:bg-meta-4 font-bold text-black dark:text-white border-t border-stroke dark:border-strokedark">
            <td class="py-3 px-3 border-r border-stroke dark:border-strokedark uppercase text-right">Grand Total</td>
            <template x-for="m in actualMonths" :key="'grand_actual_' + m">
              <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandTotal(m))"></td>
            </template>
            <td class="bg-gray-900 dark:bg-gray-100 w-1.5 p-0"></td>
            <template x-for="m in budgetMonths" :key="'grand_budget_' + m">
              <td class="py-3 px-1.5 text-right border-r border-stroke dark:border-strokedark font-mono" x-text="fmtShort(grandTotal(m))"></td>
            </template>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <div x-show="selectedCostCenter === ''" x-cloak class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-gray-200 rounded-sm dark:border-strokedark">
    <i class="fa-solid fa-chart-column text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
    <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Silakan pilih Cost Center untuk meninjau data konsolidasi View Data.</p>
  </div>
</div>

<script>
function opexSellingViewTab() {
    return {
        searchCostCenter: '',
        dropdownOpen: false,
        costCenters: [{ id: '0', name: '[All Cost Center]' }].concat(<?= json_encode(array_map(fn($cc) => [
            'id'   => (string) (! empty($cc['cost_center_sap']) ? $cc['cost_center_sap'] : $cc['cost_center']),
            'name' => (! empty($cc['cost_center_sap']) ? $cc['cost_center_sap'] : $cc['cost_center']) . ' - ' . $cc['cost_desc'],
        ], $costCenters), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>),
        selectedCostCenter: '',

        actualMonths: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','AVG','TOTAL'],
        budgetHeaders: [
            { key: 'isi_1', label: 'JAN' }, { key: 'isi_2', label: 'FEB' },
            { key: 'isi_3', label: 'MAR' }, { key: 'isi_4', label: 'APR' },
            { key: 'isi_5', label: 'MAY' }, { key: 'isi_6', label: 'JUN' },
            { key: 'isi_7', label: 'JUL' }, { key: 'isi_8', label: 'AUG' },
            { key: 'isi_9', label: 'SEP' }, { key: 'isi_10', label: 'OCT' },
            { key: 'isi_11', label: 'NOV' }, { key: 'isi_12', label: 'DEC' },
            { key: 'isi_tot', label: 'TOTAL' }
        ],
        budgetMonths: ['isi_1','isi_2','isi_3','isi_4','isi_5','isi_6','isi_7','isi_8','isi_9','isi_10','isi_11','isi_12','isi_tot'],
        viewDataRows: [],
        viewLoading: false,

        get groupedRows() {
            const groups = [];
            const index = {};
            for (const row of this.viewDataRows) {
                const header = row.cost_center_header || 'Other';
                if (!index[header]) {
                    index[header] = { header, rows: [] };
                    groups.push(index[header]);
                }
                index[header].rows.push(row);
            }
            return groups;
        },

        selectCostCenter(item) {
            this.selectedCostCenter = item.id;
            this.dropdownOpen = false;
            this.fetchViewData();
        },

        async fetchViewData() {
            if (this.selectedCostCenter === '') {
                this.viewDataRows = [];
                return;
            }

            this.viewLoading = true;
            try {
                const body = new URLSearchParams();
                body.set('dept', this.selectedCostCenter);
                const res = await fetch('<?= base_url('opex-selling/cariActualTable') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: body.toString()
                });
                const data = await res.json();
                this.viewDataRows = Array.isArray(data)
                    ? data.map(row => ({ ...row, TOTAL: row.TOT ?? 0 }))
                    : [];
            } catch (e) {
                console.error('Gagal memuat view data:', e);
                this.viewDataRows = [];
            } finally {
                this.viewLoading = false;
            }
        },

        groupTotal(rows, key) {
            return rows.reduce((sum, row) => sum + (parseFloat(row[key]) || 0), 0);
        },

        grandTotal(key) {
            return this.groupTotal(this.viewDataRows, key);
        },

        fmtShort(val) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(val) || 0);
        },

        exportData() {
            if (this.selectedCostCenter === '') return;
            window.location.href = `<?= base_url('opex-selling/exportExcel') ?>?dept=` + encodeURIComponent(this.selectedCostCenter);
        },

        init() {
            this.selectedCostCenter = '0';
            this.fetchViewData();
        }
    }
}
</script>
