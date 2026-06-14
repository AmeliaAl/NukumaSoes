<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('detail_persediaan_produk_consumptions');
        Schema::create('detail_persediaan_produk_consumptions', function (Blueprint $table) {
            $table->id();

            $table->string('detail_transaksi_type');
            $table->unsignedBigInteger('detail_transaksi_id');

            $table->unsignedBigInteger('detail_persediaan_produk_id');
            $table->unsignedInteger('qty_digunakan');

            $table->timestamps();

            $table->foreign('detail_persediaan_produk_id', 'fk_det_persediaan_produk_id')
                ->references('id')
                ->on('detail_persediaan_produk')
                ->cascadeOnDelete();

            $table->index(['detail_transaksi_type', 'detail_transaksi_id']);
            $table->index(['detail_persediaan_produk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_persediaan_produk_consumptions');
    }
};

