<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemakaianBahanBaku extends Model
{
    use HasFactory;

    protected $table = 'pemakaian_bahan_baku';
    protected $primaryKey = 'id_pemakaian';

    protected $fillable = [
        'id_permintaan_produksi',
        'id_bahan',
        'id_stok',
        'id_produk_wip',
        'id_stok_produk',
        'id_admin',
        'tanggal_pemakaian',
        'jumlah_pakai',
        'harga_satuan',
        'total_biaya',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pemakaian' => 'date',
        'jumlah_pakai' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'total_biaya' => 'decimal:2',
    ];

    // ACCESSOR: Agar view bisa pakai harga_per_satuan (backward compatibility)
    // Database pakai harga_satuan, view bisa pakai harga_satuan or harga_per_satuan
    protected $appends = ['harga_per_satuan'];

    public function getHargaPerSatuanAttribute()
    {
        return $this->harga_satuan;
    }

    // Relasi: Pemakaian ini untuk job order tertentu
    public function permintaanProduksi()
    {
        return $this->belongsTo(PermintaanProduksi::class, 'id_permintaan_produksi', 'id_permintaan_produksi');
    }

    // Relasi: Pemakaian ini menggunakan bahan baku tertentu (bisa null)
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan', 'id_bahan');
    }

    // Relasi: Pemakaian ini menggunakan produk WIP tertentu (bisa null)
    public function produkWip()
    {
        return $this->belongsTo(Produk::class, 'id_produk_wip', 'id_produk');
    }

    // Relasi: Pemakaian ini dari stok bahan baku tertentu (FIFO tracking)
    public function stokBahanBaku()
    {
        return $this->belongsTo(StokBahanBaku::class, 'id_stok', 'id_stok');
    }

    // Relasi: Pemakaian ini dari stok produk WIP tertentu (FIFO tracking)
    public function stokProduk()
    {
        return $this->belongsTo(StokProduk::class, 'id_stok_produk', 'id_stok_produk');
    }

    // Relasi: Pemakaian dicatat oleh admin
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    // Helper: Auto calculate total biaya
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pemakaian) {
            if (!$pemakaian->total_biaya) {
                $pemakaian->total_biaya = $pemakaian->jumlah_pakai * $pemakaian->harga_satuan;
            }
        });

        static::updating(function ($pemakaian) {
            if (!$pemakaian->total_biaya) {
                $pemakaian->total_biaya = $pemakaian->jumlah_pakai * $pemakaian->harga_satuan;
            }
        });
    }

    // Scope: Filter by job order
    public function scopeByJob($query, $idJob)
    {
        return $query->where('id_permintaan_produksi', $idJob);
    }

    // Scope: Filter by bahan
    public function scopeByBahan($query, $idBahan)
    {
        return $query->where('id_bahan', $idBahan);
    }

    // Scope: Group by job order dengan summary
    public function scopeGroupedByJob($query)
    {
        return $query->selectRaw('id_permintaan_produksi, COUNT(*) as total_items, SUM(total_biaya) as total_biaya_job')
                     ->groupBy('id_permintaan_produksi');
    }

    // Helper: Get detail FIFO untuk pemakaian ini
    public function getFifoDetail()
    {
        if ($this->id_produk_wip) {
            return [
                'batch_id' => $this->id_stok_produk,
                'tanggal_batch' => $this->stokProduk ? $this->stokProduk->tanggal_masuk->format('d/m/Y') : '-',
                'jumlah_pakai' => $this->jumlah_pakai,
                'harga_satuan' => $this->harga_satuan,
                'total_biaya' => $this->total_biaya,
                'nama_bahan' => $this->produkWip->nama_produk ?? '-',
                'satuan' => $this->produkWip->satuan_produk ?? '-',
            ];
        }

        return [
            'batch_id' => $this->id_stok,
            'tanggal_batch' => $this->stokBahanBaku ? $this->stokBahanBaku->tanggal_masuk->format('d/m/Y') : '-',
            'jumlah_pakai' => $this->jumlah_pakai,
            'harga_satuan' => $this->harga_satuan,
            'total_biaya' => $this->total_biaya,
            'nama_bahan' => $this->bahanBaku->nama_bahan ?? '-',
            'satuan' => $this->bahanBaku->satuan ?? '-',
        ];
    }
}