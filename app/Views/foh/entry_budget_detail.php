<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="fohDetailEntry()" x-init="init()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

  <!-- HEADER -->
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <a href="<?= base_url('foh/entry-budget') ?>" class="hover:text-brand-500 transition-colors">FOH</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span>Entry Budget</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-brand-500 font-bold" x-text="headerName"></span>
      </div>
      <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400 shadow-xs">
          <i class="fa-solid fa-table-cells-large text-base"></i>
        </span>
        <span x-text="headerName"></span>
      </h1>
      <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5" x-text="deptLabel"></p>
    </div>
    <div class="flex items-center gap-3">
      <a href="<?= base_url('foh/entry-budget') ?>" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-xs transition-all">
        <i class="fa-solid fa-arrow-left text-[11px]"></i> Kembali
      </a>
      <div class="flex items-center gap-2 rounded-xl border border-gray-200/80 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-xs dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <i class="fa-solid fa-calendar-days text-brand-500"></i>
        <span>Tahun Anggaran :</span>
        <span class="text-brand-600 dark:text-brand-400 font-bold"><?= esc($workingYear) ?></span>
      </div>
    </div>
  </div>

  <!-- TABLE: Matrix Budget + Actual -->
  <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse min-w-[1200px] text-gray-600 dark:text-gray-300">
        <thead class="bg-brand-500 text-white text-xs font-semibold">
          <tr class="bg-brand-500 text-white border-b border-brand-600 font-semibold text-xs">
            <th rowspan="2" class="py-3 px-4 text-center w-16 border-r border-white/20 text-white font-semibold">Detail</th>
            <th rowspan="2" class="py-3 px-4 min-w-[240px] border-r border-white/20 text-white font-semibold">Main Account</th>
            <th colspan="12" class="py-2 px-4 text-center bg-sky-700/60 text-white border-r border-white/20 font-semibold">Budget (Dalam Jutaan)</th>
            <th colspan="12" class="py-2 px-4 text-center bg-emerald-700/60 text-white font-semibold">Actual (Dalam Jutaan)</th>
          </tr>
          <tr class="bg-brand-600 text-white border-b border-brand-600 text-[10px] font-semibold">
            <template x-for="m in months" :key="m">
              <th class="py-2 px-2 text-right border-r border-white/20 text-white font-semibold" x-text="m"></th>
            </template>
            <template x-for="m in months" :key="'act_'+m">
              <th class="py-2 px-2 text-right border-r border-white/20 text-white font-semibold" x-text="m"></th>
            </template>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
          <template x-if="matrixRows.length === 0 && !loading">
            <tr>
              <td colspan="26" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                    <i class="fa-solid fa-table-cells-large text-xl"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Belum Ada Data Sub-Account</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ditemukan rincian sub-account pada header ini.</p>
                </div>
              </td>
            </tr>
          </template>
          <template x-if="loading">
            <tr>
              <td colspan="26" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-brand-500">
                    <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Memuat Data Matriks...</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Mohon tunggu sebentar, sistem sedang memproses rincian FOH.</p>
                </div>
              </td>
            </tr>
          </template>
          <template x-for="(row, idx) in matrixRows" :key="idx">
            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
              <td class="py-2.5 px-4 text-center border-r border-gray-100 dark:border-gray-800">
                <button @click="openModalDetail(row, idx)" title="Edit Detail Item" class="h-8 w-8 rounded-lg inline-flex items-center justify-center bg-brand-500 text-white shadow-2xs hover:bg-brand-600 active:scale-[0.98] transition-all">
                  <i class="fa-solid fa-pen-to-square text-xs"></i>
                </button>
              </td>
              <td class="py-2.5 px-4 font-medium text-gray-800 dark:text-gray-200 border-r border-gray-100 dark:border-gray-800" x-text="row.acct_code + ' - ' + row.coa_name"></td>
              <template x-for="m in 12" :key="m">
                <td class="py-2.5 px-2 text-right font-mono border-r border-gray-100 dark:border-gray-800" x-text="formatNumber(row.budget['b'+m] || 0)"></td>
              </template>
              <template x-for="m in 12" :key="'act_'+m">
                <td class="py-2.5 px-2 text-right font-mono border-r border-gray-100 dark:border-gray-800"
                    :class="hasActual(row) ? 'text-gray-700 dark:text-gray-300' : 'text-blue-500 dark:text-blue-400 italic'"
                    x-text="formatNumber(getActualValue(row, m))"></td>
              </template>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>

  <!-- MODAL: Detail Breakdown -->
  <div x-show="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
    <div @click.outside="isModalOpen = false" x-transition class="w-full max-w-5xl rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800 space-y-4">

      <div class="flex items-center justify-between border-b pb-3 dark:border-gray-700">
        <h3 class="text-base font-bold text-brand-500 dark:text-brand-400">
          <i class="fa-solid fa-pen-to-square mr-1"></i>
          Detail Entry Budget: <span x-text="activeRow ? activeRow.coa_name : ''"></span>
        </h3>
        <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <div class="rounded-lg border border-blue-200 bg-blue-50/70 p-3 text-xs text-blue-700 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300">
        <i class="fa-solid fa-lightbulb mr-1"></i>
        <strong>Tip:</strong> copy 13 kolom (Detail Item + 12 bulan) dari Excel lalu paste di salah satu kolom untuk mengisi otomatis satu baris atau lebih.
      </div>

      <div class="overflow-x-auto max-h-96 rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-left text-xs">
          <thead class="bg-brand-500 text-white font-semibold text-xs border-b border-brand-600">
            <tr class="bg-brand-500 text-white font-semibold">
              <th class="px-3 py-2 border-r border-white/20 min-w-[180px] text-white">Detail Item</th>
              <template x-for="m in months" :key="m">
                <th class="px-2 py-2 border-r border-white/20 text-center min-w-[75px] text-white" x-text="m"></th>
              </template>
              <th class="px-2 py-2 text-center w-10 text-white">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <template x-for="(item, i) in detailItems" :key="i">
              <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                <td class="p-1.5">
                  <input type="text" x-model="item.name" placeholder="Nama Detail Item..." class="w-full rounded border border-gray-300 px-2 py-1 text-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </td>
                <template x-for="m in monthKeys" :key="m">
                  <td class="p-1.5">
                    <input type="number" step="0.01"
                      @paste="handlePaste($event, i, m)"
                      x-model.number="item[m]"
                      class="w-full text-right rounded border border-gray-300 px-1 py-1 text-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono">
                  </td>
                </template>
                <td class="p-1.5 text-center">
                  <button @click="removeItemRow(i)" class="text-red-500 hover:text-red-700 p-1 transition-colors" title="Hapus Baris">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                  </button>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <div class="flex items-center justify-between pt-2">
        <button @click="addItemRow()" class="inline-flex items-center gap-1 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 hover:bg-green-100 transition-colors dark:bg-green-950/40 dark:text-green-400">
          <i class="fa-solid fa-plus"></i> Tambah Item
        </button>
        <div class="flex gap-2">
          <button @click="isModalOpen = false" class="rounded-lg border border-gray-300 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Batal</button>
          <button @click="saveDetailItems()" class="rounded-lg bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-semibold text-white shadow transition-colors">
            <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Detail
          </button>
        </div>
      </div>

    </div>
  </div>

