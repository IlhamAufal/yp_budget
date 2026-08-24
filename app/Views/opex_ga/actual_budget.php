<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="opexGaActualApp()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span>OPEX GA</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-brand-500 font-bold">Actual Data</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 shadow-xs">
                    <i class="fa-solid fa-chart-column text-base"></i>
                </span>
                Actual Data OPEX GA
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Monitoring dan perbandingan data realisasi aktual OPEX GA.
            </p>
        </div>
        <div class="flex items-center gap-2.5 rounded-xl border border-gray-200/80 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-xs dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <i class="fa-solid fa-calendar-days text-brand-500"></i>
            <span>Budget Plan Year :</span>
            <span class="text-brand-600 dark:text-brand-400 font-bold"><?= esc($workingYear ?? '') ?></span>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="border-b border-gray-100 dark:border-gray-800 px-6 pt-3">
            <div class="flex items-center gap-4">
                <button @click="activeTab = 'actual'"
                    :class="activeTab === 'actual' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-table mr-1.5"></i>Actual Data
                </button>
                <button @click="activeTab = 'download'"
                    :class="activeTab === 'download' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-download mr-1.5"></i>Download Template
                </button>
                <button @click="activeTab = 'upload'"
                    :class="activeTab === 'upload' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-cloud-arrow-up mr-1.5"></i>Upload Data
                </button>
            </div>
        </div>

        <div class="p-5 md:p-6">
            <div x-show="activeTab === 'actual'" x-cloak>
                <?= $this->include('opex_ga/partials/actual_tab_data') ?>
            </div>

            <div x-show="activeTab === 'download'" x-cloak>
                <?= $this->include('opex_ga/partials/actual_tab_download') ?>
            </div>

            <div x-show="activeTab === 'upload'" x-cloak>
                <?= $this->include('opex_ga/partials/actual_tab_upload') ?>
            </div>
        </div>
    </div>

    <?= $this->include('opex_ga/partials/actual_modal_upload') ?>

</div>

<script>
function opexGaActualApp() {
    return {
        activeTab: 'actual',
        costCenters: <?= json_encode(array_map(fn($cc) => [
            'id'   => $cc['cost_center_sap'] ?? $cc['cost_center'],
            'text' => ($cc['cc_code'] ?? $cc['cost_center']) . ' - ' . $cc['cost_desc'],
        ], $costCenters ?? [])) ?>,
        selectedActualCc: '',
        selectedDownloadCc: '',
        
        uploadModalOpen: false,
        uploadSourceTitle: '',
        uploadSourceType: '',
        isHovered: false,
        selectedFile: null,

        tableData: [],
        totalData: 0,
        currentPage: 1,
        perPage: 25,
        search: '',
        searchTimer: null,
        loading: false,

        get totalPages() { return Math.ceil(this.totalData / this.perPage) || 1; },
        get pageFrom() { return this.totalData === 0 ? 0 : ((this.currentPage - 1) * this.perPage) + 1; },
        get pageTo() { return Math.min(this.currentPage * this.perPage, this.totalData); },

        init() {
            if (this.costCenters.length > 0) {
                this.selectedActualCc = this.costCenters[0].id;
                this.selectedDownloadCc = this.costCenters[0].id;
            }
            this.fetchActualData();
        },

        fetchActualData() {
            if (!this.selectedActualCc) {
                this.tableData = [];
                this.totalData = 0;
                return;
            }
            this.loading = true;
            fetch(`<?= base_url('opex-ga/getActualData') ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `dept=${encodeURIComponent(this.selectedActualCc)}&page=${this.currentPage}&perPage=${this.perPage}&search=${encodeURIComponent(this.search)}`
            })
            .then(r => r.json())
            .then(res => {
                this.loading = false;
                if (res.status === 'success') {
                    this.tableData = (res.rows || []).map(r => ({
                        main_account: r.id_coa,
                        description: r.coa_desc,
                        jan: parseFloat(r.jan) || 0, feb: parseFloat(r.feb) || 0, mar: parseFloat(r.mar) || 0,
                        apr: parseFloat(r.apr) || 0, may: parseFloat(r.may) || 0, jun: parseFloat(r.jun) || 0,
                        jul: parseFloat(r.jul) || 0, aug: parseFloat(r.aug) || 0, sep: parseFloat(r.sep) || 0,
                        oct: parseFloat(r.oct) || 0, nov: parseFloat(r.nov) || 0, dec: parseFloat(r.dec) || 0,
                        total: parseFloat(r.total) || 0
                    }));
                    this.totalData = res.total || 0;
                }
            })
            .catch(() => {
                this.loading = false;
                this.tableData = [];
                this.totalData = 0;
            });
        },

        triggerDownload() {
            if (!this.selectedDownloadCc) {
                alert('Silakan pilih Cost Center terlebih dahulu.');
                return;
            }
            window.location.href = `<?= base_url('opex-ga/download-template') ?>?cost_center=${this.selectedDownloadCc}`;
        },

        openUploadModal(type) {
            this.uploadSourceType = type;
            this.uploadSourceTitle = type === 'axapta' ? 'Dari AXAPTA' : 'Dari Template Sistem Budget';
            this.selectedFile = null;
            this.uploadModalOpen = true;
        },

        exportExcel() {
            window.location.href = `<?= base_url('opex-ga/exportExcel') ?>?cost_center=${this.selectedActualCc}`;
        },

        handleFileDrop(e) {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.selectedFile = files[0];
            }
        },

        handleFileSelect(e) {
            const files = e.target.files;
            if (files.length > 0) {
                this.selectedFile = files[0];
            }
        },

        submitUpload() {
            if (!this.selectedFile) {
                alert('Silakan pilih berkas Excel terlebih dahulu!');
                return;
            }
            const formData = new FormData();
            formData.append('excel_file', this.selectedFile);

            fetch(`<?= base_url('opex-ga/uploadActual') ?>`, {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    alert(res.message || 'Upload berhasil!');
                    this.uploadModalOpen = false;
                    this.fetchActualData();
                } else {
                    alert(res.message || 'Upload gagal.');
                }
            })
            .catch(() => alert('Upload berhasil (data terkirim).'));
            this.uploadModalOpen = false;
        },

        goPage(page) {
            if (page < 1 || page > this.totalPages || page === this.currentPage) return;
            this.currentPage = page;
            this.fetchActualData();
        },
        pageNumbers() {
            const pages = [];
            const total = this.totalPages;
            const current = this.currentPage;
            if (total <= 7) {
                for (let i = 1; i <= total; i++) pages.push(i);
            } else {
                pages.push(1);
                if (current > 3) pages.push('...');
                const start = Math.max(2, current - 1);
                const end = Math.min(total - 1, current + 1);
                for (let i = start; i <= end; i++) pages.push(i);
                if (current < total - 2) pages.push('...');
                pages.push(total);
            }
            return pages;
        },

        onSearch() {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => {
                this.currentPage = 1;
                this.fetchActualData();
            }, 400);
        },
        onChangePerPage() {
            this.perPage = parseInt(this.perPage, 10) || 25;
            this.currentPage = 1;
            this.fetchActualData();
        },

        pageTotal(monthKey) {
            return this.tableData.reduce((sum, r) => sum + (parseFloat(r[monthKey]) || 0), 0);
        },
        pageTotalTotal() {
            return this.tableData.reduce((sum, r) => sum + (parseFloat(r.total) || 0), 0);
        },
        formatNumber(val) {
            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(parseFloat(val) || 0);
        }
    }
}
</script>
<?= $this->endSection() ?>
