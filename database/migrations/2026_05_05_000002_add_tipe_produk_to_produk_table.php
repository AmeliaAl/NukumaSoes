<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            // Tipe produk: kulit (WIP per gram), isi (setelah filling), jadi (finished goods)
            $table->enum('tipe_produk', ['kulit', 'isi', 'jadi'])
                  ->default('jadi')
                  ->after('kategori')
                  ->comment('kulit=WIP/setengah jadi per gram, isi=setelah filling, jadi=barang jadi');
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn('tipe_produk');
        });
    }
};
