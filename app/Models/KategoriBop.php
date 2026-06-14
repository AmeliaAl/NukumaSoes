<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBop extends Model
{
    use HasFactory;

    protected $table = 'kategori_bop';
    protected $primaryKey = 'id_kategori_bop';

    protected $fillable = [
        'nama_kategori',
        'keterangan',
    ];

    // Relasi: Kategori BOP ini dipakai di banyak transaksi BOP
    public function biayaOverheadPabrik()
    {
        return $this->hasMany(BiayaOverheadPabrik::class, 'id_kategori_bop', 'id_kategori_bop');
    }
}
