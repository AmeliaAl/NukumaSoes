<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_umum', function (Blueprint $table) {
            $table->id('id_jurnal');
            $table->date('tanggal');
            $table->string('nomor_bukti', 30)->unique();
            $table->text('keterangan');
            $table->unsignedBigInteger('id_referensi')->nullable();
            $table->string('tipe_referensi', 50)->nullable(); // e.g. 'penerimaan_bahan_baku', 'pemakaian_bahan_baku', etc.
            $table->foreignId('id_admin')->constrained('admins', 'id_admin')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_umum');
    }
};
