<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanProduksi;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PenerimaanOrderProduksiController extends Controller
{
    /**
     * Daftar semua order produksi yang diterima dari bagian lain.
     * Memfilter hanya job order yang memiliki kode_order_eksternal.
     */
    public function index()
    {
        $orders = PermintaanProduksi::with(['produk', 'admin'])
            ->whereNotNull('kode_order_eksternal')
            ->orderBy('tanggal_terima_order', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('transaksi.penerimaan-order-produksi.index', compact('orders'));
    }

    /**
     * Form untuk menerima order baru dari bagian lain.
     */
    public function create()
    {
        // Auto-generate nomor job
        $lastJob = PermintaanProduksi::orderBy('created_at', 'desc')->first();
        if ($lastJob) {
            $lastNumber = (int) substr($lastJob->nomor_job, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        $nomor_job = 'JOB-' . date('Ymd') . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $produk = Produk::where('status', 'aktif')->get();

        return view('transaksi.penerimaan-order-produksi.create', compact('nomor_job', 'produk'));
    }

    /**
     * Simpan order yang diterima — ini langsung membuat Job Order baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_order_eksternal' => 'required|string|max:100|unique:permintaan_produksi,kode_order_eksternal',
            'nama_pemesan'         => 'required|string|max:150',
            'tanggal_terima_order' => 'required|date',
            'nomor_job'            => 'required|string|max:50|unique:permintaan_produksi,nomor_job',
            'id_produk'            => 'required|exists:produk,id_produk',
            'tanggal_mulai'        => 'required|date|after_or_equal:tanggal_terima_order',
            'jumlah_produksi'      => 'required|numeric|min:1',
            'jumlah_batch'         => 'required|integer|min:1',
            'kapasitas_batch_per_hari' => 'nullable|integer|min:1|max:50',
            'jenis_produksi'       => 'required|in:maklun,brand_sendiri',
            'tujuan_produksi'      => 'required|in:pesanan,stok_wip,stok_barang_jadi',
            'tahap_produksi'       => 'required|in:persiapan,produksi,filling',
            'keterangan'           => 'nullable|string',
        ], [
            'kode_order_eksternal.unique' => 'Kode Order tersebut sudah pernah diterima sebelumnya!',
        ]);

        $job = PermintaanProduksi::create([
            'nomor_job'            => $request->nomor_job,
            'kode_order_eksternal' => $request->kode_order_eksternal,
            'nama_pemesan'         => $request->nama_pemesan,
            'tanggal_terima_order' => $request->tanggal_terima_order,
            'id_produk'            => $request->id_produk,
            'id_admin'             => Auth::guard('admin')->id(),
            'tanggal_mulai'        => $request->tanggal_mulai,
            'tanggal_selesai'      => null,
            'jumlah_produksi'      => $request->jumlah_produksi,
            'jumlah_batch'         => $request->jumlah_batch,
            'jenis_produksi'       => $request->jenis_produksi,
            'tujuan_produksi'      => $request->tujuan_produksi,
            'tahap_produksi'       => $request->tahap_produksi,
            'nama_customer_maklun' => $request->nama_pemesan, // sumber dari luar
            'customer'             => $request->nama_pemesan,
            'status'               => 'pending',
            'total_biaya_bahan'    => 0,
            'total_biaya_tenaga_kerja' => 0,
            'total_biaya_overhead' => 0,
            'total_biaya_produksi' => 0,
            'harga_pokok_per_unit' => 0,
            'keterangan'           => $request->keterangan,
        ]);
        $job->sinkronkanBatchRencana(
            (int) $request->jumlah_batch,
            (float) $request->jumlah_produksi,
            $request->tanggal_mulai,
            (int) $request->input('kapasitas_batch_per_hari', 1)
        );

        Log::info("Order Produksi Diterima: {$job->nomor_job} dari Kode Eksternal: {$job->kode_order_eksternal}");

        return redirect()->route('penerimaan-order-produksi.index')
                         ->with('success', "Order {$request->kode_order_eksternal} berhasil diterima! Job Order {$job->nomor_job} telah dibuat.");
    }

    /**
     * Detail order yang diterima — redirect ke halaman job order.
     */
    public function show($id)
    {
        $order = PermintaanProduksi::with(['produk', 'admin'])->findOrFail($id);
        return redirect()->route('permintaan-produksi.show', $id);
    }
}
