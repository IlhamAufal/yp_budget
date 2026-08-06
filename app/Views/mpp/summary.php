<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="mppSummary()" class="p-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-gray-100">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Summary Headcount & OPEX Sync</h1>
            <p class="text-xs text-gray-500 mt-1">Konsolidasi Alokasi Gaji & Sync ke Engine OPEX Tahun: <span class="font-semibold text-blue-600"><?= esc($workingYear) ?></span></p>
        </div>
        <button type="button" @click="syncToOpex()" :disabled="syncing"
            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-xl transition-colors shadow-xs flex items-center space-x-2">
            <span x-show="!syncing">Process to OPEX Engine</span>
            <span x-show="syncing">Sychronizing...</span>
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h2 class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Rekapitulasi Alokasi per Department & COA</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold">
                        <th class="p-3">Department</th>
                        <th class="p-3">Tipe Karyawan</th>
                        <th class="p-3">COA Gaji</th>
                        <th class="p-3 text-right">Tarif Monthly</th>
                        <th class="p-3 text-center">Total HC (Jan-Des)</th>
                        <th class="p-3 text-right">Est. Total Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($summary)): ?>
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-400">Belum ada data summary MPP untuk tahun ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($summary as $row): 
                            $totalHc = $row['jan'] + $row['feb'] + $row['mar'] + $row['apr'] + $row['may'] + $row['jun'] + $row['jul'] + $row['aug'] + $row['sep'] + $row['oct'] + $row['nov'] + $row['dec'];
                            $totalNominal = $totalHc * (float)($row['monthly_salary'] ?? 0);
                        ?>
                            <tr class="hover:bg-gray-50/50">
                                <td class="p-3 font-medium text-gray-800"><?= esc($row['id_dept']) ?> - <?= esc($row['department_name'] ?? 'N/A') ?></td>
                                <td class="p-3 text-gray-600"><?= esc($row['employee_type']) ?></td>
                                <td class="p-3"><span class="px-2 py-0.5 text-[10px] bg-blue-50 text-blue-600 rounded font-mono"><?= esc($row['coa_code'] ?? 'Unmapped') ?></span></td>
                                <td class="p-3 text-right font-mono">Rp <?= number_format($row['monthly_salary'] ?? 0, 0, ',', '.') ?></td>
                                <td class="p-3 text-center font-bold text-gray-700"><?= number_format($totalHc, 0) ?></td>
                                <td class="p-3 text-right font-mono font-semibold text-emerald-600">Rp <?= number_format($totalNominal, 0, ',', '.') ?></td>
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