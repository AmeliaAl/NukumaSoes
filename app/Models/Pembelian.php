<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;
use App\Models\BahanBaku;
use App\Models\Coa;

class Pembelian extends Model
{
    protected $fillable = [
        'no_pembelian',
        'tanggal',
        'supplier_id',
        'nomor_permintaan',
        'bahan_baku_id',
        'qty',
        'harga',
        'total',
        'subtotal',
        'diskon',
        'ongkir',
        'total_bersih',
        'grand_total',
        'pembayaran',
        'coa_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class);
    }

    public function details()
    {
        return $this->hasMany(PembelianDetail::class);
    }
}