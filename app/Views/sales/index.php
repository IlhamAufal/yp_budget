<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="salesMainContainer()" x-init="initData()" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">2. Sales</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Pusat simulasi, alokasi target volume & revenue, serta penyesuaian diskon sales.</p>
        </div>
        <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li><a href="<?= base_url('dashboard') ?>" class="hover:text-primary">Home</a></li>
                <li>/</li>
                <li class="text-gray-700 dark:text-gray-200 font-semibold">2. Sales</li>
            </ol>
        </nav>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="border-b border-gray-200 dark:border-gray-700 overflow-x-auto scrollbar-thin">
            <nav class="flex space-x-1 p-2 min-w-max" aria-label="Tabs">
                <button @click="activeTab = 'summary'" :class="activeTab === 'summary' ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'" class="px-4 py-2 text-xs rounded-lg border-b-2 border-transparent transition-all">Summary</button>
                <button @click="activeTab = 'domestic'" :class="activeTab === 'domestic' ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'" class="px-4 py-2 text-xs rounded-lg border-b-2 border-transparent transition-all">Domestic</button>
                <button @click="activeTab = 'intl_valas'" :class="activeTab === 'intl_valas' ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'" class="px-4 py-2 text-xs rounded-lg border-b-2 border-transparent transition-all">INTL (VALAS)</button>
                <button @click="activeTab = 'intl_idr'" :class="activeTab === 'intl_idr' ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'" class="px-4 py-2 text-xs rounded-lg border-b-2 border-transparent transition-all">INTL (IDR)</button>
                <button @click="activeTab = 'delivery_claim'" :class="activeTab === 'delivery_claim' ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'" class="px-4 py-2 text-xs rounded-lg border-b-2 border-transparent transition-all">Delivery Exp & Customer Claim</button>
                <button @click="activeTab = 'key_product'" :class="activeTab === 'key_product' ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'" class="px-4 py-2 text-xs rounded-lg border-b-2 border-transparent transition-all">Report Key Product</button>
                <button @click="activeTab = 'reclass'" :class="activeTab === 'reclass' ? 'bg-primary/10 text-primary border-primary font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'" class="px-4 py-2 text-xs rounded-lg border-b-2 border-transparent transition-all">Reclass A&P</button>
            </nav>
        </div>
    </div>

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