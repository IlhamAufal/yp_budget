<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="capexEntryHandler()" class="space-y-6">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Form Pengajuan CAPEX</h1>
            <p class="text-xs text-gray-500 mt-1">Tahun Anggaran: <span class="font-semibold text-brand-600"><?= esc($working_year) ?></span></p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openModal = true" class="px-4 py-2 bg-brand-600 text-white rounded-xl text-xs font-medium hover:bg-brand-700 transition-colors shadow-xs flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Aset CAPEX
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-sm font-semibold text-gray-800">Daftar Rencana Belanja Modal (CAPEX)</h2>
            <div class="relative min-w-[240px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Cari deskripsi / aset..." class="w-full pl-10 pr-4 py-2 bg-gray-50/60 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="p-4">Deskripsi Aset</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4 text-center">Bulan Akuisisi</th>
                        <th class="p-4 text-center">Masa Manfaat</th>
                        <th class="p-4 text-right">Nilai Akuisisi</th>
                        <th class="p-4 text-right">Depresiasi / Bln</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    <template x-for="(item, index) in filteredItems" :key="index">
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="p-4 font-medium text-gray-900" x-text="item.asset_description"></td>
                            <td class="p-4" x-text="item.category_name"></td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-[11px] font-medium" x-text="'Bulan ' + item.acquisition_month"></span>
                            </td>
                            <td class="p-4 text-center" x-text="item.useful_life_years + ' Tahun'"></td>
                            <td class="p-4 text-right font-medium text-gray-900" x-text="formatCurrency(item.acquisition_cost)"></td>
                            <td class="p-4 text-right text-emerald-600 font-medium" x-text="formatCurrency(item.monthly_depreciation)"></td>
                            <td class="p-4 text-center">
                                <button @click="editItem(item)" class="text-gray-400 hover:text-brand-600 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredItems.length === 0">
                        <td colspan="7" class="p-8 text-center text-gray-400">Belum ada rencana belanja modal yang diinput.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="openModal" class="fixed inset-0 flex items-center justify-center p-4 z-[999999]" x-cloak>
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs" @click="openModal = false"></div>
        <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden z-10">
            <div class="p-5 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-sm font-bold text-gray-800" x-text="form.id ? 'Edit Item CAPEX' : 'Tambah Item CAPEX Baru'"></h3>
                <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form @submit.prevent="submitForm()" class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi Aset / Barang</label>
                    <input type="text" x-model="form.asset_description" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kategori Aset</label>
                        <select x-model="form.category_id" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $cat) : ?>
                                <option value="<?= $cat['id'] ?>"><?= esc($cat['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bulan Akuisisi</label>
                        <select x-model="form.acquisition_month" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                            <?php for ($m = 1; $m <= 12; $m++) : ?>
                                <option value="<?= $m ?>">Bulan <?= $m ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nilai Akuisisi (Rp)</label>
                        <input type="number" x-model.number="form.acquisition_cost" @input="calculateDepreciation()" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Masa Manfaat (Tahun)</label>
                        <input type="number" x-model.number="form.useful_life_years" @input="calculateDepreciation()" min="1" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                    </div>
                </div>

                <div class="p-3 bg-brand-50/50 border border-brand-100 rounded-xl flex justify-between items-center">
                    <span class="text-xs text-brand-700 font-medium">Estimasi Depresiasi / Bulan:</span>
                    <span class="text-sm font-bold text-brand-700" x-text="formatCurrency(form.monthly_depreciation)"></span>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="openModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-xs hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl text-xs font-medium hover:bg-brand-700">Simpan Aset</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function capexEntryHandler() {
    return {
        searchQuery: '',
        openModal: false,
        items: <?= json_encode($capex_items ?? []) ?>,
        form: {
            id: null,
            asset_description: '',
            category_id: '',
            acquisition_month: 1,
            acquisition_cost: 0,
            useful_life_years: 4,
            monthly_depreciation: 0
        },
        get filteredItems() {
            if (!this.searchQuery) return this.items;
            return this.items.filter(i => i.asset_description.toLowerCase().includes(this.searchQuery.toLowerCase()));
        },
        calculateDepreciation() {
            if (this.form.acquisition_cost > 0 && this.form.useful_life_years > 0) {
                this.form.monthly_depreciation = Math.round(this.form.acquisition_cost / (this.form.useful_life_years * 12));
            } else {
                this.form.monthly_depreciation = 0;
            }
        },
        editItem(item) {
            this.form = { ...item };
            this.openModal = true;
        },
        formatCurrency(val) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
        },
        async submitForm() {
            const response = await fetch('<?= base_url('capex/save_capex') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams(this.form)
            });
            const res = await response.json();
            if (res.status === 'success') {
                location.reload();
            }
        }
    }
}
</script>
<?= $this->endSection() ?>