<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="mppEntry()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span>MPP</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-brand-500 font-bold">Entry Form</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 shadow-xs">
                    <i class="fa-solid fa-users text-base"></i>
                </span>
                Man Power Planning
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Perencanaan dan estimasi alokasi tenaga kerja departemen (Man Power Planning).
            </p>
        </div>
        <div class="flex items-center gap-2.5 rounded-xl border border-gray-200/80 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-xs dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <i class="fa-solid fa-calendar-days text-brand-500"></i>
            <span>Budget Plan Year :</span>
            <span class="text-brand-600 dark:text-brand-400 font-bold"><?= esc($workingYear) ?></span>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="border-b border-gray-100 dark:border-gray-800 px-6 pt-3">
            <div class="flex items-center gap-4">
                <button @click="activeTab = 'entry'"
                    :class="activeTab === 'entry' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-pen-to-square mr-1"></i>
                    7.1 Entry MPP
                </button>
                <button @click="activeTab = 'view'"
                    :class="activeTab === 'view' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-chart-column mr-1"></i>
                    7.3 View MPP Data
                </button>
            </div>
        </div>

        <div class="p-5 md:p-6 space-y-6">
            <div x-show="activeTab === 'entry'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                <?= $this->include('mpp/partials/entry_tab_mpp') ?>
            </div>

            <div x-show="activeTab === 'view'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                <?= $this->include('mpp/partials/entry_tab_view_data') ?>
            </div>
        </div>
    </div>

    <?= $this->include('mpp/partials/modal_mpp_form') ?>

</div>

