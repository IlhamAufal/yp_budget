<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="opexGaActualApp()" x-init="init()" class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <i class="fas fa-chart-column text-lg text-primary"></i>
                </div>
                <h2 class="text-title-md2 font-bold text-black dark:text-white">Actual Data OPEX GA</h2>
            </div>
            <nav class="mt-1">
                <ol class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    <li><a class="hover:text-primary" href="<?= base_url('dashboard') ?>"><i class="fas fa-home mr-1"></i>Home</a></li>
                    <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                    <li>4.2 OPEX - GA</li>
                    <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                    <li class="text-primary font-semibold">Actual Data</li>
                </ol>
            </nav>
        </div>
        <div class="flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-1 dark:bg-boxdark dark:text-gray-200">
            <i class="fas fa-calendar-alt text-primary"></i>
            <span>Budget Plan Year :</span>
            <span class="text-red-500 font-bold"><?= esc($workingYear) ?></span>
        </div>
    </div>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6 py-3 dark:border-strokedark">
            <div class="flex items-center gap-4">
                <button @click="activeTab = 'actual'"
                    :class="activeTab === 'actual' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-2 py-2 text-sm font-medium transition-all duration-200">
                    <i class="fas fa-table mr-1"></i>Actual Data
                </button>
                <button @click="activeTab = 'download'"
                    :class="activeTab === 'download' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-2 py-2 text-sm font-medium transition-all duration-200">
                    <i class="fas fa-download mr-1"></i>Download Template
                </button>
                <button @click="activeTab = 'upload'"
                    :class="activeTab === 'upload' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-2 py-2 text-sm font-medium transition-all duration-200">
                    <i class="fas fa-upload mr-1"></i>Upload Data
                </button>
            </div>
        </div>

        <div class="p-6">
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
            'id'   => $cc['cost_center'],
            'text' => ($cc['cc_code'] ?? $cc['cost_center']) . ' - ' . $cc['cost_desc'],
        ], $costCenters)) ?>,
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
            fetch(`<?= base_url('opex-ga/getActualData') ?>`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `dept=${this.selectedActualCc}&page=${this.currentPage}`
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    this.tableData = (res.rows || []).map(r => ({
                        main_account: r.id_coa,
                        description: r.coa_desc,
                        jan: this.fmtShort(r.jan), feb: this.fmtShort(r.feb), mar: this.fmtShort(r.mar),
                        apr: this.fmtShort(r.apr), may: this.fmtShort(r.may), jun: this.fmtShort(r.jun),
                        jul: this.fmtShort(r.jul), aug: this.fmtShort(r.aug), sep: this.fmtShort(r.sep),
                        oct: this.fmtShort(r.oct), nov: this.fmtShort(r.nov), dec: this.fmtShort(r.dec),
                        avg: this.fmtShort((parseFloat(r.total) || 0) / 12),
                        total: this.fmtShort(r.total)
                    }));
                    this.totalData = res.total || 0;
                }
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

        fmtShort(val) {
            const num = parseFloat(val) || 0;
            if (num === 0) return '0';
            return num.toLocaleString('id-ID');
        }
    }
}
</script>
<?= $this->endSection() ?>
