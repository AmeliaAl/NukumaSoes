<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukKeluarEntry extends Model
{
    use HasFactory;

    protected $table = 'produk_keluar_entries';

    protected $fillable = [
        'id_transaksi',
        'tanggal',
        'kode_produk',
        'inventory_id',
        'nama_produk',
        'kategori',

        'jumlah_keluar',
        'jumlah_pouch',
        'jumlah_ecofam',
        'jumlah_family',
        'rasa_pouch',
        'rasa_ecofam',
        'rasa_family',
        'harga',
        'harga_pouch',
        'harga_ecofam',
        'harga_family',
        'diskon',
        'diskon_persen',
        'total_harga',
        'status',
        'jenis',
        'jumlah_pack_keluar',
        'harga_pokok_per_pack',
        'harga_persediaan_produk_jadi',
        'jumlah_pack',
    ];
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
