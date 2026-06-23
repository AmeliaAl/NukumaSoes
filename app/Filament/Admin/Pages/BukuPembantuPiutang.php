<?php

namespace App\Filament\Admin\Pages;

use App\Models\Pelanggan;
use App\Models\Mitra;
use App\Models\PenjualanNonKonsinyasi;
use App\Models\Pembayaran;
use App\Models\TagihanKonsinyasi;
use App\Models\PembayaranTagihanKonsinyasi;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class BukuPembantuPiutang extends Page
{
    protected string $view = 'filament.admin.pages.buku-pembantu-piutang';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Buku Pembantu Piutang';

    protected static ?string $title = 'Buku Pembantu Piutang';

    protected static \UnitEnum|string|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 3;

    public ?string $dari        = null;
    public ?string $sampai      = null;
    public ?string $inputDari   = null;
    public ?string $inputSampai = null;
    public string  $filterAkun  = 'semua'; // semua | pelanggan | mitra

    public function applyFilter(): void
    {
        $this->dari   = $this->inputDari;
        $this->sampai = $this->inputSampai;
    }

    public function resetFilter(): void
    {
        $this->dari        = null;
        $this->sampai      = null;
        $this->inputDari   = null;
        $this->inputSampai = null;
        $this->filterAkun  = 'semua';
    }

    /**
     * Data piutang per pelanggan (penjualan non-konsinyasi kredit)
     */
    public function getDataPelanggan(): Collection
    {
        // Hanya pelanggan yang punya transaksi kredit
        $pelangganIds = PenjualanNonKonsinyasi::where('jenis_pembayaran', 'kredit')
            ->when($this->dari,   fn ($q) => $q->whereDate('tanggal', '>=', $this->dari))
            ->when($this->sampai, fn ($q) => $q->whereDate('tanggal', '<=', $this->sampai))
            ->distinct()
            ->pluck('pelanggan_id');

        return Pelanggan::whereIn('id', $pelangganIds)
            ->orderBy('kode_pelanggan')
            ->get()
            ->map(function ($pelanggan) {
                $penjualans = PenjualanNonKonsinyasi::where('pelanggan_id', $pelanggan->id)
                    ->where('jenis_pembayaran', 'kredit')
                    ->when($this->dari,   fn ($q) => $q->whereDate('tanggal', '>=', $this->dari))
                    ->when($this->sampai, fn ($q) => $q->whereDate('tanggal', '<=', $this->sampai))
                    ->orderBy('tanggal')
                    ->get();

                $saldo = 0;
                $rows  = [];

                foreach ($penjualans as $p) {
                    // Baris penjualan (debit piutang)
                    $saldo += $p->total;
                    $rows[] = [
                        'tanggal'     => $p->tanggal,
                        'keterangan'  => 'Penjualan ' . $p->no_invoice,
                        'ref'         => $p->no_invoice,
                        'debit'       => $p->total,
                        'kredit'      => 0,
                        'saldo_debit' => $saldo > 0 ? $saldo : 0,
                        'saldo_kredit'=> $saldo < 0 ? abs($saldo) : 0,
                    ];

                    // Baris pembayaran (kredit piutang)
                    $pembayarans = Pembayaran::where('penjualan_id', $p->id)
                        ->when($this->dari,   fn ($q) => $q->whereDate('tanggal_bayar', '>=', $this->dari))
                        ->when($this->sampai, fn ($q) => $q->whereDate('tanggal_bayar', '<=', $this->sampai))
                        ->orderBy('tanggal_bayar')
                        ->get();

                    foreach ($pembayarans as $bayar) {
                        $saldo -= $bayar->jumlah_bayar;
                        $rows[] = [
                            'tanggal'     => $bayar->tanggal_bayar,
                            'keterangan'  => 'Pembayaran ' . $bayar->kode_pembayaran,
                            'ref'         => $bayar->kode_pembayaran,
                            'debit'       => 0,
                            'kredit'      => $bayar->jumlah_bayar,
                            'saldo_debit' => $saldo > 0 ? $saldo : 0,
                            'saldo_kredit'=> $saldo < 0 ? abs($saldo) : 0,
                        ];
                    }
                }

                return [
                    'kode'        => $pelanggan->kode_pelanggan,
                    'nama'        => $pelanggan->namaPelanggan,
                    'rows'        => collect($rows),
                    'saldo_akhir' => $saldo,
                ];
            });
    }

    /**
     * Data piutang per mitra (tagihan konsinyasi)
     */
    public function getDataMitra(): Collection
    {
        $mitraIds = TagihanKonsinyasi::query()
            ->join('laporan_konsinyasi', 'tagihan_konsinyasi.laporan_konsinyasi_id', '=', 'laporan_konsinyasi.id')
            ->join('penjualan_konsinyasi', 'laporan_konsinyasi.penjualan_konsinyasi_id', '=', 'penjualan_konsinyasi.id')
            ->when($this->dari,   fn ($q) => $q->whereDate('tagihan_konsinyasi.tanggal_tagihan', '>=', $this->dari))
            ->when($this->sampai, fn ($q) => $q->whereDate('tagihan_konsinyasi.tanggal_tagihan', '<=', $this->sampai))
            ->distinct()
            ->pluck('penjualan_konsinyasi.kode_mitra');

        return Mitra::whereIn('kode_mitra', $mitraIds)
            ->orderBy('kode_mitra')
            ->get()
            ->map(function ($mitra) {
                $tagihans = TagihanKonsinyasi::query()
                    ->join('laporan_konsinyasi', 'tagihan_konsinyasi.laporan_konsinyasi_id', '=', 'laporan_konsinyasi.id')
                    ->join('penjualan_konsinyasi', 'laporan_konsinyasi.penjualan_konsinyasi_id', '=', 'penjualan_konsinyasi.id')
                    ->where('penjualan_konsinyasi.kode_mitra', $mitra->kode_mitra)
                    ->when($this->dari,   fn ($q) => $q->whereDate('tagihan_konsinyasi.tanggal_tagihan', '>=', $this->dari))
                    ->when($this->sampai, fn ($q) => $q->whereDate('tagihan_konsinyasi.tanggal_tagihan', '<=', $this->sampai))
                    ->orderBy('tagihan_konsinyasi.tanggal_tagihan')
                    ->select('tagihan_konsinyasi.*')
                    ->get();

                $saldo = 0;
                $rows  = [];

                foreach ($tagihans as $tagihan) {
                    // Baris tagihan (debit piutang)
                    $saldo += $tagihan->total_tagihan;
                    $rows[] = [
                        'tanggal'     => $tagihan->tanggal_tagihan,
                        'keterangan'  => 'Tagihan ' . $tagihan->no_tagihan,
                        'ref'         => $tagihan->no_tagihan,
                        'debit'       => $tagihan->total_tagihan,
                        'kredit'      => 0,
                        'saldo_debit' => $saldo > 0 ? $saldo : 0,
                        'saldo_kredit'=> $saldo < 0 ? abs($saldo) : 0,
                    ];

                    // Baris pembayaran tagihan (kredit piutang)
                    $pembayarans = PembayaranTagihanKonsinyasi::where('tagihan_konsinyasi_id', $tagihan->id)
                        ->when($this->dari,   fn ($q) => $q->whereDate('tanggal_bayar', '>=', $this->dari))
                        ->when($this->sampai, fn ($q) => $q->whereDate('tanggal_bayar', '<=', $this->sampai))
                        ->orderBy('tanggal_bayar')
                        ->get();

                    foreach ($pembayarans as $bayar) {
                        $saldo -= $bayar->jumlah_bayar;
                        $rows[] = [
                            'tanggal'     => $bayar->tanggal_bayar,
                            'keterangan'  => 'Pembayaran Tagihan',
                            'ref'         => $tagihan->no_tagihan,
                            'debit'       => 0,
                            'kredit'      => $bayar->jumlah_bayar,
                            'saldo_debit' => $saldo > 0 ? $saldo : 0,
                            'saldo_kredit'=> $saldo < 0 ? abs($saldo) : 0,
                        ];
                    }
                }

                return [
                    'kode'        => $mitra->kode_mitra,
                    'nama'        => $mitra->namaMitra,
                    'rows'        => collect($rows),
                    'saldo_akhir' => $saldo,
                ];
            });
    }
}
