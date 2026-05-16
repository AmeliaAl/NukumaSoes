<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $table = 'jurnal';
    protected $fillable = [
        'tanggal',
        'no_referensi',
        'deskripsi',
    ];

    public function jurnalDetail()
    {
        return $this->hasMany(JurnalDetail::class, 'id_jurnal');
    }

     public function isBalanced()
    {
        $debit = $this->detailjurnal->sum('debit');
        $kredit = $this->detailjurnal->sum('credit');
        return $debit == $kredit;
    }

    protected $casts = [
    'tanggal' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(JurnalDetail::class, 'id_jurnal');
    }

    public function utangJangkaPanjang()
    {
        return $this->hasOne(UtangJangkaPanjang::class, 'jurnal_id');
    }

    public function saldoAwals()
    {
        return $this->hasMany(\App\Models\saldoawal::class, 'jurnal_id');
    }
}
