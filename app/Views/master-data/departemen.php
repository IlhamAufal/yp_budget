<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-4" x-data="departmentPage()">

  <!-- ============================================================ -->
  <!-- BREADCRUMB & HEADER -->
  <!-- ============================================================ -->
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-brand-500 transition-colors">
          <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span>Master Data</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-brand-500 font-bold">Departemen</span>
      </div>
      <h1 class="text-xl md:text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
          <i class="fa-solid fa-building text-base"></i>
        </span>
        Master Departemen
      </h1>
      <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
        Pengelolaan struktur departemen organisasi perusahaan sebagai pemegang anggaran budget.
      </p>
    </div>

    <!-- Quick Action Buttons -->
    <div class="flex flex-wrap items-center gap-2.5">
      <a
        href="<?= base_url('master/department/export?' . http_build_query($filters ?? [])) ?>"
        class="inline-flex items-center gap-2 rounded-xl border border-emerald-300 dark:border-emerald-700/60 bg-emerald-50 dark:bg-emerald-950/40 px-3.5 py-2 text-xs font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-all shadow-xs"
      >
        <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-400"></i>
        <span>Export CSV</span>
      </a>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- STATS -->
  <!-- ============================================================ -->
  <?php
    $totalRows    = count($rows ?? []);
    $activeCount  = count(array_filter($rows ?? [], fn($r) => ($r['status'] ?? 'A') === 'A'));
    $inactiveCount = $totalRows - $activeCount;
  ?>
  <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-xl border border-gray-200/80 bg-white px-4 py-2.5 text-xs dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
        <i class="fa-solid fa-building text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Total:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= number_format($totalRows) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400">
        <i class="fa-solid fa-circle-check text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Aktif:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= number_format($activeCount) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
        <i class="fa-solid fa-circle-pause text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Non-Aktif:</span>
      <span class="font-bold text-gray-900 dark:text-white"><?= number_format($inactiveCount) ?></span>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- DATA TABLE & FILTER -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <!-- Filter Bar -->
    <div class="p-3 border-b border-gray-100 dark:border-gray-800">
      <form method="GET" action="<?= base_url('master/department') ?>" class="flex flex-wrap items-center gap-2">
        
        <!-- Search Input -->
        <div class="relative flex-1 min-w-[180px]">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400"></i>
          <input
            type="text"
            name="search"
            value="<?= esc($filters['search'] ?? '') ?>"
            placeholder="Cari kode departemen atau nama..."
            class="w-full rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 pl-9 pr-3 text-xs text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-colors"
          />
        </div>

        <!-- Filter Status -->
        <select
          name="status"
          class="rounded-lg border border-gray-200 bg-gray-50/50 py-1.5 px-2.5 text-xs text-gray-800 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-400 transition-colors min-w-[120px]"
        >
          <option value="">Semua Status</option>
          <option value="A" <?= ($filters['status'] ?? '') === 'A' ? 'selected' : '' ?>>Aktif (A)</option>
          <option value="D" <?= ($filters['status'] ?? '') === 'D' ? 'selected' : '' ?>>Non-Aktif (D)</option>
        </select>

        <!-- Submit & Reset Buttons -->
        <button
          type="submit"
          class="rounded-lg bg-gray-900 dark:bg-brand-500 py-1.5 px-3 text-xs font-semibold text-white hover:bg-black dark:hover:bg-brand-600 transition-colors shadow-xs"
          title="Terapkan Filter"
        >
          <i class="fa-solid fa-filter text-[10px]"></i>
        </button>
        <?php if (! empty($has_filter)): ?>
        <a
          href="<?= base_url('master/department') ?>"
          class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-1.5 px-2.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          title="Reset Filter"
        >
          <i class="fa-solid fa-rotate-left text-[10px]"></i>
        </a>
        <?php endif; ?>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-800/50 text-[11px] font-bold capitalize tracking-normal text-gray-500 dark:text-gray-400">
            <th class="py-3 px-4 w-12 text-center">No</th>
            <th class="py-3 px-4 w-40">Code</th>
            <th class="py-3 px-4">Department</th>
            <th class="py-3 px-4 text-right w-28">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="4" class="py-12 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center gap-2">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-400">
                    <i class="fa-solid fa-building text-xl"></i>
                  </div>
                  <p class="font-semibold text-gray-600 dark:text-gray-300">Tidak ada data departemen ditemukan</p>
                  <p class="text-xs text-gray-400">Coba ubah kata kunci pencarian atau reset filter di atas.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($rows as $index => $r): ?>
              <tr x-show="isRowVisible(<?= $index ?>)" class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                <!-- No -->
                <td class="py-3 px-4 text-center text-gray-400 font-medium"><?= $index + 1 ?></td>

                <!-- Code -->
                <td class="py-3 px-4 font-mono font-bold text-gray-900 dark:text-white">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300 font-black">
                    <i class="fa-solid fa-tag text-[10px] text-blue-400"></i>
                    <?= esc($r['dept_code']) ?>
                  </span>
                </td>

                <!-- Department -->
                <td class="py-3 px-4 font-bold text-gray-800 dark:text-gray-100">
                  <?= esc($r['dept_desc']) ?>
                </td>

                <!-- Action -->
                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Edit Button -->
                    <button
                      type="button"
                      @click='openEditModal(<?= json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                      class="h-7 w-7 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-brand-50 hover:text-brand-500 hover:border-brand-200 dark:hover:bg-brand-500/10 dark:hover:text-brand-400 transition-colors flex items-center justify-center shadow-2xs"
                      title="Edit Departemen"
                    >
                      <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                    </button>

                    <!-- Delete Button -->
                    <button
                      type="button"
                      @click="toggleStatus(<?= (int)$r['id_dept'] ?>, 'hapus')"
                      class="h-7 w-7 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-red-600 hover:bg-red-50 hover:border-red-200 dark:hover:bg-red-500/10 transition-colors flex items-center justify-center shadow-2xs"
                      title="Hapus Departemen"
                    >
                      <i class="fa-solid fa-trash text-[11px]"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="border-t border-gray-100 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
      <div class="flex items-center gap-1.5 text-xs">
        <span>Menampilkan</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="totalItems === 0 ? 0 : ((currentPage - 1) * perPage + 1)"></span>
        <span>-</span>
        <span class="font-bold text-gray-800 dark:text-gray-200" x-text="Math.min(currentPage * perPage, totalItems)"></span>
        <span>dari</span>
        <span class="font-bold text-gray-800 dark:text-gray-200"><?= number_format($totalRows) ?></span>
        <span>Departemen</span>
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

  <!-- ============================================================ -->
  <!-- MODAL: TAMBAH / EDIT DEPARTEMEN -->
  <!-- ============================================================ -->
  <div
    x-show="modalOpen"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center p-4"
    style="z-index: 9999999; background-color: rgba(0,0,0,0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);"
    @click.self="modalOpen = false"
    x-cloak
  >
    <div
      x-show="modalOpen"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95 translate-y-3"
      x-transition:enter-end="opacity-100 scale-100 translate-y-0"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100 scale-100 translate-y-0"
      x-transition:leave-end="opacity-0 scale-95 translate-y-3"
      class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 overflow-hidden"
    >
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
            <i class="fa-solid fa-building text-base"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="form.id ? 'Edit Departemen' : 'Tambah Departemen Baru'"></h3>
            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="form.id ? 'Perbarui detail data departemen' : 'Daftarkan nama departemen baru'"></p>
          </div>
        </div>
        <button
          type="button"
          @click="modalOpen = false"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <i class="fa-solid fa-xmark text-base"></i>
        </button>
      </div>

      <!-- Modal Form -->
      <form @submit.prevent="saveDepartment()" class="p-6 space-y-4">
        <input type="hidden" x-model="form.id" />

        <!-- Department Code -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
            Kode Departemen <span class="text-red-500">*</span>
          </label>
          <input
            type="text"
            x-model="form.dept_code"
            required
            placeholder="Contoh: HRD, IT, FIN, GA, PROD"
            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-mono font-bold uppercase text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
          />
        </div>

        <!-- Department Name -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
            Nama / Deskripsi Departemen <span class="text-red-500">*</span>
          </label>
          <input
            type="text"
            x-model="form.dept_desc"
            required
            placeholder="Contoh: Human Resource & General Affairs"
            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-semibold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
          />
        </div>

        <!-- Status -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1.5">
            Status <span class="text-red-500">*</span>
          </label>
          <select
            x-model="form.status"
            required
            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-bold text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
          >
            <option value="A">Aktif (A)</option>
            <option value="D">Non-Aktif (D)</option>
          </select>
        </div>

        <!-- Form Actions -->
        <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
          <button
            type="button"
            @click="modalOpen = false"
            class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/40 disabled:opacity-50 transition-colors"
          >
            <i class="fa-solid fa-spinner fa-spin" x-show="saving"></i>
            <span x-text="saving ? 'Menyimpan...' : (form.id ? 'Perbarui Data' : 'Simpan Data')"></span>
          </button>
        </div>
      </form>
    </div>
  </div>

