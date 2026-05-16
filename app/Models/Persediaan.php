<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Persediaan extends Model
{
    use hasFactory;
    protected $table = 'persediaan';

    protected $fillable = [
        'id_faktur',
        'id_faktur_item',
        'id_kategori',
        'id_vendor',
        'nama_barang',
        'qty',
        'harga_satuan',
        'total',
        'tanggal_masuk',
        'kode_lokasi',
    ];

     public function faktur()
    {
        return $this->belongsTo(FakturPembelian::class, 'id_faktur');
    }

    public function fakturItem()
    {
        return $this->belongsTo(FakturPembelianItem::class, 'id_faktur_item');
    }

    public function kategoriAset()
    {
        return $this->belongsTo(KategoriAset::class, 'id_kategori');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }

    protected static function booted()
    {
        static::created(function ($persediaan) {

            $aset = AsetLancar::firstOrCreate(
                [
                    'id_kategori' => $persediaan->id_kategori,
                    'nama_barang' => $persediaan->nama_barang,
                ],
                [
                    'kode_barang' => 'AL-' . strtoupper(Str::random(6)),
                    'stok_tersedia' => 0,
                    'harga_satuan_rata' => 0,
                    'nilai_total' => 0,
                    'tanggal_update_terakhir' => now(),
                ]
            );

            // 🔥 LOGIC YANG BENAR
            $stokBaru = $aset->stok_tersedia + $persediaan->qty;
            $nilaiBaru = $aset->nilai_total + $persediaan->total;

            $hargaRataBaru = $stokBaru > 0
                ? $nilaiBaru / $stokBaru
                : 0;

            $aset->update([
                'stok_tersedia' => $stokBaru,
                'nilai_total' => $nilaiBaru,
                'harga_satuan_rata' => $hargaRataBaru,
                'tanggal_update_terakhir' => now(),
            ]);
        });
    }

}
