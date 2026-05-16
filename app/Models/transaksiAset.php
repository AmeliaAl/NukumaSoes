<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PerolehanAset;

class transaksiAset extends Model
{
    use HasFactory;
    protected $table = 'transaksi_aset';
    protected $fillable = [
        'tanggal',
        'tipe_transaksi',
        'total_nilai',
    ];

     public function perolehan()
    {
        return $this->hasOne(PerolehanAset::class, 'id_transaksi');
    }

}
