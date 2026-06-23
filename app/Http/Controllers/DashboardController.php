<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
        public function index()
    {
        $startDate = Carbon::now();

        $totalProduk = 0;
        $produkAman = 0;
        $produkAkanExpired = 0;
        $produkExpired = 0;
        $totalTransaksi = 0;

        $statusChartData = [
            'labels' => ['Aman', 'Warning', 'Expired'],
            'data' => [0, 0, 0],
        ];

        $transactionChartData = [
            'labels' => [],
            'masuk' => [],
            'keluar' => [],
        ];

        return view('dashboard', compact(
            'startDate',
            'totalProduk',
            'produkAman',
            'produkAkanExpired',
            'produkExpired',
            'totalTransaksi',
            'statusChartData',
            'transactionChartData'
        ));
    }
}
