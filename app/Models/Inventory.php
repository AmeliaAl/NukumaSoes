<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'kode_produk',
        'no_batch',
        'nama_produk',
        'rasa_produk',
        'kategori',
        'stok_awal',
        'jumlah',
        'jumlah_per_batch',
        'harga',
        'tgl_masuk',
        'tgl_expired',
        'status',
        'sisa_hari',
        'masa_simpan',
        'satuan_masa_simpan',
    ];

    protected $casts = [
        'tgl_masuk' => 'date',
        'tgl_expired' => 'date',
    ];
}
