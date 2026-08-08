<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="fohActualPage()" class="p-4 md:p-8 mx-auto max-w-(--breakpoint-2xl) space-y-6 md:space-y-8">

  <!-- HEADER -->
  <div>
    <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-3">
      <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
      <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
      <span>FOH</span>
      <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
      <span class="text-brand-500 font-bold">Actual (Realisasi)</span>
    </div>
    <h1 class="text-2xl md:text-3xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-4">
      <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400">
        <i class="fa-solid fa-chart-column text-xl"></i>
      </span>
      Actual Data FOH
    </h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
      Pengelolaan data aktual Factory Overhead per Cost Center — Tahun Anggaran <span class="font-bold text-brand-500"><?= esc($workingYear) ?></span>
    </p>
  </div>

  <!-- MAIN CARD -->
  <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">

    <!-- TAB NAV -->
    <div class="border-b border-gray-200/80 dark:border-gray-800 px-6 pt-4">
      <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <button
          @click="activeTab = 'actual'"
          :class="activeTab === 'actual' ? 'border-brand-600 text-brand-600 dark:border-brand-400 dark:text-brand-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
          class="flex items-center gap-2 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
          <i class="fa-solid fa-table-cells-large text-xs"></i>
          Actual Data
        </button>

        <button
          @click="activeTab = 'download'"
          :class="activeTab === 'download' ? 'border-brand-600 text-brand-600 dark:border-brand-400 dark:text-brand-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
          class="flex items-center gap-2 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
          <i class="fa-solid fa-cloud-arrow-down text-xs"></i>
          Download Template
        </button>

        <button
          @click="activeTab = 'upload'"
          :class="activeTab === 'upload' ? 'border-brand-600 text-brand-600 dark:border-brand-400 dark:text-brand-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
          class="flex items-center gap-2 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
          <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
          Upload Data
        </button>
      </nav>
    </div>

    <!-- TAB CONTENT -->
    <div class="p-6">
      <div x-show="activeTab === 'actual'" x-cloak>
        <?= $this->include('foh/partials/tab_actual_data') ?>
      </div>

      <div x-show="activeTab === 'download'" x-cloak>
        <?= $this->include('foh/partials/tab_download_template') ?>
      </div>

      <div x-show="activeTab === 'upload'" x-cloak>
        <?= $this->include('foh/partials/tab_upload_data') ?>
      </div>
    </div>

  </div>
</div>

<script>
function fohActualPage() {
  return {
    activeTab: 'actual',
    selectedCostCenter: null,
    selectedTemplateCC: '',
    isUploadModalOpen: false,
    uploadSource: '',
    months: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
    costCenterList: <?= json_encode($costCenters) ?>,
    actualTableData: [],
    loading: false,
    currentPage: 1,
    perPage: 10,
    total: 0,
    get totalPages() {
      return Math.ceil(this.total / this.perPage) || 1;
    },

    filteredCostCenters(search) {
      if (!search) return this.costCenterList;
      const q = search.toLowerCase();
      return this.costCenterList.filter(c =>
        (c.cost_center || '').toString().includes(q) ||
        (c.cost_desc || '').toLowerCase().includes(q) ||
        (c.cc_code || '').toLowerCase().includes(q)
      );
    },

    async fetchActualData() {
      if (!this.selectedCostCenter) return;
      this.loading = true;
      this.currentPage = 1;
      try {
        const res = await window.ypFetch('<?= base_url('foh/cariActualTable') ?>', {
          dept: this.selectedCostCenter.cost_center,
          page: this.currentPage
        });
        this.actualTableData = res.rows || [];
        this.total = res.total || 0;
      } catch (err) {
        console.error('Gagal memuat data actual:', err);
        this.actualTableData = [];
        this.total = 0;
      } finally {
        this.loading = false;
      }
    },

    async goPage(page) {
      if (page < 1 || page > this.totalPages) return;
      this.currentPage = page;
      this.loading = true;
      try {
        const res = await window.ypFetch('<?= base_url('foh/cariActualTable') ?>', {
          dept: this.selectedCostCenter.cost_center,
          page: this.currentPage
        });
        this.actualTableData = res.rows || [];
        this.total = res.total || 0;
      } catch (err) {
        console.error('Gagal memuat data:', err);
      } finally {
        this.loading = false;
      }
    },

    downloadTemplate() {
      if (!this.selectedTemplateCC) return;
      window.location.href = `<?= base_url('foh/download-template') ?>?cc=` + this.selectedTemplateCC;
    },

    openUploadModal(source) {
      this.uploadSource = source;
      this.isUploadModalOpen = true;
    },

    formatNumber(num) {
      return new Intl.NumberFormat('id-ID').format(num || 0);
    }
  }
}
</script>

<?= $this->endSection() ?>
