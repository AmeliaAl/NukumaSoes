<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomMesin extends Model
{
    use HasFactory;

    protected $table = 'bom_mesin';
    protected $primaryKey = 'id_bom_mesin';

    protected $fillable = [
        'id_produk',
        'nama_mesin',
        'keterangan',
    ];

    // Relasi: BOM mesin ini untuk produk tertentu
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
