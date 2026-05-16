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
        Schema::create('pemeliharaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_id')
                  ->constrained('aset')
                  ->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('jenis_perbaikan'); // maintenance | peningkatan
            $table->decimal('biaya', 15, 2);
            $table->string('keterangan')->nullable();
            $table->string('metode_pembayaran')->after('biaya');
            $table->foreignId('id_akun')
                ->nullable()
                ->constrained('akun')
                ->nullOnDelete();
            $table->integer('tambah_umur')->nullable(); // untuk peningkatan umur aset
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeliharaan');
    }
};
