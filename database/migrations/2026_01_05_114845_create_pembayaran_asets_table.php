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
        Schema::create('pembayaran_aset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_faktur')->constrained('faktur_pembelian')->onDelete('cascade');
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->enum('metode_pembayaran', ['cash', 'transfer', 'giro', 'lainnya'])->default('cash');
            $table->foreignId('id_akun')
                ->constrained('akun')
                ->onDelete('cascade');
            $table->text('keterangan')->nullable();
            $table->date('tgl_terima_brg')->nullable();
            $table->timestamps();
            
            $table->index('id_faktur');
            $table->index('tanggal_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_aset');
    }
};
