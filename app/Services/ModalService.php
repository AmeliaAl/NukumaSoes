<?php

namespace App\Services;

use App\Models\Jurnal;
use App\Models\Akun;
use Illuminate\Support\Facades\DB;

class ModalService
{
    /**
     * Buat jurnal dari transaksi modal
     * jenis: setoran | prive
     */
    public static function buatJurnal($modal): void
    {
        DB::transaction(function () use ($modal) {

            // =========================
            // 1️⃣ AMBIL AKUN
            // =========================
           // $akunKas   = Akun::where('no_akun', 111)->firstOrFail();
           // $kasBCA    = Akun::where('no_akun', 112)->firstOrFail();
           // $kasMandiri = Akun::where('no_akun', 113)->firstOrFail();
            $akunModal = Akun::where('no_akun', 311)->firstOrFail(); // Modal
            $akunPrive = Akun::where('no_akun', 312)->firstOrFail(); // Prive (kontra modal)

            // =========================
            // 2️⃣ HEADER JURNAL
            // =========================
            $jurnal = Jurnal::create([
                'tanggal'      => $modal->tanggal,
                'no_referensi' => null,
                'deskripsi'    => ucfirst($modal->jenis) . ' modal',
            ]);

            // =========================
            // 3️⃣ JURNAL BERDASARKAN JENIS
            // =========================
           if ($modal->jenis === 'setoran') {
            $jurnal->details()->create([
                'no_akun'   => $modal->id_akun, // ← langsung pakai id_akun dari form
                'debit'     => $modal->jumlah,
                'credit'    => 0,
                'deskripsi' => 'Setoran modal',
            ]);

            $jurnal->details()->create([
                'no_akun'   => $akunModal->id,
                'debit'     => 0,
                'credit'    => $modal->jumlah,
                'deskripsi' => 'Penambahan modal',
            ]);

            } elseif ($modal->jenis === 'prive') {
            $jurnal->details()->create([
                'no_akun'   => $akunPrive->id,
                'debit'     => $modal->jumlah,
                'credit'    => 0,
                'deskripsi' => 'Pengambilan modal (prive)',
            ]);

            $jurnal->details()->create([
                'no_akun'   => $modal->id_akun, // ← langsung pakai id_akun dari form
                'debit'     => 0,
                'credit'    => $modal->jumlah,
                'deskripsi' => 'Pengeluaran kas untuk prive',
            ]);


            } else {
                throw new \Exception('Jenis transaksi modal tidak valid');
            }
        });
    }
}
