<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-4" x-data="assumptionPage()">

  <!-- ============================================================ -->
  <!-- BREADCRUMB & HEADER -->
  <!-- ============================================================ -->
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors">
          <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span>Master Data</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-brand-500 font-bold">Master Assumption</span>
      </div>
      <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
          <i class="fa-solid fa-sliders text-base"></i>
        </span>
        Master Assumption
      </h1>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
        Asumsi ekonomi (KURS/Inflasi/GDP), Volume & ASP Sales, serta rasio FOH — basis kalkulasi simulasi & rencana anggaran.
      </p>
    </div>

    <!-- Year Selector -->
    <div class="flex flex-wrap items-center gap-2.5">
      <label class="text-xs font-bold text-gray-500 dark:text-gray-400">Tahun:</label>
      <select
        class="rounded-lg border border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 py-1.5 px-2.5 text-xs font-bold text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500/20 transition-colors"
        @change="window.location.href = '<?= base_url('master/assumption') ?>?year=' + $event.target.value"
      >
        <?php foreach (($years ?? []) as $y): ?>
          <option value="<?= (int) $y ?>" <?= (int) $y === (int) $year ? 'selected' : '' ?>><?= (int) $y ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <!-- Flash -->
  <?php if (! empty($flash['success'])): ?>
    <div class="rounded-xl border border-emerald-200 dark:border-emerald-700/40 bg-emerald-50 dark:bg-emerald-950/40 px-4 py-3 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
      <i class="fa-solid fa-circle-check mr-1.5"></i><?= esc($flash['success']) ?>
    </div>
  <?php endif; ?>
  <?php if (! empty($flash['error'])): ?>
    <div class="rounded-xl border border-red-200 dark:border-red-700/40 bg-red-50 dark:bg-red-950/40 px-4 py-3 text-xs font-semibold text-red-700 dark:text-red-300">
      <i class="fa-solid fa-circle-exclamation mr-1.5"></i><?= esc($flash['error']) ?>
    </div>
  <?php endif; ?>

  <!-- ============================================================ -->
  <!-- TABS -->
  <!-- ============================================================ -->
  <div class="inline-flex p-1 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 gap-1">
    <button @click="tab = 'ekonomi'" :class="tab === 'ekonomi' ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
            class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all">
      <i class="fa-solid fa-money-bill-wave mr-1"></i>Ekonomi (KURS)
    </button>
    <button @click="tab = 'domestic'" :class="tab === 'domestic' ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
            class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all">
      <i class="fa-solid fa-store mr-1"></i>Sales Domestic
    </button>
    <button @click="tab = 'export'" :class="tab === 'export' ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
            class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all">
      <i class="fa-solid fa-ship mr-1"></i>Sales Export
    </button>
    <button @click="tab = 'other'" :class="tab === 'other' ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
            class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all">
      <i class="fa-solid fa-industry mr-1"></i>Lainnya (FOH)
    </button>
  </div>

  <?php
    $tabMeta = [
        'ekonomi'  => ['label' => 'Asumsi Ekonomi', 'desc' => 'KURS mata uang, inflasi, GDP, dan variabel ekonomi lain per tahun anggaran.', 'icon' => 'fa-money-bill-wave'],
        'domestic' => ['label' => 'Asumsi Sales Domestic', 'desc' => 'Volume & ASP per channel penjualan (GT, MT, OEM, dll).', 'icon' => 'fa-store'],
        'export'   => ['label' => 'Asumsi Sales Export', 'desc' => 'Volume & ASP per produk ekspor (dalam USD).', 'icon' => 'fa-ship'],
        'other'    => ['label' => 'Asumsi Lainnya (FOH)', 'desc' => 'Rasio biaya FOH / selling & A/P expenses (Delivery, Insurance, dll).', 'icon' => 'fa-industry'],
    ];
  ?>

  <!-- TAB: EKONOMI -->
  <div x-show="tab === 'ekonomi'" x-cloak class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 border-b border-gray-100 dark:border-gray-800">
      <div>
        <h3 class="text-sm font-bold text-gray-800 dark:text-white"><?= $tabMeta['ekonomi']['label'] ?></h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= $tabMeta['ekonomi']['desc'] ?></p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <a href="<?= base_url('master/assumption/export/ekonomi?year=' . $year) ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 transition-colors">
          <i class="fa-solid fa-file-excel"></i> Template / Export
        </a>
        <form action="<?= base_url('master/assumption/upload') ?>" method="post" enctype="multipart/form-data" class="flex items-center gap-1.5">
          <input type="hidden" name="category" value="ekonomi" />
          <input type="hidden" name="year" value="<?= (int) $year ?>" />
          <input type="file" name="excel_file" accept=".xlsx,.xls" required class="text-[11px] text-gray-500 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-2.5 file:py-1.5 file:text-[11px] file:font-bold file:text-indigo-600 file:hover:bg-indigo-100 file:cursor-pointer" />
          <button type="submit" class="rounded-lg bg-indigo-500 px-3 py-1.5 text-[11px] font-bold text-white hover:bg-indigo-600 transition-colors">Upload</button>
        </form>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <th class="py-3 px-4">Tipe</th>
            <th class="py-3 px-4">Deskripsi</th>
            <th class="py-3 px-4 text-right">Nilai</th>
            <th class="py-3 px-4 text-right w-16"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
          <template x-for="(row, idx) in economic" :key="idx">
            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
              <td class="py-2.5 px-4">
                <select x-model="row.type_id" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2 py-1.5 text-xs text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none">
                  <template x-for="t in types" :key="t.id">
                    <option :value="t.id" x-text="t.desc"></option>
                  </template>
                </select>
              </td>
              <td class="py-2.5 px-4">
                <input type="text" x-model="row.desc" placeholder="Deskripsi asumsi" class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-xs text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" />
              </td>
              <td class="py-2.5 px-4 text-right">
                <input type="number" step="any" x-model.number="row.value" class="w-36 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-right text-xs font-mono font-bold text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" />
              </td>
              <td class="py-2.5 px-4 text-right">
                <button @click="removeRow('economic', idx)" class="h-7 w-7 rounded-lg border border-gray-200 dark:border-gray-700 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"><i class="fa-solid fa-trash text-[11px]"></i></button>
              </td>
            </tr>
          </template>
          <tr x-show="economic.length === 0">
            <td colspan="4" class="py-10 text-center text-gray-400 dark:text-gray-500">
              <i class="fa-solid fa-circle-info mr-1"></i>Belum ada data asumsi ekonomi untuk tahun <?= (int) $year ?>. Klik <span class="font-bold">+ Tambah Baris</span> atau upload Excel.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
      <button @click="addRow('economic')" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-[11px] font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <i class="fa-solid fa-plus"></i> Tambah Baris
      </button>
      <button @click="save('ekonomi')" :disabled="saving" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-1.5 text-[11px] font-bold text-white hover:bg-brand-600 disabled:opacity-50 transition-colors">
        <i class="fa-solid fa-spinner fa-spin" x-show="saving"></i>
        <span x-text="saving ? 'Menyimpan...' : 'Simpan Asumsi Ekonomi'"></span>
      </button>
    </div>
  </div>

  <!-- TAB: SALES DOMESTIC -->
  <div x-show="tab === 'domestic'" x-cloak class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 border-b border-gray-100 dark:border-gray-800">
      <div>
        <h3 class="text-sm font-bold text-gray-800 dark:text-white"><?= $tabMeta['domestic']['label'] ?></h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= $tabMeta['domestic']['desc'] ?></p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <a href="<?= base_url('master/assumption/export/domestic?year=' . $year) ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 transition-colors">
          <i class="fa-solid fa-file-excel"></i> Template / Export
        </a>
        <form action="<?= base_url('master/assumption/upload') ?>" method="post" enctype="multipart/form-data" class="flex items-center gap-1.5">
          <input type="hidden" name="category" value="domestic" />
          <input type="hidden" name="year" value="<?= (int) $year ?>" />
          <input type="file" name="excel_file" accept=".xlsx,.xls" required class="text-[11px] text-gray-500 file:mr-2 file:rounded-lg file:border-0 file:bg-blue-50 file:px-2.5 file:py-1.5 file:text-[11px] file:font-bold file:text-blue-600 file:hover:bg-blue-100 file:cursor-pointer" />
          <button type="submit" class="rounded-lg bg-blue-500 px-3 py-1.5 text-[11px] font-bold text-white hover:bg-blue-600 transition-colors">Upload</button>
        </form>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <th class="py-3 px-4">Channel</th>
            <th class="py-3 px-4">Deskripsi</th>
            <th class="py-3 px-4">Indicator</th>
            <th class="py-3 px-4 text-right">Nilai</th>
            <th class="py-3 px-4 text-right w-16"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
          <template x-for="(row, idx) in domestic" :key="idx">
            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
              <td class="py-2.5 px-4">
                <input type="text" x-model="row.key_channel" placeholder="GT" class="w-24 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-xs text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" />
              </td>
              <td class="py-2.5 px-4">
                <select x-model="row.key_description" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-xs text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none">
                  <option value="Volume">Volume</option>
                  <option value="ASP">ASP</option>
                </select>
              </td>
              <td class="py-2.5 px-4">
                <input type="text" x-model="row.key_indicator" placeholder="*" class="w-20 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-xs text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" />
              </td>
              <td class="py-2.5 px-4 text-right">
                <input type="number" step="any" x-model.number="row.key_value" class="w-36 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-right text-xs font-mono font-bold text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" />
              </td>
              <td class="py-2.5 px-4 text-right">
                <button @click="removeRow('domestic', idx)" class="h-7 w-7 rounded-lg border border-gray-200 dark:border-gray-700 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"><i class="fa-solid fa-trash text-[11px]"></i></button>
              </td>
            </tr>
          </template>
          <tr x-show="domestic.length === 0">
            <td colspan="5" class="py-10 text-center text-gray-400 dark:text-gray-500">
              <i class="fa-solid fa-circle-info mr-1"></i>Belum ada data asumsi domestic untuk tahun <?= (int) $year ?>.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
      <button @click="addRow('domestic')" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-[11px] font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <i class="fa-solid fa-plus"></i> Tambah Baris
      </button>
      <button @click="save('domestic')" :disabled="saving" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-1.5 text-[11px] font-bold text-white hover:bg-brand-600 disabled:opacity-50 transition-colors">
        <i class="fa-solid fa-spinner fa-spin" x-show="saving"></i>
        <span x-text="saving ? 'Menyimpan...' : 'Simpan Asumsi Domestic'"></span>
      </button>
    </div>
  </div>

  <!-- TAB: SALES EXPORT -->
  <div x-show="tab === 'export'" x-cloak class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 border-b border-gray-100 dark:border-gray-800">
      <div>
        <h3 class="text-sm font-bold text-gray-800 dark:text-white"><?= $tabMeta['export']['label'] ?></h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= $tabMeta['export']['desc'] ?></p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <a href="<?= base_url('master/assumption/export/export?year=' . $year) ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 transition-colors">
          <i class="fa-solid fa-file-excel"></i> Template / Export
        </a>
        <form action="<?= base_url('master/assumption/upload') ?>" method="post" enctype="multipart/form-data" class="flex items-center gap-1.5">
          <input type="hidden" name="category" value="export" />
          <input type="hidden" name="year" value="<?= (int) $year ?>" />
          <input type="file" name="excel_file" accept=".xlsx,.xls" required class="text-[11px] text-gray-500 file:mr-2 file:rounded-lg file:border-0 file:bg-teal-50 file:px-2.5 file:py-1.5 file:text-[11px] file:font-bold file:text-teal-600 file:hover:bg-teal-100 file:cursor-pointer" />
          <button type="submit" class="rounded-lg bg-teal-500 px-3 py-1.5 text-[11px] font-bold text-white hover:bg-teal-600 transition-colors">Upload</button>
        </form>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <th class="py-3 px-4">Channel</th>
            <th class="py-3 px-4">Deskripsi</th>
            <th class="py-3 px-4">Produk</th>
            <th class="py-3 px-4 text-right">Nilai</th>
            <th class="py-3 px-4 text-right w-16"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
          <template x-for="(row, idx) in exportRows" :key="idx">
            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
              <td class="py-2.5 px-4">
                <input type="text" x-model="row.key_channel" placeholder="*" class="w-20 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-xs text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" />
              </td>
              <td class="py-2.5 px-4">
                <select x-model="row.key_description" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-xs text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none">
                  <option value="Volume">Volume</option>
                  <option value="ASP">ASP</option>
                </select>
              </td>
              <td class="py-2.5 px-4">
                <input type="text" x-model="row.key_indicator" placeholder="GUMMY" class="w-28 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-xs text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" />
              </td>
              <td class="py-2.5 px-4 text-right">
                <input type="number" step="any" x-model.number="row.key_value" class="w-36 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-right text-xs font-mono font-bold text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" />
              </td>
              <td class="py-2.5 px-4 text-right">
                <button @click="removeRow('exportRows', idx)" class="h-7 w-7 rounded-lg border border-gray-200 dark:border-gray-700 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"><i class="fa-solid fa-trash text-[11px]"></i></button>
              </td>
            </tr>
          </template>
          <tr x-show="exportRows.length === 0">
            <td colspan="5" class="py-10 text-center text-gray-400 dark:text-gray-500">
              <i class="fa-solid fa-circle-info mr-1"></i>Belum ada data asumsi export untuk tahun <?= (int) $year ?>.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
      <button @click="addRow('export')" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-[11px] font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <i class="fa-solid fa-plus"></i> Tambah Baris
      </button>
      <button @click="save('export')" :disabled="saving" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-1.5 text-[11px] font-bold text-white hover:bg-brand-600 disabled:opacity-50 transition-colors">
        <i class="fa-solid fa-spinner fa-spin" x-show="saving"></i>
        <span x-text="saving ? 'Menyimpan...' : 'Simpan Asumsi Export'"></span>
      </button>
    </div>
  </div>

  <!-- TAB: OTHER (FOH) -->
  <div x-show="tab === 'other'" x-cloak class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-xs overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 border-b border-gray-100 dark:border-gray-800">
      <div>
        <h3 class="text-sm font-bold text-gray-800 dark:text-white"><?= $tabMeta['other']['label'] ?></h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= $tabMeta['other']['desc'] ?></p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <a href="<?= base_url('master/assumption/export/other?year=' . $year) ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 transition-colors">
          <i class="fa-solid fa-file-excel"></i> Template / Export
        </a>
        <form action="<?= base_url('master/assumption/upload') ?>" method="post" enctype="multipart/form-data" class="flex items-center gap-1.5">
          <input type="hidden" name="category" value="other" />
          <input type="hidden" name="year" value="<?= (int) $year ?>" />
          <input type="file" name="excel_file" accept=".xlsx,.xls" required class="text-[11px] text-gray-500 file:mr-2 file:rounded-lg file:border-0 file:bg-amber-50 file:px-2.5 file:py-1.5 file:text-[11px] file:font-bold file:text-amber-600 file:hover:bg-amber-100 file:cursor-pointer" />
          <button type="submit" class="rounded-lg bg-amber-500 px-3 py-1.5 text-[11px] font-bold text-white hover:bg-amber-600 transition-colors">Upload</button>
        </form>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <th class="py-3 px-4">Kode</th>
            <th class="py-3 px-4">Tipe Group</th>
            <th class="py-3 px-4">Variabel</th>
            <th class="py-3 px-4 text-right">Nilai</th>
            <th class="py-3 px-4 text-right w-16"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
          <template x-for="(row, idx) in other" :key="idx">
            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
              <td class="py-2.5 px-4">
                <input type="text" x-model="row.id_assp" placeholder="FOH1" class="w-20 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-xs text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" />
              </td>
              <td class="py-2.5 px-4">
                <input type="text" x-model="row.tipe_group" placeholder="FOH - SELLING & A/P EXPENSES" class="w-56 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-xs text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" />
              </td>
              <td class="py-2.5 px-4">
                <input type="text" x-model="row.variable_text" placeholder="Delivery Expense Domestic" class="w-full min-w-[160px] rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-xs text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" />
              </td>
              <td class="py-2.5 px-4 text-right">
                <input type="number" step="any" x-model.number="row.value_text" class="w-36 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 text-right text-xs font-mono font-bold text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" />
              </td>
              <td class="py-2.5 px-4 text-right">
                <button @click="removeRow('other', idx)" class="h-7 w-7 rounded-lg border border-gray-200 dark:border-gray-700 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"><i class="fa-solid fa-trash text-[11px]"></i></button>
              </td>
            </tr>
          </template>
          <tr x-show="other.length === 0">
            <td colspan="5" class="py-10 text-center text-gray-400 dark:text-gray-500">
              <i class="fa-solid fa-circle-info mr-1"></i>Belum ada data asumsi lain untuk tahun <?= (int) $year ?>.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
      <button @click="addRow('other')" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-[11px] font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <i class="fa-solid fa-plus"></i> Tambah Baris
      </button>
      <button @click="save('other')" :disabled="saving" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-1.5 text-[11px] font-bold text-white hover:bg-brand-600 disabled:opacity-50 transition-colors">
        <i class="fa-solid fa-spinner fa-spin" x-show="saving"></i>
        <span x-text="saving ? 'Menyimpan...' : 'Simpan Asumsi Lain'"></span>
      </button>
    </div>
  </div>

