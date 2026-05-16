<?php

namespace App\Services;

use App\Models\JurnalUmum;
use App\Models\JurnalDetail;
use App\Models\Akun;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JurnalService
{
    /**
     * Generate nomor jurnal otomatis
     */
    /*public function generateNoJurnal(): string
    {
        $tanggal = Carbon::now()->format('Ymd');
        $lastJurnal = JurnalUmum::whereDate('created_at', Carbon::today())
            ->latest('id')
            ->first();
        
        $urutan = $lastJurnal ? (int) substr($lastJurnal->no_jurnal, -3) + 1 : 1;
        
        return 'JRN-' . $tanggal . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Buat jurnal baru
     * 
     * @param array $data
     * @param array $items [['id_akun' => 1, 'debit' => 1000, 'kredit' => 0], ...]
     * @return JurnalUmum
     */
    /*public function buatJurnal(array $data, array $items): JurnalUmum
    {
        return DB::transaction(function () use ($data, $items) {
            // Validasi balance
            $totalDebit = collect($items)->sum('debit');
            $totalKredit = collect($items)->sum('kredit');
            
            if ($totalDebit != $totalKredit) {
                throw new \Exception("Jurnal tidak balance! Debit: {$totalDebit}, Kredit: {$totalKredit}");
            }

            // Buat header jurnal
            $jurnal = Jurnal::create([
                'tanggal' => $data['tanggal'] ?? Carbon::now(),
                'no_referensi' => $data['no_referensi'] ?? null,
                'deskripsi' => $data['deskripsi'],
                'created_by' => auth()->id(),
            ]);

            // Buat detail jurnal & update saldo akun
            foreach ($items as $index => $item) {
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id,
                    'no_akun' => $item['no_akun'],
                    'deskripsi' => $item['deskripsi'] ?? null,
                    'debit' => $item['debit'] ?? 0,
                    'kredit' => $item['kredit'] ?? 0,
                    'urutan' => $index + 1,
                ]);

                // Update saldo akun
                $akun = Akun::find($item['no_akun']);
                if ($akun) {
                    $akun->updateSaldo($item['debit'] ?? 0, $item['kredit'] ?? 0);
                }
            }

            return $jurnal->load('details.akun');
        });
    }

    /**
     * Jurnal untuk Pembelian Aset
     */
    /**
 * Jurnal untuk Pembelian Aset (Cash Basis)
 */
/*public function jurnalPembelianAset($fakturPembelian): JurnalUmum
{
    // ✅ Cek dulu status pembayaran
    if ($fakturPembelian->status !== 'lunas') {
        throw new \Exception('Faktur belum lunas, jurnal tidak dapat dibuat');
    }

    $items = [];

    foreach ($fakturPembelian->items as $item) {
        $items[] = [
            // Debit: Aset Tetap
            'no_akun' => $this->getAkunByKode('121'),
            'debit' => $item->total_harga,
            'kredit' => 0,
            'keterangan' => "Pembelian {$item->nama_aset}",
        ];
    }

    // Kredit: Kas (karena sudah bayar tunai)
    $items[] = [
        'no_akun' => $this->getAkunByKode('111'), // Kas
        'debit' => 0,
        'kredit' => $fakturPembelian->total_tagihan,
        'keterangan' => "Pembayaran ke {$fakturPembelian->vendor->nama_vendor}",
    ];

    return $this->buatJurnal([
        'tanggal' => now(), // Tanggal saat dibayar
        'no_referensi' => $fakturPembelian->id,
        'keterangan' => "Pembelian Aset (LUNAS) - {$fakturPembelian->no_faktur}",
    ], $items);
}

    /**
     * Jurnal untuk Penyusutan Bulanan
     */
    /*public function jurnalPenyusutan($penyusutan): JurnalUmum
    {
        $items = [
            [
                // Debit: Beban Penyusutan
                'id_akun' => $this->getAkunByKode('5-1004'),
                'debit' => $penyusutan->beban_penyusutan,
                'kredit' => 0,
                'keterangan' => "Penyusutan {$penyusutan->aset->nama_aset}",
            ],
            [
                // Kredit: Akumulasi Penyusutan
                'id_akun' => $this->getAkunByKode('1-2900'),
                'debit' => 0,
                'kredit' => $penyusutan->beban_penyusutan,
                'keterangan' => "Akumulasi Penyusutan {$penyusutan->aset->nama_aset}",
            ],
        ];

        return $this->buatJurnal([
            'tanggal_transaksi' => Carbon::parse($penyusutan->periode)->endOfMonth(),
            'jenis_transaksi' => 'penyusutan',
            'referensi_id' => $penyusutan->id,
            'referensi_tipe' => 'App\Models\Penyusutan',
            'keterangan' => "Penyusutan Periode {$penyusutan->periode}",
        ], $items);
    }

    /**
     * Jurnal untuk Pemakaian Persediaan
     */
    /*public function jurnalPemakaianPersediaan($pemakaian): JurnalUmum
    {
        $aset = $pemakaian->asetLancar;
        $totalNilai = $pemakaian->jumlah * $aset->harga_satuan_rata;

        $items = [
            [
                // Debit: Beban Persediaan
                'id_akun' => $this->getAkunByKode('5-1005'),
                'debit' => $totalNilai,
                'kredit' => 0,
                'keterangan' => "Pemakaian {$aset->nama_barang}",
            ],
            [
                // Kredit: Persediaan
                'id_akun' => $this->getAkunByKode('1-1100'),
                'debit' => 0,
                'kredit' => $totalNilai,
                'keterangan' => "Pengurangan stok {$aset->nama_barang}",
            ],
        ];

        return $this->buatJurnal([
            'tanggal_transaksi' => $pemakaian->tanggal,
            'jenis_transaksi' => 'pemakaian_persediaan',
            'referensi_id' => $pemakaian->id,
            'referensi_tipe' => 'App\Models\PemakaianPersediaan',
            'keterangan' => "Pemakaian Persediaan {$aset->nama_barang}",
        ], $items);
    }

    /**
     * Jurnal untuk Pembelian Persediaan
     */
    /*public function jurnalPembelianPersediaan($persediaan): JurnalUmum
    {
        $items = [
            [
                // Debit: Persediaan
                'id_akun' => $this->getAkunByKode('1-1100'),
                'debit' => $persediaan->total,
                'kredit' => 0,
                'keterangan' => "Pembelian {$persediaan->nama_barang}",
            ],
            [
                // Kredit: Kas
                'id_akun' => $this->getAkunByKode('1-1001'),
                'debit' => 0,
                'kredit' => $persediaan->total,
                'keterangan' => "Pembayaran persediaan",
            ],
        ];

        return $this->buatJurnal([
            'tanggal_transaksi' => $persediaan->tanggal_masuk,
            'jenis_transaksi' => 'pembelian_persediaan',
            'referensi_id' => $persediaan->id,
            'referensi_tipe' => 'App\Models\Persediaan',
            'keterangan' => "Pembelian Persediaan {$persediaan->nama_barang}",
        ], $items);
    }

    /**
     * Helper: Get ID akun by kode
     */
    /*protected function getAkunByKode(string $kode): int
    {
        $akun = Akun::where('kode_akun', $kode)->first();
        
        if (!$akun) {
            throw new \Exception("Akun dengan kode {$kode} tidak ditemukan!");
        }
        
        return $akun->id;
    }

    /**
     * Void/batalkan jurnal
     */
    /*public function voidJurnal(JurnalUmum $jurnal): bool
    {
        return DB::transaction(function () use ($jurnal) {
            // Reverse saldo akun
            foreach ($jurnal->details as $detail) {
                $akun = $detail->akun;
                if ($akun) {
                    // Balik nilai debit kredit untuk reverse
                    $akun->updateSaldo($detail->kredit, $detail->debit);
                }
            }

            // Update status
            $jurnal->update(['status' => 'void']);
            
            return true;
        });
    }*/
}