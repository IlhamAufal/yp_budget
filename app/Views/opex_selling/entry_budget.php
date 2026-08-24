<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="opexSellingEntryApp()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span>OPEX Selling</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-brand-500 font-bold">Entry Budget</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 shadow-xs">
                    <i class="fa-solid fa-cart-shopping text-base"></i>
                </span>
                Entry OPEX Selling
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Pengelolaan dan entri anggaran beban operasional penjualan (Selling Expense).
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
                    class="border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-pen-to-square mr-1.5"></i>Entry / Update Budget
                </button>
                <button @click="activeTab = 'view'"
                    :class="activeTab === 'view' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-eye mr-1.5"></i>View Data
                </button>
            </div>
        </div>

        <div class="p-5 md:p-6">
            <div x-show="activeTab === 'entry'" x-cloak>
                <?= $this->include('opex_selling/partials/entry_tab_entry') ?>
            </div>

            <div x-show="activeTab === 'view'" x-cloak>
                <?= $this->include('opex_selling/partials/entry_tab_view') ?>
            </div>
        </div>
    </div>

</div>

<script>
function opexSellingEntryApp() {
    return {
        activeTab: 'entry',
        init() {}
    }
}
</script>
<?= $this->endSection() ?>
