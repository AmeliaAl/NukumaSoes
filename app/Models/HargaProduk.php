<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaProduk extends Model
{
    protected $table = 'harga_produk';

    protected $fillable = [
        'kode_produk',
        'kategori_id',
        'jenis_mitra',
        'harga',
        'deskripsi',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'kategori_id');
    }
}
?>

