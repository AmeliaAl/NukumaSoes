<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokProduk extends Model
{
    use HasFactory;

    protected $table = 'stok_produk';
    protected $primaryKey = 'id_stok_produk';

    protected $fillable = [
        'id_permintaan_produksi',
        'id_produk',
        'tipe_stok',
        'jumlah',
        'sisa_stok',
        'satuan',
        'harga_pokok_per_unit',
        'total_nilai',
        'status',
        'tanggal_masuk',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_masuk'       => 'date',
        'jumlah'              => 'decimal:2',
        'sisa_stok'           => 'decimal:2',
        'harga_pokok_per_unit'=> 'decimal:2',
        'total_nilai'         => 'decimal:2',
    ];

    // Relasi: Stok ini milik produk tertentu
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    // Relasi: Stok ini dihasilkan dari job order tertentu
    public function permintaanProduksi()
    {
        return $this->belongsTo(PermintaanProduksi::class, 'id_permintaan_produksi', 'id_permintaan_produksi');
    }

    // Relasi: Stok WIP ini dipakai di banyak pemakaian (FIFO tracking)
    public function pemakaianBahanBaku()
    {
        return $this->hasMany(PemakaianBahanBaku::class, 'id_stok_produk', 'id_stok_produk');
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

    // Helper: Label tipe stok
    public function getTipeLabelAttribute(): string
    {
        return match($this->tipe_stok) {
            'wip_kulit'    => 'WIP — Kulit (Setengah Jadi)',
            'barang_jadi'  => 'Barang Jadi',
            default        => '-',
        };
    }

    public function getTipeBadgeAttribute(): string
    {
        return match($this->tipe_stok) {
            'wip_kulit'   => '<span class="badge bg-warning text-dark"><i class="fas fa-box-open me-1"></i>WIP Kulit</span>',
            'barang_jadi' => '<span class="badge bg-success"><i class="fas fa-box me-1"></i>Barang Jadi</span>',
            default       => '<span class="badge bg-secondary">-</span>',
        };
    }

    // Scope: Filter WIP saja
    public function scopeWip($query)
    {
        return $query->where('tipe_stok', 'wip_kulit');
    }

    // Scope: Filter Barang Jadi saja
    public function scopeBarangJadi($query)
    {
        return $query->where('tipe_stok', 'barang_jadi');
    }

    // Scope: Hanya stok tersedia
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia')
                     ->where('sisa_stok', '>', 0);
    }

    // Scope: Order by FIFO (tanggal masuk paling lama)
    public function scopeFifo($query)
    {
        return $query->orderBy('tanggal_masuk', 'asc')
                     ->orderBy('id_stok_produk', 'asc');
    }
}
