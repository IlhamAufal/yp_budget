<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="opexSellingDetailApp()" x-init="init()" class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <i class="fas fa-shopping-cart text-lg text-primary"></i>
                </div>
                <h2 class="text-title-md2 font-bold text-black dark:text-white">Entry Detail Budget Item</h2>
            </div>
            <nav class="mt-1">
                <ol class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    <li><a class="hover:text-primary" href="<?= base_url('dashboard') ?>"><i class="fas fa-home mr-1"></i>Home</a></li>
                    <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                    <li><a class="hover:text-primary" href="<?= base_url('opex-selling/entry-budget') ?>">Entry Budget</a></li>
                    <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                    <li class="text-primary font-semibold">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('opex-selling/entry-budget') ?>" class="inline-flex items-center gap-2 rounded bg-gray-500 px-4 py-2 text-xs font-semibold text-white hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="text-xs font-medium text-gray-500">Main Account / Description:</span>
            <h3 class="text-lg font-bold text-black dark:text-white" x-text="headerName"></h3>
        </div>
        <div>
            <span class="text-xs font-medium text-gray-500">Cost Center:</span>
            <p class="text-sm font-bold text-primary" x-text="costCenterCode"></p>
        </div>
        <div class="flex gap-2">
            <button @click="openAddItemModal()" class="inline-flex items-center gap-2 rounded bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-opacity-90 transition shadow">
                <i class="fas fa-plus"></i> Tambah Item
            </button>
            <button @click="saveDetailAll()" class="inline-flex items-center gap-2 rounded bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition shadow">
                <i class="fas fa-save"></i> Simpan Detail
            </button>
        </div>
    </div>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-6">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-left text-xs">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300">
                        <th class="border-b border-r px-3 py-2.5 font-bold uppercase text-center w-10">NO.</th>
                        <th class="border-b border-r px-3 py-2.5 font-bold uppercase">DESCRIPTION ITEM</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">JAN</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">FEB</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">MAR</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">APR</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">MAY</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">JUN</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">JUL</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">AUG</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">SEP</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">OCT</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">NOV</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">DEC</th>
                        <th class="border-b border-r px-3 py-2.5 text-center font-bold uppercase">TOTAL</th>
                        <th class="border-b px-3 py-2.5 text-center font-bold uppercase w-20">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="itemList.length === 0">
                        <tr>
                            <td colspan="16" class="border-b py-6 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox mr-1"></i>No data available in table
                            </td>
                        </tr>
                    </template>
                    <template x-for="(item, idx) in itemList" :key="idx">
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="border-b border-r px-3 py-2 text-center" x-text="idx + 1"></td>
                            <td class="border-b border-r px-3 py-2 font-medium" x-text="item.description"></td>
                            <td class="border-b border-r px-2 py-2 text-right" x-text="fmtShort(item.jan)"></td>
                            <td class="border-b border-r px-2 py-2 text-right" x-text="fmtShort(item.feb)"></td>
                            <td class="border-b border-r px-2 py-2 text-right" x-text="fmtShort(item.mar)"></td>
                            <td class="border-b border-r px-2 py-2 text-right" x-text="fmtShort(item.apr)"></td>
                            <td class="border-b border-r px-2 py-2 text-right" x-text="fmtShort(item.may)"></td>
                            <td class="border-b border-r px-2 py-2 text-right" x-text="fmtShort(item.jun)"></td>
                            <td class="border-b border-r px-2 py-2 text-right" x-text="fmtShort(item.jul)"></td>
                            <td class="border-b border-r px-2 py-2 text-right" x-text="fmtShort(item.aug)"></td>
                            <td class="border-b border-r px-2 py-2 text-right" x-text="fmtShort(item.sep)"></td>
                            <td class="border-b border-r px-2 py-2 text-right" x-text="fmtShort(item.oct)"></td>
                            <td class="border-b border-r px-2 py-2 text-right" x-text="fmtShort(item.nov)"></td>
                            <td class="border-b border-r px-2 py-2 text-right" x-text="fmtShort(item.dec)"></td>
                            <td class="border-b border-r px-2 py-2 text-right font-bold text-primary" x-text="fmtShort(item.total)"></td>
                            <td class="border-b px-2 py-2 text-center">
                                <button @click="editItem(item)" class="rounded bg-amber-500 px-2 py-1 text-xs font-semibold text-white hover:bg-amber-600 transition">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <?= $this->include('opex_selling/partials/modal_entry_detail') ?>

