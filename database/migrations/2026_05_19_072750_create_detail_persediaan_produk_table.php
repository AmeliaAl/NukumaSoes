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
        Schema::create('detail_persediaan_produk', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('barang_id');
            $table->string('batch');

            $table->unsignedInteger('stok_awal')->default(0);
            $table->unsignedInteger('stok_saat_ini')->default(0);

            $table->unsignedInteger('harga')->default(0);
            $table->date('tanggal_expired')->nullable();

            $table->timestamps();

            $table->foreign('barang_id')
                ->references('id')
                ->on('barang')
                ->cascadeOnDelete();

            $table->index(['barang_id', 'batch']);
            $table->index(['barang_id', 'tanggal_expired']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_persediaan_produk');
    }
};
