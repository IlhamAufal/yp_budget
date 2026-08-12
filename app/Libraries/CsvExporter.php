<?php

namespace App\Libraries;

use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

/**
 * CSV response builder shared by Master Data exports.
 */
class CsvExporter
{
    private const UTF8_BOM = "\xEF\xBB\xBF";

    /**
     * Build a testable CSV download response with one UTF-8 BOM.
     *
     * @param array<int, mixed>          $headers
     * @param iterable<int, array>      $rows
     */
    public static function response(array $headers, iterable $rows, string $filename): ResponseInterface
    {
        $stream = fopen('php://temp', 'w+');
        if ($stream === false) {
            throw new \RuntimeException('Temporary stream CSV tidak dapat dibuka.');
        }

        fwrite($stream, self::UTF8_BOM);
        fputcsv($stream, $headers);
        foreach ($rows as $row) {
            fputcsv($stream, $row);
        }

        rewind($stream);
        $content = stream_get_contents($stream);
        fclose($stream);

        $safeFilename = self::normalizeFilename($filename);
        return Services::response()
            ->setContentType('text/csv', 'UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $safeFilename . '"')
            ->setBody($content === false ? '' : $content);
    }

    public static function normalizeFilename(string $filename): string
    {
        $filename = basename($filename);
        $filename = preg_replace('/[^A-Za-z0-9._-]+/', '_', $filename) ?? '';
        $filename = trim($filename, '._');

        return $filename !== '' ? $filename : 'export.csv';
    }
}
