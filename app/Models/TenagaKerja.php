<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenagaKerja extends Model
{
    use HasFactory;

    protected $table = 'tenaga_kerja';
    protected $primaryKey = 'id_tenaga';

    protected $fillable = [
        'kode_tenaga',
        'nama_tenaga',
        'jabatan',
        'bagian',
        'jenis_tenaga',
        'upah_per_jam',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'upah_per_jam' => 'decimal:2',
    ];

    // Relasi: Tenaga kerja punya banyak biaya tenaga kerja (di berbagai job)
    public function biayaTenagaKerja()
    {
        return $this->hasMany(BiayaTenagaKerja::class, 'id_tenaga', 'id_tenaga');
    }

    // Relasi: Tenaga kerja punya banyak kehadiran harian
    public function kehadiranHarian()
    {
        return $this->hasMany(KehadiranHarian::class, 'id_tenaga', 'id_tenaga');
    }

    // Helper: Hitung total hari kerja bulan ini
    public function getTotalHariKerjaBulanIni()
    {
        return $this->kehadiranHarian()
                    ->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year)
                    ->where('status_kehadiran', 'hadir')
                    ->count();
    }

    // Helper: Hitung total biaya bulan ini
    public function getTotalBiayaBulanIni()
    {
        return $this->biayaTenagaKerja()
                    ->whereMonth('tanggal_kerja', now()->month)
                    ->whereYear('tanggal_kerja', now()->year)
                    ->sum('total_biaya');
    }

    // Helper: Get badge jenis tenaga kerja
    public function getJenisBadgeAttribute()
    {
        if ($this->jenis_tenaga == 'langsung') {
            return '<span class="badge bg-primary">Langsung</span>';
        }
        return '<span class="badge bg-secondary">Tidak Langsung</span>';
    }

    // Helper: Get label jenis tenaga kerja
    public function getJenisLabelAttribute()
    {
        return $this->jenis_tenaga == 'langsung' ? 'Tenaga Kerja Langsung' : 'Tenaga Kerja Tidak Langsung';
    }

    // Scope: Filter by jenis
    public function scopeJenisLangsung($query)
    {
        return $query->where('jenis_tenaga', 'langsung');
    }

    public function scopeJenisTidakLangsung($query)
    {
        return $query->where('jenis_tenaga', 'tidak_langsung');
    }

    // Scope: Filter by bagian
    public function scopeByBagian($query, $bagian)
    {
        return $query->where('bagian', $bagian);
    }
}