</div>

<script>
function fohDetailEntry() {
  const params = new URLSearchParams(window.location.search);
  return {
    headerAccount: params.get('header') || '',
    dept: params.get('dept') || '',
    idx: params.get('idx') || '1',
    headerName: '',
    deptLabel: '',
    months: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
    monthKeys: ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'],
    matrixRows: [],
    loading: false,
    isModalOpen: false,
    activeRow: null,
    activeRowIndex: -1,
    detailItems: [],

    async init() {
      // Load cost center label
      try {
        const ccRes = await fetch('<?= base_url('foh/getEntryData') ?>?dept=<?= esc($dept) ?>', {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        // We just need the header name
        this.headerName = decodeURIComponent(this.headerAccount);
        this.deptLabel = this.dept;
      } catch(e) {}

      await this.loadMatrix();
    },

    async loadMatrix() {
      if (!this.headerAccount || !this.dept) return;
      this.loading = true;
      try {
        const res = await fetch(`<?= base_url('foh/getDetailMatrix') ?>?dept=${encodeURIComponent(this.dept)}&header=${encodeURIComponent(this.headerAccount)}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        this.matrixRows = data.matrix || [];
      } catch (e) {
        console.error('Gagal memuat matriks:', e);
        this.matrixRows = [];
      } finally {
        this.loading = false;
      }
    },

    openModalDetail(row, idx) {
      this.activeRow = row;
      this.activeRowIndex = idx;
      // Reset dulu agar tidak menampilkan data baris sebelumnya saat fetch
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
        const res = await fetch(`<?= base_url('foh/getDetailItems') ?>?entry_data_id=${entryDataId}`, {
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

    /**
     * Handle paste dari Excel: 13 kolom (Detail Item + 12 Bulan).
     * Bisa paste beberapa baris sekaligus. Kolom bulan dipetakan ke
     * nama bulan (jan..dec) sesuai kolom tabel entry_detail.
     */
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
        window.showToast('error', 'Tidak ada item untuk disimpan.');
        return;
      }

      try {
        const res = await window.ypFetch('<?= base_url('foh/saveDetailItems') ?>', {
          entry_data_id: entryDataId,
          id_coa: this.activeRow?.main_account || 0,
          dept: this.dept,
          items: JSON.stringify(validItems)
        });

        if (res.status === 'success') {
          window.showToast('success', res.message || 'Detail item berhasil disimpan!');
          this.isModalOpen = false;
          // Refresh matriks agar entry_data_id parent terbaru tampil
          this.loadMatrix();
        } else {
          window.showToast('error', res.message || 'Gagal menyimpan detail item.');
        }
      } catch (e) {
        console.error('Error saving:', e);
        window.showToast('error', 'Gagal menyimpan detail item.');
      }
    },

    formatNumber(val) {
      return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(val || 0);
    },

    hasActual(row) {
      // Check if actual data from yp_plan__trans_budget_actual has any non-zero value
      for (let m = 1; m <= 12; m++) {
        if (parseFloat(row.actual['a'+m]) !== 0) return true;
      }
      return false;
    },

    getActualValue(row, m) {
      // Use actual if available, otherwise use simulated from entry_detail
      if (this.hasActual(row)) {
        return row.actual['a'+m] || 0;
      }
      return row.simulated ? (row.simulated['a'+m] || 0) : 0;
    }
  }
}
</script>

<?= $this->endSection() ?>
