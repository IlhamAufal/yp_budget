<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div x-data="capexEntryApp()" class="mx-auto max-w-7xl p-4 md:p-6 2xl:p-10">
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
    <?= $this->include('capex/partials/modal_manual_book') ?>
</div>

<script>
function capexEntryApp() {
    return {
        activeSubTab: 'entry',
        selectedCostCenter: '1000GP1100',
        viewCostCenter: '1000KA1004',
        manualBookOpen: false,
        formCapexOpen: false,
        activeCategory: { code: '', name: '' },

        // Mock Cost Centers
        costCenters: [
            { id: '1000GP1100', name: '1. [1000GP1100] President Director' },
            { id: '1000KA1004', name: '1. [1000KA1004] Project Jateng Line 9' },
            { id: '1000KA1007', name: '2. [1000KA1007] Project Sungkono KRG' },
            { id: '1000KAF001', name: '3. [1000KAF001] Accounting KRG' },
            { id: '1000GPF004', name: '4. [1000GPF004] IT Department' },
            { id: '1000KAF002', name: '5. [1000KAF002] IT Dept. KRG' }
        ],

        // Mock Categories
        categories: [
            { code: '7710000', name: 'Land' },
            { code: '7710202', name: 'Building And Facility' },
            { code: '7710203', name: 'Machinery Equipment' },
            { code: '7710204', name: 'Office Equipment' },
            { code: '7710205', name: 'Transportation Equipment' },
            { code: '7710206', name: 'Laboratory Equipment' }
        ],

        // Modal Form Rows State
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

        openFormCapex(cat) {
            this.activeCategory = cat;
            this.formCapexOpen = true;
        },

        addRow() {
            this.formRows.push({
                description: '',
                costCenter: '',
                newLines: 'Tidak',
                qty: 0,
                unitPrice: 0,
                remarks: '',
                jan: 0, feb: 0, mar: 0, apr: 0, may: 0, jun: 0, jul: 0, aug: 0, sep: 0, oct: 0, nov: 0, dec: 0
            });
        },

        removeRow(index) {
            if (this.formRows.length > 1) {
                this.formRows.splice(index, 1);
            }
        },

        saveFormCapex() {
            alert('Data CAPEX berhasil disimpan!');
            this.formCapexOpen = false;
        }
    }
}
</script>
<?= $this->endSection() ?>