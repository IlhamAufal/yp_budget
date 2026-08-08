<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{
    activeTab: 'actual',
    costCenter: 'Domestic',
    
    // Modal state for Upload Data
    uploadModalOpen: false,
    uploadType: '', // 'dalam_juta' or 'satuan_juta'
    uploadTitle: '',
    
    openUploadModal(type, title) {
        this.uploadType = type;
        this.uploadTitle = title;
        this.uploadModalOpen = true;
    }
}" class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white">
                Actual Data
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                4.1 OPEX - Selling
            </p>
        </div>

        <nav>
            <ol class="flex items-center gap-2 text-sm font-medium">
                <li class="flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                    <span class="font-bold text-danger">Budget Plan Year : 2027</span>
                </li>
                <li class="text-gray-400">|</li>
                <li><a class="text-gray-600 hover:text-primary dark:text-gray-400" href="<?= base_url() ?>">Home</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-primary">Actual Data</li>
            </ol>
        </nav>
    </div>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6 dark:border-strokedark">
            <div class="flex gap-8">
                <button 
                    @click="activeTab = 'actual'"
                    :class="activeTab === 'actual' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-600 hover:text-primary dark:text-gray-400'"
                    class="py-4 border-b-2 text-sm font-medium transition-colors duration-150 focus:outline-none"
                >
                    Actual Data
                </button>
                <button 
                    @click="activeTab = 'download'"
                    :class="activeTab === 'download' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-600 hover:text-primary dark:text-gray-400'"
                    class="py-4 border-b-2 text-sm font-medium transition-colors duration-150 focus:outline-none"
                >
                    Download Template
                </button>
                <button 
                    @click="activeTab = 'upload'"
                    :class="activeTab === 'upload' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-600 hover:text-primary dark:text-gray-400'"
                    class="py-4 border-b-2 text-sm font-medium transition-colors duration-150 focus:outline-none"
                >
                    Upload Data
                </button>
            </div>
        </div>

        <div class="p-6">
            <div x-show="activeTab === 'actual'" x-cloak>
                <?= $this->include('opex_selling/partials/actual_tab_data') ?>
            </div>

            <div x-show="activeTab === 'download'" x-cloak>
                <?= $this->include('opex_selling/partials/actual_tab_download') ?>
            </div>

            <div x-show="activeTab === 'upload'" x-cloak>
                <?= $this->include('opex_selling/partials/actual_tab_upload') ?>
            </div>
        </div>
    </div>

    <?= $this->include('opex_selling/partials/actual_modal_upload') ?>

</div>
<?= $this->endSection() ?>