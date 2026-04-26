<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailLaporanKonsinyasi extends Model
{
    protected $table = 'detail_laporan_konsinyasi';
    protected $guarded = [];

    public function laporanKonsinyasi()
    {
        return $this->belongsTo(LaporanKonsinyasi::class, 'no_laporan', 'no_laporan');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}