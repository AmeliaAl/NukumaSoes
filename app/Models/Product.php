<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_produk',
        'rasa_produk',
        'kode_produk',
        'kategori',
        'satuan',
        'stok_minimum',
        'stok_awal',
        'tgl_masuk',
        'jumlah',
        'harga',
        'harga_jual',
        'tgl_expired',
        'masa_simpan',
        'satuan_masa_simpan',
        'sisa_hari',
        'status',
    ];

    protected $casts = [
        'tgl_masuk' => 'date',
        'tgl_expired' => 'date',
        'sisa_hari' => 'int',
    ];

    /**
     * Sync the product's total quantity from the Inventory batches.
     */
    public static function syncQuantity($kode_produk)
    {
        $totalStock = \App\Models\Inventory::where('kode_produk', $kode_produk)
            ->where('status', '!=', 'Expired')
            ->sum('jumlah');
        return self::where('kode_produk', $kode_produk)->update(['jumlah' => $totalStock]);
    }

    /**
     * Get the inventories associated with the product.
     */
    public function inventories()
    {
        return $this->hasMany(\App\Models\Inventory::class, 'kode_produk', 'kode_produk');
    }
}
