<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use App\Models\Akun;
use App\Models\JurnalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class NeracaPage extends Page
{
    protected static ?string $navigationLabel = 'Laporan Posisi Keuangan';
    protected static ?string $title = 'Laporan Posisi Keuangan';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-right-on-rectangle';
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 303;

    /**
     * Check if current user can access this page
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin dan Pemilik bisa akses Neraca
        return $user->isAdmin() || $user->isPemilik();
    }

    public function getView(): string
    {
        return 'filament.admin.pages.neraca-page';
    }

    // ================== AKTIVA ==================
    public array $aktivaLancar = [];
    public array $aktivaTetap  = [];
    public int $totalAktivaLancar = 0;
    public int $totalAktivaTetap  = 0;
    public int $totalAktiva       = 0;

    // ================== PASIVA ==================
    public array $liabilitas = [];
    public array $ekuitas    = [];
    public int $totalLiabilitas = 0;
    public int $totalEkuitas    = 0;
    public int $totalPasiva     = 0;

    // ================== SALDO TIDAK NORMAL ==================
    public array $saldoTidakNormal = [];

    // ================== DEBUG INFO ==================
    public array $debugInfo = [];
    public bool $showDebug = false;

    // ================== PERIODE ==================
    public string $periode;
    public string $tanggal_awal;
    public string $tanggal_akhir;

    

    public function mount(): void
    {

        $this->periode = now()->format('Y-m');
         $this->tanggal_awal = Carbon::createFromFormat('Y-m', $this->periode)
        ->startOfMonth()
        ->format('Y-m-d');

        $this->tanggal_akhir = Carbon::createFromFormat('Y-m', $this->periode)
        ->endOfMonth()
        ->format('Y-m-d');

        $this->hitungNeraca();
    }

   public function updatedPeriode(): void
{
    $this->tanggal_awal = Carbon::createFromFormat('Y-m', $this->periode)
        ->startOfMonth()
        ->format('Y-m-d');

    $this->tanggal_akhir = Carbon::createFromFormat('Y-m', $this->periode)
        ->endOfMonth()
        ->format('Y-m-d');

    $this->hitungNeraca();
}

    protected function hitungNeraca(): void
    {
        // Reset semua nilai
        $this->aktivaLancar = [];
        $this->aktivaTetap = [];
        $this->liabilitas = [];
        $this->ekuitas = [];
        $this->saldoTidakNormal = [];
        $this->debugInfo = [];
        
        $this->totalAktivaLancar = 0;
        $this->totalAktivaTetap  = 0;
        $this->totalLiabilitas   = 0;
        $this->totalEkuitas      = 0;

        // ================= VALIDASI JURNAL BALANCE =================
        $totalDebitJurnal = JurnalDetail::whereHas('jurnal', function($q) {
            $q->where('tanggal', '<=', $this->tanggal_akhir);
        })->sum('debit');

        $totalKreditJurnal = JurnalDetail::whereHas('jurnal', function($q) {
            $q->where('tanggal', '<=', $this->tanggal_akhir);
        })->sum('credit');

        $this->debugInfo['total_debit_jurnal'] = $totalDebitJurnal;
        $this->debugInfo['total_kredit_jurnal'] = $totalKreditJurnal;
        $this->debugInfo['selisih_jurnal'] = $totalDebitJurnal - $totalKreditJurnal;

        // ================= AKTIVA (HEADER 1) =================
        $akunAktiva = Akun::where('header_akun', 1)->get();
        $this->debugInfo['jumlah_akun_aset'] = $akunAktiva->count();
        $this->debugInfo['detail_aset'] = [];

        foreach ($akunAktiva as $akun) {
            // Hitung total debit dan kredit
            $debit = JurnalDetail::where('no_akun', $akun->id)
                ->whereHas('jurnal', function($q) {
                    $q->where('tanggal', '<=', $this->tanggal_akhir);
                })
                ->sum('debit');

            $credit = JurnalDetail::where('no_akun', $akun->id)
                ->whereHas('jurnal', function($q) {
                    $q->where('tanggal', '<=', $this->tanggal_akhir);
                })
                ->sum('credit');

            // Akumulasi Penyusutan = kontra aset, saldo normal kredit
            $isKontraAset = in_array((string) $akun->no_akun, ['173', '174', '175', '176', '177']);

            if ($isKontraAset) {
                // Kontra aset: saldo normal kredit, ditampilkan sebagai pengurang
                $saldo = $credit - $debit;
                
                // Jika saldo negatif (debit > kredit), tandai sebagai tidak normal
                if ($saldo < 0) {
                    $this->saldoTidakNormal[] = [
                        'akun' => $akun->nama_akun,
                        'saldo' => $saldo,
                        'keterangan' => 'Akumulasi Penyusutan seharusnya kredit'
                    ];
                }
                
                // Untuk neraca, akumulasi penyusutan ditampilkan sebagai pengurang (negatif)
                $saldo = -abs($saldo);
            } else {
                // Aset normal: saldo normal debit
                $saldo = $debit - $credit;
                
                // Jika saldo negatif (kredit > debit), tandai sebagai tidak normal
                if ($saldo < 0) {
                    $this->saldoTidakNormal[] = [
                        'akun' => $akun->nama_akun,
                        'saldo' => $saldo,
                        'keterangan' => 'Aset seharusnya debit'
                    ];
                }
            }

            $akun->saldo = $saldo;
            $akun->is_abnormal = $saldo < 0 && !$isKontraAset;

            // Kategorikan berdasarkan kode akun
            $subkode = (int) substr($akun->no_akun, 0, 2);
            $kategori = '';

            if (in_array($subkode, [11, 12, 13, 14, 15])) {
                // AKTIVA LANCAR (11x = Kas, 12x = Piutang, 13x = Persediaan, 14x = Investasi Jangka Pendek, 15x = Perlengkapan)
                $this->aktivaLancar[] = $akun;
                $this->totalAktivaLancar += round($saldo, 0); // Bulatkan per akun
                $kategori = 'Aset Lancar';
            } elseif (in_array($subkode, [16, 17, 18, 19])) {
                // AKTIVA TETAP (16x = Tanah, 17x = Peralatan/Akumulasi, 18x = Kendaraan/Bangunan, 19x = Aset Tetap Lainnya)
                $this->aktivaTetap[] = $akun;
                $this->totalAktivaTetap += round($saldo, 0); // Bulatkan per akun
                $kategori = 'Aset Tetap';
            } else {
                $kategori = 'Tidak Terkategori';
            }

            // Debug info per akun
            if ($debit != 0 || $credit != 0) {
                $this->debugInfo['detail_aset'][] = [
                    'no_akun' => $akun->no_akun,
                    'nama_akun' => $akun->nama_akun,
                    'debit' => $debit,
                    'kredit' => $credit,
                    'saldo' => $saldo,
                    'kategori' => $kategori,
                    'is_kontra' => $isKontraAset
                ];
            }
        }

        $this->totalAktiva = $this->totalAktivaLancar + $this->totalAktivaTetap;

        // ================= LIABILITAS (header_akun 2) =================
        $akunLiabilitas = Akun::where('header_akun', 2)->get();
        $this->debugInfo['jumlah_akun_liabilitas'] = $akunLiabilitas->count();
        $this->debugInfo['detail_liabilitas'] = [];

        foreach ($akunLiabilitas as $akun) {
            // Hitung total debit dan kredit
            $debit = JurnalDetail::where('no_akun', $akun->id)
                ->whereHas('jurnal', function($q) {
                    $q->where('tanggal', '<=', $this->tanggal_akhir);
                })
                ->sum('debit');

            $credit = JurnalDetail::where('no_akun', $akun->id)
                ->whereHas('jurnal', function($q) {
                    $q->where('tanggal', '<=', $this->tanggal_akhir);
                })
                ->sum('credit');

            // Liabilitas: saldo normal kredit
            $saldo = $credit - $debit;

            // Jika saldo negatif (debit > kredit), tandai sebagai tidak normal
            if ($saldo < 0) {
                $this->saldoTidakNormal[] = [
                    'akun' => $akun->nama_akun,
                    'saldo' => $saldo,
                    'keterangan' => 'Kewajiban seharusnya kredit'
                ];
            }

            $akun->saldo = $saldo;
            $akun->is_abnormal = $saldo < 0;
            $this->liabilitas[] = $akun;
            $this->totalLiabilitas += round($saldo, 0); // Bulatkan per akun

            // Debug info
            if ($debit != 0 || $credit != 0) {
                $this->debugInfo['detail_liabilitas'][] = [
                    'no_akun' => $akun->no_akun,
                    'nama_akun' => $akun->nama_akun,
                    'debit' => $debit,
                    'kredit' => $credit,
                    'saldo' => $saldo
                ];
            }
        }

        // ================= EKUITAS (header_akun 3) =================
        $akunEkuitas = Akun::where('header_akun', 3)->get();
        $this->debugInfo['jumlah_akun_ekuitas'] = $akunEkuitas->count();
        $this->debugInfo['detail_ekuitas'] = [];

        foreach ($akunEkuitas as $akun) {
            // ❌ SKIP laba ditahan dari database
            if ($akun->no_akun == 313) {
                continue;
            }

            // Hitung total debit dan kredit
            $debit = JurnalDetail::where('no_akun', $akun->id)
                ->whereHas('jurnal', function($q) {
                    $q->where('tanggal', '<=', $this->tanggal_akhir);
                })
                ->sum('debit');

            $credit = JurnalDetail::where('no_akun', $akun->id)
                ->whereHas('jurnal', function($q) {
                    $q->where('tanggal', '<=', $this->tanggal_akhir);
                })
                ->sum('credit');

            // Ekuitas: saldo normal kredit (kecuali Prive yang debit)
            $isPrive = stripos($akun->nama_akun, 'prive') !== false;
            
            if ($isPrive) {
                // Prive: saldo normal debit, ditampilkan sebagai pengurang ekuitas
                $saldo = $debit - $credit;
                
                if ($saldo < 0) {
                    $this->saldoTidakNormal[] = [
                        'akun' => $akun->nama_akun,
                        'saldo' => $saldo,
                        'keterangan' => 'Prive seharusnya debit'
                    ];
                }
                
                // Untuk neraca, prive ditampilkan sebagai pengurang (negatif)
                $saldo = -abs($saldo);
            } else {
                // Ekuitas normal (Modal, dll): saldo normal kredit
                $saldo = $credit - $debit;
                
                if ($saldo < 0) {
                    $this->saldoTidakNormal[] = [
                        'akun' => $akun->nama_akun,
                        'saldo' => $saldo,
                        'keterangan' => 'Ekuitas seharusnya kredit'
                    ];
                }
            }

            $akun->saldo = $saldo;
            $akun->is_abnormal = $saldo < 0 && !$isPrive;
            $this->ekuitas[] = $akun;
            $this->totalEkuitas += round($saldo, 0); // Bulatkan per akun

            // Debug info
            if ($debit != 0 || $credit != 0) {
                $this->debugInfo['detail_ekuitas'][] = [
                    'no_akun' => $akun->no_akun,
                    'nama_akun' => $akun->nama_akun,
                    'debit' => $debit,
                    'kredit' => $credit,
                    'saldo' => $saldo,
                    'is_prive' => $isPrive
                ];
            }
        }

        // ================= LABA =================

        // PENDAPATAN (4xx): saldo normal kredit
        $debitPendapatan = JurnalDetail::whereHas('akun', function($q) {
            $q->where('no_akun', 'like', '4%');
        })
        ->whereHas('jurnal', function($q) {
            $q->where('tanggal', '<=', $this->tanggal_akhir);
        })
        ->sum('debit');

        $creditPendapatan = JurnalDetail::whereHas('akun', function($q) {
            $q->where('no_akun', 'like', '4%');
        })
        ->whereHas('jurnal', function($q) {
            $q->where('tanggal', '<=', $this->tanggal_akhir);
        })
        ->sum('credit');

        $pendapatan = $creditPendapatan - $debitPendapatan;

        // BEBAN (5xx, 6xx, 7xx): saldo normal debit
        $debitBeban = JurnalDetail::whereHas('akun', function($q) {
            $q->whereIn('header_akun', [5, 6, 7]);
        })
        ->whereHas('jurnal', function($q) {
            $q->where('tanggal', '<=', $this->tanggal_akhir);
        })
        ->sum('debit');

        $creditBeban = JurnalDetail::whereHas('akun', function($q) {
            $q->whereIn('header_akun', [5, 6, 7]);
        })
        ->whereHas('jurnal', function($q) {
            $q->where('tanggal', '<=', $this->tanggal_akhir);
        })
        ->sum('credit');

        $beban = $debitBeban - $creditBeban;

        // LABA = Pendapatan - Beban
        $laba = round($pendapatan - $beban, 0); // Bulatkan laba

        // Debug info laba
        $this->debugInfo['pendapatan'] = [
            'debit' => $debitPendapatan,
            'kredit' => $creditPendapatan,
            'saldo' => $pendapatan
        ];
        $this->debugInfo['beban'] = [
            'debit' => $debitBeban,
            'kredit' => $creditBeban,
            'saldo' => $beban
        ];
        $this->debugInfo['laba_rugi'] = $laba;

        // Tambahkan ke ekuitas
        $this->totalEkuitas += $laba;

        // Tampilkan di tabel
        $labaDitahan = (object)[
            'nama_akun' => 'Laba Ditahan',
            'saldo' => $laba,
            'is_abnormal' => false
        ];
        $this->ekuitas[] = $labaDitahan;

        $this->totalPasiva = $this->totalLiabilitas + $this->totalEkuitas;

        // ================= DEBUG SUMMARY =================
        $this->debugInfo['summary'] = [
            'total_aset_lancar' => round($this->totalAktivaLancar, 0),
            'total_aset_tetap' => round($this->totalAktivaTetap, 0),
            'total_aset' => round($this->totalAktiva, 0),
            'total_liabilitas' => round($this->totalLiabilitas, 0),
            'total_ekuitas' => round($this->totalEkuitas, 0),
            'total_pasiva' => round($this->totalPasiva, 0),
            'selisih_neraca' => round($this->totalAktiva - $this->totalPasiva, 0),
            'is_balance' => abs(round($this->totalAktiva - $this->totalPasiva, 0)) < 1
        ];
    }

    public function toggleDebug(): void
    {
        $this->showDebug = !$this->showDebug;
    }
}

