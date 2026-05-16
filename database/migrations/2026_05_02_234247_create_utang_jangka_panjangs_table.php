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
        Schema::create('utang_jangka_panjang', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_utang');
            $table->foreignId('akun_id')->constrained('akun')->cascadeOnDelete();
            $table->foreignId('akun_debit_id')->nullable()->constrained('akun')->cascadeOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->date('jatuh_tempo');
            $table->text('keterangan')->nullable();
            $table->foreignId('jurnal_id')->nullable()->constrained('jurnal')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utang_jangka_panjang');
    }
};
