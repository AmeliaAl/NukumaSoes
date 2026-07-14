<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overhead extends Model
{
    protected $fillable = [
        'tanggal',
        'jenis_periode',
        'periode_mulai',
        'periode_akhir',
        'coa_id',
        'coa_pembayaran_id',
        'keterangan',
        'nominal',
    ];

    public function coa()
    {
        return $this->belongsTo(Akun::class, 'coa_id');
    }

    public function paymentCoa()
    {
        return $this->belongsTo(Akun::class, 'coa_pembayaran_id');
    }

    public function details()
    {
        return $this->hasMany(OverheadDetail::class);
    }

    public function paymentProofs()
    {
        return $this->morphMany(PaymentProof::class, 'proofable');
    }
}