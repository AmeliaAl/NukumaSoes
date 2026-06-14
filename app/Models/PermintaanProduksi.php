<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanProduksi extends Model
{
    use HasFactory;

    protected $table = 'permintaan_produksi';
    protected $primaryKey = 'id_permintaan_produksi';

    protected $fillable = [
        'nomor_job',
        'kode_order_eksternal',
        'nama_pemesan',
        'tanggal_terima_order',
        'id_produk',
        'id_admin',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_produksi',
        'jumlah_batch',
        'customer',
        'jenis_produksi',
        'tujuan_produksi',
        'tahap_produksi',
        'nama_customer_maklun',
        'status',
        'total_biaya_bahan',
        'total_biaya_btk_langsung',
        'total_biaya_btk_tidak_langsung',
        'total_biaya_tenaga_kerja',
        'total_biaya_overhead',
        'total_biaya_produksi',
        'harga_pokok_per_unit',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai'                  => 'date',
        'tanggal_selesai'                => 'date',
        'jumlah_produksi'                => 'decimal:2',
        'jumlah_batch'                   => 'integer',
        'total_biaya_bahan'              => 'decimal:2',
        'total_biaya_btk_langsung'       => 'decimal:2',
        'total_biaya_btk_tidak_langsung' => 'decimal:2',
        'total_biaya_tenaga_kerja'       => 'decimal:2',
        'total_biaya_overhead'           => 'decimal:2',
        'total_biaya_produksi'           => 'decimal:2',
        'harga_pokok_per_unit'           => 'decimal:2',
    ];

    // ==========================================
    // RELASI
    // ==========================================
    
    // Relasi: Job Order ini untuk produk tertentu
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    // Relasi: Job Order dibuat oleh admin
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    // Relasi: Job Order punya banyak pemakaian bahan baku
    public function pemakaianBahanBaku()
    {
        return $this->hasMany(PemakaianBahanBaku::class, 'id_permintaan_produksi', 'id_permintaan_produksi');
    }

    // Relasi: Job Order punya pemakaian bahan baku langsung (termasuk WIP)
    public function pemakaianBahanBakuLangsung()
    {
        return $this->hasMany(PemakaianBahanBaku::class, 'id_permintaan_produksi', 'id_permintaan_produksi')
                    ->where(function($query) {
                        $query->whereNotNull('id_produk_wip')
                              ->orWhereHas('bahanBaku', function($q) {
                                  $q->where('jenis_bahan', 'langsung');
                              });
                    });
    }

    // Relasi: Job Order punya pemakaian bahan baku tidak langsung (Kemasan/BOP)
    public function pemakaianBahanBakuTidakLangsung()
    {
        return $this->hasMany(PemakaianBahanBaku::class, 'id_permintaan_produksi', 'id_permintaan_produksi')
                    ->whereHas('bahanBaku', function($q) {
                        $q->where('jenis_bahan', 'tidak_langsung');
                    });
    }


    // Relasi: Job Order punya banyak biaya tenaga kerja
    public function biayaTenagaKerja()
    {
        return $this->hasMany(BiayaTenagaKerja::class, 'id_permintaan_produksi', 'id_permintaan_produksi');
    }

    // Relasi: Job Order punya BTK Langsung saja
    public function biayaTenagaKerjaLangsung()
    {
        return $this->hasMany(BiayaTenagaKerja::class, 'id_permintaan_produksi', 'id_permintaan_produksi')
                    ->whereHas('tenagaKerja', fn($q) => $q->where('jenis_tenaga', 'langsung'));
    }

    // Relasi: Job Order punya BTK Tidak Langsung saja
    public function biayaTenagaKerjaTidakLangsung()
    {
        return $this->hasMany(BiayaTenagaKerja::class, 'id_permintaan_produksi', 'id_permintaan_produksi')
                    ->whereHas('tenagaKerja', fn($q) => $q->where('jenis_tenaga', 'tidak_langsung'));
    }

    // Relasi: Job Order punya banyak biaya overhead
    public function biayaOverheadPabrik()
    {
        return $this->hasMany(BiayaOverheadPabrik::class, 'id_permintaan_produksi', 'id_permintaan_produksi');
    }

    // ==========================================
    // HELPER METHODS - PERHITUNGAN BIAYA
    // ==========================================
    
    /**
     * Hitung total biaya bahan baku dari tabel pemakaian_bahan_baku
     * Menggunakan kolom 'total_biaya'
     * @return float
     */
    public function hitungTotalBiayaBahan()
    {
        return $this->pemakaianBahanBaku()->sum('total_biaya');
    }

    /**
     * Hitung total BTK Langsung
     */
    public function hitungTotalBiayaBtkLangsung()
    {
        return $this->biayaTenagaKerjaLangsung()->sum('total_biaya');
    }

    /**
     * Hitung total BTK Tidak Langsung
     */
    public function hitungTotalBiayaBtkTidakLangsung()
    {
        return $this->biayaTenagaKerjaTidakLangsung()->sum('total_biaya');
    }

    /**
     * Hitung total biaya tenaga kerja dari tabel biaya_tenaga_kerja (L + TL)
     */
    public function hitungTotalBiayaTenagaKerja()
    {
        return $this->biayaTenagaKerja()->sum('total_biaya');
    }

    /**
     * Hitung total biaya overhead dari tabel biaya_overhead_pabrik
     * FIXED: Menggunakan kolom 'nominal' sesuai struktur tabel
     * 
     * @return float
     */
    public function hitungTotalBiayaOverhead()
    {
        // MENGGUNAKAN KOLOM 'nominal' untuk Biaya Overhead Pabrik
        return $this->biayaOverheadPabrik()->sum('nominal');
    }

    /**
     * MAIN METHOD: Hitung ulang SEMUA komponen biaya dan update database
     * 
     * Method ini adalah INTI dari perhitungan Job Order Costing.
     * Dipanggil setiap kali ada perubahan pada:
     * - Pemakaian Bahan Baku (tambah/hapus/edit)
     * - Biaya Tenaga Kerja (tambah/hapus/edit)
     * - Biaya Overhead Pabrik (tambah/hapus/edit)
     * 
     * CRITICAL FIX: Method ini memastikan semua kolom biaya di tabel
     * permintaan_produksi selalu ter-update dengan benar.
     * 
     * @return array Breakdown biaya untuk debugging/logging
     */
    public function hitungTotalBiayaProduksi()
    {
        \Log::info("=== START: Hitung Total Biaya Produksi (Academic Revision) ===", [
            'job_order' => $this->nomor_job,
            'id_permintaan_produksi' => $this->id_permintaan_produksi,
        ]);

        // 1. Hitung masing-masing komponen biaya
        // BBB Langsung: pemakaian bahan baku (jenis_bahan = langsung) ATAU pemakaian WIP (id_produk_wip is not null)
        $totalBiayaBahanLangsung = $this->pemakaianBahanBaku()
            ->where(function($query) {
                $query->whereNotNull('id_produk_wip')
                      ->orWhereHas('bahanBaku', function($q) {
                          $q->where('jenis_bahan', 'langsung');
                      });
            })
            ->sum('total_biaya');

        // Bahan Tidak Langsung / Kemasan: jenis_bahan = tidak_langsung
        $totalBiayaBahanTidakLangsung = $this->pemakaianBahanBaku()
            ->whereHas('bahanBaku', function($q) {
                $q->where('jenis_bahan', 'tidak_langsung');
            })
            ->sum('total_biaya');

        // BTK Langsung (BTKL)
        $totalBtkLangsung = $this->hitungTotalBiayaBtkLangsung();

        // BTK Tidak Langsung (BTKTL)
        $totalBtkTidakLangsung = $this->hitungTotalBiayaBtkTidakLangsung();

        // BOP Umum (Listrik, Air, dll.) dari tabel biaya_overhead_pabrik
        $totalBopUmum = $this->hitungTotalBiayaOverhead();

        // BOP Total = BOP Umum + BTKTL + Bahan Tidak Langsung
        $totalBiayaOverheadTotal = $totalBopUmum + $totalBtkTidakLangsung + $totalBiayaBahanTidakLangsung;

        // Total Biaya Produksi (HPP) = BBB Langsung + BTKL + BOP Total
        $totalBiayaProduksi = $totalBiayaBahanLangsung + $totalBtkLangsung + $totalBiayaOverheadTotal;

        // Harga pokok per unit
        $hargaPokokPerUnit = 0;
        if ($this->jumlah_produksi > 0) {
            $hargaPokokPerUnit = $totalBiayaProduksi / $this->jumlah_produksi;
        }

        \Log::info("HPP Breakdown (Academic Revision):", [
            'bbb_langsung'         => $totalBiayaBahanLangsung,
            'btk_langsung'         => $totalBtkLangsung,
            'btk_tidak_langsung'   => $totalBtkTidakLangsung,
            'bahan_tidak_langsung' => $totalBiayaBahanTidakLangsung,
            'bop_umum'             => $totalBopUmum,
            'bop_total'            => $totalBiayaOverheadTotal,
            'total_hpp'            => $totalBiayaProduksi,
            'hpp_per_unit'         => $hargaPokokPerUnit,
        ]);

        // Update database columns
        $this->update([
            'total_biaya_bahan'              => $totalBiayaBahanLangsung,
            'total_biaya_btk_langsung'       => $totalBtkLangsung,
            'total_biaya_btk_tidak_langsung' => $totalBtkTidakLangsung,
            'total_biaya_tenaga_kerja'       => $totalBtkLangsung, // Hanya mencakup BTKL sesuai rencana
            'total_biaya_overhead'           => $totalBiayaOverheadTotal, // Menyimpan BOP Total
            'total_biaya_produksi'           => $totalBiayaProduksi,
            'harga_pokok_per_unit'           => $hargaPokokPerUnit,
        ]);

        \Log::info("=== END: Hitung Total Biaya Produksi (Academic Revision) ===");

        return [
            'bahan_baku'           => $totalBiayaBahanLangsung,
            'btk_langsung'         => $totalBtkLangsung,
            'btk_tidak_langsung'   => $totalBtkTidakLangsung,
            'tenaga_kerja'         => $totalBtkLangsung,
            'overhead'             => $totalBiayaOverheadTotal,
            'total'                => $totalBiayaProduksi,
            'per_unit'             => $hargaPokokPerUnit,
        ];
    }

    /**
     * Get summary biaya dalam format yang sudah diformat
     * Berguna untuk tampilan di view
     * 
     * @return array
     */
    public function getSummaryBiayaProduksi()
    {
        return [
            'bahan_baku' => [
                'total'     => $this->total_biaya_bahan ?? 0,
                'formatted' => 'Rp ' . number_format($this->total_biaya_bahan ?? 0, 0, ',', '.'),
            ],
            'btk_langsung' => [
                'total'     => $this->total_biaya_btk_langsung ?? 0,
                'formatted' => 'Rp ' . number_format($this->total_biaya_btk_langsung ?? 0, 0, ',', '.'),
            ],
            'btk_tidak_langsung' => [
                'total'     => $this->total_biaya_btk_tidak_langsung ?? 0,
                'formatted' => 'Rp ' . number_format($this->total_biaya_btk_tidak_langsung ?? 0, 0, ',', '.'),
            ],
            'tenaga_kerja' => [
                'total'     => $this->total_biaya_tenaga_kerja ?? 0,
                'formatted' => 'Rp ' . number_format($this->total_biaya_tenaga_kerja ?? 0, 0, ',', '.'),
            ],
            'overhead' => [
                'total'     => $this->total_biaya_overhead ?? 0,
                'formatted' => 'Rp ' . number_format($this->total_biaya_overhead ?? 0, 0, ',', '.'),
            ],
            'total_produksi' => [
                'total'     => $this->total_biaya_produksi ?? 0,
                'formatted' => 'Rp ' . number_format($this->total_biaya_produksi ?? 0, 0, ',', '.'),
            ],
            'per_unit' => [
                'total'     => $this->harga_pokok_per_unit ?? 0,
                'formatted' => 'Rp ' . number_format($this->harga_pokok_per_unit ?? 0, 0, ',', '.'),
            ],
        ];
    }

    // Helper: Label jenis produksi
    public function getJenisProduksiLabelAttribute(): string
    {
        return match($this->jenis_produksi) {
            'maklun'       => 'Maklun (Merk Lain)',
            'brand_sendiri'=> 'Brand Sendiri',
            default        => '-',
        };
    }

    // Helper: Label tujuan produksi
    public function getTujuanProduksiLabelAttribute(): string
    {
        return match($this->tujuan_produksi) {
            'pesanan'          => 'Pesanan Customer',
            'stok_wip'         => 'Stok WIP (Kulit)',
            'stok_barang_jadi' => 'Stok Barang Jadi',
            default            => '-',
        };
    }

    // Helper: Label tahap produksi
    public function getTahapProduksiLabelAttribute(): string
    {
        return match($this->tahap_produksi) {
            'persiapan' => 'Persiapan',
            'produksi'  => 'Produksi',
            'filling'   => 'Filling',
            'selesai'   => 'Selesai',
            default     => '-',
        };
    }

    // Otomatis buat Stok Produk saat job order selesai
    public function buatStokProduk(): void
    {
        if ($this->status !== 'selesai') return;

        $tipe = match($this->tujuan_produksi) {
            'stok_wip'         => 'wip_kulit',
            'stok_barang_jadi' => 'barang_jadi',
            default            => null, // Pesanan tidak masuk stok
        };

        if ($tipe === null) return;

        // Hindari duplikasi
        $sudahAda = StokProduk::where('id_permintaan_produksi', $this->id_permintaan_produksi)->exists();
        if ($sudahAda) return;

        StokProduk::create([
            'id_permintaan_produksi' => $this->id_permintaan_produksi,
            'id_produk'              => $this->id_produk,
            'tipe_stok'              => $tipe,
            'jumlah'                 => $this->jumlah_produksi,
            'sisa_stok'              => $this->jumlah_produksi,
            'satuan'                 => $this->produk->satuan_produk ?? 'pcs',
            'harga_pokok_per_unit'   => $this->harga_pokok_per_unit ?? 0,
            'total_nilai'            => ($this->jumlah_produksi * ($this->harga_pokok_per_unit ?? 0)),
            'status'                 => 'tersedia',
            'tanggal_masuk'          => $this->tanggal_selesai ?? now()->toDateString(),
            'keterangan'             => 'Otomatis dari Job Order ' . $this->nomor_job,
        ]);
    }

    // ==========================================
    // STATUS CHECKS
    // ==========================================
    
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProses()
    {
        return $this->status === 'proses';
    }

    public function isSelesai()
    {
        return $this->status === 'selesai';
    }

    // ==========================================
    // QUERY SCOPES
    // ==========================================
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProses($query)
    {
        return $query->where('status', 'proses');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    // ==========================================
    // ACCESSORS (Backup jika field kosong)
    // ==========================================
    
    /**
     * Get total biaya (jika field kosong, hitung dari komponen)
     * Ini adalah fallback accessor
     */
    public function getTotalBiayaAttribute()
    {
        $total = ($this->attributes['total_biaya_produksi'] ?? 0);
        
        // Jika total masih 0, coba hitung dari komponen
        if ($total == 0) {
            $total = ($this->attributes['total_biaya_bahan'] ?? 0) + 
                     ($this->attributes['total_biaya_tenaga_kerja'] ?? 0) + 
                     ($this->attributes['total_biaya_overhead'] ?? 0);
        }
        
        return $total;
    }

    /**
     * Get biaya per unit (jika field kosong, hitung ulang)
     * Ini adalah fallback accessor
     */
    public function getBiayaPerUnitAttribute()
    {
        $biayaPerUnit = ($this->attributes['harga_pokok_per_unit'] ?? 0);
        
        // Jika masih 0 dan ada jumlah produksi, hitung ulang
        if ($biayaPerUnit == 0 && $this->jumlah_produksi > 0) {
            $totalBiaya = $this->total_biaya;
            if ($totalBiaya > 0) {
                $biayaPerUnit = $totalBiaya / $this->jumlah_produksi;
            }
        }
        
        return $biayaPerUnit;
    }
}