<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="mppEntry()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="hover:text-[#2F3185]">MPP</span>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold">Entry Form</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Man Power Planning
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Perencanaan dan estimasi alokasi tenaga kerja departemen (Man Power Planning).
            </p>
        </div>
    </div>

    <!-- TAB NAV -->
    <div class="nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="MPP Tabs">
            <button
                type="button"
                @click="activeTab = 'entry'"
                :class="activeTab === 'entry' ? 'active' : ''"
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Entry MPP</span>
            </button>
            <button
                type="button"
                @click="activeTab = 'view'"
                :class="activeTab === 'view' ? 'active' : ''"
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>View MPP Data</span>
            </button>
        </nav>
    </div>

    <!-- TAB CONTENT -->
    <div class="space-y-6">
        <div x-show="activeTab === 'entry'" x-cloak>
            <?= $this->include('mpp/partials/entry_tab_mpp') ?>
        </div>

        <div x-show="activeTab === 'view'" x-cloak>
            <?= $this->include('mpp/partials/entry_tab_view_data') ?>
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
