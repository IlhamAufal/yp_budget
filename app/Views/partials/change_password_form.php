<div x-data="{ saving: false, form: { current_password: '', new_password: '', confirm_password: '' } }">
  <form @submit.prevent="
    if (form.new_password !== form.confirm_password) { window.showToast('error', 'Password konfirmasi tidak cocok.'); return; }
    if (form.new_password.length < 4) { window.showToast('error', 'Password baru minimal 4 karakter.'); return; }
    saving = true;
    window.ypFetch('<?= base_url('auth/changePassword') ?>', form).then(res => {
      saving = false;
      if (res.success) {
        window.showToast('success', res.message || 'Password berhasil diubah.');
        Modal.close();
      } else {
        window.showToast('error', res.message || 'Gagal mengubah password.');
      }
    }).catch(() => { saving = false; window.showToast('error', 'Terjadi kesalahan.'); });
  " class="space-y-4">

    <!-- Current Password -->
    <div>
      <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password Lama</label>
      <input type="password" x-model="form.current_password" required placeholder="Masukkan password saat ini"
        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
    </div>

    <!-- New Password -->
    <div>
      <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password Baru</label>
      <input type="password" x-model="form.new_password" required placeholder="Masukkan password baru"
        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
    </div>

    <!-- Confirm Password -->
    <div>
      <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi Password Baru</label>
      <input type="password" x-model="form.confirm_password" required placeholder="Ulangi password baru"
        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
      <p x-show="form.confirm_password && form.new_password !== form.confirm_password" class="mt-1.5 text-xs text-red-500 font-medium">
        <i class="fa-solid fa-circle-exclamation mr-1"></i>Password konfirmasi tidak cocok
      </p>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100 dark:border-gray-800">
      <button type="button" @click="Modal.close()" class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
        Batal
      </button>
      <button type="submit" :disabled="saving || form.new_password !== form.confirm_password || !form.new_password || !form.current_password"
        class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
        <i x-show="saving" class="fa-solid fa-spinner fa-spin"></i>
        <span x-text="saving ? 'Menyimpan...' : 'Ubah Password'"></span>
      </button>
    </div>
  </form>
</div>
