<?php

namespace App\Filament\Admin\Resources\Jurnals\Widgets;
use Filament\Widgets\Widget;
use App\Filament\Admin\Resources\Jurnals\JurnalResource;
use App\Models\Jurnal;

class JurnalTableOverview extends Widget
{
    protected string $view = 'filament.admin.widgets.jurnal-table-overview';
    protected static string $resource = JurnalResource::class;

    // tambahan
    protected int | string | array $columnSpan = 'full';
    
    public $periode; // properti untuk menyimpan periode

    protected $listeners = ['filterUpdated' => 'getViewData'];

    public function mount(): void
    {
        $this->periode = request('periode', now()->format('Y-m')); 
    }

    public function filterJurnal(): void
    {
        
    }

    // // public function getData(): array
    public function getViewData(): array
    {
        
        // dd($periode);
        $jurnalsQuery = Jurnal::with('jurnaldetail.akun');

        if ($this->periode) {
            [$year, $month] = explode('-', $this->periode);
            $jurnalsQuery->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
        }

        $jurnals = $jurnalsQuery
        ->orderBy('tanggal', 'asc')
        ->orderBy('id', 'asc')    
        ->get();

        return [
            'jurnals' => $jurnals,
            'periode' => $this->periode,
        ];
    }

}
