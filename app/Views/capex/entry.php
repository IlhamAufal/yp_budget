<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="capexEntryApp()" x-init="loadEntryData()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="hover:text-[#2F3185]">CAPEX</span>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold">Entry Form</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Entry Form CAPEX
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Pengisian dan peninjauan pengajuan belanja modal (Capital Expenditure).
            </p>
        </div>
        <div class="shrink-0">
            <button type="button" data-action="open-modal"
                    data-modal-url="<?= base_url('manual-book/view') ?>?file=<?= rawurlencode('MANUAL BOOK - BUDGET SYSTEM - INPUT CAPEX.pdf') ?>&amp;title=<?= rawurlencode('Manual Book - Input CAPEX') ?>"
                    data-modal-title="Manual Book - Input CAPEX"
                    data-modal-size="xl"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fa-solid fa-book-open"></i>
                <span>Manual Book</span>
            </button>
        </div>
    </div>

    <!-- Sub Tabs Navigation -->
    <div class="inline-flex max-w-full nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="CAPEX Entry Tabs">
            <button type="button" @click="activeSubTab = 'entry'"
                :class="activeSubTab === 'entry' ? 'active' : ''"
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>6.1 Entry CAPEX</span>
            </button>
            <button type="button" @click="activeSubTab = 'view_cost_center'"
                :class="activeSubTab === 'view_cost_center' ? 'active' : ''"
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>6.2 View By Cost Center</span>
            </button>
        </nav>
    </div>

    <div>
        <div class="space-y-6">
            <div x-show="activeSubTab === 'entry'" x-transition:enter.opacity.duration.300ms>
                <?= $this->include('capex/partials/tab_entry_capex') ?>
            </div>

            <div x-show="activeSubTab === 'view_cost_center'" x-transition:enter.opacity.duration.300ms>
                <?= $this->include('capex/partials/tab_view_by_cost_center') ?>
            </div>
        </div>
    </div>

    <?= $this->include('capex/partials/modal_form_capex') ?>
</div>

<script>
function capexEntryApp() {
    return {
        activeSubTab: 'entry',
        selectedCostCenter: <?= json_encode($cost_center_options[0]['id'] ?? '1000GP1100', JSON_HEX_TAG) ?>,
        viewCostCenter: <?= json_encode($cost_center_options[1]['id'] ?? ($cost_center_options[0]['id'] ?? '1000KA1004'), JSON_HEX_TAG) ?>,
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