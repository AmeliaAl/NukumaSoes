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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk');
            $table->string('nama_produk');
            $table->string('kategori');
            $table->integer('jumlah');
            $table->decimal('harga', 15, 2); // Unit price
            $table->date('tgl_masuk');
            $table->date('tgl_expired')->nullable();
            $table->string('status')->nullable();
            $table->integer('sisa_hari')->nullable();
            $table->integer('masa_simpan')->default(0);
            $table->string('satuan_masa_simpan')->default('hari');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
