<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabaRugiManual extends Model
{
    protected $fillable = [
        'periode',
        'penjualan_bersih',
        'persediaan_produk_jadi_awal',
        'persediaan_bdp_awal',
        'biaya_bahan_baku',
        'biaya_tenaga_kerja_langsung',
        'biaya_overhead_pabrik',
        'persediaan_bdp_akhir',
        'harga_pokok_produksi',
        'persediaan_produk_jadi_akhir',
        'harga_pokok_penjualan',
        'biaya_pemasaran',
        'biaya_adm_umum',
    ];
}
