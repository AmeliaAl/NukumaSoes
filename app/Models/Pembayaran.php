<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $guarded = [];

    public function penjualan()
    {
        return $this->belongsTo(PenjualanNonKonsinyasi::class, 'penjualan_id');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public static function getKodePembayaran(): string
    {
        $data = DB::select("
            SELECT IFNULL(MAX(kode_pembayaran), 'BYR-000') AS kode_pembayaran
            FROM pembayaran
        ");

        $kode = $data[0]->kode_pembayaran;

        $noAwal  = (int) substr($kode, -3);
        $noAkhir = $noAwal + 1;

        return 'BYR-' . str_pad($noAkhir, 3, '0', STR_PAD_LEFT);
    }

    protected static function booted()
    {
        static::creating(function ($pembayaran) {
            $penjualan = $pembayaran->penjualan;

            $sisa = ($penjualan->total ?? 0) - ($penjualan->total_terbayar ?? 0);

            if ($pembayaran->jumlah_bayar > $sisa) {
                throw new \Exception('Jumlah pembayaran melebihi sisa piutang');
            }
        });

        static::created(function ($pembayaran) {
            $pembayaran->penjualan?->refreshStatus();

            \App\Services\JurnalPerpetualService::pembayaranNonKonsinyasi($pembayaran);
        });

        static::deleted(function ($pembayaran) {
            $pembayaran->penjualan?->refreshStatus();
        });
    }
}