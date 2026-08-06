<?php
/* =====================================================================
 * partials/breakdown_modal.php — REUSABLE MODAL SUB-DETAIL COA (PRD 1.5)
 *
 * Modal Alpine.js untuk save/delete breakdown item (yp_plan__trans_budget_entry_detail)
 * via AJAX partial update TANPA refresh halaman. Dipakai bersama oleh
 * modul FOH, OPEX GA, dan OPEX Selling agar konsisten.
 *
 * Cara pakai di view pemanggil:
 *   1. include partial ini di dalam root Alpine component halaman:
 *        <?= $this->include('partials/breakdown_modal', [
 *            'bmListUrl'   => base_url('foh/getDetailItems'),
 *            'bmSaveUrl'   => base_url('foh/saveDetail'),
 *            'bmDeleteUrl' => base_url('foh/deleteDetail'),
 *        ]) ?>
 *   2. Spread komponen Alpine ke page component:
 *        return { ...breakdownModalComponent(), rows: [], ... }
 *   3. Buka modal dari baris mana pun:
 *        @click="openDetail(row.id)"
 * ===================================================================== */
$bmListUrl   = $bmListUrl ?? '';
$bmSaveUrl   = $bmSaveUrl ?? '';
$bmDeleteUrl = $bmDeleteUrl ?? '';
?>

<!-- ================================================================ -->
<!-- MODAL: BREAKDOWN DETAIL ITEM (SUB-DETAIL COA)                    -->
<!-- Diletakkan di bagian bawah DOM (di dalam root Alpine component)  -->
<!-- dengan z-index tinggi & backdrop blur (standar 1.2).             -->
<!-- ================================================================ -->
<div
  x-show="detailModal.open"
  x-transition:enter="transition ease-out duration-250"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  class="fixed inset-0 flex items-center justify-center p-4"
  style="z-index: 9999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
  @click.self="closeDetail()"
  x-cloak
>
  <div
    x-show="detailModal.open"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95 translate-y-3"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
    x-transition:leave-end="opacity-0 scale-95 translate-y-3"
    class="relative w-full max-w-4xl rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden"
  >
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
      <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400">
          <i class="fa-solid fa-list-ul text-lg"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">Breakdown Detail Item</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">Rincian sub-detail COA — simpan/hapus tanpa refresh halaman</p>
        </div>
      </div>
      <button type="button" @click="closeDetail()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>

    <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
      <!-- Daftar item existing -->
      <div>
        <div class="flex items-center justify-between mb-3">
          <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            Item Tersimpan (<span x-text="detailModal.items.length"></span>)
          </h4>
          <span x-show="detailModal.loading" class="text-xs text-gray-400"><i class="fa-solid fa-spinner fa-spin"></i> Memuat...</span>
        </div>

        <template x-if="detailModal.items.length === 0 && !detailModal.loading">
          <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-8 text-center text-sm text-gray-400 dark:text-gray-500">
            Belum ada item breakdown. Tambahkan melalui formulir di bawah.
          </div>
        </template>

        <div class="overflow-x-auto" x-show="detailModal.items.length > 0">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="border-b border-gray-200 dark:border-gray-800 text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wide">
                <th class="py-3 px-3">Nama Barang / Item</th>
                <th class="py-3 px-2 text-right" x-for="k in MONTH_KEYS" x-text="k.toUpperCase()" :key="k"></th>
                <th class="py-3 px-3 text-right">Total</th>
                <th class="py-3 px-3 text-center w-12">#</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
              <template x-for="item in detailModal.items" :key="item.id">
                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40">
                  <td class="py-2.5 px-3 font-semibold text-gray-800 dark:text-gray-100" x-text="item.nama_barang"></td>
                  <td class="py-2.5 px-2 text-right text-gray-600 dark:text-gray-300" x-for="k in MONTH_KEYS" :key="k" x-text="Number(item[k] || 0).toLocaleString('id-ID', {maximumFractionDigits:0})"></td>
                  <td class="py-2.5 px-3 text-right font-bold text-brand-500 dark:text-brand-400" x-text="Number(item.total || 0).toLocaleString('id-ID', {maximumFractionDigits:0})"></td>
                  <td class="py-2.5 px-3 text-center">
                    <button type="button" @click="deleteDetailItem(item.id)" class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors" title="Hapus item">
                      <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Form tambah item -->
      <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30 p-5">
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4 flex items-center gap-2">
          <i class="fa-solid fa-circle-plus text-brand-500"></i> Tambah Item Baru
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="md:col-span-1">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Nama Barang / Item <span class="text-red-500">*</span></label>
            <input
              type="text"
              x-model="detailModal.form.nama_barang"
              placeholder="Contoh: Bahan pembantu produksi"
              class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-colors"
            />
          </div>

          <div class="md:col-span-1">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Total Otomatis</label>
            <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 px-4 py-2.5 text-sm font-bold text-brand-500 dark:text-brand-400" x-text="detailItemTotal().toLocaleString('id-ID', {maximumFractionDigits:0})"></div>
          </div>

          <div class="md:col-span-1">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">&nbsp;</label>
            <button
              type="button"
              @click="saveDetailItem()"
              :disabled="detailModal.saving"
              class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-brand-600 disabled:opacity-50 transition-colors"
            >
              <i class="fa-solid fa-spinner fa-spin" x-show="detailModal.saving"></i>
              <span x-text="detailModal.saving ? 'Menyimpan...' : 'Simpan Item'"></span>
            </button>
          </div>
        </div>

        <!-- Input bulanan -->
        <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
          <template x-for="k in MONTH_KEYS" :key="k">
            <div>
              <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1" x-text="k.toUpperCase()"></label>
              <input type="number" min="0" x-model="detailModal.form[k]" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1.5 text-xs text-right text-gray-900 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-colors" />
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-800 flex justify-end">
      <button type="button" @click="closeDetail()" class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
        Tutup
      </button>
    </div>
  </div>
