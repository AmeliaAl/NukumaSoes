<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturPenjualan extends Model
{
    use HasFactory;

    protected $table = 'retur_penjualan';
    protected $guarded = [];

    public function penjualanNonKonsinyasi()
    {
        return $this->belongsTo(PenjualanNonKonsinyasi::class, 'penjualan_non_konsinyasi_id');
    }

    public function penjualanKonsinyasi()
    {
        return $this->belongsTo(PenjualanKonsinyasi::class, 'penjualan_konsinyasi_id');
    }

    public function detailRetur()
    {
        return $this->hasMany(DetailReturPenjualan::class, 'retur_id');
    }

    /**
     * Label sumber penjualan untuk ditampilkan di tabel.
     */
    public function getSumberAttribute(): string
    {
        if ($this->penjualan_non_konsinyasi_id) {
            return $this->penjualanNonKonsinyasi?->no_invoice ?? '-';
        }

        if ($this->penjualan_konsinyasi_id) {
            return $this->penjualanKonsinyasi?->no_konsinyasi ?? '-';
        }

        return '-';
    }

    public static function getNoRetur(): string
    {
        $date = now()->format('Ymd');

        $last = self::whereDate('created_at', today())
            ->orderByDesc('id')
            ->value('no_retur');

        if (! $last) {
            return "RTR-{$date}-001";
        }

        $lastNumber = (int) substr($last, -3);
        $next = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return "RTR-{$date}-{$next}";
    }
}
