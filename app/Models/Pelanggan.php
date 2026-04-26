<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// tambahan
use Illuminate\Support\Facades\DB;

class Pelanggan extends Model
{
     /** @use HasFactory<\Database\Factories\MitraFactory> */
    use HasFactory;
    // beri nama
    protected $table = 'pelanggan';

    // ijinkan seluruh kolom dapat dimodifikasi
    protected $guarded = [];

    // query nilai max dari kode mitra untuk generate otomatis kode mitra
    public static function getKodePelanggan()
    {
        // query kode mitra
        $sql = "SELECT IFNULL(MAX(kode_pelanggan), 'PG-000') as kode_pelanggan
                FROM pelanggan";
        $idpelanggan = DB::select($sql);

        // cacah hasilnya
        foreach ($idpelanggan as $kdplgn) {
            $kd = $kdplgn->kode_pelanggan;
        }
        // Mengambil substring tiga digit akhir dari string MR-000
        $noawal = substr($kd,-3);
        $noakhir = $noawal+1; //menambahkan 1, hasilnya adalah integer cth 1
        
        //menyambung dengan string PR-001
        $noakhir = 'PG-'.str_pad($noakhir,3,"0",STR_PAD_LEFT); 

        return $noakhir;
    }
    /* =====================
     | RELASI
     ===================== */
    public function penjualanNonKonsinyasi()
    {
        return $this->hasMany(PenjualanNonKonsinyasi::class, 'pelanggan_id');
    }

}
