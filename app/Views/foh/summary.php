<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
// Dropdown cost center dinamis (replika lama): mode 0/1/2/3 + [New Lines] + cost center biasa
$options = array_merge(
    [
        ['id' => '', 'label' => '- Pilih -'],
        ['id' => '0', 'label' => '[All Cost Center] Include New Lines'],
        ['id' => '1', 'label' => '[All Cost Center] Only New Lines'],
        ['id' => '2', 'label' => '[All Cost Center] Exclude New Lines'],
        ['id' => '3', 'label' => '[Eng Cost Center] All Cost Center Engineering'],
    ],
    array_map(static fn ($d) => [
        'id'    => (string) $d['cost_center'] . '000',
        'label' => '[New Lines] ' . ($d['cost_desc'] ?? ''),
    ], $deptx2 ?? []),
    array_map(static fn ($d) => [
        'id'    => (string) $d['cost_center'],
        'label' => '[' . $d['cost_center'] . '] ' . ($d['cost_desc'] ?? ''),
    ], $deptx ?? [])
);
?>

<div x-data="fohSummaryPage()" class="space-y-6 p-4 sm:p-6">

  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Summary FOH</h2>
      <p class="text-sm text-gray-500 dark:text-gray-400">Konsolidasi data aktual dan anggaran Factory Overhead</p>
    </div>
    <div class="flex items-center gap-2 text-sm text-gray-500">
      <span class="font-medium text-brand-600">Budget Plan Year:</span>
      <span class="rounded bg-brand-50 px-2.5 py-1 font-bold text-brand-700 dark:bg-brand-900/30 dark:text-brand-300"><?= esc($workingYear ?? date('Y')) ?></span>
    </div>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">

    <div class="border-b border-gray-200 px-6 pt-4 dark:border-gray-800">
      <nav class="-mb-px flex gap-6 space-x-8" aria-label="Tabs">
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

    // Tab View Data
    selectedCostCenter: '',
    costCenterList: <?= json_encode($options) ?>,
    viewDataRows: [],
    viewDataLoading: false,

    // Tab Summary by Cost Center
    costCenterRows: [],
    ccLoading: false,

    // Tab Summary by Account
    accountRows: [],
    accLoading: false,

    // Kolom tabel View Data
    actualCols: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','AVG','TOT'],
    budgetCols: ['isi_1','isi_2','isi_3','isi_4','isi_5','isi_6','isi_7','isi_8','isi_9','isi_10','isi_11','isi_12','isi_tot'],

    async loadViewData() {
      if (!this.selectedCostCenter) { this.viewDataRows = []; return; }
      this.viewDataLoading = true;
      try {
        const res = await window.ypFetch('<?= base_url('foh/cariActualTable') ?>', {
          dept: this.selectedCostCenter
        });
        this.viewDataRows = Array.isArray(res) ? res : [];
      } catch (e) {
        this.viewDataRows = [];
      } finally {
        this.viewDataLoading = false;
      }
    },

    async loadSummaryCostCenter() {
      this.ccLoading = true;
      try {
        const res = await window.ypFetch('<?= base_url('foh/summaryCostCenter') ?>');
        this.costCenterRows = Array.isArray(res) ? res : [];
      } catch (e) {
        this.costCenterRows = [];
      } finally {
        this.ccLoading = false;
      }
    },

    async loadSummaryAccount() {
      this.accLoading = true;
      try {
        const res = await window.ypFetch('<?= base_url('foh/summaryAccount') ?>');
        this.accountRows = Array.isArray(res) ? res : [];
      } catch (e) {
        this.accountRows = [];
      } finally {
        this.accLoading = false;
      }
    },

    exportViewData() {
      const cc = this.selectedCostCenter || 'ALL';
      window.location.href = '<?= base_url('foh/summary/export') ?>?cc=' + encodeURIComponent(cc);
    },

    fmt(val) {
      return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(val) || 0);
    },

    groupBy(arr, key) {
      return (arr || []).reduce((acc, item) => {
        const k = (item && item[key]) || '';
        if (!acc[k]) acc[k] = [];
        acc[k].push(item);
        return acc;
      }, {});
    },

    sumColumn(arr, key) {
      return (arr || []).reduce((s, r) => s + (parseFloat(r && r[key]) || 0), 0);
    }
  }
}
</script>
<?= $this->endSection() ?>