</div>

<!-- ============================================================ -->
<!-- ALPINE COMPONENT SCRIPT -->
<!-- ============================================================ -->
<script>
  function departmentPage() {
    return {
      modalOpen: false,
      saving: false,

      // Pagination
      currentPage: 1,
      perPage: 10,
      totalItems: <?= (int)$totalRows ?>,
      get totalPages() {
        return Math.ceil(this.totalItems / this.perPage) || 1;
      },
      isRowVisible(index) {
        return index >= (this.currentPage - 1) * this.perPage && index < this.currentPage * this.perPage;
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

      form: {
        id: null,
        dept_code: '',
        dept_desc: '',
        status: 'A'
      },

      openCreateModal() {
        this.form = {
          id: null,
          dept_code: '',
          dept_desc: '',
          status: 'A'
        };
        this.modalOpen = true;
      },

      openEditModal(row) {
        this.form = {
          id: row.id_dept,
          dept_code: row.dept_code || '',
          dept_desc: row.dept_desc || '',
          status: row.status || 'A'
        };
        this.modalOpen = true;
      },

      async saveDepartment() {
        this.saving = true;
        try {
          const res = await window.ypFetch('<?= base_url('master/api/department/save') ?>', this.form);
          if (res.success) {
            window.showToast('success', res.message || 'Departemen berhasil disimpan');
            this.modalOpen = false;
            setTimeout(() => window.location.reload(), 600);
          } else {
            window.showToast('error', res.message || 'Gagal menyimpan departemen');
          }
        } catch (e) {
          window.showToast('error', 'Terjadi kesalahan sistem');
        } finally {
          this.saving = false;
        }
      },

      async toggleStatus(id, actionText) {
        if (!window.ypConfirm(`Apakah Anda yakin ingin ${actionText} departemen ini?`)) {
          return;
        }

        try {
          const res = await window.ypFetch('<?= base_url('master/api/department/toggle') ?>', { id });
          if (res.success) {
            window.showToast('success', res.message || 'Status departemen berhasil diubah');
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