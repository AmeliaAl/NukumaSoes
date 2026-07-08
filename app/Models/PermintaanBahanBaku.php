<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBahanBaku extends Model
{
    use HasFactory;

    protected $table = 'permintaan_bahan_baku';
    protected $primaryKey = 'id_permintaan_bahan';

    protected $fillable = [
        'nomor_permintaan',
        'id_admin',
        'tanggal_permintaan',
        'keperluan',
        'status_permintaan',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_permintaan' => 'date',
    ];

    // ========== RELASI ==========
    
    public function details()
    {
        return $this->hasMany(PermintaanBahanBakuDetail::class, 'id_permintaan_bahan');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public function penerimaanBahanBaku()
    {
        return $this->hasMany(PenerimaanBahanBaku::class, 'id_permintaan_bahan', 'id_permintaan_bahan');
    }

    // ========== HELPER METHOD ==========

    /**
     * Update status berdasarkan seluruh detailnya
     */
    /**
     * Helper to get overall status_penerimaan dynamically
     */
    public function getStatusPenerimaanAttribute()
    {
        if (!$this->relationLoaded('details')) {
            $this->load('details');
        }

        $allDetails = $this->details;
        if ($allDetails->isEmpty()) {
            return 'belum';
        }

        $totalItems = $allDetails->count();
        $completedItems = 0;
        $partialItems = 0;

        foreach ($allDetails as $detail) {
            if ($detail->status_penerimaan === 'completed') {
                $completedItems++;
            } elseif ($detail->status_penerimaan === 'partial' || $detail->jumlah_diterima > 0) {
                $partialItems++;
            }
        }

        if ($completedItems === $totalItems) {
            return 'completed';
        } elseif ($completedItems > 0 || $partialItems > 0) {
            return 'partial';
        }

        return 'belum';
    }

    public function bisaDiterimaLagi()
    {
        return $this->status_permintaan === 'aktif' && $this->getStatusPenerimaanAttribute() !== 'completed';
    }

    // ========== SCOPE ==========

    public function scopeAktif($query)
    {
        return $query->where('status_permintaan', 'aktif');
    }

    // SCOPE BARU untuk status penerimaan (menggunakan relasi details)
    public function scopeBelumDiterima($query)
    {
        return $query->whereDoesntHave('details', function ($q) {
            $q->where('jumlah_diterima', '>', 0);
        });
    }

    public function scopeCompleted($query)
    {
        return $query->whereDoesntHave('details', function ($q) {
            $q->where('status_penerimaan', '!=', 'completed');
        });
    }
}