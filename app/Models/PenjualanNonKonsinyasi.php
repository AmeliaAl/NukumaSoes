<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanNonKonsinyasi extends Model
{
    use HasFactory;

    protected $table = 'penjualan_non_konsinyasi';
    protected $guarded = [];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'penjualan_id');
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualanNonKonsinyasi::class, 'penjualan_id');
    }

    public static function getNoInvoice(): string
    {
        $date = now()->format('Ymd');

        $last = self::whereDate('created_at', today())
            ->orderByDesc('id')
            ->value('no_invoice');

        if (! $last) {
            return "NKSY-{$date}-001";
        }

        $lastNumber = (int) substr($last, -3);
        $next = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return "NKSY-{$date}-{$next}";
    }

    public function getTotalTerbayarAttribute(): float
    {
        return $this->pembayaran()->sum('jumlah_bayar');
    }

    public function getSisaPiutangAttribute(): float
    {
        return max(
            ($this->total ?? 0) - $this->total_terbayar,
            0
        );
    }

    public function getStatusAttribute(): string
    {
        if (($this->total ?? 0) <= 0) {
            return 'DRAFT';
        }

        if ($this->total_terbayar >= $this->total) {
            return 'LUNAS';
        }

        return 'BELUM LUNAS';
    }

    public function refreshStatus(): void
    {
        $totalBayar = $this->pembayaran()->sum('jumlah_bayar');

        if ($totalBayar > $this->total) {
            throw new \Exception('Total pembayaran melebihi total penjualan');
        }

        $this->updateQuietly([
            'total_terbayar' => $totalBayar,
        ]);
    }

    public function hitungTotal(): void
    {
        $total = (int) $this->detailPenjualan()->sum('subtotal');

        $this->updateQuietly([
            'total' => $total,
        ]);

        if ($total > 0) {
            \App\Services\JurnalPerpetualService::penjualanNonKonsinyasi($this);
        }
    }

    public function isLocked(): bool
    {
        return $this->status === 'LUNAS';
    }
}