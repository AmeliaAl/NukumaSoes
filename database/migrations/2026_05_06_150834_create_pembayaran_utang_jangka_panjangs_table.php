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
        Schema::create('pembayaran_utang_jangka_panjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utang_jangka_panjang_id')->constrained('utang_jangka_panjang')->cascadeOnDelete();
            $table->date('tanggal_bayar');
            $table->decimal('nominal', 15, 2);
            $table->foreignId('akun_id')->constrained('akun');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_utang_jangka_panjang');
    }
};
