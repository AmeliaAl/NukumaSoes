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
        Schema::create('detail_penjualan_non_konsinyasi', function (Blueprint $table) {
            $table->id();

            // relasi ke penjualan
            $table->foreignId('penjualan_id')
                ->constrained('penjualan_non_konsinyasi')
                ->cascadeOnDelete();

            // relasi ke barang
            $table->foreignId('barang_id')
                ->constrained('barang')
                ->restrictOnDelete();

            $table->integer('qty');
            $table->string('kemasan')->nullable(); // pcs, box, pack, dll
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan_non_konsinyasi');
    }
};
