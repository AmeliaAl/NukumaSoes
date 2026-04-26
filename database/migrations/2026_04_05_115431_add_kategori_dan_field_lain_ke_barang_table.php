<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_id')->nullable()->after('nama_barang');
            $table->string('rasa')->nullable()->after('kategori_id');
            $table->string('satuan')->nullable()->after('rasa');
            $table->integer('stok_min')->default(0)->after('satuan');
            $table->integer('stok_awal')->default(0)->after('stok_min');
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn(['kategori_id', 'rasa', 'satuan', 'stok_min', 'stok_awal']);
        });
    }
};