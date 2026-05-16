<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyusutan extends Model
{
    protected $table = 'penyusutan';
    protected $fillable = [
        'aset_id',
        'periode',
        'beban_penyusutan',
        'akumulasi_penyusutan',
        'nilai_buku',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }
}
