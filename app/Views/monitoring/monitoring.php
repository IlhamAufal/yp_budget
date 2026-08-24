<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ 
    activeTab: 'opex_ga',
    detailModalOpen: false,
    modalTitle: '',
    modalSubtitle: '',
    modalContent: '',
    isLoadingModal: false,
    page: { opex_ga: 1, foh: 1, mpp_opex: 1, mpp_foh: 1, capex: 1 },
    perPage: 10,
    isRowVisible(tab, idx) {
        return idx >= (this.page[tab] - 1) * this.perPage && idx < this.page[tab] * this.perPage;
    },
    totalPages(total) {
        return Math.ceil(total / this.perPage) || 1;
    },
    pageNumbers(tab, total) {
        const totalP = this.totalPages(total);
        const currP = this.page[tab] || 1;
        if (totalP <= 7) return Array.from({ length: totalP }, (_, i) => i + 1);
        if (currP <= 4) return [1, 2, 3, 4, 5, '...', totalP];
        if (currP >= totalP - 3) return [1, '...', totalP - 4, totalP - 3, totalP - 2, totalP - 1, totalP];
        return [1, '...', currP - 1, currP, currP + 1, '...', totalP];
    },

    openDetail(idDept, deptDesc, type) {
        this.detailModalOpen = true;
        this.isLoadingModal = true;
        this.modalTitle = deptDesc || ('Cost Center: ' + idDept);
        this.modalSubtitle = type || '';
        this.modalContent = '';

        // Fetch data modal via AJAX
        fetch(`<?= base_url('monitoring/cari_view_data') ?>?id_dept=${idDept}&type=${type}`)
            .then(response => response.text())
            .then(html => {
                this.modalContent = html;
                this.isLoadingModal = false;
            })
            .catch(err => {
                this.modalContent = '<div class=&quot;p-4 text-center text-red-500 font-medium&quot;>Gagal memuat data detail. Silakan coba lagi.</div>';
                this.isLoadingModal = false;
            });
    }
}" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-brand-500 font-bold">Monitoring</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 shadow-xs">
                    <i class="fa-solid fa-chart-line text-base"></i>
                </span>
                Summary Profit & Loss Monitoring
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Monitoring alokasi anggaran OPEX, FOH, MPP, dan CAPEX per Cost Center.
            </p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        
        <div class="border-b border-gray-100 dark:border-gray-800 px-6 pt-3 overflow-x-auto">
            <nav class="flex items-center gap-4 min-w-max" aria-label="Tabs">
                <button 
                    @click="activeTab = 'opex_ga'"
                    :class="activeTab === 'opex_ga' 
                        ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' 
                        : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-calculator mr-1"></i>
                    <span>Budget - OPEX GA</span>
                </button>

                <button 
                    @click="activeTab = 'foh'"
                    :class="activeTab === 'foh' 
                        ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' 
                        : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-industry mr-1"></i>
                    <span>Budget - FOH</span>
                </button>

                <button 
                    @click="activeTab = 'mpp_opex'"
                    :class="activeTab === 'mpp_opex' 
                        ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' 
                        : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-users mr-1"></i>
                    <span>Budget - MPP OPEX</span>
                </button>

                <button 
                    @click="activeTab = 'mpp_foh'"
                    :class="activeTab === 'mpp_foh' 
                        ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' 
                        : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-user-gear mr-1"></i>
                    <span>Budget - MPP FOH</span>
                </button>

                <button 
                    @click="activeTab = 'capex'"
                    :class="activeTab === 'capex' 
                        ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' 
                        : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                    class="flex items-center gap-1.5 border-b-2 px-3 pb-3 text-xs font-semibold transition-all duration-200">
                    <i class="fa-solid fa-cubes-stacked mr-1"></i>
                    <span>Budget - CAPEX (OPEX & FOH)</span>
                </button>
            </nav>
        </div>

        <div class="p-4 sm:p-6">

            <div x-show="activeTab === 'opex_ga'" x-cloak class="space-y-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-brand-500 text-white font-semibold text-xs border-b border-brand-600">
                            <tr class="bg-brand-500 text-white font-semibold">
                                <th rowspan="2" class="px-4 py-3 border-r border-white/20 min-w-[200px] text-white">Cost Center</th>
                                <th colspan="14" class="px-4 py-2 border-r border-white/20 text-center text-white font-semibold">Budget Expense (Rp)</th>
                            </tr>
                            <tr class="bg-brand-600 text-white text-[11px] font-semibold">
                                <th class="px-3 py-2 border-r border-white/20 text-white">Entry By</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jan</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Feb</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Mar</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Apr</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">May</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jun</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jul</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Aug</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Sep</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Oct</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Nov</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Dec</th>
                                <th class="px-3 py-2 text-right font-bold text-white bg-brand-600">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300">
                            <?php if (!empty($curr)): ?>
                                <?php foreach ($curr as $i => $files): ?>
                                    <tr x-show="isRowVisible('opex_ga', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                        <td class="px-4 py-2.5 font-medium border-r border-gray-200 dark:border-gray-800">
                                            <button @click="openDetail('<?= $files['id_dept']; ?>', '<?= esc($files['cost_desc'] ?? ''); ?>', 'OPEX GA')" 
                                                    class="text-brand-600 hover:text-brand-700 dark:text-brand-400 font-semibold hover:underline text-left">
                                                <?= esc($files['cc_sap'] ?? $files['id_dept']); ?> - <?= esc($files['cost_desc']); ?>
                                            </button>
                                        </td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($files['tags'] ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files['JAN'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files['FEB'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files['MAR'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files['APR'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files['MAY'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files['JUN'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files['JUL'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files['AUG'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files['SEP'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files['OCT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files['NOV'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files['DEC'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-800/20"><?= number_format($files['TOT'] ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="15" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                        <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                                <i class="fa-solid fa-calculator text-xl"></i>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Budget OPEX GA</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada data anggaran OPEX GA untuk periode ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php $totCount = count($curr ?? []); ?>
                <div class="p-3 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $totCount ?> === 0 ? 0 : ((page.opex_ga - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.opex_ga * perPage, <?= $totCount ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $totCount ?></span> data</div>
                    <div class="flex items-center gap-1.5" x-show="totalPages(<?= $totCount ?>) > 1">
                        <button type="button" @click="page.opex_ga--" :disabled="page.opex_ga === 1" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Prev</button>
                        <template x-for="(p, i) in pageNumbers('opex_ga', <?= $totCount ?>)" :key="i">
                            <div>
                                <template x-if="p === '...'">
                                    <span class="px-1.5 font-bold">...</span>
                                </template>
                                <template x-if="p !== '...'">
                                    <button type="button" @click="page.opex_ga = p" :class="page.opex_ga === p ? 'bg-brand-500 text-white font-bold' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300'" class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
                                </template>
                            </div>
                        </template>
                        <button type="button" @click="page.opex_ga++" :disabled="page.opex_ga === totalPages(<?= $totCount ?>)" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Next</button>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'foh'" x-cloak class="space-y-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-brand-500 text-white font-semibold text-xs border-b border-brand-600">
                            <tr class="bg-brand-500 text-white font-semibold">
                                <th rowspan="2" class="px-4 py-3 border-r border-white/20 min-w-[200px] text-white">Cost Center</th>
                                <th colspan="14" class="px-4 py-2 border-r border-white/20 text-center text-white font-semibold">Budget Expense (Rp)</th>
                            </tr>
                            <tr class="bg-brand-600 text-white text-[11px] font-semibold">
                                <th class="px-3 py-2 border-r border-white/20 text-white">Entry By</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jan</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Feb</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Mar</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Apr</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">May</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jun</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jul</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Aug</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Sep</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Oct</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Nov</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Dec</th>
                                <th class="px-3 py-2 text-right font-bold text-white bg-brand-600">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300">
                            <?php if (!empty($curr2)): ?>
                                <?php foreach ($curr2 as $i => $files2): ?>
                                    <tr x-show="isRowVisible('foh', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                        <td class="px-4 py-2.5 font-medium border-r border-gray-200 dark:border-gray-800">
                                            <button @click="openDetail('<?= $files2['id_dept']; ?>', '<?= esc($files2['cost_desc'] ?? ''); ?>', 'FOH')" 
                                                    class="text-brand-600 hover:text-brand-700 dark:text-brand-400 font-semibold hover:underline text-left">
                                                <?= esc($files2['cc_sap'] ?? $files2['id_dept']); ?> - <?= esc($files2['cost_desc']); ?>
                                            </button>
                                        </td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($files2['tags'] ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files2['JAN'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files2['FEB'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files2['MAR'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files2['APR'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files2['MAY'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files2['JUN'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files2['JUL'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files2['AUG'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files2['SEP'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files2['OCT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files2['NOV'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files2['DEC'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-800/20"><?= number_format($files2['TOT'] ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="15" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                        <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                                <i class="fa-solid fa-industry text-xl"></i>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Budget FOH</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada data anggaran FOH untuk periode ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php $totCount2 = count($curr2 ?? []); ?>
                <div class="p-3 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $totCount2 ?> === 0 ? 0 : ((page.foh - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.foh * perPage, <?= $totCount2 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $totCount2 ?></span> data</div>
                    <div class="flex items-center gap-1.5" x-show="totalPages(<?= $totCount2 ?>) > 1">
                        <button type="button" @click="page.foh--" :disabled="page.foh === 1" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Prev</button>
                        <template x-for="(p, i) in pageNumbers('foh', <?= $totCount2 ?>)" :key="i">
                            <div>
                                <template x-if="p === '...'">
                                    <span class="px-1.5 font-bold">...</span>
                                </template>
                                <template x-if="p !== '...'">
                                    <button type="button" @click="page.foh = p" :class="page.foh === p ? 'bg-brand-500 text-white font-bold' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300'" class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
                                </template>
                            </div>
                        </template>
                        <button type="button" @click="page.foh++" :disabled="page.foh === totalPages(<?= $totCount2 ?>)" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Next</button>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'mpp_opex'" x-cloak class="space-y-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-brand-500 text-white font-semibold text-xs border-b border-brand-600">
                            <tr class="bg-brand-500 text-white font-semibold">
                                <th rowspan="2" class="px-4 py-3 border-r border-white/20 min-w-[180px] text-white">Cost Center</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-white">Tipe</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 min-w-[140px] text-white">Jabatan</th>
                                <th colspan="13" class="px-3 py-2 border-r border-white/20 text-center text-white bg-emerald-700/60 font-semibold">New Headcount</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 min-w-[150px] text-white">Notes</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-white">Salary</th>
                                <th colspan="13" class="px-3 py-2 text-center text-white bg-sky-700/60 font-semibold">Amount Headcount (Rp)</th>
                            </tr>
                            <tr class="bg-brand-600 text-white text-[11px] font-semibold">
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Jan</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Feb</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Mar</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Apr</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">May</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Jun</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Jul</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Aug</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Sep</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Oct</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Nov</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Dec</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right font-bold text-white bg-emerald-700/60">Tot</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jan</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Feb</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Mar</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Apr</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">May</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jun</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jul</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Aug</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Sep</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Oct</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Nov</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Dec</th>
                                <th class="px-3 py-2 text-right font-bold text-white bg-brand-700">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300">
                            <?php if (!empty($mpp)): ?>
                                <?php foreach ($mpp as $i => $files1): ?>
                                    <tr x-show="isRowVisible('mpp_opex', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                        <td class="px-4 py-2.5 font-medium border-r border-gray-200 dark:border-gray-800">
                                            <button @click="openDetail('<?= $files1['cost_center']; ?>', '<?= esc($files1['cost_desc'] ?? ''); ?>', 'MPP OPEX')" 
                                                    class="text-brand-600 hover:text-brand-700 dark:text-brand-400 font-semibold hover:underline text-left">
                                                <?= esc($files1['cc_sap'] ?? $files1['cost_center']); ?> - <?= esc($files1['cost_desc']); ?>
                                            </button>
                                        </td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($files1['desc_mpp'] ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($files1['staff_name'] ?? '-'); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['JAN'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['FEB'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['MAR'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['APR'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['MAY'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['JUN'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['JUL'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['AUG'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['SEP'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['OCT'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['NOV'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['DEC'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right font-bold border-r border-gray-200 dark:border-gray-800 bg-gray-50/50"><?= number_format($files1['TOT'] ?? 0, 0); ?></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($files1['notes'] ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($files1['salary'] ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['JAN_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['FEB_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['MAR_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['APR_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['MAY_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['JUN_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['JUL_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['AUG_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['SEP_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['OCT_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['NOV_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files1['DEC_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-800/20"><?= number_format($files1['TOT_AMT'] ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="31" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                        <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                                <i class="fa-solid fa-users text-xl"></i>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data MPP OPEX</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada data perencanaan tenaga kerja OPEX untuk periode ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php $totCount3 = count($mpp ?? []); ?>
                <div class="p-3 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $totCount3 ?> === 0 ? 0 : ((page.mpp_opex - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.mpp_opex * perPage, <?= $totCount3 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $totCount3 ?></span> data</div>
                    <div class="flex items-center gap-1.5" x-show="totalPages(<?= $totCount3 ?>) > 1">
                        <button type="button" @click="page.mpp_opex--" :disabled="page.mpp_opex === 1" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Prev</button>
                        <template x-for="(p, i) in pageNumbers('mpp_opex', <?= $totCount3 ?>)" :key="i">
                            <div>
                                <template x-if="p === '...'">
                                    <span class="px-1.5 font-bold">...</span>
                                </template>
                                <template x-if="p !== '...'">
                                    <button type="button" @click="page.mpp_opex = p" :class="page.mpp_opex === p ? 'bg-brand-500 text-white font-bold' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300'" class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
                                </template>
                            </div>
                        </template>
                        <button type="button" @click="page.mpp_opex++" :disabled="page.mpp_opex === totalPages(<?= $totCount3 ?>)" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Next</button>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'mpp_foh'" x-cloak class="space-y-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-brand-500 text-white font-semibold text-xs border-b border-brand-600">
                            <tr class="bg-brand-500 text-white font-semibold">
                                <th rowspan="2" class="px-4 py-3 border-r border-white/20 min-w-[180px] text-white">Cost Center</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-white">Tipe</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 min-w-[140px] text-white">Jabatan</th>
                                <th colspan="13" class="px-3 py-2 border-r border-white/20 text-center text-white bg-emerald-700/60 font-semibold">New Headcount</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 min-w-[150px] text-white">Notes</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-white">Salary</th>
                                <th colspan="13" class="px-3 py-2 text-center text-white bg-sky-700/60 font-semibold">Amount Headcount (Rp)</th>
                            </tr>
                            <tr class="bg-brand-600 text-white text-[11px] font-semibold">
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Jan</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Feb</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Mar</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Apr</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">May</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Jun</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Jul</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Aug</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Sep</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Oct</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Nov</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right text-white">Dec</th>
                                <th class="px-2 py-2 border-r border-white/20 text-right font-bold text-white bg-emerald-700/60">Tot</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jan</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Feb</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Mar</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Apr</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">May</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jun</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jul</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Aug</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Sep</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Oct</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Nov</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Dec</th>
                                <th class="px-3 py-2 text-right font-bold text-white bg-brand-700">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300">
                            <?php if (!empty($mpp_foh)): ?>
                                <?php foreach ($mpp_foh as $i => $files_foh): ?>
                                    <tr x-show="isRowVisible('mpp_foh', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                        <td class="px-4 py-2.5 font-medium border-r border-gray-200 dark:border-gray-800">
                                            <button @click="openDetail('<?= $files_foh['cost_center']; ?>', '<?= esc($files_foh['cost_desc'] ?? ''); ?>', 'MPP FOH')" 
                                                    class="text-brand-600 hover:text-brand-700 dark:text-brand-400 font-semibold hover:underline text-left">
                                                <?= esc($files_foh['cc_sap'] ?? $files_foh['cost_center']); ?> - <?= esc($files_foh['cost_desc']); ?>
                                            </button>
                                        </td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($files_foh['desc_mpp'] ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($files_foh['staff_name'] ?? '-'); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['JAN'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['FEB'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['MAR'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['APR'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['MAY'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['JUN'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['JUL'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['AUG'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['SEP'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['OCT'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['NOV'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['DEC'] ?? 0, 0); ?></td>
                                        <td class="px-2 py-2.5 text-right font-bold border-r border-gray-200 dark:border-gray-800 bg-gray-50/50"><?= number_format($files_foh['TOT'] ?? 0, 0); ?></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($files_foh['notes'] ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($files_foh['salary'] ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['JAN_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['FEB_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['MAR_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['APR_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['MAY_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['JUN_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['JUL_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['AUG_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['SEP_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['OCT_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['NOV_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($files_foh['DEC_AMT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-800/20"><?= number_format($files_foh['TOT_AMT'] ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="31" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                        <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                                <i class="fa-solid fa-user-gear text-xl"></i>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data MPP FOH</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada data perencanaan tenaga kerja FOH untuk periode ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php $totCount4 = count($mpp_foh ?? []); ?>
                <div class="p-3 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $totCount4 ?> === 0 ? 0 : ((page.mpp_foh - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.mpp_foh * perPage, <?= $totCount4 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $totCount4 ?></span> data</div>
                    <div class="flex items-center gap-1.5" x-show="totalPages(<?= $totCount4 ?>) > 1">
                        <button type="button" @click="page.mpp_foh--" :disabled="page.mpp_foh === 1" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Prev</button>
                        <template x-for="(p, i) in pageNumbers('mpp_foh', <?= $totCount4 ?>)" :key="i">
                            <div>
                                <template x-if="p === '...'">
                                    <span class="px-1.5 font-bold">...</span>
                                </template>
                                <template x-if="p !== '...'">
                                    <button type="button" @click="page.mpp_foh = p" :class="page.mpp_foh === p ? 'bg-brand-500 text-white font-bold' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300'" class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
                                </template>
                            </div>
                        </template>
                        <button type="button" @click="page.mpp_foh++" :disabled="page.mpp_foh === totalPages(<?= $totCount4 ?>)" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Next</button>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'capex'" x-cloak class="space-y-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-brand-500 text-white font-semibold text-xs border-b border-brand-600">
                            <tr class="bg-brand-500 text-white font-semibold">
                                <th rowspan="2" class="px-4 py-3 border-r border-white/20 min-w-[180px] text-white">Cost Center</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 min-w-[200px] text-white">Item Description</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-white">Account</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-white">CC Code</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-right text-white">Unit</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-right min-w-[120px] text-white">Unit Price</th>
                                <th rowspan="2" class="px-3 py-3 border-r border-white/20 min-w-[150px] text-white">Remarks</th>
                                <th colspan="12" class="px-3 py-2 border-r border-white/20 text-center text-white bg-sky-700/60 font-semibold">Acquisition Period</th>
                                <th rowspan="2" class="px-3 py-3 text-right font-bold text-white bg-brand-700 min-w-[130px]">Total</th>
                            </tr>
                            <tr class="bg-brand-600 text-white text-[11px] font-semibold">
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jan</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Feb</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Mar</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Apr</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">May</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jun</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Jul</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Aug</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Sep</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Oct</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Nov</th>
                                <th class="px-3 py-2 border-r border-white/20 text-right text-white">Dec</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300">
                            <?php if (!empty($capex)): ?>
                                <?php foreach ($capex as $i => $row): ?>
                                    <tr x-show="isRowVisible('capex', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                        <td class="px-4 py-2.5 font-medium border-r border-gray-200 dark:border-gray-800"><?= esc($row['cost_center_desc'] ?? ''); ?></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($row['item_desc'] ?? ''); ?></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($row['main_account'] ?? ''); ?></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($row['cost_center'] ?? ''); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['unit'] ?? 0, 0); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['unit_price'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800"><?= esc($row['remarks'] ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['JAN'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['FEB'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['MAR'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['APR'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['MAY'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['JUN'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['JUL'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['AUG'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['SEP'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['OCT'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['NOV'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['DEC'] ?? 0, 2); ?></td>
                                        <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-800/20"><?= number_format($row['total'] ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="20" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                                        <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                                <i class="fa-solid fa-cubes-stacked text-xl"></i>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Budget CAPEX</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada data belanja modal CAPEX untuk periode ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php $totCount5 = count($capex ?? []); ?>
                <div class="p-3 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $totCount5 ?> === 0 ? 0 : ((page.capex - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.capex * perPage, <?= $totCount5 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $totCount5 ?></span> data</div>
                    <div class="flex items-center gap-1.5" x-show="totalPages(<?= $totCount5 ?>) > 1">
                        <button type="button" @click="page.capex--" :disabled="page.capex === 1" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Prev</button>
                        <template x-for="(p, i) in pageNumbers('capex', <?= $totCount5 ?>)" :key="i">
                            <div>
                                <template x-if="p === '...'">
                                    <span class="px-1.5 font-bold">...</span>
                                </template>
                                <template x-if="p !== '...'">
                                    <button type="button" @click="page.capex = p" :class="page.capex === p ? 'bg-brand-500 text-white font-bold' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300'" class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
                                </template>
                            </div>
                        </template>
                        <button type="button" @click="page.capex++" :disabled="page.capex === totalPages(<?= $totCount5 ?>)" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Next</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div x-show="detailModalOpen" 
         class="fixed inset-0 flex items-center justify-center p-4 sm:p-6"
         style="z-index: 9999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
         @click.self="detailModalOpen = false" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <div class="relative w-full max-w-5xl max-h-[90vh] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-gray-200 dark:border-gray-800">
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50/80 dark:bg-gray-800/60 border-b border-gray-200 dark:border-gray-800">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Rincian Budget - <span x-text="modalTitle"></span></span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="modalSubtitle"></p>
                </div>
                <button @click="detailModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                <template x-if="isLoadingModal">
                    <div class="flex flex-col items-center justify-center py-12 gap-3 text-gray-500">
                        <svg class="w-8 h-8 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-xs font-medium">Memuat data rincian...</span>
                    </div>
                </template>

                <div x-show="!isLoadingModal" x-html="modalContent" class="text-sm"></div>
            </div>

            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800/60 border-t border-gray-200 dark:border-gray-800 flex justify-end">
                <button type="button" @click="detailModalOpen = false" class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>