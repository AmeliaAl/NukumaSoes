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

class LaporanPenjualan extends Page
{
    protected string $view = 'filament.admin.pages.laporan-penjualan';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Penjualan';

    protected static ?string $title = 'Laporan Penjualan';

    protected static \UnitEnum|string|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 3;

    public ?string $dari        = null;
    public ?string $sampai      = null;
    public ?string $inputDari   = null;
    public ?string $inputSampai = null;
    
    public ?string $tanggalDari = null;
    public ?string $tanggalSampai = null;
    public ?string $status = 'semua';

    public function mount(): void
    {
        $this->tanggalDari = now()->startOfMonth()->format('Y-m-d');
        $this->tanggalSampai = now()->format('Y-m-d');
        $this->status = 'semua';
    }

    public function getLaporanDataProperty()
    {
        $tanggalDari = $this->tanggalDari ?? now()->startOfMonth()->format('Y-m-d');
        $tanggalSampai = $this->tanggalSampai ?? now()->format('Y-m-d');
        $status = $this->status ?? 'semua';

        $konsinyasi = \DB::table('detail_laporan_konsinyasi as dlk')
            ->join('laporan_konsinyasi as lk', 'dlk.no_laporan', '=', 'lk.no_laporan')
            ->join('barang as b', 'dlk.barang_id', '=', 'b.id')
            ->join('kategori as k', 'b.kategori_id', '=', 'k.id')
            ->join('tagihan_konsinyasi as tk', 'lk.id', '=', 'tk.laporan_konsinyasi_id')
            ->join('penjualan_konsinyasi as pk', 'lk.penjualan_konsinyasi_id', '=', 'pk.id')
            ->join('mitra as m', 'pk.kode_mitra', '=', 'm.kode_mitra')
            ->whereBetween('lk.tanggal_laporan', [$tanggalDari, $tanggalSampai])
            ->select([
                'b.nama_barang', 'k.nama_kategori', 'dlk.harga_konsinyasi as harga', 'dlk.qty_terjual as kuantitas',
                'dlk.subtotal', \DB::raw('0 as diskon'), 'dlk.subtotal as total', 'lk.no_laporan as ref',
                'lk.tanggal_laporan as tanggal', 'm.namaMitra as pelanggan_mitra',
                \DB::raw("CASE WHEN tk.total_terbayar >= tk.total_tagihan THEN 'LUNAS' ELSE 'BELUM LUNAS' END as status_pembayaran"),
                \DB::raw("'Konsinyasi' as jenis")
            ]);

        $nonKonsinyasi = \DB::table('detail_penjualan_non_konsinyasi as dpnk')
            ->join('penjualan_non_konsinyasi as pnk', 'dpnk.penjualan_id', '=', 'pnk.id')
            ->join('barang as b', 'dpnk.barang_id', '=', 'b.id')
            ->join('kategori as k', 'b.kategori_id', '=', 'k.id')
            ->join('pelanggan as p', 'pnk.pelanggan_id', '=', 'p.id')
            ->whereBetween('pnk.tanggal', [$tanggalDari, $tanggalSampai])
            ->select([
                'b.nama_barang', 'k.nama_kategori', 'dpnk.harga', 'dpnk.qty as kuantitas',
                \DB::raw('(dpnk.harga * dpnk.qty) as subtotal'), 'dpnk.diskon', 'dpnk.subtotal as total',
                'pnk.no_invoice as ref', 'pnk.tanggal as tanggal', 'p.namaPelanggan as pelanggan_mitra',
                \DB::raw("CASE WHEN pnk.total_terbayar >= pnk.total THEN 'LUNAS' ELSE 'BELUM LUNAS' END as status_pembayaran"),
                \DB::raw("'Non Konsinyasi' as jenis")
            ]);

        $query = $konsinyasi->unionAll($nonKonsinyasi);

        if ($status !== 'semua') {
            $statusFilter = $status === 'lunas' ? 'LUNAS' : 'BELUM LUNAS';
            $query = \DB::table(\DB::raw("({$query->toSql()}) as combined"))
                ->mergeBindings($query)
                ->where('status_pembayaran', $statusFilter)
                ->select('*');
        }

        return \DB::table(\DB::raw("({$query->toSql()}) as final"))
            ->mergeBindings($query)
            ->orderBy('tanggal', 'desc')
            ->orderBy('ref', 'desc')
            ->get();
    }

    public function getTotalSubtotal(): int
    {
        return $this->laporanData->sum('subtotal');
    }

    public function getTotalDiskon(): int
    {
        return $this->laporanData->sum('diskon');
    }

    public function getTotalAkhir(): int
    {
        return $this->laporanData->sum('total');
    }

    public function applyFilter(): void
    {
        // Method ini dipanggil dari view
    }

    public function resetFilter(): void
    {
        $this->tanggalDari = now()->startOfMonth()->format('Y-m-d');
        $this->tanggalSampai = now()->format('Y-m-d');
        $this->status = 'semua';
    }
}

