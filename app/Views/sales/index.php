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

    <div x-show="activeTab === 'domestic' || activeTab === 'summary'" class="bg-sky-50/70 dark:bg-gray-800 p-5 rounded-xl border border-sky-100 dark:border-gray-700 shadow-sm space-y-4">
        
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            <div class="w-32 flex-shrink-0">
                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">Key Product</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 flex-1">
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">Global - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.global_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">Global - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.global_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">GUMMY - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.gummy_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">GUMMY - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.gummy_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">BOLI - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.boli_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">BOLI - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.boli_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">EXTRUDER - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.extruder_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">EXTRUDER - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.extruder_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-sky-200 dark:border-gray-700 my-2">

        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            <div class="w-32 flex-shrink-0">
                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">Channel</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 flex-1">
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">GT - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.gt_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">GT - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.gt_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">MT - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.mt_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">MT - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.mt_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">OEM - Volume</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.oem_vol" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <label class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">OEM - ASP</label>
                        <div class="flex items-center gap-1">
                            <input type="number" step="0.1" x-model.number="adjustment.oem_asp" class="w-16 text-right text-xs p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary">
                            <span class="text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-end justify-end">
                    <button type="button" @click="processAdjustment()" :disabled="processing" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded shadow transition-colors flex items-center gap-1.5 disabled:opacity-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                        <span x-text="processing ? 'Processing...' : 'Process'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <div>
        <div x-show="activeTab === 'domestic'"><?= $this->include('sales/partials/tab_domestic') ?></div>
        <div x-show="activeTab === 'intl_valas'"><?= $this->include('sales/partials/tab_intl_valas') ?></div>
        <div x-show="activeTab === 'intl_idr'"><?= $this->include('sales/partials/tab_intl_idr') ?></div>
        <div x-show="activeTab === 'delivery_claim'"><?= $this->include('sales/partials/tab_delivery_claim') ?></div>
        <div x-show="activeTab === 'key_product'"><?= $this->include('sales/partials/tab_key_product') ?></div>
        <div x-show="activeTab === 'summary'"><?= $this->include('sales/partials/tab_summary') ?></div>
        <div x-show="activeTab === 'reclass'"><?= $this->include('sales/partials/tab_reclass') ?></div>
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