<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HargaBarang extends Model
{
    protected $table = 'harga_barang';

    protected $fillable = [
        'barang_id',
        'jenis_mitra',
        'harga',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}