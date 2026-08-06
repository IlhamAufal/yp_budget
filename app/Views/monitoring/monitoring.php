<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ 
    activeTab: 'opex_ga',
    detailModalOpen: false,
    modalTitle: '',
    modalSubtitle: '',
    modalContent: '',
    isLoadingModal: false,

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
                this.modalContent = '<div class="p-4 text-center text-red-500 font-medium">Gagal memuat data detail. Silakan coba lagi.</div>';
                this.isLoadingModal = false;
            });
    }
}" class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                Summary Profit & Loss Monitoring
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Monitoring alokasi anggaran OPEX, FOH, MPP, dan CAPEX per Cost Center.
            </p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900">
        
        <div class="border-b border-gray-200 dark:border-gray-800 px-4 pt-3 overflow-x-auto">
            <nav class="flex space-x-2 sm:space-x-4 min-w-max" aria-label="Tabs">
                <button 
                    @click="activeTab = 'opex_ga'"
                    :class="activeTab === 'opex_ga' 
                        ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-semibold bg-brand-50/50 dark:bg-brand-950/20' 
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="flex items-center gap-2 px-3.5 py-2.5 text-xs sm:text-sm font-medium border-b-2 rounded-t-lg transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Budget - OPEX GA</span>
                </button>

                <button 
                    @click="activeTab = 'foh'"
                    :class="activeTab === 'foh' 
                        ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-semibold bg-brand-50/50 dark:bg-brand-950/20' 
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="flex items-center gap-2 px-3.5 py-2.5 text-xs sm:text-sm font-medium border-b-2 rounded-t-lg transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Budget - FOH</span>
                </button>

                <button 
                    @click="activeTab = 'mpp_opex'"
                    :class="activeTab === 'mpp_opex' 
                        ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-semibold bg-brand-50/50 dark:bg-brand-950/20' 
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="flex items-center gap-2 px-3.5 py-2.5 text-xs sm:text-sm font-medium border-b-2 rounded-t-lg transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Budget - MPP OPEX</span>
                </button>

                <button 
                    @click="activeTab = 'mpp_foh'"
                    :class="activeTab === 'mpp_foh' 
                        ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-semibold bg-brand-50/50 dark:bg-brand-950/20' 
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="flex items-center gap-2 px-3.5 py-2.5 text-xs sm:text-sm font-medium border-b-2 rounded-t-lg transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Budget - MPP FOH</span>
                </button>

                <button 
                    @click="activeTab = 'capex'"
                    :class="activeTab === 'capex' 
                        ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-semibold bg-brand-50/50 dark:bg-brand-950/20' 
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="flex items-center gap-2 px-3.5 py-2.5 text-xs sm:text-sm font-medium border-b-2 rounded-t-lg transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Budget - CAPEX (OPEX & FOH)</span>
                </button>
            </nav>
        </div>

        <div class="p-4 sm:p-6">

            <div x-show="activeTab === 'opex_ga'" x-cloak class="space-y-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 uppercase tracking-wider font-semibold">
                            <tr>
                                <th rowspan="2" class="px-4 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[200px]">Cost Center</th>
                                <th colspan="14" class="px-4 py-2 border-b border-gray-200 dark:border-gray-800 text-center">Budget Expense (Rp)</th>
                            </tr>
                            <tr class="bg-gray-100/70 dark:bg-gray-800/80">
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800">Entry By</th>
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
                            <?php if (!empty($curr)): ?>
                                <?php foreach ($curr as $files): ?>
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
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
                                    <td colspan="15" class="px-4 py-8 text-center text-gray-400">Tidak ada data budget OPEX GA tersedia.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="activeTab === 'foh'" x-cloak class="space-y-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 uppercase tracking-wider font-semibold">
                            <tr>
                                <th rowspan="2" class="px-4 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[200px]">Cost Center</th>
                                <th colspan="14" class="px-4 py-2 border-b border-gray-200 dark:border-gray-800 text-center">Budget Expense (Rp)</th>
                            </tr>
                            <tr class="bg-gray-100/70 dark:bg-gray-800/80">
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800">Entry By</th>
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
                            <?php if (!empty($curr2)): ?>
                                <?php foreach ($curr2 as $files2): ?>
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
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
                                    <td colspan="15" class="px-4 py-8 text-center text-gray-400">Tidak ada data budget FOH tersedia.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="activeTab === 'mpp_opex'" x-cloak class="space-y-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 uppercase tracking-wider font-semibold">
                            <tr>
                                <th rowspan="2" class="px-4 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[180px]">Cost Center</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800">Tipe</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[140px]">Jabatan</th>
                                <th colspan="13" class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-center">New Headcount</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[150px]">Notes</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800">Salary</th>
                                <th colspan="13" class="px-3 py-2 border-b border-gray-200 dark:border-gray-800 text-center">Amount Headcount (Rp)</th>
                            </tr>
                            <tr class="bg-gray-100/70 dark:bg-gray-800/80">
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Jan</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Feb</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Mar</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Apr</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">May</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Jun</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Jul</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Aug</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Sep</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Oct</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Nov</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Dec</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right font-bold">Tot</th>
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
                            <?php if (!empty($mpp)): ?>
                                <?php foreach ($mpp as $files1): ?>
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
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
                                    <td colspan="31" class="px-4 py-8 text-center text-gray-400">Tidak ada data MPP OPEX tersedia.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="activeTab === 'mpp_foh'" x-cloak class="space-y-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 uppercase tracking-wider font-semibold">
                            <tr>
                                <th rowspan="2" class="px-4 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[180px]">Cost Center</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800">Tipe</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[140px]">Jabatan</th>
                                <th colspan="13" class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-center">New Headcount</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[150px]">Notes</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800">Salary</th>
                                <th colspan="13" class="px-3 py-2 border-b border-gray-200 dark:border-gray-800 text-center">Amount Headcount (Rp)</th>
                            </tr>
                            <tr class="bg-gray-100/70 dark:bg-gray-800/80">
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Jan</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Feb</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Mar</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Apr</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">May</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Jun</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Jul</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Aug</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Sep</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Oct</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Nov</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Dec</th>
                                <th class="px-2 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right font-bold">Tot</th>
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
                            <?php if (!empty($mpp_foh)): ?>
                                <?php foreach ($mpp_foh as $files_foh): ?>
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
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
                                    <td colspan="31" class="px-4 py-8 text-center text-gray-400">Tidak ada data MPP FOH tersedia.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="activeTab === 'capex'" x-cloak class="space-y-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 uppercase tracking-wider font-semibold">
                            <tr>
                                <th rowspan="2" class="px-4 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[180px]">Cost Center</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[200px]">Item Description</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800">Account</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800">CC Code</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800 text-right">Unit</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800 text-right min-w-[120px]">Unit Price</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[150px]">Remarks</th>
                                <th colspan="12" class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-center">Acquisition Period</th>
                                <th rowspan="2" class="px-3 py-3 border-b border-gray-200 dark:border-gray-800 text-right font-bold text-gray-900 dark:text-white min-w-[130px]">Total</th>
                            </tr>
                            <tr class="bg-gray-100/70 dark:bg-gray-800/80">
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300">
                            <?php if (!empty($capex)): ?>
                                <?php foreach ($capex as $row): ?>
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
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
                                    <td colspan="20" class="px-4 py-8 text-center text-gray-400">Tidak ada data budget CAPEX tersedia.</td>
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