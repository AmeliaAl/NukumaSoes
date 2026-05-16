<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PerolehanAset;

class vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor';
    protected $fillable = [
        'nama_vendor',
        'alamat_vendor',
        'kontak_vendor',
    ];

     public function perolehanAset()
    {
        return $this->hasMany(PerolehanAset::class, 'id_vendor');
    }
}
