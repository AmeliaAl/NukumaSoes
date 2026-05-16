<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\kategoriAset;
use App\Models\TransaksiAset;
use App\Models\Vendor;
use App\Models\FakturPembelian;
use App\Models\FakturPembelianItem;

class perolehanAset extends Model
{
    use HasFactory;

    protected $table = 'perolehan_aset';

    protected $fillable = [
        'id_transaksi',
        'id_kategori',
        'id_faktur',
        'id_faktur_item',
        'id_vendor',
        'tanggal_faktur',
        'qty',
        'harga_satuan',
        'biaya_lain',
        'total_perolehan',
        'masa_manfaat',
        'metode_penyusutan',
        'nama_aset',
        'tanggal_pakai',
        'tanggal_perolehan',
        'kode_lokasi',
        'nilai_perolehan',
        'lokasi_aset',
        'kondisi_aset',
        'kode_aset',
        'nilai_residu',
    ];

    public function kategoriAset(): BelongsTo
    {
        return $this->belongsTo(KategoriAset::class, 'id_kategori');
    }

    
    public function transaksi()
    {
        return $this->belongsTo(TransaksiAset::class, 'id_transaksi');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }
    public function fakturPembelian(): BelongsTo
    {
        return $this->belongsTo(FakturPembelian::class, 'id_faktur');
    }

    public function fakturItem(): BelongsTo
    {
        return $this->belongsTo(FakturPembelianItem::class, 'id_faktur_item');
    }

    public function lokasiAset(): BelongsTo
    {
        return $this->belongsTo(LokasiAset::class, 'kode_lokasi');
    }



}
