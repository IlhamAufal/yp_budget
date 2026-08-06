<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ 
    activeTab: 'summary',
    filterDept: '',
    filterCategory: '',
    searchQuery: '',
    detailModalOpen: false,
    selectedAccount: '',
    modalData: [],
    isLoadingModal: false,

    openDetail(accountCode, accountDesc) {
        this.selectedAccount = accountCode + ' - ' + accountDesc;
        this.detailModalOpen = true;
        this.isLoadingModal = true;
        
        fetch(`<?= base_url('pl/get_detail_account') ?>?account=${accountCode}`)
            .then(res => res.json())
            .then(data => {
                this.modalData = data;
                this.isLoadingModal = false;
            })
            .catch(() => {
                this.modalData = [];
                this.isLoadingModal = false;
            });
    }
}" class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                Profit & Loss (P&L) Report
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Rincian dan ringkasan Laporan Laba Rugi Operasional.
            </p>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="<?= base_url('pl/export_excel') ?>" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-800 dark:hover:bg-emerald-900/40 transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900">
        
        <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row gap-3 sm:items-center justify-between">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="Cari akun / uraian..." 
                           class="w-full pl-10 pr-3.5 py-2 text-xs rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:focus:ring-brand-500/20">
                </div>

                <select x-model="filterDept" class="px-3 py-2 text-xs rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <option value="">-- Semua Cost Center --</option>
                    <?php if (!empty($departments)): ?>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= esc($dept['id_dept']); ?>"><?= esc($dept['id_dept']); ?> - <?= esc($dept['cost_desc']); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="flex items-center p-1 bg-gray-100 dark:bg-gray-800 rounded-xl self-start sm:self-auto">
                <button @click="activeTab = 'summary'" 
                        :class="activeTab === 'summary' ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
                    Summary P&L
                </button>
                <button @click="activeTab = 'detail'" 
                        :class="activeTab === 'detail' ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
                    Breakdown per Akun
                </button>
            </div>
        </div>

        <div class="p-4 sm:p-6 overflow-x-auto">
            
            <div x-show="activeTab === 'summary'" x-cloak class="space-y-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-4 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[240px]">Kategori / Uraian Account</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Jan</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Feb</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Mar</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Apr</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">May</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Jun</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Jul</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Aug</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Sep</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Oct</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Nov</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Dec</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-gray-800 text-right font-bold text-gray-900 dark:text-white">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300">
                            <?php if (!empty($pl_summary)): ?>
                                <?php foreach ($pl_summary as $item): ?>
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors <?= !empty($item['is_header']) ? 'font-bold bg-gray-50/50 dark:bg-gray-800/30' : '' ?>">
                                        <td class="px-4 py-2.5 border-r border-gray-200 dark:border-gray-800">
                                            <?= esc($item['account_desc']); ?>
                                        </td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($item['jan'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($item['feb'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($item['mar'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($item['apr'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($item['may'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($item['jun'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($item['jul'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($item['aug'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($item['sep'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($item['oct'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($item['nov'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($item['dec'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-800/20"><?= number_format($item['total'] ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="14" class="px-4 py-8 text-center text-gray-400">Tidak ada data P&L Summary.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="activeTab === 'detail'" x-cloak class="space-y-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-4 py-3 border-b border-r border-gray-200 dark:border-gray-800">Kode Akun</th>
                                <th class="px-4 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[200px]">Deskripsi Akun</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800">Tipe</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Total Annual Budget</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-gray-800 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300">
                            <?php if (!empty($pl_details)): ?>
                                <?php foreach ($pl_details as $detail): ?>
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                        <td class="px-4 py-2.5 font-medium border-r border-gray-200 dark:border-gray-800"><?= esc($detail['account_code']); ?></td>
                                        <td class="px-4 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($detail['account_desc']); ?></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400">
                                                <?= esc($detail['type'] ?? 'Expense'); ?>
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-right font-semibold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800">
                                            <?= number_format($detail['annual_total'] ?? 0, 2); ?>
                                        </td>
                                        <td class="px-3 py-2.5 text-center">
                                            <button @click="openDetail('<?= $detail['account_code']; ?>', '<?= esc($detail['account_desc']); ?>')" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100 dark:bg-brand-950/40 dark:text-brand-400 dark:hover:bg-brand-900/40 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail</span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Tidak ada data rincian akun.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div x-show="detailModalOpen" 
         class="fixed inset-0 flex items-center justify-center p-4 sm:p-6"
         style="z-index: 999999; background-color: rgba(0,0,0,0.6); backdrop-filter: blur(4px);"
         @click.self="detailModalOpen = false" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <div class="relative w-full max-w-4xl max-h-[85vh] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-gray-200 dark:border-gray-800">
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50/80 dark:bg-gray-800/60 border-b border-gray-200 dark:border-gray-800">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Rincian Transaksi Akun</h3>
                    <p class="text-xs text-brand-600 dark:text-brand-400 font-semibold mt-0.5" x-text="selectedAccount"></p>
                </div>
                <button @click="detailModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                <template x-if="isLoadingModal">
                    <div class="flex flex-col items-center justify-center py-10 gap-3 text-gray-500">
                        <svg class="w-7 h-7 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-xs">Memuat detail transaksi...</span>
                    </div>
                </template>

                <template x-if="!isLoadingModal">
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 uppercase font-semibold">
                                <tr>
                                    <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800">Cost Center</th>
                                    <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800">Uraian / Remarks</th>
                                    <th class="px-3 py-2 border-b border-gray-200 dark:border-gray-800 text-right">Nominal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                <template x-for="row in modalData" :key="row.id">
                                    <tr>
                                        <td class="px-3 py-2 border-r border-gray-200 dark:border-gray-800 font-medium" x-text="row.cost_center"></td>
                                        <td class="px-3 py-2 border-r border-gray-200 dark:border-gray-800" x-text="row.remarks"></td>
                                        <td class="px-3 py-2 text-right font-semibold text-gray-900 dark:text-white" x-text="row.amount"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800/60 border-t border-gray-200 dark:border-gray-800 flex justify-end">
                <button type="button" @click="detailModalOpen = false" class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>