<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ activeTab: 'department' }" class="space-y-6">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Laporan Konsolidasi CAPEX</h1>
            <p class="text-xs text-gray-500 mt-1">Rekap alokasi belanja modal & dampak penyusutan ke OPEX/FOH (<?= esc($working_year) ?>)</p>
        </div>
        <button @click="syncToOpex()" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-medium hover:bg-emerald-700 transition-colors shadow-xs flex items-center gap-2">
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

    <div x-show="activeTab === 'department'" class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="p-4">Departemen</th>
                        <th class="p-4 text-right">Total Item Aset</th>
                        <th class="p-4 text-right">Nilai Akuisisi CAPEX</th>
                        <th class="p-4 text-right">Beban Depresiasi / Tahun</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($dept_reports as $row) : ?>
                        <tr class="hover:bg-gray-50/50">
                            <td class="p-4 font-medium text-gray-900"><?= esc($row['department_name']) ?></td>
                            <td class="p-4 text-right"><?= number_format($row['total_items']) ?></td>
                            <td class="p-4 text-right font-medium text-gray-900">Rp <?= number_format($row['total_acquisition'], 0, ',', '.') ?></td>
                            <td class="p-4 text-right text-emerald-600 font-medium">Rp <?= number_format($row['annual_depreciation'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="activeTab === 'total_sync'" class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Konsolidasi Alokasi Depresiasi CAPEX ke Akun OPEX / FOH</h3>
        <p class="text-xs text-gray-500 mb-6">Nilai depresiasi bulanan di bawah ini secara otomatis dialokasikan ke beban operasional departemen terkait.</p>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="p-4">Akun Tujuan (OPEX/FOH)</th>
                        <th class="p-4 text-right">Jan - Jun</th>
                        <th class="p-4 text-right">Jul - Des</th>
                        <th class="p-4 text-right">Total Depresiasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($total_opex_sync as $sync) : ?>
                        <tr>
                            <td class="p-4 font-medium text-gray-900"><?= esc($sync['account_name']) ?></td>
                            <td class="p-4 text-right">Rp <?= number_format($sync['h1_amount'], 0, ',', '.') ?></td>
                            <td class="p-4 text-right">Rp <?= number_format($sync['h2_amount'], 0, ',', '.') ?></td>
                            <td class="p-4 text-right text-brand-600 font-bold">Rp <?= number_format($sync['total_amount'], 0, ',', '.') ?></td>
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