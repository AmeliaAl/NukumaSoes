<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_konsinyasi', function (Blueprint $table) {
        $table->id();
        $table->string('no_konsinyasi')->unique();
        $table->date('tanggal');

        $table->foreignId('kode_mitra')
            ->constrained('mitra')
            ->cascadeOnDelete();

        $table->integer('term');
        $table->date('jatuh_tempo');

        $table->decimal('total_konsinyasi', 15, 2)->default(0);
        $table->decimal('total_laporan', 15, 2)->default(0);

        $table->enum('status', ['BELUM TERJUAL', 'PIUTANG', 'LUNAS'])
            ->default('BELUM TERJUAL');

        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_konsinyasi');
    }
};
