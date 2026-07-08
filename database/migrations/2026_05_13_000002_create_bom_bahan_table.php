<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bom_bahan', function (Blueprint $table) {
            $table->id('id_bom_bahan');
            $table->foreignId('id_produk')->constrained('produk', 'id_produk')->onDelete('cascade');
            $table->foreignId('id_bahan')->constrained('bahan_baku', 'id_bahan')->onDelete('cascade');
            $table->decimal('jumlah_kebutuhan', 15, 2); // Jumlah bahan per batch
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('id_produk');
            // Satu produk tidak boleh punya bahan yang sama di BOM
            $table->unique(['id_produk', 'id_bahan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bom_bahan');
    }
};
