<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'kode_produk',
        'no_batch',
        'nama_produk',
        'rasa_produk',
        'kategori',
        'jenis_produk',
        'maklon_id',
        'stok_awal',
        'jumlah',
        'jumlah_per_batch',
        'harga',
        'hpp',
        'margin',
        'harga_dasar_jual',
        'tgl_masuk',
        'tgl_expired',
        'status',
        'sisa_hari',
        'masa_simpan',
        'satuan_masa_simpan',
    ];

    protected $casts = [
        'tgl_masuk' => 'date',
        'tgl_expired' => 'date',
    ];

    /**
     * Synchronize sisa_hari and status for all inventory items based on current date.
     */
    public static function syncAllStatus()
    {
        $inventories = self::whereNotNull('tgl_expired')->get();
        $now = \Carbon\Carbon::now()->startOfDay();
        
        foreach ($inventories as $inv) {
            $expiredDate = \Carbon\Carbon::parse($inv->tgl_expired)->startOfDay();
            $sisa_hari = (int) $now->diffInDays($expiredDate, false);
            
            $status = 'Aman';
            if ($sisa_hari > 30) {
                $status = 'Aman';
            } elseif ($sisa_hari >= 1) {
                $status = 'Hampir Expired';
            } else {
                $status = 'Expired';
            }

            if ($inv->sisa_hari !== $sisa_hari || $inv->status !== $status) {
                $inv->sisa_hari = $sisa_hari;
                $inv->status = $status;
                $inv->save();
            }
        }
    }

    /**
     * Get the master product associated with the inventory.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'kode_produk', 'kode_produk');
    }
}
