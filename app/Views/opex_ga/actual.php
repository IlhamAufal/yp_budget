<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{
    search: '',
    actualRows: [
        { account: '611001', desc: 'Atk Printer Paper & Ink', month: 'Jan', amount: 2500000 }
    ]
}" class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">OPEX Actual Realization</h2>
            <p class="text-sm text-gray-500">Actual OPEX Data Stream - Year: <span class="font-semibold text-primary"><?= esc($workingYear) ?></span></p>
        </div>
        <button @click="$dispatch('open-upload-modal', { type: 'opex_actual' })" 
                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-xs hover:bg-emerald-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Upload Actual Excel
        </button>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Account Code</th>
                        <th class="px-4 py-3 font-semibold">Description</th>
                        <th class="px-4 py-3 text-center">Month</th>
                        <th class="px-4 py-3 text-right">Actual Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="(row, i) in actualRows" :key="i">
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-mono font-medium text-gray-900" x-text="row.account"></td>
                            <td class="px-4 py-3 text-gray-800" x-text="row.desc"></td>
                            <td class="px-4 py-3 text-center uppercase font-semibold text-gray-600" x-text="row.month"></td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900" x-text="row.amount.toLocaleString()"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?= $this->include('opex_ga/upload_modal') ?>
<?= $this->endSection() ?>