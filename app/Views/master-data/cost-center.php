<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-4" x-data="costCenterPage()">

  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors">
          <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span>Master Data</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-brand-500 font-bold">Cost Center</span>
      </div>
      <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400 shadow-xs">
          <i class="fa-solid fa-sitemap text-lg"></i>
        </span>
        Master Cost Center
      </h1>
      <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
        Kelola daftar Cost Center, alokasi departemen, dan pusat pertanggungjawaban anggaran.
      </p>
    </div>
  </div>

  <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-xl border border-gray-200/80 bg-white px-4 py-2.5 text-xs dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-purple-50 text-purple-500 dark:bg-purple-500/10 dark:text-purple-400">
        <i class="fa-solid fa-sitemap text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Total CC:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= count($costCenters ?? []) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400">
        <i class="fa-solid fa-circle-check text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Aktif:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= count(array_filter($costCenters ?? [], fn($c) => ($c['status'] ?? 'A') === 'A')) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-rose-50 text-rose-500 dark:bg-rose-500/10 dark:text-rose-400">
        <i class="fa-solid fa-circle-xmark text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Non-Aktif:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= count(array_filter($costCenters ?? [], fn($c) => ($c['status'] ?? '') === 'N')) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-50 text-blue-500 dark:bg-blue-500/10 dark:text-blue-400">
        <i class="fa-solid fa-building text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Departemen:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= count($departments ?? []) ?></span>
    </div>
  </div>

  <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs overflow-hidden dark:border-gray-800 dark:bg-gray-900">
    <div class="p-3 border-b border-gray-100 dark:border-gray-800">
      <div class="flex flex-wrap items-center gap-2">
        <div class="relative flex-1 min-w-[180px]">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400"></i>
          <input
            type="text"
            x-model="searchQuery"
            @input="currentPage = 1"
            placeholder="Cari kode atau nama cost center..."
            class="w-full rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 pl-9 pr-3 text-xs text-gray-900 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-all"
          />
        </div>
        <select
          x-model="selectedDepartment"
          @change="currentPage = 1"
          class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white min-w-[120px] transition-all"
        >
          <option value="">Semua Departemen</option>
          <?php foreach ($departments ?? [] as $dept): ?>
            <option value="<?= esc($dept['department_id'] ?? $dept['id'] ?? '') ?>">
              <?= esc($dept['department_name'] ?? $dept['name'] ?? '') ?>
            </option>
          <?php endforeach; ?>
        </select>
        <select
          x-model="selectedStatus"
          @change="currentPage = 1"
          class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white min-w-[120px] transition-all"
        >
          <option value="">Semua Status</option>
          <option value="A">Aktif</option>
          <option value="N">Non-Aktif</option>
        </select>
        <button
          x-show="searchQuery !== '' || selectedDepartment !== '' || selectedStatus !== ''"
          x-cloak
          @click="resetFilters()"
          class="rounded-lg border border-gray-200 py-1.5 px-2.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-all"
          title="Reset Filter"
        >
          <i class="fa-solid fa-rotate-left text-[10px]"></i>
        </button>
      </div>
    </div>
    
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
        <thead class="bg-gray-50/80 text-[11px] font-bold capitalize tracking-normal text-gray-500 dark:bg-gray-800/60 dark:text-gray-400 border-b border-gray-200/80 dark:border-gray-800">
          <tr>
            <th scope="col" class="px-5 py-3.5 w-12 text-center">No</th>
            <th scope="col" class="px-5 py-3.5 w-36">Code</th>
            <th scope="col" class="px-5 py-3.5">Cost Center Desc</th>
            <th scope="col" class="px-5 py-3.5 w-28 text-center">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <template x-for="(row, index) in paginatedCostCenters" :key="row.id || index">
            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
              <td class="px-5 py-3.5 text-center font-medium text-gray-400" x-text="(currentPage - 1) * perPage + index + 1"></td>
              
              <td class="px-5 py-3.5 font-mono font-bold text-brand-600 dark:text-brand-400" x-text="row.cost_center_sap"></td>
              
              <td class="px-5 py-3.5 font-semibold text-gray-900 dark:text-white" x-text="row.cost_desc"></td>

              <td class="px-5 py-3.5 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    @click="openEditModal(row)"
                    title="Edit Cost Center"
                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-gray-100 text-gray-600 hover:bg-brand-50 hover:text-brand-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-brand-900/30 dark:hover:text-brand-400 transition-colors"
                  >
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                  </button>

                  <button
                    @click="toggleStatus(row.id, 'hapus')"
                    title="Hapus Cost Center"
                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors"
                  >
                    <i class="fa-solid fa-trash text-xs"></i>
                  </button>
                </div>
              </td>
            </tr>
          </template>

          <tr x-show="filteredCostCenters.length === 0">
            <td colspan="4" class="px-5 py-12 text-center">
              <div class="flex flex-col items-center justify-center gap-2">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                  <i class="fa-solid fa-folder-open text-xl"></i>
                </div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Tidak Ada Data Cost Center</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm">
                  Tidak ditemukan data yang sesuai dengan kriteria pencarian atau filter Anda.
                </p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="border-t border-gray-100 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
      <div class="flex items-center gap-1.5 text-xs">
        <span>Menampilkan</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="filteredCostCenters.length === 0 ? 0 : ((currentPage - 1) * perPage + 1)"></span>
        <span>-</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, filteredCostCenters.length)"></span>
        <span>dari</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="filteredCostCenters.length"></span>
        <span>Cost Center</span>
      </div>

      <div class="flex items-center gap-1" x-show="totalPages > 1">
        <!-- Prev -->
        <button
          type="button"
          @click="prevPage()"
          :disabled="currentPage === 1"
          class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-xs font-semibold flex items-center gap-1"
        >
          <i class="fa-solid fa-chevron-left text-[10px]"></i>
          <span class="hidden sm:inline">Sebelumnya</span>
        </button>

        <!-- Page Numbers -->
        <template x-for="(p, idx) in pageNumbers()" :key="idx">
          <div>
            <template x-if="p === '...'">
              <span class="px-2 py-1 text-gray-400 font-bold">...</span>
            </template>
            <template x-if="p !== '...'">
              <button
                type="button"
                @click="goToPage(p)"
                :class="currentPage === p ? 'bg-brand-500 text-white font-bold shadow-xs' : 'border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                class="h-8 min-w-[32px] px-2 rounded-lg text-xs font-semibold transition-colors flex items-center justify-center"
                x-text="p"
              ></button>
            </template>
          </div>
        </template>

        <!-- Next -->
        <button
          type="button"
          @click="nextPage()"
          :disabled="currentPage === totalPages"
          class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-xs font-semibold flex items-center gap-1"
        >
          <span class="hidden sm:inline">Berikutnya</span>
          <i class="fa-solid fa-chevron-right text-[10px]"></i>
        </button>
      </div>
    </div>
  </div>

  <div
    x-show="modalOpen"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-xs p-4 overflow-y-auto"
  >
    <div
      @click.away="modalOpen = false"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0 scale-95"
      x-transition:enter-end="opacity-100 scale-100"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100 scale-100"
      x-transition:leave-end="opacity-0 scale-95"
      class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
    >
      <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
          <i class="fa-solid text-brand-500" :class="isEdit ? 'fa-pen-to-square' : 'fa-plus'"></i>
          <span x-text="isEdit ? 'Edit Cost Center' : 'Tambah Cost Center'"></span>
        </h3>
        <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <form @submit.prevent="saveCostCenter()" class="mt-4 space-y-4">
        
        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
            Kode Cost Center <span class="text-rose-500">*</span>
          </label>
          <input
            type="text"
            x-model="form.cost_center_sap"
            required
            placeholder="Contoh: CC-101"
            class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-all font-mono"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
            Nama Cost Center <span class="text-rose-500">*</span>
          </label>
          <input
            type="text"
            x-model="form.cost_desc"
            required
            placeholder="Contoh: Operational General & Admin"
            class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-all"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
            Departemen Terkait <span class="text-rose-500">*</span>
          </label>
          <select
            x-model="form.department_id"
            required
            class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-all"
          >
            <option value="">-- Pilih Departemen --</option>
            <?php foreach ($departments ?? [] as $dept): ?>
              <option value="<?= esc($dept['department_id'] ?? $dept['id'] ?? '') ?>">
                <?= esc($dept['department_name'] ?? $dept['name'] ?? '') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
            Status
          </label>
          <select
            x-model="form.status"
            class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-all"
          >
            <option value="A">Aktif</option>
            <option value="N">Non-Aktif</option>
          </select>
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100 dark:border-gray-800">
          <button
            type="button"
            @click="modalOpen = false"
            class="rounded-xl border border-gray-200 px-4 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-all"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand-600 focus:outline-hidden focus:ring-2 focus:ring-brand-500/20 disabled:opacity-50 transition-all cursor-pointer"
          >
            <i class="fa-solid" :class="saving ? 'fa-spinner fa-spin' : 'fa-check'"></i>
            <span x-text="saving ? 'Menyimpan...' : 'Simpan Data'"></span>
          </button>
        </div>

      </form>
    </div>
  </div>

