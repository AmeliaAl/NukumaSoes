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
        Schema::create('aset', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset')->unique();
            $table->string('nama_aset');
            // Relasi ke kategori aset
            $table->foreignId('id_kategori')
                  ->constrained('kategori_aset')
                  ->onDelete('cascade');
            $table->date('tanggal_perolehan');
            $table->decimal('nilai_perolehan', 15, 2);
            $table->decimal('nilai_residu', 15, 2);
            $table->integer('masa_manfaat'); // dalam tahun
            $table->string('metode_penyusutan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};
