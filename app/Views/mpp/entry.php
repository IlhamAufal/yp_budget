<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="mppEntry()" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Man Power Planning (MPP) Entry</h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Pengajuan Alokasi Jumlah Tenaga Kerja.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Cost Center / Department</label>
                <select x-model="selectedDept" @change="loadTable()" class="w-full text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white p-2.5 focus:border-primary focus:ring-primary outline-none transition-all">
                    <option value="">-- Pilih Department --</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= esc($dept['department_code']) ?>"><?= esc($dept['department_code']) ?> - <?= esc($dept['department_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Tipe Struktur Line</label>
                <div class="flex items-center space-x-4 mt-2">
                    <label class="inline-flex items-center text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="radio" x-model="isNewlines" value="false" @change="loadTable()" class="text-primary focus:ring-primary">
                        <span class="ml-2">Existing Dept / Regular</span>
                    </label>
                    <label class="inline-flex items-center text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="radio" x-model="isNewlines" value="true" @change="loadTable()" class="text-primary focus:ring-primary">
                        <span class="ml-2">New Lines / Sub-Dept</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800 overflow-hidden">
        <div x-show="loading" class="p-8 text-center text-gray-500 dark:text-gray-400 text-xs">
            <svg class="animate-spin h-5 w-5 mx-auto mb-2 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Memuat Data Form MPP...
        </div>

        <div x-show="!loading && tableHtml === ''" class="p-8 text-center text-gray-400 dark:text-gray-500 text-xs">
            Silakan pilih <strong class="text-gray-700 dark:text-gray-300">Cost Center / Department</strong> di atas untuk memuat form entry.
        </div>

        <div x-show="!loading && tableHtml !== ''" x-html="tableHtml"></div>
    </div>
</div>

<script>
function mppEntry() {
    return {
        selectedDept: '',
        isNewlines: 'false',
        loading: false,
        tableHtml: '',

        async loadTable() {
            if (!this.selectedDept) {
                this.tableHtml = '';
                return;
            }

            this.loading = true;
            try {
                let response = await fetch(`<?= base_url('mpp/getEntryTable') ?>?id_dept=${this.selectedDept}&is_newlines=${this.isNewlines}`);
                let result = await response.json();
                
                if (result.status === 'success') {
                    this.tableHtml = result.html;
                } else {
                    alert(result.message);
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
<?= $this->endSection() ?>