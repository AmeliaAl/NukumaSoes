<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    // Nama tabel baru
    protected $table = 'bahan_baku';

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'jenis_bahan',
        'satuan',
        'isi_per_kemasan',
        'stok_minimum',
        'stok_saat_ini',
    ];
}
