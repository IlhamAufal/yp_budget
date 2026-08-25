<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="opexGaDetailApp()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <a href="<?= base_url('opex-ga/entry-budget') ?>" class="hover:text-[#2F3185] transition-colors">Entry Budget</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold" x-text="headerName"></span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Entry Detail Budget Item
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="'Cost Center: ' + costCenterCode"></p>
        </div>
        <a href="<?= base_url('opex-ga/entry-budget') ?>" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-xs transition-all active:scale-[0.98]">
            <i class="fa-solid fa-arrow-left text-[11px]"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-xs dark:border-gray-800 dark:bg-gray-900 flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Main Account / Description:</span>
            <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white mt-0.5" x-text="headerName"></h3>
        </div>
        <div>
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Cost Center:</span>
            <p class="text-sm font-bold text-[#2F3185] dark:text-indigo-400 mt-0.5 font-mono" x-text="costCenterCode"></p>
        </div>
    </div>

    <div class="space-y-4">
        <!-- Table Section Label (Separated from table container) -->
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Rincian Akun OPEX GA (Actual)</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar rincian biaya actual sebelum pengisian alokasi budget.</p>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300 border-collapse min-w-[1000px] whitespace-nowrap">
                    <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
                        <tr class="border-b border-white/20 bg-[#2F3185] text-white font-semibold">
                            <th rowspan="2" class="border-r border-white/20 px-4 py-3 font-semibold text-white text-center w-20">Entry</th>
                            <th rowspan="2" class="border-r border-white/20 px-4 py-3 font-semibold text-white min-w-[260px]">General Administrative Expense</th>
                            <th colspan="8" class="border-r border-white/20 px-2 py-2 font-semibold text-white text-center bg-[#25276d]">Actual</th>
                            <th rowspan="2" class="px-4 py-3 font-semibold text-white text-right w-32 bg-[#25276d] font-bold">Total</th>
                        </tr>
                        <tr class="border-b border-white/20 bg-[#25276d] text-white text-xs font-semibold">
                            <template x-for="m in actualMonths" :key="m">
                                <th class="border-r border-white/20 px-2.5 py-2 text-right text-white font-semibold" x-text="m"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs font-mono">
                        <template x-if="matrixRows.length === 0 && !loading">
                            <tr>
                                <td colspan="11" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                                            <i class="fa-solid fa-coins text-xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak Ada Rincian Akun</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ditemukan sub-akun dalam kelompok header ini.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="loading">
                            <tr>
                                <td colspan="11" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500 font-sans">
                                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                                            <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data...</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sedang mengambil data rincian.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-for="(row, idx) in paginatedRows" :key="idx">
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="border-r border-gray-200 dark:border-gray-800 px-4 py-2.5 text-center font-sans">
                                    <button @click="openModalDetail(row, idx)" title="Entry Detail Item"
                                        class="h-7 w-7 rounded-lg inline-flex items-center justify-center bg-[#2F3185] hover:bg-[#25276d] text-white shadow-xs active:scale-[0.98] transition-all">
                                        <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                    </button>
                                </td>
                                <td class="border-r border-gray-200 dark:border-gray-800 px-4 py-2.5 font-sans font-medium text-gray-900 dark:text-white" x-text="row.coa_name"></td>
                                <template x-for="m in 8" :key="m">
                                    <td class="border-r border-gray-200 dark:border-gray-800 px-2.5 py-2 text-right font-mono" x-text="formatNumber(getActualValue(row, m))"></td>
                                </template>
                                <td class="px-4 py-2.5 text-right font-bold text-gray-900 dark:text-white font-mono bg-gray-50/70 dark:bg-gray-800/50" x-text="formatNumber(getActualTotal(row))"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <template x-if="matrixRows.length > 0">
                            <tr class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-700 text-xs font-mono">
                                <td colspan="2" class="border-r border-gray-300 dark:border-gray-700 px-4 py-3 font-sans font-bold">Grand Total</td>
                                <template x-for="m in 8" :key="'ft_' + m">
                                    <td class="border-r border-gray-300 dark:border-gray-700 px-2.5 py-3 text-right font-mono" x-text="formatNumber(columnActualTotal(m))"></td>
                                </template>
                                <td class="px-4 py-3 text-right text-[#2F3185] dark:text-indigo-400 font-bold" x-text="formatNumber(allActualTotal())"></td>
                            </tr>
                        </template>
                    </tfoot>
                </table>
            </div>

            <template x-if="matrixRows.length > 0">
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 text-xs text-gray-500 dark:text-gray-400">
                    <span>
                        Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200" x-text="((currentPage - 1) * perPage) + 1"></span> -
                        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, totalRows)"></span> dari
                        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalRows"></span> data
                    </span>
                    <div class="flex items-center gap-1" x-show="totalPages > 1">
                        <button type="button" @click="goPage(currentPage - 1)" :disabled="currentPage === 1"
                            class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        </button>
                        <template x-for="(p, pi) in pageNumbers()" :key="'pg_' + pi">
                            <span x-show="p === '...'" class="px-1.5 text-gray-400">&hellip;</span>
                            <button x-show="p !== '...'" type="button" @click="goPage(p)"
                                :class="currentPage === p ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                class="h-8 min-w-[32px] px-2 rounded-lg text-xs font-semibold transition flex items-center justify-center" x-text="p"></button>
                        </template>
                        <button type="button" @click="goPage(currentPage + 1)" :disabled="currentPage === totalPages"
                            class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- MODAL: Detail Breakdown (di-teleport ke body agar tidak terpotong wrapper overflow) -->
    <template x-teleport="body">
    <div x-show="isModalOpen" class="fixed inset-0 z-[999999] flex items-center justify-center bg-black/65 backdrop-blur-xs p-4" x-cloak>
        <div @click.outside="isModalOpen = false" x-transition class="w-full max-w-5xl rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 space-y-4">

            <div class="flex items-center justify-between border-b pb-3 dark:border-gray-800">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        Detail Entry Budget: <span class="text-[#2F3185] dark:text-indigo-400" x-text="activeRow ? activeRow.coa_name : ''"></span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Penetapan detail item dan alokasi bulanan.</p>
                </div>
                <button @click="isModalOpen = false" class="h-8 w-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="rounded-2xl border border-blue-200 bg-blue-50/70 p-3.5 text-xs text-blue-800 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300">
                <i class="fa-solid fa-lightbulb mr-1 text-blue-600"></i>
                <strong>Tip:</strong> Copy 13 kolom (Detail Item + 12 bulan) dari Excel lalu paste di salah satu kolom untuk mengisi otomatis satu baris atau lebih.
            </div>

            <div class="overflow-x-auto max-h-96 rounded-2xl border border-gray-200/80 dark:border-gray-800 overflow-hidden shadow-xs">
                <table class="w-full text-left text-xs border-collapse min-w-[1100px]">
                    <thead class="bg-[#2F3185] text-white font-semibold text-xs border-b border-white/20">
                        <tr class="bg-[#2F3185] text-white font-semibold">
                            <th class="px-3.5 py-3 border-r border-white/20 min-w-[200px] text-white font-semibold">Detail Item</th>
                            <template x-for="m in months" :key="m">
                                <th class="px-2 py-3 border-r border-white/20 text-center min-w-[75px] text-white font-semibold" x-text="m"></th>
                            </template>
                            <th class="px-2 py-3 text-center w-12 text-white font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                        <template x-for="(item, i) in detailItems" :key="i">
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40">
                                <td class="p-2 border-r border-gray-200 dark:border-gray-800 font-sans">
                                    <input type="text" x-model="item.name" placeholder="Nama Detail Item..." class="w-full rounded-xl border border-gray-300 dark:border-gray-700 px-3 py-1.5 text-xs focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:outline-none dark:bg-gray-800 dark:text-white">
                                </td>
                                <template x-for="m in monthKeys" :key="m">
                                    <td class="p-2 border-r border-gray-200 dark:border-gray-800">
                                        <input type="number" step="0.01"
                                            @paste="handlePaste($event, i, m)"
                                            x-model.number="item[m]"
                                            class="w-full text-right rounded-xl border border-gray-300 dark:border-gray-700 px-2 py-1.5 text-xs focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:outline-none dark:bg-gray-800 dark:text-white font-mono">
                                    </td>
                                </template>
                                <td class="p-2 text-center">
                                    <button @click="removeItemRow(i)" class="text-rose-500 hover:text-rose-700 p-1 transition-colors" title="Hapus Baris">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between pt-2">
                <button @click="addItemRow()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition-all dark:bg-emerald-950/40 dark:text-emerald-400 active:scale-[0.98]">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Item</span>
                </button>
                <div class="flex gap-2">
                    <button @click="isModalOpen = false" class="rounded-xl border border-gray-300 dark:border-gray-700 px-5 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Batal</button>
                    <button @click="saveDetailItems()" class="rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98]">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Detail</span>
                    </button>
                </div>
            </div>        </div>
    </div>
    </template>

