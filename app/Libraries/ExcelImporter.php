<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * ExcelImporter — Mesin Import Excel terpusat (PRD Phase 2.3, standar 1.6).
 *
 * WAJIB digunakan untuk SELURUH kebutuhan upload/import Excel di semua modul
 * (Master Data, Sales, OPEX, MPP) — menggantikan PHPExcel legacy.
 * Dipanggil dari layer Controller/Service (bukan Model).
 */
class ExcelImporter
{
    /**
     * Baca file Excel (path/UploadedFile) menjadi array baris.
     *
     * @param string|\CodeIgniter\Files\File $file Path file atau objek UploadedFile
     * @param bool   $assoc     true → baris data di-map memakai header baris pertama
     * @param int    $headerRow Nomor baris header (1-based). Dipakai saat $assoc=true.
     * @param string $sheetName Nama sheet spesifik (opsional; default sheet aktif pertama)
     *
     * @return array
     */
    public static function import($file, bool $assoc = false, int $headerRow = 1, ?string $sheetName = null): array
    {
        $path = $file instanceof \CodeIgniter\Files\File ? $file->getRealPath() : (string) $file;

        if (! is_file($path)) {
            throw new \InvalidArgumentException('File Excel tidak ditemukan: ' . $path);
        }

        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);

        /** @var Spreadsheet $spreadsheet */
        $spreadsheet = $reader->load($path);

        $sheet = $sheetName ? $spreadsheet->getSheetByName($sheetName) : $spreadsheet->getActiveSheet();
        if (! $sheet) {
            throw new \InvalidArgumentException("Sheet '{$sheetName}' tidak ditemukan di file Excel.");
        }

        $rawRows = $sheet->toArray(null, true, true, false);

        if (! $assoc) {
            return $rawRows;
        }

        return self::toAssoc($rawRows, $headerRow);
    }

    /**
     * Konversi array baris mentah menjadi array asosiatif menggunakan
     * baris header sebagai kunci.
     *
     * @param array $rawRows   Hasil $sheet->toArray()
     * @param int   $headerRow Nomor baris header (1-based)
     *
     * @return array
     */
    public static function toAssoc(array $rawRows, int $headerRow = 1): array
    {
        $headerIndex = max(0, $headerRow - 1);

        if (! isset($rawRows[$headerIndex])) {
            return [];
        }

        $headers = array_map(function ($h) {
            $h = trim((string) $h);

            return strtolower(str_replace([' ', '.', '-'], '_', $h));
        }, $rawRows[$headerIndex]);

        $result = [];
        foreach ($rawRows as $idx => $row) {
            if ($idx <= $headerIndex) {
                continue;
            }
            // Lewati baris kosong total
            if (empty(array_filter($row, fn($v) => $v !== null && trim((string) $v) !== ''))) {
                continue;
            }

            $assocRow = [];
            foreach ($headers as $i => $header) {
                if ($header === '') {
                    continue;
                }
                $assocRow[$header] = $row[$i] ?? null;
            }
            $result[] = $assocRow;
        }

        return $result;
    }

    /**
     * Helper angka: parse nilai dari Excel (string/float) menjadi float bersih.
     */
    public static function toFloat($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = trim((string) $value);

        return (float) str_replace(',', '', $value);
    }

    /**
     * Ambil nilai dari baris assoc dengan fallback beberapa nama kolom.
     *
     * @param array $row    Baris hasil import assoc
     * @param array $aliases Daftar nama kolom yang mungkin (berurutan prioritas)
     * @param mixed $default Nilai default bila tidak ditemukan
     */
    public static function column(array $row, array $aliases, $default = null)
    {
        foreach ($aliases as $alias) {
            if (array_key_exists($alias, $row)) {
                return $row[$alias];
            }
        }

        return $default;
    }
}
