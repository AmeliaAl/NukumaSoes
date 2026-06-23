<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranUtangJangkaPanjang extends Model
{
    protected $table = 'pembayaran_utang_jangka_panjang';

    protected $fillable = [
        'utang_jangka_panjang_id',
        'tanggal_bayar',
        'nominal',
        'akun_id',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function utangJangkaPanjang()
    {
        return $this->belongsTo(UtangJangkaPanjang::class, 'utang_jangka_panjang_id');
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }
}
