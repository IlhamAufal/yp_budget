<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="opexGaDetailApp()" x-init="init()" class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <i class="fas fa-coins text-lg text-primary"></i>
                </div>
                <h2 class="text-title-md2 font-bold text-black dark:text-white">Entry Detail Budget Item</h2>
            </div>
            <nav class="mt-1">
                <ol class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    <li><a class="hover:text-primary" href="<?= base_url('dashboard') ?>"><i class="fas fa-home mr-1"></i>Home</a></li>
                    <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                    <li><a class="hover:text-primary" href="<?= base_url('opex-ga/entry-budget') ?>">Entry Budget</a></li>
                    <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                    <li class="text-primary font-semibold" x-text="headerName"></li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('opex-ga/entry-budget') ?>" class="inline-flex items-center gap-2 rounded bg-gray-500 px-4 py-2 text-xs font-semibold text-white hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="text-xs font-medium text-gray-500">Main Account / Description:</span>
            <h3 class="text-lg font-bold text-black dark:text-white" x-text="headerName"></h3>
        </div>
        <div>
            <span class="text-xs font-medium text-gray-500">Cost Center:</span>
            <p class="text-sm font-bold text-primary" x-text="costCenterCode"></p>
        </div>
    </div>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-6">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-left text-xs">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300">
                        <th rowspan="2" class="border-b border-r px-4 py-2.5 font-bold uppercase text-center w-16">ENTRY</th>
                        <th rowspan="2" class="border-b border-r px-4 py-2.5 font-bold uppercase min-w-[240px]">GENERAL ADMINISTRATIVE EXPENSE</th>
                        <th colspan="8" class="border-b border-r px-4 py-2.5 font-bold uppercase text-center">ACTUAL</th>
                        <th rowspan="2" class="border-b px-4 py-2.5 font-bold uppercase text-right w-32">TOTAL</th>
                    </tr>
                    <tr class="bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300">
                        <template x-for="m in actualMonths" :key="m">
                            <th class="border-b border-r px-2 py-2 text-center font-bold uppercase" x-text="m"></th>
                        </template>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="matrixRows.length === 0 && !loading">
                        <tr>
                            <td colspan="11" class="border-b py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox mr-1"></i>No data available in table
                            </td>
                        </tr>
                    </template>
                    <template x-if="loading">
                        <tr>
                            <td colspan="11" class="border-b py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-spinner fa-spin mr-1"></i>Memuat data...
                            </td>
                        </tr>
                    </template>
                    <template x-for="(row, idx) in paginatedRows" :key="idx">
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="border-b border-r px-4 py-2.5 text-center">
                                <button @click="openModalDetail(row, idx)" title="Entry Detail Item"
                                    class="inline-flex items-center justify-center rounded bg-blue-600 p-1.5 text-white shadow hover:bg-blue-700 active:scale-[0.98] transition-all">
                                    <i class="fas fa-edit text-[11px]"></i>
                                </button>
                            </td>
                            <td class="border-b border-r px-4 py-2.5 font-medium" x-text="row.coa_name"></td>
                            <template x-for="m in 8" :key="m">
                                <td class="border-b border-r px-2 py-2 text-right font-mono" x-text="formatNumber(getActualValue(row, m))"></td>
                            </template>
                            <td class="border-b px-4 py-2.5 text-right font-bold" x-text="formatNumber(getActualTotal(row))"></td>
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <template x-if="matrixRows.length > 0">
                        <tr class="bg-gray-100 dark:bg-meta-4 font-bold text-gray-900 dark:text-white">
                            <td colspan="2" class="border-t border-r px-4 py-3 uppercase">TOTAL</td>
                            <template x-for="m in 8" :key="'ft_' + m">
                                <td class="border-t border-r px-2 py-3 text-right font-bold" x-text="formatNumber(columnActualTotal(m))"></td>
                            </template>
                            <td class="border-t px-4 py-3 text-right font-bold" x-text="formatNumber(allActualTotal())"></td>
                        </tr>
                    </template>
                </tfoot>
            </table>
        </div>

        <template x-if="matrixRows.length > 0">
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-stroke dark:border-strokedark pt-4 mt-4 text-xs">
                <span class="text-gray-600 dark:text-gray-400">
                    Showing <span class="font-bold text-gray-800 dark:text-gray-200" x-text="((currentPage - 1) * perPage) + 1"></span> to
                    <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, totalRows)"></span> of
                    <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalRows"></span> entries
                </span>
                <div class="flex items-center gap-1" x-show="totalPages > 1">
                    <button type="button" @click="goPage(currentPage - 1)" :disabled="currentPage === 1"
                        class="h-7 px-2.5 rounded-lg border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-50 transition">
                        <i class="fas fa-chevron-left text-[10px]"></i>
                    </button>
                    <template x-for="(p, pi) in pageNumbers()" :key="'pg_' + pi">
                        <span x-show="p === '...'" class="px-1.5 text-gray-400">&hellip;</span>
                        <button x-show="p !== '...'" type="button" @click="goPage(p)"
                            :class="currentPage === p ? 'bg-primary text-white font-bold' : 'border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition'"
                            class="h-7 min-w-[28px] px-1.5 rounded-lg font-semibold" x-text="p"></button>
                    </template>
                    <button type="button" @click="goPage(currentPage + 1)" :disabled="currentPage === totalPages"
                        class="h-7 px-2.5 rounded-lg border border-stroke dark:border-strokedark bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 font-semibold disabled:opacity-40 hover:bg-gray-50 transition">
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- MODAL: Detail Breakdown (di-teleport ke body agar tidak terpotong wrapper overflow) -->
    <template x-teleport="body">
    <div x-show="isModalOpen" class="fixed inset-0 z-[999999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
        <div @click.outside="isModalOpen = false" x-transition class="w-full max-w-5xl rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800 space-y-4">

            <div class="flex items-center justify-between border-b pb-3 dark:border-gray-700">
                <h3 class="text-base font-bold text-primary dark:text-white">
                    <i class="fas fa-edit mr-1"></i>
                    Detail Entry Budget: <span x-text="activeRow ? activeRow.coa_name : ''"></span>
                </h3>
                <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="rounded-lg border border-blue-200 bg-blue-50/70 p-3 text-xs text-blue-700 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300">
                <i class="fas fa-lightbulb mr-1"></i>
                <strong>Tip:</strong> copy 13 kolom (Detail Item + 12 bulan) dari Excel lalu paste di salah satu kolom untuk mengisi otomatis satu baris atau lebih.
            </div>

            <div class="overflow-x-auto max-h-96 rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-3 py-2 border-b min-w-[180px]">DETAIL ITEM</th>
                            <template x-for="m in months" :key="m">
                                <th class="px-2 py-2 border-b text-center min-w-[75px]" x-text="m"></th>
                            </template>
                            <th class="px-2 py-2 border-b text-center w-10">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="(item, i) in detailItems" :key="i">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="p-1.5">
                                    <input type="text" x-model="item.name" placeholder="Nama Detail Item..." class="w-full rounded border border-gray-300 px-2 py-1 text-xs focus:border-primary focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </td>
                                <template x-for="m in monthKeys" :key="m">
                                    <td class="p-1.5">
                                        <input type="number" step="0.01"
                                            @paste="handlePaste($event, i, m)"
                                            x-model.number="item[m]"
                                            class="w-full text-right rounded border border-gray-300 px-1 py-1 text-xs focus:border-primary focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono">
                                    </td>
                                </template>
                                <td class="p-1.5 text-center">
                                    <button @click="removeItemRow(i)" class="text-red-500 hover:text-red-700 p-1 transition-colors" title="Hapus Baris">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between pt-2">
                <button @click="addItemRow()" class="inline-flex items-center gap-1 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 hover:bg-green-100 transition-colors dark:bg-green-950/40 dark:text-green-400">
                    <i class="fas fa-plus"></i> Tambah Item
                </button>
                <div class="flex gap-2">
                    <button @click="isModalOpen = false" class="rounded-lg border border-gray-300 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Batal</button>
                    <button @click="saveDetailItems()" class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-opacity-90 shadow transition-colors">
                        <i class="fas fa-save mr-1"></i> Simpan Detail
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
