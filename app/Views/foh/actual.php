<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="fohActualPage()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

  <!-- HEADER -->
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span class="hover:text-[#2F3185]">FOH</span>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span class="text-[#2F3185] font-bold">Actual Data</span>
      </div>
      <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        Actual Data FOH
      </h1>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
        Pengelolaan data aktual Factory Overhead per Cost Center.
      </p>
    </div>
  </div>

  <!-- TAB NAV -->
  <div class="inline-flex max-w-full nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
    <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="FOH Actual Tabs">
      <button
        type="button"
        @click="activeTab = 'actual'"
        :class="activeTab === 'actual' ? 'active' : ''"
        class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
        <span>Actual Data</span>
      </button>

      <button
        type="button"
        @click="activeTab = 'download'"
        :class="activeTab === 'download' ? 'active' : ''"
        class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
        <span>Download Template</span>
      </button>

      <button
        type="button"
        @click="activeTab = 'upload'"
        :class="activeTab === 'upload' ? 'active' : ''"
        class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
        <span>Upload Data</span>
      </button>
    </nav>
  </div>

  <!-- TAB CONTENT -->
  <div>
    <div class="space-y-6">
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
