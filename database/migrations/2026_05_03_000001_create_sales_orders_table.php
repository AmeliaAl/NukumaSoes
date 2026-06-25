<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_so')->unique();
            $table->date('tanggal');
            $table->string('referensi'); // No Invoice / No Konsinyasi
            $table->enum('jenis', ['Non Konsinyasi', 'Konsinyasi']);
            $table->enum('status', ['Draft', 'Diproses', 'Selesai'])->default('Draft');
            
            // Foreign keys (nullable karena bisa dari salah satu)
            $table->foreignId('penjualan_non_konsinyasi_id')->nullable()->constrained('penjualan_non_konsinyasi')->onDelete('cascade');
            $table->foreignId('penjualan_konsinyasi_id')->nullable()->constrained('penjualan_konsinyasi')->onDelete('cascade');
            
            $table->timestamps();
        });

        Schema::create('detail_sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barang')->onDelete('restrict');
            $table->integer('qty');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_sales_orders');
        Schema::dropIfExists('sales_orders');
    }
};
