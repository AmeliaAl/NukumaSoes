<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class pemakaianPersediaan extends Model
{
    protected $table = 'pemakaian_persediaan';
    protected $fillable = [
        'aset_lancar_id',
        'tanggal',
        'satuan',
        'jumlah',
        'keterangan',
    ];

    public function asetLancar()
    {
        return $this->belongsTo(AsetLancar::class);
    }
}
