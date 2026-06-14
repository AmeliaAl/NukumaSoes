<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanBahanBaku extends Model
{
    use HasFactory;

    protected $table = 'penerimaan_bahan_baku';
    protected $primaryKey = 'id_penerimaan';

    protected $fillable = [
        'nomor_penerimaan',
        'id_permintaan_bahan',
        'id_admin',
        'tanggal_penerimaan',
        'supplier',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_penerimaan' => 'date',
    ];

    // Relasi: Penerimaan ini dari permintaan tertentu
    public function permintaanBahanBaku()
    {
        return $this->belongsTo(PermintaanBahanBaku::class, 'id_permintaan_bahan', 'id_permintaan_bahan');
    }

    // Relasi ke detail penerimaan
    public function details()
    {
        return $this->hasMany(PenerimaanBahanBakuDetail::class, 'id_penerimaan');
    }

    // Relasi: Penerimaan dicatat oleh admin
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    // Relasi ke stok bahan baku yang digenerate dari penerimaan ini
    public function stokBahanBaku()
    {
        return $this->hasMany(StokBahanBaku::class, 'id_penerimaan', 'id_penerimaan');
    }

    // Helper: Total biaya untuk keperluan akuntansi
    public function getTotalBiayaAttribute()
    {
        return $this->details()->sum('total_biaya');
    }
}