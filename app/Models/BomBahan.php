<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomBahan extends Model
{
    use HasFactory;

    protected $table = 'bom_bahan';
    protected $primaryKey = 'id_bom_bahan';

    protected $fillable = [
        'id_produk',
        'id_bahan',
        'id_produk_wip',
        'jumlah_kebutuhan',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_kebutuhan' => 'decimal:2',
    ];

    // Relasi: BOM ini untuk produk tertentu
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    // Relasi: BOM ini menggunakan bahan baku tertentu (bisa null jika WIP)
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan', 'id_bahan');
    }

    // Relasi: BOM ini menggunakan WIP tertentu (bisa null jika bahan baku biasa)
    public function produkWip()
    {
        return $this->belongsTo(Produk::class, 'id_produk_wip', 'id_produk');
    }
}
