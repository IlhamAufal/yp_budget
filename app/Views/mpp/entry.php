<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="mppEntry()" class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-800">Man Power Planning</h2>
    </div>

    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-md shadow-sm relative">
        <h3 class="font-bold mb-1"><i class="fas fa-info-circle mr-2"></i> Information !</h3>
        <p class="text-sm">Periode submit data MPP dimulai pada 20 Jul 2026 s/d 31 Jul 2026</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <div class="mb-6 flex items-center space-x-4">
            <label class="font-medium text-gray-700 text-sm">Cost Center</label>
            <select x-model="selectedCostCenter" class="form-select w-64 border-gray-300 rounded-md shadow-sm text-sm">
                <option value="">-- Select Cost Center --</option>
                </select>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-600 border">
                <thead class="bg-gray-50 text-gray-700 text-center text-xs uppercase font-semibold">
                    <tr>
                        <th class="py-3 px-4 border" rowspan="2">Categories</th>
                        <th class="py-2 px-4 border border-b-0" colspan="12">Number of Headcounts</th>
                        <th class="py-3 px-4 border" rowspan="2">Total</th>
                    </tr>
                    <tr>
                        <th class="py-1 px-2 border">Jan</th>
                        <th class="py-1 px-2 border">Feb</th>
                        </tr>
                </thead>
                <tbody>
                    <template x-for="row in headcounts" :key="row.category">
                        <tr class="hover:bg-gray-50 text-center">
                            <td class="py-2 px-4 border font-medium text-left" x-text="row.category"></td>
                            <td class="py-2 px-2 border"><input type="number" x-model="row.jan" class="w-16 text-center border-gray-300 rounded text-sm"></td>
                            <td class="py-2 px-2 border"><input type="number" x-model="row.feb" class="w-16 text-center border-gray-300 rounded text-sm"></td>
                            <td class="py-2 px-4 border bg-gray-50 font-bold" x-text="calculateTotal(row)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->section('footer_scripts') ?>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mppEntry', () => ({
            selectedCostCenter: '',
            headcounts: [
                { category: 'CONTRACT', jan: 0, feb: 0 /* dst... */ },
                { category: 'PERMANENT', jan: 0, feb: 0 /* dst... */ },
                { category: 'OUTSOURCING', jan: 0, feb: 0 /* dst... */ }
            ],
            calculateTotal(row) {
                return Number(row.jan) + Number(row.feb) /* + semua bulan */;
            }
        }))
    })
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>