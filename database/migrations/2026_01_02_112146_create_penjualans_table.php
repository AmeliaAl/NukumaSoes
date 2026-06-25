<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembeli_id')
                ->constrained('pelanggan')
                ->onDelete('cascade');
            $table->string('no_faktur');
            $table->string('status');
            $table->dateTime('tgl');
            $table->decimal('tagihan', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};