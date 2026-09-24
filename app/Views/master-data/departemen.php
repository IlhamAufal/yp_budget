<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6" x-data="departmentPage()">

  <!-- ============================================================ -->
  <!-- BREADCRUMB & HEADER -->
  <!-- ============================================================ -->
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
        <a href="<?= base_url('dashboard') ?>" class="hover:text-[#2F3185] transition-colors">Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span class="hover:text-[#2F3185]">Master Data</span>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
        <span class="text-[#2F3185] font-bold">Departemen</span>
      </div>
      <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        Master Departemen
      </h1>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
        Pengelolaan struktur departemen organisasi perusahaan sebagai pemegang anggaran budget.
      </p>
    </div>

    <!-- Quick Action Buttons -->
    <div class="flex flex-wrap items-center gap-3">
      <a
        href="<?= base_url('master/department/export?' . http_build_query($filters ?? [])) ?>"
        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 text-xs font-semibold text-white shadow-xs transition-all active:scale-[0.98]"
      >
        <i class="fa-solid fa-file-excel"></i>
        <span>Export CSV</span>
      </a>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- STATS -->
  <!-- ============================================================ -->
  <?php
    $totalRows    = (int) ($total ?? 0);
    $activeCount  = count(array_filter($rows ?? [], fn($r) => ($r['status'] ?? 'A') === 'A'));
    $inactiveCount = $totalRows - $activeCount;
  ?>
  <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-2xl border border-gray-200/80 bg-white p-4 text-xs dark:border-gray-800 dark:bg-gray-900 shadow-xs">
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-[#2F3185]/10 text-[#2F3185] dark:bg-[#2F3185]/20 dark:text-indigo-400">
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
      <span class="font-bold text-emerald-600 dark:text-emerald-400"><?= number_format($activeCount) ?></span>
    </div>
    <span class="text-gray-300 dark:text-gray-700">|</span>
    <div class="flex items-center gap-2">
      <span class="flex h-6 w-6 items-center justify-center rounded-md bg-rose-50 text-rose-500 dark:bg-rose-500/10 dark:text-rose-400">
        <i class="fa-solid fa-circle-pause text-[10px]"></i>
      </span>
      <span class="text-gray-500 dark:text-gray-400">Non-Aktif:</span>
      <span class="font-bold text-rose-600 dark:text-rose-400"><?= number_format($inactiveCount) ?></span>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- FILTER CARD -->
  <!-- ============================================================ -->
  <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs">
    <form method="GET" action="<?= base_url('master/department') ?>" class="flex flex-wrap items-center gap-3">
      <!-- Search Input -->
      <div class="flex-1 min-w-[200px]">
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pencarian Departemen</label>
        <input
          type="text"
          name="search"
          value="<?= esc($filters['search'] ?? '') ?>"
          placeholder="Cari kode departemen atau nama..."
          class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-800 placeholder-gray-400 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white transition-colors shadow-xs"
        />
      </div>

      <!-- Filter Status -->
      <div class="min-w-[130px]">
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
        <select
          name="status"
          class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-800 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:text-white transition-colors"
        >
          <option value="">Semua Status</option>
          <option value="A" <?= ($filters['status'] ?? '') === 'A' ? 'selected' : '' ?>>Aktif (A)</option>
          <option value="D" <?= ($filters['status'] ?? '') === 'D' ? 'selected' : '' ?>>Non-Aktif (D)</option>
        </select>
      </div>

      <!-- Submit & Reset Buttons -->
      <div class="flex items-center gap-2 pt-5">
        <button
          type="submit"
          class="px-5 py-2.5 rounded-xl bg-[#2F3185] hover:bg-[#25276d] text-xs font-semibold text-white transition-colors inline-flex items-center gap-1.5 shadow-xs cursor-pointer active:scale-[0.98]"
        >
          <i class="fa-solid fa-filter text-[11px]"></i>
          <span>Filter</span>
        </button>
        <?php if (! empty($has_filter)): ?>
        <a
          href="<?= base_url('master/department') ?>"
          class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors inline-flex items-center gap-1.5 shadow-xs"
        >
          <i class="fa-solid fa-rotate-left text-[11px]"></i>
          <span>Reset</span>
        </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- ============================================================ -->
  <!-- DATA TABLE -->
  <!-- ============================================================ -->
  <div class="space-y-4">
    <div>
      <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Daftar Departemen</h3>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar seluruh departemen organisasi yang terdaftar pada sistem.</p>
    </div>

    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs text-gray-600 dark:text-gray-300">
          <thead class="bg-[#2F3185] text-white text-xs font-semibold border-b border-white/20">
            <tr class="bg-[#2F3185] text-white font-semibold text-xs">
              <th class="py-3 px-4 w-12 text-center text-white font-semibold">No.</th>
              <th class="py-3 px-4 w-40 text-white font-semibold">Code</th>
              <th class="py-3 px-4 text-white font-semibold">Department</th>
              <th class="py-3 px-4 text-right w-28 text-white font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs">
            <?php if (empty($rows)): ?>
              <tr>
                <td colspan="4" class="py-12 px-4 text-center text-gray-400 dark:text-gray-500">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 text-[#2F3185]">
                      <i class="fa-solid fa-building text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tidak ada data departemen ditemukan</p>
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
                  <td class="py-3 px-4 font-mono font-bold text-[#2F3185] dark:text-indigo-400">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-mono text-xs">
                      <?= esc($r['dept_code']) ?>
                    </span>
                  </td>

                  <!-- Department -->
                  <td class="py-3 px-4 font-medium text-gray-800 dark:text-gray-100">
                    <?= esc($r['dept_desc']) ?>
                  </td>

                  <!-- Action -->
                  <td class="py-3 px-4 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                      <!-- Edit Button -->
                      <button
                        type="button"
                        @click='openEditModal(<?= json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                        class="h-7 w-7 rounded-lg inline-flex items-center justify-center bg-[#2F3185] hover:bg-[#25276d] text-white shadow-xs transition active:scale-[0.98]"
                        title="Edit Departemen"
                      >
                        <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                      </button>

                      <!-- Delete Button -->
                      <button
                        type="button"
                        @click="toggleStatus(<?= (int)$r['id_dept'] ?>, 'hapus')"
                        class="h-7 w-7 rounded-lg inline-flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 transition-colors shadow-xs"
                        title="Hapus Departemen"
                      >
                        <i class="fa-solid fa-trash-can text-xs"></i>
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
      <?php
        $pages   = max(1, (int) ceil(($total ?? 0) / max(1, $perPage ?? 10)));
        $from    = ($total ?? 0) > 0 ? (($page - 1) * $perPage + 1) : 0;
        $to      = min($page * $perPage, $total ?? 0);
        $winStart = max(1, min($page - 2, max(1, $pages - 4)));
        $winEnd   = min($pages, $winStart + 4);
      ?>
      <?php if (($total ?? 0) > 0): ?>
      <div class="border-t border-gray-200 dark:border-gray-800 p-3.5 sm:p-4 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
        <span>Menampilkan <span class="font-bold text-gray-800 dark:text-gray-200"><?= $from ?></span> - <span class="font-bold text-gray-800 dark:text-gray-200"><?= $to ?></span> dari <span class="font-bold text-gray-800 dark:text-gray-200"><?= number_format($total) ?></span> Departemen</span>
        <div class="flex items-center gap-1">
          <a href="?page=<?= max(1, $page - 1) ?>" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?= $page <= 1 ? 'opacity-40 pointer-events-none' : '' ?> transition-colors text-xs font-semibold flex items-center gap-1"><i class="fa-solid fa-chevron-left text-[10px]"></i><span class="hidden sm:inline">Sebelumnya</span></a>
          <?php for ($p = $winStart; $p <= $winEnd; $p++): ?>
            <a href="?page=<?= $p ?>" class="h-8 min-w-[32px] px-2.5 rounded-lg text-xs font-semibold transition-colors flex items-center justify-center <?= $p === $page ? 'bg-[#2F3185] text-white font-bold shadow-xs' : 'border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>"><?= $p ?></a>
          <?php endfor; ?>
          <a href="?page=<?= min($pages, $page + 1) ?>" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?= $page >= $pages ? 'opacity-40 pointer-events-none' : '' ?> transition-colors text-xs font-semibold flex items-center gap-1"><span class="hidden sm:inline">Berikutnya</span><i class="fa-solid fa-chevron-right text-[10px]"></i></a>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- MODAL: TAMBAH / EDIT DEPARTEMEN -->
  <!-- ============================================================ -->
  <template x-teleport="body">
  <div
    x-show="modalOpen"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center p-4 z-[9999999]"
    style="background-color: rgba(0,0,0,0.65); backdrop-filter: blur(4px);"
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
      <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-800">
        <div>
          <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="form.id ? 'Edit Departemen' : 'Tambah Departemen Baru'"></h3>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="form.id ? 'Perbarui detail data departemen' : 'Daftarkan nama departemen baru'"></p>
        </div>
        <button
          type="button"
          @click="modalOpen = false"
          class="h-8 w-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>
      </div>

      <!-- Modal Form -->
      <form @submit.prevent="saveDepartment()" class="p-6 space-y-4">
        <input type="hidden" x-model="form.id" />

        <!-- Department Code -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Kode Departemen <span class="text-red-500">*</span>
          </label>
          <input
            type="text"
            x-model="form.dept_code"
            required
            placeholder="Contoh: HRD, IT, FIN, GA, PROD"
            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-mono font-bold uppercase text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
          />
        </div>

        <!-- Department Name -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Nama / Deskripsi Departemen <span class="text-red-500">*</span>
          </label>
          <input
            type="text"
            x-model="form.dept_desc"
            required
            placeholder="Contoh: Human Resource & General Affairs"
            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-medium text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
          />
        </div>

        <!-- Status -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Status <span class="text-red-500">*</span>
          </label>
          <select
            x-model="form.status"
            required
            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-xs font-semibold text-gray-900 focus:border-[#2F3185] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2F3185]/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
          >
            <option value="A">Aktif (A)</option>
            <option value="D">Non-Aktif (D)</option>
          </select>
        </div>

        <!-- Form Actions -->
        <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100 dark:border-gray-800">
          <button
            type="button"
            @click="modalOpen = false"
            class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs focus:outline-none disabled:opacity-50 transition-all active:scale-[0.98]"
          >
            <i class="fa-solid fa-spinner fa-spin" x-show="saving"></i>
            <span x-text="saving ? 'Menyimpan...' : (form.id ? 'Perbarui Data' : 'Simpan Data')"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
  </template>

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