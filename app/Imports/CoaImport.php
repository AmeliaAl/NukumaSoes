<?php

namespace App\Imports;

use App\Models\Coa;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class CoaImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $insertedCount = 0;

        foreach ($rows as $row) {
            $values = array_map(function ($value) {
                return trim((string)$value);
            }, array_values($row->toArray()));

            while (count($values) > 0 && $values[0] === '') {
                array_shift($values);
            }

            $kodeAkun = null;
            $headerAkun = null;
            $namaAkun = null;

            if (count($values) >= 3) {
                $first = strtolower($values[0]);
                $second = strtolower($values[1]);
                $third = strtolower($values[2]);

                if ($this->isHeaderRow($first, $second, $third)) {
                    continue;
                }

                if (str_contains($first, 'no') || str_contains($first, 'nomor') || (is_numeric($values[0]) && !preg_match('/(header|kode|nama|akun)/i', $values[1]))) {
                    $kodeAkun = $values[1];
                    $namaAkun = $values[2];
                } elseif (str_contains($second, 'header')) {
                    $kodeAkun = $values[0];
                    $headerAkun = $values[1];
                    $namaAkun = $values[2];
                } else {
                    $kodeAkun = $values[0];
                    $headerAkun = $values[1];
                    $namaAkun = $values[2];
                }
            } elseif (count($values) >= 2) {
                if ($this->isHeaderRow(strtolower($values[0]), strtolower($values[1]), '')) {
                    continue;
                }

                $kodeAkun = $values[0];
                $namaAkun = $values[1];
            }

            if (empty($kodeAkun) || empty($namaAkun)) {
                continue;
            }

            if ($this->isHeaderRow(strtolower($kodeAkun), strtolower($headerAkun), strtolower($namaAkun))) {
                continue;
            }

            Coa::updateOrCreate(
                ['kode_akun' => $kodeAkun],
                [
                    'header_akun' => $headerAkun,
                    'nama_akun' => $namaAkun,
                ]
            );
            $insertedCount++;
        }

        if ($insertedCount === 0) {
            throw new \Exception("Gagal menemukan data akun. Pastikan urutan kolom sesuai (contoh: kode_akun | header_akun | nama_akun atau kode_akun | nama_akun).");
        }
    }

    private function isHeaderRow(string $first, string $second, string $third): bool
    {
        return str_contains($first, 'kode') ||
               str_contains($first, 'rek') ||
               str_contains($first, 'no') ||
               str_contains($second, 'header') ||
               str_contains($second, 'kode') ||
               str_contains($second, 'nama') ||
               str_contains($third, 'nama') ||
               str_contains($third, 'akun');
    }
}

