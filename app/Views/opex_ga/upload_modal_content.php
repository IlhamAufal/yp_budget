<?php
/* =====================================================================
 * opex_ga/upload_modal_content.php — KONTEN form upload Excel OPEX GA
 * Dimuat ke dalam global modal via endpoint OpexGaController::uploadModalForm.
 * Submit tetap full-page POST ke opexga/processUpload (redirect + flashdata).
 * ===================================================================== */
$uploadType = $uploadType ?? 'opex_summary';
?>

<form action="<?= base_url('opexga/processUpload') ?>" method="post" enctype="multipart/form-data" class="p-6 space-y-4">
    <?= csrf_field() ?>
    <input type="hidden" name="upload_type" value="<?= esc($uploadType) ?>">

    <div>
        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Berkas Excel (.xlsx, .xls)</label>
        <input type="file" name="excel_file" accept=".xlsx, .xls, .csv" required
               class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#2F3185]/10 file:text-[#2F3185] hover:file:bg-[#2F3185]/20">
    </div>

    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
        <button type="button" onclick="window.Modal && window.Modal.close()"
                class="rounded-xl border border-gray-300 dark:border-gray-700 px-5 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
            Batal
        </button>
        <button type="submit"
                class="rounded-xl bg-[#2F3185] hover:bg-[#25276d] px-5 py-2.5 text-xs font-semibold text-white shadow-xs transition-all inline-flex items-center gap-2 active:scale-[0.98]">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            <span>Unggah &amp; Proses</span>
        </button>
    </div>
</form>
