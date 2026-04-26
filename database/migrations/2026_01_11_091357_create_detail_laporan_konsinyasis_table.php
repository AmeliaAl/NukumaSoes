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
        Schema::create('detail_laporan_konsinyasi', function (Blueprint $table) {
        $table->id();

        $table->foreignId('no_laporan')
            ->constrained('laporan_konsinyasi')
            ->cascadeOnDelete();

        $table->foreignId('barang_id')
            ->constrained('barang')
            ->cascadeOnDelete();

        $table->integer('qty_terjual');
        $table->decimal('harga_konsinyasi', 15, 2);
        $table->decimal('subtotal', 15, 2);

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_laporan_konsinyasi');
    }
};
