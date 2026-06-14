<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoAwal extends Model
{
    protected $table = 'saldoawal';

    protected $fillable = [
        'bulan',
        'tahun',
        'akun_id',
        'nominal',
        'jurnal_id',
    ];
    
    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'jurnal_id');
    }

    /**
     * Boot method untuk handle events
     */
    protected static function boot()
    {
        parent::boot();

        // Event setelah data dibuat
        static::created(function ($saldoAwal) {
            \App\Services\SaldoAwalService::generateJurnalForNewSaldoAwal($saldoAwal);
        });

        // Event setelah data dihapus
        static::deleted(function ($saldoAwal) {
            \App\Services\SaldoAwalService::deleteJurnalSaldoAwalIfEmpty(
                $saldoAwal->bulan,
                $saldoAwal->tahun
            );
        });
    }
}
