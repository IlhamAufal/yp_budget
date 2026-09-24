<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="salesMainContainer()" x-init="initData()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold">Sales Overview</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Sales Budget Overview
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Pusat simulasi, alokasi target volume & revenue, serta penyesuaian diskon sales.
            </p>
        </div>
    </div>

    <!-- Sub Tabs Navigation -->
    <div class="inline-flex max-w-full nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="Sales Overview Tabs">
            <button type="button" @click="activeTab = 'summary'" :class="activeTab === 'summary' ? 'active' : ''" class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Summary</span>
            </button>
            <button type="button" @click="activeTab = 'domestic'" :class="activeTab === 'domestic' ? 'active' : ''" class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Domestic</span>
            </button>
            <button type="button" @click="activeTab = 'intl_valas'" :class="activeTab === 'intl_valas' ? 'active' : ''" class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>INTL (Valas)</span>
            </button>
            <button type="button" @click="activeTab = 'intl_idr'" :class="activeTab === 'intl_idr' ? 'active' : ''" class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>INTL (IDR)</span>
            </button>
            <button type="button" @click="activeTab = 'delivery_claim'" :class="activeTab === 'delivery_claim' ? 'active' : ''" class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Delivery & Claim</span>
            </button>
            <button type="button" @click="activeTab = 'key_product'" :class="activeTab === 'key_product' ? 'active' : ''" class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Key Product</span>
            </button>
            <button type="button" @click="activeTab = 'reclass'" :class="activeTab === 'reclass' ? 'active' : ''" class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Reclass A&P</span>
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="space-y-6">
        <div x-show="activeTab === 'summary'" x-cloak><?= $this->include('sales/partials/summary/tab_summary') ?></div>
        <div x-show="activeTab === 'domestic'" x-cloak><?= $this->include('sales/partials/summary/tab_domestic') ?></div>
        <div x-show="activeTab === 'intl_valas'" x-cloak><?= $this->include('sales/partials/summary/tab_intl_valas') ?></div>
        <div x-show="activeTab === 'intl_idr'" x-cloak><?= $this->include('sales/partials/summary/tab_intl_idr') ?></div>
        <div x-show="activeTab === 'delivery_claim'" x-cloak><?= $this->include('sales/partials/summary/tab_delivery_claim') ?></div>
        <div x-show="activeTab === 'key_product'" x-cloak><?= $this->include('sales/partials/summary/tab_key_product') ?></div>
        <div x-show="activeTab === 'reclass'" x-cloak><?= $this->include('sales/partials/summary/tab_reclass') ?></div>
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