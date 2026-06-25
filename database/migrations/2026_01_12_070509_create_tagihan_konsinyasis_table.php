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
        Schema::create('tagihan_konsinyasi', function (Blueprint $table) {
        $table->id();
        $table->string('no_tagihan')->unique();

        $table->foreignId('laporan_konsinyasi_id')
            ->constrained('laporan_konsinyasi')
            ->cascadeOnDelete();

        $table->date('tanggal_tagihan');
        $table->decimal('total_tagihan', 15, 2);
        $table->decimal('total_terbayar', 15, 2)->default(0);

        $table->enum('status', ['BELUM LUNAS', 'LUNAS'])
            ->default('BELUM LUNAS');

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_konsinyasi');
    }
};
