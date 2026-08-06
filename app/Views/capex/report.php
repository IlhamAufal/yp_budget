<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ activeTab: 'department' }" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Laporan Konsolidasi CAPEX</h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Rekap alokasi belanja modal & dampak penyusutan ke OPEX/FOH.</p>
        </div>
        <button @click="syncToOpex()" class="px-4.5 py-2.5 bg-emerald-600 text-white rounded-xl text-xs sm:text-sm font-semibold hover:bg-emerald-700 active:scale-[0.98] transition-all shadow-xs flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Process & Sync to OPEX
        </button>
    </div>

    <div class="border-b border-gray-200">
        <nav class="flex gap-6 -mb-px">
            <button @click="activeTab = 'department'" :class="activeTab === 'department' ? 'border-brand-600 text-brand-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-3 px-1 border-b-2 text-xs transition-colors">
                Laporan Per Departemen
            </button>
            <button @click="activeTab = 'total_sync'" :class="activeTab === 'total_sync' ? 'border-brand-600 text-brand-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-3 px-1 border-b-2 text-xs transition-colors">
                Grand Total & OPEX Impact Sync
            </button>
        </nav>
    </div>

    <div x-show="activeTab === 'department'" class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-100/80 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 text-[11px] font-bold tracking-wider uppercase border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-5 py-3.5">Departemen</th>
                        <th class="px-5 py-3.5 text-right">Total Item Aset</th>
                        <th class="px-5 py-3.5 text-right">Nilai Akuisisi CAPEX</th>
                        <th class="px-5 py-3.5 text-right">Beban Depresiasi / Tahun</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/80 dark:divide-gray-800/80 text-gray-700 dark:text-gray-300">
                    <?php foreach ($dept_reports as $row) : ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-5 py-3.5 font-semibold text-gray-900 dark:text-white"><?= esc($row['department_name']) ?></td>
                            <td class="px-5 py-3.5 text-right font-mono font-medium"><?= number_format($row['total_items']) ?></td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-gray-900 dark:text-white">Rp <?= number_format($row['total_acquisition'], 0, ',', '.') ?></td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">Rp <?= number_format($row['annual_depreciation'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="activeTab === 'total_sync'" class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs p-6 space-y-4">
        <div>
            <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">Konsolidasi Alokasi Depresiasi CAPEX ke Akun OPEX / FOH</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Nilai depresiasi bulanan di bawah ini secara otomatis dialokasikan ke beban operasional departemen terkait.</p>
        </div>
        
        <div class="overflow-x-auto rounded-xl border border-gray-200/80 dark:border-gray-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-100/80 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 text-[11px] font-bold tracking-wider uppercase border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-5 py-3.5">Akun Tujuan (OPEX/FOH)</th>
                        <th class="px-5 py-3.5 text-right">Jan - Jun</th>
                        <th class="px-5 py-3.5 text-right">Jul - Des</th>
                        <th class="px-5 py-3.5 text-right">Total Depresiasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/80 dark:divide-gray-800/80 text-gray-700 dark:text-gray-300">
                    <?php foreach ($total_opex_sync as $sync) : ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-5 py-3.5 font-semibold text-gray-900 dark:text-white"><?= esc($sync['account_name']) ?></td>
                            <td class="px-5 py-3.5 text-right font-mono font-medium">Rp <?= number_format($sync['h1_amount'], 0, ',', '.') ?></td>
                            <td class="px-5 py-3.5 text-right font-mono font-medium">Rp <?= number_format($sync['h2_amount'], 0, ',', '.') ?></td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-primary">Rp <?= number_format($sync['total_amount'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
async function syncToOpex() {
    if (!confirm('Jalankan proses sinkronisasi depresiasi CAPEX ke modul OPEX/FOH?')) return;
    const response = await fetch('<?= base_url('capex/sync_to_opex') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const res = await response.json();
    alert(res.message);
}
</script>
<?= $this->endSection() ?>