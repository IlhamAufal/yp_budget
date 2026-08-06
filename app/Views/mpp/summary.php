<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="mppSummary()" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Summary Headcount & OPEX Sync</h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Konsolidasi Alokasi Gaji & Sync ke Engine OPEX.</p>
        </div>
        <button type="button" @click="syncToOpex()" :disabled="syncing"
            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-xl transition-colors shadow-xs flex items-center space-x-2">
            <span x-show="!syncing">Process to OPEX Engine</span>
            <span x-show="syncing">Sychronizing...</span>
        </button>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xs border border-gray-200/80 dark:border-gray-800 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
            <h2 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Rekapitulasi Alokasi per Department & COA</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-100/80 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 text-[11px] font-bold tracking-wider uppercase border-b border-gray-200 dark:border-gray-700">
                        <th class="px-5 py-3.5">Department</th>
                        <th class="px-5 py-3.5">Tipe Karyawan</th>
                        <th class="px-5 py-3.5">COA Gaji</th>
                        <th class="px-5 py-3.5 text-right">Tarif Monthly</th>
                        <th class="px-5 py-3.5 text-center">Total HC (Jan-Des)</th>
                        <th class="px-5 py-3.5 text-right">Est. Total Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/80 dark:divide-gray-800/80 text-gray-700 dark:text-gray-300">
                    <?php if (empty($summary)): ?>
                        <tr>
                            <td colspan="6" class="p-16 text-center text-gray-400 dark:text-gray-500">Belum ada data summary MPP untuk tahun ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($summary as $row): 
                            $totalHc = $row['jan'] + $row['feb'] + $row['mar'] + $row['apr'] + $row['may'] + $row['jun'] + $row['jul'] + $row['aug'] + $row['sep'] + $row['oct'] + $row['nov'] + $row['dec'];
                            $totalNominal = $totalHc * (float)($row['monthly_salary'] ?? 0);
                        ?>
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-5 py-3.5 font-semibold text-gray-900 dark:text-white"><?= esc($row['id_dept']) ?> - <?= esc($row['department_name'] ?? 'N/A') ?></td>
                                <td class="px-5 py-3.5 text-gray-600 dark:text-gray-300"><?= esc($row['employee_type']) ?></td>
                                <td class="px-5 py-3.5"><span class="px-2.5 py-1 text-[10px] bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-md font-mono border border-blue-200/60 dark:border-blue-800/50 font-bold"><?= esc($row['coa_code'] ?? 'Unmapped') ?></span></td>
                                <td class="px-5 py-3.5 text-right font-mono font-medium">Rp <?= number_format($row['monthly_salary'] ?? 0, 0, ',', '.') ?></td>
                                <td class="px-5 py-3.5 text-center font-mono font-bold text-gray-900 dark:text-white"><?= number_format($totalHc, 0) ?></td>
                                <td class="px-5 py-3.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">Rp <?= number_format($totalNominal, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function mppSummary() {
    return {
        syncing: false,

        async syncToOpex() {
            if (!confirm('Apakah Anda yakin ingin memproses dan mendorong (push) alokasi biaya gaji MPP ke Engine OPEX?')) return;

            this.syncing = true;
            try {
                let response = await fetch('<?= base_url('mpp/syncToOpex') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                let result = await response.json();
                alert(result.message);
                if (result.status === 'success') {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
                alert('Gagal memproses sinkronisasi OPEX.');
            } finally {
                this.syncing = false;
            }
        }
    }
}
</script>
<?= $this->endSection() ?>