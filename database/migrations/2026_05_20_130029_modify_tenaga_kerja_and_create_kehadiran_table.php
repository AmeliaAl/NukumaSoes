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
        // 1. Rename upah_per_minggu to upah_per_jam on tenaga_kerja table
        Schema::table('tenaga_kerja', function (Blueprint $table) {
            $table->renameColumn('upah_per_minggu', 'upah_per_jam');
        });

        // 2. Change upah_per_jam default value to 6000.00
        Schema::table('tenaga_kerja', function (Blueprint $table) {
            $table->decimal('upah_per_jam', 15, 2)->default(6000.00)->change();
        });

        // 3. Create kehadiran_harian table
        Schema::create('kehadiran_harian', function (Blueprint $table) {
            $table->id('id_kehadiran');
            $table->date('tanggal');
            $table->unsignedBigInteger('id_tenaga');
            $table->enum('status_kehadiran', ['hadir', 'absen', 'izin', 'sakit'])->default('hadir');
            $table->decimal('jam_kerja', 5, 2)->default(8.00);
            $table->decimal('jam_lembur', 5, 2)->default(0.00);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign Key
            $table->foreign('id_tenaga')->references('id_tenaga')->on('tenaga_kerja')->onDelete('cascade');
            
            // Unique key to prevent duplicate attendance on same date
            $table->unique(['tanggal', 'id_tenaga']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadiran_harian');

        Schema::table('tenaga_kerja', function (Blueprint $table) {
            $table->renameColumn('upah_per_jam', 'upah_per_minggu');
        });
    }
};
