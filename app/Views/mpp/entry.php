<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="mppEntry()" x-init="init()" class="mx-auto max-w-7xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white">
                Man Power Planning
            </h2>
            <nav class="mt-1">
                <ol class="flex items-center gap-2 text-sm font-medium text-gray-500">
                    <li><a class="hover:text-primary" href="#">Home</a></li>
                    <li>/</li>
                    <li class="text-primary">Man Power Planning</li>
                </ol>
            </nav>
        </div>
        <div class="text-right">
            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Budget Plan Year : </span>
            <span class="text-sm font-bold text-danger"><?= esc($workingYear) ?></span>
        </div>
    </div>

    <div class="mb-6 border-b border-stroke dark:border-strokedark">
        <ul class="-mb-px flex flex-wrap gap-6 text-sm font-medium">
            <li>
                <button @click="activeTab = 'entry'"
                        :class="activeTab === 'entry' ? 'border-primary text-primary dark:border-primary dark:text-primary' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400'"
                        class="inline-flex items-center gap-2 border-b-2 py-4 px-1 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    7.1 Entry MPP
                </button>
            </li>
            <li>
                <button @click="activeTab = 'view'"
                        :class="activeTab === 'view' ? 'border-primary text-primary dark:border-primary dark:text-primary' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400'"
                        class="inline-flex items-center gap-2 border-b-2 py-4 px-1 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    7.3 View MPP Data
                </button>
            </li>
        </ul>
    </div>

    <div x-show="activeTab === 'entry'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
        <?= $this->include('mpp/partials/entry_tab_mpp') ?>
    </div>

    <div x-show="activeTab === 'view'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
        <?= $this->include('mpp/partials/entry_tab_view_data') ?>
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
