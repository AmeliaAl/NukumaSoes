<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermintaanBahanBakuDetail extends Model
{
    use HasFactory;

    protected $table = 'permintaan_bahan_baku_detail';
    protected $primaryKey = 'id_permintaan_detail';

    protected $fillable = [
        'id_permintaan_bahan',
        'id_bahan',
        'jumlah_permintaan',
        'jumlah_diterima',
        'status_penerimaan',
    ];

    public function permintaanBahanBaku()
    {
        return $this->belongsTo(PermintaanBahanBaku::class, 'id_permintaan_bahan');
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan');
    }
}
