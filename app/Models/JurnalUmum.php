<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    use HasFactory;

    protected $table = 'jurnal_umum';
    protected $primaryKey = 'id_jurnal';

    protected $fillable = [
        'tanggal',
        'periode_mulai',
        'periode_selesai',
        'nomor_bukti',
        'nomor_pembayaran',
        'keterangan',
        'id_referensi',
        'tipe_referensi',
        'id_admin',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
    ];

    // Relasi: Jurnal punya banyak detail (debit/kredit entries)
    public function detail()
    {
        return $this->hasMany(JurnalUmumDetail::class, 'id_jurnal', 'id_jurnal');
    }

    // Relasi: Jurnal dicatat oleh admin
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public function alokasiBopAktual()
    {
        return $this->hasMany(BiayaOverheadPabrik::class, 'id_jurnal_aktual', 'id_jurnal')
            ->where('is_alokasi_aktual', true);
    }

    // Helper: Get total debit dari detail
    public function getTotalDebit()
    {
        return $this->detail()->sum('debit');
    }

    // Helper: Get total kredit dari detail
    public function getTotalKredit()
    {
        return $this->detail()->sum('kredit');
    }

    // Helper: Check balance (debit = kredit)
    public function isBalanced()
    {
        return abs($this->getTotalDebit() - $this->getTotalKredit()) < 0.01;
    }

    // Helper: Generate nomor bukti otomatis
    public static function generateNomorBukti($prefix = 'JU')
    {
        $today = now()->format('Ymd');
        $lastJurnal = self::where('nomor_bukti', 'like', "{$prefix}-{$today}-%")
                         ->orderBy('nomor_bukti', 'desc')
                         ->first();

        if ($lastJurnal) {
            $lastNumber = (int) substr($lastJurnal->nomor_bukti, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "{$prefix}-{$today}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Scope: Filter by tanggal
    public function scopeByTanggal($query, $tanggalMulai, $tanggalAkhir = null)
    {
        if ($tanggalAkhir) {
            return $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
        }
        return $query->whereDate('tanggal', $tanggalMulai);
    }
}
