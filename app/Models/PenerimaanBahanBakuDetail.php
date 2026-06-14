<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class PenerimaanBahanBakuDetail extends Model
{
    use HasFactory;

    protected $table = 'penerimaan_bahan_baku_detail';
    protected $primaryKey = 'id_penerimaan_detail';

    protected $fillable = [
        'id_penerimaan',
        'id_bahan',
        'jumlah_diterima',
        'harga_per_satuan',
        'total_biaya',
    ];

    public function penerimaanBahanBaku()
    {
        return $this->belongsTo(PenerimaanBahanBaku::class, 'id_penerimaan');
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan');
    }
}
