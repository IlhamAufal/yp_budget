<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
  $kursUsd = (float) ($kurs['USD'] ?? 0);
  $kursEur = (float) ($kurs['EUR'] ?? 0);
  $hasKurs = $kursUsd > 0 || $kursEur > 0;
?>
<div x-data="salesSimulation()" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">

  <!-- ============================================================ -->
  <!-- HEADER -->
  <!-- ============================================================ -->
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span>Sales</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-brand-500 font-bold">Simulation</span>
      </div>
      <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400">
          <i class="fa-solid fa-chart-line text-base"></i>
        </span>
        Sales Simulation
      </h2>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
        Simulasi revenue & volume berbasis asumsi Master Assumption (KURS, Volume, ASP).
      </p>
    </div>

    <!-- KURS badges -->
    <div class="flex flex-wrap items-center gap-2">
      <div class="rounded-xl border border-indigo-200 dark:border-indigo-700/50 bg-indigo-50 dark:bg-indigo-950/40 px-3.5 py-2 text-xs">
        <span class="font-bold text-indigo-700 dark:text-indigo-300">KURS USD</span>
        <input type="number" step="any" x-model.number="kursUsd" class="ml-2 w-24 rounded-lg border border-indigo-200 dark:border-indigo-700/60 bg-white dark:bg-gray-900 px-2 py-1 text-right font-mono font-bold text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500/40" placeholder="0" />
      </div>
      <div class="rounded-xl border border-violet-200 dark:border-violet-700/50 bg-violet-50 dark:bg-violet-950/40 px-3.5 py-2 text-xs">
        <span class="font-bold text-violet-700 dark:text-violet-300">KURS EUR</span>
        <input type="number" step="any" x-model.number="kursEur" class="ml-2 w-24 rounded-lg border border-violet-200 dark:border-violet-700/60 bg-white dark:bg-gray-900 px-2 py-1 text-right font-mono font-bold text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-violet-500/40" placeholder="0" />
      </div>
      <?php if (! $hasKurs): ?>
        <span class="inline-flex items-center gap-1 rounded-lg bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 px-2.5 py-1.5 text-[11px] font-semibold text-amber-700 dark:text-amber-300">
          <i class="fa-solid fa-triangle-exclamation"></i> KURS belum di-set — isi di Master Assumption → Ekonomi
        </span>
      <?php endif; ?>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- TABS -->
  <!-- ============================================================ -->
  <div class="inline-flex p-1 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 gap-1">
    <button @click="tab = 'revenue'" :class="tab === 'revenue' ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
            class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all">
      <i class="fa-solid fa-money-bill-trend-up mr-1"></i>Revenue Simulation
    </button>
    <button @click="tab = 'volume'" :class="tab === 'volume' ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
            class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all">
      <i class="fa-solid fa-boxes-stacked mr-1"></i>Volume Simulation
    </button>
  </div>

  <!-- ============================================================ -->
  <!-- TAB: REVENUE -->
  <!-- ============================================================ -->
  <div x-show="tab === 'revenue'" x-cloak class="space-y-5">

    <!-- Domestic -->
    <div class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
      <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"><i class="fa-solid fa-store text-sm"></i></span>
        <div>
          <h3 class="text-sm font-bold text-gray-800 dark:text-white">Revenue Domestic</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">Volume × ASP per channel (IDR). Angka bisa diedit untuk simulasi what-if.</p>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
              <th class="py-3 px-4">Channel</th>
              <th class="py-3 px-4 text-right">Volume</th>
              <th class="py-3 px-4 text-right">ASP (Rp)</th>
              <th class="py-3 px-4 text-right">Revenue (Rp)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
            <template x-for="(row, idx) in domesticMatrix" :key="idx">
              <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <td class="py-3 px-4 font-bold text-gray-800 dark:text-gray-200" x-text="row.channel"></td>
                <td class="py-3 px-4"><input type="number" step="any" x-model.number="row.volume" class="w-32 ml-auto block rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-right text-xs font-mono text-gray-800 dark:text-white focus:border-blue-500 focus:outline-none" /></td>
                <td class="py-3 px-4"><input type="number" step="any" x-model.number="row.asp" class="w-36 ml-auto block rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-right text-xs font-mono text-gray-800 dark:text-white focus:border-blue-500 focus:outline-none" /></td>
                <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white" x-text="fmtRp(row.volume * row.asp)"></td>
              </tr>
            </template>
            <tr x-show="domesticMatrix.length === 0">
              <td colspan="4" class="py-10 text-center text-gray-400 dark:text-gray-500">
                <i class="fa-solid fa-circle-info mr-1"></i>Belum ada asumsi domestic — isi di <a href="<?= base_url('master/assumption') ?>" class="font-bold text-brand-500 hover:underline">Master Assumption → Sales Domestic</a>.
              </td>
            </tr>
            <tr class="bg-gray-50/75 dark:bg-gray-800/50 font-bold">
              <td class="py-3 px-4 text-gray-700 dark:text-gray-300">Total Domestic</td>
              <td colspan="2"></td>
              <td class="py-3 px-4 text-right font-mono font-bold text-blue-700 dark:text-blue-400" x-text="fmtRp(domesticTotal)"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Export -->
    <div class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
      <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400"><i class="fa-solid fa-ship text-sm"></i></span>
        <div>
          <h3 class="text-sm font-bold text-gray-800 dark:text-white">Revenue Export</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">Volume × ASP (USD) × KURS USD = Revenue (IDR) per produk.</p>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
              <th class="py-3 px-4">Produk</th>
              <th class="py-3 px-4 text-right">Volume</th>
              <th class="py-3 px-4 text-right">ASP (USD)</th>
              <th class="py-3 px-4 text-right">Revenue (USD)</th>
              <th class="py-3 px-4 text-right">Revenue (IDR)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
            <template x-for="(row, idx) in exportMatrix" :key="idx">
              <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <td class="py-3 px-4 font-bold text-gray-800 dark:text-gray-200" x-text="row.product"></td>
                <td class="py-3 px-4"><input type="number" step="any" x-model.number="row.volume" class="w-32 ml-auto block rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-right text-xs font-mono text-gray-800 dark:text-white focus:border-teal-500 focus:outline-none" /></td>
                <td class="py-3 px-4"><input type="number" step="any" x-model.number="row.asp" class="w-32 ml-auto block rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-right text-xs font-mono text-gray-800 dark:text-white focus:border-teal-500 focus:outline-none" /></td>
                <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white" x-text="fmtUsd(row.volume * row.asp)"></td>
                <td class="py-3 px-4 text-right font-mono font-bold text-teal-700 dark:text-teal-400" x-text="fmtRp(row.volume * row.asp * kursUsd)"></td>
              </tr>
            </template>
            <tr x-show="exportMatrix.length === 0">
              <td colspan="5" class="py-10 text-center text-gray-400 dark:text-gray-500">
                <i class="fa-solid fa-circle-info mr-1"></i>Belum ada asumsi export — isi di <a href="<?= base_url('master/assumption') ?>" class="font-bold text-brand-500 hover:underline">Master Assumption → Sales Export</a>.
              </td>
            </tr>
            <tr class="bg-gray-50/75 dark:bg-gray-800/50 font-bold">
              <td class="py-3 px-4 text-gray-700 dark:text-gray-300">Total Export</td>
              <td colspan="3"></td>
              <td class="py-3 px-4 text-right font-mono font-bold text-teal-700 dark:text-teal-400" x-text="fmtRp(exportTotal)"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Grand total -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-5 text-white shadow-lg">
      <div>
        <p class="text-[11px] font-bold uppercase tracking-widest text-emerald-100">Total Revenue Simulasi (IDR)</p>
        <p class="text-2xl md:text-3xl font-black font-mono mt-1" x-text="fmtRp(grandTotal)"></p>
      </div>
      <div class="flex flex-wrap gap-3 text-xs">
        <div class="rounded-xl bg-white/15 px-3.5 py-2 backdrop-blur">
          <span class="text-emerald-100">Domestic</span><br />
          <span class="font-mono font-bold" x-text="fmtRp(domesticTotal)"></span>
        </div>
        <div class="rounded-xl bg-white/15 px-3.5 py-2 backdrop-blur">
          <span class="text-emerald-100">Export</span><br />
          <span class="font-mono font-bold" x-text="fmtRp(exportTotal)"></span>
        </div>
        <div class="rounded-xl bg-white/15 px-3.5 py-2 backdrop-blur">
          <span class="text-emerald-100">KURS USD</span><br />
          <span class="font-mono font-bold" x-text="kursUsd ? kursUsd.toLocaleString('id-ID') : '-'"></span>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- TAB: VOLUME -->
  <!-- ============================================================ -->
  <div x-show="tab === 'volume'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <div class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
      <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"><i class="fa-solid fa-store text-sm"></i></span>
        <div>
          <h3 class="text-sm font-bold text-gray-800 dark:text-white">Volume Matrix Domestic</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">Unit per channel.</p>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
              <th class="py-3 px-4">Channel</th>
              <th class="py-3 px-4 text-right">Volume</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
            <template x-for="(row, idx) in domesticMatrix" :key="idx">
              <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <td class="py-3 px-4 font-bold text-gray-800 dark:text-gray-200" x-text="row.channel"></td>
                <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white" x-text="fmtNum(row.volume)"></td>
              </tr>
            </template>
            <tr x-show="domesticMatrix.length === 0">
              <td colspan="2" class="py-10 text-center text-gray-400 dark:text-gray-500">Belum ada data volume domestic.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
      <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400"><i class="fa-solid fa-ship text-sm"></i></span>
        <div>
          <h3 class="text-sm font-bold text-gray-800 dark:text-white">Volume Matrix Export</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">Unit per produk ekspor.</p>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
              <th class="py-3 px-4">Produk</th>
              <th class="py-3 px-4 text-right">Volume</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
            <template x-for="(row, idx) in exportMatrix" :key="idx">
              <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <td class="py-3 px-4 font-bold text-gray-800 dark:text-gray-200" x-text="row.product"></td>
                <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white" x-text="fmtNum(row.volume)"></td>
              </tr>
            </template>
            <tr x-show="exportMatrix.length === 0">
              <td colspan="2" class="py-10 text-center text-gray-400 dark:text-gray-500">Belum ada data volume export.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- ============================================================ -->
