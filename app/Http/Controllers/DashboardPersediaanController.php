<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\PersediaanEntry;
use App\Models\ProdukKeluarEntry;
use Carbon\Carbon;

class DashboardPersediaanController extends Controller
{
    public function index(Request $request)
    {
        // Date Period Filtering
        $periode = $request->get('periode', date('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $periode)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $periode)->endOfMonth();

        $produkAman = Inventory::where('status', 'Aman')->sum('jumlah');
        $produkAkanExpired = Inventory::where('status', 'Hampir Expired')->sum('jumlah');
        $produkExpired = Inventory::where('status', 'Expired')->sum('jumlah');
        
        $totalProduk = $produkAman + $produkAkanExpired + $produkExpired;
        
        $masukTotal = PersediaanEntry::whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->count();
        $keluarTotal = ProdukKeluarEntry::whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->count();
        $totalTransaksi = $masukTotal + $keluarTotal;

        // Chart Data for Pie Chart
        $statusChartData = [
            'labels' => ['Aman', 'Hampir Expired', 'Expired'],
            'data' => [$produkAman, $produkAkanExpired, $produkExpired],
        ];

        // Daily Transaction Data for Line Chart
        $labels = [];
        $masukData = [];
        $keluarData = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayStr = $date->format('Y-m-d');
            $labels[] = $date->format('d');
            $masukData[] = PersediaanEntry::whereDate('tanggal', $dayStr)->count();
            $keluarData[] = ProdukKeluarEntry::whereDate('tanggal', $dayStr)->count();
        }

        $transactionChartData = [
            'labels' => $labels,
            'masuk' => $masukData,
            'keluar' => $keluarData,
        ];

        return view('dashboard', compact(
            'totalProduk',
            'produkAman',
            'produkAkanExpired',
            'produkExpired',
            'totalTransaksi',
            'statusChartData',
            'transactionChartData',
            'startDate'
        ));
    }
}
