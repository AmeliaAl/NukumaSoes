<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Aset;

class Pemeliharaan extends Model
{
    use HasFactory;
    protected $table = 'Pemeliharaan';
    protected $fillable= [
        'aset_id',
        'tanggal',      
        'jenis_perbaikan',
        'metode_pembayaran',
        'id_akun',
        'biaya',
        'keterangan',
        'tambah_umur'
    ];

     public function aset()
    {
        return $this->belongsTo(Aset::class);
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun');
    }
    
    protected static function booted()
    {
        static::created(function ($pemeliharaan) {
            if ($pemeliharaan->jenis_perbaikan === 'peningkatan') {
                $pemeliharaan->aset->increment('nilai_perolehan', $pemeliharaan->biaya);
            }
        });
    }
}
