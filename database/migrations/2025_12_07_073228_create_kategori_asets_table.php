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
        Schema::create('kategori_aset', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kategori')->unique();       
            $table->string('nama_kategori');  
            $table->enum('jenis_aset',['aset_tetap', 'aset_lancar']);  
            $table->string('metode_penyusutan')->default('straight_line'); 
            $table->integer('masa_manfaat');         
            $table->integer('interval_pemeliharaan'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_aset');
    }
};
