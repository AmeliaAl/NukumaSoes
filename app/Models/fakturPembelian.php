<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class fakturPembelian extends Model
{
    use HasFactory;
    protected $table = 'faktur_pembelian';
    protected $primaryKey = 'id'; // ⬅ TAMBAHKAN INI

    public $incrementing = true;
    protected $fillable = [
        'id_vendor',
        'no_faktur',
        'tanggal_faktur',
        'total_tagihan',
        'status',
        'biaya_lain',
        'keterangan',
    ];  

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }

    public function kategoriAset()
    {
        return $this->belongsTo(KategoriAset::class, 'id_kategori');
    }

    public function items() 
    {
        return $this->hasMany(FakturPembelianItem::class, 'id_faktur');
    }


    protected $attributes = [
        'status' => 'belum_dibayar', // Default status
    ];

    // Event ketika create baru
    protected static function booted()
    {
        static::creating(function ($faktur) {
            $faktur->status = 'belum_dibayar';
        });
    }

    public function pembayaran()
    {
        return $this->hasMany(
            \App\Models\PembayaranAset::class,
            'id_faktur'
        );
    }

    public function updateStatus(): void
    {
        $totalTagihan = $this->total_tagihan;
        $totalTerbayar = $this->pembayaran()->sum('jumlah_bayar');

        if ($totalTerbayar <= 0) {
            $status = 'belum_dibayar';
        } elseif ($totalTerbayar < $totalTagihan) {
            $status = 'belum_lunas';
        } else {
            $status = 'lunas';
        }

        $this->updateQuietly([
            'status' => $status,
        ]);
    }


    // Accessor untuk sisa tagihan
    public function getSisaTagihanAttribute(): float
    {
        $totalDibayar = $this->pembayaran()->sum('jumlah_bayar');
        return max(0, $this->total_tagihan - $totalDibayar);
    }

    public function getTotalHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }

    public function scopeBelumDibayar($query)
    {
        return $query->where('status', 'belum_dibayar');
    }

    public function scopeBelumLunas($query)
    {
        return $query->where('status', 'belum_lunas');
    }

    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    // Method untuk hitung proporsional (prorata) biaya lain
    public function getAlokasiBiayaLain(): array
    {
        $items = $this->items()->orderBy('id')->get();
        $totalHargaSemua = $items->sum('total_harga');
        
        $alokasi = [];
        $biayaLain = (float) ($this->biaya_lain ?? 0);

        if ($totalHargaSemua <= 0 || $biayaLain <= 0 || $items->isEmpty()) {
            foreach ($items as $item) {
                $alokasi[$item->id] = 0;
            }
            return $alokasi;
        }

        $totalAlokasi = 0;
        foreach ($items as $index => $item) {
            if ($index === $items->count() - 1) {
                // Selisih pembulatan dilempar ke item terakhir biar jurnal balance
                $alokasi[$item->id] = round($biayaLain - $totalAlokasi, 2);
            } else {
                $bagian = round(($item->total_harga / $totalHargaSemua) * $biayaLain, 2);
                $alokasi[$item->id] = $bagian;
                $totalAlokasi += $bagian;
            }
        }

        return $alokasi;
    }
}
