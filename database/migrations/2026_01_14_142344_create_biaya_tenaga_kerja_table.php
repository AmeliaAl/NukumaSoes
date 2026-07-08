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
        Schema::create('biaya_tenaga_kerja', function (Blueprint $table) {
            $table->id('id_biaya_tk');
            $table->foreignId('id_permintaan_produksi')->constrained('permintaan_produksi', 'id_permintaan_produksi')->onDelete('cascade');
            $table->foreignId('id_tenaga')->constrained('tenaga_kerja', 'id_tenaga')->onDelete('cascade');
            $table->foreignId('id_admin')->constrained('admins', 'id_admin')->onDelete('cascade');
            $table->date('tanggal_kerja');
            $table->decimal('jam_kerja', 5, 2);
            $table->decimal('upah_per_jam', 15, 2);
            $table->decimal('total_biaya', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Index untuk query per job
            $table->index('id_permintaan_produksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_tenaga_kerja');
    }
};