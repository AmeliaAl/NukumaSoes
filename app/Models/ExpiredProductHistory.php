<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpiredProductHistory extends Model
{
    protected $fillable = [
        'no_batch',
        'kode_produk',
        'nama_produk',
        'rasa_produk',
        'kategori',
        'jumlah_per_batch',
        'jumlah',
        'harga',
        'hpp',
        'total',
        'tgl_masuk',
        'tgl_expired',
        'status',
        'sisa_hari',
        'is_journaled',
    ];

    protected $casts = [
        'tgl_masuk' => 'date',
        'tgl_expired' => 'date',
    ];

    /**
     * Get the latest HPP from ProdukKeluarEntry history
     */
    public function getCurrentHpp()
    {
        $pkEntry = \App\Models\ProdukKeluarEntry::where('kategori', $this->kategori)
            ->where(function($q) {
                $q->where('nama_produk', 'like', '%' . $this->rasa_produk . '%')
                  ->orWhere('rasa_pouch', $this->rasa_produk)
                  ->orWhere('rasa_ecofam', $this->rasa_produk)
                  ->orWhere('rasa_family', $this->rasa_produk);
            })
            ->where('harga_pokok_per_pack', '>', 0)
            ->orderBy('tanggal', 'desc')
            ->first();
            
        return $pkEntry ? $pkEntry->harga_pokok_per_pack : ($this->harga > 0 ? $this->harga : 87.68);
    }

    /**
     * Calculate nominal dynamically based on latest HPP
     */
    public function getDynamicNominalAttribute()
    {
        return $this->jumlah * $this->getCurrentHpp();
    }
}
