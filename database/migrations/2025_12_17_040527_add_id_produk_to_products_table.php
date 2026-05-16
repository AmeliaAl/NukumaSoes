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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'id_produk')) {
                $table->string('id_produk')->unique();
            }
            if (!Schema::hasColumn('products', 'nama_produk')) {
                $table->string('nama_produk');
            }
            if (!Schema::hasColumn('products', 'kategori')) {
                $table->string('kategori');
            }
            if (!Schema::hasColumn('products', 'tgl_masuk')) {
                $table->date('tgl_masuk')->nullable();
            }
            if (!Schema::hasColumn('products', 'jumlah')) {
                $table->integer('jumlah');
            }
            if (!Schema::hasColumn('products', 'harga')) {
                $table->decimal('harga', 10, 2);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['id_produk', 'nama_produk', 'kategori', 'tgl_masuk', 'jumlah', 'harga']);
        });
    }
};
