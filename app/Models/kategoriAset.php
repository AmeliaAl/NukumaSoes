<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Aset;

class kategoriAset extends Model
{
    /** @use HasFactory<\Database\Factories\KategoriAsetFactory> */
    use HasFactory;
    protected $table = 'kategori_aset';
    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'jenis_aset',
        'metode_penyusutan',
        'masa_manfaat',
        'interval_pemeliharaan',
    ];
    
    public function aset()
    {
        return $this->hasMany(Aset::class, 'id_kategori');
    }

    public function akun()
    {
        return $this->hasMany(Akun::class, 'id_kategori');
    }

}
