<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TagihanKonsinyasi extends Model
{
    protected $table = 'tagihan_konsinyasi';
    protected $guarded = [];

    public const STATUS_BELUM_LUNAS = 'BELUM LUNAS';
    public const STATUS_LUNAS = 'LUNAS';

    public function laporanKonsinyasi()
    {
        return $this->belongsTo(LaporanKonsinyasi::class);
    }

    public function pembayaranTagihan()
    {
        return $this->hasMany(PembayaranTagihanKonsinyasi::class);
    }

    public static function generateNo(): string
    {
        $date = now()->format('Ymd');

        $last = self::where('no_tagihan', 'like', "TAG-{$date}-%")
            ->orderByDesc('no_tagihan')
            ->value('no_tagihan');

        if (! $last) {
            return "TAG-{$date}-001";
        }

        $no = (int) substr($last, -3) + 1;

        return "TAG-{$date}-" . str_pad($no, 3, '0', STR_PAD_LEFT);
    }

    public function refreshStatus(): void
    {
        $totalBayar = (int) $this->pembayaranTagihan()->sum('jumlah_bayar');
        $sisa = (int) $this->total_tagihan - $totalBayar;

        $this->updateQuietly([
            'total_terbayar' => $totalBayar,
            'sisa_tagihan'   => max($sisa, 0),
            'status'         => $sisa <= 0
                ? self::STATUS_LUNAS
                : self::STATUS_BELUM_LUNAS,
        ]);

        $this->laporanKonsinyasi
            ?->penjualanKonsinyasi
            ?->refreshStatus();
    }

    public function hitungTotalBayar(): void
    {
        $total = (int) $this->pembayaranTagihan()->sum('jumlah_bayar');
        $sisa = (int) $this->total_tagihan - $total;

        $this->update([
            'total_terbayar' => $total,
            'sisa_tagihan'   => max($sisa, 0),
            'status'         => $sisa <= 0
                ? self::STATUS_LUNAS
                : self::STATUS_BELUM_LUNAS,
        ]);
    }

    protected static function booted(): void
    {
        static::creating(function ($tagihan) {
            $tagihan->total_terbayar = 0;
            $tagihan->sisa_tagihan = $tagihan->total_tagihan;
            $tagihan->status = self::STATUS_BELUM_LUNAS;
        });
    }
}