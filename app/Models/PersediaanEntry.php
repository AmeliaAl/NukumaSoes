<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class PersediaanEntry extends Model
{
    protected $fillable = [
        'id_transaksi',
        'tanggal',
        'kode_produk',
        'inventory_id',
        'nama_produk',
        'jumlah_masuk',
        'stok_awal',
        'jumlah_per_batch',
        'harga',
        'total_harga',
        'no_batch',
        'jumlah_pack',
        'bbb',
        'btkl',
        'bop',
        'harga_dasar_jual',
        'margin',
    ];
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function getCategoryPriceAttribute()
    {
        $product = \App\Models\Product::where('kode_produk', $this->kode_produk)->first();
        return $product ? $product->harga : $this->harga;
    }
}
