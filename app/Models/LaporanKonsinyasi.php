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
        if (! $this->detailLaporan()->exists()) {
            return;
        }

        $total    = (int) $this->detailLaporan()->sum('subtotal');
        $totalHpp = (float) $this->detailLaporan()->sum('subtotal_hpp');

        $this->updateQuietly([
            'total_laporan' => $total,
            'total_hpp'     => $totalHpp,
        ]);

        if ($this->tagihan) {
            $totalTerbayar = (int) $this->tagihan->total_terbayar;
            $sisaTagihan   = max($total - $totalTerbayar, 0);

            $this->tagihan->updateQuietly([
                'total_tagihan' => $total,
                'sisa_tagihan'  => $sisaTagihan,
                'status'        => $sisaTagihan <= 0 ? 'LUNAS' : 'BELUM LUNAS',
            ]);
        }

        // Pass nominal dan total_hpp langsung agar tidak bergantung pada state model
        if ($total > 0) {
            \App\Services\JurnalPerpetualService::laporanKonsinyasiDenganNominal($this, $total);
        }

        $this->penjualanKonsinyasi?->refreshTotalLaporan();
    }

    protected static function booted()
    {
        static::created(function ($laporan) {
            $laporan->tagihan()->create([
                'no_tagihan'      => \App\Models\TagihanKonsinyasi::generateNo(),
                'tanggal_tagihan' => $laporan->tanggal_laporan ?? now(),
                'jatuh_tempo'     => null,
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

            // Hapus jurnal penjualan konsinyasi
            $referensiPenjualan = 'Jurnal Laporan Konsinyasi ' . $laporan->no_laporan;
            $jurnalPenjualan    = \App\Models\JurnalUmum::where('keterangan', $referensiPenjualan)->first();
            if ($jurnalPenjualan) {
                $jurnalPenjualan->details()->delete();
                $jurnalPenjualan->delete();
            }

            // Hapus jurnal HPP konsinyasi
            $referensiHpp = 'Jurnal HPP Konsinyasi ' . $laporan->no_laporan;
            $jurnalHpp    = \App\Models\JurnalUmum::where('keterangan', $referensiHpp)->first();
            if ($jurnalHpp) {
                $jurnalHpp->details()->delete();
                $jurnalHpp->delete();
            }
        });
    }
}
