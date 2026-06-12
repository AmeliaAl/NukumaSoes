<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\asetLancar;
use App\Models\Persediaan;
use App\Models\PemakaianPersediaan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;


class LaporanStokPage extends Page
{
    protected static ?string $navigationLabel = 'Laporan Stok Bahan Habis Pakai';
    protected static ?string $title = 'Laporan Stok Bahan Habis Pakai';
    protected  string $view = 'filament.admin.pages.laporan-stok-page';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 304;

    public array $laporanStok = [];
    public string $periode = '';
    public string $tanggal_awal = '';
    public string $tanggal_akhir = '';

    /**
     * Check if current user can access this page
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin dan Pemilik bisa akses Laporan Stok
        return $user->isAdmin() || $user->isPemilik();
    }

    public function mount(): void
    {
        $this->periode = now()->format('Y-m');
        $this->tanggal_awal = now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_akhir = now()->endOfMonth()->format('Y-m-d');
        
        $this->hitungLaporanStok();
    }

    public function updatedPeriode(): void
    {
        $this->tanggal_awal = \Carbon\Carbon::createFromFormat('Y-m', $this->periode)
            ->startOfMonth()
            ->format('Y-m-d');

        $this->tanggal_akhir = \Carbon\Carbon::createFromFormat('Y-m', $this->periode)
            ->endOfMonth()
            ->format('Y-m-d');

        $this->hitungLaporanStok();
    }

    protected function hitungLaporanStok(): void
    {
        $this->laporanStok = [];

        // Ambil semua aset lancar (bahan habis pakai)
        $asetLancars = asetLancar::with('kategoriAset')
            ->orderBy('nama_barang')
            ->get();

        foreach ($asetLancars as $aset) {
            // Hitung total masuk dari persediaan sampai akhir periode
            $totalMasuk = Persediaan::where('nama_barang', $aset->nama_barang)
                ->where('tanggal_masuk', '<=', $this->tanggal_akhir)
                ->sum('qty');

            // Hitung total keluar dari pemakaian sampai akhir periode
            $totalKeluar = PemakaianPersediaan::where('aset_lancar_id', $aset->id)
                ->where('tanggal', '<=', $this->tanggal_akhir)
                ->sum('jumlah');

            // Hitung stok sisa
            $stokSisa = $totalMasuk - $totalKeluar;

            // Ambil satuan dari aset_lancar
            $satuan = $aset->satuan ?? 'Unit';

            $this->laporanStok[] = [
                'id' => $aset->id,
                'nama_barang' => $aset->nama_barang,
                'satuan' => $satuan,
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'stok_sisa' => $stokSisa,
                'kategori' => $aset->kategoriAset->nama_kategori ?? '-',
            ];
        }

        // Sort by nama_barang
        usort($this->laporanStok, function($a, $b) {
            return strcmp($a['nama_barang'], $b['nama_barang']);
        });
    }

    public function exportExcel()
    {
        // TODO: Implement Excel export
        $this->notify('info', 'Fitur export Excel akan segera tersedia');
    }

    public function printLaporan()
    {
        // Trigger print via JavaScript
        $this->dispatch('print-laporan');
    }
}

