<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalDetail extends Model
{
    protected $table = 'jurnal_detail';
    protected $fillable = [
        'id_jurnal',
        'no_akun',
        'deskripsi',
        'debit',
        'credit',
    ];

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal');
    }


    public function akun()
    {
        return $this->belongsTo(Akun::class,  'no_akun');
    }
}
