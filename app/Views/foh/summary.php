<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 md:p-8 mx-auto max-w-(--breakpoint-2xl) space-y-6 md:space-y-8">

  <!-- HEADER -->
  <div>
    <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-3">
      <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
      <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
      <span>FOH</span>
      <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
      <span class="text-brand-500 font-bold">Summary</span>
    </div>
    <h1 class="text-2xl md:text-3xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-4">
      <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-500 dark:bg-sky-500/10 dark:text-sky-400">
        <i class="fa-solid fa-table-cells-large text-xl"></i>
      </span>
      Summary FOH
    </h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
      Ringkasan budget Factory Overhead per Cost Center — Tahun Anggaran <span class="font-bold text-brand-500"><?= esc($workingYear) ?></span>
    </p>
  </div>

  <!-- CARD TOTAL -->
  <?php
    $grandTotal = 0;
    foreach (($summary ?? []) as $s) { $grandTotal += (float) ($s['total'] ?? 0); }
  ?>
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-5 flex items-center gap-4">
      <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400"><i class="fa-solid fa-industry"></i></span>
      <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cost Center</p>
        <p class="text-2xl font-black text-gray-900 dark:text-white"><?= count($summary ?? []) ?></p>
      </div>
    </div>
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-5 flex items-center gap-4">
      <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400"><i class="fa-solid fa-coins"></i></span>
      <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Budget FOH</p>
        <p class="text-2xl font-black text-gray-900 dark:text-white"><?= number_format($grandTotal) ?></p>
      </div>
    </div>
    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs p-5 flex items-center gap-4">
      <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 dark:bg-amber-500/10 dark:text-amber-400"><i class="fa-solid fa-paper-plane"></i></span>
      <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sumber Data</p>
        <p class="text-lg font-black text-gray-900 dark:text-white">OPEX Engine <span class="text-xs font-bold text-emerald-500">(source: FOH)</span></p>
      </div>
    </div>
  </div>

  <!-- TABLE SUMMARY -->
  <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse min-w-[1000px]">
        <thead>
          <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200/80 dark:border-gray-800 text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <th class="py-4 px-4">Cost Center</th>
            <th class="py-4 px-4 min-w-[160px]">Deskripsi</th>
            <th class="py-4 px-2 text-right">Jan</th>
            <th class="py-4 px-2 text-right">Feb</th>
            <th class="py-4 px-2 text-right">Mar</th>
            <th class="py-4 px-2 text-right">Apr</th>
            <th class="py-4 px-2 text-right">May</th>
            <th class="py-4 px-2 text-right">Jun</th>
            <th class="py-4 px-2 text-right">Jul</th>
            <th class="py-4 px-2 text-right">Aug</th>
            <th class="py-4 px-2 text-right">Sep</th>
            <th class="py-4 px-2 text-right">Oct</th>
            <th class="py-4 px-2 text-right">Nov</th>
            <th class="py-4 px-2 text-right">Dec</th>
            <th class="py-4 px-4 text-right bg-gray-100/70 dark:bg-gray-800">Total</th>
            <th class="py-4 px-4 text-center">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <?php if (empty($summary)): ?>
            <tr>
              <td colspan="16" class="py-16 text-center text-gray-400 dark:text-gray-500">
                <i class="fa-solid fa-folder-open text-2xl mb-3"></i>
                <p class="font-semibold text-gray-600 dark:text-gray-300">Belum ada data budget FOH</p>
                <p class="text-sm">Entry budget FOH melalui menu <strong>FOH → Entry Budget</strong> terlebih dahulu.</p>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($summary as $row): ?>
              <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <td class="py-3 px-4 font-mono font-bold text-gray-900 dark:text-gray-100"><?= esc($row['id_dept']) ?></td>
                <td class="py-3 px-4 text-gray-700 dark:text-gray-300"><?= esc($row['cost_desc'] ?: '-') ?></td>
                <?php
                  $keys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
                  foreach ($keys as $k): ?>
                    <td class="py-3 px-2 text-right text-gray-600 dark:text-gray-300"><?= number_format((float) $row[$k]) ?></td>
                <?php endforeach; ?>
                <td class="py-3 px-4 text-right font-bold text-brand-500 dark:text-brand-400 bg-gray-50/70 dark:bg-gray-800/50"><?= number_format((float) $row['total']) ?></td>
                <td class="py-3 px-4 text-center">
                  <?php if ((int) ($row['submitted_rows'] ?? 0) > 0): ?>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 px-3 py-1 text-[10px] font-bold">
                      <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> SUBMITTED
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 px-3 py-1 text-[10px] font-bold">
                      <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> DRAFT
                    </span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?= $this->endSection() ?>
