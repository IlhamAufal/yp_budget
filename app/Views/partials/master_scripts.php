<script>
  /* ----------------------------------------------------------------
   * Master Scripts — Helper bersama untuk halaman-halaman aplikasi.
   * window.showToast & window.ypToast sudah didefinisikan di
   * partials/toast.php (global component). Script ini hanya mendefinisikan
   * ypFetch dan ypConfirm.
   * ---------------------------------------------------------------- */

  /**
   * ypFetch(url, data)
   * Helper fetch dengan method POST, auto attach CSRF token dan mengembalikan JSON.
   */
  window.ypFetch = async function (url, data = {}) {
    const body = (data instanceof FormData) ? data : new URLSearchParams(data);

    // Auto attach CSRF jika URLSearchParams
    if (body instanceof URLSearchParams) {
      const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.content || 'csrf_test_name';
      const csrfHash = document.querySelector('meta[name="csrf-hash"]')?.content || (document.cookie.match(/csrf_cookie_name=([^;]+)/)?.[1] || '');
      if (csrfHash && !body.has(csrfName)) {
        body.append(csrfName, decodeURIComponent(csrfHash));
      }
    }

    try {
      const isFormData = (body instanceof FormData);
      const res = await fetch(url, {
        method: 'POST',
        headers: isFormData ? {
          'X-Requested-With': 'XMLHttpRequest',
        } : {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body,
      });
      const json = await res.json();
      return json;
    } catch (e) {
      console.error('ypFetch error:', e);
      return { success: false, message: 'Koneksi gagal atau terjadi kesalahan server.' };
    }
  };

  /**
   * ypConfirm(message)
   * Wrapper sederhana untuk dialog konfirmasi bawaan browser.
   */
  window.ypConfirm = function (message) {
    return window.confirm(message);
  };
</script>
