<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailKonsinyasi extends Model
{
    use HasFactory;

    protected $table = 'detail_konsinyasi';
    protected $guarded = [];

    public function penjualanKonsinyasi()
    {
        return $this->belongsTo(PenjualanKonsinyasi::class, 'penjualan_konsinyasi_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function getSubtotalAttribute(): int
    {
        return max(
            (($this->qty_titip ?? 0) * ($this->harga_konsinyasi ?? 0)) - ($this->diskon ?? 0),
            0
        );
    }

    protected static function booted(): void
    {
        static::saving(function ($detail) {
            $detail->subtotal = max(
                (($detail->qty_titip ?? 0) * ($detail->harga_konsinyasi ?? 0)) - ($detail->diskon ?? 0),
                0
            );
        });

        static::saved(function ($detail) {
            if ($detail->penjualanKonsinyasi) {
                $detail->penjualanKonsinyasi->hitungTotalKonsinyasi();
                $detail->penjualanKonsinyasi->refresh();
            }
        });

        static::deleted(function ($detail) {
            if ($detail->penjualanKonsinyasi) {
                $detail->penjualanKonsinyasi->hitungTotalKonsinyasi();
                $detail->penjualanKonsinyasi->refresh();
            }
        });
    }
}