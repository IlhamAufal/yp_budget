<div x-data="opexSellingViewTab()" x-init="init()" class="space-y-6">

  <!-- Filter -->
  <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div class="w-full max-w-2xl">
        <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Cost Center</label>
        <div class="relative">
          <div @click="dropdownOpen = !dropdownOpen" class="flex w-full items-center justify-between rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-800 cursor-pointer dark:text-white transition-all focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:bg-white">
            <span x-text="selectedCostCenter !== '' ? costCenters.find(c => c.id === selectedCostCenter)?.name || selectedCostCenter : 'Pilih Cost Center'"></span>
            <i class="fa-solid fa-chevron-down text-[10px] text-gray-400"></i>
          </div>
          <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-cloak class="absolute left-0 top-full z-40 mt-1 w-full rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800 overflow-hidden">
            <div class="p-3 border-b border-gray-100 dark:border-gray-700">
              <input type="text" x-model="searchCostCenter" placeholder="Cari Cost Center..." class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3.5 py-2 text-xs focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:outline-none dark:text-white">
            </div>
            <ul class="max-h-48 overflow-y-auto text-xs p-1.5">
              <template x-for="item in costCenters.filter(c => c.name.toLowerCase().includes(searchCostCenter.toLowerCase()))" :key="item.id">
                <li @click="selectCostCenter(item)" class="cursor-pointer px-3.5 py-2 rounded-xl hover:bg-[#2F3185]/10 hover:text-[#2F3185] dark:hover:bg-[#2F3185]/20 dark:hover:text-indigo-400 transition-colors" :class="selectedCostCenter === item.id ? 'bg-[#2F3185] text-white font-bold' : 'text-gray-700 dark:text-gray-300'" x-text="item.name"></li>
              </template>
            </ul>
          </div>
        </div>
      </div>
      <button @click="exportData()" :disabled="selectedCostCenter === ''" :class="selectedCostCenter !== '' ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs' : 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600'" class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-xs font-semibold transition-all shadow-xs shrink-0 active:scale-[0.98]">
        <i class="fa-solid fa-file-excel text-xs"></i>
        <span>Export Data</span>
      </button>
    </div>
  </div>

  <!-- Flat View Data -->
  <div x-show="selectedCostCenter !== ''" x-cloak class="space-y-4">
    <!-- Table Section Label (Separated from table container) -->
    <div>
      <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Konsolidasi Actual vs Budget OPEX Selling</h3>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbandingan realisasi biaya actual dengan anggaran budget penjualan.</p>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs overflow-hidden dark:border-gray-800 dark:bg-gray-900">
      <div class="max-w-full overflow-x-auto scrollbar-thin" style="max-height: 70vh;">
        <table class="w-full table-auto text-left text-xs text-gray-600 dark:text-gray-300 border-collapse min-w-[1400px] whitespace-nowrap">
          <thead class="sticky top-0 z-10 bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
            <tr class="border-b border-white/20 bg-[#2F3185] text-white font-semibold">
              <th rowspan="2" class="border-r border-white/20 py-3 px-3.5 min-w-[240px] text-white font-semibold">
                <span>Selling Expense</span>
                <span class="block text-[10px] font-normal text-white/80 mt-0.5" x-text="selectedCostCenter === '0' ? '[All Cost Center]' : selectedCostCenter"></span>
              </th>
              <th colspan="10" class="border-r border-white/20 py-2 px-2 text-center text-white bg-[#25276d] font-semibold">Actual</th>
              <th rowspan="2" class="border-r border-white/20 bg-[#25276d] w-1 p-0"></th>
              <th colspan="13" class="py-2 px-2 text-center text-white bg-[#25276d] font-semibold">Budget</th>
            </tr>
            <tr class="border-b border-white/20 bg-[#25276d] text-xs font-semibold text-white">
              <template x-for="m in actualMonths" :key="'actual_head_' + m"><th class="border-r border-white/20 py-2 px-2 text-right min-w-[65px] text-white font-semibold" x-text="m"></th></template>
              <template x-for="m in budgetHeaders" :key="'budget_head_' + m.key"><th class="border-r border-white/20 py-2 px-2 text-right min-w-[65px] text-white font-semibold" x-text="m.label"></th></template>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs">
            <tr x-show="viewLoading" x-cloak>
              <td colspan="25" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                    <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data...</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sistem sedang mengkonsolidasikan data.</p>
                </div>
              </td>
            </tr>
            <tr x-show="!viewLoading && viewDataRows.length === 0" x-cloak>
              <td colspan="25" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                    <i class="fa-solid fa-folder-open text-xl"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Ditemukan</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada rincian data budget untuk cost center terpilih.</p>
                </div>
              </td>
            </tr>

          </tbody>

          <template x-for="group in groupedRows" :key="group.header">
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
              <tr class="bg-gray-50/80 dark:bg-gray-800/60 font-bold text-gray-900 dark:text-white border-y border-gray-200 dark:border-gray-700">
                  <td class="border-r border-gray-200 dark:border-gray-700 py-2.5 px-3.5 font-sans font-bold" x-text="'SUBTOTAL ' + group.header"></td>
                  <template x-for="m in actualMonths" :key="'subtotal_actual_' + group.header + '_' + m">
                    <td class="border-r border-gray-200 dark:border-gray-700 py-2.5 px-2 text-right font-mono" x-text="fmtShort(groupTotal(group.rows, m))"></td>
                  </template>
                  <td class="bg-gray-200 dark:bg-gray-700 w-1 p-0"></td>
                  <template x-for="m in budgetMonths" :key="'subtotal_budget_' + group.header + '_' + m">
                    <td class="border-r border-gray-200 dark:border-gray-700 py-2.5 px-2 text-right font-mono font-bold text-[#2F3185] dark:text-indigo-400" x-text="fmtShort(groupTotal(group.rows, m))"></td>
                  </template>
                </tr>

                <template x-for="row in group.rows" :key="group.header + '_' + row.main_account">
                  <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors border-b border-gray-200 dark:border-gray-800">
                    <td class="border-r border-gray-200 dark:border-gray-800 py-2 px-3.5 font-sans text-gray-900 dark:text-white font-medium">
                      <span class="mr-1.5 text-gray-400">↳</span><span x-text="row.main_account + ' - ' + row.cost_center_desc"></span>
                    </td>
                    <template x-for="m in actualMonths" :key="'row_actual_' + row.main_account + '_' + m">
                      <td class="border-r border-gray-200 dark:border-gray-800 py-2 px-2 text-right text-gray-700 dark:text-gray-300" x-text="fmtShort(row[m])"></td>
                    </template>
                    <td class="bg-gray-200 dark:bg-gray-700 w-1 p-0"></td>
                    <template x-for="m in budgetMonths" :key="'row_budget_' + row.main_account + '_' + m">
                      <td class="border-r border-gray-200 dark:border-gray-800 py-2 px-2 text-right text-gray-700 dark:text-gray-300" x-text="fmtShort(row[m])"></td>
                    </template>
                  </tr>
                </template>
            </tbody>
          </template>
          <tfoot>
            <tr x-show="!viewLoading && viewDataRows.length > 0" class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs font-mono">
              <td class="border-r border-gray-300 dark:border-gray-700 py-3 px-3.5 font-sans font-bold text-right">Grand Total</td>
              <template x-for="m in actualMonths" :key="'grand_actual_' + m">
                <td class="border-r border-gray-300 dark:border-gray-700 py-3 px-2 text-right" x-text="fmtShort(grandTotal(m))"></td>
              </template>
              <td class="bg-gray-200 dark:bg-gray-700 w-1 p-0"></td>
              <template x-for="m in budgetMonths" :key="'grand_budget_' + m">
                <td class="border-r border-gray-300 dark:border-gray-700 py-3 px-2 text-right text-[#2F3185] dark:text-indigo-400 font-bold" x-text="fmtShort(grandTotal(m))"></td>
              </template>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <div x-show="selectedCostCenter === ''" x-cloak class="rounded-2xl border border-gray-200/80 bg-white p-12 dark:border-gray-800 dark:bg-gray-900 text-center shadow-xs">
    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
      <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
        <i class="fa-solid fa-chart-column text-xl"></i>
      </div>
      <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Pilih Cost Center Terlebih Dahulu</p>
      <p class="text-xs text-gray-500 dark:text-gray-400">Silakan pilih Cost Center pada dropdown di atas untuk meninjau data konsolidasi View Data.</p>
    </div>
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