</div>

<script>
function opexGaDetailApp() {
    const params = new URLSearchParams(window.location.search);
    return {
        headerAccount: params.get('header') || '',
        costCenterCode: params.get('dept') || '',
        idx: params.get('idx') || '1',
        headerName: params.get('header') || '',

        months: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
        monthKeys: ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'],
        actualMonths: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG'],

        matrixRows: [],
        loading: false,
        isModalOpen: false,
        activeRow: null,
        activeRowIndex: -1,
        detailItems: [],

        async init() {
            await this.loadMatrix();
        },

        async loadMatrix() {
            if (!this.headerAccount || !this.costCenterCode) return;
            this.loading = true;
            try {
                const res = await fetch(`<?= base_url('opex-ga/getDetailMatrix') ?>?dept=${encodeURIComponent(this.costCenterCode)}&header=${encodeURIComponent(this.headerAccount)}&idx=${encodeURIComponent(this.idx)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.matrixRows = data.matrix || [];
                this.currentPage = 1;

                // Gunakan nama sub-account pertama sebagai judul header bila ada
                if (this.matrixRows.length > 0 && this.matrixRows[0].coa_name) {
                    this.headerName = this.matrixRows[0].coa_name;
                }
            } catch (e) {
                console.error('Gagal memuat matriks:', e);
                this.matrixRows = [];
            } finally {
                this.loading = false;
            }
        },

        getActualValue(row, m) {
            return row.actual ? (row.actual['a' + m] || 0) : 0;
        },

        getActualTotal(row) {
            let sum = 0;
            for (let i = 1; i <= 8; i++) {
                sum += parseFloat(this.getActualValue(row, i)) || 0;
            }
            return sum;
        },

        columnActualTotal(m) {
            return this.matrixRows.reduce((sum, row) => sum + (parseFloat(this.getActualValue(row, m)) || 0), 0);
        },

        allActualTotal() {
            return this.matrixRows.reduce((sum, row) => sum + this.getActualTotal(row), 0);
        },

        // Pagination
        currentPage: 1,
        perPage: 10,
        get totalRows() { return this.matrixRows.length; },
        get totalPages() { return Math.ceil(this.totalRows / this.perPage) || 1; },
        get paginatedRows() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.matrixRows.slice(start, start + this.perPage);
        },
        goPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
        },
        pageNumbers() {
            const pages = [];
            const total = this.totalPages;
            const current = this.currentPage;
            if (total <= 7) {
                for (let i = 1; i <= total; i++) pages.push(i);
            } else {
                pages.push(1);
                if (current > 3) pages.push('...');
                const start = Math.max(2, current - 1);
                const end = Math.min(total - 1, current + 1);
                for (let i = start; i <= end; i++) pages.push(i);
                if (current < total - 2) pages.push('...');
                pages.push(total);
            }
            return pages;
        },

        openModalDetail(row, idx) {
            this.activeRow = row;
            this.activeRowIndex = idx;
            this.detailItems = [this.newItemRow()];

            // Load existing detail items dari DB. Bila parent budget belum ada,
            // modal tetap terbuka — parent akan dibuat otomatis saat Simpan Detail.
            this.loadDetailItems(row);

            this.isModalOpen = true;
        },

        async loadDetailItems(row) {
            const entryDataId = row.entry_data_id;

            if (!entryDataId) {
                this.detailItems = [this.newItemRow()];
                return;
            }

            try {
                const res = await fetch(`<?= base_url('opex-ga/getDetailItems') ?>?entry_data_id=${entryDataId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                // Abaikan response lama bila user sudah membuka baris lain
                if (this.activeRow?.entry_data_id !== entryDataId) return;

                const items = (data.items || []).map(it => {
                    const mapped = { name: it.nama_barang || '' };
                    this.monthKeys.forEach(k => { mapped[k] = Number(it[k] || 0); });
                    return mapped;
                });

                this.detailItems = items.length > 0 ? items : [this.newItemRow()];
            } catch (e) {
                console.error('Gagal memuat detail items:', e);
                this.detailItems = [this.newItemRow()];
            }
        },

        newItemRow() {
            const row = { name: '' };
            this.monthKeys.forEach(k => { row[k] = 0; });
            return row;
        },

        addItemRow() {
            this.detailItems.push(this.newItemRow());
        },

        removeItemRow(i) {
            if (this.detailItems.length > 1) {
                this.detailItems.splice(i, 1);
            }
        },

        handlePaste(event, itemIdx, monthKey) {
            event.preventDefault();
            const clipboardData = event.clipboardData || window.clipboardData;
            const pastedText = clipboardData.getData('text');

            if (!pastedText) return;

            const lines = pastedText.split('\n').filter(l => l.trim() !== '');

            if (lines.length === 0) return;

            // Parse baris pertama (data dari cell yang di-klik)
            const firstLine = lines[0];
            const firstCols = firstLine.split('\t');

            // Jika hanya 1 kolom (tanpa tab), isi cell biasa
            if (firstCols.length <= 1) {
                this.detailItems[itemIdx][monthKey] = parseFloat(firstCols[0]) || 0;
                return;
            }

            // Multi-column paste: isi dari baris yang sesuai
            // Kolom pertama = nama item, kolom 2-13 = 12 bulan (Jan..Dec)
            for (let lineIdx = 0; lineIdx < lines.length; lineIdx++) {
                const cols = lines[lineIdx].split('\t');
                if (cols.length < 2) continue;

                const targetIdx = itemIdx + lineIdx;

                // Tambah baris baru jika perlu
                while (targetIdx >= this.detailItems.length) {
                    this.addItemRow();
                }

                // Kolom 0 = nama item
                if (cols[0] && cols[0].trim()) {
                    this.detailItems[targetIdx].name = cols[0].trim();
                }

                // Kolom 1-12 = 12 bulan
                for (let c = 1; c <= 12 && c < cols.length; c++) {
                    const val = parseFloat(cols[c].replace(/,/g, '')) || 0;
                    this.detailItems[targetIdx][this.monthKeys[c - 1]] = val;
                }
            }
        },

        async saveDetailItems() {
            const entryDataId = this.activeRow?.entry_data_id || 0;
            const idCoa = this.activeRow?.main_account || 0;

            // Filter items yang memiliki nama, lalu petakan ke format DB
            // (nama_barang + bulan jan..dec)
            const validItems = this.detailItems
                .filter(item => item.name && item.name.trim())
                .map(item => {
                    const payload = { nama_barang: item.name.trim() };
                    this.monthKeys.forEach(k => { payload[k] = Number(item[k]) || 0; });
                    return payload;
                });

            if (validItems.length === 0) {
                if (window.showToast) {
                    window.showToast('error', 'Tidak ada item untuk disimpan.');
                } else {
                    alert('Tidak ada item untuk disimpan.');
                }
                return;
            }

            try {
                const res = await window.ypFetch('<?= base_url('opex-ga/saveDetailItems') ?>', {
                    entry_data_id: entryDataId,
                    id_coa: idCoa,
                    dept: this.costCenterCode,
                    header: this.headerAccount,
                    idx: this.idx,
                    items: JSON.stringify(validItems)
                });

                if (res.status === 'success') {
                    if (window.showToast) {
                        window.showToast('success', res.message || 'Detail item berhasil disimpan!');
                    } else {
                        alert(res.message || 'Detail item berhasil disimpan!');
                    }
                    this.isModalOpen = false;
                    // Refresh matriks agar entry_data_id parent terbaru tampil
                    this.loadMatrix();
                } else {
                    if (window.showToast) {
                        window.showToast('error', res.message || 'Gagal menyimpan detail item.');
                    } else {
                        alert(res.message || 'Gagal menyimpan detail item.');
                    }
                }
            } catch (e) {
                console.error('Error saving:', e);
                if (window.showToast) {
                    window.showToast('error', 'Gagal menyimpan detail item.');
                } else {
                    alert('Gagal menyimpan detail item.');
                }
            }
        },

        formatNumber(val) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0);
        }
    }
}
</script>
<?= $this->endSection() ?>
