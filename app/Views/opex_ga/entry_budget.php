<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{
    rows: [
        { account: '611001', desc: 'Stationery & ATK', jan: 0, feb: 0, mar: 0, apr: 0, may: 0, jun: 0, jul: 0, aug: 0, sep: 0, oct: 0, nov: 0, dec: 0 }
    ],
    addRow() {
        this.rows.push({ account: '', desc: '', jan:0, feb:0, mar:0, apr:0, may:0, jun:0, jul:0, aug:0, sep:0, oct:0, nov:0, dec:0 });
    },
    removeRow(idx) {
        this.rows.splice(idx, 1);
    },
    getRowTotal(r) {
        return Number(r.jan)+Number(r.feb)+Number(r.mar)+Number(r.apr)+Number(r.may)+Number(r.jun)+Number(r.jul)+Number(r.aug)+Number(r.sep)+Number(r.oct)+Number(r.nov)+Number(r.dec);
    }
}" class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Entry Budget OPEX GA</h2>
            <p class="text-sm text-gray-500">Input & Monthly Breakdown allocation for Working Year <?= esc($workingYear) ?></p>
        </div>
        <div class="flex gap-3">
            <button @click="addRow()" class="inline-flex items-center gap-2 rounded-lg bg-gray-100 border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 shadow-xs hover:bg-gray-200 transition-colors">
                + Add Account
            </button>
            <button class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white shadow-xs hover:bg-primary-dark transition-colors">
                Save Budget
            </button>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-3 py-3 w-32">Account</th>
                        <th class="px-3 py-3 min-w-[180px]">Description</th>
                        <th class="px-2 py-3 text-right">Jan</th>
                        <th class="px-2 py-3 text-right">Feb</th>
                        <th class="px-2 py-3 text-right">Mar</th>
                        <th class="px-2 py-3 text-right">Apr</th>
                        <th class="px-2 py-3 text-right">May</th>
                        <th class="px-2 py-3 text-right">Jun</th>
                        <th class="px-2 py-3 text-right">Jul</th>
                        <th class="px-2 py-3 text-right">Aug</th>
                        <th class="px-2 py-3 text-right">Sep</th>
                        <th class="px-2 py-3 text-right">Oct</th>
                        <th class="px-2 py-3 text-right">Nov</th>
                        <th class="px-2 py-3 text-right">Dec</th>
                        <th class="px-3 py-3 text-right font-bold bg-gray-100">Total</th>
                        <th class="px-2 py-3 text-center w-12">#</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="(row, idx) in rows" :key="idx">
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-2 py-2">
                                <input type="text" x-model="row.account" class="w-full rounded border border-gray-300 px-2 py-1 text-xs font-mono">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" x-model="row.desc" class="w-full rounded border border-gray-300 px-2 py-1 text-xs">
                            </td>
                            <td class="px-1 py-2"><input type="number" x-model="row.jan" class="w-full rounded border border-gray-300 px-1 py-1 text-xs text-right"></td>
                            <td class="px-1 py-2"><input type="number" x-model="row.feb" class="w-full rounded border border-gray-300 px-1 py-1 text-xs text-right"></td>
                            <td class="px-1 py-2"><input type="number" x-model="row.mar" class="w-full rounded border border-gray-300 px-1 py-1 text-xs text-right"></td>
                            <td class="px-1 py-2"><input type="number" x-model="row.apr" class="w-full rounded border border-gray-300 px-1 py-1 text-xs text-right"></td>
                            <td class="px-1 py-2"><input type="number" x-model="row.may" class="w-full rounded border border-gray-300 px-1 py-1 text-xs text-right"></td>
                            <td class="px-1 py-2"><input type="number" x-model="row.jun" class="w-full rounded border border-gray-300 px-1 py-1 text-xs text-right"></td>
                            <td class="px-1 py-2"><input type="number" x-model="row.jul" class="w-full rounded border border-gray-300 px-1 py-1 text-xs text-right"></td>
                            <td class="px-1 py-2"><input type="number" x-model="row.aug" class="w-full rounded border border-gray-300 px-1 py-1 text-xs text-right"></td>
                            <td class="px-1 py-2"><input type="number" x-model="row.sep" class="w-full rounded border border-gray-300 px-1 py-1 text-xs text-right"></td>
                            <td class="px-1 py-2"><input type="number" x-model="row.oct" class="w-full rounded border border-gray-300 px-1 py-1 text-xs text-right"></td>
                            <td class="px-1 py-2"><input type="number" x-model="row.nov" class="w-full rounded border border-gray-300 px-1 py-1 text-xs text-right"></td>
                            <td class="px-1 py-2"><input type="number" x-model="row.dec" class="w-full rounded border border-gray-300 px-1 py-1 text-xs text-right"></td>
                            <td class="px-3 py-2 text-right font-bold text-gray-900 bg-gray-50" x-text="getRowTotal(row).toLocaleString()"></td>
                            <td class="px-2 py-2 text-center">
                                <button @click="removeRow(idx)" class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?= $this->endSection() ?>