<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pembelian;
use App\Models\BahanBaku;

class PembelianDetail extends Model
{
    protected $fillable = [
        'pembelian_id',
        'bahan_baku_id',
        'qty',
        'isi_per_kemasan',
        'harga',
        'subtotal',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
