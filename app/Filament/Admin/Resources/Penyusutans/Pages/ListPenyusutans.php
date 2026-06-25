<?php

namespace App\Filament\Admin\Resources\Penyusutans\Pages;

use App\Filament\Admin\Resources\Penyusutans\PenyusutanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Tables\Enums\FiltersLayout;
use App\Models\Aset;
use App\Models\Penyusutan;

class ListPenyusutans extends ListRecords
{
    protected static string $resource = PenyusutanResource::class;

    public $selectedAset = null;
    public $daftarAset = [];
    public $penyusutans = [];
    public $showYearlyModal = false;
    public $yearlyData = [];
    public $selectedAsetForYearly = null;

    protected $listeners = ['openYearlyModal'];

    public function mount(): void
    {
        parent::mount();
        
        // Load daftar aset
        $this->daftarAset = Aset::orderBy('nama_aset')->get();
    }

    public function updatedSelectedAset($value)
    {
        if ($value) {
            $this->penyusutans = Penyusutan::with('aset.kategori_aset')
                ->where('aset_id', $value)
                ->orderBy('periode', 'asc')
                ->get();
        } else {
            $this->penyusutans = [];
        }
    }

    public function openYearlyModal($asetId)
    {
        $this->selectedAsetForYearly = Aset::with('kategori_aset')->find($asetId);
        
        if ($this->selectedAsetForYearly) {
            $aset = $this->selectedAsetForYearly;
            
            // Hitung penyusutan per bulan
            $nilaiDisusutkan = $aset->nilai_perolehan - ($aset->nilai_residu ?? 0);
            $masaManfaatBulan = $aset->masa_manfaat * 12;
            $penyusutanPerBulan = $nilaiDisusutkan / $masaManfaatBulan;
            
            // Ambil data penyusutan dan group by tahun
            $penyusutans = Penyusutan::where('aset_id', $asetId)
                ->orderBy('periode', 'asc')
                ->get();
            
            $yearlyData = [];
            $akumulasiSebelumnya = 0;
            $totalBulanSebelumnya = 0;
            
            foreach ($penyusutans as $p) {
                $year = \Carbon\Carbon::parse($p->periode)->format('Y');
                
                if (!isset($yearlyData[$year])) {
                    $yearlyData[$year] = [
                        'year' => $year,
                        'bulan_count' => 0,
                        'penyusutan_tahun' => 0,
                        'akumulasi_akhir' => 0,
                        'nilai_buku_akhir' => 0,
                    ];
                }
                
                $yearlyData[$year]['bulan_count']++;
            }
            
            // Hitung penyusutan per tahun berdasarkan jumlah bulan
            // Hanya tampilkan sampai masa manfaat habis
            foreach ($yearlyData as $year => &$data) {
                // Cek apakah bulan di tahun ini melebihi masa manfaat
                $bulanDiTahunIni = $data['bulan_count'];
                
                // Jika total bulan sebelumnya + bulan tahun ini melebihi masa manfaat
                if ($totalBulanSebelumnya + $bulanDiTahunIni > $masaManfaatBulan) {
                    // Hitung sisa bulan yang valid
                    $sisaBulan = $masaManfaatBulan - $totalBulanSebelumnya;
                    
                    if ($sisaBulan <= 0) {
                        // Hapus tahun ini karena sudah melebihi masa manfaat
                        unset($yearlyData[$year]);
                        continue;
                    }
                    
                    // Update jumlah bulan menjadi sisa bulan saja
                    $data['bulan_count'] = $sisaBulan;
                    $bulanDiTahunIni = $sisaBulan;
                }
                
                $data['penyusutan_tahun'] = $penyusutanPerBulan * $bulanDiTahunIni;
                $data['akumulasi_akhir'] = $akumulasiSebelumnya + $data['penyusutan_tahun'];
                $data['nilai_buku_akhir'] = $aset->nilai_perolehan - $data['akumulasi_akhir'];
                
                // Pastikan nilai buku tidak kurang dari nilai residu
                if ($data['nilai_buku_akhir'] < ($aset->nilai_residu ?? 0)) {
                    $data['nilai_buku_akhir'] = $aset->nilai_residu ?? 0;
                }
                
                $akumulasiSebelumnya = $data['akumulasi_akhir'];
                $totalBulanSebelumnya += $bulanDiTahunIni;
                
                // Jika sudah mencapai masa manfaat, stop
                if ($totalBulanSebelumnya >= $masaManfaatBulan) {
                    break;
                }
            }
            
            $this->yearlyData = array_values($yearlyData);
            $this->showYearlyModal = true;
        }
    }

    public function closeYearlyModal()
    {
        $this->showYearlyModal = false;
        $this->yearlyData = [];
        $this->selectedAsetForYearly = null;
    }

    // Sembunyikan tabel resource karena pakai custom view
    public function getView(): string
    {
        return 'filament.admin.resources.penyusutans.pages.list-penyusutans';
    }
}
