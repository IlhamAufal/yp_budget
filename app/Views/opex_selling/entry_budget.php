<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="opexSellingEntryHandler()" class="space-y-6">

    <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-default flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <label for="deptSelect" class="text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Department / Cost Center:</label>
            <select id="deptSelect" 
                    x-model="selectedDept" 
                    @change="loadBudgetSummary()" 
                    class="w-full sm:w-80 px-3.5 py-2 text-xs md:text-sm bg-gray-50 dark:bg-meta-4/40 border border-stroke dark:border-strokedark rounded-xl text-gray-900 dark:text-white focus:border-primary focus:outline-none">
                <option value="">-- Pilih Cost Center Selling --</option>
                <?php foreach ($dept as $d) : ?>
                    <option value="<?= esc($d['cost_center']); ?>"><?= esc($d['cost_center']); ?> - <?= esc($d['cost_desc']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="uploadModalOpen = true" class="inline-flex items-center gap-2 px-4 py-2 bg-success text-white text-xs font-medium rounded-xl hover:bg-opacity-90 transition-all">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                    <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/>
                </svg>
                <span>Upload Actual Data</span>
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-default p-5">
        <template x-if="!selectedDept">
            <div class="py-12 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium">Silakan pilih Cost Center Selling terlebih dahulu untuk menampilkan/mengedit entri budget.</p>
            </div>
        </template>

        <template x-if="selectedDept">
            <div id="tableDetailContainer" class="space-y-4">
                <div x-show="isLoading" class="py-8 text-center text-gray-500 flex items-center justify-center gap-2">
                    <span class="w-5 h-5 border-2 border-primary border-t-transparent rounded-full animate-spin"></span>
                    <span>Memuat Data Table Budget...</span>
                </div>
                <div x-show="!isLoading" x-html="tableHtml"></div>
            </div>
        </template>
    </div>

    <?= view('opex_selling/upload_actual_modal') ?>

</div>

<script>
function opexSellingEntryHandler() {
    return {
        selectedDept: '',
        isLoading: false,
        tableHtml: '',
        uploadModalOpen: false,

        loadBudgetSummary() {
            if (!this.selectedDept) return;
            this.isLoading = true;

            fetch('<?= base_url('opex_selling/entryBudgetDetail'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ dept: this.selectedDept })
            })
            .then(res => res.text())
            .then(html => {
                this.tableHtml = html;
                this.isLoading = false;
            })
            .catch(err => {
                this.isLoading = false;
                console.error(err);
            });
        }
    }
}
</script>
<?= $this->endSection() ?>