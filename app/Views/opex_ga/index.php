<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ 
    search: '',
    filterDept: 'all',
    activeTab: 'summary'
}" class="p-4 md:p-6 lg:p-8 space-y-8 pb-12">

    <!-- Page Header Card -->
    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
        <div class="space-y-1.5">
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">OPEX General & Administrative</h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Ringkasan dan manajemen beban operasional umum & administrasi.</p>
        </div>
        
        <div class="flex items-center gap-3 shrink-0">
            <button @click="$dispatch('open-upload-modal', { type: 'opex_summary' })" 
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4.5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-xs hover:bg-emerald-700 active:scale-[0.98] transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload Excel
            </button>
            <a href="<?= base_url('opexga/exportExcel') ?>" 
               class="inline-flex items-center gap-2 rounded-xl bg-gray-900 dark:bg-gray-800 px-4.5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-xs hover:bg-gray-800 dark:hover:bg-gray-700 active:scale-[0.98] transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Report
            </a>
        </div>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <div class="relative w-full md:w-72">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" x-model="search" placeholder="Search account or description..." 
                   class="w-full rounded-lg border border-gray-300 bg-gray-50/60 py-2 pl-11 pr-4 text-xs md:text-sm shadow-xs focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary">
        </div>
        
        <div class="flex items-center gap-2">
            <label class="text-xs font-medium text-gray-600">Department:</label>
            <select x-model="filterDept" class="rounded-lg border border-gray-300 py-2 px-3 text-xs focus:border-primary focus:ring-primary">
                <option value="all">All Departments</option>
                <option value="GA">GA / General Affair</option>
                <option value="HR">Human Resource</option>
                <option value="IT">IT Support</option>
            </select>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold min-w-[120px]">Account No</th>
                        <th class="px-4 py-3 font-semibold min-w-[200px]">Account Name</th>
                        <th class="px-3 py-3 text-right">Jan</th>
                        <th class="px-3 py-3 text-right">Feb</th>
                        <th class="px-3 py-3 text-right">Mar</th>
                        <th class="px-3 py-3 text-right">Apr</th>
                        <th class="px-3 py-3 text-right">May</th>
                        <th class="px-3 py-3 text-right">Jun</th>
                        <th class="px-3 py-3 text-right">Jul</th>
                        <th class="px-3 py-3 text-right">Aug</th>
                        <th class="px-3 py-3 text-right">Sep</th>
                        <th class="px-3 py-3 text-right">Oct</th>
                        <th class="px-3 py-3 text-right">Nov</th>
                        <th class="px-3 py-3 text-right">Dec</th>
                        <th class="px-4 py-3 text-right font-bold bg-gray-100">Total Budget</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-medium text-gray-900">611001</td>
                        <td class="px-4 py-3 font-medium text-gray-900">Office Supplies & Stationeries</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-3 py-3 text-right">0</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 bg-gray-50">0</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?= $this->include('opex_ga/upload_modal') ?>
<?= $this->endSection() ?>