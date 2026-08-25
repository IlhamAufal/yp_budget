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

    <!-- ============================================================ -->
    <!-- BREADCRUMB & HEADER -->
    <!-- ============================================================ -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold">Monitoring</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Summary Profit & Loss Monitoring
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Monitoring alokasi anggaran OPEX, FOH, MPP, dan CAPEX per Cost Center.
            </p>
        </div>
    </div>

    <!-- Sub Tabs Navigation -->
    <div class="nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="Monitoring Tabs">
            <button 
                type="button" 
                @click="activeTab = 'opex_ga'" 
                :class="activeTab === 'opex_ga' ? 'active' : ''" 
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Budget - OPEX GA</span>
            </button>

            <button 
                type="button" 
                @click="activeTab = 'foh'" 
                :class="activeTab === 'foh' ? 'active' : ''" 
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Budget - FOH</span>
            </button>

            <button 
                type="button" 
                @click="activeTab = 'mpp_opex'" 
                :class="activeTab === 'mpp_opex' ? 'active' : ''" 
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Budget - MPP OPEX</span>
            </button>

            <button 
                type="button" 
                @click="activeTab = 'mpp_foh'" 
                :class="activeTab === 'mpp_foh' ? 'active' : ''" 
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Budget - MPP FOH</span>
            </button>

            <button 
                type="button" 
                @click="activeTab = 'capex'" 
                :class="activeTab === 'capex' ? 'active' : ''" 
                class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                <span>Budget - CAPEX</span>
            </button>
        </nav>
    </div>

    <div class="space-y-6">

            <!-- TAB 1: OPEX GA -->
            <div x-show="activeTab === 'opex_ga'" x-cloak class="space-y-4">
                <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                                <tr class="bg-[#2F3185] text-white font-semibold">
                                    <th rowspan="2" class="px-4 py-3 border-r border-white/20 min-w-[200px] text-white font-semibold">Cost Center</th>
                                    <th colspan="14" class="px-4 py-2 border-r border-white/20 text-center text-white font-semibold">Budget Expense (Rp)</th>
                                </tr>
                                <tr class="bg-[#25276d] text-white text-xs font-semibold">
                                    <th class="px-3 py-2 border-r border-white/20 text-white font-semibold">Entry By</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jan</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Feb</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Mar</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Apr</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">May</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jun</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jul</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Aug</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Sep</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Oct</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Nov</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Dec</th>
                                    <th class="px-3 py-2 text-right font-bold text-white bg-[#25276d]">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300 font-mono text-xs">
                                <?php if (!empty($curr)): ?>
                                    <?php foreach ($curr as $i => $files): ?>
                                        <tr x-show="isRowVisible('opex_ga', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                            <td class="px-4 py-2.5 font-sans font-medium border-r border-gray-200 dark:border-gray-800">
                                                <button @click="openDetail('<?= $files['id_dept']; ?>', '<?= esc($files['cost_desc'] ?? ''); ?>', 'OPEX GA')" 
                                                        class="text-[#2F3185] hover:text-[#25276d] dark:text-indigo-400 font-semibold hover:underline text-left">
                                                    <?= esc($files['cc_sap'] ?? $files['id_dept']); ?> - <?= esc($files['cost_desc']); ?>
                                                </button>
                                            </td>
                                            <td class="px-3 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($files['tags'] ?? '-'); ?></td>
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
                                            <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-[#2F3185]/5 dark:bg-[#2F3185]/10"><?= number_format($files['TOT'] ?? 0, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="15" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
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
                    <div class="p-3.5 sm:p-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                        <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $totCount ?> === 0 ? 0 : ((page.opex_ga - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.opex_ga * perPage, <?= $totCount ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $totCount ?></span> data</div>
                        <div class="flex items-center gap-1" x-show="totalPages(<?= $totCount ?>) > 1">
                            <button type="button" @click="page.opex_ga--" :disabled="page.opex_ga === 1" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Prev</button>
                            <template x-for="(p, i) in pageNumbers('opex_ga', <?= $totCount ?>)" :key="i">
                                <div>
                                    <template x-if="p === '...'">
                                        <span class="px-2 font-bold text-gray-400">...</span>
                                    </template>
                                    <template x-if="p !== '...'">
                                        <button type="button" @click="page.opex_ga = p" :class="page.opex_ga === p ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50'" class="h-8 min-w-[32px] px-2 rounded-lg font-semibold transition-colors" x-text="p"></button>
                                    </template>
                                </div>
                            </template>
                            <button type="button" @click="page.opex_ga++" :disabled="page.opex_ga === totalPages(<?= $totCount ?>)" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: FOH -->
            <div x-show="activeTab === 'foh'" x-cloak class="space-y-4">
                <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                                <tr class="bg-[#2F3185] text-white font-semibold">
                                    <th rowspan="2" class="px-4 py-3 border-r border-white/20 min-w-[200px] text-white font-semibold">Cost Center</th>
                                    <th colspan="14" class="px-4 py-2 border-r border-white/20 text-center text-white font-semibold">Budget Expense (Rp)</th>
                                </tr>
                                <tr class="bg-[#25276d] text-white text-xs font-semibold">
                                    <th class="px-3 py-2 border-r border-white/20 text-white font-semibold">Entry By</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jan</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Feb</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Mar</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Apr</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">May</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jun</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jul</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Aug</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Sep</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Oct</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Nov</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Dec</th>
                                    <th class="px-3 py-2 text-right font-bold text-white bg-[#25276d]">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300 font-mono text-xs">
                                <?php if (!empty($curr2)): ?>
                                    <?php foreach ($curr2 as $i => $files2): ?>
                                        <tr x-show="isRowVisible('foh', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                            <td class="px-4 py-2.5 font-sans font-medium border-r border-gray-200 dark:border-gray-800">
                                                <button @click="openDetail('<?= $files2['id_dept']; ?>', '<?= esc($files2['cost_desc'] ?? ''); ?>', 'FOH')" 
                                                        class="text-[#2F3185] hover:text-[#25276d] dark:text-indigo-400 font-semibold hover:underline text-left">
                                                    <?= esc($files2['cc_sap'] ?? $files2['id_dept']); ?> - <?= esc($files2['cost_desc']); ?>
                                                </button>
                                            </td>
                                            <td class="px-3 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($files2['tags'] ?? '-'); ?></td>
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
                                            <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-[#2F3185]/5 dark:bg-[#2F3185]/10"><?= number_format($files2['TOT'] ?? 0, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="15" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
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
                    <div class="p-3.5 sm:p-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                        <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $totCount2 ?> === 0 ? 0 : ((page.foh - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.foh * perPage, <?= $totCount2 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $totCount2 ?></span> data</div>
                        <div class="flex items-center gap-1" x-show="totalPages(<?= $totCount2 ?>) > 1">
                            <button type="button" @click="page.foh--" :disabled="page.foh === 1" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Prev</button>
                            <template x-for="(p, i) in pageNumbers('foh', <?= $totCount2 ?>)" :key="i">
                                <div>
                                    <template x-if="p === '...'">
                                        <span class="px-2 font-bold text-gray-400">...</span>
                                    </template>
                                    <template x-if="p !== '...'">
                                        <button type="button" @click="page.foh = p" :class="page.foh === p ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50'" class="h-8 min-w-[32px] px-2 rounded-lg font-semibold transition-colors" x-text="p"></button>
                                    </template>
                                </div>
                            </template>
                            <button type="button" @click="page.foh++" :disabled="page.foh === totalPages(<?= $totCount2 ?>)" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: MPP OPEX -->
            <div x-show="activeTab === 'mpp_opex'" x-cloak class="space-y-4">
                <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                                <tr class="bg-[#2F3185] text-white font-semibold">
                                    <th rowspan="2" class="px-4 py-3 border-r border-white/20 min-w-[180px] text-white font-semibold">Cost Center</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-white font-semibold">Tipe</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 min-w-[140px] text-white font-semibold">Jabatan</th>
                                    <th colspan="13" class="px-3 py-2 border-r border-white/20 text-center text-white bg-[#25276d] font-semibold">New Headcount</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 min-w-[150px] text-white font-semibold">Notes</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-white font-semibold">Salary</th>
                                    <th colspan="13" class="px-3 py-2 text-center text-white bg-[#25276d] font-semibold">Amount Headcount (Rp)</th>
                                </tr>
                                <tr class="bg-[#25276d] text-white text-xs font-semibold">
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Jan</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Feb</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Mar</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Apr</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">May</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Jun</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Jul</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Aug</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Sep</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Oct</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Nov</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Dec</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right font-bold text-white bg-[#25276d]">Tot</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jan</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Feb</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Mar</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Apr</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">May</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jun</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jul</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Aug</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Sep</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Oct</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Nov</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Dec</th>
                                    <th class="px-3 py-2 text-right font-bold text-white bg-[#25276d]">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300 font-mono text-xs">
                                <?php if (!empty($mpp)): ?>
                                    <?php foreach ($mpp as $i => $files1): ?>
                                        <tr x-show="isRowVisible('mpp_opex', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                            <td class="px-4 py-2.5 font-sans font-medium border-r border-gray-200 dark:border-gray-800">
                                                <button @click="openDetail('<?= $files1['cost_center']; ?>', '<?= esc($files1['cost_desc'] ?? ''); ?>', 'MPP OPEX')" 
                                                        class="text-[#2F3185] hover:text-[#25276d] dark:text-indigo-400 font-semibold hover:underline text-left">
                                                    <?= esc($files1['cc_sap'] ?? $files1['cost_center']); ?> - <?= esc($files1['cost_desc']); ?>
                                                </button>
                                            </td>
                                            <td class="px-3 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($files1['desc_mpp'] ?? '-'); ?></td>
                                            <td class="px-3 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($files1['staff_name'] ?? '-'); ?></td>
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
                                            <td class="px-2 py-2.5 text-right font-bold border-r border-gray-200 dark:border-gray-800 bg-[#2F3185]/5 dark:bg-[#2F3185]/10"><?= number_format($files1['TOT'] ?? 0, 0); ?></td>
                                            <td class="px-3 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($files1['notes'] ?? '-'); ?></td>
                                            <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= is_numeric($files1['salary'] ?? null) ? number_format($files1['salary'], 2) : esc($files1['salary'] ?? '-'); ?></td>
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
                                            <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-[#2F3185]/5 dark:bg-[#2F3185]/10"><?= number_format($files1['TOT_AMT'] ?? 0, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="31" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
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
                    <div class="p-3.5 sm:p-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                        <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $totCount3 ?> === 0 ? 0 : ((page.mpp_opex - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.mpp_opex * perPage, <?= $totCount3 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $totCount3 ?></span> data</div>
                        <div class="flex items-center gap-1" x-show="totalPages(<?= $totCount3 ?>) > 1">
                            <button type="button" @click="page.mpp_opex--" :disabled="page.mpp_opex === 1" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Prev</button>
                            <template x-for="(p, i) in pageNumbers('mpp_opex', <?= $totCount3 ?>)" :key="i">
                                <div>
                                    <template x-if="p === '...'">
                                        <span class="px-2 font-bold text-gray-400">...</span>
                                    </template>
                                    <template x-if="p !== '...'">
                                        <button type="button" @click="page.mpp_opex = p" :class="page.mpp_opex === p ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50'" class="h-8 min-w-[32px] px-2 rounded-lg font-semibold transition-colors" x-text="p"></button>
                                    </template>
                                </div>
                            </template>
                            <button type="button" @click="page.mpp_opex++" :disabled="page.mpp_opex === totalPages(<?= $totCount3 ?>)" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: MPP FOH -->
            <div x-show="activeTab === 'mpp_foh'" x-cloak class="space-y-4">
                <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                                <tr class="bg-[#2F3185] text-white font-semibold">
                                    <th rowspan="2" class="px-4 py-3 border-r border-white/20 min-w-[180px] text-white font-semibold">Cost Center</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-white font-semibold">Tipe</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 min-w-[140px] text-white font-semibold">Jabatan</th>
                                    <th colspan="13" class="px-3 py-2 border-r border-white/20 text-center text-white bg-[#25276d] font-semibold">New Headcount</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 min-w-[150px] text-white font-semibold">Notes</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-white font-semibold">Salary</th>
                                    <th colspan="13" class="px-3 py-2 text-center text-white bg-[#25276d] font-semibold">Amount Headcount (Rp)</th>
                                </tr>
                                <tr class="bg-[#25276d] text-white text-xs font-semibold">
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Jan</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Feb</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Mar</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Apr</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">May</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Jun</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Jul</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Aug</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Sep</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Oct</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Nov</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right text-white font-semibold">Dec</th>
                                    <th class="px-2 py-2 border-r border-white/20 text-right font-bold text-white bg-[#25276d]">Tot</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jan</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Feb</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Mar</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Apr</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">May</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jun</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jul</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Aug</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Sep</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Oct</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Nov</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Dec</th>
                                    <th class="px-3 py-2 text-right font-bold text-white bg-[#25276d]">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300 font-mono text-xs">
                                <?php if (!empty($mpp_foh)): ?>
                                    <?php foreach ($mpp_foh as $i => $files_foh): ?>
                                        <tr x-show="isRowVisible('mpp_foh', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                            <td class="px-4 py-2.5 font-sans font-medium border-r border-gray-200 dark:border-gray-800">
                                                <button @click="openDetail('<?= $files_foh['cost_center']; ?>', '<?= esc($files_foh['cost_desc'] ?? ''); ?>', 'MPP FOH')" 
                                                        class="text-[#2F3185] hover:text-[#25276d] dark:text-indigo-400 font-semibold hover:underline text-left">
                                                    <?= esc($files_foh['cc_sap'] ?? $files_foh['cost_center']); ?> - <?= esc($files_foh['cost_desc']); ?>
                                                </button>
                                            </td>
                                            <td class="px-3 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($files_foh['desc_mpp'] ?? '-'); ?></td>
                                            <td class="px-3 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($files_foh['staff_name'] ?? '-'); ?></td>
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
                                            <td class="px-2 py-2.5 text-right font-bold border-r border-gray-200 dark:border-gray-800 bg-[#2F3185]/5 dark:bg-[#2F3185]/10"><?= number_format($files_foh['TOT'] ?? 0, 0); ?></td>
                                            <td class="px-3 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($files_foh['notes'] ?? '-'); ?></td>
                                            <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= is_numeric($files_foh['salary'] ?? null) ? number_format($files_foh['salary'], 2) : esc($files_foh['salary'] ?? '-'); ?></td>
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
                                            <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-[#2F3185]/5 dark:bg-[#2F3185]/10"><?= number_format($files_foh['TOT_AMT'] ?? 0, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="31" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
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
                    <div class="p-3.5 sm:p-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                        <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $totCount4 ?> === 0 ? 0 : ((page.mpp_foh - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.mpp_foh * perPage, <?= $totCount4 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $totCount4 ?></span> data</div>
                        <div class="flex items-center gap-1" x-show="totalPages(<?= $totCount4 ?>) > 1">
                            <button type="button" @click="page.mpp_foh--" :disabled="page.mpp_foh === 1" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Prev</button>
                            <template x-for="(p, i) in pageNumbers('mpp_foh', <?= $totCount4 ?>)" :key="i">
                                <div>
                                    <template x-if="p === '...'">
                                        <span class="px-2 font-bold text-gray-400">...</span>
                                    </template>
                                    <template x-if="p !== '...'">
                                        <button type="button" @click="page.mpp_foh = p" :class="page.mpp_foh === p ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50'" class="h-8 min-w-[32px] px-2 rounded-lg font-semibold transition-colors" x-text="p"></button>
                                    </template>
                                </div>
                            </template>
                            <button type="button" @click="page.mpp_foh++" :disabled="page.mpp_foh === totalPages(<?= $totCount4 ?>)" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: CAPEX -->
            <div x-show="activeTab === 'capex'" x-cloak class="space-y-4">
                <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                                <tr class="bg-[#2F3185] text-white font-semibold">
                                    <th rowspan="2" class="px-4 py-3 border-r border-white/20 min-w-[180px] text-white font-semibold">Cost Center</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 min-w-[200px] text-white font-semibold">Item Description</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-white font-semibold">Account</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-white font-semibold">CC Code</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-right text-white font-semibold">Unit</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 text-right min-w-[120px] text-white font-semibold">Unit Price</th>
                                    <th rowspan="2" class="px-3 py-3 border-r border-white/20 min-w-[150px] text-white font-semibold">Remarks</th>
                                    <th colspan="12" class="px-3 py-2 border-r border-white/20 text-center text-white bg-[#25276d] font-semibold">Acquisition Period</th>
                                    <th rowspan="2" class="px-3 py-3 text-right font-bold text-white bg-[#25276d] min-w-[130px]">Total</th>
                                </tr>
                                <tr class="bg-[#25276d] text-white text-xs font-semibold">
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jan</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Feb</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Mar</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Apr</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">May</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jun</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Jul</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Aug</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Sep</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Oct</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Nov</th>
                                    <th class="px-3 py-2 border-r border-white/20 text-right text-white font-semibold">Dec</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300 font-mono text-xs">
                                <?php if (!empty($capex)): ?>
                                    <?php foreach ($capex as $i => $row): ?>
                                        <tr x-show="isRowVisible('capex', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                            <td class="px-4 py-2.5 font-sans font-medium border-r border-gray-200 dark:border-gray-800"><?= esc($row['cost_center_desc'] ?? ''); ?></td>
                                            <td class="px-3 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($row['item_desc'] ?? ''); ?></td>
                                            <td class="px-3 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($row['main_account'] ?? ''); ?></td>
                                            <td class="px-3 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($row['cost_center'] ?? ''); ?></td>
                                            <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['unit'] ?? 0, 0); ?></td>
                                            <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800"><?= number_format($row['unit_price'] ?? 0, 2); ?></td>
                                            <td class="px-3 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($row['remarks'] ?? '-'); ?></td>
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
                                            <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-[#2F3185]/5 dark:bg-[#2F3185]/10"><?= number_format($row['total'] ?? 0, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="20" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
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
                    <div class="p-3.5 sm:p-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                        <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $totCount5 ?> === 0 ? 0 : ((page.capex - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.capex * perPage, <?= $totCount5 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $totCount5 ?></span> data</div>
                        <div class="flex items-center gap-1" x-show="totalPages(<?= $totCount5 ?>) > 1">
                            <button type="button" @click="page.capex--" :disabled="page.capex === 1" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Prev</button>
                            <template x-for="(p, i) in pageNumbers('capex', <?= $totCount5 ?>)" :key="i">
                                <div>
                                    <template x-if="p === '...'">
                                        <span class="px-2 font-bold text-gray-400">...</span>
                                    </template>
                                    <template x-if="p !== '...'">
                                        <button type="button" @click="page.capex = p" :class="page.capex === p ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50'" class="h-8 min-w-[32px] px-2 rounded-lg font-semibold transition-colors" x-text="p"></button>
                                    </template>
                                </div>
                            </template>
                            <button type="button" @click="page.capex++" :disabled="page.capex === totalPages(<?= $totCount5 ?>)" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Next</button>
                        </div>
                    </div>
                </div>
            </div>

    </div>

    <!-- DETAIL MODAL -->
    <template x-teleport="body">
    <div x-show="detailModalOpen" 
         class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 z-[9999999]"
         style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(4px);"
         @click.self="detailModalOpen = false" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <div class="relative w-full max-w-5xl max-h-[90vh] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between p-6 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        <span>Rincian Budget - <span x-text="modalTitle"></span></span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="modalSubtitle"></p>
                </div>
                <button @click="detailModalOpen = false" class="h-8 w-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                <template x-if="isLoadingModal">
                    <div class="flex flex-col items-center justify-center py-12 gap-3 text-gray-500">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                            <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Memuat data rincian...</span>
                    </div>
                </template>

                <div x-show="!isLoadingModal" x-html="modalContent" class="text-sm"></div>
            </div>

            <div class="p-4 bg-gray-50/50 dark:bg-gray-800/40 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                <button type="button" @click="detailModalOpen = false" class="px-5 py-2.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    </template>

</div>
<?= $this->endSection() ?>