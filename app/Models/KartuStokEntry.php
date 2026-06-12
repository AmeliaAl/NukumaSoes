<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuStokEntry extends Model
{
    protected $fillable = [
        'tanggal',
        'keterangan',
        'id_transaksi',
        'masuk',
        'keluar',
        'harga',
        'total_harga',
        'no_batch',
        'kode_produk',
        'nama_produk',
    ];
}
