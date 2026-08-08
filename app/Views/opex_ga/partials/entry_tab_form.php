<div class="space-y-6">

    <div class="rounded-sm border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 text-red-600 dark:text-red-400">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-red-800 dark:text-red-300">Peringatan / Warning Information:</h4>
                    <ul class="mt-1 list-disc pl-4 text-xs text-red-700 dark:text-red-300/80 space-y-1">
                        <li>Pastikan Anda memilih <strong>Cost Center</strong> yang sesuai sebelum mengisi alokasi anggaran.</li>
                        <li>Klik tombol <strong>"Entry"</strong> pada baris akun terkait untuk memasukkan/memperbarui rincian item budget.</li>
                        <li>Jangan lupa menekan tombol <strong>Simpan Detail</strong> di halaman rincian agar perubahan tersimpan permanen.</li>
                    </ul>
                </div>
            </div>

            <button @click="manualBookModalOpen = true"
                class="inline-flex shrink-0 items-center gap-1.5 rounded bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow hover:bg-red-700 transition">
                <i class="fas fa-book"></i> Manual Book
            </button>
        </div>
    </div>

    <div class="rounded-sm border border-stroke bg-gray-50 p-4 dark:border-strokedark dark:bg-meta-4">
        <label class="mb-2 block text-sm font-semibold text-black dark:text-white">Cost Center</label>
        <div class="relative w-full max-w-2xl">
            <select x-model="selectedEntryCc" @change="fetchEntryData()"
                class="w-full rounded border border-stroke bg-white px-4 py-2.5 text-sm font-medium outline-none transition focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                <option value="">-- Pilih Cost Center --</option>
                <template x-for="item in costCenters" :key="item.id">
                    <option :value="item.id" x-text="item.text"></option>
                </template>
            </select>
        </div>
    </div>

    <div class="flex items-center justify-between text-xs text-gray-600">
        <div class="flex items-center gap-1">
            <span>Show</span>
            <select class="rounded border border-stroke bg-white px-2 py-1 dark:border-strokedark dark:bg-boxdark">
                <option>25</option>
                <option>50</option>
            </select>
            <span>entries</span>
        </div>
        <div class="flex items-center gap-2">
            <label class="font-medium">Search:</label>
            <input type="text" class="rounded border border-stroke bg-white px-3 py-1 text-xs outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
        </div>
    </div>

    <div class="max-w-full overflow-x-auto border border-stroke dark:border-strokedark">
        <table class="w-full table-auto text-left text-xs">
            <thead>
                <tr class="bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300">
                    <th class="border-b border-r px-4 py-3 font-bold uppercase w-12 text-center">NO.</th>
                    <th class="border-b border-r px-4 py-3 font-bold uppercase w-36">MAIN ACCOUNT</th>
                    <th class="border-b border-r px-4 py-3 font-bold uppercase">DESCRIPTION</th>
                    <th class="border-b border-r px-4 py-3 font-bold uppercase text-right w-44">TOTAL BUDGET</th>
                    <th class="border-b px-4 py-3 font-bold uppercase text-center w-32">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="entryAccounts.length === 0">
                    <tr>
                        <td colspan="5" class="border-b py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox mr-1"></i>No data available in table
                        </td>
                    </tr>
                </template>
                <template x-for="(row, idx) in entryAccounts" :key="row.idx">
                    <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="border-b border-r px-4 py-2.5 text-center" x-text="idx + 1"></td>
                        <td class="border-b border-r px-4 py-2.5 font-semibold text-primary" x-text="row.main_account"></td>
                        <td class="border-b border-r px-4 py-2.5 font-medium" x-text="row.description"></td>
                        <td class="border-b border-r px-4 py-2.5 text-right font-bold" x-text="'Rp ' + row.total_budget"></td>
                        <td class="border-b px-4 py-2.5 text-center">
                            <button @click="goToDetail(row)"
                                class="inline-flex items-center gap-1 rounded bg-emerald-600 px-3 py-1 text-xs font-semibold text-white hover:bg-emerald-700 transition shadow">
                                <i class="fas fa-edit"></i> Entry
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
