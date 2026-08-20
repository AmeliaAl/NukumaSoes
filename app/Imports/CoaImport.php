<?php

namespace App\Imports;

use App\Models\Coa;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class CoaImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception("File Excel kosong.");
        }

        $isValid = false;
        $checkLimit = min(10, $rows->count());
        $headerRowIndex = -1;
        
        for ($i = 0; $i < $checkLimit; $i++) {
            $row = $rows[$i];
            $rowString = strtolower(preg_replace('/[^a-z0-9]/i', '', implode("", $row->toArray())));
            
            if (str_contains($rowString, 'kode') && str_contains($rowString, 'nama')) {
                $isValid = true;
                $headerRowIndex = $i;
                break;
            }
        }

        if (!$isValid) {
             throw new \Exception("Format kolom Pada File Excel tidak sesuai. File harus memiliki kolom 'Kode Akun' dan 'Nama Akun'.");
        }

        // Tentukan index kolom berdasarkan baris header
        $headerRow = array_values($rows[$headerRowIndex]->toArray());
        $kodeIndex = -1;
        $namaIndex = -1;
        $headerAkunIndex = -1;

        foreach ($headerRow as $colIndex => $val) {
            $h = strtolower(preg_replace('/[^a-z0-9]/i', '', (string)$val));
            if (str_contains($h, 'kode') || str_contains($h, 'kodeakun')) {
                $kodeIndex = $colIndex;
            } elseif (str_contains($h, 'nama') || str_contains($h, 'namaakun')) {
                $namaIndex = $colIndex;
            } elseif (str_contains($h, 'header')) {
                $headerAkunIndex = $colIndex;
            }
        }

        if ($kodeIndex === -1 || $namaIndex === -1) {
            throw new \Exception("Format kolom Pada File Excel tidak sesuai. File harus memiliki kolom 'Kode Akun' dan 'Nama Akun'.");
        }

        // Syarat ketat: Tidak boleh ada kolom 'Header Akun'
        if ($headerAkunIndex !== -1) {
            throw new \Exception("Format kolom Pada File Excel tidak sesuai. File hanya boleh memiliki kolom 'Kode Akun' dan 'Nama Akun'.");
        }

        $insertedCount = 0;

        foreach ($rows as $index => $row) {
            if ($index <= $headerRowIndex) {
                continue; // Lewati baris header dan baris di atasnya
            }

            $cols = array_values($row->toArray());
            
            $kodeAkun = isset($cols[$kodeIndex]) ? trim((string)$cols[$kodeIndex]) : null;
            $namaAkun = isset($cols[$namaIndex]) ? trim((string)$cols[$namaIndex]) : null;
            $headerAkun = ($headerAkunIndex !== -1 && isset($cols[$headerAkunIndex])) ? trim((string)$cols[$headerAkunIndex]) : null;

            if (empty($kodeAkun) || empty($namaAkun)) {
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
            throw new \Exception("Gagal menemukan data akun. Pastikan urutan kolom sesuai (contoh: kode_akun | nama_akun) atau data tidak kosong.");
        }
    }
}

