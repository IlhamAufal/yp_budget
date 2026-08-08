<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="fohSummaryPage()" class="space-y-6 p-4 sm:p-6">

  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Summary FOH</h2>
      <p class="text-sm text-gray-500 dark:text-gray-400">Konsolidasi data aktual dan anggaran Factory Overhead</p>
    </div>
    <div class="flex items-center gap-2 text-sm text-gray-500">
      <span class="font-medium text-brand-600">Budget Plan Year:</span>
      <span class="rounded bg-brand-50 px-2.5 py-1 font-bold text-brand-700 dark:bg-brand-900/30 dark:text-brand-300">2027</span>
    </div>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
    
    <div class="border-b border-gray-200 px-6 pt-4 dark:border-gray-800">
      <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <button 
          @click="activeTab = 'view_data'"
          :class="activeTab === 'view_data' ? 'border-brand-600 text-brand-600 dark:border-brand-400 dark:text-brand-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
          class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
          View Data
        </button>

        <button 
          @click="activeTab = 'by_cost_center'"
          :class="activeTab === 'by_cost_center' ? 'border-brand-600 text-brand-600 dark:border-brand-400 dark:text-brand-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
          class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
          Summary by Cost Center
        </button>

        <button 
          @click="activeTab = 'by_account'"
          :class="activeTab === 'by_account' ? 'border-brand-600 text-brand-600 dark:border-brand-400 dark:text-brand-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
          class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
          Summary by Account
        </button>
      </nav>
    </div>

    <div class="p-6">
      <div x-show="activeTab === 'view_data'" x-cloak>
        <?= $this->include('foh/partials/summary_tab_view_data') ?>
      </div>

      <div x-show="activeTab === 'by_cost_center'" x-cloak>
        <?= $this->include('foh/partials/summary_tab_by_cost_center') ?>
      </div>

      <div x-show="activeTab === 'by_account'" x-cloak>
        <?= $this->include('foh/partials/summary_tab_by_account') ?>
      </div>
    </div>

  </div>
</div>

<script>
function fohSummaryPage() {
  return {
    activeTab: 'view_data',
    
    // Sub-Tab 1 States
    selectedCostCenter: 'ALL',
    costCenterList: [
      { id: 'ALL', label: '[All Cost Center] Include New Lines' },
      { id: '630', label: '[630] FG Warehouse Department' },
      { id: '636', label: '[636] FG Warehouse Department - KRG' },
      { id: '810', label: '[810] Production Director' },
      { id: '820', label: '[820] PPIC Department' }
    ],

    // Sub-Tab 2 States
    selectedCCViewMode: 'By Cost Center (Existing)',
    ccViewModes: ['By Cost Center (Existing)', 'By Cost Center (New Line)', 'All'],

    // Sub-Tab 3 States
    selectedAccountViewMode: 'By Account (Existing)',
    accountViewModes: ['By Account (Existing)', 'By Account (New Line)', 'All'],

    // Helper Functions
    exportDataExcel() {
      window.location.href = `<?= base_url('foh/summary/export') ?>?cc=` + this.selectedCostCenter;
    },

    formatNumber(val) {
      return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0);
    }
  }
}
</script>
<?= $this->endSection() ?>
