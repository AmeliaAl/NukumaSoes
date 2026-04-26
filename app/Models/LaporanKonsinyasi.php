<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanKonsinyasi extends Model
{
    use HasFactory;

    protected $table = 'laporan_konsinyasi';
    protected $guarded = [];

    public function penjualanKonsinyasi()
    {
        return $this->belongsTo(
            PenjualanKonsinyasi::class,
            'penjualan_konsinyasi_id',
            'id'
        );
    }

    public function tagihan()
    {
        return $this->hasOne(TagihanKonsinyasi::class, 'laporan_konsinyasi_id');
    }

    public function detailLaporan()
    {
        return $this->hasMany(DetailLaporanKonsinyasi::class, 'no_laporan', 'no_laporan');
    }

    public static function generateNo(): string
    {
        $date = now()->format('Ymd');

        $last = self::where('no_laporan', 'like', "LAP-{$date}-%")
            ->orderByDesc('no_laporan')
            ->value('no_laporan');

        if (! $last) {
            return "LAP-{$date}-001";
        }

        $lastNumber = (int) substr($last, -3);
        $next = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return "LAP-{$date}-{$next}";
    }

    public function refreshTotalLaporan(): void
    {
        // jangan sentuh laporan lama yang belum punya detail
        if (! $this->detailLaporan()->exists()) {
            return;
        }

        $total = (int) $this->detailLaporan()->sum('subtotal');

        $this->updateQuietly([
            'total_laporan' => $total,
        ]);

        if ($this->tagihan) {
            $totalTerbayar = (int) $this->tagihan->total_terbayar;
            $sisaTagihan = max($total - $totalTerbayar, 0);

            $this->tagihan->updateQuietly([
                'total_tagihan' => $total,
                'sisa_tagihan'  => $sisaTagihan,
                'status'        => $sisaTagihan <= 0 ? 'LUNAS' : 'BELUM LUNAS',
            ]);
        }

        $this->penjualanKonsinyasi?->refreshTotalLaporan();
    }

    protected static function booted()
    {
        static::created(function ($laporan) {
            $laporan->tagihan()->create([
                'no_tagihan'      => \App\Models\TagihanKonsinyasi::generateNo(),
                'tanggal_tagihan' => now(),
                'jatuh_tempo'     => now()->addDays(30),
                'total_tagihan'   => 0,
                'total_terbayar'  => 0,
                'sisa_tagihan'    => 0,
                'status'          => 'BELUM LUNAS',
            ]);

            $laporan->penjualanKonsinyasi?->refreshStatus();
        });

        static::updated(function ($laporan) {
            $laporan->penjualanKonsinyasi?->refreshStatus();
        });

        static::deleted(function ($laporan) {
            $laporan->tagihan()?->delete();
            $laporan->penjualanKonsinyasi?->refreshStatus();
        });
    }
}