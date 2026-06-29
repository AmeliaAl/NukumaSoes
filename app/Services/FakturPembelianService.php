<?php

namespace App\Services;

use App\Models\Jurnal;
use App\Models\Akun;
use Illuminate\Support\Facades\DB;

class FakturPembelianService
{
    /**
     * Mapping kategori aset ke akun aset tetap
     * Sesuai dengan akun yang ada di database
     */
    private static function getMappingKategoriAset(): array
    {
        return [
            'Mesin'                => 172, 
            'Peralatan'            => 171, 
            'Bangunan'             => 179, 
            'Kendaraan'            => 178, 
            'Peralatan Kantor'     => 171, 
            'Peralatan Produksi'   => 172,
        ];
    }

    public static function buatJurnal($faktur): void
    {
        DB::transaction(function () use ($faktur) {
            // 🔹 Mapping akun dasar
            $akun = [
                'perlengkapan' => Akun::where('no_akun', 150)->firstOrFail(),
                'utang'        => Akun::where('no_akun', 211)->firstOrFail(),
                'kas'          => Akun::where('no_akun', 111)->firstOrFail(),
                'beban_lain'   => Akun::where('no_akun', 515)->firstOrFail(),
                'peralatan_default' => Akun::where('no_akun', 171)->firstOrFail(), // Fallback untuk aset tetap
            ];

            // 🔹 Header jurnal
            $jurnal = Jurnal::create([
                'tanggal'      => $faktur->tanggal_faktur,
                'no_referensi' => $faktur->no_faktur,
                'deskripsi'    => 'Pembelian kredit faktur ' . $faktur->no_faktur,
            ]);

            $totalUtang = 0;
            $mappingKategori = self::getMappingKategoriAset();

            // 🔹 Debit per item - BACA KATEGORI ASET
            foreach ($faktur->items as $item) {
                $kategori = $item->kategoriAset;
                $namaKategori = $kategori->nama_kategori;

                // Tentukan akun debit berdasarkan jenis aset dan kategori
                if ($kategori->jenis_aset === 'aset_tetap') {
                    // 🔥 MAPPING KATEGORI KE AKUN SPESIFIK
                    if (isset($mappingKategori[$namaKategori])) {
                        $noAkunTarget = $mappingKategori[$namaKategori];
                        $akunDebit = Akun::where('no_akun', $noAkunTarget)->first();
                        
                        // Jika akun tidak ditemukan, gunakan default
                        if (!$akunDebit) {
                            $akunDebit = $akun['peralatan_default'];
                            $namaAkun = 'Peralatan';
                        } else {
                            $namaAkun = $akunDebit->nama_akun;
                        }
                    } else {
                        // Kategori tidak ada di mapping, gunakan default
                        $akunDebit = $akun['peralatan_default'];
                        $namaAkun = 'Peralatan';
                    }
                } else {
                    // Aset lancar tetap ke perlengkapan
                    $akunDebit = $akun['perlengkapan'];
                    $namaAkun = 'Perlengkapan';
                }

                $nilai = (float) $item->total_harga;
                
                // Tambahkan biaya lain ke item pertama (hanya sekali)
                if ($faktur->biaya_lain > 0) {
                    $nilai += $faktur->biaya_lain;
                    $faktur->biaya_lain = 0; // biar ga keitung 2x
                }

                $jurnal->details()->create([
                    'no_akun'      => $akunDebit->id,
                    'no_referensi' => $faktur->no_faktur,
                    'debit'        => $nilai,
                    'credit'       => 0,
                    'deskripsi'    => $namaAkun . ' - ' . $item->nama_aset,
                ]);

                $totalUtang += $nilai;
            }

            // 🔹 Kredit utang
            $jurnal->details()->create([
                'no_akun'      => $akun['utang']->id, 
                'no_referensi' => $faktur->no_faktur, 
                'debit'        => 0,
                'credit'       => $totalUtang,
                'deskripsi'    => 'Utang pembelian ke ' . $faktur->vendor->nama_vendor,
            ]);
        });
    }
}