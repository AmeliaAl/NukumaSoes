<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranTagihanKonsinyasi extends Model
{
    protected $table = 'pembayaran_konsinyasi';
    protected $guarded = [];

    public function tagihanKonsinyasi()
    {
        return $this->belongsTo(TagihanKonsinyasi::class);
    }

    protected static function booted()
{
    static::created(function ($pembayaran) {
        $pembayaran->tagihan
            ?->laporanKonsinyasi
            ?->penjualanKonsinyasi
            ?->refreshStatus();
    });

    static::updated(function ($pembayaran) {
        $pembayaran->tagihan
            ?->laporanKonsinyasi
            ?->penjualanKonsinyasi
            ?->refreshStatus();
    });

    static::deleted(function ($pembayaran) {
        $pembayaran->tagihan
            ?->laporanKonsinyasi
            ?->penjualanKonsinyasi
            ?->refreshStatus();
    });
}

}

