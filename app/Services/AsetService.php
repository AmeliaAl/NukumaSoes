<?php

namespace App\Services;

use App\Models\Aset;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use Illuminate\Support\Facades\DB;
use App\Models\Akun;


class AsetService
{
    public static function beliCash(array $data): Aset
    {
        return DB::transaction(function () use ($data) {

            $aset = Aset::create([
                'kode_aset' => Aset::generateKdAset(),
                'nama_aset' => $data['nama_aset'],
                'id_kategori' => $data['id_kategori'],
                'tanggal_perolehan' => $data['tanggal_perolehan'],
                'nilai_perolehan' => $data['nilai_perolehan'],
                'nilai_residu' => $data['nilai_residu'],
                'masa_manfaat' => $data['masa_manfaat'],
                'metode_penyusutan' => 'Garis Lurus',
            ]);

            
            return $aset;
        });
    }
}
