<?php

namespace App\Filament\Admin\Resources\PerolehanAsets\Pages;

use App\Filament\Admin\Resources\PerolehanAsets\PerolehanAsetResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\PerolehanAset;
use App\Models\TransaksiAset;
use App\Models\Aset;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class CreatePerolehanAset extends CreateRecord
{
    protected static string $resource = PerolehanAsetResource::class;

    public function getTitle(): string
        {
            return 'Perolehan Aset Tetap'; 
        }

     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $total = ($data['qty'] * $data['harga_satuan']) + ($data['biaya_lain'] ?? 0);

            $transaksi = TransaksiAset::create([
                'tanggal' => $data['tanggal_faktur'],
                'tipe_transaksi' => 'perolehan',
                'total_nilai' => $total,
                'keterangan' => 'Perolehan aset tetap',
            ]);

            // 2️⃣ PEROLEHAN
            $perolehan = PerolehanAset::create([
                'id_transaksi' => $transaksi->id,
                'id_vendor' => $data['id_vendor'],
                'id_faktur' => $data['id_faktur'] ?? null, 
                'id_faktur_item' => $data['id_faktur_item'] ?? null, 
                'nama_aset' => $data['nama_aset'],
                'kode_lokasi' => $data['kode_lokasi'],  
                'tanggal_pakai' => $data['tanggal_pakai'],
                'id_kategori' => $data['id_kategori'],
                'no_faktur' => $data['no_faktur'],
                'tanggal_faktur' => $data['tanggal_faktur'],
                'masa_manfaat' => $data['masa_manfaat'],
                'metode_penyusutan' => $data['metode_penyusutan'],
                'nilai_residu' => $data['nilai_residu'],
                'qty' => $data['qty'],
                'harga_satuan' => $data['harga_satuan'],
                'biaya_lain' => $data['biaya_lain'],
                'total_perolehan' => $total,
            ]);

            // 3️⃣ ASET TETAP (PER UNIT, SIAP DISUSUTKAN)
            for ($i = 1; $i <= $data['qty']; $i++) {

                $hargaPerUnit = $total / $data['qty'];

                Aset::create([
                    'kode_aset' => 'AST-' . strtoupper(Str::random(8)),
                    'nama_aset' => $data['nama_aset'],
                    'id_kategori' => $data['id_kategori'],
                    'tanggal_perolehan' => $data['tanggal_pakai'],
                    'nilai_perolehan' => $hargaPerUnit,
                    'masa_manfaat' => $data['masa_manfaat'],
                    'metode_penyusutan' => $data['metode_penyusutan'],
                    'nilai_residu' => $data['nilai_residu'],
                    'akumulasi_penyusutan' => 0,
                    'nilai_buku' => $hargaPerUnit,
                    'status' => 'aktif',
                ]);
            }

            return $perolehan;
        });
    }

    protected function beforeCreate(): void
    {
        $item = \App\Models\FakturPembelianItem::find($this->data['id_faktur_item']);

        if ($item->kategoriAset->jenis_aset !== 'aset_tetap') {
            throw new \Exception('Item ini bukan aset tetap.');
        }
    }


}
