<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaTenagaKerja extends Model
{
    use HasFactory;

    protected $table = 'biaya_tenaga_kerja';
    protected $primaryKey = 'id_biaya_tk';

    protected $fillable = [
        'id_permintaan_produksi',
        'id_tenaga',
        'id_admin',
        'tanggal_kerja',
        'hari_kerja',
        'jam_absen',
        'jumlah_batch',
        'upah_per_minggu',
        'nominal_potongan',
        'nominal_lembur',
        'total_biaya',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kerja'    => 'date',
        'hari_kerja'       => 'decimal:2',
        'jam_absen'        => 'decimal:2',
        'jumlah_batch'     => 'integer',
        'upah_per_minggu'  => 'decimal:2',
        'nominal_potongan' => 'decimal:2',
        'nominal_lembur'   => 'decimal:2',
        'total_biaya'      => 'decimal:2',
    ];

    // ACCESSOR: Agar view bisa pakai id_biaya_tenaga_kerja
    protected $appends = ['id_biaya_tenaga_kerja'];

    public function getIdBiayaTenagaKerjaAttribute()
    {
        return $this->id_biaya_tk;
    }

    /**
     * Hitung total biaya BTK:
     * total = ((hari_kerja / 6) × upah_per_minggu) − nominal_potongan + nominal_lembur
     * Asumsi standar 1 minggu = 6 hari kerja
     */
    public static function hitungTotalBiaya($hariKerja, $upahPerMinggu, $nominalPotongan = 0, $nominalLembur = 0): float
    {
        $porsiMinggu = $hariKerja / 6;
        $upahPenuh = $porsiMinggu * $upahPerMinggu;
        return max(0, $upahPenuh - $nominalPotongan + $nominalLembur);
    }

    // Relasi: Biaya ini untuk job order tertentu
    public function permintaanProduksi()
    {
        return $this->belongsTo(PermintaanProduksi::class, 'id_permintaan_produksi', 'id_permintaan_produksi');
    }

    // Relasi: Biaya ini untuk tenaga kerja tertentu
    public function tenagaKerja()
    {
        return $this->belongsTo(TenagaKerja::class, 'id_tenaga', 'id_tenaga');
    }

    // Relasi: Biaya dicatat oleh admin
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    // Backward Compatibility Accessors & Mutators
    public function getJamKerjaAttribute()
    {
        // Return jam_absen if set, otherwise convert hari_kerja to hours (1 day = 8 hours)
        return floatval($this->jam_absen ?? ($this->hari_kerja * 8));
    }

    public function getUpahPerJamAttribute()
    {
        // Fallback to tenagaKerja upah_per_jam or upah_per_minggu if stored
        return floatval($this->tenagaKerja->upah_per_jam ?? ($this->upah_per_minggu ?? 6000));
    }

    public function getMingguKerjaAttribute()
    {
        // Return hari_kerja divided by 6 (assuming 6 working days per week)
        return floatval($this->hari_kerja ?? 0) / 6.0;
    }

    // Helper: Auto calculate total biaya saat create/update (hanya jika total_biaya kosong/tidak di-set)
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($biaya) {
            if (empty($biaya->total_biaya) || $biaya->total_biaya == 0) {
                $biaya->total_biaya = self::hitungTotalBiaya(
                    $biaya->hari_kerja,
                    $biaya->upah_per_minggu ?? 0,
                    $biaya->nominal_potongan ?? 0,
                    $biaya->nominal_lembur ?? 0
                );
            }
        });

        static::updating(function ($biaya) {
            if (!$biaya->isDirty('total_biaya')) {
                $biaya->total_biaya = self::hitungTotalBiaya(
                    $biaya->hari_kerja,
                    $biaya->upah_per_minggu ?? 0,
                    $biaya->nominal_potongan ?? 0,
                    $biaya->nominal_lembur ?? 0
                );
            }
        });
    }

    // Scope: Filter by job order
    public function scopeByJob($query, $idJob)
    {
        return $query->where('id_permintaan_produksi', $idJob);
    }

    // Scope: Filter by tenaga kerja
    public function scopeByTenaga($query, $idTenaga)
    {
        return $query->where('id_tenaga', $idTenaga);
    }

    // Scope: Filter by tanggal
    public function scopeByTanggal($query, $tanggalMulai, $tanggalAkhir = null)
    {
        if ($tanggalAkhir) {
            return $query->whereBetween('tanggal_kerja', [$tanggalMulai, $tanggalAkhir]);
        }
        return $query->whereDate('tanggal_kerja', $tanggalMulai);
    }
}