</div>

<!-- ============================================================ -->
<!-- ALPINE COMPONENT SCRIPT -->
<!-- ============================================================ -->
<script>
  function assumptionPage() {
    return {
      tab: 'ekonomi',
      year: '<?= (int) $year ?>',
      saving: false,

      // Pagination state per tab
      page: { ekonomi: 1, domestic: 1, export: 1, other: 1 },
      perPage: 10,

      types: <?= json_encode(array_map(fn($t) => ['id' => (int) $t['id'], 'desc' => $t['desc_assumption']], $types ?? []), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>,

      economic:  <?= json_encode($economic ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>,
      domestic:  <?= json_encode($domestic ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>,
      exportRows: <?= json_encode($export ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>,
      other:     <?= json_encode($other ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>,

      paginatedRows(cat) {
        const key = cat === 'ekonomi' ? 'economic' : (cat === 'export' ? 'exportRows' : cat);
        const list = this[key] || [];
        const p = this.page[cat] || 1;
        return list.slice((p - 1) * this.perPage, p * this.perPage);
      },

      totalPages(cat) {
        const key = cat === 'ekonomi' ? 'economic' : (cat === 'export' ? 'exportRows' : cat);
        const list = this[key] || [];
        return Math.ceil(list.length / this.perPage) || 1;
      },

      pageNumbers(cat) {
        const totalP = this.totalPages(cat);
        const currP = this.page[cat] || 1;
        if (totalP <= 7) return Array.from({ length: totalP }, (_, i) => i + 1);
        if (currP <= 4) return [1, 2, 3, 4, 5, '...', totalP];
        if (currP >= totalP - 3) return [1, '...', totalP - 4, totalP - 3, totalP - 2, totalP - 1, totalP];
        return [1, '...', currP - 1, currP, currP + 1, '...', totalP];
      },

      addRow(cat) {
        if (cat === 'economic') this.economic.push({ desc: '', type_id: this.types[0] ? this.types[0].id : '', value: 0 });
        if (cat === 'domestic') this.domestic.push({ key_channel: '', key_description: 'Volume', key_indicator: '', key_value: 0 });
        if (cat === 'export') this.exportRows.push({ key_channel: '', key_description: 'Volume', key_indicator: '', key_value: 0 });
        if (cat === 'other') this.other.push({ id_assp: '', tipe_group: '', variable_text: '', value_text: 0 });
      },

      removeRowObject(cat, row) {
        const key = cat === 'ekonomi' ? 'economic' : (cat === 'export' ? 'exportRows' : cat);
        const index = this[key].indexOf(row);
        if (index > -1) this[key].splice(index, 1);
      },

      removeRow(cat, idx) {
        this[cat].splice(idx, 1);
      },

      async save(cat) {
        const key = cat === 'ekonomi' ? 'economic' : cat;
        this.saving = true;
        try {
          const res = await window.ypFetch('<?= base_url('master/api/assumption/save') ?>', {
            category: cat,
            year: this.year,
            rows: JSON.stringify(this[key])
          });
          if (res.success) {
            window.showToast('success', res.message || 'Asumsi berhasil disimpan');
            setTimeout(() => window.location.reload(), 700);
          } else {
            window.showToast('error', res.message || 'Gagal menyimpan asumsi');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan sistem');
        } finally {
          this.saving = false;
        }
      }
    };
  }
</script>

<?= $this->endSection() ?>
