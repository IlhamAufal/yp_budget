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
}" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold">Profit & Loss (P&L)</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Profit & Loss (P&L) Report
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Rincian dan ringkasan Laporan Laba Rugi Operasional Perusahaan.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?= base_url('pl/export_excel') ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl px-5 py-2.5 shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98] text-xs">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <!-- FILTER & TABS CARD -->
    <div class="space-y-4">
        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pencarian Akun / Uraian</label>
                    <input type="text" x-model="searchQuery" placeholder="Ketik untuk mencari akun atau uraian..."
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Cost Center</label>
                    <select x-model="filterDept" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white">
                        <option value="">-- Semua Cost Center --</option>
                        <?php if (!empty($departments)): ?>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= esc($dept['id_dept']); ?>"><?= esc($dept['cc_sap'] ?? $dept['id_dept']); ?> - <?= esc($dept['cost_desc']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- SUB-TABS NAVIGATION -->
        <div class="nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
            <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="Tabs P&L">
                <button type="button" @click="activeTab = 'summary'"
                        :class="activeTab === 'summary' ? 'active' : ''"
                        class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                    <span>Summary P&L</span>
                </button>
                <button type="button" @click="activeTab = 'sections'"
                        :class="activeTab === 'sections' ? 'active' : ''"
                        class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                    <span>Per Bagian</span>
                </button>
                <button type="button" @click="activeTab = 'detail'"
                        :class="activeTab === 'detail' ? 'active' : ''"
                        class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                    <span>Breakdown per Akun</span>
                </button>
            </nav>
        </div>

        <!-- TAB CONTENT AREA -->
        <div>
            <!-- TAB 1: SUMMARY -->
            <div x-show="activeTab === 'summary'" x-cloak class="space-y-4">
                <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                                <tr class="bg-[#2F3185] text-white font-semibold">
                                    <th class="px-4 py-3 border-r border-white/20 min-w-[240px] text-white font-semibold">Kategori / Uraian Account</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Jan</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Feb</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Mar</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Apr</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">May</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Jun</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Jul</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Aug</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Sep</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Oct</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Nov</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Dec</th>
                                    <th class="px-3 py-2.5 text-right font-bold text-white bg-[#25276d]">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300 font-mono text-xs">
                                <?php if (!empty($pl_summary)): ?>
                                    <?php foreach ($pl_summary as $i => $item): ?>
                                        <tr x-show="isRowVisible('summary', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors <?= !empty($item['is_header']) ? 'font-bold bg-[#2F3185]/5 dark:bg-[#2F3185]/10 text-gray-900 dark:text-white' : '' ?>">
                                            <td class="px-4 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans font-medium">
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
                                            <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-[#2F3185]/5 dark:bg-[#2F3185]/10"><?= number_format($item['total'] ?? 0, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="14" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                                                    <i class="fa-solid fa-scale-balanced text-xl"></i>
                                                </div>
                                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data P&L Summary</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada ringkasan laporan laba rugi untuk periode ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php $cnt1 = count($pl_summary ?? []); ?>
                    <div class="p-3.5 sm:p-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                        <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $cnt1 ?> === 0 ? 0 : ((page.summary - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.summary * perPage, <?= $cnt1 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $cnt1 ?></span> data</div>
                        <div class="flex items-center gap-1" x-show="totalPages(<?= $cnt1 ?>) > 1">
                            <button type="button" @click="page.summary--" :disabled="page.summary === 1" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Prev</button>
                            <template x-for="(p, i) in pageNumbers('summary', <?= $cnt1 ?>)" :key="i">
                                <div>
                                    <template x-if="p === '...'">
                                        <span class="px-2 font-bold text-gray-400">...</span>
                                    </template>
                                    <template x-if="p !== '...'">
                                        <button type="button" @click="page.summary = p" :class="page.summary === p ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50'" class="h-8 min-w-[32px] px-2 rounded-lg font-semibold transition-colors" x-text="p"></button>
                                    </template>
                                </div>
                            </template>
                            <button type="button" @click="page.summary++" :disabled="page.summary === totalPages(<?= $cnt1 ?>)" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: PER BAGIAN -->
            <div x-show="activeTab === 'sections'" x-cloak class="space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Ringkasan P/L per bagian. Klik <span class="font-semibold text-[#2F3185] dark:text-indigo-400">Detail</span> untuk melihat rincian akun, atau edit
                        <span class="font-semibold text-gray-800 dark:text-gray-200">Adjustment</span> & <span class="font-semibold text-gray-800 dark:text-gray-200">Catatan</span> langsung (tersimpan otomatis saat blur).
                    </p>
                </div>
                <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                                <tr class="bg-[#2F3185] text-white font-semibold">
                                    <th class="px-4 py-3 border-r border-white/20 min-w-[220px] text-white font-semibold">Bagian P/L</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Jan</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Feb</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Mar</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Apr</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">May</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Jun</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Jul</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Aug</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Sep</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Oct</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Nov</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Dec</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right font-bold text-white bg-[#25276d]">Total</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right min-w-[130px] text-white font-semibold">Adjustment</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right font-bold text-white bg-[#25276d]">Total Adj.</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 min-w-[200px] text-white font-semibold">Catatan</th>
                                    <th class="px-3 py-2.5 text-center text-white font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300 font-mono text-xs">
                                <?php if (!empty($pl_sections)): ?>
                                    <?php foreach ($pl_sections as $i => $sec): ?>
                                        <?php $secCode = $sec['code']; ?>
                                        <tr x-show="isRowVisible('sections', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors align-top">
                                            <td class="px-4 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans">
                                                <div class="font-bold text-gray-900 dark:text-white"><?= esc($secCode); ?></div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400"><?= esc($sec['label']); ?></div>
                                                <div class="text-[10px] mt-1 h-3" x-show="flashCode === '<?= $secCode; ?>'" x-cloak>
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400" x-text="flashMsg"></span>
                                                </div>
                                            </td>
                                            <?php foreach (range(1, 12) as $m): ?>
                                                <td class="px-3 py-2.5 text-right border-r border-gray-200 dark:border-gray-800 whitespace-nowrap"><?= number_format($sec['m' . $m], 0); ?></td>
                                            <?php endforeach; ?>
                                            <td class="px-3 py-2.5 text-right font-bold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800 whitespace-nowrap bg-[#2F3185]/5 dark:bg-[#2F3185]/10" x-text="fmt(rowState['<?= $secCode; ?>'].total)"></td>
                                            <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800">
                                                <input type="number" step="any"
                                                       x-model.number="rowState['<?= $secCode; ?>'].adj"
                                                       @blur="saveSectionAdjs('<?= $secCode; ?>')"
                                                       class="w-28 px-3 py-1.5 text-xs font-mono rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white">
                                            </td>
                                            <td class="px-3 py-2.5 text-right font-bold text-[#2F3185] dark:text-indigo-400 border-r border-gray-200 dark:border-gray-800 whitespace-nowrap bg-[#2F3185]/5 dark:bg-[#2F3185]/10" x-text="fmt(adjTotal('<?= $secCode; ?>'))"></td>
                                            <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans">
                                                <textarea rows="1" @blur="saveSectionNotes('<?= $secCode; ?>', $event)"
                                                          class="w-full min-w-[180px] px-3 py-1.5 text-xs rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white resize-y"
                                                          placeholder="Tulis catatan..."><?= esc($pl_notes[$secCode] ?? ''); ?></textarea>
                                            </td>
                                            <td class="px-3 py-2.5 text-center font-sans">
                                                <button @click="openSection('<?= $secCode; ?>', '<?= esc($sec['label'], 'js'); ?>')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#2F3185] bg-[#2F3185]/10 hover:bg-[#2F3185]/20 dark:bg-[#2F3185]/20 dark:text-indigo-400 dark:hover:bg-[#2F3185]/30 rounded-xl transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Detail</span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="18" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                                                    <i class="fa-solid fa-layer-group text-xl"></i>
                                                </div>
                                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Data Bagian P&L</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada ringkasan per bagian untuk periode ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php $cnt2 = count($pl_sections ?? []); ?>
                    <div class="p-3.5 sm:p-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                        <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $cnt2 ?> === 0 ? 0 : ((page.sections - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.sections * perPage, <?= $cnt2 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $cnt2 ?></span> data</div>
                        <div class="flex items-center gap-1" x-show="totalPages(<?= $cnt2 ?>) > 1">
                            <button type="button" @click="page.sections--" :disabled="page.sections === 1" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Prev</button>
                            <template x-for="(p, i) in pageNumbers('sections', <?= $cnt2 ?>)" :key="i">
                                <div>
                                    <template x-if="p === '...'">
                                        <span class="px-2 font-bold text-gray-400">...</span>
                                    </template>
                                    <template x-if="p !== '...'">
                                        <button type="button" @click="page.sections = p" :class="page.sections === p ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50'" class="h-8 min-w-[32px] px-2 rounded-lg font-semibold transition-colors" x-text="p"></button>
                                    </template>
                                </div>
                            </template>
                            <button type="button" @click="page.sections++" :disabled="page.sections === totalPages(<?= $cnt2 ?>)" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: BREAKDOWN PER AKUN -->
            <div x-show="activeTab === 'detail'" x-cloak class="space-y-4">
                <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                                <tr class="bg-[#2F3185] text-white font-semibold">
                                    <th class="px-4 py-3 border-r border-white/20 text-white font-semibold">Kode Akun</th>
                                    <th class="px-4 py-3 border-r border-white/20 min-w-[200px] text-white font-semibold">Deskripsi Akun</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-white font-semibold">Tipe</th>
                                    <th class="px-3 py-2.5 border-r border-white/20 text-right text-white font-semibold">Total Annual Budget</th>
                                    <th class="px-3 py-2.5 text-center text-white font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300 text-xs">
                                <?php if (!empty($pl_details)): ?>
                                    <?php foreach ($pl_details as $i => $detail): ?>
                                        <tr x-show="isRowVisible('detail', <?= $i ?>)" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                            <td class="px-4 py-2.5 font-mono font-medium border-r border-gray-200 dark:border-gray-800"><?= esc($detail['account_code']); ?></td>
                                            <td class="px-4 py-2.5 font-sans border-r border-gray-200 dark:border-gray-800"><?= esc($detail['account_desc']); ?></td>
                                            <td class="px-3 py-2.5 border-r border-gray-200 dark:border-gray-800">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400">
                                                    <?= esc($detail['type'] ?? 'Expense'); ?>
                                                </span>
                                            </td>
                                            <td class="px-3 py-2.5 text-right font-mono font-semibold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800">
                                                <?= number_format($detail['annual_total'] ?? 0, 2); ?>
                                            </td>
                                            <td class="px-3 py-2.5 text-center">
                                                <button @click="openDetail('<?= $detail['id_coa']; ?>', '<?= esc($detail['account_desc']); ?>')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#2F3185] bg-[#2F3185]/10 hover:bg-[#2F3185]/20 dark:bg-[#2F3185]/20 dark:text-indigo-400 dark:hover:bg-[#2F3185]/30 rounded-xl transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Detail</span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                            <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                                                    <i class="fa-solid fa-list-check text-xl"></i>
                                                </div>
                                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Rincian Akun</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ditemukan data akun untuk kriteria filter yang dipilih.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php $cnt3 = count($pl_details ?? []); ?>
                    <div class="p-3.5 sm:p-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
                        <div>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="<?= $cnt3 ?> === 0 ? 0 : ((page.detail - 1) * perPage + 1)"></span> - <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(page.detail * perPage, <?= $cnt3 ?>)"></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= $cnt3 ?></span> data</div>
                        <div class="flex items-center gap-1" x-show="totalPages(<?= $cnt3 ?>) > 1">
                            <button type="button" @click="page.detail--" :disabled="page.detail === 1" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Prev</button>
                            <template x-for="(p, i) in pageNumbers('detail', <?= $cnt3 ?>)" :key="i">
                                <div>
                                    <template x-if="p === '...'">
                                        <span class="px-2 font-bold text-gray-400">...</span>
                                    </template>
                                    <template x-if="p !== '...'">
                                        <button type="button" @click="page.detail = p" :class="page.detail === p ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50'" class="h-8 min-w-[32px] px-2 rounded-lg font-semibold transition-colors" x-text="p"></button>
                                    </template>
                                </div>
                            </template>
                            <button type="button" @click="page.detail++" :disabled="page.detail === totalPages(<?= $cnt3 ?>)" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 disabled:opacity-40 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">Next</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- DETAIL MODAL -->
    <template x-teleport="body">
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

        <div class="relative w-full max-w-4xl max-h-[85vh] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-gray-200 dark:border-gray-800">
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50/80 dark:bg-gray-800/60 border-b border-gray-200 dark:border-gray-800">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Rincian Transaksi Akun</h3>
                    <p class="text-xs text-[#2F3185] dark:text-indigo-400 font-semibold mt-0.5" x-text="selectedAccount"></p>
                </div>
                <button @click="detailModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                <template x-if="isLoadingModal">
                    <div class="flex flex-col items-center justify-center py-10 gap-3 text-gray-500">
                        <svg class="w-7 h-7 animate-spin text-[#2F3185]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-xs font-semibold">Memuat detail transaksi...</span>
                    </div>
                </template>

                <template x-if="!isLoadingModal">
                    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                                <tr class="bg-[#2F3185] text-white font-semibold">
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-white font-semibold">Cost Center</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-white font-semibold">Uraian / Remarks</th>
                                    <th class="px-3.5 py-2.5 text-right text-white font-semibold">Nominal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs">
                                <template x-for="(row, i) in modalData" :key="i">
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 font-mono font-medium" x-text="row.cost_center"></td>
                                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800" x-text="row.remarks"></td>
                                        <td class="px-3.5 py-2.5 text-right font-mono font-semibold text-gray-900 dark:text-white" x-text="row.amount"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            <div class="px-6 py-3 bg-gray-50/50 dark:bg-gray-800/40 border-t border-gray-200 dark:border-gray-800 flex justify-end">
                <button type="button" @click="detailModalOpen = false" class="px-5 py-2.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    </template>

    <!-- SECTION MODAL -->
    <template x-teleport="body">
    <div x-show="sectionModalOpen"
         class="fixed inset-0 flex items-center justify-center p-4 sm:p-6"
         style="z-index: 9999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
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
                    <p class="text-xs text-[#2F3185] dark:text-indigo-400 font-semibold mt-0.5" x-text="sectionLabel"></p>
                </div>
                <button @click="sectionModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 overflow-auto flex-1">
                <template x-if="isLoadingSection">
                    <div class="flex flex-col items-center justify-center py-10 gap-3 text-gray-500">
                        <svg class="w-7 h-7 animate-spin text-[#2F3185]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-xs font-semibold">Memuat rincian bagian...</span>
                    </div>
                </template>

                <template x-if="!isLoadingSection">
                    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                                <tr class="bg-[#2F3185] text-white font-semibold">
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-white font-semibold">Account</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 min-w-[160px] text-white font-semibold">Uraian</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-white font-semibold">Cost Center</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-right text-white font-semibold">Jan</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-right text-white font-semibold">Feb</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-right text-white font-semibold">Mar</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-right text-white font-semibold">Apr</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-right text-white font-semibold">May</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-right text-white font-semibold">Jun</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-right text-white font-semibold">Jul</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-right text-white font-semibold">Aug</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-right text-white font-semibold">Sep</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-right text-white font-semibold">Oct</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-right text-white font-semibold">Nov</th>
                                    <th class="px-3.5 py-2.5 border-r border-white/20 text-right text-white font-semibold">Dec</th>
                                    <th class="px-3.5 py-2.5 text-right font-bold text-white bg-[#25276d]">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                                <template x-if="sectionData.length === 0">
                                    <tr>
                                        <td colspan="16" class="px-4 py-8 text-center text-gray-400 font-sans">Tidak ada rincian untuk bagian ini.</td>
                                    </tr>
                                </template>
                                <template x-for="(row, i) in sectionData" :key="i">
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 font-medium" x-text="row.account"></td>
                                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans" x-text="row.account_desc"></td>
                                        <td class="px-3.5 py-2.5 border-r border-gray-200 dark:border-gray-800 font-sans" x-text="row.cost_center"></td>
                                        <td class="px-3.5 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m1)"></td>
                                        <td class="px-3.5 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m2)"></td>
                                        <td class="px-3.5 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m3)"></td>
                                        <td class="px-3.5 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m4)"></td>
                                        <td class="px-3.5 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m5)"></td>
                                        <td class="px-3.5 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m6)"></td>
                                        <td class="px-3.5 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m7)"></td>
                                        <td class="px-3.5 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m8)"></td>
                                        <td class="px-3.5 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m9)"></td>
                                        <td class="px-3.5 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m10)"></td>
                                        <td class="px-3.5 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m11)"></td>
                                        <td class="px-3.5 py-2.5 text-right border-r border-gray-200 dark:border-gray-800" x-text="fmt(row.m12)"></td>
                                        <td class="px-3.5 py-2.5 text-right font-bold text-gray-900 dark:text-white bg-[#2F3185]/5 dark:bg-[#2F3185]/10" x-text="fmt(row.total)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            <div class="px-6 py-3 bg-gray-50/50 dark:bg-gray-800/40 border-t border-gray-200 dark:border-gray-800 flex justify-end">
                <button type="button" @click="sectionModalOpen = false" class="px-5 py-2.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    </template>

</div>
<?= $this->endSection() ?>
