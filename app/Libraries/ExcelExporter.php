<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * ExcelExporter — Mesin Export Excel terpusat (PRD Phase 2.3, standar 1.6).
 *
 * WAJIB digunakan untuk SELURUH kebutuhan export Excel di semua modul
 * (Master Data, Sales, OPEX, MPP, P/L) — menggantikan PHPExcel legacy.
 * Dipanggil dari layer Controller/Service (bukan Model).
 */
class ExcelExporter
{
    /** Warna header tabel (brand blue). */
    public const HEADER_FILL = '1D4ED8';

    /**
     * Buat response download .xlsx dari array sederhana.
     *
     * @param array  $headers    Baris header: ['Kolom A', 'Kolom B', ...]
     * @param array  $rows       Data: [ ['a', 'b'], ['c', 'd'], ... ] atau assoc
     * @param string $filename   Nama file hasil download (tanpa ekstensi opsional)
     * @param string $sheetTitle Judul sheet (maks 31 karakter)
     * @param array  $options    ['freeze' => 'A2', 'auto_width' => true]
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public static function export(
        array $headers,
        array $rows,
        string $filename,
        string $sheetTitle = 'Sheet1',
        array $options = []
    ): \CodeIgniter\HTTP\ResponseInterface {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle(self::sanitizeTitle($sheetTitle));

        // ---- Header Row -------------------------------------------------
        $col = 1;
        foreach ($headers as $header) {
            $cell = Coordinate::stringFromColumnIndex($col) . '1';
            $sheet->setCellValueExplicit($cell, (string) $header, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $col++;
        }
        $lastColumn = Coordinate::stringFromColumnIndex(max(1, count($headers)));
        $headerRange = 'A1:' . $lastColumn . '1';

        $sheet->getStyle($headerRange)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::HEADER_FILL);
        $sheet->getStyle($headerRange)
            ->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle($headerRange)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // ---- Data Rows --------------------------------------------------
        $rowIdx = 2;
        foreach ($rows as $row) {
            $col = 1;
            foreach (array_values((array) $row) as $value) {
                $cell = Coordinate::stringFromColumnIndex($col) . $rowIdx;
                $sheet->setCellValue($cell, $value);
                $col++;
            }
            $rowIdx++;
        }

        // ---- Styling ----------------------------------------------------
        $lastDataRow = max(1, $rowIdx - 1);
        $dataRange   = 'A1:' . $lastColumn . $lastDataRow;
        $sheet->getStyle($dataRange)
            ->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setARGB('CBD5E1');

        if (($options['auto_width'] ?? true) !== false) {
            foreach (range(1, count($headers)) as $i) {
                $colLetter = Coordinate::stringFromColumnIndex($i);
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
        }

        $freeze = $options['freeze'] ?? 'A2';
        if ($freeze) {
            $sheet->freezePane($freeze);
        }

        return self::send($spreadsheet, $filename);
    }

    /**
     * Kirim spreadsheet sebagai response download.
     */
    protected static function send(Spreadsheet $spreadsheet, string $filename): \CodeIgniter\HTTP\ResponseInterface
    {
        $filename = preg_replace('/[^\w.\-]+/', '_', $filename);
        if (stripos($filename, '.xlsx') === false) {
            $filename .= '.xlsx';
        }

        $writer = new Xlsx($spreadsheet);

        // Simpan ke buffer lalu kirim via CI4 response
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        $response = \Config\Services::response();

        return $response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody($content);
    }

    /**
     * Sanitize nama sheet (PhpSpreadsheet maks 31 karakter & tanpa karakter khusus).
     */
    protected static function sanitizeTitle(string $title): string
    {
        $title = preg_replace('/[\\\\\/\?\*\[\]:]/', '', $title);
        $title = mb_substr($title, 0, 31);

        return $title !== '' ? $title : 'Sheet1';
    }
}
