<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="capexEntryApp()" x-init="loadEntryData()" class="mx-auto max-w-7xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white">Entry Form Capex</h2>
            <p class="text-sm text-gray-500">Budget Plan Year : <span class="font-semibold text-brand-600">2027</span></p>
        </div>
        <nav>
            <ol class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <li><a class="hover:text-primary" href="<?= base_url('dashboard') ?>">Home</a></li>
                <li class="before:content-['/'] before:mr-2">Entry Form Capex</li>
            </ol>
        </nav>
    </div>

    <div class="mb-6 border-b border-gray-200 dark:border-gray-800">
        <div class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <button @click="activeSubTab = 'entry'"
                :class="activeSubTab === 'entry' ? 'border-brand-600 text-brand-600 dark:text-brand-400 dark:border-brand-400' : 'border-transparent text-gray-500 hover:text-gray-600 hover:border-gray-300'"
                class="inline-flex items-center gap-2 p-4 border-b-2 rounded-t-lg transition-colors font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                6.1 Entry Capex
            </button>
            <button @click="activeSubTab = 'view_cost_center'"
                :class="activeSubTab === 'view_cost_center' ? 'border-brand-600 text-brand-600 dark:text-brand-400 dark:border-brand-400' : 'border-transparent text-gray-500 hover:text-gray-600 hover:border-gray-300'"
                class="inline-flex items-center gap-2 p-4 border-b-2 rounded-t-lg transition-colors font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                6.2 View By Cost Center
            </button>
        </div>
    </div>

    <div x-show="activeSubTab === 'entry'" x-transition:enter.opacity.duration.300ms>
        <?= $this->include('capex/partials/tab_entry_capex') ?>
    </div>

    <div x-show="activeSubTab === 'view_cost_center'" x-transition:enter.opacity.duration.300ms>
        <?= $this->include('capex/partials/tab_view_by_cost_center') ?>
    </div>

    <?= $this->include('capex/partials/modal_form_capex') ?>
    <?= $this->include('partials/manual_book_modal', [
        'mbTitle' => 'Manual Book - Input CAPEX',
        'mbPdfUrl' => base_url('assets/docs/manual_book/MANUAL BOOK - BUDGET SYSTEM - INPUT CAPEX.pdf'),
        'mbPdfExists' => is_file(FCPATH . 'assets/docs/manual_book/MANUAL BOOK - BUDGET SYSTEM - INPUT CAPEX.pdf'),
    ]) ?>
</div>

<script>
function capexEntryApp() {
    return {
        activeSubTab: 'entry',
        selectedCostCenter: <?= json_encode($cost_center_options[0]['id'] ?? '1000GP1100', JSON_HEX_TAG) ?>,
        viewCostCenter: <?= json_encode($cost_center_options[1]['id'] ?? ($cost_center_options[0]['id'] ?? '1000KA1004'), JSON_HEX_TAG) ?>,
        manualBookOpen: false,
        formCapexOpen: false,
        activeCategory: { code: '', name: '' },
        saving: false,

        // Semua cost center aktif dari master (format sesuai cost_center_sap)
        costCenters: <?= json_encode($cost_center_options ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,

        // Kategori aset dari master depresiasi (bukan mock)
        categories: <?= json_encode($categories ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,

        // Data tersimpan per kategori (dari DB) untuk tabel tab_entry_capex
        entryData: {},
        loadingEntry: false,
        monthKeys: ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'],

        // Modal Form Rows State
        emptyRow() {
            return {
                description: '',
                costCenter: '',
                newLines: 'Tidak',
                qty: 0,
                unitPrice: 0,
                remarks: '',
                jan: 0, feb: 0, mar: 0, apr: 0, may: 0, jun: 0, jul: 0, aug: 0, sep: 0, oct: 0, nov: 0, dec: 0
            };
        },

        formRows: [
            {
                description: '',
                costCenter: '',
                newLines: 'Tidak',
                qty: 0,
                unitPrice: 0,
                remarks: '',
                jan: 0, feb: 0, mar: 0, apr: 0, may: 0, jun: 0, jul: 0, aug: 0, sep: 0, oct: 0, nov: 0, dec: 0
            }
        ],

        async loadEntryData() {
            if (!this.selectedCostCenter) return;
            this.loadingEntry = true;
            try {
                const res = await fetch(`<?= base_url('capex/getEntryData') ?>?dept=${encodeURIComponent(this.selectedCostCenter)}`);
                const json = await res.json();
                const map = {};
                (json.rows || []).forEach(r => { map[r.category_code] = r; });
                this.entryData = map;
            } catch (e) {
                this.entryData = {};
            } finally {
                this.loadingEntry = false;
            }
        },

        // Nilai satu bulan utk satu kategori (fallback 0 bila belum ada data)
        catVal(cat, key) {
            const d = this.entryData[cat.code];
            return d ? (parseFloat(d[key]) || 0) : 0;
        },

        catTotal(cat) {
            return this.monthKeys.reduce((sum, m) => sum + this.catVal(cat, m), 0);
        },

        openFormCapex(cat) {
            this.activeCategory = cat;
            this.formRows = [this.emptyRow()];
            this.formCapexOpen = true;
        },

        addRow() {
            this.formRows.push(this.emptyRow());
        },

        removeRow(index) {
            if (this.formRows.length > 1) {
                this.formRows.splice(index, 1);
            }
        },

        rowTotal(row) {
            return ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec']
                .reduce((sum, m) => sum + (parseFloat(row[m]) || 0), 0);
        },

        columnTotal(key) {
            return this.formRows.reduce((sum, row) => sum + (parseFloat(row[key]) || 0), 0);
        },

        totalColumn() {
            return this.formRows.reduce((sum, row) => sum + this.rowTotal(row), 0);
        },

        fmtNumber(val) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(val) || 0);
        },

        async saveFormCapex() {
            if (this.saving) return;

            const rows = this.formRows.filter(r => (r.description || '').trim() !== '');
            if (rows.length === 0) {
                alert('Isi minimal satu item dengan DESCRIPTION.');
                return;
            }

            this.saving = true;
            try {
                const body = new FormData();
                body.append('main_account', this.activeCategory.code);
                body.append('dept', this.selectedCostCenter);
                body.append('rows', JSON.stringify(rows));

                const res = await fetch(`<?= base_url('capex/saveFormCapex') ?>`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: body
                });
                const json = await res.json();

                if (json.status === 'success') {
                    alert(json.message || 'Data CAPEX berhasil disimpan!');
                    this.formCapexOpen = false;
                    this.formRows = [this.emptyRow()];
                    await this.loadEntryData();
                } else {
                    alert(json.message || 'Gagal menyimpan data.');
                }
            } catch (e) {
                alert('Terjadi kesalahan saat menyimpan data.');
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>
<?= $this->endSection() ?>