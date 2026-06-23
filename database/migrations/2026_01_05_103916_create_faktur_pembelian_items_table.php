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
        Schema::create('faktur_pembelian_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_faktur')->constrained('faktur_pembelian')->onDelete('cascade');
            $table->string('nama_aset', 255);
            $table->foreignId('id_kategori')->nullable()->constrained('kategori_aset')->onDelete('set null');
            $table->integer('qty')->default(1);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2);
            //$table->integer('masa_manfaat')->nullable()->comment('Dalam bulan');
            //$table->decimal('nilai_residu', 15, 2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faktur_pembelian_item');
    }
};
