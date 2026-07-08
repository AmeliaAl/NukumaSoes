<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku';
    protected $primaryKey = 'id_bahan';

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'satuan',
        'satuan_beli',
        'isi_per_kemasan',
        'stok_minimum',
        'stok_saat_ini',
        'status',
        'jenis_bahan',
        'keterangan',
    ];

    protected $casts = [
        'stok_minimum' => 'decimal:2',
        'stok_saat_ini' => 'decimal:2',
        'isi_per_kemasan' => 'decimal:2',
    ];

    // Relasi: Bahan baku punya banyak stok (batch-batch masuk) - untuk FIFO
    public function stokBahanBaku()
    {
        return $this->hasMany(StokBahanBaku::class, 'id_bahan', 'id_bahan');
    }

    // Relasi: Bahan baku punya banyak stok yang masih tersedia (untuk FIFO)
    public function stokTersedia()
    {
        return $this->hasMany(StokBahanBaku::class, 'id_bahan', 'id_bahan')
                    ->where('status', 'tersedia')
                    ->where('sisa_stok', '>', 0)
                    ->orderBy('tanggal_masuk', 'asc'); // FIFO: yang lama dulu
    }

    // Relasi: Bahan baku punya banyak permintaan
    public function permintaanBahanBaku()
    {
        return $this->hasMany(PermintaanBahanBaku::class, 'id_bahan', 'id_bahan');
    }

    // Relasi: Bahan baku punya banyak penerimaan
    public function penerimaanBahanBaku()
    {
        return $this->hasMany(PenerimaanBahanBaku::class, 'id_bahan', 'id_bahan');
    }

    // Relasi: Bahan baku punya banyak pemakaian
    public function pemakaianBahanBaku()
    {
        return $this->hasMany(PemakaianBahanBaku::class, 'id_bahan', 'id_bahan');
    }

    // Helper: Cek apakah stok menipis
    public function isStokMenipis()
    {
        return $this->stok_saat_ini <= $this->stok_minimum;
    }

    // Helper: Get stok tersedia untuk FIFO
    public function getStokFifo()
    {
        return $this->stokTersedia()->get();
    }
}