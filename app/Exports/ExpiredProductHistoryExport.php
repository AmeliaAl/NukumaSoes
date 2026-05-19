<?php
// c:\laragon\www\tugasakhir\app\Exports\ExpiredProductHistoryExport.php

namespace App\Exports;

use App\Models\ExpiredProductHistory;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExpiredProductHistoryExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        return view('monitoring.expired-history-excel', [
            'histories' => ExpiredProductHistory::orderBy('created_at', 'desc')->get()
        ]);
    }
}
