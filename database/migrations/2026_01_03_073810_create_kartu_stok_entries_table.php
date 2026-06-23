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
        Schema::create('kartu_stok_entries', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('keterangan');
            $table->string('id_transaksi');
            $table->integer('masuk')->nullable();
            $table->integer('keluar')->nullable();
            $table->integer('saldo')->nullable();
            $table->decimal('harga', 15, 2);
            $table->decimal('total_harga', 15, 2)->nullable();
            $table->string('no_batch')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_stok_entries');
    }
};
