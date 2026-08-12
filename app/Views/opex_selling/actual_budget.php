<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div x-data="opexSellingActualApp()" x-init="init()" class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white">Actual Data</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">4.1 OPEX - Selling</p>
        </div>
        <nav>
            <ol class="flex items-center gap-2 text-sm font-medium">
                <li class="text-gray-600 dark:text-gray-400">Budget Plan Year : <span class="font-bold text-danger"><?= esc($workingYear ?? '') ?></span></li>
                <li class="text-gray-400">|</li>
                <li><a class="text-gray-600 hover:text-primary dark:text-gray-400" href="<?= base_url() ?>">Home</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-primary">Actual Data</li>
            </ol>
        </nav>
    </div>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6 dark:border-strokedark">
            <div class="flex gap-8">
                <button @click="activeTab = 'actual'" :class="activeTab === 'actual' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-600 hover:text-primary dark:text-gray-400'" class="border-b-2 py-4 text-sm font-medium transition-colors focus:outline-none">Actual Data</button>
                <button @click="activeTab = 'download'" :class="activeTab === 'download' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-600 hover:text-primary dark:text-gray-400'" class="border-b-2 py-4 text-sm font-medium transition-colors focus:outline-none">Download Template</button>
                <button @click="activeTab = 'upload'" :class="activeTab === 'upload' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-600 hover:text-primary dark:text-gray-400'" class="border-b-2 py-4 text-sm font-medium transition-colors focus:outline-none">Upload Data</button>
            </div>
        </div>

        <div class="p-6">
            <div x-show="activeTab === 'actual'" x-cloak><?= $this->include('opex_selling/partials/actual_tab_data') ?></div>
            <div x-show="activeTab === 'download'" x-cloak><?= $this->include('opex_selling/partials/actual_tab_download') ?></div>
            <div x-show="activeTab === 'upload'" x-cloak><?= $this->include('opex_selling/partials/actual_tab_upload') ?></div>
        </div>
    </div>

    <?= $this->include('opex_selling/partials/actual_modal_upload') ?>
</div>

