<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    protected $fillable = [
        'tanggal',
        'keterangan',
        'ref',
        'debit',
        'kredit',
        'id_transaksi',
    ];
}
