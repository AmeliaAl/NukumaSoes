<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_akun',
        'produk_expired',
        'jumlah_expired',
        'harga_pokok_per_pack',
        'tanggal_pengeluaran',
        'deskripsi',
        'nominal',
    ];

    protected $casts = [
        'tanggal_pengeluaran' => 'date',
        'nominal' => 'decimal:2',
    ];
}
