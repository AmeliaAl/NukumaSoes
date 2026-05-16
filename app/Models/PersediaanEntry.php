<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\HargaProduk;

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
    ];
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function getCategoryPriceAttribute()
    {
        $kategoriName = $this->inventory->kategori ?? null;
        if (!$kategoriName) return $this->harga;

        $category = Category::where('nama_kategori', $kategoriName)->first();
        if (!$category) return $this->harga;

        $hargaProduk = HargaProduk::where('kategori_id', $category->id)->first();
        return $hargaProduk ? $hargaProduk->harga : $this->harga;
    }
}
