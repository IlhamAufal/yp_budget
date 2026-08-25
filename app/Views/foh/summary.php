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

<div x-data="fohSummaryPage()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

  <!-- HEADER -->
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span class="hover:text-[#2F3185]">FOH</span>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span class="text-[#2F3185] font-bold">Summary</span>
      </div>
      <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        Summary FOH
      </h1>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
        Konsolidasi data aktual dan anggaran Factory Overhead.
      </p>
    </div>
  </div>

  <!-- TAB NAV -->
  <div class="nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
    <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="FOH Summary Tabs">
      <button
        type="button"
        @click="activeTab = 'view_data'"
        :class="activeTab === 'view_data' ? 'active' : ''"
        class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
        <span>View Data</span>
      </button>

      <button
        type="button"
        @click="activeTab = 'by_cost_center'"
        :class="activeTab === 'by_cost_center' ? 'active' : ''"
        class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
        <span>Summary by Cost Center</span>
      </button>

      <button
        type="button"
        @click="activeTab = 'by_account'"
        :class="activeTab === 'by_account' ? 'active' : ''"
        class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
        <span>Summary by Account</span>
      </button>
    </nav>
  </div>

  <!-- TAB CONTENT -->
  <div>
    <div class="space-y-6">
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
