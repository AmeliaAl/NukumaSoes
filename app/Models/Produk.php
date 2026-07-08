<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;



    protected $table = 'produk';
    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'kategori',
        'tipe_produk',
        'satuan_produk',
        'deskripsi',
        'status',
    ];

    // Tipe: kulit (WIP per gram), isi (setelah filling), jadi (finished goods)
    public function getTipeLabelAttribute(): string
    {
        return match($this->tipe_produk) {
            'kulit' => 'Kulit (WIP / Setengah Jadi)',
            'isi'   => 'Isi (Setelah Filling)',
            'jadi'  => 'Barang Jadi',
            default => 'Barang Jadi',
        };
    }

    public function getTipeBadgeAttribute(): string
    {
        return match($this->tipe_produk) {
            'kulit' => '<span class="badge bg-warning text-dark">Kulit/WIP</span>',
            'isi'   => '<span class="badge bg-info">Isi</span>',
            'jadi'  => '<span class="badge bg-success">Barang Jadi</span>',
            default => '<span class="badge bg-secondary">-</span>',
        };
    }

    // Relasi: Produk punya stok produk (hasil produksi)
    public function stokProduk()
    {
        return $this->hasMany(StokProduk::class, 'id_produk', 'id_produk');
    }

    // Relasi: Produk punya BOM Bahan Baku
    public function bomBahan()
    {
        return $this->hasMany(BomBahan::class, 'id_produk', 'id_produk');
    }

    // Relasi: Produk punya banyak permintaan produksi (job order)
    public function permintaanProduksi()
    {
        return $this->hasMany(PermintaanProduksi::class, 'id_produk', 'id_produk');
    }

    // Relasi: Produk punya banyak job yang selesai
    public function jobSelesai()
    {
        return $this->hasMany(PermintaanProduksi::class, 'id_produk', 'id_produk')
                    ->where('status', 'selesai');
    }

    // Helper: Total jumlah produksi bulan ini
    public function getTotalProduksiBulanIni()
    {
        return $this->permintaanProduksi()
                    ->whereMonth('tanggal_mulai', now()->month)
                    ->whereYear('tanggal_mulai', now()->year)
                    ->sum('jumlah_produksi');
    }

    // Helper: Rata-rata harga pokok produk
    public function getRataRataHargaPokok()
    {
        return $this->jobSelesai()
                    ->avg('harga_pokok_per_unit');
    }
}
