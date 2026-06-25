<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailReturPenjualan extends Model
{
    protected $table = 'detail_retur_penjualan';

    protected $fillable = [
        'retur_id',
        'barang_id',
        'qty',
        'kondisi',
    ];

    public function retur()
    {
        return $this->belongsTo(ReturPenjualan::class, 'retur_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
