<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class fakturPembelianItem extends Model
{
    use HasFactory;
    protected $table = 'faktur_pembelian_item';
    protected $fillable = [
        'id_faktur',
        'nama_aset',
        'id_kategori',
        'qty',
        'keterangan',
        'harga_satuan',
        'total_harga',
        //'masa_manfaat',
        //'nilai_residu',
    ];

     protected $casts = [
        'tanggal_faktur' => 'date',
        'total_tagihan' => 'decimal:2',
        'biaya_lain' => 'decimal:2',
    ];


        public function faktur()
    {
        return $this->belongsTo(FakturPembelian::class, 'id_faktur');
    }

    public function kategoriAset()
    {
        return $this->belongsTo(KategoriAset::class, 'id_kategori');
    }

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset');
    }

    public function perolehanAset()
    {
        return $this->hasOne(\App\Models\PerolehanAset::class, 'id_faktur_item');
    }

    // Method untuk update status otomatis
    public function updateStatus(): void
    {
        $totalDibayar = $this->pembayaran()->sum('jumlah_bayar');
        
        if ($totalDibayar == 0) {
            $this->status = 'belum_dibayar';
        } elseif ($totalDibayar < $this->total_tagihan) {
            $this->status = 'belum_lunas';
        } else {
            $this->status = 'lunas';
        }
        
        $this->saveQuietly();
    }
}
