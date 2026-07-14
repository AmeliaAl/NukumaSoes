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
        Schema::create('pembelians', function (Blueprint $table) {

            $table->id();

            $table->date('tanggal');

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->onDelete('cascade');

            $table->foreignId('bahan_baku_id')
                ->constrained('bahan_bakus')
                ->onDelete('cascade');

            $table->integer('qty');

            $table->integer('harga');

            $table->integer('total');

            $table->foreignId('coa_id')
                ->constrained('coas')
                ->onDelete('cascade');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
