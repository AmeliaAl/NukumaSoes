<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchProduksi extends Model
{
    use HasFactory;

    protected $table = 'batch_produksi';
    protected $primaryKey = 'id_batch_produksi';

    protected $fillable = [
        'id_permintaan_produksi',
        'urutan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_target',
        'jumlah_hasil',
        'jenis_batch',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jumlah_target' => 'decimal:2',
        'jumlah_hasil' => 'decimal:2',
        'urutan' => 'integer',
    ];

    public function permintaanProduksi()
    {
        return $this->belongsTo(PermintaanProduksi::class, 'id_permintaan_produksi', 'id_permintaan_produksi');
    }
}
