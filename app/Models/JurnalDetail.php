<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalDetail extends Model
{
    protected $table = 'jurnal_detail';
    protected $fillable = [
        'id_jurnal',
        'jurnal_umum_id',
        'no_akun',
        'akun_id',
        'deskripsi',
        'debit',
        'credit',
        'kredit',
    ];

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal');
    }

    public function jurnalUmum()
    {
        return $this->belongsTo(JurnalUmum::class, 'id_jurnal');
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class,  'no_akun');
    }
}
