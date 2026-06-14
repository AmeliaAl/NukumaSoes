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
        Schema::table('stok_bahan_baku', function (Blueprint $table) {
            // Tambah kolom id_penerimaan setelah id_bahan
            $table->unsignedBigInteger('id_penerimaan')->nullable()->after('id_bahan');
            
            // Tambah index untuk performa query
            $table->index('id_penerimaan');
            
            // Tambah foreign key constraint
            $table->foreign('id_penerimaan')
                  ->references('id_penerimaan')
                  ->on('penerimaan_bahan_baku')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_bahan_baku', function (Blueprint $table) {
            // Hapus foreign key dulu
            $table->dropForeign(['id_penerimaan']);
            
            // Hapus kolom
            $table->dropColumn('id_penerimaan');
        });
    }
};