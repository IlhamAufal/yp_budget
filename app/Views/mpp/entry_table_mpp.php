<div x-data="mppTableData()" class="p-5 space-y-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold">
                    <th class="p-3 w-40 sticky left-0 bg-gray-50 z-10">Tipe Karyawan</th>
                    <th class="p-3 text-center min-w-[60px]">Jan</th>
                    <th class="p-3 text-center min-w-[60px]">Feb</th>
                    <th class="p-3 text-center min-w-[60px]">Mar</th>
                    <th class="p-3 text-center min-w-[60px]">Apr</th>
                    <th class="p-3 text-center min-w-[60px]">May</th>
                    <th class="p-3 text-center min-w-[60px]">Jun</th>
                    <th class="p-3 text-center min-w-[60px]">Jul</th>
                    <th class="p-3 text-center min-w-[60px]">Aug</th>
                    <th class="p-3 text-center min-w-[60px]">Sep</th>
                    <th class="p-3 text-center min-w-[60px]">Oct</th>
                    <th class="p-3 text-center min-w-[60px]">Nov</th>
                    <th class="p-3 text-center min-w-[60px]">Dec</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="(row, index) in rows" :key="index">
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-3 font-medium text-gray-700 sticky left-0 bg-white z-10 border-r border-gray-100" x-text="row.employee_type"></td>
                        <template x-for="m in 12" :key="m">
                            <td class="p-2">
                                <input type="number" min="0" x-model.number="row['m' + m]" 
                                    class="w-full text-center text-xs p-1.5 border border-gray-200 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </td>
                        </template>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="flex justify-end pt-3">
        <button type="button" @click="saveData()" :disabled="saving"
            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-xl transition-colors shadow-xs flex items-center space-x-2">
            <span x-show="!saving">Simpan Alokasi MPP</span>
            <span x-show="saving">Menyimpan...</span>
        </button>
    </div>
</div>

<script>
function mppTableData() {
    return {
        saving: false,
        rows: <?= json_encode(!empty($entryData) ? $entryData : [
            ['employee_type' => 'Helper', 'm1'=>0,'m2'=>0,'m3'=>0,'m4'=>0,'m5'=>0,'m6'=>0,'m7'=>0,'m8'=>0,'m9'=>0,'m10'=>0,'m11'=>0,'m12'=>0],
            ['employee_type' => 'Driver', 'm1'=>0,'m2'=>0,'m3'=>0,'m4'=>0,'m5'=>0,'m6'=>0,'m7'=>0,'m8'=>0,'m9'=>0,'m10'=>0,'m11'=>0,'m12'=>0],
            ['employee_type' => 'Staff', 'm1'=>0,'m2'=>0,'m3'=>0,'m4'=>0,'m5'=>0,'m6'=>0,'m7'=>0,'m8'=>0,'m9'=>0,'m10'=>0,'m11'=>0,'m12'=>0],
            ['employee_type' => 'Supervisor', 'm1'=>0,'m2'=>0,'m3'=>0,'m4'=>0,'m5'=>0,'m6'=>0,'m7'=>0,'m8'=>0,'m9'=>0,'m10'=>0,'m11'=>0,'m12'=>0],
            ['employee_type' => 'Manager', 'm1'=>0,'m2'=>0,'m3'=>0,'m4'=>0,'m5'=>0,'m6'=>0,'m7'=>0,'m8'=>0,'m9'=>0,'m10'=>0,'m11'=>0,'m12'=>0],
        ]) ?>,

        async saveData() {
            this.saving = true;
            let formData = new FormData();
            formData.append('id_dept', this.$root.closest('[x-data]').selectedDept);
            formData.append('is_newlines', this.$root.closest('[x-data]').isNewlines);
            
            this.rows.forEach((row, i) => {
                formData.append(`details[${i}][employee_type]`, row.employee_type);
                for (let m = 1; m <= 12; m++) {
                    formData.append(`details[${i}][m${m}]`, row[`m${m}`] || 0);
                }
            });

            try {
                let response = await fetch('<?= base_url('mpp/saveBudget') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                let result = await response.json();
                alert(result.message);
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan koneksi.');
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>