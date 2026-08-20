<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\KartuStokEntry;
use Carbon\Carbon;

use App\Exports\ExpiredProductHistoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MonitoringController extends Controller
{
    public function fefo()
    {
        $totalProdukTerdaftar = \App\Models\Inventory::sum('jumlah');
        $produkAman = \App\Models\Inventory::where('tgl_expired', '>', Carbon::now()->addMonth())->sum('jumlah');
        $produkAkanExpired = \App\Models\Inventory::where('tgl_expired', '>', Carbon::now())
                                    ->where('tgl_expired', '<=', Carbon::now()->addMonth())
                                    ->sum('jumlah');
        $produkExpired = \App\Models\Inventory::where('tgl_expired', '<', Carbon::now())->sum('jumlah');
        // Calculate Total Transactions
        $masukTotal = \App\Models\PersediaanEntry::count();
        $keluarTotal = \App\Models\ProdukKeluarEntry::count();
        $totalTransaksi = $masukTotal + $keluarTotal;

        // Fetch all inventory items ordered by expiration date (FEFO)
        $products = \App\Models\Inventory::orderBy('tgl_expired', 'asc')->get();

        // Fetch expired product history
        $expiredHistories = \App\Models\ExpiredProductHistory::orderBy('created_at', 'desc')->get();

        return view('monitoring.fefo', compact(
            'totalProdukTerdaftar',
            'produkAman',
            'produkAkanExpired',
            'produkExpired',
            'totalTransaksi',
            'products',
            'expiredHistories'
        ));
    }

    public function updateProductExpiry(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'expiry_date' => 'required|date',
        ]);

        $product = Product::find($request->product_id);
        $product->update([
            'tgl_expired' => $request->expiry_date,
        ]);

        return response()->json(['success' => true, 'message' => 'Tanggal expired berhasil diperbarui']);
    }

    public function destroyExpiredHistory($id)
    {
        $history = \App\Models\ExpiredProductHistory::findOrFail($id);
        $history->delete();

        return redirect()->back()->with('success', 'Riwayat produk expired berhasil dihapus');
    }

    public function exportExcelExpiredHistory()
    {
        return Excel::download(new ExpiredProductHistoryExport, 'riwayat-produk-expired.xlsx');
    }

    public function exportPdfExpiredHistory()
    {
        $histories = \App\Models\ExpiredProductHistory::orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('monitoring.expired-history-pdf', compact('histories'))->setPaper('a4', 'landscape');
        return $pdf->download('riwayat-produk-expired.pdf');
    }

    public function addToJournal($id)
    {
        $history = \App\Models\ExpiredProductHistory::findOrFail($id);

        if ($history->is_journaled) {
            return redirect()->back()->with('error', 'Riwayat ini sudah dimasukkan ke jurnal.');
        }

        // Get COAs
        $coaDebit = \App\Models\Coa::where('nama_akun', 'LIKE', '%Kerugian Produk Expired%')->first();
        $refDebit = $coaDebit ? $coaDebit->kode_akun : '515';

        $coaKredit = \App\Models\Coa::where('nama_akun', 'LIKE', '%Persediaan Produk Jadi%')->first();
        $refKredit = $coaKredit ? $coaKredit->kode_akun : '113';

        $totalNominal = $history->jumlah * ($history->hpp ?? 0);

        // Buat ID transaksi unik untuk entry kartu stok
        $idTransaksi = 'EXP-' . now()->format('Ymd') . '-' . str_pad($history->id, 5, '0', STR_PAD_LEFT);

        // Create Journal Entries
        \App\Models\JurnalUmum::create([
            'tanggal'    => now(),
            'keterangan' => 'Kerugian Produk Expired',
            'ref'        => $refDebit,
            'debit'      => $totalNominal,
            'kredit'     => 0,
        ]);

        \App\Models\JurnalUmum::create([
            'tanggal'    => now(),
            'keterangan' => 'Persediaan Produk Jadi',
            'ref'        => $refKredit,
            'debit'      => 0,
            'kredit'     => $totalNominal,
        ]);

        // Create Pengeluaran Entry
        \App\Models\PengeluaranEntry::create([
            'nama_akun'            => 'Kerugian Produk Expired',
            'produk_expired'       => 'Kerugian Produk Expired pada Persediaan Produk Jadi',
            'tanggal_pengeluaran'  => now(),
            'deskripsi'            => 'Penghapusan produk expired via jurnal',
            'nominal'              => $totalNominal,
        ]);

        // Catat ke Kartu Stok sebagai Produk Keluar akibat Expired
        if ($history->jumlah > 0) {
            \App\Models\KartuStokEntry::create([
                'tanggal'      => now()->toDateString(),
                'keterangan'   => 'Produk Keluar (Expired - Batch: ' . ($history->no_batch ?? '-') . ')',
                'id_transaksi' => $idTransaksi,
                'masuk'        => 0,
                'keluar'       => $history->jumlah,
                'harga'        => $history->hpp ?? 0,
                'total_harga'  => $totalNominal,
                'no_batch'     => $history->no_batch ?? null,
                'kode_produk'  => $history->kode_produk ?? null,
                'nama_produk'  => $history->nama_produk ?? null,
            ]);
        }

        // Mark as journaled
        $history->update(['is_journaled' => true]);

        return redirect()->back()->with('success', 'Riwayat produk expired berhasil dimasukkan ke jurnal dan dicatat di Kartu Stok.');
    }
}
