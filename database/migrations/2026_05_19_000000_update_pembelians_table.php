<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            // Tambah kolom no_pembelian
            $table->string('no_pembelian')->after('id')->unique();
            
            // Tambah nomor permintaan
            $table->string('nomor_permintaan')->nullable()->after('tanggal');
            
            // Ubah harga menjadi decimal untuk presisi
            // Hapus kolom lama terlebih dahulu jika perlu
            
            // Tambah subtotal
            $table->decimal('subtotal', 12, 2)->after('harga')->default(0);
            
            // Tambah diskon (opsional)
            $table->decimal('diskon', 12, 2)->nullable()->after('subtotal')->default(0);
            
            // Tambah ongkir (opsional)
            $table->decimal('ongkir', 12, 2)->nullable()->after('diskon')->default(0);
            
            // Tambah total_bersih
            $table->decimal('total_bersih', 12, 2)->after('ongkir')->default(0);
            
            // Tambah grand_total
            $table->decimal('grand_total', 12, 2)->after('total_bersih')->default(0);
            
            // Tambah pembayaran status
            $table->string('pembayaran')->default('belum_bayar')->after('grand_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn([
                'no_pembelian',
                'nomor_permintaan',
                'subtotal',
                'diskon',
                'ongkir',
                'total_bersih',
                'grand_total',
                'pembayaran',
            ]);
        });
    }
};
