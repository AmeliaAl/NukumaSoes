<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Overhead;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | FILTER TAHUN
        |--------------------------------------------------------------------------
        */

        $tahun = $request->tahun ?? date('Y');

        /*
        |--------------------------------------------------------------------------
        | CARD DASHBOARD
        |--------------------------------------------------------------------------
        */

        $totalPembelian = Pembelian::whereYear('tanggal', $tahun)
            ->sum('total');

        $totalOverhead = Overhead::whereYear('tanggal', $tahun)
            ->sum('nominal');

        $totalTransaksi =
            Pembelian::whereYear('tanggal', $tahun)->count()
            +
            Overhead::whereYear('tanggal', $tahun)->count();

        $jumlahSupplier = Supplier::count();

        /*
        |--------------------------------------------------------------------------
        | GRAFIK PEMBELIAN PER BULAN
        |--------------------------------------------------------------------------
        */

        $grafikPembelian = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {

            $total = Pembelian::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->sum('total');

            $grafikPembelian[] = $total;
        }

        $bulanLabel = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Ags',
            'Sep',
            'Okt',
            'Nov',
            'Des'
        ];

        return view('dashboard', compact(
            'tahun',
            'totalPembelian',
            'totalOverhead',
            'totalTransaksi',
            'jumlahSupplier',
            'grafikPembelian',
            'bulanLabel'
        ));
    }
}