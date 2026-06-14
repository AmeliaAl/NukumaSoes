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
        Schema::create('tenaga_kerja', function (Blueprint $table) {
            $table->id('id_tenaga');
            $table->string('kode_tenaga', 20)->unique();
            $table->string('nama_tenaga', 100);
            $table->string('jabatan', 50);
            $table->decimal('upah_per_jam', 15, 2);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenaga_kerja');
    }
};