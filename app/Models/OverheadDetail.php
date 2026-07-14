<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OverheadDetail extends Model
{
    protected $fillable = [
        'overhead_id',
        'coa_id',
        'coa_pembayaran_id',
        'keterangan',
        'nominal',
    ];

    public function overhead()
    {
        return $this->belongsTo(Overhead::class);
    }

    public function coa()
    {
        return $this->belongsTo(Akun::class, 'coa_id');
    }

    public function paymentCoa()
    {
        return $this->belongsTo(Akun::class, 'coa_pembayaran_id');
    }
}
