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
        $pembayaran->tagihanKonsinyasi
            ?->laporanKonsinyasi
            ?->penjualanKonsinyasi
            ?->refreshStatus();

        // Buat jurnal: Bank (D) / Piutang (K)
        \App\Services\JurnalPerpetualService::pembayaranTagihanKonsinyasi($pembayaran);
    });

    static::updated(function ($pembayaran) {
        $pembayaran->tagihanKonsinyasi
            ?->laporanKonsinyasi
            ?->penjualanKonsinyasi
            ?->refreshStatus();

        // Update jurnal jika jumlah berubah
        \App\Services\JurnalPerpetualService::pembayaranTagihanKonsinyasi($pembayaran);
    });

    static::deleted(function ($pembayaran) {
        $pembayaran->tagihanKonsinyasi
            ?->laporanKonsinyasi
            ?->penjualanKonsinyasi
            ?->refreshStatus();

        // Hapus jurnal terkait
        $noTagihan = $pembayaran->tagihanKonsinyasi?->no_tagihan ?? $pembayaran->tagihan_konsinyasi_id;
        $referensi = 'Jurnal Pembayaran Konsinyasi ' . $noTagihan . '-' . $pembayaran->id;
        $jurnal = \App\Models\JurnalUmum::where('keterangan', $referensi)->first();
        if ($jurnal) {
            $jurnal->details()->delete();
            $jurnal->delete();
        }
    });
}

}

