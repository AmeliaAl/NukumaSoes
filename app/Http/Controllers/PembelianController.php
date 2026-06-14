<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\BahanBaku;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PembelianController extends Controller
{
    public function index()
    {
        $pembelians = Pembelian::all();

        return view('pembelian.index', compact('pembelians'));
    }

    public function create()
    {
        $suppliers = Supplier::all();

        $bahanBakus = BahanBaku::all();

        $coas = Coa::where('tipe_akun', 'Aktiva Lancar')
                    ->orWhere('tipe_akun', 'Kewajiban Lancar')
                    ->get();

        return view('pembelian.create', compact(
            'suppliers',
            'bahanBakus',
            'coas'
        ));
    }

    public function store(Request $request)
    {
        $request->merge([
            'diskon' => preg_replace('/\D/', '', $request->input('diskon')),
            'ongkir' => preg_replace('/\D/', '', $request->input('ongkir')),
        ]);

        $request->validate([
            'tanggal' => 'required|date',
            'nomor_permintaan' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'coa_id' => 'required|exists:coas,id',
            'details' => 'required|array|min:1',
            'details.*.bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.harga' => 'required|string',
            'diskon' => 'nullable|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
        ]);

        // extra validation: ensure selected coa is allowed as payment account
        $coa = Coa::find($request->coa_id);
        if (!$coa || !in_array($coa->tipe_akun, ['Aktiva Lancar', 'Kewajiban Lancar'])) {
            return back()->withErrors(['coa_id' => 'Akun pembayaran tidak valid atau tidak diizinkan.'])->withInput();
        }

        $diskon = (float) $request->input('diskon');
        $ongkir = (float) $request->input('ongkir');
        $subtotal = 0;

        $parsedDetails = [];
        foreach ($request->details as $detail) {
            $harga = (float) preg_replace('/\D/', '', $detail['harga']);
            $detailSubtotal = $detail['qty'] * $harga;
            $subtotal += $detailSubtotal;

            $parsedDetails[] = [
                'bahan_baku_id' => $detail['bahan_baku_id'],
                'qty' => $detail['qty'],
                'harga' => $harga,
                'subtotal' => $detailSubtotal,
            ];
        }

        $totalBersih = $subtotal - $diskon;
        $grandTotal = $totalBersih + $ongkir;

        $nomorPermintaan = $request->nomor_permintaan ?: null;

        $firstDetail = $parsedDetails[0];

        $pembelian = Pembelian::create([
            'no_pembelian' => 'PB-TMP',
            'tanggal' => $request->tanggal,
            'nomor_permintaan' => $nomorPermintaan,
            'supplier_id' => $request->supplier_id,
            'bahan_baku_id' => $firstDetail['bahan_baku_id'],
            'qty' => $firstDetail['qty'],
            'harga' => $firstDetail['harga'],
            'total' => $firstDetail['subtotal'],
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'ongkir' => $ongkir,
            'total_bersih' => $totalBersih,
            'grand_total' => $grandTotal,
            'coa_id' => $request->coa_id,
        ]);

        $pembelian->update([
            'no_pembelian' => 'PB-' . str_pad($pembelian->id, 3, '0', STR_PAD_LEFT),
            'nomor_permintaan' => $nomorPermintaan ?: 'PR-' . str_pad($pembelian->id, 3, '0', STR_PAD_LEFT),
        ]);

        foreach ($parsedDetails as $pd) {
            $pembelian->details()->create($pd);
        }

        return redirect()->route('pembelian.index');
    }

    public function edit(Pembelian $pembelian)
    {
        $pembelian->load('details');
        $suppliers = Supplier::all();

        $bahanBakus = BahanBaku::all();

        $coas = Coa::where('tipe_akun', 'Aktiva Lancar')
                    ->orWhere('tipe_akun', 'Kewajiban Lancar')
                    ->get();

        return view('pembelian.edit', compact(
            'pembelian',
            'suppliers',
            'bahanBakus',
            'coas'
        ));
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        $request->merge([
            'diskon' => preg_replace('/\D/', '', $request->input('diskon')),
            'ongkir' => preg_replace('/\D/', '', $request->input('ongkir')),
        ]);

        $request->validate([
            'tanggal' => 'required|date',
            'nomor_permintaan' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'coa_id' => 'required|exists:coas,id',
            'details' => 'required|array|min:1',
            'details.*.bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.harga' => 'required|string',
            'diskon' => 'nullable|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
        ]);

        // extra validation: ensure selected coa is allowed as payment account
        $coa = Coa::find($request->coa_id);
        if (!$coa || !in_array($coa->tipe_akun, ['Aktiva Lancar', 'Kewajiban Lancar'])) {
            return back()->withErrors(['coa_id' => 'Akun pembayaran tidak valid atau tidak diizinkan.'])->withInput();
        }

        $diskon = (float) $request->input('diskon');
        $ongkir = (float) $request->input('ongkir');
        $subtotal = 0;

        $parsedDetails = [];
        foreach ($request->details as $detail) {
            $harga = (float) preg_replace('/\D/', '', $detail['harga']);
            $detailSubtotal = $detail['qty'] * $harga;
            $subtotal += $detailSubtotal;

            $parsedDetails[] = [
                'bahan_baku_id' => $detail['bahan_baku_id'],
                'qty' => $detail['qty'],
                'harga' => $harga,
                'subtotal' => $detailSubtotal,
            ];
        }

        $totalBersih = $subtotal - $diskon;
        $grandTotal = $totalBersih + $ongkir;

        $firstDetail = $parsedDetails[0];

        $pembelian->update([
            'tanggal' => $request->tanggal,
            'nomor_permintaan' => $request->nomor_permintaan ?: $pembelian->nomor_permintaan,
            'supplier_id' => $request->supplier_id,
            'bahan_baku_id' => $firstDetail['bahan_baku_id'],
            'qty' => $firstDetail['qty'],
            'harga' => $firstDetail['harga'],
            'total' => $firstDetail['subtotal'],
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'ongkir' => $ongkir,
            'total_bersih' => $totalBersih,
            'grand_total' => $grandTotal,
            'coa_id' => $request->coa_id,
        ]);

        // Recreate details
        $pembelian->details()->delete();
        foreach ($parsedDetails as $pd) {
            $pembelian->details()->create($pd);
        }

        return redirect()->route('pembelian.index');
    }

    public function destroy(Pembelian $pembelian)
    {
        $pembelian->delete();

        return redirect('/pembelian');
    }
}