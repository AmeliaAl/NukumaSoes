<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Supplier;
use App\Models\BahanBaku;
use App\Models\Coa;
use App\Models\Pembelian;

class PembelianPB013Seeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed transaksi pembelian PB-013
     * Tanggal  : 10 Juni 2026
     * Supplier : CV Mitra Pangan
     * Bayar    : Bank BCA (COA 112)
     */
    public function run(): void
    {
        // ----------------------------------------------------------------
        // 1. Pastikan Supplier: CV Mitra Pangan
        // ----------------------------------------------------------------
        $supplier = Supplier::firstOrCreate(
            ['nama_supplier' => 'CV Mitra Pangan'],
            [
                'kode_supplier' => 'SUP005',
                'alamat'        => '-',
                'telepon'       => '-',
            ]
        );

        // ----------------------------------------------------------------
        // 2. Pastikan Bahan Baku: Terigu Protein Sedang
        //    (item baru yang belum ada di master)
        // ----------------------------------------------------------------
        $teriguProteinSedang = BahanBaku::firstOrCreate(
            ['nama_bahan' => 'Terigu Protein Sedang'],
            [
                'kode_bahan'      => 'BB-041',
                'jenis_bahan'     => 'Langsung',
                'satuan'          => 'Kg',
                'isi_per_kemasan' => '25',
                'stok_minimum'    => 0,
                'stok_saat_ini'   => 0,
            ]
        );

        // ----------------------------------------------------------------
        // 3. Ambil Bahan Baku yang sudah ada di master
        // ----------------------------------------------------------------
        $fillingChoco  = BahanBaku::where('nama_bahan', 'Filling Choco')->firstOrFail();   // id=6
        $plastikSoes   = BahanBaku::where('nama_bahan', 'Plastik soes')->firstOrFail();    // id=33
        $lakbanCoklat  = BahanBaku::where('nama_bahan', 'Lakban Coklat')->firstOrFail();   // id=29

        // ----------------------------------------------------------------
        // 4. Ambil COA Pembayaran: Bank BCA (kode 112)
        // ----------------------------------------------------------------
        $coaBankBca = Coa::where('kode_akun', '112')->firstOrFail(); // id=2

        // ----------------------------------------------------------------
        // 5. Hitung nilai transaksi
        // ----------------------------------------------------------------
        $details = [
            [
                'bahan_baku_id'   => $teriguProteinSedang->id,
                'qty'             => 2,
                'isi_per_kemasan' => '25',
                'harga'           => 234000,
                'subtotal'        => 468000,
            ],
            [
                'bahan_baku_id'   => $fillingChoco->id,
                'qty'             => 1,
                'isi_per_kemasan' => '12',
                'harga'           => 1280940,
                'subtotal'        => 1280940,
            ],
            [
                'bahan_baku_id'   => $plastikSoes->id,
                'qty'             => 5,
                'isi_per_kemasan' => '1',
                'harga'           => 52000,
                'subtotal'        => 260000,
            ],
            [
                'bahan_baku_id'   => $lakbanCoklat->id,
                'qty'             => 2,
                'isi_per_kemasan' => '1',
                'harga'           => 18000,
                'subtotal'        => 36000,
            ],
        ];

        $subtotal    = 2044940; // 468000 + 1280940 + 260000 + 36000
        $diskon      = 44940;
        $totalBersih = 2000000; // subtotal - diskon
        $ongkir      = 25000;
        $grandTotal  = 2025000; // total_bersih + ongkir

        // ----------------------------------------------------------------
        // 6. Buat record Pembelian (header)
        //    Gunakan firstDetail sebagai legacy field di tabel pembelians
        // ----------------------------------------------------------------
        $firstDetail = $details[0];

        $pembelian = Pembelian::create([
            'no_pembelian'     => 'PB-013',
            'tanggal'          => '2026-06-10',
            'nomor_permintaan' => 'PR-013',
            'supplier_id'      => $supplier->id,
            // Legacy columns (diisi dari item pertama, sesuai pola controller)
            'bahan_baku_id'    => $firstDetail['bahan_baku_id'],
            'qty'              => $firstDetail['qty'],
            'harga'            => $firstDetail['harga'],
            'total'            => $firstDetail['subtotal'],
            // Ringkasan finansial
            'subtotal'         => $subtotal,
            'diskon'           => $diskon,
            'ongkir'           => $ongkir,
            'total_bersih'     => $totalBersih,
            'grand_total'      => $grandTotal,
            'coa_id'           => $coaBankBca->id,
        ]);

        // ----------------------------------------------------------------
        // 7. Buat record PembelianDetail (4 item)
        // ----------------------------------------------------------------
        foreach ($details as $detail) {
            $pembelian->details()->create($detail);
        }

        $this->command->info('Seeder PB-013 berhasil dibuat. ID Pembelian: ' . $pembelian->id);
    }
}
