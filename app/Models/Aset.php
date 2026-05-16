<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\kategoriAset;
use Carbon\Carbon;
use App\Models\Penyusutan;
use App\Models\Pemeliharaan;

class Aset extends Model
{
    /** @use HasFactory<\Database\Factories\AsetFactory> */
    use HasFactory;
    protected $table = 'aset';
    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'id_kategori',
        'tanggal_perolehan',
        'nilai_perolehan',
        'nilai_residu',
        'masa_manfaat',
        'metode_penyusutan',
        'reminder',
    ];
protected $casts = [
    'tanggal_perolehan' => 'date',
    'reminder' => 'date',
];

    //relasi model
    public function kategori_aset()
    {
        return $this->belongsTo(KategoriAset::class, 'id_kategori');
    }
    public function pemeliharaan()
    {
        return $this->hasMany(Pemeliharaan::class);
    }
    public function penyusutan()
    {
        return $this->hasMany(Penyusutan::class, 'aset_id');
    }


    public static function generateKdAset()
    {
        $lastAkun = self::query()
                       ->latest('id') 
                       ->first();

        $prefix = 'AS-'; 
        $nextNumber = 1;

        if ($lastAkun && $lastAkun->kode_aset) {
            $lastNumber = (int) substr($lastAkun->kode_aset, 3); 
            $nextNumber = $lastNumber + 1;
        }
        $paddedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        return $prefix . $paddedNumber;
    }

    public function getIntervalPemeliharaanBulan(): int
{
    return (int) ($this->kategori_aset?->interval_pemeliharaan ?? 0);
}

public function getTanggalDasarPemeliharaan(): ?Carbon
{
    $lastPemeliharaan = $this->pemeliharaan()
        ->where('jenis_perbaikan', 'rutin') // penting
        ->whereNotNull('tanggal')
        ->orderByDesc('tanggal')
        ->first();

    if ($lastPemeliharaan?->tanggal) {
        return Carbon::parse($lastPemeliharaan->tanggal)->startOfDay();
    }

    if (!empty($this->tanggal_perolehan)) {
        return Carbon::parse($this->tanggal_perolehan)->startOfDay();
    }

    return null;
}

public function getNextPemeliharaan(): ?Carbon
{
    $tanggalDasar = $this->getTanggalDasarPemeliharaan();
    $interval = $this->getIntervalPemeliharaanBulan();

    if (!$tanggalDasar || $interval <= 0) {
        return null;
    }

    return $tanggalDasar->copy()->addMonthsNoOverflow($interval);
}

public function isButuhPemeliharaan(int $hariSebelum = 7): bool
{
    $next = $this->getNextPemeliharaan();

    if (!$next) return false;

    return now()->startOfDay()->gte(
        $next->copy()->subDays($hariSebelum)->startOfDay()
    );
}

    public function getStatusPemeliharaan(): ?string
    {
        $next = $this->getNextPemeliharaan();

        if (! $next) {
            return null;
        }

        if (now()->gt($next)) {
            return 'overdue';
        }

        if (now()->diffInDays($next, false) <= 7) {
            return 'soon';
        }

        return 'aman';
    }

    //penyusutan
    public function bebanPenyusutanTahunan(): float
    {
        return ($this->nilai_perolehan - $this->nilai_residu)
            / ($this->masa_manfaat * 12);
    }

    public function nilaiBukuTerakhir(): float
    {
        return $this->penyusutan()
            ->latest()
            ->value('nilai_buku')
            ?? $this->nilai_perolehan;
    }

    public function akumulasiTerakhir(): float
    {
        return $this->penyusutan()
            ->latest()
            ->value('akumulasi_penyusutan')
            ?? 0;
    }

    protected static function booted()
{
    static::created(function ($aset) {
        //
    });
}



}
