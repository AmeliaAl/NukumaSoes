<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Modal extends Model
{
    use HasFactory;
    protected $table = 'modal';
    protected $fillable= [
        'tanggal',
        'jenis',
        'id_akun',
        'jumlah',
    ];

    public function akun()
{
    return $this->belongsTo(Akun::class, 'id_akun');
}

protected static function booted()
{
    /*static::created(function ($modal) {

        $akun = $modal->akun;

        if ($modal->jenis == 'setoran') {
            $akun->saldo += $modal->jumlah;
        }

        if ($modal->jenis == 'prive') {
            $akun->saldo -= $modal->jumlah;
        }

        $akun->save();
    });*/
}
}
