<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel jurnal_detail sebagai detail entri debit/kredit.
     * Struktur sesuai kebutuhan integrasi:
     *   id, id_jurnal, no_akun, deskripsi, debit, credit, created_at, updated_at
     *
     * Relasi:
     *   jurnal_detail.id_jurnal → jurnal.id  (CASCADE)
     *   jurnal_detail.no_akun   → akun.no_akun (soft reference, no FK constraint
     *                             karena no_akun adalah string, bukan integer PK)
     */
    public function up(): void
    {
        Schema::create('jurnal_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_jurnal')
                  ->constrained('jurnal')
                  ->onDelete('cascade');
            $table->string('no_akun')->index();   // kode akun, referensi ke akun.no_akun
            $table->string('deskripsi')->nullable();
            $table->decimal('debit',  15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_detail');
    }
};
