<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBahanBaku extends Model
{
    use HasFactory;

    protected $table = 'stok_bahan_baku';
    protected $primaryKey = 'id_stok';

    protected $fillable = [
        'id_bahan',
        'id_penerimaan',  // DITAMBAHKAN: Foreign key ke tabel penerimaan_bahan_baku
        'tanggal_masuk',
        'jumlah_masuk',
        'harga_per_satuan',
        'sisa_stok',
        'sumber',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'jumlah_masuk' => 'decimal:2',
        'harga_per_satuan' => 'decimal:2',
        'sisa_stok' => 'decimal:2',
    ];

    // Relasi: Stok ini milik bahan baku tertentu
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan', 'id_bahan');
    }

    // Relasi: Stok ini berasal dari penerimaan tertentu
    public function penerimaanBahanBaku()
    {
        return $this->belongsTo(PenerimaanBahanBaku::class, 'id_penerimaan', 'id_penerimaan');
    }

    // Relasi: Stok ini dipakai di banyak pemakaian (FIFO tracking)
    public function pemakaianBahanBaku()
    {
        return $this->hasMany(PemakaianBahanBaku::class, 'id_stok', 'id_stok');
    }

    // Helper: Cek apakah stok masih tersedia
    public function isTersedia()
    {
        return $this->status === 'tersedia' && $this->sisa_stok > 0;
    }

    // Helper: Update status jika habis
    public function updateStatus()
    {
        if ($this->sisa_stok <= 0) {
            $this->status = 'habis';
            $this->save();
        }
    }

    // Helper: Kurangi stok (untuk FIFO)
    public function kurangiStok($jumlah)
    {
        $this->sisa_stok -= $jumlah;
        $this->save();
        $this->updateStatus();
    }

    // Scope: Query stok yang tersedia untuk FIFO
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia')
                     ->where('sisa_stok', '>', 0);
    }

    // Scope: Query stok berdasarkan bahan (untuk FIFO)
    public function scopeByBahan($query, $idBahan)
    {
        return $query->where('id_bahan', $idBahan);
    }

    // Scope: Order by FIFO (tanggal masuk paling lama)
    public function scopeFifo($query)
    {
        return $query->orderBy('tanggal_masuk', 'asc')
                     ->orderBy('id_stok', 'asc');
    }
} 