<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\JurnalUmum;

class BiayaOverheadPabrik extends Model
{
    use HasFactory;

    protected $table = 'biaya_overhead_pabrik';
    protected $primaryKey = 'id_overhead';

    protected $fillable = [
        'id_permintaan_produksi',
        'id_admin',
        'id_kategori_bop',
        'tanggal_overhead',
        'jenis_overhead',
        'satuan_periode',
        'jumlah_batch',
        'keterangan',
        'nominal',
        'total_nominal_global',
        'jumlah_batch_terlibat',
        'id_jurnal_aktual',
        'is_alokasi_aktual',
    ];

    protected $casts = [
        'tanggal_overhead'    => 'date',
        'jumlah_batch'        => 'integer',
        'nominal'             => 'decimal:2',
        'total_nominal_global'=> 'decimal:2',
        'jumlah_batch_terlibat' => 'integer',
        'is_alokasi_aktual' => 'boolean',
    ];

    // Accessor aliases untuk kompatibilitas view lama
    protected $appends = ['total_biaya', 'id_biaya_overhead', 'jenis_biaya', 'jumlah_periode', 'satuan_periode_label'];

    public function getTotalBiayaAttribute()
    {
        return $this->nominal;
    }

    public function getIdBiayaOverheadAttribute()
    {
        return $this->id_overhead;
    }

    public function getJenisBiayaAttribute()
    {
        return $this->jenis_overhead;
    }

    public function getJumlahPeriodeAttribute()
    {
        return $this->jumlah_batch;
    }

    public function getSatuanPeriodeLabelAttribute()
    {
        return match($this->satuan_periode) {
            'per_batch'  => 'batch',
            'per_hari'   => 'hari',
            'per_minggu' => 'minggu',
            'per_bulan'  => 'bulan',
            default      => 'batch',
        };
    }

    // ─── Helper: Hitung porsi nominal untuk job ini ──────────────────────────────

    /**
     * Hitung porsi BOP untuk 1 job order berdasarkan batch.
     *
     * Rumus: nominal = (total_nominal_global / jumlah_batch_terlibat) × jumlah_batch_job
     *
     * @param  float  $totalGlobal         Total tagihan sebelum dibagi
     * @param  int    $jumlahBatchTerlibat Total batch dari semua job yang berbagi BOP ini
     * @param  int    $jumlahBatchJob      Jumlah batch milik job order yang sedang diinput
     * @return float
     */
    public static function hitungPorsiNominal(float $totalGlobal, int $jumlahBatchTerlibat, int $jumlahBatchJob): float
    {
        if ($jumlahBatchTerlibat <= 0) return $totalGlobal;
        return round(($totalGlobal / $jumlahBatchTerlibat) * $jumlahBatchJob, 2);
    }

    /**
     * Apakah BOP ini menggunakan mode shared (total dibagi batch)?
     */
    public function isShared(): bool
    {
        return !is_null($this->total_nominal_global) && !is_null($this->jumlah_batch_terlibat);
    }

    /**
     * Hitung total batch dari semua job order yang sedang aktif (proses/pending).
     * Dipakai sebagai default saran jumlah_batch_terlibat di form.
     */
    public static function totalBatchAktif(): int
    {
        $batchAktif = BatchProduksi::whereIn('status', ['proses', 'selesai'])->count();
        if ($batchAktif > 0) return $batchAktif;

        return (int) PermintaanProduksi::whereIn('status', ['pending', 'proses'])->sum('jumlah_batch');
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────────

    public function permintaanProduksi()
    {
        return $this->belongsTo(PermintaanProduksi::class, 'id_permintaan_produksi', 'id_permintaan_produksi');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public function kategoriBop()
    {
        return $this->belongsTo(KategoriBop::class, 'id_kategori_bop', 'id_kategori_bop');
    }

    /**
     * Relasi ke jurnal aktual BOP (pengeluaran sesungguhnya).
     */
    public function jurnalAktual()
    {
        return $this->belongsTo(JurnalUmum::class, 'id_jurnal_aktual', 'id_jurnal');
    }

    /**
     * Apakah BOP ini sudah diaktualkan (sudah ada pembayaran aktual)?
     */
    public function isSudahDiaktualkan(): bool
    {
        return !is_null($this->id_jurnal_aktual);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────────

    public function scopeByJob($query, $idJob)
    {
        return $query->where('id_permintaan_produksi', $idJob);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_overhead', $jenis);
    }

    public function scopeBelumDiaktualkan($query)
    {
        return $query->whereNull('id_jurnal_aktual');
    }

    public function scopeSudahDiaktualkan($query)
    {
        return $query->whereNotNull('id_jurnal_aktual');
    }

    public function scopeByTanggal($query, $tanggalMulai, $tanggalAkhir = null)
    {
        if ($tanggalAkhir) {
            return $query->whereBetween('tanggal_overhead', [$tanggalMulai, $tanggalAkhir]);
        }
        return $query->whereDate('tanggal_overhead', $tanggalMulai);
    }

    // ─── Static Helpers ──────────────────────────────────────────────────────────

    public static function getJenisOverheadList()
    {
        return [
            'Listrik'             => 'Listrik',
            'Air'                 => 'Air',
            'Gas'                 => 'Gas',
            'Asuransi Pabrik'     => 'Asuransi Pabrik',
            'Bahan Penolong'      => 'Bahan Penolong',
            'Lainnya'             => 'Lainnya',
        ];
    }

    /**
     * Jenis overhead yang biasanya dibagi antar batch (shared).
     * Untuk jenis ini, form akan menyarankan mode pembagian.
     */
    public static function getJenisShared(): array
    {
        return ['Listrik', 'Air', 'Gas', 'Asuransi Pabrik'];
    }

    public static function getDefaultSatuanPeriode($jenis)
    {
        return match($jenis) {
            'Listrik'             => 'per_bulan',
            'Air'                 => 'per_bulan',
            'Gas'                 => 'per_hari',
            'Asuransi Pabrik'     => 'per_bulan',
            'Bahan Penolong'      => 'per_batch',
            default               => 'per_batch',
        };
    }
}
