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
        return $user->isAdmin();
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

        $akhirPeriode = Carbon::parse($this->periode . '-01')->endOfMonth();
        $awalPeriode = Carbon::parse($this->periode . '-01')->startOfMonth();

        // 🔒 LOCK PERIODE (WAJIB URUT) — cek gap
        $lastGlobal = Penyusutan::orderBy('periode', 'desc')->first();

        if ($lastGlobal) {
            $lastPeriode = Carbon::parse($lastGlobal->periode . '-01');
            $inputPeriode = Carbon::parse($this->periode . '-01');

            // Cek apakah ada gap (loncat lebih dari 1 bulan)
            $diffMonths = $lastPeriode->diffInMonths($inputPeriode);

            if ($diffMonths > 1) {
                $nextPeriode = $lastPeriode->addMonth()->format('Y-m');
                Notification::make()
                    ->title('Tidak bisa loncat periode!')
                    ->body("Harus generate bulan $nextPeriode dulu")
                    ->danger()
                    ->send();
                return;
            }

            // Cek kalau mau generate periode yang lebih lama dari terakhir
            if ($inputPeriode->lt($lastPeriode)) {
                Notification::make()
                    ->title('Tidak bisa generate periode yang sudah lewat!')
                    ->body("Periode terakhir sudah {$lastGlobal->periode}")
                    ->danger()
                    ->send();
                return;
            }
        }

        // ❗ CEGAH DOUBLE
        if (Penyusutan::where('periode', $this->periode)->exists()) {
            Notification::make()
                ->title('Periode ini sudah disusutkan')
                ->danger()
                ->send();
            return;
        }

        // Load aset beserta relasi kategori sekaligus (eager load)
        $asets = Aset::with('kategori_aset')
            ->whereNotNull('tanggal_perolehan')
            ->whereDate('tanggal_perolehan', '<=', $akhirPeriode)
            ->get();

        // Tampung beban per kategori:
        // [
        //   'Mesin'      => ['total' => 500000, 'nama' => 'Mesin'],
        //   'Peralatan'  => ['total' => 300000, 'nama' => 'Peralatan'],
        // ]
        $totalPerKategori = [];

        foreach ($asets as $aset) {

            $tanggalPerolehan = Carbon::parse($aset->tanggal_perolehan)->startOfMonth();

            // ❌ kalau periode sebelum bulan beli → skip
            if (Carbon::parse($this->periode . '-01')->lt($tanggalPerolehan)) {
                continue;
            }

            $nilaiResidu = $aset->nilai_residu ?? 0;

            // 🔥 AMBIL PENINGKATAN SAMPAI PERIODE INI
            $peningkatan = \App\Models\Pemeliharaan::where('aset_id', $aset->id)
                ->where('jenis_perbaikan', 'peningkatan')
                ->whereDate('tanggal', '<=', $akhirPeriode)
                ->sum('biaya');

            // 🔥 NILAI PEROLEHAN + PENINGKATAN
            $nilaiPerolehan = $aset->nilai_perolehan + $peningkatan;

            // 🔥 MASA MANFAAT (bulan)
            $masaManfaat = $aset->masa_manfaat * 12;

            // 🔥 TAMBAH UMUR dari peningkatan s/d periode ini
            $tambahUmur = \App\Models\Pemeliharaan::where('aset_id', $aset->id)
                ->where('jenis_perbaikan', 'peningkatan')
                ->whereDate('tanggal', '<=', $akhirPeriode)
                ->sum('tambah_umur'); // dalam bulan

            $masaManfaat += $tambahUmur;

            // 🔥 AMBIL STATE TERAKHIR SEBELUM PERIODE INI
            $last = Penyusutan::where('aset_id', $aset->id)
                ->where('periode', '<', $this->periode)
                ->orderBy('periode', 'desc')
                ->first();

            // ✅ Ambil nilai perolehan yang dipakai di periode sebelumnya
            // supaya perubahan peningkatan tidak merusak akumulasi lama
            $nilaiPerolehanSebelumnya = $last
                ? ($last->nilai_buku + $last->akumulasi_penyusutan)
                : $aset->nilai_perolehan;

            $akumulasiSebelumnya = $last ? $last->akumulasi_penyusutan : 0;

            // 🔥 Kalau ada peningkatan baru sejak periode lalu,
            // recalculate beban berdasarkan sisa masa manfaat
            $periodePerolehan = Carbon::parse($aset->tanggal_perolehan)->startOfMonth();
            $periodeSaat = Carbon::parse($this->periode . '-01');
            $bulanTerpakai = $periodePerolehan->diffInMonths($periodeSaat); // sudah berapa bulan

            $sisaMasaManfaat = $masaManfaat - $bulanTerpakai;

            if ($sisaMasaManfaat <= 0) {
                continue; // aset sudah habis masa manfaatnya
            }

            $nilaiBukuSebelumnya = $last
            ? $last->nilai_buku
            : $aset->nilai_perolehan;

            $peningkatanBaru = \App\Models\Pemeliharaan::where('aset_id', $aset->id)
                ->where('jenis_perbaikan', 'peningkatan')
                ->whereDate('tanggal', '>=', $awalPeriode)
                ->whereDate('tanggal', '<=', $akhirPeriode)
                ->sum('biaya');

            $nilaiBukuSebelumnya += $peningkatanBaru;

            // 🔥 Beban bulan ini = (nilai buku sebelumnya - residu) / sisa masa manfaat
            $beban = ($nilaiBukuSebelumnya - $nilaiResidu) / $sisaMasaManfaat;

            // 🔴 Pastikan beban tidak negatif dan tidak melebihi sisa
            $sisaYangBisaDisusut = $nilaiPerolehan - $nilaiResidu - $akumulasiSebelumnya;

            if ($beban > $sisaYangBisaDisusut) {
                $beban = $sisaYangBisaDisusut;
            }

            if ($beban <= 0) {
                continue;
            }

            $akumulasi = $akumulasiSebelumnya + $beban;
            $nilai_buku = $nilaiBukuSebelumnya - $beban;

            Penyusutan::create([
                'aset_id'              => $aset->id,
                'periode'              => $this->periode,
                'beban_penyusutan'     => round($beban, 2),
                'akumulasi_penyusutan' => round($akumulasi, 2),
                'nilai_buku'           => round($nilai_buku, 2),
            ]);

            // 📂 Akumulasi beban per kategori
            $namaKategori = $aset->kategori_aset?->nama_kategori ?? 'Lainnya';

            if (!isset($totalPerKategori[$namaKategori])) {
                $totalPerKategori[$namaKategori] = 0;
            }

            $totalPerKategori[$namaKategori] += $beban;
        }

        if (empty($totalPerKategori)) {
            Notification::make()
                ->title('Tidak ada beban penyusutan di periode ini')
                ->danger()
                ->send();
            return;
        }

        // Mapping kategori → [no_akun_beban, no_akun_akumulasi]
        $mappingAkun = [
            'Mesin'      => ['beban' => 753, 'akumulasi' => 174],
            'Peralatan'  => ['beban' => 754, 'akumulasi' => 175],
            'Bangunan'   => ['beban' => 755, 'akumulasi' => 176],
            'Kendaraan'  => ['beban' => 756, 'akumulasi' => 177],
        ];

        // Mapping kategori ke akun aset (untuk konsistensi dengan FakturPembelianService)
        $mappingAkunAset = [
            'Mesin'      => 172,
            'Peralatan'  => 171,
            'Bangunan'   => 179,
            'Kendaraan'  => 178,
        ];

        // Fallback kalau kategori tidak ada di mapping
        $akunBebanDefault     = Akun::where('no_akun', 752)->firstOrFail();
        $akunAkumulasiDefault = Akun::where('no_akun', 173)->firstOrFail();

        // 🔵 JURNAL PER KATEGORI
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
                'keterangan' => 'Penyusutan ' . $namaKategori . ' ' . $this->periode,
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

        Notification::make()
            ->title('Penyusutan bulanan berhasil dibuat')
            ->success()
            ->send();
    }
}