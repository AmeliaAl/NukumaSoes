<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoAwal extends Model
{
    protected $fillable = [
        'no_bukti',
        'tanggal',
        'coa_id',
        'nominal',
        'keterangan',
    ];

    public function coa()
    {
        return $this->belongsTo(Akun::class, 'coa_id');
    }
}
