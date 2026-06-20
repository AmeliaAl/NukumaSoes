<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoAwal extends Model
{
    protected $guarded = [];

    public function coa()
    {
        return $this->belongsTo(coa::class, 'coa_id');
    }

     public function akun()
    {
        return $this->belongsTo(akun::class, 'akun_id');
    }
}
