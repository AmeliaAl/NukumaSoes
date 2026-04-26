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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pembayaran')->unique();
            $table->date('tanggal_bayar');

            // relasi ke penjualan
            $table->foreignId('penjualan_id')
                ->constrained('penjualan_non_konsinyasi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // relasi ke pelanggan
            $table->foreignId('pelanggan_id')
                ->constrained('pelanggan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // jumlah yang dibayar
            $table->decimal('jumlah_bayar', 15, 2);

            // metode pembayaran
            $table->enum('metode_pembayaran', [
                'tunai',
                'transfer',
                'qris'
            ]);

            // keterangan opsional
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn('kode_pembayaran');
        });
    }
};
