<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class asetLancar extends Model
{
    use hasFactory;
    protected $table = 'aset_lancar';
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
        'id_kategori',
        'stok_tersedia',
        'harga_satuan_rata',
        'nilai_total',
        'tanggal_update_terakhir',
        'keterangan',
    ];

    public function kategoriAset()
    {
        return $this->belongsTo(KategoriAset::class, 'id_kategori');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }

    public function persediaan()
    {
        return $this->hasMany(Persediaan::class, 'id_aset_lancar');
    }

    public function pemakaian()
    {
        return $this->hasMany(PemakaianPersediaan::class, 'kode_barang');
    }

    public function tambahStok(int $qty, float $hargaSatuan): void
    {
        $stokLama = $this->stok_tersedia;
        $nilaiLama = $this->nilai_total;
        
        $stokBaru = $stokLama + $qty;
        $nilaiBaru = $nilaiLama + ($qty * $hargaSatuan);
        
        $hargaRataBaru = $stokBaru > 0 ? $nilaiBaru / $stokBaru : 0;
        
        $this->update([
            'stok_tersedia' => $stokBaru,
            'nilai_total' => $nilaiBaru,
            'harga_satuan_rata' => $hargaRataBaru,
            'tanggal_update_terakhir' => now(),
        ]);
    }

    public function kurangiStok(int $qty): void
    {
        $stokBaru = max(0, $this->stok_tersedia - $qty);
        $nilaiBaru = $stokBaru * $this->harga_satuan_rata;
        
        $this->update([
            'stok_tersedia' => $stokBaru,
            'nilai_total' => $nilaiBaru,
            'tanggal_update_terakhir' => now(),
        ]);
        
        \Log::info("Stok dikurangi: {$this->nama_barang}, qty: {$qty}, stok baru: {$stokBaru}");
    }

    public function isStokMinimum(): bool
    {
        return $this->stok_tersedia <= $this->stok_minimum;
    }

   }
