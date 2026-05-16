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
        Schema::create('penyusutan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_id')
                ->constrained('aset')
                ->cascadeOnDelete();

            // periode penyusutan (contoh: 2024-03)
            $table->string('periode', 7);
            $table->decimal('beban_penyusutan', 15, 2);
            $table->decimal('akumulasi_penyusutan', 15, 2);
            $table->decimal('nilai_buku', 15, 2);
 
            $table->unique(['aset_id', 'periode']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyusutan');
    }
};