<!-- ALPINE COMPONENT SCRIPT -->
<!-- ============================================================ -->
<script>
  function salesSimulation() {
    return {
      tab: 'revenue',
      kursUsd: <?= json_encode($kursUsd) ?>,
      kursEur: <?= json_encode($kursEur) ?>,

      domestic: <?= json_encode($domesticAssump ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>,
      exportRows: <?= json_encode($exportAssump ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>,

      get domesticMatrix() {
        const map = {};
        this.domestic.forEach(r => {
          const k = String(r.key_channel || '-');
          if (!map[k]) map[k] = { channel: k, volume: 0, asp: 0 };
          if (String(r.key_description) === 'Volume') map[k].volume = parseFloat(r.key_value) || 0;
          if (String(r.key_description) === 'ASP') map[k].asp = parseFloat(r.key_value) || 0;
        });
        return Object.values(map);
      },

      get exportMatrix() {
        const map = {};
        this.exportRows.forEach(r => {
          const k = String(r.key_indicator || '-');
          if (!map[k]) map[k] = { product: k, volume: 0, asp: 0 };
          if (String(r.key_description) === 'Volume') map[k].volume = parseFloat(r.key_value) || 0;
          if (String(r.key_description) === 'ASP') map[k].asp = parseFloat(r.key_value) || 0;
        });
        return Object.values(map);
      },

      get domesticTotal() {
        return this.domesticMatrix.reduce((s, r) => s + (r.volume * r.asp), 0);
      },

      get exportTotal() {
        return this.exportMatrix.reduce((s, r) => s + (r.volume * r.asp * this.kursUsd), 0);
      },

      get grandTotal() {
        return this.domesticTotal + this.exportTotal;
      },

      fmtNum(n) {
        return (Number(n) || 0).toLocaleString('id-ID');
      },
      fmtUsd(n) {
        return '$ ' + (Number(n) || 0).toLocaleString('en-US', { maximumFractionDigits: 2 });
      },
      fmtRp(n) {
        return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 });
      }
    };
  }
</script>

<?= $this->endSection() ?>
