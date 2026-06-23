<?php

namespace App\Imports;

use App\Models\Coa;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class CoaImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $insertedCount = 0;

        foreach ($rows as $row) {
            // Karena seringkali ada kolom "Nomor" (No), maka kita asumsikan:
            // Jika ada 3 kolom (atau lebih): Kolom 1 (No), Kolom 2 (Kode), Kolom 3 (Nama)
            // Jika ada 2 kolom: Kolom 1 (Kode), Kolom 2 (Nama)
            
            $hasThreeColumns = isset($row[2]) && trim((string)$row[2]) !== '';

            $kodeAkun = $hasThreeColumns ? trim((string)$row[1]) : trim((string)($row[0] ?? ''));
            $namaAkun = $hasThreeColumns ? trim((string)$row[2]) : trim((string)($row[1] ?? ''));

            // Pastikan sel tidak kosong
            if (!empty($kodeAkun) && !empty($namaAkun)) {
                
                // Abaikan jika baris tersebut ternyata adalah baris judul tabel / header
                // Misalnya baris yang isinya kata "kode" atau "rek" atau "nama"
                $isHeader = str_contains(strtolower($kodeAkun), 'kode') || 
                            str_contains(strtolower($kodeAkun), 'rek') ||
                            str_contains(strtolower($namaAkun), 'nama') ||
                            str_contains(strtolower($namaAkun), 'akun');

                if (!$isHeader) {
                    Coa::updateOrCreate(
                        ['kode_akun' => $kodeAkun],
                        [
                            'nama_akun' => $namaAkun,
                        ]
                    );
                    $insertedCount++;
                }
            }
        }

        // Tampilkan error jika sama sekali tidak ada satupun baris data yang valid yang masuk
        if ($insertedCount === 0) {
            throw new \Exception("Gagal menemukan data akun. Pastikan urutan kolom sesuai (contoh: No | Kode Akun | Nama Akun).");
        }
    }
}

