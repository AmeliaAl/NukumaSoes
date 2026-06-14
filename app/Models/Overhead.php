<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Coa;

class Overhead extends Model
{
    protected $fillable = [
        'tanggal',
        'coa_id',
        'coa_pembayaran_id',
        'keterangan',
        'nominal',
    ];

    public function coa()
    {
        return $this->belongsTo(Coa::class);
    }

    public function paymentCoa()
    {
        return $this->belongsTo(Coa::class, 'coa_pembayaran_id');
    }
}