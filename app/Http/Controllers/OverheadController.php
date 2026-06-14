<?php

namespace App\Http\Controllers;

use App\Models\Overhead;
use App\Models\Coa;
use Illuminate\Http\Request;

class OverheadController extends Controller
{
    public function index()
    {
        $overheads = Overhead::all();

        return view('overhead.index', compact('overheads'));
    }

    public function create()
    {
        $coas = Coa::where('tipe_akun', 'Overhead Produksi')
                    ->orWhere('tipe_akun', 'Biaya Beban Operasional Umum')
                    ->get();

        $paymentCoas = Coa::where('tipe_akun', 'Aktiva Lancar')
                    ->orWhere('tipe_akun', 'Kewajiban Lancar')
                    ->get();

        return view('overhead.create', compact('coas', 'paymentCoas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'coa_id' => 'required|exists:coas,id',
            'coa_pembayaran_id' => 'required|exists:coas,id',
            'keterangan' => 'required|string',
            'nominal' => 'required|string',
        ]);

        Overhead::create([
            'tanggal' => $request->tanggal,
            'coa_id' => $request->coa_id,
            'coa_pembayaran_id' => $request->coa_pembayaran_id,
            'keterangan' => $request->keterangan,
            'nominal' => str_replace('.', '', $request->nominal),
        ]);

        return redirect('/overhead');
    }

    public function edit(Overhead $overhead)
    {
        $coas = Coa::where('tipe_akun', 'Overhead Produksi')
                    ->orWhere('tipe_akun', 'Biaya Beban Operasional Umum')
                    ->get();

        $paymentCoas = Coa::where('tipe_akun', 'Aktiva Lancar')
                    ->orWhere('tipe_akun', 'Kewajiban Lancar')
                    ->get();

        return view('overhead.edit', compact(
            'overhead',
            'coas',
            'paymentCoas'
        ));
    }

    public function update(Request $request, Overhead $overhead)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'coa_id' => 'required|exists:coas,id',
            'coa_pembayaran_id' => 'required|exists:coas,id',
            'keterangan' => 'required|string',
            'nominal' => 'required|string',
        ]);

        $overhead->update([
            'tanggal' => $request->tanggal,
            'coa_id' => $request->coa_id,
            'coa_pembayaran_id' => $request->coa_pembayaran_id,
            'keterangan' => $request->keterangan,
            'nominal' => str_replace('.', '', $request->nominal),
        ]);

        return redirect('/overhead');
    }

    public function destroy(Overhead $overhead)
    {
        $overhead->delete();

        return redirect('/overhead');
    }
}