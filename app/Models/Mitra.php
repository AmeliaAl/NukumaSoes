<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// tambahan
use Illuminate\Support\Facades\DB;

class Mitra extends Model
{
     /** @use HasFactory<\Database\Factories\MitraFactory> */
    use HasFactory;
    // beri nama
    protected $table = 'mitra';

    // ijinkan seluruh kolom dapat dimodifikasi
    protected $guarded = [];

    // query nilai max dari kode mitra untuk generate otomatis kode mitra
    public static function getKodeMitra()
    {
        // query kode mitra
        $sql = "SELECT IFNULL(MAX(kode_mitra), 'MR-000') as kode_mitra 
                FROM mitra";
        $idmitra = DB::select($sql);

        // cacah hasilnya
        foreach ($idmitra as $kdmtra) {
            $kd = $kdmtra->kode_mitra;
        }
        // Mengambil substring tiga digit akhir dari string MR-000
        $noawal = substr($kd,-3);
        $noakhir = $noawal+1; //menambahkan 1, hasilnya adalah integer cth 1
        
        //menyambung dengan string PR-001
        $noakhir = 'MR-'.str_pad($noakhir,3,"0",STR_PAD_LEFT); 

        return $noakhir;
    }
}
