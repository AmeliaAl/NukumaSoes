<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Carbon\Carbon;
use App\Models\Aset;
use App\Models\Penyusutan;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use Filament\Notifications\Notification;
use App\Models\Akun;
use Illuminate\Support\Facades\Auth;

class GeneratePenyusutan extends Page
{
    protected static ?string $navigationLabel = 'Generate Penyusutan';
    protected static ?string $title = 'Generate Penyusutan';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-right-on-rectangle';
    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.admin.pages.generate-penyusutan';

    /**
     * Check if current user can access this page
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Hanya Admin yang bisa akses Generate Penyusutan
         return $user->isAdmin() || $user->isAsset();
    }

    protected static function isPemiliks(): bool
    {
        return true;
    }
    
    public $periode;

   public $periodeTerakhir;
    public $periodeBerikutnya;

    public function mount()
    {
        $last = \App\Models\Penyusutan::max('periode');

        $this->periodeTerakhir = $last
            ? \Carbon\Carbon::parse($last)->translatedFormat('F Y')
            : 'Belum ada';

        $this->periodeBerikutnya = $last
            ? \Carbon\Carbon::parse($last)->addMonth()->translatedFormat('F Y')
            : '-';
    }
    

    public function generate()
    {
        $tanggal = Carbon::parse($this->periode);
        $tahun = $tanggal->year;

        if (!$tahun) {
            Notification::make()
                ->title('Periode wajib diisi')
                ->danger()
                ->send();
            return;
        }

        // ❗ CEGAH DOUBLE - Cek apakah periode ini sudah pernah di-generate
        $sudahAdaJurnal = Jurnal::whereDate('tanggal', Carbon::parse($this->periode . '-01')->endOfMonth())
            ->where('deskripsi', 'like', 'Penyusutan%' . $this->periode)
            ->exists();

        if ($sudahAdaJurnal) {
            Notification::make()
                ->title('Periode ini sudah disusutkan')
                ->body('Periode ' . Carbon::parse($this->periode)->translatedFormat('F Y') . ' sudah pernah di-generate sebelumnya')
                ->danger()
                ->send();
            return;
        }

        // 🔒 VALIDASI URUTAN - Cek apakah bulan sebelumnya sudah di-generate
        $periodeInput = Carbon::parse($this->periode . '-01');
        
        // Cari penyusutan terakhir yang sudah di-generate (dari tabel penyusutan, lebih akurat)
        $penyusutanTerakhir = Penyusutan::orderBy('periode', 'desc')->first();

        if ($penyusutanTerakhir) {
            $periodeTerakhir = Carbon::parse($penyusutanTerakhir->periode . '-01');
            $periodeBerikutnya = $periodeTerakhir->copy()->addMonth();
            
            // Jika user mau generate periode yang bukan bulan berikutnya
            if (!$periodeInput->isSameMonth($periodeBerikutnya)) {
                // Jika mau generate periode yang sudah lewat
                if ($periodeInput->lte($periodeTerakhir)) {
                    Notification::make()
                        ->title('Tidak bisa generate periode yang sudah lewat!')
                        ->body('Periode terakhir yang sudah di-generate: ' . $periodeTerakhir->translatedFormat('F Y'))
                        ->danger()
                        ->send();
                    return;
                }
                
                // Jika mau loncat periode (skip bulan)
                if ($periodeInput->gt($periodeBerikutnya)) {
                    Notification::make()
                        ->title('Tidak bisa loncat periode!')
                        ->body('Harus generate periode ' . $periodeBerikutnya->translatedFormat('F Y') . ' terlebih dahulu')
                        ->warning()
                        ->send();
                    return;
                }
            }
        }

        $akhirPeriode = Carbon::parse($this->periode . '-01')->endOfMonth();
        $awalPeriode = Carbon::parse($this->periode . '-01')->startOfMonth();

        // 🔥 GENERATE OTOMATIS DARI TANGGAL PEROLEHAN SAMPAI PERIODE INI
        $this->generateHistoris($this->periode);

        Notification::make()
            ->title('Penyusutan berhasil dibuat')
            ->body('Data penyusutan telah di-generate dari tanggal perolehan sampai ' . Carbon::parse($this->periode)->translatedFormat('F Y'))
            ->success()
            ->send();
            
        // Refresh data periode
        $this->mount();
    }

    /**
     * Generate penyusutan historis sampai periode tertentu
     */
    protected function generateHistoris($sampaiPeriode)
    {
        $sampai = Carbon::parse($sampaiPeriode . '-01');
        
        // Load aset beserta relasi kategori sekaligus (eager load)
        $asets = Aset::with('kategori_aset')
            ->whereNotNull('tanggal_perolehan')
            ->whereDate('tanggal_perolehan', '<=', $sampai)
            ->get();

        $periodeYangDiGenerate = []; // Track periode yang di-generate untuk buat jurnal

        foreach ($asets as $aset) {
            $tanggalPerolehan = Carbon::parse($aset->tanggal_perolehan)->startOfMonth();
            $masaManfaatBulan = $aset->masa_manfaat * 12;
            
            // Hitung sampai bulan mana (yang lebih kecil antara sampai atau masa manfaat)
            $bulanMulai = $tanggalPerolehan->copy();
            $bulanAkhir = $sampai->copy();
            $bulanAkhirMasaManfaat = $tanggalPerolehan->copy()->addMonths($masaManfaatBulan - 1);
            
            // Gunakan yang lebih kecil
            if ($bulanAkhir->gt($bulanAkhirMasaManfaat)) {
                $bulanAkhir = $bulanAkhirMasaManfaat;
            }

            // Ambil akumulasi terakhir sebelum periode yang akan di-generate
            $penyusutanTerakhir = Penyusutan::where('aset_id', $aset->id)
                ->where('periode', '<', $bulanMulai->format('Y-m'))
                ->orderBy('periode', 'desc')
                ->first();
            
            $akumulasiSebelumnya = $penyusutanTerakhir ? $penyusutanTerakhir->akumulasi_penyusutan : 0;
            $currentMonth = $bulanMulai->copy();

            while ($currentMonth->lte($bulanAkhir)) {
                $periode = $currentMonth->format('Y-m');

                // Skip jika sudah ada
                $penyusutanExisting = Penyusutan::where('aset_id', $aset->id)
                    ->where('periode', $periode)
                    ->first();

                if ($penyusutanExisting) {
                    $akumulasiSebelumnya = $penyusutanExisting->akumulasi_penyusutan;
                    $currentMonth->addMonth();
                    continue;
                }

                // Generate penyusutan untuk bulan ini
                $this->generatePenyusutanBulan($aset, $periode, $akumulasiSebelumnya);

                // Track periode yang baru di-generate
                if (!in_array($periode, $periodeYangDiGenerate)) {
                    $periodeYangDiGenerate[] = $periode;
                }

                // Update akumulasi untuk bulan berikutnya
                $penyusutanBaru = Penyusutan::where('aset_id', $aset->id)
                    ->where('periode', $periode)
                    ->first();
                if ($penyusutanBaru) {
                    $akumulasiSebelumnya = $penyusutanBaru->akumulasi_penyusutan;
                }

                $currentMonth->addMonth();
            }
        }
        
        // Generate jurnal untuk SEMUA periode yang baru di-generate
        foreach ($periodeYangDiGenerate as $periode) {
            $this->generateJurnalPeriode($periode);
        }
    }

