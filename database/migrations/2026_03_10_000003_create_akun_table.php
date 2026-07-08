<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akun', function (Blueprint $table) {
            $table->id('id_akun');
            $table->string('kode_akun', 20)->unique();
            $table->string('nama_akun', 100);
            $table->enum('tipe_akun', ['aset', 'kewajiban', 'ekuitas', 'pendapatan', 'beban']);
            $table->enum('saldo_normal', ['debit', 'kredit']);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akun');
    }
};
