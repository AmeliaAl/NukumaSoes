<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $guard = 'admin';
    
    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'nama_lengkap',
        'username',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * Relationships
     */
    
    public function permintaanBahanBaku()
    {
        return $this->hasMany(PermintaanBahanBaku::class, 'id_admin', 'id_admin');
    }

    public function penerimaanBahanBaku()
    {
        return $this->hasMany(PenerimaanBahanBaku::class, 'id_admin', 'id_admin');
    }

    public function permintaanProduksi()
    {
        return $this->hasMany(PermintaanProduksi::class, 'id_admin', 'id_admin');
    }

    public function pemakaianBahanBaku()
    {
        return $this->hasMany(PemakaianBahanBaku::class, 'id_admin', 'id_admin');
    }

    public function biayaTenagaKerja()
    {
        return $this->hasMany(BiayaTenagaKerja::class, 'id_admin', 'id_admin');
    }

    public function biayaOverheadPabrik()
    {
        return $this->hasMany(BiayaOverheadPabrik::class, 'id_admin', 'id_admin');
    }
}