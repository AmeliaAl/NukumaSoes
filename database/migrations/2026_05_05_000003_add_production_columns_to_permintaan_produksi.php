<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_produksi', function (Blueprint $table) {
            // Jenis produksi: maklun (untuk merk lain) atau brand_sendiri
            $table->enum('jenis_produksi', ['maklun', 'brand_sendiri'])
                  ->default('brand_sendiri')
                  ->after('customer')
                  ->comment('maklun=untuk merk lain, brand_sendiri=produk sendiri');

            // Tujuan produksi: pesanan customer, stok WIP, atau stok barang jadi
            $table->enum('tujuan_produksi', ['pesanan', 'stok_wip', 'stok_barang_jadi'])
                  ->default('pesanan')
                  ->after('jenis_produksi')
                  ->comment('pesanan=per order, stok_wip=stok kulit, stok_barang_jadi=stok produk jadi');

            // Tahap produksi — produksi hanya sampai filling, pengemasan oleh bagian lain
            $table->enum('tahap_produksi', ['persiapan', 'produksi', 'filling', 'selesai'])
                  ->default('persiapan')
                  ->after('tujuan_produksi')
                  ->comment('Tahap saat ini: persiapan → produksi → filling → selesai');

            // Nama customer maklun (hanya diisi jika jenis_produksi = maklun)
            $table->string('nama_customer_maklun', 100)
                  ->nullable()
                  ->after('tahap_produksi')
                  ->comment('Nama merk/customer untuk produksi maklun');

            // Jumlah batch per hari (default 3, bisa lebih atau kurang)
            $table->unsignedSmallInteger('jumlah_batch')
                  ->default(3)
                  ->after('jumlah_produksi')
                  ->comment('Jumlah batch produksi, default 3 batch/hari');

            // Pecahan BTK untuk detail HPP sesuai standar akuntansi
            $table->decimal('total_biaya_btk_langsung', 15, 2)
                  ->default(0)
                  ->after('total_biaya_bahan')
                  ->comment('Total BTK Langsung (operator produksi)');

            $table->decimal('total_biaya_btk_tidak_langsung', 15, 2)
                  ->default(0)
                  ->after('total_biaya_btk_langsung')
                  ->comment('Total BTK Tidak Langsung (supervisor, QC, dll)');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_produksi', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_produksi',
                'tujuan_produksi',
                'tahap_produksi',
                'nama_customer_maklun',
                'jumlah_batch',
                'total_biaya_btk_langsung',
                'total_biaya_btk_tidak_langsung',
            ]);
        });
    }
};