    /**
     * Generate penyusutan untuk 1 bulan
     */
    protected function generatePenyusutanBulan(Aset $aset, string $periode, float $akumulasiSebelumnya)
    {
        $akhirPeriode = Carbon::parse($periode . '-01')->endOfMonth();
        $awalPeriode = Carbon::parse($periode . '-01')->startOfMonth();
        
        $nilaiResidu = $aset->nilai_residu ?? 0;

        // AMBIL PENINGKATAN SAMPAI PERIODE INI
        $peningkatan = \App\Models\Pemeliharaan::where('aset_id', $aset->id)
            ->where('jenis_perbaikan', 'peningkatan')
            ->whereDate('tanggal', '<=', $akhirPeriode)
            ->sum('biaya');

        // NILAI PEROLEHAN + PENINGKATAN
        $nilaiPerolehan = $aset->nilai_perolehan + $peningkatan;

        // MASA MANFAAT (bulan)
        $masaManfaat = $aset->masa_manfaat * 12;

        // TAMBAH UMUR dari peningkatan s/d periode ini
        $tambahUmur = \App\Models\Pemeliharaan::where('aset_id', $aset->id)
            ->where('jenis_perbaikan', 'peningkatan')
            ->whereDate('tanggal', '<=', $akhirPeriode)
            ->sum('tambah_umur');

        $masaManfaat += $tambahUmur;

        // Hitung bulan terpakai
        $periodePerolehan = Carbon::parse($aset->tanggal_perolehan)->startOfMonth();
        $periodeSaat = Carbon::parse($periode . '-01');
        $bulanTerpakai = $periodePerolehan->diffInMonths($periodeSaat);

        $sisaMasaManfaat = $masaManfaat - $bulanTerpakai;

        if ($sisaMasaManfaat <= 0) {
            return; // aset sudah habis masa manfaatnya
        }

        // Ambil nilai buku sebelumnya
        $last = Penyusutan::where('aset_id', $aset->id)
            ->where('periode', '<', $periode)
            ->orderBy('periode', 'desc')
            ->first();

        $nilaiBukuSebelumnya = $last ? $last->nilai_buku : $aset->nilai_perolehan;

        // Cek peningkatan baru di periode ini
        $peningkatanBaru = \App\Models\Pemeliharaan::where('aset_id', $aset->id)
            ->where('jenis_perbaikan', 'peningkatan')
            ->whereDate('tanggal', '>=', $awalPeriode)
            ->whereDate('tanggal', '<=', $akhirPeriode)
            ->sum('biaya');

        $nilaiBukuSebelumnya += $peningkatanBaru;

        // Beban bulan ini = (nilai buku sebelumnya - residu) / sisa masa manfaat
        $beban = ($nilaiBukuSebelumnya - $nilaiResidu) / $sisaMasaManfaat;

        // Pastikan beban tidak negatif dan tidak melebihi sisa
        $sisaYangBisaDisusut = $nilaiPerolehan - $nilaiResidu - $akumulasiSebelumnya;

        if ($beban > $sisaYangBisaDisusut) {
            $beban = $sisaYangBisaDisusut;
        }

        if ($beban <= 0) {
            return;
        }

        $akumulasi = $akumulasiSebelumnya + $beban;
        $nilai_buku = $nilaiBukuSebelumnya - $beban;

        Penyusutan::create([
            'aset_id'              => $aset->id,
            'periode'              => $periode,
            'beban_penyusutan'     => round($beban, 2),
            'akumulasi_penyusutan' => round($akumulasi, 2),
            'nilai_buku'           => round($nilai_buku, 2),
        ]);
    }

