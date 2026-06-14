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
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id('id_bahan');
            $table->string('kode_bahan', 20)->unique();
            $table->string('nama_bahan', 100);
            $table->string('satuan', 20); // Kg, Liter, Pcs, dll
            $table->decimal('stok_minimum', 10, 2)->default(0);
            $table->decimal('stok_saat_ini', 10, 2)->default(0);
            $table->decimal('harga_rata_rata', 15, 2)->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_baku');
    }
};