</div>

<script>
  function costCenterPage() {
    return {
      rawCostCenters: <?= json_encode($costCenters ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
      searchQuery: '',
      selectedDepartment: '',
      selectedStatus: '',

      // Pagination
      currentPage: 1,
      perPage: 10,

      // Modal State
      modalOpen: false,
      isEdit: false,
      saving: false,
      form: {
        id: null,
        cost_center_sap: '',
        cost_desc: '',
        department_id: '',
        status: 'A'
      },

      get filteredCostCenters() {
        return this.rawCostCenters.filter(item => {
          const q = (this.searchQuery || '').toLowerCase();
          const matchesSearch = !q || 
            (item.cost_center_sap && item.cost_center_sap.toLowerCase().includes(q)) ||
            (item.cost_desc && item.cost_desc.toLowerCase().includes(q));

          const matchesDept = !this.selectedDepartment || 
            String(item.department_id) === String(this.selectedDepartment);

          const matchesStatus = !this.selectedStatus || 
            item.status === this.selectedStatus;

          return matchesSearch && matchesDept && matchesStatus;
        });
      },

      get totalPages() {
        return Math.ceil(this.filteredCostCenters.length / this.perPage) || 1;
      },

      get paginatedCostCenters() {
        const start = (this.currentPage - 1) * this.perPage;
        return this.filteredCostCenters.slice(start, start + this.perPage);
      },

      goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
          this.currentPage = page;
        }
      },
      prevPage() {
        if (this.currentPage > 1) {
          this.currentPage--;
        }
      },
      nextPage() {
        if (this.currentPage < this.totalPages) {
          this.currentPage++;
        }
      },
      pageNumbers() {
        const total = this.totalPages;
        const current = this.currentPage;
        if (total <= 7) {
          return Array.from({ length: total }, (_, i) => i + 1);
        }
        if (current <= 4) {
          return [1, 2, 3, 4, 5, '...', total];
        }
        if (current >= total - 3) {
          return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
        }
        return [1, '...', current - 1, current, current + 1, '...', total];
      },

      resetFilters() {
        this.searchQuery = '';
        this.selectedDepartment = '';
        this.selectedStatus = '';
        this.currentPage = 1;
      },

      openAddModal() {
        this.isEdit = false;
        this.form = {
          id: null,
          cost_center_sap: '',
          cost_desc: '',
          department_id: '',
          status: 'A'
        };
        this.modalOpen = true;
      },

      openEditModal(row) {
        this.isEdit = true;
        this.form = {
          id: row.id || row.cost_center_id || null,
          cost_center_sap: row.cost_center_sap || '',
          cost_desc: row.cost_desc || '',
          department_id: row.department_id || '',
          status: row.status || 'A'
        };
        this.modalOpen = true;
      },

      async saveCostCenter() {
        this.saving = true;
        try {
          const res = await window.ypFetch('<?= base_url('master/api/cost-center/save') ?>', this.form);
          if (res.success) {
            window.showToast('success', res.message || 'Cost Center berhasil disimpan');
            this.modalOpen = false;
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal menyimpan cost center');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan sistem');
        } finally {
          this.saving = false;
        }
      },

      async toggleStatus(id, actionText) {
        if (!window.ypConfirm(`Apakah Anda yakin ingin ${actionText} Cost Center ini?`)) {
          return;
        }

        try {
          const res = await window.ypFetch('<?= base_url('master/api/cost-center/toggle') ?>', { id });
          if (res.success) {
            window.showToast('success', res.message || 'Status Cost Center berhasil diubah');
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal mengubah status');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan saat memproses permintaan');
        }
      }
    };
  }
</script>

<?= $this->endSection() ?>