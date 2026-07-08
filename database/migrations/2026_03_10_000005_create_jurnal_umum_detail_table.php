<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_umum_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_jurnal')->constrained('jurnal_umum', 'id_jurnal')->onDelete('cascade');
            $table->foreignId('id_akun')->constrained('akun', 'id_akun')->onDelete('cascade');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('id_jurnal');
            $table->index('id_akun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_umum_detail');
    }
};
