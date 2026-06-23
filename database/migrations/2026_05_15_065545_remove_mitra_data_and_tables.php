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
        Schema::dropIfExists('mitras');

        Schema::table('produk_keluar_entries', function (Blueprint $table) {
            if (Schema::hasColumn('produk_keluar_entries', 'mitra')) {
                $table->dropColumn('mitra');
            }
        });

        Schema::table('inventories', function (Blueprint $table) {
            if (Schema::hasColumn('inventories', 'mitra')) {
                $table->dropColumn('mitra');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('mitras', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mitra');
            $table->string('jenis_mitra')->nullable();
            $table->timestamps();
        });

        Schema::table('produk_keluar_entries', function (Blueprint $table) {
            $table->string('mitra')->nullable()->after('nama_produk');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->string('mitra')->nullable()->after('rasa_produk');
        });
    }
};
