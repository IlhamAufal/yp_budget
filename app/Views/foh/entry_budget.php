<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="fohEntryPage()" class="p-4 md:p-8 mx-auto max-w-(--breakpoint-2xl) space-y-6 md:space-y-8">

  <!-- ============================================================ -->
  <!-- HEADER & ACTIONS                                             -->
  <!-- ============================================================ -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-3">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span>FOH</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-brand-500 font-bold">Entry Budget</span>
      </div>
      <h1 class="text-2xl md:text-3xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-4">
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400">
          <i class="fa-solid fa-industry text-xl"></i>
        </span>
        Entry Budget FOH
      </h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
        Factory Overhead — Tahun Anggaran <span class="font-bold text-brand-500"><?= esc($workingYear) ?></span>
      </p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
      <button
        type="button"
        @click="submitBudget()"
        :disabled="submitting || rows.length === 0"
        class="inline-flex items-center gap-2 rounded-xl border border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 px-5 py-3 text-sm font-bold text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/40 disabled:opacity-50 transition-all"
      >
        <i class="fa-solid fa-paper-plane"></i>
        <span x-text="submitting ? 'Memproses...' : 'Submit Workflow'"></span>
      </button>
      <button
        type="button"
        @click="addRow()"
        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-3 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all"
      >
        <i class="fa-solid fa-plus"></i>
        <span>Tambah Baris</span>
      </button>
      <button
        type="button"
        @click="saveBudget()"
        :disabled="saving || rows.length === 0"
        class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-brand-600 disabled:opacity-50 transition-all"
      >
        <i class="fa-solid fa-floppy-disk"></i>
        <span x-text="saving ? 'Menyimpan...' : 'Simpan Budget'"></span>
      </button>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- FILTER COST CENTER                                           -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-5">
    <div class="flex flex-wrap items-end gap-4">
      <div class="flex-1 min-w-[260px]">
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">
          Cost Center (FOH) <span class="text-red-500">*</span>
        </label>
        <select
          x-model="dept"
          @change="loadData()"
          class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-colors"
        >
          <option value="">— Pilih Cost Center —</option>
          <?php foreach ($costCenters as $cc): ?>
            <option value="<?= esc($cc['cost_center']) ?>"><?= esc($cc['cost_center']) ?> — <?= esc($cc['cost_desc']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 pb-1">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 px-3 py-1 text-xs font-bold">
          <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
          <span x-text="rows.filter(r => r.submit_status === 'SUBMITTED').length + '/' + rows.length"></span> baris di-submit
        </span>
      </div>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- TABLE ENTRY BUDGET                                           -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse min-w-[1100px]">
        <thead>
          <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200/80 dark:border-gray-800 text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <th class="py-4 px-4 w-40">Main Account (COA)</th>
            <th class="py-4 px-4 min-w-[180px]">Deskripsi</th>
            <template x-for="(m, i) in MONTH_LABELS" :key="m">
              <th class="py-4 px-2 text-right" x-text="m"></th>
            </template>
            <th class="py-4 px-4 text-right bg-gray-100/70 dark:bg-gray-800">Total</th>
            <th class="py-4 px-4 text-center w-24">Detail & #</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <template x-if="!dept">
            <tr>
              <td colspan="16" class="py-16 text-center text-gray-400 dark:text-gray-500">
                <i class="fa-solid fa-hand-pointer text-2xl mb-3"></i>
                <p class="font-semibold text-gray-600 dark:text-gray-300">Pilih Cost Center terlebih dahulu</p>
              </td>
            </tr>
          </template>
          <template x-if="dept && rows.length === 0">
            <tr>
              <td colspan="16" class="py-16 text-center text-gray-400 dark:text-gray-500">
                <p class="font-semibold text-gray-600 dark:text-gray-300 mb-1">Belum ada data untuk cost center ini</p>
                <p class="text-sm">Gunakan <strong>+ Tambah Baris</strong> untuk memulai entry budget FOH.</p>
              </td>
            </tr>
          </template>

          <template x-for="(row, idx) in rows" :key="idx">
            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
              <!-- COA -->
              <td class="py-2.5 px-4">
                <select
                  x-model="row.id_coa"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1.5 text-xs font-mono text-gray-900 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-colors"
                >
                  <option value="">— Pilih COA —</option>
                  <?php foreach ($coas as $coa): ?>
                    <option value="<?= (int) $coa['main_account'] ?>"><?= (int) $coa['main_account'] ?> — <?= esc($coa['cost_center_desc']) ?></option>
                  <?php endforeach; ?>
                </select>
              </td>

              <!-- Deskripsi -->
              <td class="py-2.5 px-4">
                <span class="text-gray-800 dark:text-gray-200 font-medium" x-text="row.coa_desc || '(pilih COA)'"></span>
                <span class="block text-[10px] mt-0.5" x-show="row.submit_status">
                  <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold"
                        :class="row.submit_status === 'SUBMITTED' ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'"
                        x-text="row.submit_status"></span>
                </span>
              </td>

              <!-- Bulan -->
              <template x-for="m in 12" :key="'m' + m">
                <td class="py-2.5 px-2">
                  <input
                    type="number"
                    min="0"
                    x-model="row['m' + m]"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1.5 text-xs text-right font-mono text-gray-900 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-colors"
                  />
                </td>
              </template>

              <!-- Total -->
              <td class="py-2.5 px-4 text-right font-bold text-brand-500 dark:text-brand-400 bg-gray-50/70 dark:bg-gray-800/50 whitespace-nowrap" x-text="rowTotal(row).toLocaleString('id-ID')"></td>

              <!-- Detail & Hapus -->
              <td class="py-2.5 px-4">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    type="button"
                    @click="openDetail(row.id)"
                    :disabled="!row.id"
                    class="h-8 w-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 disabled:opacity-30 disabled:cursor-not-allowed transition-colors flex items-center justify-center"
                    title="Breakdown Detail Item"
                  >
                    <i class="fa-solid fa-list-ul text-xs"></i>
                  </button>
                  <button
                    type="button"
                    @click="removeRow(idx)"
                    class="h-8 w-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors flex items-center justify-center"
                    title="Hapus baris"
                  >
                    <i class="fa-solid fa-trash text-xs"></i>
                  </button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
        <tfoot x-show="dept && rows.length > 0">
          <tr class="bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200/80 dark:border-gray-800 font-bold text-gray-700 dark:text-gray-200">
            <td class="py-3 px-4" colspan="2">GRAND TOTAL</td>
            <td class="py-3 px-2 text-right" x-for="m in 12" :key="'ft' + m" x-text="monthlyTotal(m).toLocaleString('id-ID')"></td>
            <td class="py-3 px-4 text-right text-brand-500 dark:text-brand-400 whitespace-nowrap" x-text="grandTotal().toLocaleString('id-ID')"></td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- MODAL BREAKDOWN SUB-DETAIL (reusable partial)                -->
  <!-- ============================================================ -->
  <?= $this->include('partials/breakdown_modal', [
      'bmListUrl'   => base_url('foh/getDetailItems'),
      'bmSaveUrl'   => base_url('foh/saveDetail'),
      'bmDeleteUrl' => base_url('foh/deleteDetail'),
  ]) ?>

</div>

<script>
  function fohEntryPage() {
    return {
      ...breakdownModalComponent({
        list: '<?= base_url('foh/getDetailItems') ?>',
        save: '<?= base_url('foh/saveDetail') ?>',
        delete: '<?= base_url('foh/deleteDetail') ?>',
      }),

      MONTH_LABELS: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],

      dept: '',
      rows: [],
      saving: false,
      submitting: false,
      loading: false,

      async loadData() {
        if (!this.dept) {
          this.rows = [];
          return;
        }
        this.loading = true;
        const res = await window.ypFetch('<?= base_url('foh/getEntryData') ?>?dept=' + encodeURIComponent(this.dept));
        this.loading = false;

        if (res.status !== 'success') {
          window.showToast('error', res.message || 'Gagal memuat data.');
          return;
        }

        this.rows = (res.rows || []).map(r => ({
          id: r.id || null,
          id_coa: r.id_coa ? String(r.id_coa) : '',
          coa_desc: r.coa_desc || '',
          submit_status: r.submit_status || 'DRAFT',
          m1: Number(r.jan || 0), m2: Number(r.feb || 0), m3: Number(r.mar || 0),
          m4: Number(r.apr || 0), m5: Number(r.may || 0), m6: Number(r.jun || 0),
          m7: Number(r.jul || 0), m8: Number(r.aug || 0), m9: Number(r.sep || 0),
          m10: Number(r.oct || 0), m11: Number(r.nov || 0), m12: Number(r.dec || 0),
        }));

        if (this.rows.length === 0) {
          this.addRow();
        }
      },

      addRow() {
        const row = { id: null, id_coa: '', coa_desc: '', submit_status: 'DRAFT' };
        for (let m = 1; m <= 12; m++) row['m' + m] = 0;
        this.rows.push(row);
      },

      removeRow(idx) {
        if (this.rows.length <= 1) {
          window.showToast('info', 'Minimal satu baris data.');
          return;
        }
        this.rows.splice(idx, 1);
      },

      rowTotal(row) {
        let t = 0;
        for (let m = 1; m <= 12; m++) t += Number(row['m' + m] || 0);
        return t;
      },

      monthlyTotal(m) {
        return this.rows.reduce((s, r) => s + Number(r['m' + m] || 0), 0);
      },

      grandTotal() {
        return this.rows.reduce((s, r) => {
          for (let m = 1; m <= 12; m++) s += Number(r['m' + m] || 0);
          return s;
        }, 0);
      },

      async saveBudget() {
        if (!this.dept) {
          window.showToast('error', 'Cost Center wajib dipilih.');
          return;
        }

        const payload = this.rows
          .filter(r => r.id_coa)
          .map(r => {
            const row = { id_coa: r.id_coa };
            for (let m = 1; m <= 12; m++) row['m' + m] = Number(r['m' + m] || 0);
            return row;
          });

        if (payload.length === 0) {
          window.showToast('error', 'Minimal satu baris dengan COA terisi.');
          return;
        }

        this.saving = true;
        const fd = new FormData();
        fd.append('dept', this.dept);
        fd.append('rows', JSON.stringify(payload));

        const res = await window.ypFetch('<?= base_url('foh/saveBudget') ?>', fd);
        this.saving = false;

        if (res.status === 'success') {
          window.showToast('success', res.message || 'Data berhasil disimpan.');
          await this.loadData();
        } else {
          window.showToast('error', res.message || 'Gagal menyimpan data.');
        }
      },

      async submitBudget() {
        if (!this.dept) {
          window.showToast('error', 'Cost Center wajib dipilih.');
          return;
        }
        if (!window.ypConfirm('Submit budget FOH untuk cost center ini? Status akan menjadi SUBMITTED.')) return;

        this.submitting = true;
        const res = await window.ypFetch('<?= base_url('foh/submit') ?>', { dept: this.dept });
        this.submitting = false;

        if (res.status === 'success') {
          window.showToast('success', res.message);
          await this.loadData();
        } else {
          window.showToast('error', res.message);
        }
      },
    };
  }
</script>

<?= $this->endSection() ?>
