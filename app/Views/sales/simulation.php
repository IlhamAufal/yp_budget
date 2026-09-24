<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
  $kursUsd = (float) ($kurs['USD'] ?? 0);
  $kursEur = (float) ($kurs['EUR'] ?? 0);
  $hasKurs = $kursUsd > 0 || $kursEur > 0;
?>
<div x-data="salesSimulation()" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

  <!-- ============================================================ -->
  <!-- HEADER -->
  <!-- ============================================================ -->
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <a href="<?= base_url('sales') ?>" class="hover:text-[#2F3185] transition-colors">Sales</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span class="text-[#2F3185] font-bold">Simulation</span>
      </div>
      <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        Sales Simulation
      </h1>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
        Simulasi revenue & volume berbasis asumsi Master Assumption (KURS, Volume, ASP).
      </p>
    </div>

    <!-- KURS controls -->
    <div class="flex flex-wrap items-center gap-2">
      <div class="rounded-xl border border-indigo-200 dark:border-indigo-700/50 bg-indigo-50 dark:bg-indigo-950/40 px-3.5 py-2 text-xs flex items-center">
        <span class="font-bold text-indigo-700 dark:text-indigo-300">KURS USD</span>
        <input type="number" step="any" x-model.number="kursUsd" class="ml-2 w-24 rounded-lg border border-indigo-200 dark:border-indigo-700/60 bg-white dark:bg-gray-900 px-2 py-1 text-right font-mono font-bold text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500/40" placeholder="0" />
      </div>
      <div class="rounded-xl border border-violet-200 dark:border-violet-700/50 bg-violet-50 dark:bg-violet-950/40 px-3.5 py-2 text-xs flex items-center">
        <span class="font-bold text-violet-700 dark:text-violet-300">KURS EUR</span>
        <input type="number" step="any" x-model.number="kursEur" class="ml-2 w-24 rounded-lg border border-violet-200 dark:border-violet-700/60 bg-white dark:bg-gray-900 px-2 py-1 text-right font-mono font-bold text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-violet-500/40" placeholder="0" />
      </div>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- TABS -->
  <!-- ============================================================ -->
  <div class="inline-flex max-w-full nav-tab-container bg-[#2F3185] p-1.5 rounded-2xl shadow-xs">
    <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar" aria-label="Simulation Tabs">
      <button type="button" @click="tab = 'revenue'" :class="tab === 'revenue' ? 'active' : ''"
              class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
        <span>Revenue Simulation</span>
      </button>
      <button type="button" @click="tab = 'volume'" :class="tab === 'volume' ? 'active' : ''"
              class="tab-btn px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-2">
        <span>Volume Simulation</span>
      </button>
    </nav>
  </div>

  <!-- ============================================================ -->
  <!-- TAB: REVENUE -->
  <!-- ============================================================ -->
  <div x-show="tab === 'revenue'" x-cloak class="space-y-6">

    <!-- Domestic -->
    <div class="space-y-3">
      <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Revenue Domestic</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Volume × ASP per channel (IDR). Angka dapat disesuaikan untuk simulasi what-if.</p>
      </div>
      <div class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
          <table class="w-full text-left border-collapse">
            <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
              <tr class="bg-[#2F3185] text-white font-semibold">
                <th class="py-3 px-4 text-white border-r border-white/20">Channel</th>
                <th class="py-3 px-4 text-right text-white border-r border-white/20">Volume (Kg)</th>
                <th class="py-3 px-4 text-right text-white border-r border-white/20">ASP (Rp/kg)</th>
                <th class="py-3 px-4 text-right text-white">Revenue</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
              <template x-for="(row, idx) in domesticMatrix" :key="idx">
                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                  <td class="py-3 px-4 font-bold text-gray-800 dark:text-gray-200 border-r border-gray-200 dark:border-gray-800" x-text="row.channel"></td>
                  <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-800"><input type="number" step="any" x-model.number="row.volume" class="w-36 ml-auto block rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-1.5 text-right text-xs font-mono text-gray-800 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 outline-none transition-all" /></td>
                  <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-800"><input type="number" step="any" x-model.number="row.asp" class="w-36 ml-auto block rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-1.5 text-right text-xs font-mono text-gray-800 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 outline-none transition-all" /></td>
                  <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white" x-text="fmtRp(row.volume * row.asp)"></td>
                </tr>
              </template>
              <tr x-show="domesticMatrix.length === 0">
                <td colspan="4" class="py-10 text-center text-gray-400 dark:text-gray-500">
                  Belum ada asumsi domestic — silakan input data asumsi pada entry sales domestic.
                </td>
              </tr>
              <tr class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold border-t-2 border-[#2F3185]/30">
                <td class="py-3 px-4 text-gray-900 dark:text-white">Total Domestic</td>
                <td colspan="2"></td>
                <td class="py-3 px-4 text-right font-mono font-bold text-[#2F3185] dark:text-indigo-400" x-text="fmtRp(domesticTotal)"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Export -->
    <div class="space-y-3">
      <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Revenue Export (International)</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Volume × ASP (USD) × KURS USD = Revenue (IDR) per produk.</p>
      </div>
      <div class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
          <table class="w-full text-left border-collapse">
            <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
              <tr class="bg-[#2F3185] text-white font-semibold">
                <th class="py-3 px-4 text-white border-r border-white/20">Produk</th>
                <th class="py-3 px-4 text-right text-white border-r border-white/20">Volume (Kg)</th>
                <th class="py-3 px-4 text-right text-white border-r border-white/20">ASP (USD)</th>
                <th class="py-3 px-4 text-right text-white border-r border-white/20">Revenue (USD)</th>
                <th class="py-3 px-4 text-right text-white">Revenue (IDR)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
              <template x-for="(row, idx) in exportMatrix" :key="idx">
                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                  <td class="py-3 px-4 font-bold text-gray-800 dark:text-gray-200 border-r border-gray-200 dark:border-gray-800" x-text="row.product"></td>
                  <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-800"><input type="number" step="any" x-model.number="row.volume" class="w-32 ml-auto block rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-1.5 text-right text-xs font-mono text-gray-800 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 outline-none transition-all" /></td>
                  <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-800"><input type="number" step="any" x-model.number="row.asp" class="w-32 ml-auto block rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-1.5 text-right text-xs font-mono text-gray-800 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 outline-none transition-all" /></td>
                  <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-800" x-text="fmtUsd(row.volume * row.asp)"></td>
                  <td class="py-3 px-4 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="fmtRp(row.volume * row.asp * kursUsd)"></td>
                </tr>
              </template>
              <tr x-show="exportMatrix.length === 0">
                <td colspan="5" class="py-10 text-center text-gray-400 dark:text-gray-500">
                  Belum ada asumsi export — silakan input data asumsi pada entry sales international.
                </td>
              </tr>
              <tr class="bg-[#2F3185]/10 dark:bg-[#2F3185]/20 font-bold border-t-2 border-[#2F3185]/30">
                <td class="py-3 px-4 text-gray-900 dark:text-white">Total Export</td>
                <td colspan="3"></td>
                <td class="py-3 px-4 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="fmtRp(exportTotal)"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Grand total Card -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-2xl bg-[#2F3185] px-6 py-5 text-white shadow-md">
      <div>
        <p class="text-xs font-semibold text-white/80">Total Revenue Simulasi (IDR)</p>
        <p class="text-2xl md:text-3xl font-black font-mono mt-1" x-text="fmtRp(grandTotal)"></p>
      </div>
      <div class="flex flex-wrap gap-3 text-xs">
        <div class="rounded-xl bg-white/10 px-4 py-2.5 backdrop-blur">
          <span class="text-white/70">Domestic</span><br />
          <span class="font-mono font-bold" x-text="fmtRp(domesticTotal)"></span>
        </div>
        <div class="rounded-xl bg-white/10 px-4 py-2.5 backdrop-blur">
          <span class="text-white/70">Export</span><br />
          <span class="font-mono font-bold" x-text="fmtRp(exportTotal)"></span>
        </div>
        <div class="rounded-xl bg-white/10 px-4 py-2.5 backdrop-blur">
          <span class="text-white/70">KURS USD</span><br />
          <span class="font-mono font-bold" x-text="kursUsd ? kursUsd.toLocaleString('id-ID') : '-'"></span>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- TAB: VOLUME -->
  <!-- ============================================================ -->
  <div x-show="tab === 'volume'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="space-y-3">
      <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Volume Matrix Domestic</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Unit per channel.</p>
      </div>
      <div class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
              <tr class="bg-[#2F3185] text-white font-semibold">
                <th class="py-3 px-4 text-white border-r border-white/20">Channel</th>
                <th class="py-3 px-4 text-right text-white">Volume (Kg)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
              <template x-for="(row, idx) in domesticMatrix" :key="idx">
                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                  <td class="py-3 px-4 font-bold text-gray-800 dark:text-gray-200 border-r border-gray-200 dark:border-gray-800" x-text="row.channel"></td>
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
    </div>

    <div class="space-y-3">
      <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Volume Matrix Export</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Unit per produk ekspor.</p>
      </div>
      <div class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
              <tr class="bg-[#2F3185] text-white font-semibold">
                <th class="py-3 px-4 text-white border-r border-white/20">Produk</th>
                <th class="py-3 px-4 text-right text-white">Volume (Kg)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
              <template x-for="(row, idx) in exportMatrix" :key="idx">
                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                  <td class="py-3 px-4 font-bold text-gray-800 dark:text-gray-200 border-r border-gray-200 dark:border-gray-800" x-text="row.product"></td>
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
