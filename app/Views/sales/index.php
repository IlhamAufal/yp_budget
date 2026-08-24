<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="salesMainContainer()" x-init="initData()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-brand-500 font-bold">Sales Overview</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 shadow-xs">
                    <i class="fa-solid fa-chart-line text-base"></i>
                </span>
                Sales Budget Overview
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Pusat simulasi, alokasi target volume & revenue, serta penyesuaian diskon sales.
            </p>
        </div>
        <div class="flex items-center gap-2.5 rounded-xl border border-gray-200/80 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-xs dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <i class="fa-solid fa-calendar-days text-brand-500"></i>
            <span>Budget Plan Year :</span>
            <span class="text-brand-600 dark:text-brand-400 font-bold"><?= esc($workingYear ?? date('Y')) ?></span>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="border-b border-gray-100 dark:border-gray-800 px-6 pt-3">
            <div class="flex flex-wrap items-center gap-4">
                <button @click="activeTab = 'summary'" :class="activeTab === 'summary' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'" class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-chart-pie mr-1"></i>Summary
                </button>
                <button @click="activeTab = 'domestic'" :class="activeTab === 'domestic' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'" class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-boxes-packing mr-1"></i>Domestic
                </button>
                <button @click="activeTab = 'intl_valas'" :class="activeTab === 'intl_valas' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'" class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-globe mr-1"></i>INTL (VALAS)
                </button>
                <button @click="activeTab = 'intl_idr'" :class="activeTab === 'intl_idr' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'" class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-money-bill-transfer mr-1"></i>INTL (IDR)
                </button>
                <button @click="activeTab = 'delivery_claim'" :class="activeTab === 'delivery_claim' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'" class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-truck mr-1"></i>Delivery & Claim
                </button>
                <button @click="activeTab = 'key_product'" :class="activeTab === 'key_product' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'" class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-award mr-1"></i>Key Product
                </button>
                <button @click="activeTab = 'reclass'" :class="activeTab === 'reclass' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'" class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-right-left mr-1"></i>Reclass A&P
                </button>
            </div>
        </div>

        <div class="p-5 md:p-6 space-y-6">

    <?php /* COMMENTED OUT: Key Product & Channel form — currently GET only
    <div x-show="activeTab === 'domestic' || activeTab === 'intl_valas'" class="bg-sky-50/70 dark:bg-gray-800 p-5 rounded-xl border border-sky-100 dark:border-gray-700 shadow-sm space-y-4">
        ... Key Product grid 4x2 + Channel grid 3x2 + Process button ...
    </div>
    */ ?>

        <div>
            <div x-show="activeTab === 'domestic'"><?= $this->include('sales/partials/summary/tab_domestic') ?></div>
            <div x-show="activeTab === 'intl_valas'"><?= $this->include('sales/partials/summary/tab_intl_valas') ?></div>
            <div x-show="activeTab === 'intl_idr'"><?= $this->include('sales/partials/summary/tab_intl_idr') ?></div>
            <div x-show="activeTab === 'delivery_claim'"><?= $this->include('sales/partials/summary/tab_delivery_claim') ?></div>
            <div x-show="activeTab === 'key_product'"><?= $this->include('sales/partials/summary/tab_key_product') ?></div>
            <div x-show="activeTab === 'summary'"><?= $this->include('sales/partials/summary/tab_summary') ?></div>
            <div x-show="activeTab === 'reclass'"><?= $this->include('sales/partials/summary/tab_reclass') ?></div>
        </div>
    </div>

</div>

<script>
function salesMainContainer() {
    return {
        activeTab: 'domestic',
        processing: false,
        adjustedProducts: [],
        adjustment: {
            global_vol: 0, global_asp: 0,
            gummy_vol: 0, gummy_asp: 0,
            boli_vol: 0, boli_asp: 0,
            extruder_vol: 0, extruder_asp: 0,
            gt_vol: 0, gt_asp: 0,
            mt_vol: 0, mt_asp: 0,
            oem_vol: 0, oem_asp: 0
        },

        initData() {},

        processAdjustment() {
            this.processing = true;

            fetch('<?= base_url('sales/processAdjustment') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({ adjustment: this.adjustment })
            })
            .then(r => r.json())
            .then(res => {
                this.processing = false;
                if (res.status === 'success') {
                    this.adjustedProducts = res.products || [];
                    if (window.ypToast) {
                        window.ypToast.success(res.message || 'Adjustment berhasil dikalkulasi.');
                    } else {
                        alert(res.message || 'Adjustment berhasil dikalkulasi.');
                    }
                } else {
                    if (window.ypToast) {
                        window.ypToast.error(res.message || 'Gagal memproses adjustment.');
                    } else {
                        alert(res.message || 'Gagal memproses adjustment.');
                    }
                }
            })
            .catch(err => {
                this.processing = false;
                console.error(err);
                if (window.ypToast) {
                    window.ypToast.error('Error: ' + err.message);
                } else {
                    alert('Error: ' + err.message);
                }
            });
        }
    }
}
</script>
<?= $this->endSection() ?>