</div>

<script>
function opexSellingDetailApp() {
    const params = new URLSearchParams(window.location.search);
    return {
        headerName: params.get('header') || '',
        costCenterCode: params.get('dept') || '',
        idx: params.get('idx') || '1',
        itemModalOpen: false,
        editIndex: -1,
        
        formItem: {
            desc: '',
            jan: 0, feb: 0, mar: 0, apr: 0, may: 0, jun: 0,
            jul: 0, aug: 0, sep: 0, oct: 0, nov: 0, dec: 0
        },

        itemList: [],

        init() {
            this.loadMatrix();
        },

        loadMatrix() {
            const dept = this.costCenterCode;
            const header = encodeURIComponent(this.headerName);
            fetch(`<?= base_url('opex-selling/getDetailMatrix') ?>?dept=${dept}&header=${header}`)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        this.itemList = (res.matrix || []).map(m => ({
                            id: m.id,
                            id_coa: m.id_coa,
                            description: m.id_coa,
                            jan: m.b_jan, feb: m.b_feb, mar: m.b_mar,
                            apr: m.b_apr, may: m.b_may, jun: m.b_jun,
                            jul: m.b_jul, aug: m.b_aug, sep: m.b_sep,
                            oct: m.b_oct, nov: m.b_nov, dec: m.b_dec,
                            total: m.b_total
                        }));
                    }
                });
        },

        openAddItemModal() {
            this.editIndex = -1;
            this.formItem = { desc: '', jan: 0, feb: 0, mar: 0, apr: 0, may: 0, jun: 0, jul: 0, aug: 0, sep: 0, oct: 0, nov: 0, dec: 0 };
            this.itemModalOpen = true;
        },

        editItem(item) {
            this.editIndex = this.itemList.indexOf(item);
            this.formItem = { ...item };
            this.itemModalOpen = true;
        },

        saveItemModal() {
            if (this.formItem.desc.trim() === '') {
                alert('Nama barang wajib diisi!');
                return;
            }
            const total = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec']
                .reduce((s, m) => s + (parseFloat(this.formItem[m]) || 0), 0);
            this.formItem.total = total;

            if (this.editIndex >= 0) {
                this.itemList[this.editIndex] = { ...this.formItem };
            } else {
                this.itemList.push({ ...this.formItem });
            }
            this.itemModalOpen = false;
        },

        saveDetailAll() {
            if (this.itemList.length === 0) {
                alert('Tidak ada data untuk disimpan!');
                return;
            }
            const entryDataId = this.itemList[0]?.id || 0;
            const items = this.itemList.map(i => ({
                nama_barang: i.description || i.desc || i.id_coa,
                jan: i.jan, feb: i.feb, mar: i.mar, apr: i.apr, may: i.may, jun: i.jun,
                jul: i.jul, aug: i.aug, sep: i.sep, oct: i.oct, nov: i.nov, dec: i.dec
            }));

            window.ypFetch(`<?= base_url('opex-selling/saveDetailItems') ?>`, {
                entry_data_id: entryDataId,
                items: JSON.stringify(items)
            }).then(res => {
                if (res.status === 'success') {
                    alert(res.message || 'Detail berhasil disimpan!');
                    window.location.href = `<?= base_url('opex-selling/entry-budget') ?>`;
                } else {
                    alert(res.message || 'Gagal menyimpan detail.');
                }
            });
        },

        fmtShort(val) {
            const num = parseFloat(val) || 0;
            if (num === 0) return '0';
            return num.toLocaleString('id-ID');
        }
    }
}
</script>
<?= $this->endSection() ?>
