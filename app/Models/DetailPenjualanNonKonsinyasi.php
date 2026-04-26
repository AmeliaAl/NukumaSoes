<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualanNonKonsinyasi extends Model
{
    protected $table = 'detail_penjualan_non_konsinyasi';

    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'qty',
        'harga',
        'diskon',
        'subtotal',
    ];

    public function penjualan()
    {
        return $this->belongsTo(
            PenjualanNonKonsinyasi::class,
            'penjualan_id'
        );
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }


}
