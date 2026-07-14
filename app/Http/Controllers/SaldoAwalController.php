<?php

namespace App\Http\Controllers;

use App\Models\SaldoAwal;
use App\Models\Akun;
use Illuminate\Http\Request;

class SaldoAwalController extends Controller
{
    public function index()
    {
        $saldoAwals = SaldoAwal::with('coa')->orderBy('tanggal')->get();

        return view('saldo_awal.index', compact('saldoAwals'));
    }

    public function create()
    {
        // Ambil coa_id yang sudah punya saldo awal
        $coaIdsYangSudahAda = SaldoAwal::pluck('coa_id')->toArray();

        // Dropdown hanya akun 111 & 112 yang BELUM punya saldo awal
        $coas = Akun::whereIn('no_akun', ['111', '112'])
                    ->whereNotIn('id', $coaIdsYangSudahAda)
                    ->get();

        // Jika semua sudah ada, redirect balik
        if ($coas->isEmpty()) {
            return redirect()->route('saldo-awal.index')
                ->with('info', 'Seluruh akun telah memiliki saldo awal.');
        }

        // Cek apakah sudah ada saldo awal pertama
        // Jika ada, ambil tanggal periode awal (sebagai string Y-m-d)
        $saldoAwalPertama = SaldoAwal::orderBy('tanggal')->first();
        $periodeAwal = $saldoAwalPertama ? $saldoAwalPertama->tanggal : null;

        return view('saldo_awal.create', compact('coas', 'periodeAwal'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'nominal' => preg_replace('/\D/', '', $request->input('nominal')),
        ]);

        $request->validate([
            'tanggal' => ['required', 'date'],
            'coa_id'  => ['required', 'exists:akun,id'],
            'nominal' => ['required', 'numeric', 'min:1'],
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'coa_id.required'  => 'Akun wajib dipilih.',
            'coa_id.exists'    => 'Akun tidak valid.',
            'nominal.required' => 'Nominal saldo awal wajib diisi.',
            'nominal.numeric'  => 'Nominal saldo awal harus berupa angka.',
            'nominal.min'      => 'Nominal saldo awal harus lebih besar dari 0.',
        ]);

        // Cegah duplikat — satu akun hanya boleh punya satu saldo awal
        $sudahAda = SaldoAwal::where('coa_id', $request->coa_id)->exists();
        if ($sudahAda) {
            return back()
                ->withInput()
                ->withErrors(['coa_id' => 'Saldo Awal untuk akun ini sudah tersedia. Saldo Awal hanya dapat diinput satu kali.']);
        }

        // Cek apakah sudah ada saldo awal pertama
        $saldoAwalPertama = SaldoAwal::orderBy('tanggal')->first();
        
        if ($saldoAwalPertama) {
            // Jika sudah ada saldo awal pertama, validasi tanggal harus sama
            $periodeAwal = \Carbon\Carbon::parse($saldoAwalPertama->tanggal)->format('Y-m-d');
            $tanggalInput = \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d');

            if ($tanggalInput !== $periodeAwal) {
                return back()
                    ->withInput()
                    ->withErrors(['tanggal' => 
                        "Saldo Awal hanya dapat diinput pada periode awal penggunaan aplikasi. " .
                        "Seluruh akun harus menggunakan periode saldo awal yang sama (" . 
                        \Carbon\Carbon::parse($periodeAwal)->translatedFormat('d F Y') . ")."
                    ]);
            }

            // Gunakan tanggal periode awal, bukan input user
            $tanggalFinal = $periodeAwal;
        } else {
            // Input pertama — gunakan tanggal dari input user
            $tanggalFinal = $request->tanggal;
        }

        // Generate no_bukti otomatis: SA-001, SA-002, dst
        $last      = SaldoAwal::orderBy('id', 'desc')->first();
        $newNumber = $last ? ((int) substr($last->no_bukti, 3)) + 1 : 1;
        $noBukti   = 'SA-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        SaldoAwal::create([
            'no_bukti'   => $noBukti,
            'tanggal'    => $tanggalFinal,
            'coa_id'     => $request->coa_id,
            'nominal'    => $request->nominal,
            'keterangan' => 'Saldo Awal',
        ]);

        // ── Posting jurnal otomatis ────────────────────────────────────────
        $akun = \App\Models\Akun::find($request->coa_id);
        \App\Services\JurnalService::jurnalSetoranModal(
            $tanggalFinal,
            $noBukti,
            $akun->no_akun ?? '111',
            $akun->nama_akun ?? 'Kas',
            (float) $request->nominal
        );

        return redirect()->route('saldo-awal.index')
            ->with('success', 'Saldo awal berhasil disimpan.');
    }

    // Edit & Update diblokir — saldo awal adalah Opening Balance yang permanen
    public function edit(SaldoAwal $saldoAwal)
    {
        return redirect()->route('saldo-awal.index')
            ->with('error', 'Saldo awal tidak dapat diubah setelah disimpan.');
    }

    public function update(Request $request, SaldoAwal $saldoAwal)
    {
        return redirect()->route('saldo-awal.index')
            ->with('error', 'Saldo awal tidak dapat diubah setelah disimpan.');
    }

    public function destroy(SaldoAwal $saldoAwal)
    {
        return redirect()->route('saldo-awal.index')
            ->with('error', 'Saldo awal tidak dapat dihapus.');
    }
}
