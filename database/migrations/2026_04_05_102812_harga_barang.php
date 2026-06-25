<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harga_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
            $table->string('jenis_mitra');
            $table->decimal('harga', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['barang_id', 'jenis_mitra']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harga_barang');
    }
};