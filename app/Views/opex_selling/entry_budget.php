<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="opexSellingEntryApp()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="hover:text-[#2F3185]">OPEX Selling</span>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold">Entry Budget</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Entry OPEX Selling
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Pengelolaan dan entri anggaran beban operasional penjualan (Selling Expense).
            </p>
        </div>
    </div>

    <!-- TAB NAV -->
    <div class="nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="OPEX Selling Tabs">
            <button
                type="button"
                @click="activeTab = 'entry'"
                :class="activeTab === 'entry' ? 'active' : ''"
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Entry / Update Budget</span>
            </button>
            <button
                type="button"
                @click="activeTab = 'view'"
                :class="activeTab === 'view' ? 'active' : ''"
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>View Data</span>
            </button>
        </nav>
    </div>

    <!-- TAB CONTENT -->
    <div>
        <div class="space-y-6">
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
