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
        Schema::create('penerimaan_bahan_baku', function (Blueprint $table) {
            $table->id('id_penerimaan');
            $table->string('nomor_penerimaan', 50)->unique();
            
            // Foreign Keys
            $table->unsignedBigInteger('id_permintaan_bahan')->nullable(); // Bisa null (penerimaan langsung)
            $table->unsignedBigInteger('id_bahan');
            $table->unsignedBigInteger('id_admin');
            
            // FIXED: tanggal_diterima → tanggal_penerimaan
            $table->date('tanggal_penerimaan');
            
            // Detail Penerimaan
            $table->decimal('jumlah_diterima', 10, 2);
            $table->decimal('harga_per_satuan', 15, 2);
            $table->decimal('total_biaya', 15, 2);
            
            // Additional Info
            $table->string('supplier', 100)->nullable();
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('id_permintaan_bahan')
                  ->references('id_permintaan_bahan')
                  ->on('permintaan_bahan_baku')
                  ->onDelete('set null');
                  
            $table->foreign('id_bahan')
                  ->references('id_bahan')
                  ->on('bahan_baku')
                  ->onDelete('restrict');
                  
            $table->foreign('id_admin')
                  ->references('id_admin')
                  ->on('admins')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_bahan_baku');
    }
};