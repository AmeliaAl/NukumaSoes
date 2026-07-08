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
        Schema::create('biaya_overhead_pabrik', function (Blueprint $table) {
            $table->id('id_overhead');
            $table->foreignId('id_permintaan_produksi')->constrained('permintaan_produksi', 'id_permintaan_produksi')->onDelete('cascade');
            $table->foreignId('id_admin')->constrained('admins', 'id_admin')->onDelete('cascade');
            $table->date('tanggal_overhead');
            $table->string('jenis_overhead', 50); // listrik, maintenance, depresiasi, dll
            $table->text('keterangan')->nullable();
            $table->decimal('nominal', 15, 2);
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
        Schema::dropIfExists('biaya_overhead_pabrik');
    }
};