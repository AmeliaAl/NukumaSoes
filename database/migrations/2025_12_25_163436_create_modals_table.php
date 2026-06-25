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
        Schema::create('modal', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('jenis', ['setoran', 'prive', 'penyesuaian']);
            $table->decimal('jumlah', 15, 2);
            $table->foreignId('id_akun')
            ->constrained('akun')
            ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modal');
    }
};
