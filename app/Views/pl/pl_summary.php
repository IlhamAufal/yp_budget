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

    sectionModalOpen: false,
    sectionData: [],
    sectionLabel: '',
    isLoadingSection: false,
    flashCode: '',
    flashMsg: '',
    _flashTimer: null,

    page: { summary: 1, sections: 1, detail: 1 },
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

    rowState: <?= esc(json_encode($rowState ?? [], JSON_NUMERIC_CHECK), 'attr') ?>,

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
    },

    openSection(code, label) {
        this.sectionLabel = code + ' - ' + label;
        this.sectionModalOpen = true;
        this.isLoadingSection = true;
        this.sectionData = [];

        fetch(`<?= base_url('pl/get_section_detail') ?>?section=${code}`)
            .then(res => res.json())
            .then(data => {
                this.sectionData = data.items || [];
                this.isLoadingSection = false;
            })
            .catch(() => {
                this.sectionData = [];
                this.isLoadingSection = false;
            });
    },

    saveSectionNotes(code, event) {
        const fd = new FormData();
        fd.append('code', code);
        fd.append('notes', event.target.value);

        fetch('<?= base_url('pl/save_notes') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
            .then(r => r.json())
            .then(d => this.saveFlash(code, d.message || 'Tersimpan'))
            .catch(() => this.saveFlash(code, 'Gagal menyimpan catatan.'));
    },

    saveSectionAdjs(code) {
        const fd = new FormData();
        fd.append('code', code);
        fd.append('value', this.rowState[code] ? this.rowState[code].adj : 0);

        fetch('<?= base_url('pl/save_adjs') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
            .then(r => r.json())
            .then(d => this.saveFlash(code, d.message || 'Tersimpan'))
            .catch(() => this.saveFlash(code, 'Gagal menyimpan adjustment.'));
    },

    saveFlash(code, msg) {
        this.flashCode = code;
        this.flashMsg = msg;
        clearTimeout(this._flashTimer);
        this._flashTimer = setTimeout(() => { this.flashCode = ''; this.flashMsg = ''; }, 2500);
    },

    adjTotal(code) {
        const s = this.rowState[code] || {};
        return Number(s.total || 0) + Number(s.adj || 0);
    },

    fmt(v) {
        return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Number(v || 0));
    }
}" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Profit & Loss (P&L) Report
            </h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
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
                           class="w-full pl-11 pr-3.5 py-2 text-xs rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:focus:ring-brand-500/20">
                </div>

                <select x-model="filterDept" class="px-3 py-2 text-xs rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <option value="">-- Semua Cost Center --</option>
                    <?php if (!empty($departments)): ?>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= esc($dept['id_dept']); ?>"><?= esc($dept['cc_sap'] ?? $dept['id_dept']); ?> - <?= esc($dept['cost_desc']); ?></option>
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
                <button @click="activeTab = 'sections'"
                        :class="activeTab === 'sections' ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
                    Per Bagian
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
                                <?php foreach ($pl_summary as $i => $item): ?>
                                    <tr x-show="isRowVisible('summary', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors <?= !empty($item['is_header']) ? 'font-bold bg-gray-50/50 dark:bg-gray-800/30' : '' ?>">
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
                <?php $cnt1 = count($pl_summary ?? []); ?>
                <div class="p-3 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $cnt1 ?> === 0 ? 0 : ((page.summary - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.summary * perPage, <?= $cnt1 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $cnt1 ?></span> data</div>
                    <div class="flex items-center gap-1.5" x-show="totalPages(<?= $cnt1 ?>) > 1">
                        <button type="button" @click="page.summary--" :disabled="page.summary === 1" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Prev</button>
                        <template x-for="(p, i) in pageNumbers('summary', <?= $cnt1 ?>)" :key="i">
                            <div>
                                <template x-if="p === '...'">
                                    <span class="px-1.5 font-bold">...</span>
                                </template>
                                <template x-if="p !== '...'">
                                    <button type="button" @click="page.summary = p" :class="page.summary === p ? 'bg-brand-500 text-white font-bold' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300'" class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
                                </template>
                            </div>
                        </template>
                        <button type="button" @click="page.summary++" :disabled="page.summary === totalPages(<?= $cnt1 ?>)" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Next</button>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'sections'" x-cloak class="space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Ringkasan P/L per bagian. Klik <span class="font-semibold">Detail</span> untuk melihat rincian akun, atau edit
                        <span class="font-semibold">Adjustment</span> & <span class="font-semibold">Catatan</span> langsung (tersimpan otomatis saat blur).
                    </p>
                </div>
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-4 py-3 border-b border-r border-gray-200 dark:border-gray-800 min-w-[220px]">Bagian P/L</th>
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
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Total</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right min-w-[130px]">Adjustment</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 text-right">Total Adj.</th>
                                <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 min-w-[200px]">Catatan</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-gray-800 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300">
                            <?php if (!empty($pl_sections)): ?>
                                <?php foreach ($pl_sections as $i => $sec): ?>
                                    <?php $secCode = $sec['code']; ?>
                                    <tr x-show="isRowVisible('sections', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors align-top">
                                        <td class="px-4 py-2.5 border-r border-gray-200 dark:border-gray-800">
                                            <div class="font-bold text-gray-900 dark:text-white"><?= esc($secCode); ?></div>
                                            <div class="text-[11px] text-gray-500 dark:text-gray-400"><?= esc($sec['label']); ?></div>
                                            <div class="text-[10px] mt-1 h-3" x-show="flashCode === '<?= $secCode; ?>'" x-cloak>
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400" x-text="flashMsg"></span>
                                            </div>
                                        </td>
                                        <?php foreach (range(1, 12) as $m): ?>
                                            <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800 whitespace-nowrap"><?= number_format($sec['m' . $m], 0); ?></td>
                                        <?php endforeach; ?>
                                        <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800 whitespace-nowrap" x-text="fmt(rowState['<?= $secCode; ?>'].total)"></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800">
                                            <input type="number" step="any"
                                                   x-model.number="rowState['<?= $secCode; ?>'].adj"
                                                   @blur="saveSectionAdjs('<?= $secCode; ?>')"
                                                   class="w-28 px-2 py-1.5 text-xs rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                        </td>
                                        <td class="px-3 py-2.5 text-right font-semibold text-brand-600 dark:text-brand-400 border-r border-gray-200 dark:border-gray-800 whitespace-nowrap" x-text="fmt(adjTotal('<?= $secCode; ?>'))"></td>
                                        <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800">
                                            <textarea rows="1" @blur="saveSectionNotes('<?= $secCode; ?>', $event)"
                                                      class="w-full min-w-[180px] px-2 py-1.5 text-xs rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white resize-y"
                                                      placeholder="Tulis catatan..."><?= esc($pl_notes[$secCode] ?? ''); ?></textarea>
                                        </td>
                                        <td class="px-3 py-2.5 text-center">
                                            <button @click="openSection('<?= $secCode; ?>', '<?= esc($sec['label'], 'js'); ?>')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100 dark:bg-brand-950/40 dark:text-brand-400 dark:hover:bg-brand-900/40 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail</span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="18" class="px-4 py-8 text-center text-gray-400">Tidak ada data P&L per bagian.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php $cnt2 = count($pl_sections ?? []); ?>
                <div class="p-3 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $cnt2 ?> === 0 ? 0 : ((page.sections - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.sections * perPage, <?= $cnt2 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $cnt2 ?></span> data</div>
                    <div class="flex items-center gap-1.5" x-show="totalPages(<?= $cnt2 ?>) > 1">
                        <button type="button" @click="page.sections--" :disabled="page.sections === 1" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Prev</button>
                        <template x-for="(p, i) in pageNumbers('sections', <?= $cnt2 ?>)" :key="i">
                            <div>
                                <template x-if="p === '...'">
                                    <span class="px-1.5 font-bold">...</span>
                                </template>
                                <template x-if="p !== '...'">
                                    <button type="button" @click="page.sections = p" :class="page.sections === p ? 'bg-brand-500 text-white font-bold' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300'" class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
                                </template>
                            </div>
                        </template>
                        <button type="button" @click="page.sections++" :disabled="page.sections === totalPages(<?= $cnt2 ?>)" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Next</button>
                    </div>
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
                                <?php foreach ($pl_details as $i => $detail): ?>
                                    <tr x-show="isRowVisible('detail', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
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
                                            <button @click="openDetail('<?= $detail['id_coa']; ?>', '<?= esc($detail['account_desc']); ?>')"
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
                <?php $cnt3 = count($pl_details ?? []); ?>
                <div class="p-3 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $cnt3 ?> === 0 ? 0 : ((page.detail - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.detail * perPage, <?= $cnt3 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $cnt3 ?></span> data</div>
                    <div class="flex items-center gap-1.5" x-show="totalPages(<?= $cnt3 ?>) > 1">
                        <button type="button" @click="page.detail--" :disabled="page.detail === 1" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Prev</button>
                        <template x-for="(p, i) in pageNumbers('detail', <?= $cnt3 ?>)" :key="i">
                            <div>
                                <template x-if="p === '...'">
                                    <span class="px-1.5 font-bold">...</span>
                                </template>
                                <template x-if="p !== '...'">
                                    <button type="button" @click="page.detail = p" :class="page.detail === p ? 'bg-brand-500 text-white font-bold' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300'" class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
                                </template>
                            </div>
                        </template>
                        <button type="button" @click="page.detail++" :disabled="page.detail === totalPages(<?= $cnt3 ?>)" class="h-7 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold">Next</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div x-show="detailModalOpen"
         class="fixed inset-0 flex items-center justify-center p-4 sm:p-6"
         style="z-index: 999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
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
                                <template x-for="(row, i) in modalData" :key="i">
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

    <div x-show="sectionModalOpen"
         class="fixed inset-0 flex items-center justify-center p-4 sm:p-6"
         style="z-index: 999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
         @click.self="sectionModalOpen = false"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <div class="relative w-full max-w-6xl max-h-[85vh] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-gray-200 dark:border-gray-800">
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50/80 dark:bg-gray-800/60 border-b border-gray-200 dark:border-gray-800">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Rincian Bagian P/L</h3>
                    <p class="text-xs text-brand-600 dark:text-brand-400 font-semibold mt-0.5" x-text="sectionLabel"></p>
                </div>
                <button @click="sectionModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 overflow-auto flex-1">
                <template x-if="isLoadingSection">
                    <div class="flex flex-col items-center justify-center py-10 gap-3 text-gray-500">
                        <svg class="w-7 h-7 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-xs">Memuat rincian bagian...</span>
                    </div>
                </template>

                <template x-if="!isLoadingSection">
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 uppercase font-semibold">
                                <tr>
                                    <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800">Account</th>
                                    <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800 min-w-[160px]">Uraian</th>
                                    <th class="px-3 py-2 border-b border-r border-gray-200 dark:border-gray-800">Cost Center</th>
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
                                    <th class="px-3 py-2 border-b border-gray-200 dark:border-gray-800 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                <template x-if="sectionData.length === 0">
                                    <tr>
                                        <td colspan="16" class="px-4 py-8 text-center text-gray-400">Tidak ada rincian untuk bagian ini.</td>
                                    </tr>
                                </template>
                                <template x-for="(row, i) in sectionData" :key="i">
                                    <tr>
                                        <td class="px-3 py-2 border-r border-gray-200 dark:border-gray-800 font-medium" x-text="row.account"></td>
                                        <td class="px-3 py-2 border-r border-gray-200 dark:border-gray-800" x-text="row.account_desc"></td>
                                        <td class="px-3 py-2 border-r border-gray-200 dark:border-gray-800" x-text="row.cost_center"></td>
                                        <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m1)"></td>
                                        <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m2)"></td>
                                        <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m3)"></td>
                                        <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m4)"></td>
                                        <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m5)"></td>
                                        <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m6)"></td>
                                        <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m7)"></td>
                                        <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m8)"></td>
                                        <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m9)"></td>
                                        <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m10)"></td>
                                        <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m11)"></td>
                                        <td class="px-3 py-2 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m12)"></td>
                                        <td class="px-3 py-2 text-right font-bold text-gray-900 dark:text-white" x-text="fmt(row.total)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800/60 border-t border-gray-200 dark:border-gray-800 flex justify-end">
                <button type="button" @click="sectionModalOpen = false" class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
