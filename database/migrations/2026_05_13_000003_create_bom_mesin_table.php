<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bom_mesin', function (Blueprint $table) {
            $table->id('id_bom_mesin');
            $table->foreignId('id_produk')->constrained('produk', 'id_produk')->onDelete('cascade');
            $table->string('nama_mesin', 100);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('id_produk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bom_mesin');
    }
};
