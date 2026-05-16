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
        Schema::create('pemakaian_persediaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_lancar_id')
                  ->constrained('aset_lancar')
                  ->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('jumlah');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemakaian_persediaan');
    }
};
