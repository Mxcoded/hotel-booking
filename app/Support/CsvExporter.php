<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    /**
     * Build a streaming CSV download response from a collection of associative
     * arrays (or models). Array keys become the header row unless $headers is given.
     */
    public static function fromCollection(string $filename, Collection $rows, ?array $headers = null): StreamedResponse
    {
        $headers = $headers ?? self::guessHeaders($rows);

        return response()->streamDownload(function () use ($rows, $headers) {
            $stream = fopen('php://output', 'w');

            fputcsv($stream, $headers);

            foreach ($rows as $row) {
                if ($row instanceof \Illuminate\Database\Eloquent\Model) {
                    $row = $row->getAttributes();
                }
                $row = (array) $row;

                $line = [];
                foreach ($headers as $key) {
                    $line[] = $row[$key] ?? '';
                }
                fputcsv($stream, $line);
            }

            fclose($stream);
        }, $filename);
    }

    protected static function guessHeaders(Collection $rows): array
    {
        $first = $rows->first();
        if ($first instanceof \Illuminate\Database\Eloquent\Model) {
            return array_keys($first->getAttributes());
        }

        return array_keys((array) $first);
    }
}
