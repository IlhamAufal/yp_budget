<script>
  /* ----------------------------------------------------------------
   * Master Scripts — Helper bersama untuk halaman-halaman aplikasi.
   * window.showToast & window.ypToast sudah didefinisikan di
   * partials/toast.php (global component). Script ini hanya mendefinisikan
   * ypFetch dan ypConfirm.
   * ---------------------------------------------------------------- */

  /**
   * ypFetch(url, data)
   * Helper fetch dengan method POST, mengembalikan JSON response.
   */
  window.ypFetch = async function (url, data) {
    const body = new URLSearchParams(data);
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body,
      });
      return await res.json();
    } catch (e) {
      return { success: false, message: 'Koneksi gagal, silakan coba lagi.' };
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
