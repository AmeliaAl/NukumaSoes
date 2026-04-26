<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetoranKonsinyasi extends Model
{
    use HasFactory;

    protected $table = 'setoran_konsinyasi';

    protected $guarded = [];

    /* ======================
     | RELASI
     ====================== */
    public function penjualanKonsinyasi()
    {
        return $this->belongsTo(PenjualanKonsinyasi::class);
    }

    /* ======================
     | EVENT MODEL
     ====================== */
    protected static function booted()
    {
        static::creating(function ($setoran) {
            $penjualan = $setoran->penjualanKonsinyasi;

            if ($penjualan->status === 'LUNAS') {
                throw new \Exception('Penjualan sudah lunas.');
            }

            $sisa = $penjualan->total_terjual - $penjualan->total_setoran;

            if ($setoran->jumlah_setor > $sisa) {
                throw new \Exception('Jumlah setor melebihi sisa piutang.');
            }
        });

        static::saved(fn ($s) => $s->penjualanKonsinyasi->hitungTotalSetoran());
        static::deleted(fn ($s) => $s->penjualanKonsinyasi->hitungTotalSetoran());
    }

}
