<?php

namespace Database\Seeders;


use App\Models\Coa;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class CoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Truncate the table to clear existing corrupted data
        Schema::disableForeignKeyConstraints();
        Coa::truncate();
        Schema::enableForeignKeyConstraints();

        $coas = [
            ['kode_akun' => '111', 'header_akun' => '1', 'nama_akun' => 'Kas'],
            ['kode_akun' => '112', 'header_akun' => '1', 'nama_akun' => 'Persediaan Barang Dagang'],
            ['kode_akun' => '411', 'header_akun' => '4', 'nama_akun' => 'Penjualan'],
            ['kode_akun' => '412', 'header_akun' => '4', 'nama_akun' => 'Diskon Penjualan'],
            ['kode_akun' => '511', 'header_akun' => '5', 'nama_akun' => 'Harga Pokok Penjualan'],
            ['kode_akun' => '512', 'header_akun' => '5', 'nama_akun' => 'Diskon Pembelian'],
            ['kode_akun' => '521', 'header_akun' => '5', 'nama_akun' => 'Beban Listrik'],
            ['kode_akun' => '522', 'header_akun' => '5', 'nama_akun' => 'Beban Air'],
            ['kode_akun' => '523', 'header_akun' => '5', 'nama_akun' => 'Beban Telepon'],
            ['kode_akun' => '531', 'header_akun' => '5', 'nama_akun' => 'Beban Produk Kedaluarsa'],
        ];

        foreach ($coas as $coa) {
            Coa::create($coa);
        }
        //origin/sarah-backup-final
    }
}