<script>
function mppEntry() {
    return {
        activeTab: 'entry',
        loading: false,
        saving: false,

        // Cost Center
        selectedCostCenter: '<?= esc(($costCenterList[0]['cost_center'] ?? ''), 'js') ?>',
        costCenterList: <?= json_encode($costCenterList, JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP) ?>,

        // Period Info
        periodInfo: <?= json_encode($periodInfo) ?>,

        // Working Year
        workingYear: <?= json_encode($workingYear) ?>,

        // Matrix Data (Sub-Tab 1: Entry MPP)
        matrixData: [],

        // Modal State
        isModalOpen: false,
        modalCategory: '',
        modalTipeId: 0,
        modalPositions: [],
        modalNote: '',
        modalLoading: false,
        deletingPositionId: null,

        // View Data (Sub-Tab 2)
        viewData: [],
        viewTotals: [0,0,0,0,0,0,0,0,0,0,0,0,0],
        viewLoading: false,

        // CSRF Token
        csrfName: <?= json_encode(csrf_token()) ?>,
        csrfHash: <?= json_encode(csrf_hash()) ?>,

        init() {
            if (this.costCenterList.length > 0) {
                this.selectedCostCenter = String(this.costCenterList[0].cost_center);
            }
            this.$watch('selectedCostCenter', () => {
                if (this.selectedCostCenter) {
                    this.loadMatrixData();
                    this.loadViewData();
                }
            });
            if (this.selectedCostCenter) {
                this.loadMatrixData();
                this.loadViewData();
            }
        },

        // -------------------------------------------------------
        // Sub-Tab 1: Entry MPP - Load Matrix Data
        // -------------------------------------------------------
        async loadMatrixData() {
            if (!this.selectedCostCenter) return;
            this.loading = true;
            try {
                const res = await fetch(`<?= base_url('mpp/getMppMatrix') ?>?id_dept=${this.selectedCostCenter}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await res.json();
                if (result.status === 'success') {
                    this.matrixData = result.data;
                }
            } catch (err) {
                console.error('Error loading MPP matrix:', err);
            } finally {
                this.loading = false;
            }
        },

        // -------------------------------------------------------
        // Sub-Tab 1: Modal - Load Breakdown per Kategori
        // -------------------------------------------------------
        async openFormModal(category, tipeId) {
            this.modalCategory = category;
            this.modalTipeId = tipeId;
            this.modalPositions = [];
            this.modalNote = '';
            this.isModalOpen = true;
            this.modalLoading = true;

            try {
                const res = await fetch(`<?= base_url('mpp/getMppBreakdown') ?>?id_dept=${this.selectedCostCenter}&tipe_id=${tipeId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await res.json();
                if (result.status === 'success') {
                    this.modalPositions = (result.data.positions || []).map(p => ({
                        id_mppx: Number(p.id_mppx),
                        position_name: p.position_name,
                        has_entry: Number(p.has_entry) === 1,
                        months: [
                            parseInt(p.m1)||0, parseInt(p.m2)||0, parseInt(p.m3)||0, parseInt(p.m4)||0,
                            parseInt(p.m5)||0, parseInt(p.m6)||0, parseInt(p.m7)||0, parseInt(p.m8)||0,
                            parseInt(p.m9)||0, parseInt(p.m10)||0, parseInt(p.m11)||0, parseInt(p.m12)||0
                        ]
                    }));
                    this.modalNote = result.data.note || '';
                }
            } catch (err) {
                console.error('Error loading breakdown:', err);
            } finally {
                this.modalLoading = false;
            }
        },

        // -------------------------------------------------------
        // Sub-Tab 1: Modal - Auto-Fill ke Kanan (same row)
        // -------------------------------------------------------
        fillRight(posIdx, colIdx, value) {
            const numVal = parseInt(value) || 0;
            for (let i = colIdx; i < 12; i++) {
                this.modalPositions[posIdx].months[i] = numVal;
            }
        },

        // -------------------------------------------------------
        // Sub-Tab 1: Modal - Simpan Data Breakdown (multi-posisi)
        // -------------------------------------------------------
        async saveMppBreakdown() {
            this.saving = true;
            try {
                const rows = this.modalPositions.map(p => ({
                    staff_name: p.position_name,
                    months: p.months
                }));
                const payload = {
                    id_dept: this.selectedCostCenter,
                    tipe_id: this.modalTipeId,
                    rows: rows,
                    note: this.modalNote
                };

                const res = await fetch(`<?= base_url('mpp/saveMppBreakdown') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();
                if (result.status === 'success') {
                    this.isModalOpen = false;
                    this.loadMatrixData();
                    this.loadViewData();
                    if (window.showToast) {
                        window.showToast('success', result.message || 'Data berhasil disimpan!');
                    }
                } else {
                    alert(result.message || 'Gagal menyimpan data.');
                }
            } catch (err) {
                console.error('Error saving breakdown:', err);
                alert('Terjadi kesalahan saat menyimpan data.');
            } finally {
                this.saving = false;
            }
        },

        async deleteMppPosition(pos) {
            if (!pos || !pos.has_entry || !pos.id_mppx || this.deletingPositionId !== null) return;
            if (! window.confirm(`Hapus entry posisi ${pos.position_name}?`)) return;

            this.deletingPositionId = pos.id_mppx;
            try {
                const res = await fetch(`<?= base_url('mpp/deleteMppPosition') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        id_dept: this.selectedCostCenter,
                        tipe_id: this.modalTipeId,
                        position_id: pos.id_mppx
                    })
                });
                const result = await res.json();
                if (!res.ok || result.status !== 'success') {
                    throw new Error(result.message || 'Gagal menghapus entry posisi.');
                }

                if (window.showToast) {
                    window.showToast('success', result.message || 'Entry posisi berhasil dihapus.');
                }

                await this.openFormModal(this.modalCategory, this.modalTipeId);
                await Promise.all([this.loadMatrixData(), this.loadViewData()]);
            } catch (err) {
                console.error('Error deleting MPP position:', err);
                if (window.showToast) {
                    window.showToast('error', err.message || 'Terjadi kesalahan saat menghapus entry posisi.');
                } else {
                    alert(err.message || 'Terjadi kesalahan saat menghapus entry posisi.');
                }
            } finally {
                this.deletingPositionId = null;
            }
        },

        // -------------------------------------------------------
        // Sub-Tab 1: Helper - Get CC Display Name
        // -------------------------------------------------------
        getCostCenterName() {
            const found = this.costCenterList.find(c => String(c.cost_center) === String(this.selectedCostCenter));
            return found ? `[${found.cost_center_sap}] ${found.cost_desc}` : '';
        },

        // -------------------------------------------------------
        // Sub-Tab 1: Helper - Format Number
        // -------------------------------------------------------
        fmtNum(val) {
            return parseInt(val) || 0;
        },

        // -------------------------------------------------------
        // Sub-Tab 2: View MPP Data
        // -------------------------------------------------------
        async loadViewData() {
            if (!this.selectedCostCenter) return;
            this.viewLoading = true;
            try {
                const res = await fetch(`<?= base_url('mpp/getViewData') ?>?id_dept=${this.selectedCostCenter}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await res.json();
                if (result.status === 'success') {
                    this.viewData = result.data || [];
                    this.viewTotals = result.totals || [0,0,0,0,0,0,0,0,0,0,0,0,0];
                }
            } catch (err) {
                console.error('Error loading view data:', err);
            } finally {
                this.viewLoading = false;
            }
        }
    };
}
</script>
<?= $this->endSection() ?>
