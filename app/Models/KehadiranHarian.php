<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KehadiranHarian extends Model
{
    use HasFactory;

    protected $table = 'kehadiran_harian';
    protected $primaryKey = 'id_kehadiran';

    protected $fillable = [
        'tanggal',
        'id_tenaga',
        'status_kehadiran',
        'jam_kerja',
        'jam_lembur',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_kerja' => 'decimal:2',
        'jam_lembur' => 'decimal:2',
    ];

    // Relasi: Kehadiran milik tenaga kerja tertentu
    public function tenagaKerja()
    {
        return $this->belongsTo(TenagaKerja::class, 'id_tenaga', 'id_tenaga');
    }

    /**
     * Helper: Hitung upah harian pekerja berdasarkan jam kerja & lembur
     */
    public function hitungUpahHarian(): float
    {
        if ($this->status_kehadiran !== 'hadir') {
            return 0.0;
        }

        $upahJam = floatval($this->tenagaKerja->upah_per_jam ?? 6000);
        $upahKerja = floatval($this->jam_kerja) * $upahJam;
        // Lembur dihitung 1.5x upah per jam
        $upahLembur = floatval($this->jam_lembur) * $upahJam * 1.5;

        return $upahKerja + $upahLembur;
    }
}
