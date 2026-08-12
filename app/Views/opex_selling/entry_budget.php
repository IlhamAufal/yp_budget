<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="opexSellingEntryApp()" x-init="init()" class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <i class="fas fa-shopping-cart text-lg text-primary"></i>
                </div>
                <h2 class="text-title-md2 font-bold text-black dark:text-white">Entry OPEX Selling</h2>
            </div>
            <nav class="mt-1">
                <ol class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    <li><a class="hover:text-primary" href="<?= base_url('dashboard') ?>"><i class="fas fa-home mr-1"></i>Home</a></li>
                    <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                    <li>4.1 OPEX - Selling</li>
                    <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                    <li class="text-primary font-semibold">Entry Budget</li>
                </ol>
            </nav>
        </div>
        <div class="flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-1 dark:bg-boxdark dark:text-gray-200">
            <i class="fas fa-calendar-alt text-primary"></i>
            <span>Budget Plan Year :</span>
            <span class="text-red-500 font-bold"><?= esc($workingYear) ?></span>
        </div>
    </div>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6 py-3 dark:border-strokedark">
            <div class="flex items-center gap-4">
                <button @click="activeTab = 'entry'"
                    :class="activeTab === 'entry' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-2 py-2 text-sm font-medium transition-all duration-200">
                    <i class="fas fa-edit mr-1"></i>Entry / Update Budget
                </button>
                <button @click="activeTab = 'view'"
                    :class="activeTab === 'view' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white'"
                    class="border-b-2 px-2 py-2 text-sm font-medium transition-all duration-200">
                    <i class="fas fa-eye mr-1"></i>View Data
                </button>
            </div>
        </div>

        <div class="p-6">
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
