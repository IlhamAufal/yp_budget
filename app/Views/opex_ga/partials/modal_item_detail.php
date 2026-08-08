<div x-show="itemModalOpen" class="fixed inset-0 z-[999999] flex items-center justify-center p-4" x-cloak>
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="itemModalOpen = false"></div>

    <div class="relative w-full max-w-xl rounded-lg bg-white p-6 shadow-2xl dark:bg-boxdark">
        <div class="mb-4 flex items-center justify-between border-b pb-3 dark:border-strokedark">
            <h3 class="text-base font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-edit text-primary"></i> Form Rincian Item Budget
            </h3>
            <button @click="itemModalOpen = false" class="text-gray-400 hover:text-black dark:hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="space-y-4 text-xs">
            <div>
                <label class="mb-1.5 block font-bold text-black dark:text-white">Deskripsi Rincian Item</label>
                <input type="text" x-model="formItem.nama_barang" placeholder="Contoh: Gaji Pokok Staff GA"
                    class="w-full rounded border border-stroke bg-white px-3 py-2 outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <template x-for="m in ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec']" :key="m">
                    <div>
                        <label class="mb-1 block uppercase font-semibold text-gray-600 dark:text-gray-400" x-text="m"></label>
                        <input type="number" x-model="formItem[m]" class="w-full rounded border border-stroke bg-white px-2 py-1 text-right outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                    </div>
                </template>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2 border-t pt-3 dark:border-strokedark">
            <button @click="itemModalOpen = false" class="rounded px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300">
                Batal
            </button>
            <button @click="saveItemModal()" class="rounded bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-opacity-90">
                <i class="fas fa-save mr-1"></i> Simpan Item
            </button>
        </div>
    </div>
</div>