</div>

<script>
  /**
   * breakdownModalComponent(endpoints) — state & methods Alpine untuk modal
   * breakdown sub-detail COA. Dipakai via spread di page component:
   *   return { ...breakdownModalComponent({ list, save, delete }), ... }
   */
  function breakdownModalComponent(endpoints) {
    const MONTH_KEYS = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

    function emptyForm() {
      const f = { nama_barang: '' };
      MONTH_KEYS.forEach(k => { f[k] = 0; });
      return f;
    }

    return {
      MONTH_KEYS,

      detailModal: {
        open: false,
        entryDataId: null,
        items: [],
        loading: false,
        saving: false,
        form: emptyForm(),
      },

      openDetail(entryDataId) {
        this.detailModal.entryDataId = entryDataId;
        this.detailModal.form = emptyForm();
        this.detailModal.open = true;
        this.loadDetailItems();
      },

      closeDetail() {
        this.detailModal.open = false;
      },

      async loadDetailItems() {
        if (!this.detailModal.entryDataId) return;
        this.detailModal.loading = true;
        const res = await window.ypFetch(endpoints.list, { entry_data_id: this.detailModal.entryDataId });
        this.detailModal.loading = false;
        this.detailModal.items = res.items || [];
      },

      detailItemTotal() {
        return MONTH_KEYS.reduce((sum, k) => sum + Number(this.detailModal.form[k] || 0), 0);
      },

      async saveDetailItem() {
        if (!this.detailModal.form.nama_barang.trim()) {
          window.showToast('error', 'Nama barang / item wajib diisi.');
          return;
        }

        this.detailModal.saving = true;
        const body = new URLSearchParams({
          entry_data_id: this.detailModal.entryDataId,
          nama_barang: this.detailModal.form.nama_barang,
        });
        MONTH_KEYS.forEach(k => body.append(k, this.detailModal.form[k] || 0));

        const res = await window.ypFetch(endpoints.save, body);
        this.detailModal.saving = false;

        if (res.status === 'success') {
          window.showToast('success', res.message || 'Item berhasil disimpan.');
          this.detailModal.form = emptyForm();
          await this.loadDetailItems();
        } else {
          window.showToast('error', res.message || 'Gagal menyimpan item.');
        }
      },

      async deleteDetailItem(id) {
        if (!window.ypConfirm('Hapus detail item ini?')) return;

        const res = await window.ypFetch(endpoints.delete, { id });
        if (res.status === 'success') {
          window.showToast('success', res.message || 'Item berhasil dihapus.');
          await this.loadDetailItems();
        } else {
          window.showToast('error', res.message || 'Gagal menghapus item.');
        }
      },
    };
  }
</script>
