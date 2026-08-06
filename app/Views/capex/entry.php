<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="capexEntryHandler()" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Form Pengajuan CAPEX</h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Input dan kelola rincian rencana alokasi belanja modal (CAPEX).</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openModal = true" class="px-4.5 py-2.5 bg-primary text-white rounded-xl text-xs sm:text-sm font-semibold hover:bg-primary-dark active:scale-[0.98] transition-all shadow-xs flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Aset CAPEX
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-200/80 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50/50 dark:bg-gray-800/20">
            <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">Daftar Rencana Belanja Modal (CAPEX)</h2>
            <div class="relative min-w-[240px] w-full sm:w-auto">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Cari deskripsi / aset..." class="w-full pl-11 pr-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-100/80 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 text-[11px] font-bold tracking-wider uppercase border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-5 py-3.5">Deskripsi Aset</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-4 py-3.5 text-center">Bulan Akuisisi</th>
                        <th class="px-4 py-3.5 text-center">Masa Manfaat</th>
                        <th class="px-5 py-3.5 text-right">Nilai Akuisisi</th>
                        <th class="px-5 py-3.5 text-right">Depresiasi / Bln</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/80 dark:divide-gray-800/80 text-gray-700 dark:text-gray-300">
                    <template x-for="(item, index) in filteredItems" :key="index">
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-5 py-3.5 font-semibold text-gray-900 dark:text-white" x-text="item.asset_description"></td>
                            <td class="px-5 py-3.5" x-text="item.category_name"></td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/50 rounded-lg text-[11px] font-bold" x-text="'Bulan ' + item.acquisition_month"></span>
                            </td>
                            <td class="px-4 py-3.5 text-center font-medium" x-text="item.useful_life_years + ' Tahun'"></td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-gray-900 dark:text-white" x-text="formatCurrency(item.acquisition_cost)"></td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="formatCurrency(item.monthly_depreciation)"></td>
                            <td class="px-4 py-3.5 text-center">
                                <button @click="editItem(item)" class="text-gray-400 dark:text-gray-500 hover:text-primary dark:hover:text-primary p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredItems.length === 0">
                        <td colspan="7" class="p-16 text-center text-gray-400 dark:text-gray-500">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3 text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <p class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300">Belum Ada Rencana Belanja Modal</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Klik tombol "+ Tambah Aset CAPEX" di atas untuk menambahkan item belanja modal.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Dialog CAPEX -->
    <div x-show="openModal" class="fixed inset-0 z-[9999999] overflow-y-auto" x-cloak>
        <div class="fixed inset-0" style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);" @click="openModal = false"></div>
        <div class="relative flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-2xl shadow-xl overflow-hidden z-10">
            <div class="p-5 bg-gray-50 dark:bg-gray-800/60 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white" x-text="form.id ? 'Edit Item CAPEX' : 'Tambah Item CAPEX Baru'"></h3>
                <button @click="openModal = false" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form @submit.prevent="submitForm()" class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Deskripsi Aset / Barang</label>
                    <input type="text" x-model="form.asset_description" required class="w-full px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kategori Aset</label>
                        <select x-model="form.category_id" required class="w-full px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $cat) : ?>
                                <option value="<?= $cat['id'] ?>"><?= esc($cat['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Bulan Akuisisi</label>
                        <select x-model="form.acquisition_month" required class="w-full px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <?php for ($m = 1; $m <= 12; $m++) : ?>
                                <option value="<?= $m ?>">Bulan <?= $m ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Nilai Akuisisi (Rp)</label>
                        <input type="number" x-model.number="form.acquisition_cost" @input="calculateDepreciation()" required class="w-full px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white font-mono rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Masa Manfaat (Tahun)</label>
                        <input type="number" x-model.number="form.useful_life_years" @input="calculateDepreciation()" min="1" required class="w-full px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white font-mono rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                </div>

                <div class="p-3 bg-primary/10 border border-primary/20 rounded-xl flex justify-between items-center">
                    <span class="text-xs text-primary font-semibold">Estimasi Depresiasi / Bulan:</span>
                    <span class="text-sm font-extrabold font-mono text-primary" x-text="formatCurrency(form.monthly_depreciation)"></span>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="openModal = false" class="px-4 py-2 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-xl text-xs font-medium hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">Batal</button>
                    <button type="submit" class="px-4.5 py-2 bg-primary text-white rounded-xl text-xs font-semibold hover:bg-primary-dark active:scale-[0.98] transition-all">Simpan Aset</button>
                </div>
            </form>
            </div>
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