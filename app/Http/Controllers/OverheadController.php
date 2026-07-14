<?php

namespace App\Http\Controllers;

use App\Models\Overhead;
use App\Models\OverheadDetail;
use App\Models\Akun;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OverheadController extends Controller
{
    public function index()
    {
        $overheads = Overhead::with(['details.coa', 'details.paymentCoa', 'paymentProofs'])->get();

        return view('overhead.index', compact('overheads'));
    }

    public function create()
    {
        $coas = Akun::whereIn('header_akun', ['Overhead Produksi', 'Biaya Beban Operasional Umum'])
                    ->get();

        $paymentCoas = Akun::where('header_akun', 'Aktiva Lancar')->get();

        return view('overhead.create', compact('coas', 'paymentCoas'));
    }

    public function store(Request $request)
    {
        // ── Sanitize nominal sebelum validasi ──────────────────────────────
        $details = $request->input('details', []);
        foreach ($details as $i => $detail) {
            if (isset($detail['nominal'])) {
                $details[$i]['nominal'] = preg_replace('/\D/', '', $detail['nominal']);
            }
        }
        $request->merge(['details' => $details]);

        // ── Validasi umum + per jenis periode ─────────────────────────────
        $rules = [
            'tanggal'       => 'required|date',
            'jenis_periode' => 'required|in:harian,mingguan,bulanan',
            'periode_mulai' => 'required|date',
            'details'       => 'required|array|min:1',
            'details.*.coa_id'            => 'required|exists:akun,id',
            'details.*.coa_pembayaran_id' => 'required|exists:akun,id',
            'details.*.keterangan'        => 'required|string|max:500',
            'details.*.nominal'           => 'required|integer|min:1',
            'payment_proofs'   => 'nullable|array|max:5',
            'payment_proofs.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        if ($request->jenis_periode === 'mingguan') {
            $rules['periode_akhir'] = 'required|date|after_or_equal:periode_mulai';
        }

        $messages = [
            'tanggal.required'                       => 'Tanggal wajib diisi.',
            'tanggal.date'                           => 'Tanggal tidak valid.',
            'jenis_periode.required'                 => 'Jenis periode harus dipilih.',
            'jenis_periode.in'                       => 'Jenis periode tidak valid.',
            'periode_mulai.required'                 => 'Silakan pilih bulan dan tahun untuk periode bulanan, atau isi tanggal pembebanan.',
            'periode_mulai.date'                     => 'Tanggal mulai periode tidak valid.',
            'periode_akhir.required'                 => 'Tanggal akhir wajib diisi untuk periode mingguan.',
            'periode_akhir.date'                     => 'Tanggal akhir tidak valid.',
            'periode_akhir.after_or_equal'           => 'Tanggal akhir tidak boleh sebelum tanggal mulai.',
            'details.required'                       => 'Minimal satu item detail harus diisi.',
            'details.*.coa_id.required'              => 'Akun overhead wajib dipilih.',
            'details.*.coa_id.exists'                => 'Akun overhead tidak valid.',
            'details.*.coa_pembayaran_id.required'   => 'Akun pembayaran wajib dipilih.',
            'details.*.coa_pembayaran_id.exists'     => 'Akun pembayaran tidak valid.',
            'details.*.keterangan.required'          => 'Keterangan wajib diisi.',
            'details.*.nominal.required'             => 'Nominal wajib diisi.',
            'details.*.nominal.integer'              => 'Nominal harus berupa angka.',
            'details.*.nominal.min'                  => 'Nominal harus lebih besar dari 0.',
            'payment_proofs.max'                     => 'Maksimal 5 file bukti pembayaran.',
            'payment_proofs.*.file'                  => 'File tidak valid.',
            'payment_proofs.*.mimes'                 => 'File harus berformat JPG, JPEG, PNG, atau PDF.',
            'payment_proofs.*.max'                   => 'Ukuran file maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        // ── Validasi bisnis: akun pembayaran harus Aktiva Lancar ──────────
        foreach ($request->details as $idx => $detail) {
            $paymentCoa = Akun::find($detail['coa_pembayaran_id']);
            if (!$paymentCoa || $paymentCoa->header_akun !== 'Aktiva Lancar') {
                return back()->withInput()->withErrors([
                    "details.{$idx}.coa_pembayaran_id" =>
                        'Akun pembayaran hanya boleh menggunakan akun Kas atau Bank.',
                ]);
            }
        }

        // ── Simpan ────────────────────────────────────────────────────────
        $parsedDetails = [];
        foreach ($request->details as $detail) {
            $parsedDetails[] = [
                'coa_id'            => $detail['coa_id'],
                'coa_pembayaran_id' => $detail['coa_pembayaran_id'],
                'keterangan'        => $detail['keterangan'],
                'nominal'           => (int) $detail['nominal'],
            ];
        }

        $firstDetail = $parsedDetails[0];

        $overhead = Overhead::create([
            'tanggal'           => $request->tanggal,
            'jenis_periode'     => $request->jenis_periode,
            'periode_mulai'     => $request->periode_mulai,
            'periode_akhir'     => $request->jenis_periode === 'harian' ? null : $request->periode_akhir,
            'coa_id'            => $firstDetail['coa_id'],
            'coa_pembayaran_id' => $firstDetail['coa_pembayaran_id'],
            'keterangan'        => $firstDetail['keterangan'],
            'nominal'           => $firstDetail['nominal'],
        ]);

        foreach ($parsedDetails as $pd) {
            $overhead->details()->create($pd);
        }

        if ($request->hasFile('payment_proofs')) {
            foreach ($request->file('payment_proofs') as $file) {
                $path = $file->store('payment_proofs', 'public');
                $overhead->paymentProofs()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        // ── Posting jurnal otomatis ────────────────────────────────────────
        $noOverhead = 'OH-' . str_pad($overhead->id, 3, '0', STR_PAD_LEFT);
        $jurnalDetails = [];
        foreach ($parsedDetails as $pd) {
            $akunBeban = \App\Models\Akun::find($pd['coa_id']);
            $akunBayar = \App\Models\Akun::find($pd['coa_pembayaran_id']);
            $jurnalDetails[] = [
                'no_akun_beban' => $akunBeban->no_akun ?? $akunBeban->kode_akun ?? '',
                'nama_beban'    => $akunBeban->nama_akun ?? 'Biaya Overhead',
                'no_akun_bayar' => $akunBayar->no_akun ?? $akunBayar->kode_akun ?? '111',
                'nominal'       => $pd['nominal'],
            ];
        }
        \App\Services\JurnalService::jurnalOverhead($overhead->tanggal, $noOverhead, $jurnalDetails);

        return redirect('/overhead')->with('success', 'Data overhead berhasil disimpan.');
    }

    public function edit(Overhead $overhead)
    {
        $overhead->load(['details', 'paymentProofs']);

        $coas = Akun::whereIn('header_akun', ['Overhead Produksi', 'Biaya Beban Operasional Umum'])
                    ->get();

        $paymentCoas = Akun::where('header_akun', 'Aktiva Lancar')->get();

        return view('overhead.edit', compact('overhead', 'coas', 'paymentCoas'));
    }

    public function update(Request $request, Overhead $overhead)
    {
        // ── Sanitize nominal ──────────────────────────────────────────────
        $details = $request->input('details', []);
        foreach ($details as $i => $detail) {
            if (isset($detail['nominal'])) {
                $details[$i]['nominal'] = preg_replace('/\D/', '', $detail['nominal']);
            }
        }
        $request->merge(['details' => $details]);

        // ── Validasi ──────────────────────────────────────────────────────
        $rules = [
            'tanggal'       => 'required|date',
            'jenis_periode' => 'required|in:harian,mingguan,bulanan',
            'periode_mulai' => 'required|date',
            'details'       => 'required|array|min:1',
            'details.*.coa_id'            => 'required|exists:akun,id',
            'details.*.coa_pembayaran_id' => 'required|exists:akun,id',
            'details.*.keterangan'        => 'required|string|max:500',
            'details.*.nominal'           => 'required|integer|min:1',
            'payment_proofs'   => 'nullable|array|max:5',
            'payment_proofs.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        if ($request->jenis_periode === 'mingguan') {
            $rules['periode_akhir'] = 'required|date|after_or_equal:periode_mulai';
        }

        $messages = [
            'tanggal.required'                       => 'Tanggal wajib diisi.',
            'jenis_periode.required'                 => 'Jenis periode harus dipilih.',
            'periode_mulai.required'                 => 'Silakan pilih bulan dan tahun untuk periode bulanan, atau isi tanggal pembebanan.',
            'periode_akhir.required'                 => 'Tanggal akhir wajib diisi untuk periode mingguan.',
            'periode_akhir.after_or_equal'           => 'Tanggal akhir tidak boleh sebelum tanggal mulai.',
            'details.*.coa_id.required'              => 'Akun overhead wajib dipilih.',
            'details.*.coa_pembayaran_id.required'   => 'Akun pembayaran wajib dipilih.',
            'details.*.keterangan.required'          => 'Keterangan wajib diisi.',
            'details.*.nominal.required'             => 'Nominal wajib diisi.',
            'details.*.nominal.integer'              => 'Nominal harus berupa angka.',
            'details.*.nominal.min'                  => 'Nominal harus lebih besar dari 0.',
            'payment_proofs.max'                     => 'Maksimal 5 file bukti pembayaran.',
            'payment_proofs.*.mimes'                 => 'File harus berformat JPG, JPEG, PNG, atau PDF.',
            'payment_proofs.*.max'                   => 'Ukuran file maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        // ── Validasi bisnis: akun pembayaran ──────────────────────────────
        foreach ($request->details as $idx => $detail) {
            $paymentCoa = Akun::find($detail['coa_pembayaran_id']);
            if (!$paymentCoa || $paymentCoa->header_akun !== 'Aktiva Lancar') {
                return back()->withInput()->withErrors([
                    "details.{$idx}.coa_pembayaran_id" =>
                        'Akun pembayaran hanya boleh menggunakan akun Kas atau Bank.',
                ]);
            }
        }

        // ── Cek batas file ────────────────────────────────────────────────
        $existingCount = $overhead->paymentProofs()->count();
        $newCount      = $request->hasFile('payment_proofs') ? count($request->file('payment_proofs')) : 0;
        if ($existingCount + $newCount > 5) {
            return back()->withErrors(['payment_proofs' => 'Maksimal 5 berkas bukti pembayaran per transaksi.'])->withInput();
        }

        // ── Update ────────────────────────────────────────────────────────
        $parsedDetails = [];
        foreach ($request->details as $detail) {
            $parsedDetails[] = [
                'coa_id'            => $detail['coa_id'],
                'coa_pembayaran_id' => $detail['coa_pembayaran_id'],
                'keterangan'        => $detail['keterangan'],
                'nominal'           => (int) $detail['nominal'],
            ];
        }

        $firstDetail = $parsedDetails[0];

        $overhead->update([
            'tanggal'           => $request->tanggal,
            'jenis_periode'     => $request->jenis_periode,
            'periode_mulai'     => $request->periode_mulai,
            'periode_akhir'     => $request->jenis_periode === 'harian' ? null : $request->periode_akhir,
            'coa_id'            => $firstDetail['coa_id'],
            'coa_pembayaran_id' => $firstDetail['coa_pembayaran_id'],
            'keterangan'        => $firstDetail['keterangan'],
            'nominal'           => $firstDetail['nominal'],
        ]);

        $overhead->details()->delete();
        foreach ($parsedDetails as $pd) {
            $overhead->details()->create($pd);
        }

        if ($request->hasFile('payment_proofs')) {
            foreach ($request->file('payment_proofs') as $file) {
                $path = $file->store('payment_proofs', 'public');
                $overhead->paymentProofs()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect('/overhead')->with('success', 'Data overhead berhasil diperbarui.');
    }

    public function destroy(Overhead $overhead)
    {
        foreach ($overhead->paymentProofs as $proof) {
            Storage::disk('public')->delete($proof->file_path);
        }
        $overhead->paymentProofs()->delete();
        $overhead->delete();

        return redirect('/overhead');
    }

    public function deleteProof($id)
    {
        $proof = PaymentProof::findOrFail($id);
        Storage::disk('public')->delete($proof->file_path);
        $proof->delete();

        return back()->with('success', 'Bukti pembayaran berhasil dihapus.');
    }
}