<script>
function opexSellingActualApp() {
    return {
        activeTab: 'actual',
        costCenters: [
            { id: '600', name: 'Domestic' },
            { id: '700', name: 'Export' }
        ],
        selectedActualCc: '',
        selectedDownloadCc: '',

        uploadModalOpen: false,
        uploadType: '',
        uploadTitle: '',
        uploadFileName: '',
        uploadFeedback: '',
        uploadError: '',
        uploading: false,

        allRows: [],
        currentPage: 1,
        perPage: 25,
        search: '',
        loading: false,
        errorMessage: '',

        get filteredRows() {
            const term = this.search.trim().toLowerCase();
            if (!term) return this.allRows;
            return this.allRows.filter(row => [
                row.main_account,
                row.cost_center_header,
                row.cost_center_desc
            ].some(value => String(value || '').toLowerCase().includes(term)));
        },
        get totalData() { return this.filteredRows.length; },
        get totalPages() { return Math.ceil(this.totalData / this.perPage) || 1; },
        get pageFrom() { return this.totalData === 0 ? 0 : ((this.currentPage - 1) * this.perPage) + 1; },
        get pageTo() { return Math.min(this.currentPage * this.perPage, this.totalData); },

        init() {},

        fetchActualData() {
            if (!this.selectedActualCc) {
                this.allRows = [];
                this.currentPage = 1;
                this.errorMessage = '';
                return;
            }

            this.loading = true;
            this.errorMessage = '';
            fetch(`<?= base_url('opex-selling/getActualData') ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `dept=${encodeURIComponent(this.selectedActualCc)}`
            })
            .then(async response => {
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || 'Gagal memuat data actual.');
                return payload;
            })
            .then(res => {
                if (res.status !== 'success') throw new Error(res.message || 'Gagal memuat data actual.');
                this.allRows = (res.rows || []).map(row => {
                    const normalized = { ...row };
                    ['jan','feb','mar','apr','may','jun','jul','aug'].forEach(month => normalized[month] = parseFloat(row[month]) || 0);
                    normalized.total = ['jan','feb','mar','apr','may','jun','jul','aug'].reduce((sum, month) => sum + normalized[month], 0);
                    normalized.avg = normalized.total / 8;
                    return normalized;
                });
                this.currentPage = 1;
            })
            .catch(error => {
                this.allRows = [];
                this.currentPage = 1;
                this.errorMessage = error.message || 'Gagal memuat data actual.';
            })
            .finally(() => this.loading = false);
        },

        pageRows() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredRows.slice(start, start + this.perPage);
        },
        pageGroups() {
            const groups = [];
            const index = {};
            this.pageRows().forEach(row => {
                const name = String(row.cost_center_header || 'Lainnya');
                if (index[name] === undefined) {
                    index[name] = groups.length;
                    groups.push({ name, items: [] });
                }
                groups[index[name]].items.push(row);
            });
            return groups;
        },
        displayRows() {
            const entries = [];
            let number = this.pageFrom;
            this.pageGroups().forEach(group => {
                entries.push({ type: 'heading', name: group.name, key: `heading_${group.name}` });
                group.items.forEach(row => {
                    entries.push({ type: 'item', row, number: number++, key: `item_${row.id || row.main_account}` });
                });
                entries.push({ type: 'subtotal', name: group.name, key: `subtotal_${group.name}` });
            });
            return entries;
        },
        groupRows(groupName) {
            return this.filteredRows.filter(row => String(row.cost_center_header || 'Lainnya') === groupName);
        },
        groupTotal(groupName, month) {
            return this.groupRows(groupName).reduce((sum, row) => sum + (parseFloat(row[month]) || 0), 0);
        },
        filteredTotal(month) {
            return this.filteredRows.reduce((sum, row) => sum + (parseFloat(row[month]) || 0), 0);
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
                for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) pages.push(i);
                if (current < total - 2) pages.push('...');
                pages.push(total);
            }
            return pages;
        },
        goPage(page) {
            if (page < 1 || page > this.totalPages || page === this.currentPage) return;
            this.currentPage = page;
        },
        onSearch() { this.currentPage = 1; },
        onChangePerPage() {
            this.perPage = parseInt(this.perPage, 10) || 25;
            this.currentPage = 1;
        },
        formatNumber(value) {
            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(parseFloat(value) || 0);
        },

        triggerDownloadTemplate() {
            if (!this.selectedDownloadCc) return;
            window.location.href = `<?= base_url('opex-selling/download-template') ?>?cost_center=${encodeURIComponent(this.selectedDownloadCc)}`;
        },
        openUploadModal(type, title) {
            this.uploadType = type;
            this.uploadTitle = title;
            this.uploadFileName = '';
            this.uploadError = '';
            this.uploadModalOpen = true;
        },
        handleUploadFile(event) {
            const file = event.target.files[0];
            this.uploadFileName = file ? file.name : '';
            this.uploadError = '';
            if (file && !/\.(xls|xlsx)$/i.test(file.name)) {
                this.uploadError = 'File harus berformat .xls atau .xlsx.';
                event.target.value = '';
                this.uploadFileName = '';
            }
        },
        handleUploadDrop(event) {
            const file = event.dataTransfer.files[0];
            const input = event.currentTarget.closest('form').querySelector('input[type="file"]');
            if (!file) return;
            if (!/\.(xls|xlsx)$/i.test(file.name)) {
                this.uploadError = 'File harus berformat .xls atau .xlsx.';
                return;
            }
            const transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
            this.uploadFileName = file.name;
            this.uploadError = '';
        },
        submitUpload(event) {
            const form = event.target;
            const file = form.querySelector('input[type="file"]').files[0];
            if (!file) {
                this.uploadError = 'File Excel wajib dipilih.';
                return;
            }
            this.uploading = true;
            this.uploadError = '';
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(async response => {
                const payload = await response.json();
                if (!response.ok || payload.status !== 'success') throw new Error(payload.message || 'Upload gagal.');
                return payload;
            })
            .then(payload => {
                this.uploadFeedback = payload.message || 'Data actual berhasil diimport.';
                const importedDepartments = (payload.departments || []).map(String);
                if (this.selectedActualCc && importedDepartments.includes(String(this.selectedActualCc))) {
                    this.fetchActualData();
                } else if (importedDepartments.length > 0) {
                    this.uploadFeedback += ' Pilih Domestic (600) atau Export (700) yang sesuai untuk melihat data terbaru.';
                }
                this.uploadModalOpen = false;
                form.reset();
                this.uploadFileName = '';
            })
            .catch(error => this.uploadError = error.message || 'Upload gagal.')
            .finally(() => this.uploading = false);
        }
    }
}
</script>
<?= $this->endSection() ?>