    /**
     * Generate jurnal untuk periode tertentu
     */
    protected function generateJurnalPeriode($periode)
    {
        $akhirPeriode = Carbon::parse($periode . '-01')->endOfMonth();
        
        // Cek apakah sudah ada jurnal untuk periode ini
        $jurnalExist = Jurnal::whereDate('tanggal', $akhirPeriode)
            ->where('deskripsi', 'like', 'Penyusutan%' . $periode)
            ->exists();
            
        if ($jurnalExist) {
            return; // Skip jika sudah ada jurnal
        }
        
        // Ambil semua penyusutan di periode ini dan group by kategori
        $penyusutans = Penyusutan::where('periode', $periode)
            ->with('aset.kategori_aset')
            ->get();
            
        $totalPerKategori = [];
        
        foreach ($penyusutans as $p) {
            $namaKategori = $p->aset->kategori_aset?->nama_kategori ?? 'Lainnya';
            
            if (!isset($totalPerKategori[$namaKategori])) {
                $totalPerKategori[$namaKategori] = 0;
            }
            
            $totalPerKategori[$namaKategori] += $p->beban_penyusutan;
        }
        
        if (empty($totalPerKategori)) {
            return;
        }

        // Mapping kategori → [no_akun_beban, no_akun_akumulasi]
        $mappingAkun = [
            'Mesin'      => ['beban' => 753, 'akumulasi' => 174],
            'Peralatan'  => ['beban' => 754, 'akumulasi' => 175],
            'Bangunan'   => ['beban' => 755, 'akumulasi' => 176],
            'Kendaraan'  => ['beban' => 756, 'akumulasi' => 177],
        ];

        // Fallback kalau kategori tidak ada di mapping
        $akunBebanDefault     = Akun::where('no_akun', 752)->firstOrFail();
        $akunAkumulasiDefault = Akun::where('no_akun', 173)->firstOrFail();

        // JURNAL PER KATEGORI
        foreach ($totalPerKategori as $namaKategori => $totalBeban) {
            if (isset($mappingAkun[$namaKategori])) {
                $akunBeban     = Akun::where('no_akun', $mappingAkun[$namaKategori]['beban'])->firstOrFail();
                $akunAkumulasi = Akun::where('no_akun', $mappingAkun[$namaKategori]['akumulasi'])->firstOrFail();
            } else {
                $akunBeban     = $akunBebanDefault;
                $akunAkumulasi = $akunAkumulasiDefault;
            }

            $jurnal = Jurnal::create([
                'tanggal'    => $akhirPeriode,
                'deskripsi' => 'Penyusutan ' . $namaKategori . ' ' . $periode,
            ]);

            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun'   => $akunBeban->id,
                'debit'     => round($totalBeban, 2),
                'credit'    => 0,
                'deskripsi' => 'Beban Penyusutan ' . $namaKategori,
            ]);

            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun'   => $akunAkumulasi->id,
                'debit'     => 0,
                'credit'    => round($totalBeban, 2),
                'deskripsi' => 'Akumulasi Penyusutan ' . $namaKategori,
            ]);
        }
    }
}