<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\BahanBaku;
use App\Models\Akun;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::with(['details.bahanBaku', 'supplier', 'coa', 'paymentProofs']);

        // Filter Nomor Permintaan
        if ($request->filter_nomor_permintaan) {
            $query->where('nomor_permintaan', 'like', '%' . $request->filter_nomor_permintaan . '%');
        }

        // Filter Status Permintaan
        if ($request->filter_status) {
            $query->where('status_permintaan', $request->filter_status);
        }

        $pembelians = $query->get();

        return view('pembelian.index', compact('pembelians'));
    }

    public function create()
    {
        $suppliers  = Supplier::all();
        $bahanBakus = BahanBaku::all();
        $coas       = Akun::where('header_akun', 'Aktiva Lancar')->get();

        return view('pembelian.create', compact('suppliers', 'bahanBakus', 'coas'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'diskon' => preg_replace('/\D/', '', $request->input('diskon')),
            'ongkir' => preg_replace('/\D/', '', $request->input('ongkir')),
        ]);

        $details = $request->input('details', []);
        foreach ($details as $i => $detail) {
            if (isset($detail['harga'])) {
                $details[$i]['harga'] = preg_replace('/\D/', '', $detail['harga']);
            }
        }
        $request->merge(['details' => $details]);

        $request->validate([
            'tanggal'                    => 'required|date',
            'nomor_permintaan'           => 'nullable|string|max:255',
            'supplier_id'               => 'required|exists:suppliers,id',
            'coa_id'                    => 'required|exists:akun,id',
            'details'                   => 'required|array|min:1',
            'details.*.bahan_baku_id'   => 'required|exists:bahan_baku,id',
            'details.*.qty'             => 'required|integer|min:1',
            'details.*.isi_per_kemasan' => 'nullable|string|max:255',
            'details.*.harga'           => 'required|numeric|min:0',
            'diskon'                    => 'nullable|numeric|min:0',
            'ongkir'                    => 'nullable|numeric|min:0',
            'payment_proofs'            => 'nullable|array|max:5',
            'payment_proofs.*'          => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $coa = Akun::find($request->coa_id);
        if (!$coa || $coa->header_akun !== 'Aktiva Lancar') {
            return back()->withErrors(['coa_id' => 'Akun pembayaran tidak valid. Pilih Kas Kecil atau Bank.'])->withInput();
        }

        $diskon  = (float) $request->input('diskon');
        $ongkir  = (float) $request->input('ongkir');
        $subtotal = 0;

        $parsedDetails = [];
        foreach ($request->details as $detail) {
            $harga         = (float) preg_replace('/\D/', '', $detail['harga']);
            $detailSubtotal = $detail['qty'] * $harga;
            $subtotal      += $detailSubtotal;

            $parsedDetails[] = [
                'bahan_baku_id'   => $detail['bahan_baku_id'],
                'qty'             => $detail['qty'],
                'isi_per_kemasan' => $detail['isi_per_kemasan'] ?? null,
                'harga'           => $harga,
                'subtotal'        => $detailSubtotal,
            ];
        }

        $totalBersih     = $subtotal - $diskon;
        $grandTotal      = $totalBersih + $ongkir;
        $nomorPermintaan = $request->nomor_permintaan ?: null;
        $firstDetail     = $parsedDetails[0];

        $pembelian = Pembelian::create([
            'no_pembelian'      => 'PB-TMP',
            'tanggal'           => $request->tanggal,
            'nomor_permintaan'  => $nomorPermintaan,
            'supplier_id'       => $request->supplier_id,
            'bahan_baku_id'     => $firstDetail['bahan_baku_id'],
            'qty'               => $firstDetail['qty'],
            'harga'             => $firstDetail['harga'],
            'total'             => $firstDetail['subtotal'],
            'subtotal'          => $subtotal,
            'diskon'            => $diskon,
            'ongkir'            => $ongkir,
            'total_bersih'      => $totalBersih,
            'grand_total'       => $grandTotal,
            'coa_id'            => $request->coa_id,
            // Placeholder integrasi Produksi — diisi manual untuk saat ini
            'status_permintaan' => $request->status_permintaan ?: null,
        ]);

        $pembelian->update([
            'no_pembelian'     => 'PB-' . str_pad($pembelian->id, 3, '0', STR_PAD_LEFT),
            'nomor_permintaan' => $nomorPermintaan ?: 'PR-' . str_pad($pembelian->id, 3, '0', STR_PAD_LEFT),
        ]);

        foreach ($parsedDetails as $pd) {
            $pembelian->details()->create($pd);
        }

        if ($request->hasFile('payment_proofs')) {
            foreach ($request->file('payment_proofs') as $file) {
                $path = $file->store('payment_proofs', 'public');
                $pembelian->paymentProofs()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        // ── Posting jurnal otomatis ────────────────────────────────────────
        \App\Services\JurnalService::jurnalPembelian(
            $pembelian->tanggal,
            $pembelian->no_pembelian,
            $subtotal, $diskon, $ongkir, $grandTotal,
            $coa->no_akun ?? $coa->kode_akun,
            $coa->nama_akun
        );

        return redirect()->route('pembelian.index');
    }

    public function edit(Pembelian $pembelian)
    {
        $pembelian->load(['details', 'paymentProofs']);
        $suppliers  = Supplier::all();
        $bahanBakus = BahanBaku::all();
        $coas       = Akun::where('header_akun', 'Aktiva Lancar')->get();

        return view('pembelian.edit', compact('pembelian', 'suppliers', 'bahanBakus', 'coas'));
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        $request->merge([
            'diskon' => preg_replace('/\D/', '', $request->input('diskon')),
            'ongkir' => preg_replace('/\D/', '', $request->input('ongkir')),
        ]);

        $details = $request->input('details', []);
        foreach ($details as $i => $detail) {
            if (isset($detail['harga'])) {
                $details[$i]['harga'] = preg_replace('/\D/', '', $detail['harga']);
            }
        }
        $request->merge(['details' => $details]);

        $request->validate([
            'tanggal'                    => 'required|date',
            'nomor_permintaan'           => 'nullable|string|max:255',
            'supplier_id'               => 'required|exists:suppliers,id',
            'coa_id'                    => 'required|exists:akun,id',
            'details'                   => 'required|array|min:1',
            'details.*.bahan_baku_id'   => 'required|exists:bahan_baku,id',
            'details.*.qty'             => 'required|integer|min:1',
            'details.*.isi_per_kemasan' => 'nullable|string|max:255',
            'details.*.harga'           => 'required|numeric|min:0',
            'diskon'                    => 'nullable|numeric|min:0',
            'ongkir'                    => 'nullable|numeric|min:0',
            'payment_proofs'            => 'nullable|array|max:5',
            'payment_proofs.*'          => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $existingCount = $pembelian->paymentProofs()->count();
        $newCount      = $request->hasFile('payment_proofs') ? count($request->file('payment_proofs')) : 0;
        if ($existingCount + $newCount > 5) {
            return back()->withErrors(['payment_proofs' => 'Maksimal 5 berkas bukti pembayaran per transaksi.'])->withInput();
        }

        $coa = Akun::find($request->coa_id);
        if (!$coa || $coa->header_akun !== 'Aktiva Lancar') {
            return back()->withErrors(['coa_id' => 'Akun pembayaran tidak valid. Pilih Kas Kecil atau Bank.'])->withInput();
        }

        $diskon  = (float) $request->input('diskon');
        $ongkir  = (float) $request->input('ongkir');
        $subtotal = 0;

        $parsedDetails = [];
        foreach ($request->details as $detail) {
            $harga         = (float) preg_replace('/\D/', '', $detail['harga']);
            $detailSubtotal = $detail['qty'] * $harga;
            $subtotal      += $detailSubtotal;

            $parsedDetails[] = [
                'bahan_baku_id'   => $detail['bahan_baku_id'],
                'qty'             => $detail['qty'],
                'isi_per_kemasan' => $detail['isi_per_kemasan'] ?? null,
                'harga'           => $harga,
                'subtotal'        => $detailSubtotal,
            ];
        }

        $totalBersih = $subtotal - $diskon;
        $grandTotal  = $totalBersih + $ongkir;
        $firstDetail = $parsedDetails[0];

        $pembelian->update([
            'tanggal'           => $request->tanggal,
            'nomor_permintaan'  => $request->nomor_permintaan ?: $pembelian->nomor_permintaan,
            'supplier_id'       => $request->supplier_id,
            'bahan_baku_id'     => $firstDetail['bahan_baku_id'],
            'qty'               => $firstDetail['qty'],
            'harga'             => $firstDetail['harga'],
            'total'             => $firstDetail['subtotal'],
            'subtotal'          => $subtotal,
            'diskon'            => $diskon,
            'ongkir'            => $ongkir,
            'total_bersih'      => $totalBersih,
            'grand_total'       => $grandTotal,
            'coa_id'            => $request->coa_id,
            // Placeholder integrasi Produksi
            'status_permintaan' => $request->status_permintaan ?: null,
        ]);

        $pembelian->details()->delete();
        foreach ($parsedDetails as $pd) {
            $pembelian->details()->create($pd);
        }

        if ($request->hasFile('payment_proofs')) {
            foreach ($request->file('payment_proofs') as $file) {
                $path = $file->store('payment_proofs', 'public');
                $pembelian->paymentProofs()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('pembelian.index');
    }

    public function destroy(Pembelian $pembelian)
    {
        foreach ($pembelian->paymentProofs as $proof) {
            Storage::disk('public')->delete($proof->file_path);
        }
        $pembelian->paymentProofs()->delete();
        $pembelian->delete();

        return redirect('/pembelian');
    }

    public function deleteProof($id)
    {
        $proof = PaymentProof::findOrFail($id);
        Storage::disk('public')->delete($proof->file_path);
        $proof->delete();

        return back()->with('success', 'Bukti pembayaran berhasil dihapus.');
    }
}
