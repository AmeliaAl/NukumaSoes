<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PenjualanKonsinyasi extends Model
{
    use HasFactory;

    protected $table = 'penjualan_konsinyasi';
    protected $guarded = [];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'kode_mitra', 'kode_mitra');
    }

    public function detailKonsinyasi()
    {
        return $this->hasMany(DetailKonsinyasi::class);
    }

    public function laporanKonsinyasi()
    {
        return $this->hasMany(
            LaporanKonsinyasi::class,
            'penjualan_konsinyasi_id',
            'id'
        );
    }

    public function pembayaranKonsinyasi()
    {
        return $this->hasMany(
            PembayaranKonsinyasi::class,
            'no_konsinyasi',
            'no_konsinyasi'
        );
    }

    public function pembayarans()
    {
        return $this->hasMany(
            \App\Models\Pembayaran::class,
            'penjualan_id'
        );
    }

    public function tagihanKonsinyasi()
    {
        return $this->hasMany(
            TagihanKonsinyasi::class,
            'no_konsinyasi',
            'no_konsinyasi'
        );
    }

    public function getTotalLaporanAttribute(): int
    {
        return (int) $this->laporanKonsinyasi()->sum('total_laporan');
    }

    public function getTotalBarangAttribute(): int
    {
        return (int) $this->detailKonsinyasi()->sum('subtotal');
    }

    public function getTotalPiutangAttribute(): int
    {
        return max(
            (int) $this->total_barang - (int) ($this->diskon ?? 0),
            0
        );
    }

    public function getSisaPiutangAttribute(): int
    {
        return max(
            (int) $this->total_piutang - (int) ($this->total_terbayar ?? 0),
            0
        );
    }

    public static function generateNo(): string
    {
        $date = Carbon::now()->format('Ymd');

        $last = DB::table('penjualan_konsinyasi')
            ->whereDate('created_at', today())
            ->orderByDesc('id')
            ->value('no_konsinyasi');

        if (! $last) {
            return "KSY-{$date}-001";
        }

        $lastNumber = (int) substr($last, -3);
        $next = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return "KSY-{$date}-{$next}";
    }

    public function refreshStatus(): void
    {
        $totalLaporan = (int) $this->laporanKonsinyasi()->sum('total_laporan');
        $totalTitip = (int) $this->detailKonsinyasi()->sum('subtotal');

        if ($totalLaporan <= 0) {
            $status = 'BELUM TERJUAL';
        } elseif ($totalLaporan < $totalTitip) {
            $status = 'SEBAGIAN TERJUAL';
        } else {
            $status = 'SELESAI';
        }

        $this->updateQuietly([
            'status' => $status,
        ]);
    }

    public function hitungTotalKonsinyasi(): void
    {
        $this->updateQuietly([
            'total_konsinyasi' => $this->total_barang,
        ]);
    }

    public function refreshTotalLaporan(): void
    {
        $total = (int) $this->laporanKonsinyasi()->sum('total_laporan');

        $this->updateQuietly([
            'total_laporan' => $total,
        ]);

        $this->refreshStatus();
    }

    protected static function booted(): void
    {
        static::created(function ($penjualan) {
            Log::info('EVENT CREATED PENJUALAN KONSINYASI', [
                'id' => $penjualan->id,
                'no_konsinyasi' => $penjualan->no_konsinyasi,
            ]);
        });
    }
}