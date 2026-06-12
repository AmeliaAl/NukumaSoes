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
        Schema::create('expired_product_histories', function (Blueprint $table) {
            $table->id();
            $table->string('no_batch')->nullable();
            $table->string('nama_produk');
            $table->string('rasa_produk')->nullable();
            $table->string('kategori');
            $table->integer('jumlah_per_batch')->default(0);
            $table->integer('jumlah')->default(0);
            $table->decimal('harga', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->date('tgl_masuk')->nullable();
            $table->date('tgl_expired')->nullable();
            $table->string('status')->nullable();
            $table->integer('sisa_hari')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expired_product_histories');
    }
};
