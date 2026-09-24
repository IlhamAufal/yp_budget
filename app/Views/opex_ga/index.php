<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ 
    search: '',
    filterDept: 'all',
    activeTab: 'summary'
}" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">

    <!-- Page Header Card -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="hover:text-[#2F3185]">OPEX GA</span>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-[#2F3185] font-bold">Summary</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white tracking-tight">OPEX General & Administrative</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ringkasan dan manajemen beban operasional umum & administrasi.</p>
        </div>
        
        <div class="flex items-center gap-3 shrink-0">
            <button type="button" data-action="open-modal"
                    data-modal-url="<?= base_url('opexga/upload-modal?type=opex_summary') ?>"
                    data-modal-title="Upload OPEX GA"
                    data-modal-size="sm"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-emerald-700 active:scale-[0.98] transition-all cursor-pointer">
                <i class="fa-solid fa-file-arrow-up"></i>
                <span>Upload Excel</span>
            </button>
            <a href="<?= base_url('opexga/exportExcel') ?>" 
               class="inline-flex items-center gap-2 rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs active:scale-[0.98] transition-all">
                <i class="fa-solid fa-file-export"></i>
                <span>Export Report</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pencarian Akun</label>
                <input type="text" x-model="search" placeholder="Pencarian akun atau deskripsi..." 
                       class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:bg-white outline-none transition">
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Department</label>
                <select x-model="filterDept" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-700 dark:text-gray-200 focus:border-[#2F3185] focus:ring-2 focus:ring-[#2F3185]/20 focus:bg-white outline-none transition">
                    <option value="all">Semua Departemen</option>
                    <option value="GA">GA / General Affair</option>
                    <option value="HR">Human Resource</option>
                    <option value="IT">IT Support</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table Section Label & Container -->
    <div class="space-y-4">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Tabel Ringkasan OPEX GA</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Alokasi anggaran per akun pengeluaran operasional umum & administrasi.</p>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs overflow-hidden dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300 border-collapse min-w-[1200px] whitespace-nowrap">
                    <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
                        <tr class="border-b border-white/20 bg-[#2F3185] text-white font-semibold">
                            <th class="border-r border-white/20 px-4 py-3 font-semibold min-w-[120px] text-white">Account No</th>
                            <th class="border-r border-white/20 px-4 py-3 font-semibold min-w-[220px] text-white">Account Name</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white">Jan</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white">Feb</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white">Mar</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white">Apr</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white">May</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white">Jun</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white">Jul</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white">Aug</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white">Sep</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white">Oct</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white">Nov</th>
                            <th class="border-r border-white/20 px-3 py-3 text-right text-white">Dec</th>
                            <th class="px-4 py-3 text-right font-bold text-white bg-[#25276d]">Total Budget</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 font-mono text-xs">
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="border-r border-gray-200 dark:border-gray-800 px-4 py-2.5 font-bold text-gray-900 dark:text-white">611001</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-4 py-2.5 font-sans font-medium text-gray-900 dark:text-white">Office Supplies & Stationeries</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2.5 text-right">0</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2.5 text-right">0</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2.5 text-right">0</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2.5 text-right">0</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2.5 text-right">0</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2.5 text-right">0</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2.5 text-right">0</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2.5 text-right">0</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2.5 text-right">0</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2.5 text-right">0</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2.5 text-right">0</td>
                            <td class="border-r border-gray-200 dark:border-gray-800 px-3 py-2.5 text-right">0</td>
                            <td class="px-4 py-2.5 text-right font-bold text-[#2F3185] dark:text-indigo-400 bg-gray-50/70 dark:bg-gray-800/50">0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>