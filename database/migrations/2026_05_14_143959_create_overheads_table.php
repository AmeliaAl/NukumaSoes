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
    Schema::create('overheads', function (Blueprint $table) {

        $table->id();

        $table->date('tanggal');

        $table->foreignId('coa_id')
            ->constrained('coas')
            ->onDelete('cascade');

        $table->text('keterangan');

        $table->integer('nominal');

        $table->timestamps();

    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overheads');
    }
};
