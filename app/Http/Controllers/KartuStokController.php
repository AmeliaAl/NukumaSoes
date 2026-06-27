<?php

namespace App\Http\Controllers;

use App\Models\KartuStokEntry;
use App\Models\PersediaanEntry;
use App\Models\ProdukKeluarEntry;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

class KartuStokController extends Controller
{
    public function index(Request $request) 
    {
        $data = $this->getData($request);
        return view('kartu-stok.index', $data);
    }

    public function downloadPdf(Request $request)
    {
        $data = $this->getData($request);
        $pdf = Pdf::loadView('kartu-stok.pdf', $data);
        return $pdf->setPaper('a4', 'landscape')->download('kartu-stok.pdf');
    }

    private function getData(Request $request)
    {
        $kodeProduk = $request->get('kode_produk');
        $kategori = $request->get('kategori');
        $periode = $request->get('periode', date('Y-m'));
        $selectedProduct = null;

        if ($kodeProduk) {
            $selectedProduct = Product::where('kode_produk', $kodeProduk)->first();

            // Fallback to Inventory if Product model doesn't find it
            if (!$selectedProduct) {
                $inv = Inventory::where('kode_produk', $kodeProduk)->first();
                if ($inv) {
                    $selectedProduct = (object)[
                        'nama_produk' => $inv->nama_produk,
                        'kode_produk' => $inv->kode_produk,
                        'satuan' => 'Unit',
                    ];
                }
            }

            // Fallback to PersediaanEntry if still not found
            if (!$selectedProduct) {
                $entry = PersediaanEntry::where('kode_produk', $kodeProduk)->first();
                if ($entry) {
                    $selectedProduct = (object)[
                        'nama_produk' => $entry->nama_produk,
                        'kode_produk' => $entry->kode_produk,
                        'satuan' => 'Unit',
                    ];
                }
            }
        }

        // Get persediaan entries (produk masuk)
        $masukEntries = PersediaanEntry::when($kodeProduk, function ($query) use ($kodeProduk) {
            return $query->where('kode_produk', $kodeProduk);
        })->when($kategori, function ($query) use ($kategori) {
            return $query->whereHas('inventory', function($q) use ($kategori) {
                $q->where('kategori', $kategori);
            });
        })->when($periode, function ($query) use ($periode) {
            return $query->where('tanggal', 'like', $periode . '%');
        })->get()->map(function ($entry) {
            $inv = Inventory::where('kode_produk', $entry->kode_produk)
                ->where('no_batch', $entry->no_batch)
                ->first();
            return [
                'id' => $entry->id,
                'tanggal' => $entry->tanggal,
                'keterangan' => 'Produk Masuk',
                'id_transaksi' => $entry->id_transaksi,
                'masuk' => $entry->jumlah_masuk,
                'keluar' => 0,
                'harga' => $entry->harga,
                'total_harga' => $entry->total_harga,
                'no_batch' => $entry->no_batch ?? '',
                'tgl_masuk' => $inv ? $inv->tgl_masuk : null,
                'tgl_expired' => $inv ? $inv->tgl_expired : null,
                'hpp' => $inv ? $inv->hpp : null,
                'kode_produk' => $entry->kode_produk,
                'nama_produk' => $entry->nama_produk,
                'penanggung_jawab' => '-',
                'keterangan_detail' => '-',
            ];
        });

        // Get produk keluar entries
        $keluarEntries = ProdukKeluarEntry::when($kodeProduk, function ($query) use ($kodeProduk) {
            return $query->where('kode_produk', $kodeProduk);
        })->when($kategori, function ($query) use ($kategori) {
            return $query->where(function($q) use ($kategori) {
                $q->where('kategori', $kategori)
                  ->orWhereHas('inventory', function($sq) use ($kategori) {
                      $sq->where('kategori', $kategori);
                  });
            });
        })->when($periode, function ($query) use ($periode) {
            return $query->where('tanggal', 'like', $periode . '%');
        })->get()->map(function ($entry) {
            $inv = Inventory::where('kode_produk', $entry->kode_produk)
                ->orderBy('tgl_expired', 'asc')
                ->first();
            return [
                'id' => $entry->id,
                'tanggal' => $entry->tanggal,
                'keterangan' => 'Produk Keluar',
                'id_transaksi' => $entry->id_transaksi,
                'masuk' => 0,
                'keluar' => $entry->jumlah_keluar,
                'harga' => $entry->harga,
                'total_harga' => $entry->total_harga,
                'no_batch' => $entry->no_batch ?? ($inv ? $inv->no_batch : ''),
                'tgl_masuk' => $inv ? $inv->tgl_masuk : null,
                'tgl_expired' => $inv ? $inv->tgl_expired : null,
                'hpp' => $inv ? $inv->hpp : null,
                'kode_produk' => $entry->kode_produk,
                'nama_produk' => $entry->nama_produk,
                'penanggung_jawab' => '-',
                'keterangan_detail' => '-',
            ];
        });

        // Merge and sort entries by date and id
        $entries = collect($masukEntries)->concat($keluarEntries)->sortBy([['tanggal', 'desc'], ['id', 'desc']])->values();

        // Calculate running balance for all filtered entries
        $balance = 0;
        $totalValue = 0;
        $avgCost = 0;
        
        $entries = $entries->sortBy([['tanggal', 'asc'], ['id', 'asc']])->map(function ($entry) use (&$balance, &$totalValue, &$avgCost) {
            if ($entry['masuk'] > 0) {
                $totalValue += $entry['total_harga'];
                $balance += $entry['masuk'];
                $avgCost = $balance > 0 ? $totalValue / $balance : 0;
            } else {
                $balance -= $entry['keluar'];
                $totalValue = $balance * $avgCost;
            }
            
            $entry['saldo'] = $balance;
            $entry['saldo_harga'] = $avgCost;
            $entry['saldo_total'] = $totalValue;

            $qty = $entry['masuk'] > 0 ? $entry['masuk'] : $entry['keluar'];
            $entry['total_hpp'] = ($entry['hpp'] ?? 0) > 0 && $qty > 0
                ? $qty * $entry['hpp']
                : null;
            
            return $entry;
        });

        $totalNilaiMasuk = $entries->sum(fn ($entry) => $entry['masuk'] > 0 ? ($entry['total_hpp'] ?? 0) : 0);
        $totalNilaiKeluar = $entries->sum(fn ($entry) => $entry['keluar'] > 0 ? ($entry['total_hpp'] ?? 0) : 0);
        $totalMasuk = $entries->sum(fn ($entry) => $entry['masuk'] ?? 0);
        $totalKeluar = $entries->sum(fn ($entry) => $entry['keluar'] ?? 0);
        $totalNilaiBersih = $totalNilaiMasuk - $totalNilaiKeluar;
        $sisaAkhir = $entries->last()['saldo'] ?? ($totalMasuk - $totalKeluar);

        // Get all categories and inventories for filters
        $categories = Category::all();
        $inventories = Inventory::select('kode_produk', 'nama_produk', 'no_batch')->distinct()->get();
        
        return compact('entries', 'kodeProduk', 'categories', 'inventories', 'selectedProduct', 'periode', 'totalNilaiMasuk', 'totalNilaiKeluar', 'totalMasuk', 'totalKeluar', 'totalNilaiBersih', 'sisaAkhir');
    }
}
