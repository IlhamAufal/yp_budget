<div>
  <form id="changePasswordForm" class="space-y-4">
    <!-- Current Password -->
    <div>
      <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password Lama</label>
      <input type="password" id="cp_current_password" name="current_password" required placeholder="Masukkan password saat ini"
        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
    </div>

    <!-- New Password -->
    <div>
      <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password Baru</label>
      <input type="password" id="cp_new_password" name="new_password" required placeholder="Masukkan password baru"
        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
    </div>

    <!-- Confirm Password -->
    <div>
      <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi Password Baru</label>
      <input type="password" id="cp_confirm_password" name="confirm_password" required placeholder="Ulangi password baru"
        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors" />
      <p id="cp_mismatch_msg" class="mt-1.5 text-xs text-red-500 font-medium hidden">
        <i class="fa-solid fa-circle-exclamation mr-1"></i>Password konfirmasi tidak cocok
      </p>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100 dark:border-gray-800">
      <button type="button" id="cp_cancel_btn" class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
        Batal
      </button>
      <button type="submit" id="cp_submit_btn"
        class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
        <i id="cp_spinner" class="fa-solid fa-spinner fa-spin hidden"></i>
        <span id="cp_submit_text">Ubah Password</span>
      </button>
    </div>
  </form>
</div>

<script>
(function() {
  const form = document.getElementById('changePasswordForm');
  const currentPass = document.getElementById('cp_current_password');
  const newPass = document.getElementById('cp_new_password');
  const confirmPass = document.getElementById('cp_confirm_password');
  const mismatchMsg = document.getElementById('cp_mismatch_msg');
  const cancelBtn = document.getElementById('cp_cancel_btn');
  const submitBtn = document.getElementById('cp_submit_btn');
  const spinner = document.getElementById('cp_spinner');
  const submitText = document.getElementById('cp_submit_text');

  function checkMatch() {
    const valNew = newPass.value;
    const valConf = confirmPass.value;
    if (valConf && valNew !== valConf) {
      mismatchMsg.classList.remove('hidden');
    } else {
      mismatchMsg.classList.add('hidden');
    }
  }

  if (newPass) newPass.addEventListener('input', checkMatch);
  if (confirmPass) confirmPass.addEventListener('input', checkMatch);

  if (cancelBtn) {
    cancelBtn.addEventListener('click', () => {
      if (window.Modal && typeof window.Modal.close === 'function') {
        window.Modal.close();
      }
    });
  }

  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const current_password = currentPass.value.trim();
      const new_password = newPass.value.trim();
      const confirm_password = confirmPass.value.trim();

      if (new_password !== confirm_password) {
        if (window.showToast) window.showToast('error', 'Password konfirmasi tidak cocok.');
        return;
      }
      if (new_password.length < 4) {
        if (window.showToast) window.showToast('error', 'Password baru minimal 4 karakter.');
        return;
      }

      submitBtn.disabled = true;
      if (spinner) spinner.classList.remove('hidden');
      if (submitText) submitText.textContent = 'Menyimpan...';

      try {
        const payload = { current_password, new_password, confirm_password };
        const res = await window.ypFetch('<?= base_url('auth/changePassword') ?>', payload);
        if (res && res.success) {
          if (window.showToast) window.showToast('success', res.message || 'Password berhasil diubah.');
          if (window.Modal && typeof window.Modal.close === 'function') {
            window.Modal.close();
          }
        } else {
          if (window.showToast) window.showToast('error', (res && res.message) || 'Gagal mengubah password.');
        }
      } catch (err) {
        if (window.showToast) window.showToast('error', 'Terjadi kesalahan sistem.');
      } finally {
        submitBtn.disabled = false;
        if (spinner) spinner.classList.add('hidden');
        if (submitText) submitText.textContent = 'Ubah Password';
      }
    });
  }
})();
</script>
