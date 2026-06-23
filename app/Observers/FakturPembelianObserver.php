<?php

namespace App\Observers;

use App\Models\FakturPembelian;

class FakturPembelianObserver
{
    public function creating(FakturPembelian $faktur): void
    {
        // Set default status untuk faktur baru
        if (empty($faktur->status)) {
            $faktur->status = 'belum_dibayar';
        }
        
        // Hitung total_tagihan dari input form
        // Karena items belum ada saat creating, ambil dari input
        if (empty($faktur->total_tagihan)) {
            $faktur->total_tagihan = 0;
        }
    }

    public function created(FakturPembelian $faktur): void
    {
        // Setelah faktur dan items tersimpan, hitung ulang total
        $this->recalculateTotal($faktur);
    }

    public function updating(FakturPembelian $faktur): void
    {
        // Jangan update total di sini karena items belum update
    }

    public function updated(FakturPembelian $faktur): void
    {
        // Setelah update, hitung ulang total
        $this->recalculateTotal($faktur);
    }

    protected function recalculateTotal(FakturPembelian $faktur): void
    {
        // Hitung subtotal dari items
        $subtotal = $faktur->items()->sum('total_harga');
        
        // Hitung total tagihan = subtotal + biaya_lain
        $totalTagihan = $subtotal + ($faktur->biaya_lain ?? 0);
        
        // Update jika berbeda
        if ($faktur->total_tagihan != $totalTagihan) {
            $faktur->updateQuietly(['total_tagihan' => $totalTagihan]);
        }
    }

    /*public function __construct(JurnalService $jurnalService)
    {
        $this->jurnalService = $jurnalService;
    }

    public function update(FakturPembelian $faktur): void
    {
        // ✅ Jurnal dibuat HANYA saat status berubah jadi "lunas"
        if ($faktur->isDirty('status') && $faktur->status === 'lunas') {
            try {
                $this->jurnalService->jurnalPembelianAset($faktur);
                \Log::info("✅ Jurnal pembelian aset berhasil dibuat untuk {$faktur->no_faktur}");
            } catch (\Exception $e) {
                \Log::error("❌ Gagal membuat jurnal: " . $e->getMessage());
            }
        }
    }*/

}